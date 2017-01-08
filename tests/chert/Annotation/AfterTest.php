<?php

use Chert\Annotation\After;
use Chert\Annotation\RouteAbstract;

class AfterTest extends BaseTestCase
{
    public function setUp()
    {
        $this->getApp();
    }

    public function testAfter()
    {
        $route = $this->createMock(RouteAbstract::class);
        $controller = $this->createMock(\Silex\Controller::class);

        $after = new After();

        $after->modify($controller, $this->app, $route);
    }

    public function testModify()
    {
        $after = new After();
        $after->callback = 'method';

        $route = $this->createMock(RouteAbstract::class);
        $route->modifiers[] = $after;

        $controller = $this->app->match($route->path, $route->callback)->method($route->methods)->bind($route->name);

        $after->modify($controller, $this->app, $route);
    }


}