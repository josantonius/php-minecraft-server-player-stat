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

class MinecraftServerVersionTest extends TestCase
{
    public function test_should_validate_that_it_works_on_spigot_1_17(): void
    {
        $server   = 'spigot';
        $version  = '1.17';
        $language = 'en_us';

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

        $this->assertNotEmpty($minecraftServer->getAvailableStats());
        $this->assertNotEmpty($minecraftServer->getPlayerList());

        unlink("{$storagePath}/{$version}/{$language}/players.json");
        unlink("{$storagePath}/{$version}/{$language}/terms.json");

        rmdir("{$storagePath}/{$version}/{$language}");
        rmdir("{$storagePath}/{$version}");
    }
}
