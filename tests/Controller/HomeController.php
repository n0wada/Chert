<?php
namespace Test\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Chert\Annotation\Route;
use Chert\Annotation\Before;
use Chert\Annotation\After;

/**
 * @Route(path="/home")
 * @After("afterhome")
 */
class HomeController
{

    /**
     * @Route(path="/index",methods={"GET"}, name="home.index")
     * @Before("beforeIndex")
     */
    function index(Application $app)
    {
        return new JsonResponse("index");
    }

    static function beforeIndex()
    {
        echo 'beforeIndex';
    }

    static function afterhome()
    {
        echo 'afterhome';
    }

    /**
     * @Route(path="/edit",methods={"GET"}, name="home.edit")
     */
    function edit(Application $app)
    {
        return new JsonResponse("index");
    }
}

