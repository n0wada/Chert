<?php

use Chert\Annotation\Route;
use Chert\Annotation\RouteAbstract;
use Chert\Annotation\After;
use Chert\Annotation\Before;
use Test\Controller\HomeController;
use Chert\Annotation\RouteModifier;

class RouteTest extends BaseTestCase
{
    public function setUp()
    {
        $this->getApp();
        $config['Chert']['chert.controller_dirs'] = [CONTROLLER_NAMESPACE => CONTROLLER_DIR];
        $config['Chert']['chert.cache_dir'] = CACHE_DIR;

        $this->app->register($this->provider, $config);
    }

    public function testRoute()
    {
        $route = new Route();

        $this->assertEquals("", $route->service);
        $this->assertEquals("", $route->fqcn);
        $this->assertEquals("", $route->path);
        $this->assertEquals("", $route->callback);
        $this->assertEquals('GET', $route->methods);
        $this->assertEquals([], $route->modifiers);
        $this->assertEquals("", $route->name);

        $class = $this->createMock(\ReflectionClass::class);
        $method = $this->createMock(\ReflectionMethod::class);
        $routeAbs = $this->createMock(RouteAbstract::class);

        $route->prepareRoute($class, $method, $routeAbs, [], []);
    }

    public function testPrepareRoute()
    {
        $route = new Route();
        $route->path = "/index";
        $route->name = "home";

        $class = new \ReflectionClass(HomeController::class);
        $method = $class->getMethod("index");
        $routeAbs = $this->createMock(RouteAbstract::class);
        $routeAbs->path = "/classpath";

        $route->prepareRoute($class, $method, $routeAbs, ['class'], ['method']);

        $this->assertEquals("", $route->service);
        $this->assertEquals(HomeController::class, $route->fqcn);
        $this->assertEquals("/classpath/index", $route->path);
        $this->assertEquals(HomeController::class . "::" . "index", $route->callback);
        $this->assertEquals('GET', $route->methods);
        $this->assertEquals("home", $route->name);
        $this->assertTrue(is_array($route->modifiers));

        $routeAbs->service = "HomeService";

        $route->prepareRoute($class, $method, $routeAbs, ['class'], ['method']);

        $this->assertEquals("HomeService", $routeAbs->service);
        $this->assertEquals("", $route->service);
        $this->assertEquals(HomeController::class, $route->fqcn);
        $this->assertEquals("/classpath/classpath/index", $route->path);
        $this->assertEquals("HomeService:index", $route->callback);
        $this->assertEquals('GET', $route->methods);
        $this->assertEquals("home", $route->name);
        $this->assertTrue(is_array($route->modifiers));
    }

    public function testPrepareRoute2()
    {
        $reader = $this->app['chert.reader'];

        $class = new \ReflectionClass(HomeController::class);

        $method = $class->getMethod("index");
        $cAnnot = $reader->getClassAnnotations($class);
        $Annot = $reader->getMethodAnnotations($method);

        $cRoute = $this->filter($cAnnot, RouteAbstract::class);
        $cModifiers = $this->filter($cAnnot, RouteModifier::class);

        $route = $this->filter($Annot, RouteAbstract::class);
        $modifiers = $this->filter($Annot, RouteModifier::class);

        $route->prepareRoute($class, $method, $cRoute, $cModifiers, $modifiers);

        $this->assertEquals("", $route->service);
        $this->assertEquals(HomeController::class, $route->fqcn);
        $this->assertEquals("/home/index", $route->path);
        $this->assertEquals(HomeController::class . "::" . "index", $route->callback);
        $this->assertEquals('GET', $route->methods);
        $this->assertEquals("home.index", $route->name);
        $this->assertTrue(is_array($route->modifiers));
        $this->assertInstanceOf(After::class, $route->modifiers[0]);
        $this->assertInstanceOf(Before::class, $route->modifiers[1]);
    }

    public function filter(array $annotations, $type)
    {
        $types = array_filter($annotations, function ($annotation) use ($type) {

            return $annotation instanceof $type;
        });

        return ($type === RouteAbstract::class) ? reset($types) : $types;
    }

}