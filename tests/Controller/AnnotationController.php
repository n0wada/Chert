<?php
namespace Test\Controller;

use Silex\Application;
use Chert\Annotation\Route;
use Chert\Annotation\Assert;
use Chert\Annotation\Convert;
use Chert\Annotation\Value;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route(path="/test")
 */
class AnnotationController
{
    /**
     * @Route(path="/index",methods={"GET"}, name="test.index")
     */
    function index(Application $app)
    {
        return new JsonResponse("index");
    }

    /**
     * @Route(path="/post",methods={"POST"}, name="test.post")
     */
    function post(Application $app)
    {
        return new JsonResponse("post");
    }

    /**
     * @Route(path="/delete",methods={"GET","DELETE","POST"}, name="test.delete")
     */
    function delete(Application $app)
    {
        return new JsonResponse("delete");
    }

    /**
     * @Route(path="/value/{pageName}",methods={"GET"}, name="test.value")
     * @Value(variable="pageName",default=123)
     */
    function value(Application $app, $pageName)
    {
        return new JsonResponse(["value" => $pageName]);
    }

    /**
     * @Route(path="/convert/{title}",methods={"GET"}, name="test.convert")
     * @Convert(variable="title",callback="converter.title:convert")
     */
    function convert(Application $app, $title)
    {
        return new JsonResponse($title);
    }

    /**
     * @Route(path="/convert2/{title}",methods={"GET"}, name="test.convert2")
     * @Convert(variable="title",callback="convertProc")
     */
    function convert2(Application $app, $title)
    {
        return new JsonResponse($title);
    }

    static function convertProc($title)
    {
        return $title . " Successful!";
    }

    /**
     * @Route(path="/assert/{id}",methods={"GET"}, name="test.assert")
     * @Assert(variable="id",regexp="\d+")
     */
    function assert(Application $app, $id)
    {
        return new JsonResponse("assertion ok!");
    }
}

