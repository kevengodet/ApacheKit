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

class GzFile extends File
{
    /**
     *
     * @var string
     */
    private $filePath;

    /**
     *
     * @param string $filepath 
     */
    public function __construct($filepath)
    {
        $this->filePath = $filepath;

        parent::__construct("compress.zlib://$filepath");
    }

    /**
     *
     * @see Zend Framework
     * @return int
     */
    public function getLogSize()
    {
        if (!$handler = fopen($this->filePath, "rb")) {
            throw new \Exception("Error opening the archive $this->filePath");
        }

        fseek($handler, -4, SEEK_END);
        $packet = fread($handler, 4);
        $bytes  = unpack("V", $packet);
        $size   = end($bytes);
        fclose($handler);

        return $size;
    }
}