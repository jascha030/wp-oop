<?php

namespace Jascha030\WP\OOPOR\Container\Psr11;

use Psr\Container\ContainerInterface;

/**
 * Interface ContainerObjectInterface
 *
 * @package Jascha030\WP\OOPOR\Container\Psr11
 * @author Jascha van Aalst <contact@jaschavanaalst.nl>
 */
interface ContainerObjectInterface extends ContainerInterface
{
    public function set(string $id, $entry): void;
}
