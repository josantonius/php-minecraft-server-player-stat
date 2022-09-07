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
use Josantonius\MinecraftServerPlayerStat\MinecraftPlayerStat;
use Josantonius\MinecraftServerPlayerStat\Exceptions\WrongTermException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\StatsNotFoundException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\UnknownUsernameException;

class GetPlayerStatMethodTest extends TestCase
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

    public function test_should_fail_if_the_username_is_wrong(): void
    {
        $this->expectException(UnknownUsernameException::class);

        $this->minecraftServer = new MinecraftServer(
            version: $this->version,
            language: $this->language,
            logsPath: $this->logsPath,
            statsPath: $this->statsPath,
            storagePath: $this->storagePath,
        );

        $this->minecraftServer->getPlayerStat(
            username: 'foo',
            term: 'diamond'
        );
    }

    public function test_should_fail_if_the_term_is_wrong(): void
    {
        $this->expectException(WrongTermException::class);

        $this->minecraftServer = new MinecraftServer(
            version: $this->version,
            language: $this->language,
            logsPath: $this->logsPath,
            statsPath: $this->statsPath,
            storagePath: $this->storagePath,
        );

        $this->minecraftServer->getPlayerStat(username: 'user1', term: 'foo');
    }

    public function test_should_fail_if_the_statistics_file_does_not_exist(): void
    {
        $this->expectException(StatsNotFoundException::class);

        $this->minecraftServer = new MinecraftServer(
            version: $this->version,
            language: $this->language,
            logsPath: $this->logsPath,
            statsPath: $this->statsPath,
            storagePath: $this->storagePath,
        );

        $this->minecraftServer->getPlayerStat(username: 'user8', term: 'diamond');
    }

    public function test_should_obtain_statistics_from_the_player_about_an_entity(): void
    {
        $entity = $this->minecraftServer->getPlayerStat('User1', 'zombie');

        $this->assertInstanceOf(MinecraftPlayerStat::class, $entity);

        $this->assertNull($entity->broken);
        $this->assertNull($entity->crafted);
        $this->assertNull($entity->custom);
        $this->assertNull($entity->dropped);
        $this->assertEquals(8, $entity->killed);
        $this->assertEquals(2, $entity->killedBy);
        $this->assertNull($entity->mined);
        $this->assertNull($entity->pickedUp);
        $this->assertNull($entity->used);
        $this->assertEquals('zombie', $entity->key);
        $this->assertEquals('Zombie', $entity->prettyTerm);
        $this->assertEquals('zombie', $entity->term);
        $this->assertEquals('entity', $entity->type);
        $this->assertEquals('amount', $entity->unitType);
        $this->assertEquals('User1', $entity->username);
        $this->assertEquals('5cd5d2e7-9b3a-3f06-befb-34f7a81b14c6', $entity->uuid);
    }

    public function test_should_obtain_statistics_from_the_player_about_an_item(): void
    {
        $item = $this->minecraftServer->getPlayerStat('user2', 'wooden axe');

        $this->assertInstanceOf(MinecraftPlayerStat::class, $item);

        $this->assertEquals(8, $item->broken);
        $this->assertEquals(8, $item->crafted);
        $this->assertNull($item->custom);
        $this->assertNull($item->dropped);
        $this->assertNull($item->killed);
        $this->assertNull($item->killedBy);
        $this->assertNull($item->mined);
        $this->assertNull($item->pickedUp);
        $this->assertEquals(111, $item->used);
        $this->assertEquals('wooden_axe', $item->key);
        $this->assertEquals('Wooden Axe', $item->prettyTerm);
        $this->assertEquals('wooden axe', $item->term);
        $this->assertEquals('item', $item->type);
        $this->assertEquals('amount', $item->unitType);
        $this->assertEquals('user2', $item->username);
        $this->assertEquals('8cb86072-3472-3b86-90f2-1e11b7188197', $item->uuid);
    }

    public function test_should_obtain_statistics_from_the_player_about_a_time_stat(): void
    {
        $stat = $this->minecraftServer->getPlayerStat('user3', 'Time Since Last death');

        $this->assertInstanceOf(MinecraftPlayerStat::class, $stat);

        $this->assertNull($stat->broken);
        $this->assertNull($stat->crafted);
        $this->assertEquals(105092, $stat->custom);
        $this->assertNull($stat->dropped);
        $this->assertNull($stat->killed);
        $this->assertNull($stat->killedBy);
        $this->assertNull($stat->mined);
        $this->assertNull($stat->pickedUp);
        $this->assertNull($stat->used);
        $this->assertEquals('time_since_death', $stat->key);
        $this->assertEquals('Time Since Last Death', $stat->prettyTerm);
        $this->assertEquals('Time Since Last death', $stat->term);
        $this->assertEquals('stat', $stat->type);
        $this->assertEquals('time', $stat->unitType);
        $this->assertEquals('user3', $stat->username);
        $this->assertEquals('8d5b923d-37fb-38d1-8a6a-a17cd5ccf768', $stat->uuid);
    }

    public function test_should_obtain_statistics_from_the_player_about_a_distance_stat(): void
    {
        $stat = $this->minecraftServer->getPlayerStat('uSeR4', 'Distance flown');

        $this->assertInstanceOf(MinecraftPlayerStat::class, $stat);

        $this->assertNull($stat->broken);
        $this->assertNull($stat->crafted);
        $this->assertEquals(35389, $stat->custom);
        $this->assertNull($stat->dropped);
        $this->assertNull($stat->killed);
        $this->assertNull($stat->killedBy);
        $this->assertNull($stat->mined);
        $this->assertNull($stat->pickedUp);
        $this->assertNull($stat->used);
        $this->assertEquals('fly_one_cm', $stat->key);
        $this->assertEquals('Distance Flown', $stat->prettyTerm);
        $this->assertEquals('Distance flown', $stat->term);
        $this->assertEquals('stat', $stat->type);
        $this->assertEquals('distance', $stat->unitType);
        $this->assertEquals('uSeR4', $stat->username);
        $this->assertEquals('14e55460-c753-31f2-bd0a-c305e2ff34b5', $stat->uuid);
    }

    public function test_should_obtain_statistics_from_the_player_about_a_block(): void
    {
        $block = $this->minecraftServer->getPlayerStat('user5', 'granite');

        $this->assertInstanceOf(MinecraftPlayerStat::class, $block);

        $this->assertNull($block->broken);
        $this->assertNull($block->crafted);
        $this->assertNull($block->custom);
        $this->assertNull($block->dropped);
        $this->assertNull($block->killed);
        $this->assertNull($block->killedBy);
        $this->assertEquals(51, $block->mined);
        $this->assertEquals(52, $block->pickedUp);
        $this->assertNull($block->used);
        $this->assertEquals('granite', $block->key);
        $this->assertEquals('Granite', $block->prettyTerm);
        $this->assertEquals('granite', $block->term);
        $this->assertEquals('block', $block->type);
        $this->assertEquals('amount', $block->unitType);
        $this->assertEquals('user5', $block->username);
        $this->assertEquals('18f154fe-3678-37e9-9b77-185e0bfe446d', $block->uuid);
    }
}
