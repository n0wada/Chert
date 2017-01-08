Chert
====

It is a simple routing library using annotations and caching.


## Usage

Resister Provider in your application.
```php
$app = new \Silex\Application();
 
$app->register(new \Chert\RouteCompileServiceProvider(),[
    'chert.cache_dir' => __DIR__ . '/cache,
    'chert.controller_dirs' => ['Controller' => __DIR__ . '/controllers]
]);
 
$app->run();
```

A controller example
```php
namespace Test\Controller;
 
use Chert\Annotation\Route;
use Chert\Annotation\Value;
use Symfony\Component\HttpFoundation\JsonResponse;
 
/**
 * @Route(path="/test")
 */
class TestController
{
    /**
     * @Route(path="/index/{id}",methods={"GET"}, name="test.index")
     * @Value(variable="id",default="1")
     */
    function index($id)
    {
        return new JsonResponse($id);
    }
}
```

## Parameters

### chert.cache_dir
The cache directory. This library save RouteCollection Object there.  
If you use FilesystemCache(default), This Parameter is required. 

### chert.cache_lifetime
The lifetime in number of seconds for this cache entry. default lifetime is 0.

### chert.controller_dirs
It is an array of pairs of namespace and dir.

### chert.cache
If you want to use ApcCache, MemCached, Redis etc., you can set Cache Object here.  
You need to implements Doctrine\Common\Cache\Cache Interface.

## Install

Chert uses Composer to install and update:

```
composer require nowada/Chert
```

## Licence

[MIT](https://github.com/n0wada/Chert/blob/master/LICENSE)
