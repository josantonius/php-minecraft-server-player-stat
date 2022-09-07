<?php

/*
 * This file is part of https://github.com/josantonius/php-minecraft-server-player-stat repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josantonius\MinecraftServerPlayerStat;

class MinecraftPlayerStat
{
    /**
     * Create new object.
     *
     * @param ?int $broken   Number of broken items or null if there are no statistics.
     * @param ?int $crafted  Number of crafted items or null if there are no statistics.
     * @param ?int $dropped  Number of dropped items or null if there are no statistics.
     * @param ?int $killed   Number of killed mobs or null if there are no statistics.
     * @param ?int $killedBy Number of killed by mobs or null if there are no statistics.
     * @param ?int $mined    Number of mined items or null if there are no statistics.
     * @param ?int $pickedUp Number of picked up items or null if there are no statistics.
     * @param ?int $used     Number of used items or null if there are no statistics.
     *
     * @param ?int $custom Custom statistics value or null if there are no statistics.
     *                     * If the unit type is distance, this value is given in centimeters.
     *                     * If the unit type is time, this value is given in ticks.
     *
     * @param string $key        Sanitized key for the term.
     * @param string $prettyTerm Term expressed in a nice way.
     * @param string $term       Query term.
     * @param string $type       Item type. Available types: block, entity, item, stat.
     * @param string $unitType   Unit type. Available types: amount, distance, time.
     * @param string $username   Username for which the query is being made.
     * @param string $uuid       User UUID for which the query is being made.
     *
     * @see https://minecraft.fandom.com/wiki/Tutorials/Units_of_measure to see the unit conversions.
     */
    public function __construct(
        public readonly ?int $broken,
        public readonly ?int $crafted,
        public readonly ?int $custom,
        public readonly ?int $dropped,
        public readonly ?int $killed,
        public readonly ?int $killedBy,
        public readonly ?int $mined,
        public readonly ?int $pickedUp,
        public readonly ?int $used,
        public readonly string $key,
        public readonly string $prettyTerm,
        public readonly string $term,
        public readonly string $type,
        public readonly string $unitType,
        public readonly string $username,
        public readonly string $uuid,
    ) {
    }
}
