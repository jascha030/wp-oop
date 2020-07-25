<?php

declare(strict_types=1);

namespace Jascha030\WP\OOPOR\Container\Psr11;

use Pimple\Container as PimpleContainer;
use Psr\Container\ContainerInterface;

/**
 * Class Psr11Container
 *
 * PSR-11 Compliant wrapper for Pimple Container.
 * The one pimple provides is final, this one can be extended.
 *
 * @package Jascha030\WP\OOPOR\Container\Psr11
 * @author Jascha van Aalst <contact@jaschavanaalst.nl>
 */
class Psr11Container implements ContainerInterface, ContainerObjectInterface
{
    protected PimpleContainer $pimple;

    public function __construct(PimpleContainer $pimple = null)
    {
        $this->pimple = $pimple ?? new PimpleContainer();
        $this->set(static::class, fn () => $this);
    }

    /**
     * {@inheritDoc}
     */
    final public function get($id)
    {
        return $this->pimple[$id];
    }

    /**
     * {@inheritDoc}
     */
    final public function has($id): bool
    {
        return isset($this->pimple[$id]);
    }

    /**
     * @param string $id
     * @param mixed $entry
     */
    final public function set(string $id, $entry): void
    {
        $this->pimple[$id] = $entry;
    }
}
