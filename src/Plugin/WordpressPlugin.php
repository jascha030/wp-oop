<?php


namespace Jascha030\WP\OOPOR\Plugin;

/**
 * Class WordpressPlugin
 * Default Plugin setup
 *
 * @package Jascha030\WP\OOPOR\Plugin
 */
class WordpressPlugin extends AbstractWordpressPlugin
{
    protected static $requiredWordpress = '5.0';

    public function __construct(string $file, array $bindings = [])
    {
        parent::__construct($file, $bindings);
    }

    private function verifyWpVersion(): bool
    {
        return get_bloginfo('version') >= (float)static::$requiredWordpress;
    }
}