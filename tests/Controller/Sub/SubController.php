<?php
namespace Test\Controller\Sub;

use Silex\Application;
use Chert\Annotation\Route;
use Chert\Annotation\After;
use Chert\Annotation\Before;
use Chert\Annotation\Assert;
use Chert\Annotation\Convert;
use Chert\Annotation\Value;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(path="/sub/test")
 * @Before("beforeProc")
 */
class SubController
{
    /**
     * @Route(path="/before",methods={"GET"}, name="test.before")
     */
    function before(Application $app)
    {
        global $before;

        if ($before == "request accept") {
            return new JsonResponse("success");
        }
        return new JsonResponse("failed");
    }

    static function beforeProc(Request $request)
    {
        global $before;

        if ($request->getRequestUri() == "/sub/test/before") {
            $before = "request accept";
        }
    }

    /**
     * @Route(path="/beforeAfter",methods={"GET"}, name="test.beforeAfter")
     * @After("afterProc")
     */
    function beforeAfter(Application $app)
    {
        global $before;
        global $after;

        $after = "failed";

        if ($before == "request accept") {
            return new JsonResponse("success");
        }
        return new JsonResponse("failed");
    }

    static function afterProc(Request $request)
    {
        global $after;

        $after = "success";
    }
}

