<?php
namespace Test\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Chert\Annotation\Route;

/**
 * @Route(path="/nomethod")
 */
class NoMethodAnnotationController
{


    function index(Application $app)
    {
        return new Response("index");
    }

    function patch(Application $app)
    {
        return new Response("patch");
    }
}

