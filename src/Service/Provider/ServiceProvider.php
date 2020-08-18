<?php

namespace Jascha030\WP\OOPOR\Service\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    private array $services = [];

    public function register(Container $pimple): void
    {
        $pimple->register($this, $this->services);
    }
}
