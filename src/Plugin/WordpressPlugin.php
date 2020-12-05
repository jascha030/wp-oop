<?php


namespace Jascha030\WP\OOPOR\Plugin;

use Jascha030\WP\OOPOR\Plugin\Notice\AdminNotice;

/**
 * Class WordpressPlugin
 * Default Plugin setup
 *
 * @package Jascha030\WP\OOPOR\Plugin
 */
class WordpressPlugin extends AbstractWordpressPlugin
{
    use ReadsPluginData,
        DisplaysAdminNotices;

    protected static string $requiredWordpress = '5.0';

    public function __construct(string $file, array $bindings = [])
    {
        $this->verifyPlugin();

        parent::__construct($file, $bindings);
    }

    private function verifyWpVersion(): bool
    {
        return get_bloginfo('version') >= (float)static::$requiredWordpress;
    }

    private function verifyPlugin(): void
    {
        if (! $this->verifyWpVersion()) {
            $plugin    = $this->getPluginData('Title');
            $wpVersion = $this->getPluginData('RequiresWP') ?? static::$requiredWordpress;

            $this->setNotice(
                "{$plugin} requires at Wordpress version: {$wpVersion}, Update your install.",
                AdminNotice::ERROR
            );

            add_action('admin_notices', [$this, 'displayNotices']);
        }
    }

    final public function getPluginFile(): string
    {
        return $this->file;
    }
}