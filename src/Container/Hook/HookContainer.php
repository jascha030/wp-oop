<?php

namespace Jascha030\WP\OOPOR\Container\Hook;

use Jascha030\WP\OOPOR\Container\Psr11\Container;
use Jascha030\WP\OOPOR\Service\Hook\HookableServiceInterface;
use Pimple\ServiceProviderInterface;

class HookContainer extends Container
{
    final public function addHookable(string $class): void
    {
//        if (! is_subclass_of($class, HookableServiceInterface::class))
    }
}