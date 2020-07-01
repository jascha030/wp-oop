<?php

namespace Jascha030\WP\OOPOR\Subscription;

interface Subscription
{
    public function subscribe(): void;

    public function unsubscribe(): void;
}