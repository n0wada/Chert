<?php

use Silex\Application;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Annotations\Reader;
use Chert\Compiler\Compiler;
use Chert\Compiler\RouteCollection;
use Chert\Annotation\RouteAbstract;
use Chert\Annotation\RouteModifier;
use Test\Controller;
use Symfony\Component\Finder\Finder;

class CompilerTest extends BaseTestCase
{
    public function setUp()
    {
        $this->getApp();
        $config['Chert']['chert.controller_dirs'] = [CONTROLLER_NAMESPACE => CONTROLLER_DIR];
        $config['Chert']['chert.cache_dir'] = CACHE_DIR;
        $this->app->register($this->provider, $config['Chert']);
    }

    public function testFetchSuccess()
    {
        $fqcn = "dummy";
        $file = new \SplFileInfo(__FILE__);
        $routes = new RouteCollection($file->getMTime());

        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);
        $cache->expects($this->any())->method('fetch')->with($this->anything())->willReturn($routes);

        $compiler = new Compiler($this->app, $reader, $cache, 0);
        $fetch = $this->toPublic($compiler, 'fetch');

        $rev = $fetch($fqcn, $file);

        $this->assertInstanceOf(RouteCollection::class, $rev);
    }

    public function testFetchFail()
    {
        $fqcn = "dummy";
        $file = new \SplFileInfo(__FILE__);

        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);
        $cache->expects($this->any())->method('fetch')->with($this->anything())->willReturn(false);

        $compiler = new Compiler($this->app, $reader, $cache, 0);
        $fetch = $this->toPublic($compiler, 'fetch');

        $rev = $fetch($fqcn, $file);

        $this->assertNotTrue($rev);
    }

    public function testFetchOld()
    {
        $fqcn = "dummy";
        $file = new \SplFileInfo(__FILE__);

        $routes = new RouteCollection($file->getMTime() - 1);

        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);
        $cache->expects($this->any())->method('fetch')->with($this->anything())->willReturn($routes);

        $compiler = new Compiler($this->app, $reader, $cache, 0);
        $fetch = $this->toPublic($compiler, 'fetch');

        $rev = $fetch($fqcn, $file);

        $this->assertNotTrue($rev);
    }

    public function testFilter()
    {
        $filter = $this->toPublic($this->app['chert.compiler'], 'filter');

        $test = function ($annots, $type) use ($filter) {
            $rev = $filter($annots, $type);
            if (!is_array($rev)) $this->assertTrue(false);
            foreach ($rev as $var) $this->assertInstanceOf($type, $var);
        };

        $array = [];
        $this->assertNotTrue($filter($array, RouteAbstract::class));

        $annots[] = $route1 = $this->createMock(RouteAbstract::class);

        $test($annots, RouteAbstract::class);
        $this->assertEquals([], $filter($annots, RouteModifier::class));

        $annots[] = $this->createMock(RouteModifier::class);

        $test($annots, RouteAbstract::class);
        $test($annots, RouteModifier::class);

        $annots[] = $route2 = $this->createMock(RouteAbstract::class);

        $test($annots, RouteAbstract::class);
        $test($annots, RouteModifier::class);

        $this->assertSame(current($filter($annots, RouteAbstract::class)), $route1);

        $annots[] = $this->createMock(RouteModifier::class);

        $test($annots, RouteAbstract::class);
        $test($annots, RouteModifier::class);

        $this->assertSame(current($filter($annots, RouteAbstract::class)), $route1);
        $this->assertNotSame(current($filter($annots, RouteAbstract::class)), $route2);
    }

    public function testCompile()
    {
        $class = new \ReflectionClass(Controller\HomeController::class);
        $app = $this->createMock(Application::class);
        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);
        $routeCollection = $this->createMock(RouteCollection::class);
        $routeAbs = $this->createMock(RouteAbstract::class);
        $routeAbs->expects($this->atLeastOnce())->method('prepareRoute');
        $annots[] = $routeAbs;
        $annots[] = $this->createMock(RouteModifier::class);
        $reader->expects($this->once())->method('getClassAnnotations')->willReturn($annots);
        $reader->expects($this->any())->method('getMethodAnnotations')->willReturn($annots);

        $compiler = new Compiler($app, $reader, $cache, 0);
        $compile = $this->toPublic($compiler, 'compile');

        $routes = $compile($class, $routeCollection);

        $this->assertInstanceOf(RouteCollection::class, $routes);

        foreach ($routes as $route) {
            $this->assertInstanceOf(RouteAbstract::class, $route);
            foreach ($route->modifiers as $var) {
                $this->assertInstanceOf(RouteModifier::class, $var);
            }
        }
    }

    public function testCompileAnnotNotExists()
    {
        $class = new \ReflectionClass(Controller\NoAnnotationController::class);
        $app = $this->createMock(Application::class);
        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);
        $routeCollection = $this->createMock(RouteCollection::class);
        $routeAbs = $this->createMock(RouteAbstract::class);
        $routeAbs->expects($this->never())->method('prepareRoute');
        $annots[] = $routeAbs;
        $annots[] = $this->createMock(RouteModifier::class);
        $reader->expects($this->once())->method('getClassAnnotations')->willReturn([]);
        $reader->expects($this->never())->method('getMethodAnnotations')->willReturn([]);

        $compiler = new Compiler($app, $reader, $cache, 0);
        $compile = $this->toPublic($compiler, 'compile');

        $routes = $compile($class, $routeCollection);

        $this->assertInstanceOf(RouteCollection::class, $routes);
        foreach ($routes as $route) $this->fail($route);
    }

    public function testCompileClassAnnotNotExists()
    {
        $class = new \ReflectionClass(Controller\NoClassAnnotationController::class);
        $app = $this->createMock(Application::class);
        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);
        $routeCollection = $this->createMock(RouteCollection::class);
        $routeAbs = $this->createMock(RouteAbstract::class);
        $routeAbs->expects($this->never())->method('prepareRoute');
        $annots[] = $routeAbs;
        $annots[] = $this->createMock(RouteModifier::class);
        $reader->expects($this->once())->method('getClassAnnotations')->willReturn([]);
        $reader->expects($this->never())->method('getMethodAnnotations')->willReturn([]);

        $compiler = new Compiler($app, $reader, $cache, 0);
        $compile = $this->toPublic($compiler, 'compile');

        $routes = $compile($class, $routeCollection);

        $this->assertInstanceOf(RouteCollection::class, $routes);
        foreach ($routes as $route) $this->fail($route);
    }


    public function testCompileMethodAnnotNotExists()
    {
        $class = new \ReflectionClass(Controller\NoMethodAnnotationController::class);
        $app = $this->createMock(Application::class);
        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);
        $routeCollection = $this->createMock(RouteCollection::class);
        $routeAbs = $this->createMock(RouteAbstract::class);
        $routeAbs->expects($this->never())->method('prepareRoute');
        $annots[] = $routeAbs;
        $annots[] = $this->createMock(RouteModifier::class);
        $reader->expects($this->once())->method('getClassAnnotations')->willReturn($annots);
        $reader->expects($this->any())->method('getMethodAnnotations')->willReturn([]);

        $compiler = new Compiler($app, $reader, $cache, 0);
        $compile = $this->toPublic($compiler, 'compile');

        $routes = $compile($class, $routeCollection);

        $this->assertInstanceOf(RouteCollection::class, $routes);
        foreach ($routes as $route) $this->fail($route);
    }

    public function testSaveSuccess()
    {
        $app = $this->createMock(Application::class);
        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);
        $routeCollection = $this->createMock(RouteCollection::class);
        $cache->expects($this->once())->method('save')->willReturn(true);

        $compiler = new Compiler($app, $reader, $cache, 0);

        $save = $this->toPublic($compiler, 'save');
        $save('test', $routeCollection, 0);
    }

    public function testSaveFail()
    {
        $app = $this->createMock(Application::class);
        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);
        $routeCollection = $this->createMock(RouteCollection::class);
        $cache->expects($this->once())->method('save')->willReturn(false);

        $compiler = new Compiler($app, $reader, $cache, 0);

        $save = $this->toPublic($compiler, 'save');

        try {
            $save('test', $routeCollection, 0);
            $this->fail('PHPUnit fail');
        } catch (Exception $e) {
            $this->assertSame('Failed to saving cache, CacheID: test', $e->getMessage());
        }
    }

    public function testRouting()
    {
        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);

        $compiler = new Compiler($this->app, $reader, $cache, 0);

        $route = $this->createMock(RouteAbstract::class);
        $modifier = $this->createMock(RouteModifier::class);
        $modifier->expects($this->once())->method('modify');
        $route->modifiers[] = $modifier;

        $routing = $this->toPublic($compiler, 'routing');
        $routing($route);

        $controller = $this->app->match($route->path, $route->callback)->method($route->methods)->bind($route->name);

        $this->assertInstanceOf(\Silex\Controller::class, $controller);
    }

    public function testChartNoCache()
    {
        $namespace = CONTROLLER_NAMESPACE;
        $path = CONTROLLER_DIR;

        $app = $this->createMock(Application::class);
        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);
        $routeCollection = $this->createMock(RouteCollection::class);
        $annots[] = $this->createMock(RouteAbstract::class);
        $annots[] = $this->createMock(RouteModifier::class);
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->willReturn($annots);
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->willReturn($annots);
        // no cache
        $cache->expects($this->atLeastOnce())->method('fetch')->willReturn(false);
        $cache->expects($this->atLeastOnce())->method('save')->willReturn(true);

        $compiler = new Compiler($app, $reader, $cache, 0);

        $fetch = $this->toPublic($compiler, 'fetch');
        $compile = $this->toPublic($compiler, 'compile');
        $save = $this->toPublic($compiler, 'save');
        $routing = $this->toPublic($compiler, 'routing');

        $finder = Finder::create()->files()->in($path);

        foreach ($finder as $file) {

            $fqcn = $namespace . DIRECTORY_SEPARATOR . rtrim($file->getRelativePathname(), '.php');

            $routes = $fetch($fqcn, $file);

            if (!$routes) {
                $routes = $compile(new \ReflectionClass($fqcn), $routeCollection);
                $save($fqcn, $routes, 100);
            }

            foreach ($routes as $route) {
                $route instanceof RouteAbstract && $routing($route);
            }
        }
    }

    public function testChartCacheExist()
    {
        $namespace = CONTROLLER_NAMESPACE;
        $path = CONTROLLER_DIR;

        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);
        $route = $this->createMock(RouteAbstract::class);
        $routeCollection = new RouteCollection(1, [$route]);
        $annots[] = $this->createMock(RouteAbstract::class);
        $annots[] = $this->createMock(RouteModifier::class);
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->willReturn($annots);
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->willReturn($annots);
        // exist cache
        $cache->expects($this->atLeastOnce())->method('fetch')->willReturn($routeCollection);
        $cache->expects($this->atLeastOnce())->method('save')->willReturn(true);

        $compiler = new Compiler($this->app, $reader, $cache, 0);

        $fetch = $this->toPublic($compiler, 'fetch');
        $compile = $this->toPublic($compiler, 'compile');
        $save = $this->toPublic($compiler, 'save');
        $routing = $this->toPublic($compiler, 'routing');

        $finder = Finder::create()->files()->in($path);

        foreach ($finder as $file) {

            $fqcn = $namespace . DIRECTORY_SEPARATOR . rtrim($file->getRelativePathname(), '.php');

            $routes = $fetch($fqcn, $file);

            if (!$routes) {
                $routes = $compile(new \ReflectionClass($fqcn), $routeCollection);
                $save($fqcn, $routes, 100);
            }

            foreach ($routes as $route) {
                $route instanceof RouteAbstract && $routing($route);
            }
        }
    }

    public function testChartCacheExistMockRoute()
    {
        $namespace = CONTROLLER_NAMESPACE;
        $path = CONTROLLER_DIR;

        $app = $this->createMock(Application::class);
        $reader = $this->createMock(Reader::class);
        $cache = $this->createMock(Cache::class);
        $routeCollection = $this->createMock(RouteCollection::class);
        $annots[] = $this->createMock(RouteAbstract::class);
        $annots[] = $this->createMock(RouteModifier::class);
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->willReturn($annots);
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->willReturn($annots);
        // exist cache
        $cache->expects($this->atLeastOnce())->method('fetch')->willReturn($routeCollection);
        $cache->expects($this->atLeastOnce())->method('save')->willReturn(true);

        $compiler = new Compiler($app, $reader, $cache, 0);

        $fetch = $this->toPublic($compiler, 'fetch');
        $compile = $this->toPublic($compiler, 'compile');
        $save = $this->toPublic($compiler, 'save');
        $routing = $this->toPublic($compiler, 'routing');

        $finder = Finder::create()->files()->in($path);

        foreach ($finder as $file) {

            $fqcn = $namespace . DIRECTORY_SEPARATOR . rtrim($file->getRelativePathname(), '.php');

            $routes = $fetch($fqcn, $file);

            if (!$routes) {
                $routes = $compile(new \ReflectionClass($fqcn), $routeCollection);
                $save($fqcn, $routes, 100);
            }

            foreach ($routes as $route) {

                $route instanceof RouteAbstract && $routing($route);
                $this->assertTrue(false);
            }
        }
    }
}