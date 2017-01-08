<?php

use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class WebTestBase extends WebTestCase
{
    /** @var \Chert\RouteCompileServiceProvider */
    public $provider;

    public function createApplication()
    {
        $config['Chert']['chert.controller_dirs'] = [CONTROLLER_NAMESPACE => CONTROLLER_DIR];
        $config['Chert']['chert.cache_dir'] = CACHE_DIR;

        $app = new Silex\Application();
        $this->provider = new Chert\RouteCompileServiceProvider();
        $app->register($this->provider, $config['Chert']);

        $this->provider->boot($app);

        $app->error(function (\Exception $e, Request $request, $code) use ($app) {
            switch ($code) {
                case 404:
                    $message = '404 The requested page could not be found.';

                    break;
                default:
                    $message = 'We are sorry, but something went terribly wrong.';
            }

            return new JsonResponse($message);
        });

        return $app;
    }

    public function tearDown()
    {
        unset($this->app);
    }
}

class UserConverter
{
    public function convert($title)
    {
        return $title . " Success!!";
    }
}