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

interface RouteModifier
{
    /**
     * @param \Silex\Controller $controller
     * @param \Silex\Application $app
     * @param RouteAbstract $route
     */
    public function modify(Controller $controller, Application $app, RouteAbstract $route);
}