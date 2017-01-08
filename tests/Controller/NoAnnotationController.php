<?php
namespace Test\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class NoAnnotationController
{
    function index(Application $app)
    {
        return new Response("index");
    }

    function edit(Application $app)
    {
        return new Response("edit");
    }
}

