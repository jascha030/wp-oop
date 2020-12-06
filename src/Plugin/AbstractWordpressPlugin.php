<?php

namespace Jascha030\WP\OOP\Plugin;

use Jascha030\WP\OOP\Container\Hook\WpHookContainer;
use Jascha030\WP\OOP\Exception\InvalidClassLiteralArgumentException;
use Jascha030\WP\OOP\Plugin\Notice\AdminNotice;

abstract class AbstractWordpressPlugin
{
    use DisplaysAdminNotices;
    use ReadsPluginData;

    protected static AbstractWordpressPlugin $instance;

    protected static string $requiredWordpress = '5.0';

    protected string $file;

    /**
     * @var WpHookContainer
     */
    protected WpHookContainer $container;

    public static function getInstance(): AbstractWordpressPlugin
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __construct(string $file = null, array $hookableClasses = [])
    {
        $this->verifyPlugin();

        $this->container = new WpHookContainer();

        $this->bindClasses($hookableClasses);

        self::$instance = $this;
    }

    /**
     * @param  string  $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->container->get($key);
    }

    protected function bindClasses(array $bindings): void
    {
        foreach ($bindings as $class) {
            try {
                $this->container->registerHookable($class);
            } catch (InvalidClassLiteralArgumentException $e) {
                $this->addNotice(AdminNotice::fromException($e));
            }
        }
    }

    public function getPluginFile(): string
    {
        return $this->file;
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

            $this->createAndAddNotice(
                "{$plugin} requires at Wordpress version: {$wpVersion}, Update your install.",
                AdminNotice::ERROR
            );

            add_action('admin_notices', [$this, 'displayNotices']);
        }
    }
}
