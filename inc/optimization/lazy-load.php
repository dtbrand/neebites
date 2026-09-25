<?php
/**
 * Lazy Loading, Image Optimization & Speed Enhancements
 *
 * @package Neebites
 * @version 1.1.1
 */

if (!defined('ABSPATH')) exit;

/**
 * Ensure native lazy loading and async decoding for images and attachments
 */
function neebites_lazy_load_attributes($attr, $attachment, $size) {
    if (is_admin()) return $attr;

    // Async image decoding for smoother rendering thread
    if (!isset($attr['decoding'])) {
        $attr['decoding'] = 'async';
    }

    // Check if image is hero or priority
    if (isset($attr['fetchpriority']) && $attr['fetchpriority'] === 'high') {
        $attr['loading'] = 'eager';
    } else {
        $attr['loading'] = 'lazy';
    }

    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'neebites_lazy_load_attributes', 10, 3);

/**
 * Add preconnect resource hints for high-speed font rendering
 */
function neebites_add_resource_hints($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = array(
            'href'        => 'https://fonts.googleapis.com',
            'crossorigin' => 'anonymous',
        );
        $urls[] = array(
            'href'        => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        );
    }
    return $urls;
}
add_filter('wp_resource_hints', 'neebites_add_resource_hints', 10, 2);

/**
 * Remove default WordPress Emoji scripts and styles to boost page load speed
 */
function neebites_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', 'neebites_disable_emojis_tinymce');
    add_filter('wp_resource_hints', 'neebites_disable_emojis_dns', 10, 2);
}
add_action('init', 'neebites_disable_emojis');

/**
 * Filter function used to remove the tinymce emoji plugin.
 */
function neebites_disable_emojis_tinymce($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    }
    return array();
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 */
function neebites_disable_emojis_dns($urls, $relation_type) {
    if ('dns-prefetch' === $relation_type) {
        $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/');
        foreach ($urls as $key => $url) {
            if (is_string($url) && strpos($url, $emoji_svg_url) !== false) {
                unset($urls[$key]);
            } elseif (is_array($url) && isset($url['href']) && strpos($url['href'], $emoji_svg_url) !== false) {
                unset($urls[$key]);
            }
        }
    }
    return $urls;
}
