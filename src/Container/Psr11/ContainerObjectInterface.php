<?php

/** @noinspection MissingParameterTypeDeclarationInspection */

namespace Jascha030\WP\OOP\Container\Psr11;

use Psr\Container\ContainerInterface;

/**
 * Interface ContainerObjectInterface
 *
 * @package Jascha030\WP\OOP\Container\Psr11
 * @author Jascha030 <contact@jaschavanaalst.nl>
 */
interface ContainerObjectInterface extends ContainerInterface
{
    public function set(string $id, $entry): void;
}
