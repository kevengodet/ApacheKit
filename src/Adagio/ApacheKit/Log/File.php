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

class File extends \SplFileObject implements FileInterface
{
    const AVG_LINE_LENGTH = 276;

    /**
     *
     * @var ParserInterface
     */
    private $parser;

    /**
     *
     * @param ParserInterface $parser 
     */
    public function setLogParser(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     *
     * @return mixed
     */
    public function current()
    {
        if ($this->parser) {
            return $this->parser->parseLine(parent::current());
        }

        return parent::current();
    }

    /**
     * 
     * @return int
     */
    public function getLogSize()
    {
        return $this->getSize();
    }

    /**
     *
     * @return int
     */
    public function countLines()
    {
        return substr_count(file_get_contents($this->getPathname()), "\n");
    }

    /**
     *
     * @return int
     */
    public function estimateLines()
    {
        return round($this->getLogSize() / self::AVG_LINE_LENGTH);
    }
}