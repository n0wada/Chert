<?php

/**
 * This file is part of the Chert package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) 2017, Naoto Owada <naoto0wada@gmail.com>
 */

namespace Chert\Annotation;

use Silex\Controller;
use Silex\Application;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class Value implements RouteModifier
{
    /** @var string */
    public $variable;

    /** @var mixed */
    public $default;

    /**
     * @inheritdoc
     */
    public function modify(Controller $controller, Application $app, RouteAbstract $route)
    {
        $controller->value($this->variable, $this->default);
    }
}