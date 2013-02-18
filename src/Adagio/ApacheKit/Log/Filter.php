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

class Filter extends \FilterIterator
{
    /**
     *
     * @var callable 
     */
    private $callable;
    /**
     *
     * @var string 
     */
    private $filter;

    /**
     *
     * @var string
     */
    private $directive;

    /**
     *
     * @var string
     */
    private $method;

    /**
     *
     * @param Iterator $iterator
     * @param string $directive
     * @param string|callable $filter 
     */
    public function __construct(\Iterator $iterator, $directive, $filter)
    {
        parent::__construct($iterator);

        $this->directive = $directive;
        if (is_callable($filter)) {
            $this->callable = $filter;
            $this->method = 'callable';
            $this->filter = $filter;
        } elseif (false === @preg_match($filter, '')) {
            $this->filter = $filter;
            if ('%' == $filter{0}) {
                $this->method = 'contains';
                $this->filter = substr($filter, 1);
            } else {
                $this->method = 'equals';
            }
        } else {
            $this->filter = $filter;
            $this->method = 'regex';
        }
    }

    /**
     *
     * @return bool 
     */
    public function accept()
    {
        return $this->{$this->method}($this->current());
    }

    /**
     *
     * @param array $line
     * @return bool
     */
    private function callable($line)
    {
        return call_user_func_array($this->callable, array($line, $this->directive));
    }

    /**
     *
     * @param array $line
     * @return bool
     */
    private function regex($line)
    {
        return isset($line[$this->directive]) && preg_match($this->filter, $line[$this->directive]) > 0;
    }

    /**
     *
     * @param array $line
     * @return bool
     */
    private function contains($line)
    {
        return isset($line[$this->directive]) && (false !== stripos($line[$this->directive], $this->filter));
    }

    /**
     *
     * @param array $line
     * @return bool
     */
    private function equals($line)
    {
        return isset($line[$this->directive]) && $this->filter == $line[$this->directive];
    }

    /**
     *
     * @return int
     */
    public function key()
    {
        return $this->getInnerIterator()->key();
    }
}