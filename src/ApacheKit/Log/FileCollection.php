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

class FileCollection implements \IteratorAggregate
{
    /**
     *
     * @var Traversable
     */
    private $files;

    /**
     *
     * @var ParserInterface
     */
    private $parser;

    /**
     *
     * @var Iterator
     */
    private $iterator;

    /**
     *
     * @var array
     */
    private $logFiles = array();

    /**
     *
     * @var int
     */
    private $logSize;

    /**
     *
     * @var int
     */
    private $lineCount;

    /**
     *
     * @var int
     */
    private $position;

    /**
     *
     * @var int
     */
    private $lineCountEstimation;

    /**
     *
     * @param Traversable $files 
     * @param ParserInterface $parser 
     */
    public function __construct($files, ParserInterface $parser = null)
    {
        $this->files = $files;
        $this->parser = $parser;

        $this->getIterator();
    }

    /**
     *
     * @return AppendIterator 
     */
    public function getIterator()
    {
        if (!$this->iterator) {
            $this->iterator = new \AppendIterator();
            foreach ($this->files as $key => $file) {
                if (!is_file($file)) continue;

                if ('application/x-gzip' == mime_content_type($file)) {
                    $this->logFiles[$key] = new GzFile($file);
                } else {
                    $this->logFiles[$key] = new File($file);
                }

                if ($this->parser) {
                    $this->logFiles[$key]->setLogParser($this->parser);
                }

                $this->iterator->append($this->logFiles[$key]);
            }
        }

        return $this->iterator;
    }

    /**
     * 
     */
    public function next()
    {
        parent::next();
        $this->position++;
    }

    /**
     * 
     */
    public function rewind()
    {
        parent::rewind();
        $this->position = 0;
    }

    /**
     *
     * @return int 
     */
    public function key()
    {
        return $this->position;
    }

    /**
     *
     * @return int
     */
    public function getLogSize()
    {
        if (!$this->logSize) {
            $this->logSize = 0;
            foreach ($this->logFiles as $file) {
                /* @var $file File */
                $this->logSize += $file->getLogSize();
            }
        }

        return $this->logSize;
    }

    /**
     * 
     * @return int
     */
    public function countLines()
    {
        if (!$this->lineCount) {
            $this->lineCount = 0;
            foreach ($this->logFiles as $file) {
                /* @var $file File */
                $this->lineCount += $file->countLines();
            }
        }

        return $this->lineCount;
    }

    /**
     * 
     * @return int
     */
    public function estimateLines()
    {
        if (!$this->lineCountEstimation) {
            $this->lineCountEstimation = 0;
            foreach ($this->logFiles as $file) {
                /* @var $file File */
                $this->lineCountEstimation += $file->estimateLines();
            }
        }

        return $this->lineCountEstimation;
    }

    /**
     *
     * @param string $directive
     * @param string $filter
     * @return FileCollection 
     */
    public function filter($directive, $filter = null)
    {
        $this->iterator = new Filter($this->iterator, $directive, $filter);

        return $this;
    }
}