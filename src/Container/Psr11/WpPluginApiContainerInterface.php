<?php

namespace Jascha030\WP\OOP\Container\Psr11;

use Jascha030\WP\OOP\Service\Filter\HookableInterface;
use Psr\Container\ContainerInterface;

/**
 * Interface WpPluginApiContainerInterface
 *
 * @package Jascha030\WP\OOP\Container\Psr11
 * @author Jascha030 <contact@jaschavanaalst.nl>
 */
interface WpPluginApiContainerInterface extends ContainerInterface
{
    public function registerHookable(string $class, HookableInterface $object): void;
}
