<?php

namespace Jascha030\WP\OOPOR\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private $services = [];

    private $hookables = [];

    private $subscription = [];

    public function get($id)
    {
    }

    public function has($id)
    {
    }
}