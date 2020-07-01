<?php

namespace Jascha030\WP\OOPOR\Definition;

interface Definition
{
    public function getName(): string;

    public function getProvider(): string;

    public function getSubscription(): string;
}