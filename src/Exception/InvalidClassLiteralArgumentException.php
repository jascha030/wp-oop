<?php

namespace Jascha030\WP\OOP\Exception;

/**
 * Class InvalidClassLiteralArgumentException
 *
 * @package Jascha030\WP\OOP\Exception
 * @author Jascha030 <contact@jaschavanaalst.nl>
 */
final class InvalidClassLiteralArgumentException extends InvalidClassLiteralException
{
    private const PREPEND_STRING = 'Invalid argument%s: ';

    public function __construct(
        string $argumentName = null,
        string $providedClass = null,
        $implements = null,
        string $message = null
    ) {
        parent::__construct($message ? $this->createArgumentMessage($argumentName, $providedClass, $implements) : null);
    }


    /**
     * @param string|null $argumentName
     * @param string|null $providedClass
     * @param string|array|null $implements
     *
     * @return string
     */
    private function createArgumentMessage(
        string $argumentName = null,
        string $providedClass = null,
        string $implements = null
    ): string {
        $append  = sprintf(self::PREPEND_STRING, ! $argumentName ? '' : $argumentName);
        $classes = is_array($implements) ? implode(',', $implements) : $implements;

        return $append . $this->createMessage($providedClass, $classes, is_array($implements));
    }
}
