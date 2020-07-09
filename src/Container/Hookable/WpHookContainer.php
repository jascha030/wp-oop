<?php

declare(strict_types=1);

namespace Jascha030\WP\OOPOR\Container\Hookable;

use Jascha030\WP\OOPOR\Container\Psr11\Psr11Container;
use Jascha030\WP\OOPOR\Container\Psr11\WpPluginApiContainerInterface;
use Jascha030\WP\OOPOR\Service\Hook\HookableServiceInterface;
use Jascha030\WP\OOPOR\Service\Hook\Reference\FilterManager;

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
    public function __construct()
    {
        parent::__construct();

        $this->set(FilterManager::class, fn ($container) => new FilterManager($container));
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
        $this->get(FilterManager::class)->registerHookableService($serviceClass, $object);
    }
}
