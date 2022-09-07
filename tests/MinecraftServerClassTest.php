<?php

/*
 * This file is part of https://github.com/josantonius/php-minecraft-server-player-stat repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */

namespace Josantonius\MinecraftServerPlayerStat\Tests;

use PHPUnit\Framework\TestCase;
use Josantonius\MinecraftServerPlayerStat\MinecraftServer;
use Josantonius\MinecraftServerPlayerStat\Exceptions\MinecraftServerException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\UnreadableDirectoryException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\UnwriteableDirectoryException;

class MinecraftServerClassTest extends TestCase
{
    private string $server   = 'spigot';
    private string $version  = '1.17';
    private string $language = 'en_us';

    private string $basePath;
    private string $logsPath;
    private string $statsPath;
    private string $storagePath;

    private MinecraftServer $minecraftServer;

    public function setUp(): void
    {
        parent::setUp();

        $this->basePath = __DIR__ . "/minecraft/{$this->server}/{$this->version}/{$this->language}";

        $this->logsPath    = "{$this->basePath}/logs";
        $this->statsPath   = "{$this->basePath}/stats";
        $this->storagePath = "{$this->basePath}/storage";

        $this->minecraftServer = new MinecraftServer(
            version: $this->version,
            language: $this->language,
            logsPath: $this->logsPath,
            statsPath: $this->statsPath,
            storagePath: $this->storagePath,
        );
    }

    public function test_should_fail_if_the_version_is_wrong(): void
    {
        $this->expectException(MinecraftServerException::class);

        new MinecraftServer(
            version: '8.0.0',
            language: $this->language,
            logsPath: $this->logsPath,
            statsPath: $this->statsPath,
            storagePath: $this->storagePath,
        );
    }

    public function test_should_fail_if_the_language_is_wrong(): void
    {
        $this->expectException(MinecraftServerException::class);

        new MinecraftServer(
            version: $this->version,
            language: 'es_fr',
            logsPath: $this->logsPath,
            statsPath: $this->statsPath,
            storagePath: $this->storagePath,
        );
    }

    public function test_should_fail_if_the_logs_path_is_wrong(): void
    {
        $this->expectException(UnreadableDirectoryException::class);

        new MinecraftServer(
            version: $this->version,
            language: $this->language,
            logsPath: 'foo',
            statsPath: $this->statsPath,
            storagePath: $this->storagePath,
        );
    }

    public function test_should_fail_if_the_stats_path_is_wrong(): void
    {
        $this->expectException(UnreadableDirectoryException::class);

        new MinecraftServer(
            version: $this->version,
            language: $this->language,
            logsPath: $this->logsPath,
            statsPath: 'bar',
            storagePath: $this->storagePath,
        );
    }

    public function test_should_fail_if_the_storage_path_is_wrong(): void
    {
        $this->expectException(UnwriteableDirectoryException::class);

        new MinecraftServer(
            version: $this->version,
            language: $this->language,
            logsPath: $this->logsPath,
            statsPath: $this->statsPath,
            storagePath: 'baz',
        );
    }

    public function test_should_get_available_terms(): void
    {
        $terms = $this->minecraftServer->getAvailableStats();

        $this->assertIsArray($terms);
        $this->assertNotEmpty($terms);

        $this->assertArrayHasKey('diamond', $terms);

        $this->assertEquals('diamond', $terms['diamond']['key']);
        $this->assertEquals('Diamond', $terms['diamond']['pretty_term']);
        $this->assertEquals('item', $terms['diamond']['type']);
        $this->assertEquals('amount', $terms['diamond']['unit_type']);
    }

    public function test_should_get_available_players(): void
    {
        $players = $this->minecraftServer->getPlayerList();

        $this->assertIsArray($players);
        $this->assertNotEmpty($players);

        $this->assertCount(8, $players);

        for ($i = 1; $i <= 8; $i++) {
            $this->assertArrayHasKey("user{$i}", $players);
        }

        $this->assertEquals('5cd5d2e7-9b3a-3f06-befb-34f7a81b14c6', $players['user1']);
        $this->assertEquals('8cb86072-3472-3b86-90f2-1e11b7188197', $players['user2']);
        $this->assertEquals('8d5b923d-37fb-38d1-8a6a-a17cd5ccf768', $players['user3']);
        $this->assertEquals('14e55460-c753-31f2-bd0a-c305e2ff34b5', $players['user4']);
        $this->assertEquals('18f154fe-3678-37e9-9b77-185e0bfe446d', $players['user5']);
        $this->assertEquals('28db6663-6166-3a17-a057-00e4eab214eb', $players['user6']);
        $this->assertEquals('9de7b4a0-e911-366d-9a0c-f56b62ff58e7', $players['user7']);
        $this->assertEquals('9dfc997b-c172-3f83-af3e-7062f773455a', $players['user8']);
    }

    public function test_should_ignore_accents_if_the_language_is_spanish(): void
    {
        $server   = 'spigot';
        $version  = '1.17';
        $language = 'es_es';

        $basePath = __DIR__ . "/minecraft/{$server}/{$version}/{$language}";

        $logsPath    = "{$basePath}/logs";
        $statsPath   = "{$basePath}/stats";
        $storagePath = "{$basePath}/storage";

        $minecraftServer = new MinecraftServer(
            version: $version,
            language: $language,
            logsPath: $logsPath,
            statsPath: $statsPath,
            storagePath: $storagePath,
        );

        $this->assertArrayHasKey(
            'tiempo desde la ultima muerte',
            $minecraftServer->getAvailableStats()
        );

        unlink("{$storagePath}/{$version}/{$language}/players.json");
        unlink("{$storagePath}/{$version}/{$language}/terms.json");

        rmdir("{$storagePath}/{$version}/{$language}");
        rmdir("{$storagePath}/{$version}");
    }
}
