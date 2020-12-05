<?php


namespace Jascha030\WP\OOPOR\Plugin\Notice;


final class InfoNotice extends AdminNotice
{
    public function __construct(string $message, bool $dismissible = true)
    {
        parent::__construct($message, self::INFO, $dismissible);
    }
}