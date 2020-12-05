<?php

namespace Jascha030\WP\OOPOR\Container\Psr11;

use Jascha030\WP\OOPOR\Service\Filter\HookServiceInterface;
use Psr\Container\ContainerInterface;

/**
 * Interface WpPluginApiContainerInterface
 *
 * @package Jascha030\WP\OOPOR\Container\Psr11
 * @author Jascha van Aalst <contact@jaschavanaalst.nl>
 */
interface WpPluginApiContainerInterface extends ContainerInterface
{
    public function registerHookService(string $class, HookServiceInterface $object): void;
}
