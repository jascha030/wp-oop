<?php

namespace Jascha030\WP\OOPOR\Subscription\Filter;

use Jascha030\WP\OOPOR\Subscription\Subscription;

class FilterSubscription implements Subscription
{
    protected const ACTION_KEY = 'action';
    protected const FILTER_KEY = 'filter';

    private const TYPE = self::FILTER_KEY;

    private string $id;

    private string $tag;

    private string $ref;

    private string $priority;

    private string $acceptedArguments;

    //    private int $status = ;

    public function __construct(string $tag, object $ref, int $priority = 10, int $acceptedArguments = 1)
    {
        $this->tag               = $tag;
        $this->ref               = $ref;
        $this->priority          = $priority;
        $this->acceptedArguments = $acceptedArguments;
    }

    public function subscribe(): void
    {
        $this->hook();
    }

    public function unsubscribe(): void
    {
    }

    private function hook(): void
    {
    }
}