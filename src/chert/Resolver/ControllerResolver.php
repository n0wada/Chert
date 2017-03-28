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
        $this->app = $app;

        parent::__construct($logger);
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

        return $refClass->newInstanceArgs($this->getArgs($refClass));
    }

    /**
     * @param \ReflectionClass $refClass
     * @param array $args [optional]
     * @return array $args
     */
    private function getArgs(\ReflectionClass $refClass, array $args = [])
    {
        foreach ($refClass->getConstructor()->getParameters() as $parameter) {

            $class = $parameter->getClass();

            if (is_null($class) || $class->isInstance($this->app)) {
                $args[$parameter->getName()] = $this->app;
            } else {
                $args[$parameter->getName()] = $class->newInstance($this->app);
            }
        }

        return $args;
    }
}