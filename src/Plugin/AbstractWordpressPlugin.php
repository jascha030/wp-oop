<?php

namespace Jascha030\WP\OOPOR\Plugin;

use Jascha030\WP\OOPOR\Container\Hook\WpHookContainer;
use Jascha030\WP\OOPOR\Exception\InvalidClassLiteralArgumentException;

abstract class AbstractWordpressPlugin
{
    protected string $file;

    private array $pluginData = [];

    private static AbstractWordpressPlugin $instance;

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static;
        }
    }

    public function __construct(string $file, array $bindings = [])
    {
        $this->file = $file;

        $this->container = new WpHookContainer();

        foreach ($bindings as $class) {
            try {
                $this->container->registerHookService($class);
            } catch (InvalidClassLiteralArgumentException $e) {
                // print notices
            }
        }
    }
}