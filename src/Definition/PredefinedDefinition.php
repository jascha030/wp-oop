<?php

namespace Jascha030\WP\OOPOR\Definition;

class PredefinedDefinition implements Definition
{
    protected const NAME = '';

    protected const PROVIDER = '';

    protected const SUBSCRIPTION = '';

    final public function getName(): string
    {
        return static::NAME;
    }

    final public function getProvider(): string
    {
        return static::PROVIDER;
    }

    final public function getSubscription(): string
    {
        return static::SUBSCRIPTION;
    }
}