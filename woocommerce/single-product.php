<?php
/**
 * The Template for displaying all single products
 *
 * @package Neebites
 * @version 2.4.0
 */

defined('ABSPATH') || exit;

get_header('shop');

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked neebites_woocommerce_wrapper_before - 10
 */
do_action('woocommerce_before_main_content');

while (have_posts()) :
	the_post();
	wc_get_template_part('content', 'single-product');
endwhile;

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked neebites_woocommerce_wrapper_after - 10
 */
do_action('woocommerce_after_main_content');

get_footer('shop');
