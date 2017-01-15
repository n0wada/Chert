<?php

/**
 * This file is part of the Chert package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) 2017, Naoto Owada <naoto0wada@gmail.com>
 */

namespace Chert\Annotation;

use Silex\Application;
use Silex\Controller;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class Assert implements RouteModifier
{
    /** @var string */
    public $variable;

    /** @var string */
    public $regexp;

    /**
     * @inheritdoc
     */
    public function modify(Controller $controller, Application $app, RouteAbstract $route)
    {
        $controller->assert($this->variable, $this->regexp);
    }
}