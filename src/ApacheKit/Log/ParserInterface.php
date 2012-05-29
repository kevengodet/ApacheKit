<?php

/*
 * This file is part of the ApacheKit package.
 *
 * (c) Keven <keven.mail@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ApacheKit\Log;

interface ParserInterface
{
    /**
     * 
     * @param string $line
     * @return array
     */
    function parseLine($line);
}