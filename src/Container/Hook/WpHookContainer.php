<?php

declare(strict_types=1);

namespace Jascha030\WP\OOPOR\Container\Hook;

use Jascha030\WP\OOPOR\Container\Psr11\Psr11Container;
use Jascha030\WP\OOPOR\Container\Psr11\WpPluginApiContainerInterface;
use Jascha030\WP\OOPOR\Exception\InvalidClassLiteralArgumentException;
use Jascha030\WP\OOPOR\Service\Filter\Manager\FilterService;
use Jascha030\WP\OOPOR\Service\Filter\HookServiceInterface;

use function add_action;
use function add_filter;

/**
 * Class WpHookContainer
 *
 * @package Jascha030\WP\OOPOR\Container\Hook
 * @author Jascha van Aalst <contact@jaschavanaalst.nl>
 */
final class WpHookContainer extends Psr11Container implements WpPluginApiContainerInterface
{
    public function __construct(bool $storeFilters = false)
    {
        parent::__construct();

        $this->set(FilterService::class, fn ($container) => new FilterService($container[static::class]));
    }

    /**
     * Registers a service that provides methods for Wordpress hooks.
     * Wraps hookable methods in closures which share one instance of that is only constructed upon first hook call.
     *
     * @param  string  $serviceClass
     * @param  HookServiceInterface|null  $object  to add if already constructed
     * @throws InvalidClassLiteralArgumentException
     */
    public function registerHookService(string $serviceClass, HookServiceInterface $object = null): void
    {
        $this->get(FilterService::class)->registerHookService($serviceClass, $object);
    }
}
