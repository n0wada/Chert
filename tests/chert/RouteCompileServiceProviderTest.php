<?php

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Annotations\Reader;
use Chert\Compiler\Compiler;

class RouteCompileServiceProviderTest extends BaseTestCase
{
    public function setUp()
    {
        $this->getApp();
    }

    public function testRegister()
    {
        $this->provider->register($this->app);

        $this->assertEquals("", $this->app['chert.cache_dir']);
        $this->assertEquals(0, $this->app['chert.cache_lifetime']);
        $this->assertEquals([], $this->app['chert.controller_dirs']);
        $this->assertEquals([ANNOTATION_NAMESPACE => ANNOTATION_DIR], $this->app['chert.annotation_dirs']);

        $this->assertTrue(isset($this->app['chert.cache']));
        $this->assertTrue(isset($this->app['chert.compiler']));

        $this->app['chert.cache_dir'] = CACHE_DIR;

        $this->assertInstanceOf(Cache::class, $this->app['chert.cache']);
        $this->assertInstanceOf(Reader::class, $this->app['chert.reader']);
        $this->assertInstanceOf(Compiler::class, $this->app['chert.compiler']);
    }

    public function testBoot()
    {
        $this->provider->register($this->app);

        $this->app['chert.controller_dirs'] = [CONTROLLER_NAMESPACE => CONTROLLER_DIR];
        $this->app['chert.annotation_dirs'] = ['Chert\Annotation' => dirname(__DIR__)];
        $this->app['chert.cache_dir'] = CACHE_DIR;

        $this->app['chert.compiler'] = $this->createMock(Compiler::class);

        $this->provider->boot($this->app);
    }

    public function testBootNotConfigured()
    {
        $this->provider->register($this->app);

        try {
            $reciever = $this->app['chert.cache'];
            $this->fail('PHPUnit fail');
        } catch (Exception $e) {
            $this->assertSame('The directory "" does not exist and could not be created.', $e->getMessage());
        }

        $mock = $this->createMock(Compiler::class);
        $mock->expects($this->never())->method($this->anything());

        $this->app['chert.compiler'] = $mock;

        $this->provider->boot($this->app);

        $this->app['chert.controller_dirs'] = ['test' => 'testdir'];

        $mock = $this->createMock(Compiler::class);
        $mock->expects($this->once())->method('chart');

        $this->app['chert.compiler'] = $mock;

        $this->provider->boot($this->app);
    }
}