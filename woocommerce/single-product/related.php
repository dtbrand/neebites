<?php
/**
 * Related Products Template Override
 *
 * Provides luxury artisan headers, matching the homepage and shop archive card architecture.
 *
 * @package Neebites
 * @version 2.7.2
 */

defined('ABSPATH') || exit;

if ($related_products) {
    if (count($related_products) < 4 && function_exists('wc_get_products')) {
        $exclude_ids = array_map(function($p) { return $p->get_id(); }, $related_products);
        $exclude_ids[] = get_the_ID();
        $fallback_ids = wc_get_products([
            'status'  => 'publish',
            'limit'   => 4 - count($related_products),
            'exclude' => $exclude_ids,
            'return'  => 'ids',
        ]);
        foreach ($fallback_ids as $fid) {
            $f_prod = wc_get_product($fid);
            if ($f_prod) {
                $related_products[] = $f_prod;
            }
        }
    }
}

if ($related_products) : ?>

    <section class="related products">

        <div class="related-section-header">
            <span class="related-section-tag"><?php esc_html_e('Curated Pairings & Delicacies', 'neebites'); ?></span>
            <h2 class="related-section-title"><?php esc_html_e('Artisan Confections You’ll Love', 'neebites'); ?></h2>
        </div>

        <?php woocommerce_product_loop_start(); ?>

        <?php foreach ($related_products as $related_product) : ?>

            <?php
            $post_object = get_post($related_product->get_id());

            setup_postdata($GLOBALS['post'] = &$post_object); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.CommentedOutCode.Found

            wc_get_template_part('content', 'product');
            ?>

        <?php endforeach; ?>

        <?php woocommerce_product_loop_end(); ?>

    </section>
    <?php
endif;

wp_reset_postdata();
