<?php
namespace Test\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Chert\Annotation\Route;

class NoClassAnnotationController
{
    /**
     * @Route(path="/noclass/index")
     */
    function index(Application $app)
    {
        return new Response("index");
    }

    /**
     * @Route(path="/noclass/post")
     */
    function post(Application $app)
    {
        return new Response("post");
    }
}

