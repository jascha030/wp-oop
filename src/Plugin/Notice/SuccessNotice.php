<?php


namespace Jascha030\WP\OOPOR\Plugin\Notice;


class SuccessNotice extends AdminNotice
{
    final public function __construct(string $message, bool $dismissible = true)
    {
        parent::__construct($message, self::SUCCESS, $dismissible);
    }
}