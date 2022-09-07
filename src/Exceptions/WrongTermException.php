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

class WrongTermException extends \Exception
{
    public function __construct(string $term)
    {
        $message = 'The term "' . $term . '" is not valid.';

        parent::__construct($message);
    }
}
