<?php

use Chert\Annotation\Assert;
use Chert\Annotation\RouteAbstract;

class AssetTest extends BaseTestCase
{
    public function setUp()
    {
        $this->getApp();
    }

    public function testAssert()
    {
        $route = $this->createMock(RouteAbstract::class);
        $controller = $this->createMock(\Silex\Controller::class);

        $assert = new Assert();

        $assert->modify($controller, $this->app, $route);
    }

    public function testModify()
    {
        $assert = new Assert();
        $assert->variable = 'method';
        $assert->regexp = '^test$';

        $route = $this->createMock(RouteAbstract::class);
        $route->modifiers[] = $assert;

        $controller = $this->app->match($route->path, $route->callback)->method($route->methods)->bind($route->name);

        $assert->modify($controller, $this->app, $route);
    }


}