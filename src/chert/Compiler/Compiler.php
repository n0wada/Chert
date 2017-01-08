<?php

/**
 * This file is part of the Chert package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) 2017, Naoto Owada <naoto0wada@gmail.com>
 */

namespace Chert\Compiler;

use Pimple\Container;
use Silex\Application;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Annotations\Reader;
use Chert\Annotation\RouteModifier;
use Chert\Annotation\RouteAbstract;
use Symfony\Component\Finder\Finder;

/**
 *  Compiler Servise Class
 */
class Compiler
{
    /** @var \Silex\Application */
    private $app;
    /** @var \Doctrine\Common\Annotations\Reader */
    private $reader;
    /** @var \Doctrine\Common\Cache\Cache */
    private $cache;
    /** @var int */
    private $lifetime;

    /**
     * @param Container|Application $app
     * @param Reader $reader
     * @param Cache $cache
     * @param int $lifetime
     */
    public function __construct(Application $app, Reader $reader, Cache $cache, int $lifetime)
    {
        $this->app = $app;
        $this->reader = $reader;
        $this->cache = $cache;
        $this->lifetime = $lifetime;
    }

    /**
     * @param string $namespace
     * @param string $path
     * @throws \RuntimeException
     */
    public function chart(string $namespace, string $path)
    {
        $finder = Finder::create()->files()->in($path);

        foreach ($finder as $file) {

            $fqcn = $namespace . DIRECTORY_SEPARATOR . rtrim($file->getRelativePathname(), '.php');

            $routes = $this->fetch($fqcn, $file);

            if (!$routes) {
                $routes = $this->compile(new \ReflectionClass($fqcn), new RouteCollection($file->getMTime()));
                $this->save($fqcn, $routes, $this->lifetime);
            }

            foreach ($routes as $route) {
                $this->routing($route);
            }
        }
    }

    /**
     * Fetches an RouteCollection from the cache.
     *
     * @param string $fqcn
     * @param \SplFileInfo $file
     * @return RouteCollection|bool
     */
    protected function fetch(string $fqcn, \SplFileInfo $file)
    {
        $routes = $this->cache->fetch($fqcn);

        if (!$routes || $routes->getMTime() !== $file->getMTime()) return false;

        return $routes;
    }

    /**
     * Maps a pattern to a callable.
     *
     * @param RouteAbstract $route
     */
    protected function routing(RouteAbstract $route)
    {
        $controller = $this->app->match($route->path, $route->callback)
            ->method($route->methods)
            ->bind($route->name);

        foreach ($route->modifiers as $modifier) {
            /** @var RouteModifier $modifier */
            $modifier->modify($controller, $this->app, $route);
        }
    }

    /**
     * @param \ReflectionClass $class
     * @param RouteCollection $routes
     * @return RouteCollection $routes
     */
    protected function compile(\ReflectionClass $class, RouteCollection $routes)
    {
        $annotations = $this->reader->getClassAnnotations($class);

        $cRoute = $this->filter($annotations, RouteAbstract::class);
        $cModifiers = $this->filter($annotations, RouteModifier::class);

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {

            $annotations = $this->reader->getMethodAnnotations($method);

            $route = $this->filter($annotations, RouteAbstract::class);
            $modifiers = $this->filter($annotations, RouteModifier::class);

            if (!is_subclass_of($route, RouteAbstract::class)) continue;

            $route->prepareRoute($class, $method, $cRoute, $cModifiers, $modifiers);

            $routes->append($route);
        }

        return $routes;
    }

    /**
     * @param array $annotations
     * @param string $type
     * @return RouteAbstract|array
     */
    protected function filter(array $annotations, $type)
    {
        $types = array_filter($annotations, function ($annotation) use ($type) {
            return $annotation instanceof $type;
        });

        if ($type === RouteAbstract::class) {
            return reset($types) ?: new RouteAbstract();
        }
        return $types;
    }

    /**
     * @param string $id
     * @param RouteCollection $routes
     * @param int $lifeTime
     * @throws \RuntimeException
     */
    protected function save(string $id, RouteCollection $routes, int $lifeTime)
    {
        if (!$this->cache->save($id, $routes, $lifeTime)) {
            throw new \RuntimeException(sprintf('Failed to saving cache, CacheID: %s', $id));
        }
    }
}