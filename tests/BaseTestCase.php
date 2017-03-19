<?php
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    /** @var \Silex\Application */
    public $app;
    /** @var \Chert\RoutingServiceProvider */
    public $provider;

    public function setUp()
    {
    }

    function getApp()
    {
        $this->app = new Silex\Application();
        $this->provider = new Chert\RoutingServiceProvider();
    }

    function bootApp()
    {
        $config['Chert']['chert.controller_dirs'] = [CONTROLLER_NAMESPACE => CONTROLLER_DIR];
        $config['Chert']['chert.cache_dir'] = CACHE_DIR;

        $this->app->register($this->provider, $config);

        $this->app->boot();
    }

    public function toPublic($obj, $method)
    {
        $method = new \ReflectionMethod($obj, $method);
        $method->setAccessible(true);
        return $method->getClosure($obj);
    }

    public function tearDown()
    {
        unset($this->app);
        unset($this->provider);
    }
}