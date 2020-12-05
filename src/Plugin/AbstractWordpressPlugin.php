<?php

namespace Jascha030\WP\OOPOR\Plugin;

use Jascha030\WP\OOPOR\Container\Hook\WpHookContainer;

abstract class AbstractWordpressPlugin
{
    protected string $file;

    private static AbstractWordpressPlugin $instance;

    /**
     * @var WpHookContainer
     */
    protected WpHookContainer $container;

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static;
        }
    }

    public function __construct(string $file, array $hookableClasses = [])
    {
        $this->file = $file;

        $this->container = new WpHookContainer();

        $this->bindClasses($hookableClasses);
    }

    protected function bindClasses(array $bindings): void
    {
        foreach ($bindings as $class) {
            $this->container->registerHookService($class);
        }
    }
}