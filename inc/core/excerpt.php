<?php
/**
 * Excerpt customization
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Filter the excerpt length.
 */
function neebites_custom_excerpt_length($length) {
    if (is_admin()) {
        return $length;
    }
    return (int) neebites_get_option('excerpt_length', 24);
}
add_filter('excerpt_length', 'neebites_custom_excerpt_length', 999);

/**
 * Filter the excerpt "read more" string.
 */
function neebites_custom_excerpt_more($more) {
    if (is_admin()) {
        return $more;
    }
    return '...';
}
add_filter('excerpt_more', 'neebites_custom_excerpt_more', 999);
