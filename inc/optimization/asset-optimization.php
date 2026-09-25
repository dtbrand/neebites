<?php
/**
 * Asset Optimization & Caching Helpers
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Remove script version query parameters for static caching
 */
function neebites_remove_ver_query_args($src) {
    if (strpos($src, 'ver=') && !strpos($src, 'wp-includes/js/wp-auth-check')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'neebites_remove_ver_query_args', 999);
add_filter('script_loader_src', 'neebites_remove_ver_query_args', 999);
