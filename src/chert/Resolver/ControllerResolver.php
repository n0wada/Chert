<?php
/**
 * This file is part of the Chert package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) 2017, Naoto Owada <naoto0wada@gmail.com>
 */

namespace Chert\Resolver;

use Silex\Application;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;

/**
 * ControllerResolver
 */
class ControllerResolver extends BaseControllerResolver
{
    protected $app;

    /**
     * @param Application $app A LoggerInterface instance*
     * @param LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct(Application $app, LoggerInterface $logger = null)
    {
        parent::__construct($logger);
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateController($class)
    {
        $refClass = new \ReflectionClass($class);

        if (!$refClass->hasMethod('__construct')) {
            return $refClass->newInstance();
        }

        try {

            foreach ($params = $refClass->getConstructor()->getParameters() as $key => $param) {

                if (is_null($type = $param->getClass())) {
                    throw new \InvalidArgumentException(sprintf('%s uses type declarations for injection.', self::class));
                }

                $params[$key] = $type->isInstance($this->app) ? $this->app : $type->newInstance($this->app);
            }

        } catch (\TypeError $e) {
            throw new \InvalidArgumentException(sprintf('%s could not be instantiated. %s only injects Silex applications. If you need flexibility, please use other libraries.', $class, self::class));
        }

        return $refClass->newInstanceArgs($params);
    }
}