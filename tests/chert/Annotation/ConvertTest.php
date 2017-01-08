<?php

use Chert\Annotation\Convert;
use Chert\Annotation\RouteAbstract;

class ConvertTest extends BaseTestCase
{
    public function setUp()
    {
        $this->getApp();
    }

    public function testConvert()
    {
        $route = $this->createMock(RouteAbstract::class);
        $controller = $this->createMock(\Silex\Controller::class);

        $convert = new Convert();

        $convert->modify($controller, $this->app, $route);
    }

    public function testModify()
    {
        $convert = new Convert();
        $convert->variable = 'variable';
        $convert->callback = 'method';

        $route = $this->createMock(RouteAbstract::class);
        $route->modifiers[] = $convert;

        $controller = $this->app->match($route->path, $route->callback)->method($route->methods)->bind($route->name);

        $convert->modify($controller, $this->app, $route);
    }


}