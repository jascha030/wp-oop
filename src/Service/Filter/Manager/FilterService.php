<?php

namespace Jascha030\WP\OOP\Service\Filter\Manager;

use Closure;
use Jascha030\WP\OOP\Container\Psr11\ContainerObjectInterface;
use Jascha030\WP\OOP\Exception\InvalidClassLiteralArgumentException;
use Jascha030\WP\OOP\Service\Filter\HookableInterface;
use Jascha030\WP\OOP\Service\Filter\Reference\FilterStorage;
use Jascha030\WP\OOP\Service\Filter\Reference\HookedFilter;

class FilterService
{
    public const ACTION = 2;
    public const FILTER     = 1;
    public const HOOK_TYPES = [
        self::ACTION => 'actions',
        self::FILTER => 'filters'
    ];

    private ContainerObjectInterface $container;

    // todo: filter storage to trait or child
    private FilterStorage $filters;

    public function __construct(ContainerObjectInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Registers a service that provides methods for Wordpress hooks.
     * Wraps hookable methods in closures which share one instance of that is only constructed upon first hook call.
     *
     * @param  string  $serviceClass
     * @param  HookableInterface|null  $object  to add if already constructed
     *
     * @throws InvalidClassLiteralArgumentException
     */
    final public function registerHookService(string $serviceClass, HookableInterface $object = null): void
    {
        if (! is_subclass_of($serviceClass, HookableInterface::class)) {
            throw new InvalidClassLiteralArgumentException('class', $serviceClass, HookableInterface::class);
        }

        if (! $this->container->has($serviceClass)) {
            $this->container->set(
                $serviceClass,
                ! $object ? static fn() => new $serviceClass() : static fn() => $object
            );
        }

        $this->addAll($serviceClass);
    }

    private function add(string $tag, Closure $closure, int $prio, int $arguments, int $context): void
    {
        if ($context === self::ACTION) {
            add_action($tag, $closure, $prio, $arguments);
        }

        if ($context === self::FILTER) {
            add_filter($tag, $closure, $prio, $arguments);
        }
    }

    /**
     * Wraps class and method in a Closure
     *
     * @param $service
     * @param $method
     * @return Closure
     */
    private function wrapClosure(string $service, string $method): Closure
    {
        return function (...$args) use ($service, $method) {
            return $this->container->get($service)->{$method}(...$args);
        };
    }

    /**
     * Todo: Add to Trait or extend class
     *
     * @param  string  $tag
     * @param  string  $service
     * @param  string  $method
     * @param  int  $priority
     * @param  int  $acceptedArguments
     * @param  int  $context
     */
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
                $do = $this->container->get($service)->{$method}(...$args);
                $filterReference->call();
                return $do;
            },
            $context
        );

        $this->filters->$tag = $filterReference;
    }

    /**
     * @param  string  $service
     * @param  string  $tag
     * @param  string|array  $arguments
     * @param  int|null  $context
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    private function sanitizeAndAdd(string $service, string $tag, $arguments, int $context = null): void
    {
        // Check if hook has single or multiple methods.
        $method            = is_array($arguments) ? $arguments[0] : $arguments;
        $priority          = is_array($arguments) ? $arguments[1] ?? 10 : 10;
        $acceptedArguments = is_array($arguments) ? $arguments[2] ?? 1 : 1;

        // Filter or action.
        $context = $context ?? 1;

//        if ($this->keepReference) {
//            $this->addFilterAndReference($tag, $service, $method, $priority, $acceptedArguments, $context);
//        } else {
            $this->add(
                $tag,
                $this->wrapClosure($service, $method),
                $priority,
                $acceptedArguments,
                $context
            );
//        }
    }

    /**
     * @param  string  $serviceClass
     */
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
