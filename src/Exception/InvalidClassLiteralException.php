<?php

namespace Jascha030\WP\OOP\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

/**
 * Class InvalidArgumentImplementationException
 *
 * Throwable for Class string literal arguments that don't implement their expected interface
 *
 * @package Jascha030\WP\OOP\Exception
 * @author Jascha030 <contact@jaschavanaalst.nl>
 */
class InvalidClassLiteralException extends Exception implements ContainerExceptionInterface
{
    private const MESSAGE_TEMPLATE = '%s does not implement %s.';

    /**
     * InvalidClassLiteralException constructor.
     *
     * @param  string|null  $providedClass
     * @param  string|null  $implements
     * @param  bool  $multiple
     * @param  string|null  $message
     */
    public function __construct(
        string $providedClass = null,
        string $implements = null,
        bool $multiple = false,
        string $message = null
    ) {
        parent::__construct(! $message ? $this->createMessage($providedClass, $implements, $multiple) : $message);
    }

    /**
     * @param string $providedClass
     * @param string|null $implements
     * @param bool $multiple
     *
     * @return string
     */
    final public function createMessage(
        string $providedClass = '',
        string $implements = null,
        bool $multiple = false
    ): string {
        $prepend = ! $providedClass ? 'Class ' : sprintf('Class: %s', $providedClass);
        $append  = 'interface.';

        if ($implements && $multiple) {
            $append = 'interface: %s.';
            $append = sprintf($append, $implements);
        }

        return sprintf(self::MESSAGE_TEMPLATE, $prepend, $append);
    }
}
