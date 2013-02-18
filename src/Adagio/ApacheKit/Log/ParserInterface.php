<?php

/*
 * This file is part of the Adagio ApacheKit package.
 *
 * (c) Keven <keven@adagiolabs.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adagio\ApacheKit\Log;

interface ParserInterface
{
    /**
     * 
     * @param string $line
     * @return array
     */
    function parseLine($line);
}