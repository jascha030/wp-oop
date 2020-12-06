<?php

namespace Jascha030\WP\OOP\Plugin;

use Jascha030\WP\OOP\Container\Hook\WpHookContainer;

abstract class AbstractWordpressPlugin
{
    protected string $file;

    private static AbstractWordpressPlugin $instance;

    /**
     * @var WpHookContainer
     */
    protected WpHookContainer $container;

    public static function getInstance(): WpHookContainer
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
            $this->container->registerHookable($class);
        }
    }
}