<?php
/**
 * WooCommerce Setup & Layout Hooks
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

/**
 * WooCommerce theme support & configuration
 */
function neebites_woocommerce_setup() {
    add_theme_support('woocommerce', [
        'thumbnail_image_width'         => 420,
        'single_image_width'            => 650,
        'gallery_thumbnail_image_width' => 120,
        'product_grid' => [
            'default_rows'    => 4,
            'min_rows'        => 1,
            'max_rows'        => 12,
            'default_columns' => 4,
            'min_columns'     => 2,
            'max_columns'     => 5,
        ],
    ]);

    // Bespoke Myntra-grade product gallery handled via custom template & controller (zero opacity 0 glitches)
}
add_action('after_setup_theme', 'neebites_woocommerce_setup');

/**
 * Luxurious Botanical Shop Hero Banner with Breadcrumbs & Category Pills
 */
function neebites_shop_hero_banner() {
    if (!function_exists('is_shop') || (!is_shop() && !is_product_taxonomy())) return;

    $shop_url    = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/shop');
    $cat_rows    = function_exists('neebites_shop_category_counts') ? neebites_shop_category_counts() : [];
    $total_count = function_exists('neebites_shop_total_product_count') ? neebites_shop_total_product_count() : 0;
    $current_cat = is_tax('product_cat') ? get_queried_object() : null;

    $title = is_shop() ? __('Artisan Chocolates & Confectionery', 'neebites') : single_term_title('', false);
    $subtitle = is_shop() 
        ? __('Handcrafted Belgian chocolates, California slow-roasted chocolate coated almonds, and gourmet confectionery delivered in insulated cold-pack freshness.', 'neebites')
        : term_description();
    if (empty($subtitle)) {
        $subtitle = __('Carefully tempered single-origin cocoa confections and roasted nut delicacies.', 'neebites');
    }
    ?>
    <section class="neebites-shop-hero-banner myntra-header-section">
        <div class="container">
            <div class="shop-hero-inner">
                <nav class="shop-hero-breadcrumb myntra-breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'neebites'); ?>">
                    <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'neebites'); ?></a>
                    <span class="sep">/</span>
                    <a href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('Chocolates & Confectionery', 'neebites'); ?></a>
                    <span class="sep">/</span>
                    <?php if ($current_cat) : ?>
                        <span class="current"><?php echo esc_html($current_cat->name); ?></span>
                    <?php else : ?>
                        <span class="current"><?php esc_html_e('Chocolate Coated Nuts', 'neebites'); ?></span>
                    <?php endif; ?>
                </nav>

                <div class="myntra-title-row">
                    <h1 class="shop-hero-title"><?php echo esc_html($current_cat ? $current_cat->name : __('Chocolates & Confectionery', 'neebites')); ?></h1>
                    <span class="shop-hero-count"><?php echo esc_html($total_count); ?> <?php esc_html_e('items', 'neebites'); ?></span>
                </div>

                <!-- Next-Level Flavour Filter Tabs matching Reference Screenshot -->
                <div class="trending-filter-tabs shop-filter-tabs" role="tablist" aria-label="<?php esc_attr_e('Filter Confections by Flavour', 'neebites'); ?>">
                    <button type="button" class="tab-pill active" data-filter="all" role="tab" aria-selected="true"><?php esc_html_e('All Confections', 'neebites'); ?></button>
                    <button type="button" class="tab-pill" data-filter="dark" role="tab" aria-selected="false"><?php esc_html_e('Dark Chocolate 🍫', 'neebites'); ?></button>
                    <button type="button" class="tab-pill" data-filter="kiwi" role="tab" aria-selected="false"><?php esc_html_e('Kiwi Signature 🥝', 'neebites'); ?></button>
                    <button type="button" class="tab-pill" data-filter="milk,white" role="tab" aria-selected="false"><?php esc_html_e('Milk & White 🥛', 'neebites'); ?></button>
                    <button type="button" class="tab-pill" data-filter="other,gourmet" role="tab" aria-selected="false"><?php esc_html_e('Gourmet Flavours ✨', 'neebites'); ?></button>
                </div>
            </div>
        </div>
    </section>
    <?php
}
add_action('woocommerce_before_main_content', 'neebites_shop_hero_banner', 5);

// Suppress default plain breadcrumb and plain title on shop archive
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
add_filter('woocommerce_show_page_title', function($show) {
    if (is_shop() || is_product_taxonomy()) {
        return false;
    }
    return $show;
});

/**
 * Remove default WooCommerce wrappers and replace with Neebites container
 */
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

function neebites_woocommerce_wrapper_before() {
    echo '<div class="shop-wrapper sidebar-pos-drawer"><div class="container"><div class="shop-inner-layout"><div class="shop-main-content" id="shop-main-content">';
}
add_action('woocommerce_before_main_content', 'neebites_woocommerce_wrapper_before', 10);

function neebites_woocommerce_wrapper_after() {
    echo '</div>'; // close .shop-main-content
    if (is_shop() || is_product_taxonomy()) {
        get_sidebar('shop');
    }
    echo '</div></div></div>'; // close .shop-inner-layout, .container, .shop-wrapper
}
add_action('woocommerce_after_main_content', 'neebites_woocommerce_wrapper_after', 10);

/**
 * Customize WooCommerce Breadcrumb Defaults
 */
function neebites_woocommerce_breadcrumbs_defaults($args) {
    $args['delimiter']   = '<span class="breadcrumb-sep" aria-hidden="true"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg></span>';
    $args['wrap_before'] = '<nav class="woocommerce-breadcrumb neebites-wc-breadcrumb" aria-label="' . esc_attr__('Breadcrumb', 'neebites') . '">';
    $args['wrap_after']  = '</nav>';
    $args['before']      = '<span class="breadcrumb-crumb">';
    $args['after']       = '</span>';
    return $args;
}
add_filter('woocommerce_breadcrumb_defaults', 'neebites_woocommerce_breadcrumbs_defaults');

/**
 * Custom Sale Flash Badge
 */
function neebites_custom_sale_flash($html, $post, $product) {
    if (!$product || !$product->is_on_sale()) return '';

    $discount = '';
    if ($product->is_type('simple')) {
        $reg = (float) $product->get_regular_price();
        $sale = (float) $product->get_sale_price();
        if ($reg > 0 && $sale > 0 && $reg > $sale) {
            $pct = round((($reg - $sale) / $reg) * 100);
            $discount = '-' . $pct . '%';
        }
    }

    $label = $discount ? $discount : esc_html__('Sale', 'neebites');
    return '<span class="product-badge badge-sale">' . esc_html($label) . '</span>';
}
add_filter('woocommerce_sale_flash', 'neebites_custom_sale_flash', 10, 3);

/**
 * Shop toolbar: brackets WooCommerce's result count (20) and catalog
 * ordering (30) so both land in the right-hand group, and prints the
 * filter drawer trigger for desktop & mobile.
 */
function neebites_shop_toolbar_open() {
    if (!function_exists('is_shop') || (!is_shop() && !is_product_taxonomy())) return;

    echo '<div class="shop-toolbar">';
    echo '<div class="shop-toolbar-left">';
    echo '<button type="button" class="btn-open-filters" aria-expanded="false" aria-controls="shop-secondary">';
    echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>';
    echo '<span>' . esc_html__('Filters', 'neebites') . '</span>';
    echo '<span class="filter-count-badge" aria-hidden="true">0</span>';
    echo '</button>';
    echo '</div>';
    echo '<div class="shop-toolbar-right">';
}
add_action('woocommerce_before_shop_loop', 'neebites_shop_toolbar_open', 19);

function neebites_shop_toolbar_close() {
    if (!function_exists('is_shop') || (!is_shop() && !is_product_taxonomy())) return;
    echo '</div></div>';
}
add_action('woocommerce_before_shop_loop', 'neebites_shop_toolbar_close', 31);

/**
 * Add Cart Counter & Mini-Cart Fragments for instant AJAX update
 */
function neebites_cart_fragments($fragments) {
    // Header cart count
    $count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
    $fragments['span.cart-count'] = '<span class="cart-count">' . esc_html($count) . '</span>';

    // Mini cart drawer content
    ob_start();
    neebites_render_mini_cart_content();
    $fragments['div.mini-cart-drawer-content'] = ob_get_clean();

    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'neebites_cart_fragments');

/**
 * Configure loop columns
 */
function neebites_loop_columns() {
    return (int) neebites_get_option('shop_columns', 4);
}
add_filter('loop_shop_columns', 'neebites_loop_columns');

/**
 * Set products per page
 */
function neebites_products_per_page() {
    return (int) neebites_get_option('shop_products_per_page', 12);
}
add_filter('loop_shop_per_page', 'neebites_products_per_page', 20);
