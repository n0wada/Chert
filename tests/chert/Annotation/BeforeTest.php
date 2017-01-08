<?php

use Chert\Annotation\Before;
use Chert\Annotation\RouteAbstract;

class BeforeTest extends BaseTestCase
{
    public function setUp()
    {
        $this->getApp();
    }

    public function testBefore()
    {
        $route = $this->createMock(RouteAbstract::class);
        $controller = $this->createMock(\Silex\Controller::class);

        $before = new Before();

        $before->modify($controller, $this->app, $route);
    }

    public function testModify()
    {
        $before = new Before();
        $before->callback = 'method';

        $route = $this->createMock(RouteAbstract::class);
        $route->modifiers[] = $before;

        $controller = $this->app->match($route->path, $route->callback)->method($route->methods)->bind($route->name);

        $before->modify($controller, $this->app, $route);
    }


}