<?php

/**
 * This file is part of the Chert package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) 2017, Naoto Owada <naoto0wada@gmail.com>
 */

namespace Chert\Annotation;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class RouteAbstract
{
    /** @var string */
    public $path = "";

    /** @var string|array<string> */
    public $methods = 'GET';

    /** @var string */
    public $name = "";

    /** @var string */
    public $callback = "";

    /** @var string */
    public $fqcn = "";

    /** @var string */
    public $service = "";

    /** @var array<RouteModifier> */
    public $modifiers = [];

    /**
     * @var \ReflectionClass $class
     * @var \ReflectionMethod $method
     * @var RouteAbstract $cRoute
     * @var array $cModifiers
     * @var array $modifiers
     */
    public function prepareRoute(\ReflectionClass $class, \ReflectionMethod $method, RouteAbstract $cRoute, $cModifiers, $modifiers)
    {
    }
}