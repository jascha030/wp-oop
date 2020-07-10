<?php

namespace Jascha030\WP\OOPOR\Service\Hook\Reference;

final class FilterStorage implements \ArrayAccess
{
    private array $filters = [];

    private array $actions = [];

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->filters) || array_key_exists($offset, $this->actions);
    }

    public function offsetGet($offset)
    {
        return $this->filters[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->store($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        unset($this->filters[$offset]);
    }

    private function store(string $id, HookedFilter $filter): void
    {
        //        $filter->
    }
}
