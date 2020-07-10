<?php

namespace Jascha030\WP\OOPOR\Service\Hook;

use Jascha030\WP\OOPOR\Exception\InvalidClassLiteralArgumentException;
use Jascha030\WP\OOPOR\Service\Hook\Reference\HookedFilter;
use Psr\Container\ContainerInterface;

final class FilterManagerService
{
    public const ACTION = 'actions';
    public const FILTER = 'filters';

    public const HOOK_TYPES = [self::ACTION, self::FILTER];

    private ContainerInterface $container;

    private bool $keepReference = false;

    private array $filters = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Keep track of hooks and how many times they are called.
     */
    public function enableFilterStorage(): void
    {
        $this->keepReference = true;
    }

    public function disableFilterStorage(): void
    {
        if ($this->keepReference) {
            $this->keepReference = false;
            $this->filters       = [];
        }
    }


    /**
     * Registers a service that provides methods for Wordpress hooks.
     * Wraps hookable methods in closures which share one instance of that is only constructed upon first hook call.
     *
     * @param string $serviceClass
     * @param \Jascha030\WP\OOPOR\Service\Hook\HookServiceInterface|null $object to add if already constructed
     *
     * @throws \Jascha030\WP\OOPOR\Exception\InvalidClassLiteralArgumentException
     */
    public function registerHookService(string $serviceClass, HookServiceInterface $object = null): void
    {
        if (! is_subclass_of($serviceClass, HookServiceInterface::class)) {
            throw new InvalidClassLiteralArgumentException('class', $serviceClass, HookServiceInterface::class);
        }

        if (! $this->container->has($serviceClass)) {
            $this->container->set($serviceClass, ! $object ? fn () => new $serviceClass() : fn () => $object);
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
        $filterReference = new HookedFilter($tag, $priority, $acceptedArguments);
        $referenceId     = $filterReference->hook(
            function (...$args) use ($service, $method, $filterReference) {
                ($this->container->get($service))->{$method}(...$args);
                $filterReference->call();
            },
            $context
        );

        $this->filters[$service][$referenceId] = $filterReference;
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
