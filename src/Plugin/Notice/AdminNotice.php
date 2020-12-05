<?php


namespace Jascha030\WP\OOPOR\Plugin\Notice;


class AdminNotice
{
    public const ERROR   = 0;
    public const WARNING = 1;
    public const SUCCESS = 2;
    public const INFO    = 3;

    protected const CSS_TEMPLATE = 'notice %s %s';

    protected const HTML_TEMPLATE = '<div class="%1$s"><p>%2$s</p></div>';

    public static $types = [
        self::ERROR   => 'notice-error',
        self::WARNING => 'notice-warning',
        self::SUCCESS => 'notice-success',
        self::INFO    => 'notice-info'
    ];

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
        $type  = array_key_exists($type, static::$types) ? $type : self::INFO;
        $class = static::$types[$type];

        return sprintf(static::CSS_TEMPLATE, $class, $this->dismissible ? 'is-dismissible' : '');
    }

    final public function display(): void
    {
        if (is_admin()) {
            printf(self::HTML_TEMPLATE, $this->getNoticeTypeCssClass($this->type), esc_html(__($this->message)));
        }
    }
}