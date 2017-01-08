<?php

class RouteWebTest extends WebTestBase
{
    public function testIndex()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/test/index');

        $this->assertTrue($client->getResponse()->isOK());

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);

        $this->assertEquals("index", $data);
    }

    public function testMethod()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/test/post');

        $this->assertTrue($client->getResponse()->isOK());

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);

        $this->assertEquals("post", $data);
    }

    public function testMultiMethod()
    {
        $client = $this->createClient();
        $crawler = $client->request('DELETE', '/test/delete');

        $this->assertTrue($client->getResponse()->isOK());

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);

        $this->assertEquals("delete", $data);
    }

    public function testValue1()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/test/value/5');

        $this->assertTrue($client->getResponse()->isOK());

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(5, $data['value']);
    }

    public function testValue2()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/test/value');

        $this->assertTrue($client->getResponse()->isOK());

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(123, $data['value']);
    }

    public function testConvert1()
    {
        $client = $this->createClient();
        $this->app['converter.title'] = function () {
            return new UserConverter();
        };
        $crawler = $client->request('GET', '/test/convert/testConvert');

        $this->assertTrue($client->getResponse()->isOK());

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals("testConvert Success!!", $data);
    }

    public function testConvert2()
    {
        $client = $this->createClient();
        $this->app['converter.title'] = function () {
            return new UserConverter();
        };
        $crawler = $client->request('GET', '/test/convert2/testConvert');

        $this->assertTrue($client->getResponse()->isOK());

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals("testConvert Successful!", $data);
    }

    public function testAssert1()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/test/assert/20');

        $this->assertTrue($client->getResponse()->isOK());

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals("assertion ok!", $data);
    }

    public function testAssert2()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/test/assert/test');

        $this->assertTrue($client->getResponse()->isNotFound());

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals("404 The requested page could not be found.", $data);
    }

    public function testBefore()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/sub/test/before');

        $this->assertTrue($client->getResponse()->isOK());

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals("success", $data);
    }

    public function testBeforeAfter()
    {
        global $after;

        $client = $this->createClient();
        $crawler = $client->request('GET', '/sub/test/beforeAfter');

        $this->assertTrue($client->getResponse()->isOK());

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals("success", $data);
        $this->assertEquals("success", $after);
    }
}