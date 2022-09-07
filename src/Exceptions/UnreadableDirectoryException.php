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

class UnreadableDirectoryException extends \Exception
{
    public function __construct(string $path)
    {
        $message = 'The directory path "' . $path . '" is not readable.';

        parent::__construct($message);
    }
}
