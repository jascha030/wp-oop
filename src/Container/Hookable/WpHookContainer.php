<?php

declare(strict_types=1);

namespace Jascha030\WP\OOPOR\Container\Hookable;

use Jascha030\WP\OOPOR\Container\Psr11\Psr11Container;
use Jascha030\WP\OOPOR\Container\Psr11\WpPluginApiContainerInterface;
use Jascha030\WP\OOPOR\Exception\InvalidClassLiteralArgumentException;
use Jascha030\WP\OOPOR\Service\Hook\HookableServiceInterface;

use function add_action;
use function add_filter;

/**
 * Class WpHookContainer
 *
 * @package Jascha030\WP\OOPOR\Container\Hookable
 * @author Jascha van Aalst <contact@jaschavanaalst.nl>
 */
final class WpHookContainer extends Psr11Container implements WpPluginApiContainerInterface
{
    private const KEYS = ['actions', 'filters'];

    /**
     * Registers a service that provides methods for Wordpress hooks.
     * Wraps hookable methods in closures which share one instance of that is only constructed upon first hook call.
     *
     * @param string $serviceClass
     * @param \Jascha030\WP\OOPOR\Service\Hook\HookableServiceInterface|null $object to add if already constructed
     *
     * @throws \Jascha030\WP\OOPOR\Exception\InvalidClassLiteralArgumentException
     */
    public function registerHookableService(string $serviceClass, HookableServiceInterface $object = null): void
    {
        if (! is_subclass_of($serviceClass, HookableServiceInterface::class)) {
            throw new InvalidClassLiteralArgumentException('class', $serviceClass, HookableServiceInterface::class);
        }

        if (! $this->has($serviceClass)) {
            $this->set($serviceClass, ! $object ? fn ($container) => new $serviceClass($container) : fn () => $object);
        }

        $this->addAll($serviceClass);
    }

    private function addAll(string $serviceClass): void
    {
        foreach (self::KEYS as $key) {
            // Iterates hook types and checks service for hookable methods
            if (property_exists($serviceClass, $key)) {
                foreach ($serviceClass::${$key} as $tag => $parameters) {
                    // Checks if single or multiple class methods are added to hook
                    if (is_array($parameters) && is_array($parameters[0])) {
                        foreach ($parameters as $params) {
                            $this->sanitizeAndAdd($tag, $serviceClass, $params, $key);
                        }
                    } else {
                        $this->sanitizeAndAdd($tag, $serviceClass, $parameters, $key);
                    }
                }
            }
        }
    }

    private function addFilter(
        string $tag,
        string $service,
        string $method,
        int $priority,
        int $acceptedArguments,
        string $context
    ): void {
        $call = function (...$args) use ($service, $method) {
            $instance = $this->get($service);
            $instance->{$method}(...$args);
        };

        if ($context === 'actions') {
            add_action($tag, $call, $priority, $acceptedArguments);
        }

        if ($context === 'filters') {
            add_filter($tag, $call, $priority, $acceptedArguments);
        }
    }

    /**
     * @param string $service
     * @param string $tag
     * @param string|array $arguments
     * @param string|null $context
     */
    private function sanitizeAndAdd(string $service, string $tag, $arguments, string $context = null): void
    {
        $method            = is_array($arguments) ? $arguments[0] : $arguments;
        $priority          = is_array($arguments) ? $arguments[1] ?? 10 : 10;
        $acceptedArguments = is_array($arguments) ? $arguments[2] ?? 1 : 1;
        $context           = $context ?? 'filters';

        $this->addFilter($tag, $service, $method, $priority, $acceptedArguments, $context);
    }
}
