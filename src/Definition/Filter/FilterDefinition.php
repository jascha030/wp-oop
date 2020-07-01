<?php

namespace Jascha030\WP\OOPOR\Definition\Filter;

use Jascha030\WP\OOPOR\Definition\PredefinedDefinition;
use Jascha030\WP\OOPOR\Provider\FilterProvider;
use Jascha030\WP\OOPOR\Subscription\Filter\FilterSubscription;

class FilterDefinition extends PredefinedDefinition
{
    protected const NAME = 'filter';

    protected const PROVIDER = FilterProvider::class;

    protected const SUBSCRIPTION = FilterSubscription::class;
}