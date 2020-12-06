<?php

namespace Jascha030\WP\OOP\Service\Filter\Reference;

final class FilterStorage
{
    private array $filters = [];

    public function __get(string $hook): array
    {
        return $this->filters[$hook];
    }

    public function __set(string $hook, HookedFilter $value): void
    {
        $this->store($hook, $value);
    }

    public function __isset(string $hook): bool
    {
        return array_key_exists($hook, $this->filters);
    }

    private function store(string $hook, HookedFilter $filter): void
    {
        if (! isset($this->filters[$hook]) || ! is_array($this->filters[$hook])) {
            $this->filters[$hook] = [];
        }
        $this->filters[$hook][$filter->getId()] = $filter;
    }
}
