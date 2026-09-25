<?php
/**
 * Theme Performance Optimizations
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Remove bloat from WordPress head for maximum speed & high PageSpeed scores
 */
function neebites_cleanup_wp_head() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
    remove_action('wp_head', 'wp_oembed_add_host_js');

    // Remove Emoji scripts & styles
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'neebites_cleanup_wp_head');

/**
 * Preconnect to Google Fonts and external CDNs for faster FCP
 */
function neebites_resource_hints($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = [
            'href' => 'https://fonts.googleapis.com',
            'crossorigin' => 'anonymous',
        ];
        $urls[] = [
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        ];
    }
    return $urls;
}
add_filter('wp_resource_hints', 'neebites_resource_hints', 10, 2);

/**
 * Defer non-critical scripts for faster Time to Interactive (TTI)
 */
function neebites_defer_scripts($tag, $handle, $src) {
    if (is_admin()) return $tag;

    $defer_scripts = [
        'wp-embed',
        'comment-reply',
        'wc-cart-fragments',
        'js-cookie',
    ];

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'neebites_defer_scripts', 10, 3);
