<?php

namespace Jascha030\WP\OOPOR\Service\Hook\Reference;

use Jascha030\WP\OOPOR\Container\Psr11\WpPluginApiContainerInterface;
use Jascha030\WP\OOPOR\Exception\InvalidClassLiteralArgumentException;
use Jascha030\WP\OOPOR\Service\Hook\HookableServiceInterface;

final class FilterManagerService
{
    public const ACTION = 'actions';
    public const FILTER = 'filters';

    public const HOOK_TYPES = [self::ACTION, self::FILTER];

    private WpPluginApiContainerInterface $container;

    private bool $keepReference;

    private array $filters = [];

    public function __construct(WpPluginApiContainerInterface $container)
    {
        $this->container = $container;
    }

    public function trackFilters(): void
    {
        $this->keepReference = true;
    }

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

    private function addFilter(
        string $tag,
        string $service,
        string $method,
        int $priority,
        int $acceptedArguments,
        string $context
    ): void {
        $closure = function (...$args) use ($service, $method) {
            ($this->container->get($service))->{$method}(...$args);
        };

        if ($context === 'actions') {
            add_action($tag, $closure, $priority, $acceptedArguments);
        }

        if ($context === 'filters') {
            add_filter($tag, $closure, $priority, $acceptedArguments);
        }
    }

    private function addFilterAndReference(
        string $tag,
        string $service,
        string $method,
        int $priority,
        int $acceptedArguments,
        string $context
    ): void {
        $filterReference = new HookedFilterReference($tag, $priority, $acceptedArguments);
        $referenceId     = $filterReference->hook(
            function (...$args) use ($service, $method, $filterReference) {
                ($this->container->get($service))->{$method}(...$args);
                $filterReference->call();
            },
            $context
        );

        $this->filters[$referenceId] = $filterReference;
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
        $context           = $context ?? self::HOOK_TYPES[self::FILTER];

        if ($this->keepReference) {
            $this->addFilterAndReference($tag, $service, $method, $priority, $acceptedArguments, $context);
        } else {
            $this->addFilter($tag, $service, $method, $priority, $acceptedArguments, $context);
        }
    }

    private function addAll(string $serviceClass): void
    {
        foreach (self::HOOK_TYPES as $key) {
            // Iterates hook types and checks service for hookable methods
            if (property_exists($serviceClass, $key)) {
                foreach ($serviceClass::${$key} as $tag => $parameters) {
                    // Checks if single or multiple class methods are added to hook
                    if (is_array($parameters) && is_array($parameters[0])) {
                        foreach ($parameters as $params) {
                            $this->sanitizeAndAdd($serviceClass, $tag, $params, $key);
                        }
                    } else {
                        $this->sanitizeAndAdd($serviceClass, $tag, $parameters, $key);
                    }
                }
            }
        }
    }
}
