# WP-OOP

Backbone for interfacing with the Wordpress Plugin Common API (Hooks). Keeping track of actions and filters and making it easy to hook or unhook class methods.

This package includes a simple Service container (Pimple) wrapper that hooks class methods in a way that lazy-loads the actual methods. Providing the class construction whenever the class is first called by a WP filter/action. 

## Geting started

### Requirements

- Wordpress
- composer
- php >= 7.4

> Keep in mind, because I built this package to supply a more modern way to develop wordpress plugins. 
> Therefore this package does not follow the Wordpress Coding Standards and (Like unlike Wordpress) it is not backwards compatible until php 5.3 or any older versions of PHP than 7.4 for that matter.
>
> Backwards compatibility until at least PHP 7.1 is on the Todo list. 

### Installation

composer require jascha030/wp-subscriptions



## Usage

Most logic will consist of Classes with methods that need to be hooked by one or multiple `WpHookContainer` instance.

### Container instance

The code below is an example of how a main plugin file utilises this package. And initialises a main `WpHookContainer` instance.

```php
<?php

/**
 * Plugin Name: WP OOP Test Plugin
 * Version: 1.0.0
 * Description: A test for the wp-oop package
 */

namespace Jascha030\Test;

use Jascha030\WP\OOPOR\Container\Hook\WpHookContainer;
use Jascha030\WP\OOPOR\Exception\InvalidClassLiteralArgumentException;

/**
 * Require composer PSR-4 autoloader
 */
include __DIR__ . '/vendor/autoload.php';

/**
 * Set plugin path
 */
if (! defined('WPS_TEST_PLUGIN_DIR')) {
    define('WPS_TEST_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

/**
 * Adds a global with pre defined Hook service classes.
 */
if (! defined('WP_OOP_HOOK_SERVICES')) {
    define('WP_OOP_HOOK_SERVICES', [
        WpTest::class
    ]);
}

/**
 * This method calls the main hook container.
 * This is wrapped in a method to prevent the unnecessary use of the Singleton pattern.
 * This function  allows for other containers to be added if necessary.
 *
 * @return WpHookContainer
 */
function getContainer()
{
    static $container;

    if (null === $container) {
        $container = new WpHookContainer();
    }

    return $container;
}

/**
 * Register all hooked method for each class.
 */
function testContainer()
{
    foreach (WP_OOP_HOOK_SERVICES as $hookableClass) {
        try {
            // Hook class to the container which will construct the class upon first call by a wordpress hook.
            (getContainer())->registerHookService($hookableClass);
        } catch (InvalidClassLiteralArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}


testContainer();
```

## Hookable service class

To assure that the right classes will be hooked `HookServiceInterface` is added to a class' implementation.
This implementation requires no methods and is only used by the Hook container to assert a class' validity.

> Hooked methods should always be public just like regular wordpress.

These classes use a static property that tells the hook container to hook specific methods to wordpress.

Actions:

```php
public static array $actions = []; // ActionProvider interface
```

Filters:

```php
public static array $filters = []; // FilterProvider interface
```

Below is an example of one of these classes.

```php
<?php

namespace Jascha030\Test;

use Jascha030\WP\OOPOR\Service\Filter\HookServiceInterface;

class WpTest implements HookServiceInterface
{
    public static array $actions = [
        'index_test_area'  => 'testArea',  // most basic implementation: Hook => method
        'test_custom_subs' => [            // Example of multiple methods hooked to one action hook
            ['testMethod', 1, 2],          // 1, 2 refer to priority and expected arguments
            ['secondMethod', 10, 2]
        ]
    ];

    public static array $filters = [];

    private string $pluginDir;

    private string $testOutput;

    public function __construct()
    {
        if (WPS_TEST_PLUGIN_DIR) {
            $this->pluginDir = WPS_TEST_PLUGIN_DIR;
        }

        echo '<h1>' . self::class . '</h1>';
    }

    public function testArea(): void
    {
        ob_start();

        for ($i = 0; $i < 5; $i++) {
            do_action('test_custom_subs', 'test1', 'test2');
        }

        $this->testOutput = ob_get_clean();

        echo "<pre>{$this->testOutput}</pre>";
    }

    public function testMethod(string $test, string $test2): void
    {
        echo "<p><b>Dump:</b> {$test}, {$test2} <br></p>";
    }

    public function secondMethod(string $test, string $test2): void
    {
        $test  = strrev($test);
        $test2 = strrev($test2);
        echo "<p><b>Reverse:</b> {$test}, {$test2} <br> <small>Times called: {$this->called}</small></p>";
    }
}

```
 
 ## Info & inspiration

 This idea provides flexibility, so you don't have to overuse the singleton pattern in OOP wordpress plugins. 
 Now you are not restricted to extending classes for every other instance you need (for example: when you build a post type class you can create a config with post types you can loop trough instead of having to make separate classes for each post type).
 
 This package is a continuation of one of my older experiments:
 
 https://github.com/jascha030/wp-subscriptions
 
 Somewhere during finishing this I finally realised that my older idea was overcomplicated.
 
 Original idea based on an idea from [this article](https://carlalexander.ca/polymorphism-wordpress-interfaces/) article by Carl Alexander.
 
 Further inspiration taken from developing API's in Laravel.
 

 
