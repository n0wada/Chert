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
class Before implements RouteModifier
{
    /** @var mixed */
    public $callback;

    /**
     * @inheritdoc
     */
    public function modify(Controller $controller, Application $app, RouteAbstract $route)
    {
        if (!$app['callback_resolver']->isValid($callback = $this->callback)) {
            $callback = $route->fqcn . '::' . $callback;
        }

        $controller->before($callback);
    }
}