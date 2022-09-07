<?php

/*
 * This file is part of https://github.com/josantonius/php-minecraft-server-player-stat repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josantonius\MinecraftServerPlayerStat\Exceptions;

use Throwable;

class StatsNotFoundException extends \Exception
{
    public function __construct(string $username, Throwable|null $previous = null)
    {
        $message = 'Stats for user ' . $username . ' were not found.';

        parent::__construct($message, 0, $previous);
    }
}
