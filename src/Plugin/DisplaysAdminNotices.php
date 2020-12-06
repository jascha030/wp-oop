<?php

namespace Jascha030\WP\OOP\Plugin;

use Jascha030\WP\OOP\Plugin\Notice\AdminNotice;

trait DisplaysAdminNotices
{
    /**
     * @var array|AdminNotice[]
     */
    private array $notices = [];

    /**
     * Set notice to be displayed in wp-admin
     *
     * @param  string  $message
     * @param  int  $type
     * @param  bool  $dismissible
     */
    final public function createAndAddNotice(
        string $message,
        int $type = AdminNotice::INFO,
        bool $dismissible = true
    ): void {
        $this->notices[] = new AdminNotice($message, $type);
    }


    public function addNotice(AdminNotice $notice): void
    {
        $this->notices[] = $notice;
    }

    /**
     * Display set notices in wp-admin
     */
    final public function displayNotices(): void
    {
        foreach ($this->notices as $notice) {
            $notice->display();
        }
    }
}
