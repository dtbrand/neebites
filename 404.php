<?php
/**
 * 404 Error Page Template
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

get_header();

$shop_url = home_url('/');
if (neebites_is_woocommerce_active() && function_exists('wc_get_page_id')) {
    $shop_id = wc_get_page_id('shop');
    if ($shop_id > 0) {
        $shop_url = get_permalink($shop_id);
    }
}
?>

<div class="error-404-wrapper">
    <div class="container">
        <div class="error-404-content">
            <div class="error-404-inner">
                <span class="error-badge">🍫 404</span>
                <h1 class="error-title"><?php esc_html_e('Oops! This Confection Has Melted Away', 'neebites'); ?></h1>
                <p class="error-desc"><?php esc_html_e('We couldn\'t find the confection you were looking for. It may be out of season or currently being tempered by our master chocolatier.', 'neebites'); ?></p>
                <div class="error-actions">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary btn-lg"><?php esc_html_e('Return Home', 'neebites'); ?></a>
                    <a href="<?php echo esc_url($shop_url); ?>" class="btn btn-outline btn-lg"><?php esc_html_e('Shop All Chocolates', 'neebites'); ?></a>
                </div>
                <div class="error-search">
                    <p class="search-prompt"><?php esc_html_e('Search our confectionery selection:', 'neebites'); ?></p>
                    <?php get_search_form(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
