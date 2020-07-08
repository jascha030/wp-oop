<?php

namespace Jascha030\WP\OOPOR\Service\Hook;

use Jascha030\WP\OOPOR\Container\Psr11\WpPluginApiContainerInteface;

/**
 * Interface HookableServiceInterface
 *
 * @package Jascha030\WP\OOPOR\Service\Hook
 * @author Jascha van Aalst <contact@jaschavanaalst.nl>
 */
interface HookableServiceInterface
{
    public function __construct(WpPluginApiContainerInteface $container);
}
