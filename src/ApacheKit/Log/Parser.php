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

class Parser implements ParserInterface
{
    /**
     *
     * @var string
     */
    private $logFormat;

    /**
     *
     * @var string
     */
    private $pattern;

    /**
     *
     * @var array
     */
    private $directives = array(
        '%v' => '(?<ServerName>[^:]+)',
        '%p' => '(?<ServerPort>\S+)',
        '%h' => '(?<RemoteHost>\S+)',
        '%l' => '(?<RemoteLogname>\S+)',
        '%u' => '(?<RemoteUser>\S+)',
        '%t' => '\[(?<Time>[^]]+)\]',
        '%r' => '(?<Request>(?<Method>\S+) (?<Url>\S+) (?<HttpVersion>[^"]+))',
        '%>s' => '(?<Status>\S+)',
        '%O' => '(?<ByteSent>\S+)',
        '%{Referer}i' => '(?<Referer>[^"]+)',
        '%{User-Agent}i' => '(?<UserAgent>[^"]+)',
    );

    /**
     *
     * @var array
     */
    private $filters = array();

    /**
     *
     * @param string $logFormat
     * @param array $directives 
     */
    public function __construct($logFormat, $directives = array())
    {
        $this->logFormat = $logFormat;
        $this->pattern = $this->generatePattern($logFormat);
    }

    /**
     *
     * @param string $logFormat
     * @return string
     */
    private function generatePattern($logFormat)
    {
        $pattern = str_replace('\\"', '"', $logFormat);

        foreach ($this->directives as $directive => $replacement) {
            $pattern = str_replace($directive, $replacement, $pattern);
        }

        $pattern = "/^$pattern$/";

        return $pattern;
    }

    /**
     *
     * @param string $directive
     * @param string|callable $filter 
     */
    public function addFilter($directive, $filter)
    {
        if (!isset($this->filters[$directive])) {
            $this->filters[$directive] = array();
        }

        $this->filters[$directive][] = $filter;
    }

    /**
     *
     * @param string $line
     * @return array or false if the pattern does not match, or filters are
     *           not fullfilled
     */
    public function parseLine($line)
    {
        if (!preg_match($this->pattern, $line, $matches)) {
            return false;
        }
//echo "<td colspan=14>$line</td></tr><tr>";
        // Removes numeric keys
        $matches = array_diff_key($matches, range(0, count($matches) >> 1));

        foreach ($this->filters as $directive => $filters) {
            if (!isset($matches[$directive])) {
                return false;
            }

            foreach ($filters as $filter) {
                if (is_callable($filter)
                        && !call_user_func($filter, $matches[$directive])) {
                    return false;
                } elseif (!preg_match($filter, $matches[$directive])) {
                    return false;
                }
            }
        }

        return $matches;
    }
}