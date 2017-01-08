<?php

use Chert\Annotation\Value;
use Chert\Annotation\RouteAbstract;

class ValueTest extends BaseTestCase
{
    public function setUp()
    {
        $this->getApp();
    }

    public function testValue()
    {
        $route = $this->createMock(RouteAbstract::class);
        $controller = $this->createMock(\Silex\Controller::class);

        $value = new Value();

        $value->modify($controller, $this->app, $route);
    }

    public function testModify()
    {
        $value = new Value();
        $value->variable = 'id';
        $value->default = 'test';

        $route = $this->createMock(RouteAbstract::class);
        $route->modifiers[] = $value;

        $controller = $this->app->match($route->path, $route->callback)->method($route->methods)->bind($route->name);

        $value->modify($controller, $this->app, $route);
    }


}