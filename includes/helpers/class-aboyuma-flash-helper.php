<?php

/**
 * Custom Flash Helper
 *
 * Handles temporary admin flash messages with per-user scoping.
 *
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

class Aboyuma_Flash_Helper {

    /**
     * Get transient key scoped per user.
     *
     * Prevents admin notice collision between multiple logged-in users.
     *
     * @param string $key Optional custom namespace key.
     * @return string
     */
    public static function get_flash_key($key = 'aboyuma_flash') {
        return $key . '_' . get_current_user_id();
    }

    /**
     * Set flash message.
     *
     * @param string $type
     * @param string $message
     * @param array  $extra Optional additional data.
     * @param string $key Optional namespace key.
     * @param int    $expiration Transient expiration (seconds).
     */
    public static function set($type, $message, $extra = [], $key = 'aboyuma_flash', $expiration = 30) {

        $payload = array_merge(
            [
                'status' => [
                    'type'    => $type,
                    'message' => $message,
                ],
            ],
            $extra
        );

        set_transient(
            self::get_flash_key($key),
            $payload,
            $expiration
        );
    }

    /**
     * Set flash message and redirect.
     *
     * @param string $type
     * @param string $message
     * @param string $redirect_url
     * @param array  $extra Optional additional data.
     * @param string $key Optional namespace key.
     */
    public static function redirect($type, $message, $redirect_url, $extra = [], $key = 'aboyuma_flash') {

        self::set($type, $message, $extra, $key);

        wp_safe_redirect($redirect_url);
        exit;
    }
}
