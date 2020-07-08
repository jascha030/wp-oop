<?php

namespace Jascha030\WP\OOPOR\Container\Psr11;

use Jascha030\WP\OOPOR\Service\Hook\HookableServiceInterface;

/**
 * Interface WpPluginApiContainerInterface
 *
 * @package Jascha030\WP\OOPOR\Container\Psr11
 * @author Jascha van Aalst <contact@jaschavanaalst.nl>
 */
interface WpPluginApiContainerInterface
{
    public function registerHookableService(string $class, HookableServiceInterface $object): void;
}
