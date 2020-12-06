<?php


namespace Jascha030\WP\OOP\Plugin\Notice;


use Exception;

class AdminNotice
{
    public const ERROR   = 0;
    public const WARNING = 1;
    public const SUCCESS = 2;
    public const INFO    = 3;

    public const TYPES = [
        self::ERROR   => 'notice-error',
        self::WARNING => 'notice-warning',
        self::SUCCESS => 'notice-success',
        self::INFO    => 'notice-info'
    ];

    private const HTML_TEMPLATE = '<div class="%1$s"><p>%2$s</p></div>';

    private const CSS_TEMPLATE = 'notice %s %s';

    private string $message;

    private string $type;

    private bool $dismissible;

    public function __construct(string $message, int $type = self::INFO, bool $dismissible = true)
    {
        $this->message = $message;

        $this->type = $this->getNoticeTypeCssClass($type);

        $this->dismissible = $dismissible;
    }

    private function getNoticeTypeCssClass(int $type = self::INFO): string
    {
        $type  = array_key_exists($type, self::TYPES) ? $type : self::INFO;
        $class = self::TYPES[$type];

        return sprintf(self::CSS_TEMPLATE, $class, $this->dismissible ? 'is-dismissible' : '');
    }

    final public function display(): void
    {
        if (is_admin()) {
            printf(self::HTML_TEMPLATE, $this->getNoticeTypeCssClass($this->type), esc_html(__($this->message)));
        }
    }

    final public function toException()
    {
        return new Exception(esc_html($this->message));
    }

    final public static function fromException(Exception $exception): ErrorNotice
    {
        return new ErrorNotice($exception->getMessage(), false);
    }
}