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

class UnknownUsernameException extends \Exception
{
    public function __construct(string $username)
    {
        $message = 'User ' . $username . ' was not found on the server.';

        parent::__construct($message);
    }
}
