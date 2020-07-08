<?php

declare(strict_types = 1);

namespace Jascha030\WP\OOPOR\Container\Hookable;

use Jascha030\WP\OOPOR\Container\Psr11\Psr11Container;
use Jascha030\WP\OOPOR\Container\Psr11\WpPluginApiContainerInteface;
use Jascha030\WP\OOPOR\Exception\InvalidClassLiteralArgumentException;
use Jascha030\WP\OOPOR\Service\Hook\HookableServiceInterface;

/**
 * Class WpHookContainer
 *
 * @package Jascha030\WP\OOPOR\Container\Hookable
 * @author Jascha van Aalst <contact@jaschavanaalst.nl>
 */
class WpHookContainer extends Psr11Container implements WpPluginApiContainerInteface
{
    private const KEYS = ['actions', 'filters'];

    /**
     * Registers a service that provides methods for Wordpress hooks.
     *
     * @param string $serviceClass
     * @param \Jascha030\WP\OOPOR\Service\Hook\HookableServiceInterface|null $object to add if already constructed
     *
     * @throws \Jascha030\WP\OOPOR\Exception\InvalidClassLiteralArgumentException
     */
    final public function registerHookableService(string $serviceClass, HookableServiceInterface $object = null): void
    {
        if (! is_subclass_of($serviceClass, HookableServiceInterface::class)) {
            throw new InvalidClassLiteralArgumentException('class', $serviceClass, HookableServiceInterface::class);
        }

        if (! $this->has($serviceClass)) {
            $this->set($serviceClass, ! $object ? fn($container) => new $serviceClass($container) : fn() => $object);
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
        if ($context === 'actions') {
            \add_action(
                $tag,
                function () use ($service, $method) {
                    $instance = $this->get($service);
                    $instance->{$method}();
                },
                $priority,
                $acceptedArguments
            );
        }

        if ($context === 'filters') {
            \add_filter(
                $tag,
                function () use ($service, $method) {
                    $instance = $this->get($service);
                    $instance->{$method}();
                },
                $priority,
                $acceptedArguments
            );
        }
    }

    /**
     * @param string $service
     * @param string $tag
     * @param string|array $parameters
     * @param string|null $context
     */
    private function sanitizeAndAdd(string $service, string $tag, $parameters, string $context = null): void
    {
        $method            = is_array($parameters) ? $parameters[0] : $parameters;
        $priority          = (is_array($parameters)) ? $parameters[1] ?? 10 : 10;
        $acceptedArguments = (is_array($parameters)) ? $parameters[2] ?? 1 : 1;
        $context           = $context ?? 'filters';

        $this->addFilter($tag, $service, $method, $priority, $acceptedArguments, $context);
    }
}
