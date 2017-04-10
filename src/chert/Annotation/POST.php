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
class POST extends RouteAbstract
{
    /**
     * @var \ReflectionClass $class
     * @var \ReflectionMethod $method
     * @var RouteAbstract $cRoute
     * @var array $cModifiers
     * @var array $modifiers
     */
    public function prepareRoute(\ReflectionClass $class, \ReflectionMethod $method, RouteAbstract $cRoute, $cModifiers, $modifiers)
    {
        $this->path = rtrim($cRoute->path, '/') . '/' . ltrim($this->path, '/');

        $this->fqcn = $class->name;

        $this->methods = "POST";

        if ($cRoute->service) {
            $this->callback = $cRoute->service . ":" . $method->name;
        } else if ($this->service) {
            $this->callback = $this->service . ":" . $method->name;
        } else {
            $this->callback = $class->name . "::" . $method->name;
        }

        $this->modifiers = array_merge($cModifiers, $modifiers);
    }
}