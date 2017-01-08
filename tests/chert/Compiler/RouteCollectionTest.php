<?php

use Chert\Compiler\RouteCollection;
use Chert\Annotation\RouteAbstract;

class RouteCollectionTest extends BaseTestCase
{
    public function setUp()
    {
        $this->getApp();
    }

    public function testRouteCollection()
    {
        $route = $this->createMock(RouteAbstract::class);
        $col1 = new RouteCollection(1);
        $col2 = new RouteCollection(1, [$route]);

        $this->assertEquals(1, $col1->getMTime());
        $this->assertEquals(1, $col2->getMTime());

        $this->assertEquals(0, $col1->count());
        $this->assertEquals(1, $col2->count());
        $col1->append([]);
        $col2->append([]);
        $this->assertEquals(1, $col1->count());
        $this->assertEquals(2, $col2->count());
    }

}