<?php

/*
 * This file is part of the Adagio ApacheKit package.
 *
 * (c) Keven <keven@adagiolabs.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adagio\ApacheKit\Mod;

/**
 * 
 * @see http://code.google.com/p/mod-auth-token/
 */
class AuthToken
{
    /**
     *
     * @var string
     */
    private $secret;

    /**
     * 
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * 
     * @param string $relativePath File path relative to the AuthToken location
     *                             it should start with a '/'
     * @param string $ip Optional IP address to limit the token access
     */
    public function generate($relativePath, $ip = null)
    {
        $time = dechex(time());
        $phrase = $this->secret.$relativePath.$time;

        if ($ip) {
            $phrase .= $ip;
        }

        $token = md5($phrase);

        return $token.'/'.$time.$relativePath;
    }
}