<?php

namespace Jascha030\WP\OOP\Plugin;

/**
 * Trait ReadsPluginData
 * Methods for reading data from the header in the main plugin file.
 *
 * @package Jascha030\WP\OOP\Plugin
 */
trait ReadsPluginData
{
    private array $pluginData = [];

    final protected function fetchPluginData(): void
    {
        $this->pluginData = get_plugin_data($this->getPluginFile(), false);
    }

    /**
     * Get data from the Plugin header by key
     *
     * @param  string  $key
     * @return null|string
     */
    final public function getPluginData(string $key): ?string
    {
        if (! isset($this->pluginData[$key])) {
            $this->fetchPluginData();
        }

        return $this->pluginData[$key] ?? null;
    }

    abstract public function getPluginFile(): string;
}
