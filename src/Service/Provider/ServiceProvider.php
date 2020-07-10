<?php

namespace Jascha030\WP\OOPOR\Service\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        // TODO: Implement register() method.
    }

    private function compile(Container $pimple, string $serviceName, $service = null): void
    {
        if (! $service) {
            //            $pimple[$serviceName] = function ()
        }

        if ($service instanceof \Closure || is_callable($service)) {
            $pimple[$serviceName] = $service;
        }
    }
}
