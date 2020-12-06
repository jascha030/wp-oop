<?php


namespace Jascha030\WP\OOP\Plugin\Notice;


final class ErrorNotice extends AdminNotice
{
    public function __construct(string $message, bool $dismissible = true)
    {
        parent::__construct($message, self::ERROR, $dismissible);
    }
}