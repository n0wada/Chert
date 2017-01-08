<?php

/**
 * This file is part of the Chert package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) 2017, Naoto Owada <naoto0wada@gmail.com>
 */

namespace Chert\Compiler;

class RouteCollection extends \ArrayIterator
{
    /** @var string */
    private $mtime;

    /**
     * @var int $mtime
     * @var array $routes
     */
    public function __construct($mtime, array $routes = [])
    {
        parent::__construct($routes);
        $this->mtime = $mtime;
    }

    /**
     * @return int mtime
     */
    public function getMTime()
    {
        return $this->mtime;
    }
}