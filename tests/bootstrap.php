<?php

date_default_timezone_set('Asia/Tokyo');

require dirname(__DIR__) . '/vendor/autoload.php';

require __DIR__ . '/BaseTestCase.php';
require __DIR__ . '/WebTestBase.php';

define('CACHE_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'cache');
define('CONTROLLER_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'Controller');
define('ANNOTATION_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');
define('CONTROLLER_NAMESPACE', 'Test\Controller');
define('ANNOTATION_NAMESPACE', 'Chert\Annotation');
