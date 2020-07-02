<?php

namespace Jascha030\WP\OOPOR\Container\Psr11;

use Psr\Container\ContainerInterface;

class Container extends \Pimple\Container implements ContainerInterface
{
    final public function get($id)
    {
        return $this->offsetGet($id);
    }

    public function set(string $id, $value): void
    {
        $this->offsetSet($id, $value);
    }

    final public function has($id): bool
    {
        return $this->offsetExists($id);
    }
}