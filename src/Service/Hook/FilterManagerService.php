<?php

namespace Jascha030\WP\OOPOR\Service\Hook;

use Jascha030\WP\OOPOR\Container\Psr11\ContainerObjectInterface;
use Jascha030\WP\OOPOR\Exception\InvalidClassLiteralArgumentException;
use Jascha030\WP\OOPOR\Service\Hook\Reference\FilterStorage;
use Jascha030\WP\OOPOR\Service\Hook\Reference\HookedFilter;

final class FilterManagerService
{
    public const ACTION = 2;
    public const FILTER = 1;

    public const HOOK_TYPES = [self::ACTION => 'actions', self::FILTER => 'filters'];

    private ContainerObjectInterface $container;

    private bool $keepReference = false;

    private FilterStorage $filters;

    public function __construct(ContainerObjectInterface $container)
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
            $this->filters       = new FilterStorage();
        }
    }


    /**
     * Registers a service that provides methods for Wordpress hooks.
     * Wraps hookable methods in closures which share one instance of that is only constructed upon first hook call.
     *
     * @param string $serviceClass
     * @param  HookServiceInterface|null $object to add if already constructed
     *
     * @throws InvalidClassLiteralArgumentException
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
        int $context
    ): void {
        $closure = function (...$args) use ($service, $method) {
            ($this->container->get($service))->{$method}(...$args);
        };

        if ($context === self::ACTION) {
            add_action($tag, $closure, $priority, $acceptedArguments);
        }

        if ($context === self::FILTER) {
            add_filter($tag, $closure, $priority, $acceptedArguments);
        }
    }

    private function addFilterAndReference(
        string $tag,
        string $service,
        string $method,
        int $priority,
        int $acceptedArguments,
        int $context
    ): void {
        $filterReference = new HookedFilter($tag, $priority, $acceptedArguments);
        $filterReference->hook(
            function (...$args) use ($service, $method, $filterReference) {
                ($this->container->get($service))->{$method}(...$args);
                $filterReference->call();
            },
            $context
        );

        $this->filters->$tag = $filterReference;
    }

    /**
     * @param string $service
     * @param string $tag
     * @param string|array $arguments
     * @param int|null $context
     */
    private function sanitizeAndAdd(string $service, string $tag, $arguments, int $context = null): void
    {
        // Check if hook has single or multiple methods.
        $method            = is_array($arguments) ? $arguments[0] : $arguments;
        $priority          = is_array($arguments) ? $arguments[1] ?? 10 : 10;
        $acceptedArguments = is_array($arguments) ? $arguments[2] ?? 1 : 1;

        // Filter or action.
        $context = $context ?? 1;

        if ($this->keepReference) {
            $this->addFilterAndReference($tag, $service, $method, $priority, $acceptedArguments, $context);
        } else {
            $this->addFilter($tag, $service, $method, $priority, $acceptedArguments, $context);
        }
    }

    private function addAll(string $serviceClass): void
    {
        foreach (self::HOOK_TYPES as $key => $val) {
            // Iterates hook types and checks service for hookable methods.
            if (property_exists($serviceClass, $val)) {
                foreach ($serviceClass::${$val} as $tag => $parameters) {
                    // Checks if single or multiple class methods are added to hook.
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
