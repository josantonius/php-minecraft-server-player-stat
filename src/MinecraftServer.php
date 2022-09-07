<?php

declare(strict_types=1);

/*
 * This file is part of https://github.com/josantonius/php-minecraft-server-player-stat repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josantonius\MinecraftServerPlayerStat;

use Throwable;
use SplFileInfo;
use DirectoryIterator;
use Josantonius\Json\Json;
use Josantonius\MinecraftServerPlayerStat\Exceptions\WrongTermException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\StatsNotFoundException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\MinecraftServerException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\UnknownUsernameException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\UnreadableDirectoryException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\UnwriteableDirectoryException;

class MinecraftServer
{
    private array $terms   = [];
    private array $players = [];

    private string $basePath;
    private bool $isSpanishLanguage;

    /**
     * Create a new instance for the Minecraft server.
     *
     * @param string $version     Server version.
     * @param string $language    Server language.
     * @param string $logsPath    Server logs directory path.
     * @param string $statsPath   Server stats directory path.
     * @param string $storagePath Directory path where available terms and players will be stored.
     *
     * @throws MinecraftServerException      if the Minecraft version or language is not valid.
     * @throws UnreadableDirectoryException  if the logs or stats path is not valid.
     * @throws UnwriteableDirectoryException if the storage path is not valid.
     *
     * @see https://mcasset.cloud/1.19.2/assets/minecraft/lang to see available languages.
     */
    public function __construct(
        private string $version,
        private string $language,
        private string $logsPath,
        private string $statsPath,
        private string $storagePath,
    ) {
        $this->removeEndSlash($this->logsPath);
        $this->removeEndSlash($this->statsPath);
        $this->removeEndSlash($this->storagePath);

        is_readable($this->logsPath)     || throw new UnreadableDirectoryException($this->logsPath);
        is_readable($this->statsPath)    || throw new UnreadableDirectoryException($this->statsPath);
        is_writeable($this->storagePath) || throw new UnwriteableDirectoryException($this->storagePath);

        $this->language = strtolower($language);
        $this->basePath = $this->storagePath . "/{$this->version}/{$this->language}";

        $this->isSpanishLanguage = str_contains($this->language, 'es_');

        $this->setAvailableTermsFromTranslations();
        $this->setPlayersFromLogsFiles();
    }

    /**
     * Get list of available statistics.
     */
    public function getAvailableStats(): array
    {
        return $this->terms;
    }

    /**
     * Gets details about certain player statistics.
     *
     * @param string $username Username in case insensitive.
     * @param string $term     Literal Minecraft term in case insensitive.
     *                         In Spanish, a term with accents can be written without them.
     *                         For another languages accents are required.
     *
     * @throws WrongTermException       if the term is not valid.
     * @throws StatsNotFoundException   if the stats file is not found.
     * @throws UnknownUsernameException if the username is not valid.
     */
    public function getPlayerStat(string $username, string $term): MinecraftPlayerStat
    {
        $item = $this->terms[$this->normalizeTerm($term)] ?? throw new WrongTermException($term);

        !$this->playerExists($username) && $this->setPlayersFromLogsFiles();
        !$this->playerExists($username) && throw new UnknownUsernameException($username);

        $uuid = $this->players[strtolower($username)];

        try {
            $stats = $this->file('stats', $uuid)->get()['stats'] ?? [];
        } catch (Throwable $th) {
            throw new StatsNotFoundException($username, $th);
        }

        $items = [
            'broken', 'crafted', 'custom', 'dropped', 'killed', 'killed_by', 'mined', 'picked_up', 'used'
        ];

        foreach ($items as $name) {
            $stats[$name] =  $stats["minecraft:{$name}"]["minecraft:{$item['key']}"] ?? null;
        }

        return new MinecraftPlayerStat(
            broken:     $stats['broken'],
            crafted:    $stats['crafted'],
            custom:     $stats['custom'],
            dropped:    $stats['dropped'],
            killed:     $stats['killed'],
            killedBy:   $stats['killed_by'],
            mined:      $stats['mined'],
            pickedUp:   $stats['picked_up'],
            used:       $stats['used'],
            key:        $item['key'],
            prettyTerm: $item['pretty_term'],
            term:       $term,
            type:       $item['type'],
            unitType:   $item['unit_type'],
            username:   $username,
            uuid:       $uuid,
        );
    }

    /**
     * Get list of players from the server.
     */
    public function getPlayerList(): array
    {
        unset($this->players['last update']);

        return $this->players;
    }

    /**
     * Gets JSON file instance from its identifier.
     */
    private function file(string $name, string $uuid = ''): Json
    {
        switch ($name) {
            case 'stats':
                return new Json("{$this->statsPath}/{$uuid}.json");
            case 'terms':
                return new Json("{$this->basePath}/terms.json");
            case 'translations':
                $repo = 'https://raw.githubusercontent.com/InventivetalentDev/minecraft-assets';
                return new Json("{$repo}/{$this->version}/assets/minecraft/lang/{$this->language}.json");
            case 'players':
                return new Json("{$this->basePath}/players.json");
        }
    }

    /**
     * Gets the unit type of an item.
     */
    private function getUnitType(array $item): string
    {
        $isTime     = str_contains($item['key'], 'time');
        $isDistance = str_contains($item['key'], 'one_cm');

        return $isTime ? 'time' : ($isDistance ? 'distance' : 'amount');
    }

    /**
     * Determines if the element type is valid.
     */
    private function hasValidType(array $item): bool
    {
        return in_array($item['type'], ['entity', 'item', 'stat', 'block']);
    }


    /**
     * Determines whether a log file has already been reviewed.
     */
    private function logWasReviewed(string $current): bool
    {
        $last = str_replace('-', '', $this->players['last update'] ?? '0');

        return $last >= $current;
    }

    /**
     * Converts to lowercase, removes duplicate spaces, and removes accents if language is Spanish.
     */
    private function normalizeTerm(string $string): string
    {
        $string = strtolower(preg_replace('/\s+/', ' ', $string));

        if ($this->isSpanishLanguage) {
            $string = str_replace(
                ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ç'],
                ['a', 'e', 'i', 'o', 'u', 'n', 'c'],
                $string
            );
        }

        return $string;
    }

    /**
     * Removes the end slash at the end of the path.
     */
    private function removeEndSlash(string &$path): void
    {
        $path = rtrim($path, '/');
    }

    /**
     * Sets the terms available for the server version and language.
     *
     * The available terms are set from the minecraft-assets repository of InventivetalentDev.
     *
     * @see https://github.com/InventivetalentDev/minecraft-assets/branches/all
     *
     * @throws MinecraftServerException if the Minecraft version or language is not valid.
     */
    private function setAvailableTermsFromTranslations(): void
    {
        if ($this->file('terms')->exists()) {
            $this->terms = $this->file('terms')->get();
            return;
        }

        try {
            $translations = $this->file('translations')->get();
        } catch (Throwable $th) {
            throw new MinecraftServerException('Wrong Minecraft version or language.', 0, $th);
        }

        foreach ($translations as $term => $value) {
            $keys = explode('.', $term);
            $item = [
                'key' => $keys[2] ?? '',
                'pretty_term' => $value,
                'type' => $keys[0] ?? '',
            ];

            if (!$this->hasValidType($item) || count($keys) !== 3) {
                continue;
            }

            $this->terms[$this->normalizeTerm($value)] = [
                ...$item, 'unit_type' => $this->getUnitType($item)
            ];
        }

        ksort($this->terms);

        $this->file('terms')->set($this->terms);
    }

    /**
     * Extracts usernames and UUIDs from file.
     */
    private function setPlayersFromLogFile(string $filepath): void
    {
        $file = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($file as $line) {
            if (!str_contains($line, 'UUID of player')) {
                continue;
            }
            $items = explode(' ', str_replace(' is ', ' ', $line));

            $uuid     = array_pop($items);
            $username = array_pop($items);

            $this->players[strtolower($username)] = $uuid;
        }
    }

    /**
     * Sets players and UUIDs by extracting them from log files.
     */
    private function setPlayersFromLogsFiles()
    {
        $files = [];

        foreach (new DirectoryIterator($this->logsPath) as $fileinfo) {
            $basename  = $fileinfo->getBasename('.log.gz');
            $extension = $fileinfo->getExtension();

            if (!$fileinfo->isFile() || $extension !== 'gz' || $this->logWasReviewed($basename)) {
                if ($extension === 'log') {
                    $this->setPlayersFromLogFile($fileinfo->getPathname());
                }
                continue;
            }

            $files[]  = $basename;
            $filepath = $this->unzip($fileinfo);

            $this->setPlayersFromLogFile($filepath);

            unlink($filepath);
        }

        if ($files && sort($files)) {
            $this->players['last update'] = str_replace('-', '', array_pop($files));
        }

        $this->file('players')->set($this->players);
    }

    /**
     * Unzip gz-file.
     */
    private function unzip(SplFileInfo $fileinfo): string
    {
        $filePath = "{$this->storagePath}/current.log";

        $stream   = gzopen($fileinfo->getPathname(), 'rb');
        $resource = fopen($filePath, 'wb');

        while (!gzeof($stream)) {
            fwrite($resource, gzread($stream, $fileinfo->getSize()));
        }

        fclose($resource);
        gzclose($stream);

        return $filePath;
    }

    /**
     * Determines if a player exists on the server.
     */
    private function playerExists(string $username): bool
    {
        return isset($this->players[strtolower($username)]);
    }
}
