<?php

namespace Jascha030\WP\OOP\Plugin;

use Jascha030\WP\OOP\Plugin\Notice\AdminNotice;

/**
 * Class WordpressPlugin
 * Default Plugin setup
 *
 * @package Jascha030\WP\OOP\Plugin
 */
class WordpressPlugin extends AbstractWordpressPlugin
{
    use ReadsPluginData;
    use DisplaysAdminNotices;

    protected static string $requiredWordpress = '5.0';

    public function __construct(string $file, array $bindings = [])
    {


        parent::__construct($file, $bindings);
    }
}
