<?php

/**
 * This file is part of the Chert package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) 2017, Naoto Owada <naoto0wada@gmail.com>
 */

namespace Chert;

use Chert\Compiler\Compiler;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\FilesystemCache;
use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

/**
 * Route Annotation Provider
 */
class RouteCompileServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['chert.cache_dir'] = "";
        $app['chert.cache_lifetime'] = 0;
        $app['chert.controller_dirs'] = [];
        $app['chert.annotation_dirs'] = ['Chert\Annotation' => dirname(__DIR__)];

        $app['chert.reader'] = function ($app) {
            return new AnnotationReader();
        };

        $app['chert.cache'] = function ($app) {
            return new FilesystemCache($app['chert.cache_dir']);
        };

        $app['chert.compiler'] = function ($app) {
            return new Compiler($app, $app['chert.reader'], $app['chert.cache'], $app['chert.cache_lifetime']);
        };
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
        AnnotationRegistry::registerAutoloadNamespaces($app['chert.annotation_dirs']);

        foreach ($app['chert.controller_dirs'] as $namespace => $path) {
            $app['chert.compiler']->chart($namespace, $path);
        }
    }
}