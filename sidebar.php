<?php
/**
 * The sidebar containing the main widget area
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

if (!is_active_sidebar('blog-sidebar')) {
    return;
}
?>

<aside id="secondary" class="widget-area blog-sidebar" role="complementary" aria-label="<?php esc_attr_e('Blog Sidebar', 'neebites'); ?>">
    <?php dynamic_sidebar('blog-sidebar'); ?>
</aside>
