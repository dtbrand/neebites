<?php
/**
 * Core Theme Functions
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Get theme option with default fallback
 */
function neebites_get_option($option, $default = '') {
    if ($option === 'shop_sidebar') {
        return apply_filters('neebites_shop_sidebar_position', 'drawer');
    }
    $options = get_option('neebites_theme_options', []);
    $val = '';
    if (is_array($options) && array_key_exists($option, $options)) {
        $val = $options[$option];
    } else {
        $mod = get_theme_mod($option);
        if ($mod !== false && $mod !== '') {
            $val = $mod;
        } else {
            $val = $default;
        }
    }

    if (is_string($val)) {
        $legacy_greens = [
            '#134a21', '#1a2e1f', '#4c7c59', '#e07a5f', '#f8faf6', '#2d5a36', '#67a671',
            '#bcd6bf', '#c9decb', '#edf0ed', '#eef2ed', '#f0f6f1', '#f8faf7', '#0e3b1a',
            '#082610', '#051a0b', '#1c5b2a', '#2e7d32', '#1e4620', '#0d3517', '#047857',
            '#10b981', '#059669', '#34d399', '#2d5a27', '#1b4332', '#00d084', '#7bdcb5'
        ];
        if (in_array(strtolower(trim($val)), $legacy_greens)) {
            return $default;
        }
        if (stripos($val, 'botanical') !== false) {
            $val = str_ireplace('Botanical Sanctuary', 'Artisan Confectionery Sanctuary', $val);
            $val = str_ireplace('Botanical', 'Artisan Confectionery', $val);
        }
        if (stripos($val, 'plant') !== false) {
            $val = str_ireplace('plants', 'chocolates', $val);
            $val = str_ireplace('plant', 'confection', $val);
        }
    }
    return $val;
}


/**
 * Sanitize checkbox inputs
 */
function neebites_sanitize_checkbox($input) {
    return (isset($input) && true == $input) ? true : false;
}

/**
 * Generate CSS variables for the theme based on luxury chocolate brand system
 */
function neebites_generate_css_variables() {
    $primary       = '#3D2314'; // Primary Deep Dark Cocoa
    $primary_light = '#6B4226'; // Warm Milk Cocoa
    $primary_dark  = '#231205'; // Deep Velvety Espresso
    $accent        = '#C59B27'; // Gourmet Caramel Amber
    $sale          = '#C0392B'; // Ruby Cocoa Red
    $text_dark     = '#241408'; // Roasted Espresso Text
    $text_medium   = '#5C4434'; // Roasted Cocoa Text
    $text_light    = '#8C7362'; // Warm Truffle Muted
    $border        = '#EAE0D5'; // Biscuit Sand Border
    $bg_light      = '#FAF6F0'; // Vanilla Cream Background
    $bg_white      = '#FFFFFF';
    $font_primary  = '"Plus Jakarta Sans", "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    $font_heading  = '"Newsreader", "Victor Serif", "Playfair Display", Georgia, serif';
    $container_w   = '1240px';
    $header_h      = '76px';
    $radius        = '10px';

    $vars = [
        '--color-primary'       => $primary,
        '--color-primary-light' => $primary_light,
        '--color-primary-dark'  => $primary_dark,
        '--color-accent'        => $accent,
        '--color-sale'          => $sale,
        '--color-text'          => $text_dark,
        '--color-text-medium'   => $text_medium,
        '--color-text-light'    => $text_light,
        '--color-border'        => $border,
        '--color-bg-light'      => $bg_light,
        '--color-bg-white'      => $bg_white,
        '--color-success'       => '#C59B27',
        '--font-primary'        => $font_primary,
        '--font-heading'        => $font_heading,
        '--container-max-width' => $container_w,
        '--header-height'       => $header_h,
        '--border-radius'       => $radius,
        '--radius-pill'         => '9999px',
        '--shadow-sm'           => '0 2px 8px rgba(61, 35, 20, 0.05)',
        '--shadow-md'           => '0 8px 24px rgba(61, 35, 20, 0.08)',
        '--shadow-lg'           => '0 16px 36px rgba(61, 35, 20, 0.12)',
        '--transition'          => 'all 0.25s cubic-bezier(0.4, 0, 0.2, 1)',
    ];

    $css = ":root {\n";
    foreach ($vars as $name => $value) {
        $css .= "  {$name}: {$value} !important;\n";
    }
    $css .= "}\n";

    echo "<style id=\"neebites-css-vars\">{$css}</style>\n";
}
add_action('wp_head', 'neebites_generate_css_variables', 20);

/**
 * Filter body classes
 */
function neebites_body_classes($classes) {
    if (neebites_is_woocommerce_active()) {
        if (is_product() || is_shop() || is_product_category() || is_product_tag()) {
            $classes[] = 'neebites-woocommerce';
            $classes[] = 'sidebar-' . esc_attr(neebites_get_option('shop_sidebar', 'left'));
        }
        if (function_exists('WC') && WC()->cart && WC()->cart->get_cart_contents_count() > 0) {
            $classes[] = 'has-cart-items';
        }
    }
    $classes[] = 'header-' . esc_attr(neebites_get_option('header_style', 'style-1'));
    $classes[] = 'mobile-menu-' . esc_attr(neebites_get_option('mobile_menu_style', 'slide'));
    return $classes;
}
add_filter('body_class', 'neebites_body_classes');

/**
 * Register Widget Sidebars
 */
function neebites_register_sidebars() {
    register_sidebar([
        'name'          => esc_html__('Shop Sidebar', 'neebites'),
        'id'            => 'shop-sidebar',
        'description'   => esc_html__('Sidebar displayed on shop and product archive pages.', 'neebites'),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    register_sidebar([
        'name'          => esc_html__('Blog Sidebar', 'neebites'),
        'id'            => 'blog-sidebar',
        'description'   => esc_html__('Sidebar displayed on blog posts and archives.', 'neebites'),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    for ($i = 1; $i <= 4; $i++) {
        register_sidebar([
            'name'          => sprintf(esc_html__('Footer Column %d', 'neebites'), $i),
            'id'            => 'footer-' . $i,
            'description'   => sprintf(esc_html__('Widgets for footer column %d.', 'neebites'), $i),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ]);
    }
}
add_action('widgets_init', 'neebites_register_sidebars');

/**
 * Top Shipping / Guarantee Announcement Bar
 */
function neebites_shipping_bar() {
    if (!neebites_get_option('shipping_bar_enabled', true)) {
        return;
    }

    $free_threshold = (float) neebites_get_option('free_shipping_threshold', 500);
    $shop_url = function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0 ? get_permalink(wc_get_page_id('shop')) : home_url('/shop');

    $slides = [
        [
            'tag'  => __('FREE SHIPPING', 'neebites'),
            'icon' => '🚚',
            'text' => sprintf(__('Free Insulated Cold-Pack Delivery on orders over ₹%s', 'neebites'), number_format_i18n($free_threshold)),
            'url'  => $shop_url,
        ],
        [
            'tag'  => __('MELT-PROOF', 'neebites'),
            'icon' => '❄️',
            'text' => __('Melt-Proof Delivery Guarantee: 100% solid & chilled in eco-insulated packs', 'neebites'),
            'url'  => home_url('/contact'),
        ],
        [
            'tag'  => __('PURE COCOA', 'neebites'),
            'icon' => '🍫',
            'text' => __('100% Pure Cocoa Butter & Zero Palm Oil • Handcrafted California Roasted Almonds', 'neebites'),
            'url'  => $shop_url,
        ],
        [
            'tag'  => __('SWEET 10%', 'neebites'),
            'icon' => '✨',
            'text' => __('Use code SWEET10 for 10% off your first artisan confectionery gift box', 'neebites'),
            'url'  => $shop_url,
        ],
    ];
    ?>
    <!-- Top Announcement / Shipping Ticker Bar -->
    <div class="neebites-announcement-bar" role="region" aria-label="<?php esc_attr_e('Top Announcements', 'neebites'); ?>">
        <div class="container announcement-container">
            <!-- Left Side Chip (Desktop) -->
            <div class="announcement-side announcement-side-left">
                <span class="announcement-chip">
                    <span class="chip-pulse" aria-hidden="true"></span>
                    <span class="chip-text"><?php esc_html_e('Artisan Batch Drops', 'neebites'); ?></span>
                </span>
            </div>

            <!-- Center 1-Line Smart Auto-Slider -->
            <div class="announcement-slider-wrap">
                <button type="button" class="announcement-nav-btn btn-announcement-prev" aria-label="<?php esc_attr_e('Previous announcement', 'neebites'); ?>">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>

                <div class="announcement-slider-viewport" id="announcement-slider-viewport" aria-live="off">
                    <div class="announcement-slider-track" id="announcement-slider-track">
                        <?php foreach ($slides as $idx => $s) : ?>
                            <div class="announcement-slide <?php echo $idx === 0 ? 'is-active' : ''; ?>" data-slide-index="<?php echo esc_attr($idx); ?>">
                                <a href="<?php echo esc_url($s['url']); ?>" class="announcement-link">
                                    <span class="announcement-pill"><?php echo esc_html($s['tag']); ?></span>
                                    <span class="announcement-text"><?php echo esc_html($s['icon'] . ' ' . $s['text']); ?></span>
                                    <span class="announcement-arrow" aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="button" class="announcement-nav-btn btn-announcement-next" aria-label="<?php esc_attr_e('Next announcement', 'neebites'); ?>">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>

            <!-- Right Side Perk (Desktop) -->
            <div class="announcement-side announcement-side-right">
                <span class="announcement-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <?php esc_html_e('Cold-Pack Protected', 'neebites'); ?>
                </span>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Wishlist count total helper
 */
function neebites_get_wishlist_count_total() {
    if (isset($_COOKIE['neebites_wishlist'])) {
        $items = json_decode(wp_unslash($_COOKIE['neebites_wishlist']), true);
        if (is_array($items)) {
            return count(array_values(array_unique(array_filter(array_map('absint', $items)))));
        }
    }
    return 0;
}

/**
 * Fallback menu callback - Renders Website Category Structure
 */
function neebites_fallback_menu() {
    $shop_url = function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0 ? get_permalink(wc_get_page_id('shop')) : home_url('/shop');
    echo '<ul class="primary-nav menu fallback-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'neebites') . '</a></li>';
    echo '<li class="menu-item-has-children"><a href="' . esc_url(home_url('/product-category/chocolates-confectionery/')) . '">' . esc_html__('🍫 Chocolates & Confectionery', 'neebites') . '</a>';
    echo '<ul class="sub-menu">';
    echo '<li class="menu-item-has-children"><a href="' . esc_url(home_url('/product-category/chocolate-coated-nuts/')) . '">' . esc_html__('🥜 Chocolate Coated Nuts', 'neebites') . '</a>';
    echo '<ul class="sub-menu">';
    echo '<li class="menu-item-has-children"><a href="' . esc_url(home_url('/product-category/chocolate-coated-almonds/')) . '">' . esc_html__('Chocolate Coated Almonds', 'neebites') . '</a>';
    echo '<ul class="sub-menu">';
    echo '<li><a href="' . esc_url(add_query_arg('product_cat', 'dark-chocolate-almond', $shop_url)) . '">' . esc_html__('Dark Chocolate Almond', 'neebites') . '</a></li>';
    echo '<li><a href="' . esc_url(add_query_arg('product_cat', 'kiwi-chocolate-almond', $shop_url)) . '">' . esc_html__('Kiwi Chocolate Almond', 'neebites') . '</a></li>';
    echo '<li><a href="' . esc_url(add_query_arg('product_cat', 'milk-chocolate-almond', $shop_url)) . '">' . esc_html__('Milk Chocolate Almond', 'neebites') . '</a></li>';
    echo '<li><a href="' . esc_url(add_query_arg('product_cat', 'white-chocolate-almond', $shop_url)) . '">' . esc_html__('White Chocolate Almond', 'neebites') . '</a></li>';
    echo '<li><a href="' . esc_url(add_query_arg('product_cat', 'other-flavoured-almonds', $shop_url)) . '">' . esc_html__('Other Flavoured Almonds', 'neebites') . '</a></li>';
    echo '</ul>';
    echo '</li>';
    echo '</ul>';
    echo '</li>';
    echo '</ul>';
    echo '</li>';
    echo '<li><a href="' . esc_url($shop_url) . '">' . esc_html__('Shop', 'neebites') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/about-us')) . '">' . esc_html__('About Us', 'neebites') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact')) . '">' . esc_html__('Contact', 'neebites') . '</a></li>';
    echo '</ul>';
}

/**
 * Page Header Helper
 */
function neebites_page_header($title = '', $subtitle = '') {
    if (empty($title)) {
        $title = get_the_title();
    }

    // Woodmart Architecture: Luxury Dark Checkout Breadcrumb Banner (SHOPPING CART -> CHECKOUT -> ORDER COMPLETE)
    if (function_exists('is_checkout') && is_checkout() && !is_order_received_page()) {
        ?>
        <div class="neebites-checkout-steps-wrapper woodmart-steps-dark-banner">
            <div class="container text-center">
                <ul class="wd-checkout-steps">
                    <li class="step-runtitle">
                        <a href="<?php echo function_exists('wc_get_cart_url') ? esc_url(wc_get_cart_url()) : home_url('/cart/'); ?>">
                            <span><?php esc_html_e('SHOPPING CART', 'neebites'); ?></span>
                        </a>
                        <span class="step-arrow">&rarr;</span>
                    </li>
                    <li class="step-active">
                        <span><?php esc_html_e('CHECKOUT', 'neebites'); ?></span>
                        <span class="step-arrow">&rarr;</span>
                    </li>
                    <li class="step-inactive">
                        <span><?php esc_html_e('ORDER COMPLETE', 'neebites'); ?></span>
                    </li>
                </ul>
            </div>
        </div>
        <?php
        return;
    }

    // Order Received Confirmation Header — Woodmart Step Navigation
    if (function_exists('is_order_received_page') && is_order_received_page()) {
        ?>
        <div class="neebites-checkout-steps-wrapper woodmart-steps-dark-banner">
            <div class="container text-center">
                <ul class="wd-checkout-steps">
                    <li class="step-runtitle">
                        <a href="<?php echo function_exists('wc_get_cart_url') ? esc_url(wc_get_cart_url()) : home_url('/cart/'); ?>">
                            <span><?php esc_html_e('SHOPPING CART', 'neebites'); ?></span>
                        </a>
                        <span class="step-arrow">&rarr;</span>
                    </li>
                    <li class="step-runtitle">
                        <a href="<?php echo function_exists('wc_get_checkout_url') ? esc_url(wc_get_checkout_url()) : home_url('/checkout/'); ?>">
                            <span><?php esc_html_e('CHECKOUT', 'neebites'); ?></span>
                        </a>
                        <span class="step-arrow">&rarr;</span>
                    </li>
                    <li class="step-active">
                        <span><?php esc_html_e('ORDER COMPLETE', 'neebites'); ?></span>
                    </li>
                </ul>
            </div>
        </div>
        <?php
        return;
    }
    ?>
    <section class="neebites-page-header">
        <div class="container">
            <div class="page-header-inner">
                <?php neebites_breadcrumbs(); ?>
                <h1 class="page-title"><?php echo wp_kses_post($title); ?></h1>
                <?php if (!empty($subtitle)) : ?>
                    <p class="page-subtitle"><?php echo wp_kses_post($subtitle); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
}

/**
 * Breadcrumbs helper
 */
function neebites_breadcrumbs() {
    if (is_front_page()) return;

    echo '<nav class="neebites-breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'neebites') . '">';
    echo '<ol class="breadcrumb-list">';
    echo '<li class="breadcrumb-item"><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'neebites') . '</a></li>';

    if (neebites_is_woocommerce_active() && (is_shop() || is_product_taxonomy() || is_product())) {
        $shop_id = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
        if ($shop_id > 0 && !is_shop()) {
            echo '<li class="breadcrumb-item"><a href="' . esc_url(get_permalink($shop_id)) . '">' . esc_html__('Shop', 'neebites') . '</a></li>';
        }
        if (is_product()) {
            $terms = function_exists('wc_get_product_terms') ? wc_get_product_terms(get_the_ID(), 'product_cat', ['orderby' => 'parent', 'order' => 'DESC']) : [];
            if (!empty($terms)) {
                $main_term = $terms[0];
                echo '<li class="breadcrumb-item"><a href="' . esc_url(get_term_link($main_term)) . '">' . esc_html($main_term->name) . '</a></li>';
            }
            echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_title()) . '</li>';
        } elseif (is_product_category() || is_product_tag()) {
            echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(single_term_title('', false)) . '</li>';
        } elseif (is_shop()) {
            echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html__('Shop', 'neebites') . '</li>';
        }
    } elseif (is_single()) {
        $cats = get_the_category();
        if ($cats) {
            echo '<li class="breadcrumb-item"><a href="' . esc_url(get_category_link($cats[0])) . '">' . esc_html($cats[0]->name) . '</a></li>';
        }
        echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_title()) . '</li>';
    } elseif (is_category() || is_tag()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(single_term_title('', false)) . '</li>';
    } elseif (is_page()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_title()) . '</li>';
    } elseif (is_search()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . sprintf(esc_html__('Search: "%s"', 'neebites'), esc_html(get_search_query())) . '</li>';
    } elseif (is_404()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html__('404 Not Found', 'neebites') . '</li>';
    }
    echo '</ol>';
    echo '</nav>';
}

/**
 * Reading time estimation helper
 */
function neebites_get_read_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $minutes = ceil($word_count / 200);
    return sprintf(esc_html(_n('%d min read', '%d min read', $minutes, 'neebites')), max(1, $minutes));
}

/**
 * Social Sharing Buttons
 */
function neebites_social_share() {
    $url = urlencode(get_permalink());
    $title = urlencode(get_the_title());
    ?>
    <div class="neebites-social-share">
        <span class="share-label"><?php esc_html_e('Share:', 'neebites'); ?></span>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook" class="share-btn share-facebook">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
        </a>
        <a href="https://twitter.com/intent/tweet?text=<?php echo $title; ?>&url=<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Twitter" class="share-btn share-twitter">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
        </a>
        <a href="https://pinterest.com/pin/create/button/?url=<?php echo $url; ?>&description=<?php echo $title; ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Pinterest" class="share-btn share-pinterest">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.08 3.15 9.42 7.63 11.1-.11-.94-.2-2.39.04-3.41.22-.92 1.4-5.96 1.4-5.96s-.36-.71-.36-1.77c0-1.66.96-2.9 2.16-2.9 1.02 0 1.51.77 1.51 1.69 0 1.03-.65 2.56-1 3.99-.28 1.19.6 2.16 1.77 2.16 2.12 0 3.76-2.24 3.76-5.47 0-2.86-2.06-4.86-5-4.86-3.4 0-5.39 2.55-5.39 5.18 0 1.03.4 2.13.89 2.73.1.12.11.23.08.35-.09.37-.29 1.19-.33 1.35-.05.22-.17.27-.4.16-1.5-.7-2.44-2.88-2.44-4.64 0-3.77 2.74-7.24 7.9-7.24 4.15 0 7.37 2.96 7.37 6.91 0 4.13-2.6 7.45-6.21 7.45-1.21 0-2.35-.63-2.74-1.38l-.75 2.85c-.27 1.04-1 2.35-1.49 3.14 1.13.35 2.34.54 3.59.54 6.63 0 12-5.37 12-12S18.63 0 12 0z"></path></svg>
        </a>
    </div>
    <?php
}

/**
 * Author Bio Box
 */
function neebites_author_box() {
    $author_id = get_the_author_meta('ID');
    $description = get_the_author_meta('description');
    if (empty($description)) return;
    ?>
    <div class="neebites-author-box">
        <div class="author-avatar"><?php echo get_avatar($author_id, 80); ?></div>
        <div class="author-info">
            <h4 class="author-name"><?php the_author(); ?></h4>
            <p class="author-bio"><?php echo esc_html($description); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Post Navigation
 */
function neebites_post_navigation() {
    the_post_navigation([
        'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous Article', 'neebites') . '</span> <span class="nav-title">%title</span>',
        'next_text' => '<span class="nav-subtitle">' . esc_html__('Next Article', 'neebites') . '</span> <span class="nav-title">%title</span>',
    ]);
}

/**
 * Related Posts
 */
function neebites_related_posts() {
    $cats = wp_get_post_categories(get_the_ID());
    if (empty($cats)) return;

    $related = new WP_Query([
        'category__in'   => $cats,
        'post__not_in'   => [get_the_ID()],
        'posts_per_page' => 3,
        'ignore_sticky_posts' => 1,
    ]);

    if ($related->have_posts()) : ?>
        <section class="neebites-related-posts">
            <h3 class="related-title"><?php esc_html_e('You Might Also Like', 'neebites'); ?></h3>
            <div class="posts-grid related-grid">
                <?php while ($related->have_posts()) : $related->the_post();
                    get_template_part('templates/parts/content', get_post_type());
                endwhile; ?>
            </div>
        </section>
        <?php wp_reset_postdata();
    endif;
}

/**
 * Social Icons Helper
 */
function neebites_get_social_icon($service) {
    $icons = [
        'instagram' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
        'facebook'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
        'twitter'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>',
        'pinterest' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M8 12c0 2.2 1.8 4 4 4s4-1.8 4-4-1.8-4-4-4-4 1.8-4 4z"></path></svg>',
        'youtube'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>',
    ];
    return isset($icons[$service]) ? $icons[$service] : '';
}

/**
 * Accessible Custom Nav Walker
 */
class Neebites_Nav_Walker extends Walker_Nav_Menu {
    public function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n{$indent}<ul class=\"sub-menu\" role=\"menu\" aria-hidden=\"true\">\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "{$indent}</ul>\n";
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $has_children = in_array('menu-item-has-children', $classes);
        if ($has_children) {
            $classes[] = 'has-dropdown';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= $indent . '<li' . $class_names . ' role="none">';

        $atts = [];
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target)     ? $item->target     : '';
        $atts['rel']    = !empty($item->xfn)        ? $item->xfn        : '';
        $atts['href']   = !empty($item->url)        ? $item->url        : '';
        $atts['role']   = 'menuitem';

        if ($has_children) {
            $atts['aria-haspopup'] = 'true';
            $atts['aria-expanded'] = 'false';
        }

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output = (is_object($args) && isset($args->before)) ? $args->before : '';
        $item_output .= '<a' . $attributes . '>';
        $item_output .= (is_object($args) && isset($args->link_before)) ? $args->link_before : '';
        $item_output .= $title;
        $item_output .= (is_object($args) && isset($args->link_after)) ? $args->link_after : '';

        if ($has_children) {
            $item_output .= '<span class="dropdown-caret" aria-hidden="true"><svg width="10" height="6" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m1 1 4 4 4-4"></path></svg></span>';
        }

        $item_output .= '</a>';
        $item_output .= (is_object($args) && isset($args->after)) ? $args->after : '';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}

/**
 * Get Confectionery Demo Products Dataset (8 Artisan Chocolate Coated Almond Items)
 */
function neebites_get_demo_products() {
    $theme_uri = get_template_directory_uri();
    return [
        101 => [
            'id'            => 101,
            'sku'           => 'CHOC-ALM-DARK-01',
            'flavor'        => 'dark',
            'name'          => 'Dark Chocolate Almond (70% Single-Origin Belgian)',
            'category'      => 'Dark Chocolate Almond',
            'price'         => '₹349.00',
            'raw_price'     => 349.00,
            'regular_price' => '₹449.00',
            'badge'         => 'Best Seller',
            'badge_class'   => 'badge-hot',
            'rating'        => '5.0',
            'reviews'       => 128,
            'image'         => $theme_uri . '/assets/images/choc-dark-almond.svg',
            'light'         => 'Dark (70%+)',
            'water'         => 'California Nonpareil',
            'pets'          => '100% Vegetarian & Pure Cocoa Butter',
            'difficulty'    => 'Dark Chocolate Almond',
            'desc'          => 'Whole slow-roasted California almonds drenched in silky 70% dark Belgian chocolate with deep cocoa intensity and balanced bittersweet finish.',
        ],
        102 => [
            'id'            => 102,
            'sku'           => 'CHOC-ALM-KIWI-02',
            'flavor'        => 'kiwi',
            'name'          => 'Kiwi Chocolate Almond (Signature Real Fruit Glaze)',
            'category'      => 'Kiwi Chocolate Almond',
            'price'         => '₹399.00',
            'raw_price'     => 399.00,
            'regular_price' => '₹499.00',
            'badge'         => 'Signature',
            'badge_class'   => 'badge-sale',
            'rating'        => '5.0',
            'reviews'       => 94,
            'image'         => $theme_uri . '/assets/images/choc-kiwi-almond.svg',
            'light'         => 'White & Kiwi',
            'water'         => 'California Nonpareil',
            'pets'          => '100% Vegetarian & Pure Cocoa Butter',
            'difficulty'    => 'Kiwi Chocolate Almond',
            'desc'          => 'Our crowning jewel. Crisp roasted almonds wrapped in luscious white chocolate infused with tangy natural kiwi fruit glaze for an exhilarating fruity burst.',
        ],
        103 => [
            'id'            => 103,
            'sku'           => 'CHOC-ALM-MILK-03',
            'flavor'        => 'milk',
            'name'          => 'Milk Chocolate Almond (Creamy 38% Swiss Velvet)',
            'category'      => 'Milk Chocolate Almond',
            'price'         => '₹329.00',
            'raw_price'     => 329.00,
            'regular_price' => '',
            'badge'         => 'Top Pick',
            'badge_class'   => 'badge-hot',
            'rating'        => '4.9',
            'reviews'       => 86,
            'image'         => $theme_uri . '/assets/images/choc-milk-almond.svg',
            'light'         => 'Milk (38%)',
            'water'         => 'California Nonpareil',
            'pets'          => '100% Vegetarian & Pure Cocoa Butter',
            'difficulty'    => 'Milk Chocolate Almond',
            'desc'          => 'Golden roasted almonds coated in indulgent 38% Swiss alpine milk chocolate. Rich dairy creaminess meets irresistible nutty crunch.',
        ],
        104 => [
            'id'            => 104,
            'sku'           => 'CHOC-ALM-WHT-04',
            'flavor'        => 'white',
            'name'          => 'White Chocolate Almond (Pure Bourbon Vanilla)',
            'category'      => 'White Chocolate Almond',
            'price'         => '₹349.00',
            'raw_price'     => 349.00,
            'regular_price' => '',
            'badge'         => 'Trending',
            'badge_class'   => 'badge-new',
            'rating'        => '4.9',
            'reviews'       => 62,
            'image'         => $theme_uri . '/assets/images/choc-white-almond.svg',
            'light'         => 'White & Kiwi',
            'water'         => 'California Nonpareil',
            'pets'          => '100% Vegetarian & Pure Cocoa Butter',
            'difficulty'    => 'White Chocolate Almond',
            'desc'          => 'Pure cocoa butter white chocolate laced with aromatic Bourbon vanilla beans, generously enrobing golden roasted Californian almonds.',
        ],
        105 => [
            'id'            => 105,
            'sku'           => 'CHOC-ALM-MAT-05',
            'flavor'        => 'other',
            'name'          => 'Matcha Green Tea Almond (Ceremonial Uji Japanese Matcha)',
            'category'      => 'Other Flavoured Almonds',
            'price'         => '₹419.00',
            'raw_price'     => 419.00,
            'regular_price' => '₹479.00',
            'badge'         => 'Artisan',
            'badge_class'   => 'badge-new',
            'rating'        => '4.9',
            'reviews'       => 45,
            'image'         => $theme_uri . '/assets/images/choc-matcha-almond.svg',
            'light'         => 'White & Kiwi',
            'water'         => 'California Nonpareil',
            'pets'          => '100% Vegetarian & Pure Cocoa Butter',
            'difficulty'    => 'Other Flavoured Almonds',
            'desc'          => 'Authentic stone-ground Uji matcha green tea blended into velvet white chocolate and enrobed over crunchy roasted almonds for an earthy, umami sweetness.',
        ],
        106 => [
            'id'            => 106,
            'sku'           => 'CHOC-ALM-CAR-06',
            'flavor'        => 'other',
            'name'          => 'Salted Caramel Almond (Fleur De Sel French Golden Caramel)',
            'category'      => 'Other Flavoured Almonds',
            'price'         => '₹369.00',
            'raw_price'     => 369.00,
            'regular_price' => '',
            'badge'         => 'Gourmet',
            'badge_class'   => 'badge-hot',
            'rating'        => '5.0',
            'reviews'       => 78,
            'image'         => $theme_uri . '/assets/images/choc-caramel-almond.svg',
            'light'         => 'Milk (38%)',
            'water'         => 'California Nonpareil',
            'pets'          => '100% Vegetarian & Pure Cocoa Butter',
            'difficulty'    => 'Other Flavoured Almonds',
            'desc'          => 'Buttery caramelized chocolate dusted with hand-harvested French Fleur de Sel sea salt over crisp roasted almonds. The pinnacle of sweet-savory harmony.',
        ],
        107 => [
            'id'            => 107,
            'sku'           => 'CHOC-ALM-CIN-07',
            'flavor'        => 'other',
            'name'          => 'Spiced Honey Cinnamon Almond (Ceylon Cinnamon Roasted)',
            'category'      => 'Other Flavoured Almonds',
            'price'         => '₹359.00',
            'raw_price'     => 359.00,
            'regular_price' => '₹399.00',
            'badge'         => 'Special Batch',
            'badge_class'   => 'badge-sale',
            'rating'        => '4.9',
            'reviews'       => 53,
            'image'         => $theme_uri . '/assets/images/choc-cinnamon-almond.svg',
            'light'         => 'Dark (70%+)',
            'water'         => 'California Nonpareil',
            'pets'          => '100% Vegetarian & Pure Cocoa Butter',
            'difficulty'    => 'Other Flavoured Almonds',
            'desc'          => 'Warm fragrant Ceylon cinnamon and organic wild honey glaze harmonized with rich milk chocolate over golden California almonds.',
        ],
        108 => [
            'id'            => 108,
            'sku'           => 'CHOC-ALM-RUB-08',
            'flavor'        => 'other',
            'name'          => 'Ruby Raspberry Almond (Natural Ruby Cocoa & Berry Dust)',
            'category'      => 'Other Flavoured Almonds',
            'price'         => '₹429.00',
            'raw_price'     => 429.00,
            'regular_price' => '',
            'badge'         => 'Limited Drop',
            'badge_class'   => 'badge-new',
            'rating'        => '5.0',
            'reviews'       => 67,
            'image'         => $theme_uri . '/assets/images/choc-ruby-almond.svg',
            'light'         => 'White & Kiwi',
            'water'         => 'California Nonpareil',
            'pets'          => '100% Vegetarian & Pure Cocoa Butter',
            'difficulty'    => 'Other Flavoured Almonds',
            'desc'          => 'Naturally pink ruby cocoa beans with berry fruitiness, finished with freeze-dried tart raspberry dust over crunchy roasted California almonds.',
        ],
    ];
}

/**
 * Resolve a bundled demo product to its live WooCommerce product ID.
 *
 * Demo cards use stable IDs so the theme still works without WooCommerce.
 * When WooCommerce is active, the SKU is the source of truth and actions
 * should target the real product instead of an in-memory demo item.
 *
 * @param array $demo_product Demo product data.
 * @return int Live product ID, or the stable demo ID when no live product exists.
 */
function neebites_resolve_demo_product_id($demo_product) {
    $demo_id = isset($demo_product['id']) ? absint($demo_product['id']) : 0;
    $sku     = isset($demo_product['sku']) ? sanitize_text_field($demo_product['sku']) : '';

    if ($sku && function_exists('wc_get_product_id_by_sku') && function_exists('wc_get_product')) {
        $live_id = absint(wc_get_product_id_by_sku($sku));
        if ($live_id && wc_get_product($live_id)) {
            return $live_id;
        }
    }

    return $demo_id;
}

/**
 * Derive lowercase filter tokens for product filtering.
 *
 * The single returned string powers the homepage trending tabs,
 * the shop sidebar chips & checkboxes, and the price slider.
 *
 * @return string space-separated tokens, consumed by main.js via [data-filter-tags]
 */
function neebites_derive_filter_tags($category = '', $badge = '', $difficulty = '', $light = '', $pets = '', $cat_slugs = '') {
    $haystack     = strtolower(trim($category . ' ' . $badge . ' ' . $difficulty . ' ' . $light . ' ' . $pets . ' ' . $cat_slugs));
    $tokens       = [];

    // Chocolate variant tokens
    if (strpos($haystack, 'dark') !== false || strpos($haystack, '70%') !== false) {
        $tokens[] = 'dark';
    }
    if (strpos($haystack, 'kiwi') !== false) {
        $tokens[] = 'kiwi';
    }
    if (strpos($haystack, 'milk') !== false || strpos($haystack, '38%') !== false) {
        $tokens[] = 'milk';
    }
    if (strpos($haystack, 'white') !== false || strpos($haystack, 'vanilla') !== false) {
        $tokens[] = 'white';
    }
    if (strpos($haystack, 'matcha') !== false || strpos($haystack, 'caramel') !== false || strpos($haystack, 'cinnamon') !== false || strpos($haystack, 'ruby') !== false || strpos($haystack, 'other') !== false) {
        $tokens[] = 'other';
    }

    // Purity / Dietary
    if (strpos($haystack, 'vegetarian') !== false || strpos($haystack, 'pure') !== false || strpos($haystack, 'pet') !== false || strpos($haystack, 'safe') !== false) {
        $tokens[] = 'petfriendly';
    }

    // Popularity / Badges
    if (strpos($haystack, 'best') !== false || strpos($haystack, 'seller') !== false || strpos($haystack, 'top') !== false || strpos($haystack, 'hot') !== false) {
        $tokens[] = 'bestseller';
    }
    if (strpos($haystack, 'rare') !== false || strpos($haystack, 'artisan') !== false || strpos($haystack, 'signature') !== false || strpos($haystack, 'limited') !== false) {
        $tokens[] = 'rare';
    }

    // Category slug tokens, used by the sidebar to count live results.
    $slug_source = trim((string) $cat_slugs) !== '' ? $cat_slugs : $category;
    foreach (preg_split('/[\s,]+/', $slug_source) as $slug) {
        $slug = sanitize_title($slug);
        if ($slug && strlen($slug) > 1) {
            $tokens[] = $slug;
        }
    }

    $tokens = array_values(array_unique(array_filter($tokens)));
    return implode(' ', $tokens);
}

/**
 * Category list + product counts for the shop sidebar filter.
 * Prefers real product_cat terms, falls back to the bundled demo dataset so
 * the demo shop still shows meaningful numbers.
 *
 * @return array[] Each entry: ['slug' => string, 'name' => string, 'count' => int]
 */
function neebites_shop_category_counts() {
    $out = [];

    if (taxonomy_exists('product_cat')) {
        $terms = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'orderby'    => 'term_group',
            'order'      => 'ASC',
        ]);

        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                if ('uncategorized' === $term->slug) {
                    continue;
                }
                $out[] = [
                    'slug'  => $term->slug,
                    'name'  => $term->name,
                    'count' => (int) $term->count,
                ];
            }
        }
    }

    if (empty($out)) {
        $buckets = [];
        foreach (neebites_get_demo_products() as $p) {
            $slug = sanitize_title($p['category']);
            if (!isset($buckets[$slug])) {
                $buckets[$slug] = ['slug' => $slug, 'name' => $p['category'], 'count' => 0];
            }
            $buckets[$slug]['count']++;
        }
        $out = array_values($buckets);
    }

    return $out;
}

/**
 * Total number of plants currently listed, for the sidebar "All" row and the
 * mobile filter button badge.
 */
function neebites_shop_total_product_count() {
    if (function_exists('wp_count_posts')) {
        $counts = wp_count_posts('product');
        if (!empty($counts->publish)) {
            return (int) $counts->publish;
        }
    }
    return count(neebites_get_demo_products());
}

/**
 * Build the Myntra-style rating chip markup (numeric rating + star + review count).
 */
function neebites_rating_chip_html($rating, $count) {
    $rating = (float) $rating;
    $count  = (int) $count;

    // In Myntra, every item displays an authoritative rating pill
    if ($count <= 0 || $rating <= 0) {
        $rating = 4.8;
        $count  = 28;
    }

    return sprintf(
        '<span class="rating-chip" role="img" aria-label="%s"><strong>%s</strong> <span class="rc-star" aria-hidden="true">&#9733;</span><span class="rc-sep" aria-hidden="true">|</span><span class="rc-count">%s</span></span>',
        esc_attr(sprintf(__('Rated %1$s out of 5 from %2$s reviews', 'neebites'), number_format($rating, 1), $count)),
        esc_html(number_format($rating, 1)),
        esc_html($count)
    );
}

/**
 * Format verbose care descriptions into clean, concise 2-word micro chips (e.g. "☀️ Bright Light", "💧 Weekly").
/**
 * Format Confection Profile Pills for Product Cards (e.g. Cocoa Butter, Slow-Roasted Almond)
 */
function neebites_format_care_pill($text, $type = 'light') {
    $t = strtolower(trim((string)$text));
    if (empty($t) || in_array($t, ['bright light', 'weekly', 'medium light', 'direct sun', 'low light', 'water', 'evenly moist'])) {
        return ($type === 'light') ? '🍫 Pure Cocoa Butter' : '🥜 Slow-Roasted Almond';
    }
    
    if (strpos($t, 'dark') !== false || strpos($t, '70%') !== false) return '🍫 70% Dark Cocoa';
    if (strpos($t, 'milk') !== false || strpos($t, 'velvet') !== false) return '🥛 Velvet Milk';
    if (strpos($t, 'white') !== false || strpos($t, 'vanilla') !== false) return '🤍 Bourbon Vanilla';
    if (strpos($t, 'caramel') !== false) return '🍯 Salted Caramel';
    if (strpos($t, 'matcha') !== false) return '🍵 Ceremonial Uji';
    if (strpos($t, 'cinnamon') !== false || strpos($t, 'honey') !== false) return '🍯 Ceylon Cinnamon';
    if (strpos($t, 'kiwi') !== false) return '🥝 Real Kiwi Glaze';
    if (strpos($t, 'ruby') !== false) return '🍓 Ruby Cocoa Dust';

    return ($type === 'light') ? '🍫 Pure Cocoa Butter' : '🥜 Slow-Roasted';
}

/**
 * Percentage discount label for the price row (e.g. "-24% OFF" -> "24").
 */
function neebites_discount_percent($regular, $current) {
    $regular = (float) $regular;
    $current = (float) $current;
    if ($regular <= 0 || $current <= 0 || $regular <= $current) {
        return 0;
    }
    return (int) round((($regular - $current) / $regular) * 100);
}

/**
 * Money string to float ("$45.00" -> 45.0). Used for demo product datasets.
 */
function neebites_price_to_float($value) {
    return (float) preg_replace('/[^0-9.]/', '', (string) $value);
}

/**
 * Botanical Demo Products Grid (plnts.com / Woodmart Style)
 * Renders rich botanical cards with SVG illustrations, wishlist, quick view, care tags, and prices.
 */
function neebites_render_demo_products_grid($atts = []) {
    $demo_products = neebites_get_demo_products();
    $limit = !empty($atts['limit']) ? intval($atts['limit']) : count($demo_products);
    $products = array_slice($demo_products, 0, $limit, true);
    $cols = !empty($atts['columns']) ? intval($atts['columns']) : 4;

    ob_start();
    ?>
    <div class="woocommerce neebites-demo-products-wrapper">
        <ul class="products columns-<?php echo esc_attr($cols); ?> neebites-products-grid">
            <?php foreach ($products as $p) :
                $resolved_id = neebites_resolve_demo_product_id($p);
                $live_product = ($resolved_id && $resolved_id !== absint($p['id']) && function_exists('wc_get_product'))
                    ? wc_get_product($resolved_id)
                    : false;
                $prod_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
                if ($live_product) {
                    $prod_url = get_permalink($resolved_id);
                }
                $filter_tags = neebites_derive_filter_tags(
                    isset($p['category']) ? $p['category'] : '',
                    isset($p['badge']) ? $p['badge'] : '',
                    isset($p['difficulty']) ? $p['difficulty'] : '',
                    isset($p['light']) ? $p['light'] : '',
                    isset($p['pets']) ? $p['pets'] : ''
                );
                $discount_pct = !empty($p['regular_price'])
                    ? neebites_discount_percent(neebites_price_to_float($p['regular_price']), $p['raw_price'])
                    : 0;
                $flavor = isset($p['flavor']) ? $p['flavor'] : 'other';
                $extra_classes = 'flavor-' . $flavor;
                if ($flavor === 'milk' || $flavor === 'white') {
                    $extra_classes .= ' flavor-milk-white';
                }
                if ($flavor === 'other') {
                    $extra_classes .= ' flavor-gourmet';
                }
            ?>
                <li class="product neebites-product-card post-<?php echo esc_attr($resolved_id); ?> <?php echo esc_attr($extra_classes); ?>"
                    data-product-id="<?php echo esc_attr($resolved_id); ?>"
                    data-demo-id="<?php echo esc_attr($p['id']); ?>"
                    data-product-sku="<?php echo esc_attr($p['sku']); ?>"
                    data-name="<?php echo esc_attr($p['name']); ?>"
                    data-price="<?php echo esc_attr($p['price']); ?>"
                    data-raw-price="<?php echo esc_attr($p['raw_price']); ?>"
                    data-image="<?php echo esc_url($p['image']); ?>"
                    data-category="<?php echo esc_attr($p['category']); ?>"
                    data-flavor="<?php echo esc_attr($flavor); ?>"
                    data-filter-tags="<?php echo esc_attr($filter_tags); ?>"
                    data-pets="<?php echo esc_attr($p['pets']); ?>"
                    data-light="<?php echo esc_attr($p['light']); ?>"
                    data-water="<?php echo esc_attr($p['water']); ?>"
                    data-difficulty="<?php echo esc_attr($p['difficulty']); ?>">
                    <div class="product-card-media">
                        <div class="product-badges">
                            <?php if (!empty($p['badge'])) : ?>
                                <span class="badge <?php echo esc_attr($p['badge_class']); ?>"><?php echo esc_html($p['badge']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-card-actions">
                            <button type="button" class="btn-card-action btn-wishlist" data-product-id="<?php echo esc_attr($resolved_id); ?>" aria-label="<?php esc_attr_e('Add to Wishlist', 'neebites'); ?>" title="<?php esc_attr_e('Add to Wishlist', 'neebites'); ?>">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                            </button>
                            <button type="button" class="btn-card-action btn-quickview" data-product-id="<?php echo esc_attr($resolved_id); ?>" aria-label="<?php esc_attr_e('Quick View', 'neebites'); ?>" title="<?php esc_attr_e('Quick View', 'neebites'); ?>">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                            </button>
                        </div>
                        <a href="<?php echo esc_url($prod_url); ?>" class="product-card-image-link" aria-label="<?php echo esc_attr($p['name']); ?>">
                            <img src="<?php echo esc_url($p['image']); ?>" alt="<?php echo esc_attr($p['name']); ?>" class="botanical-svg-img" loading="lazy" width="300" height="300" />
                        </a>
                        <div class="product-card-rating">
                            <?php echo neebites_rating_chip_html($p['rating'], $p['reviews']); ?>
                        </div>
                        <div class="product-quick-add">
                            <?php if ($live_product && $live_product->is_type('simple') && $live_product->is_in_stock()) : ?>
                                <a href="<?php echo esc_url($live_product->add_to_cart_url()); ?>"
                                   data-quantity="1"
                                   class="btn-quick-add btn-card-add-cart add_to_cart_button ajax_add_to_cart"
                                   data-product_id="<?php echo esc_attr($resolved_id); ?>"
                                   data-product_sku="<?php echo esc_attr($p['sku']); ?>"
                                   aria-label="<?php echo esc_attr($live_product->add_to_cart_description()); ?>">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                    <span class="btn-text"><?php esc_html_e('Add to Bag', 'neebites'); ?></span>
                                </a>
                            <?php elseif ($live_product) : ?>
                                <a href="<?php echo esc_url($prod_url); ?>" class="btn-quick-add btn-card-view-item" aria-label="<?php esc_attr_e('View Confection', 'neebites'); ?>">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"></path><circle cx="12" cy="12" r="2.5"></circle></svg>
                                    <span class="btn-text"><?php esc_html_e('View Confection', 'neebites'); ?></span>
                                </a>
                            <?php else : ?>
                            <button type="button" class="btn-quick-add demo-add-to-basket" 
                                data-product-id="<?php echo esc_attr($resolved_id); ?>"
                                data-demo-id="<?php echo esc_attr($p['id']); ?>"
                                data-name="<?php echo esc_attr($p['name']); ?>" 
                                data-price="<?php echo esc_attr($p['price']); ?>"
                                data-raw-price="<?php echo esc_attr($p['raw_price']); ?>"
                                data-image="<?php echo esc_url($p['image']); ?>"
                                aria-label="<?php esc_attr_e('Add to Basket', 'neebites'); ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                <span class="btn-text"><?php esc_html_e('Add to Bag', 'neebites'); ?></span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="product-card-info">
                        <span class="product-card-category"><?php echo esc_html($p['category']); ?></span>
                        <div class="product-card-price">
                            <span class="price-current"><?php echo esc_html($p['price']); ?></span>
                            <?php if ($discount_pct > 0) : ?>
                                <span class="price-off">(<?php echo esc_html($discount_pct); ?>% OFF)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Register the [neebites_demo_plants] shortcode used by front-page.php.
 * Without this the trending section printed the literal shortcode text.
 */
add_shortcode('neebites_demo_plants', function ($atts) {
    return neebites_render_demo_products_grid(is_array($atts) ? $atts : []);
});
add_shortcode('neebites_demo_chocolates', function ($atts) {
    return neebites_render_demo_products_grid(is_array($atts) ? $atts : []);
});

/**
 * Intercept [products] shortcode output: if empty or no items rendered, return demo grid
 */
add_filter('do_shortcode_tag', function($output, $tag, $attr, $m) {
    if ($tag === 'products') {
        if (empty(trim((string)$output)) || strpos($output, '<li') === false) {
            return neebites_render_demo_products_grid(is_array($attr) ? $attr : []);
        }
    }
    return $output;
}, 10, 4);

/**
 * Fallback for WooCommerce Shop Archive when catalog is empty
 */
add_action('woocommerce_no_products_found', function() {
    if (function_exists('neebites_shop_toolbar_open')) {
        neebites_shop_toolbar_open();
        neebites_shop_toolbar_close();
    }
    echo '<div class="neebites-no-products-fallback" style="margin:20px 0 40px;">';
    // Query published confectionery products
    $args = [
        'post_type'      => 'product',
        'posts_per_page' => 8,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    ];
    $fallback_query = new WP_Query($args);
    if ($fallback_query->have_posts()) {
        echo '<ul class="products columns-4 neebites-products-grid">';
        while ($fallback_query->have_posts()) {
            $fallback_query->the_post();
            if (function_exists('wc_get_template_part')) {
                wc_get_template_part('content', 'product');
            } else {
                get_template_part('woocommerce/content', 'product');
            }
        }
        echo '</ul>';
        wp_reset_postdata();
    }
    echo '</div>';
}, 20);

/**
 * Map WooCommerce Product Images to Botanical SVGs if no featured image is set
 */
add_filter('woocommerce_product_get_image', function($image, $product, $size, $attr, $placeholder) {
    if (!$product) return $image;
    
    $thumb_url = has_post_thumbnail($product->get_id()) ? get_the_post_thumbnail_url($product->get_id(), $size) : '';
    if (!$thumb_url || strpos($thumb_url, '.svg') !== false || strpos($thumb_url, 'placeholder') !== false) {
        $img_url = function_exists('neebites_get_product_fallback_image') 
            ? neebites_get_product_fallback_image($product)
            : get_template_directory_uri() . '/assets/images/products/choc-dark-almond.jpg';
        return sprintf(
            '<img src="%s" class="attachment-%s size-%s wp-post-image" alt="%s" loading="lazy" width="400" height="400" style="object-fit:cover;border-radius:12px;" />',
            esc_url($img_url),
            esc_attr($size),
            esc_attr($size),
            esc_attr($product->get_name())
        );
    }
    return $image;
}, 10, 5);

add_filter('woocommerce_single_product_image_thumbnail_html', function($html, $post_thumbnail_id) {
    global $product;
    if ($product) {
        $thumb_url = has_post_thumbnail($product->get_id()) ? get_the_post_thumbnail_url($product->get_id(), 'large') : '';
        if (!$thumb_url || strpos($thumb_url, '.svg') !== false || strpos($thumb_url, 'placeholder') !== false) {
            $img_url = function_exists('neebites_get_product_fallback_image') 
                ? neebites_get_product_fallback_image($product)
                : get_template_directory_uri() . '/assets/images/products/choc-dark-almond.jpg';
            return sprintf(
                '<div class="woocommerce-product-gallery__image"><img src="%s" alt="%s" class="wp-post-image" style="max-height:580px;width:100%%;object-fit:cover;margin:0 auto;display:block;" /></div>',
                esc_url($img_url),
                esc_attr($product->get_name())
            );
        }
    }
    return $html;
}, 10, 2);

add_filter('woocommerce_placeholder_img_src', function($src) {
    return get_template_directory_uri() . '/assets/images/products/choc-dark-almond.jpg';
});

/**
 * Fallback handlers for WooCommerce shortcodes & Interactive Modals when WooCommerce is inactive
 */
add_action('init', function() {
    if (!class_exists('WooCommerce')) {
        if (!shortcode_exists('products')) {
            add_shortcode('products', 'neebites_render_demo_products_grid');
        }
        if (!shortcode_exists('product_categories')) {
            add_shortcode('product_categories', function($atts = []) {
                return '<div class="neebites-demo-categories-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;margin:24px 0">'
                     . '<div style="background:#FAF6F0;border-radius:12px;padding:20px;text-align:center;border:1px solid #EAE0D5"><span style="font-size:32px">🍫</span><h4 style="margin:8px 0 4px;color:#3D2314">Chocolates & Confectionery</h4><p style="font-size:13px;color:#6B4226;margin:0">Main Category</p></div>'
                     . '<div style="background:#FAF6F0;border-radius:12px;padding:20px;text-align:center;border:1px solid #EAE0D5"><span style="font-size:32px">🥜</span><h4 style="margin:8px 0 4px;color:#3D2314">Chocolate Coated Nuts</h4><p style="font-size:13px;color:#6B4226;margin:0">Sub Category</p></div>'
                     . '<div style="background:#FAF6F0;border-radius:12px;padding:20px;text-align:center;border:1px solid #EAE0D5"><span style="font-size:32px">🌰</span><h4 style="margin:8px 0 4px;color:#3D2314">Chocolate Coated Almonds</h4><p style="font-size:13px;color:#6B4226;margin:0">Product Type</p></div>'
                     . '<div style="background:#FAF6F0;border-radius:12px;padding:20px;text-align:center;border:1px solid #EAE0D5"><span style="font-size:32px">🥝</span><h4 style="margin:8px 0 4px;color:#3D2314">Flavour Variants</h4><p style="font-size:13px;color:#6B4226;margin:0">5 Signature Flavours</p></div>'
                     . '</div>';
            });
        }
    }
});

/**
 * Universal Off-Canvas Cart Drawer, Quick View Modal, and Toast Container
 * Injected in wp_footer so drawers and dialogs are always present in the DOM
 */
add_action('wp_footer', function() {
    // Only render fallback drawer if WooCommerce is not active (otherwise cart-checkout.php renders it)
    if (!neebites_is_woocommerce_active()) :
    ?>
    <aside id="neebites-cart-drawer" class="neebites-cart-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Shopping Cart Drawer', 'neebites'); ?>" hidden>
        <div class="cart-drawer-header">
            <h3 class="drawer-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <?php esc_html_e('Your Gourmet Basket', 'neebites'); ?>
            </h3>
            <button type="button" class="btn-drawer-close" aria-label="<?php esc_attr_e('Close Cart Drawer', 'neebites'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="cart-drawer-body">
            <div class="mini-cart-drawer-content">
                <!-- Free Shipping Progress Bar -->
                <div class="neebites-free-shipping-bar" id="neebites-shipping-bar">
                    <div class="shipping-goal-text" id="shipping-goal-text">
                        <span class="icon-leaf">🍫</span>
                        <span class="shipping-msg">Add <strong>₹500</strong> more to unlock FREE Cold-Pack shipping!</span>
                    </div>
                    <div class="shipping-progress-track">
                        <div class="shipping-progress-fill" id="shipping-progress-fill" style="width: 0%;"></div>
                    </div>
                </div>

                <!-- Item List Container -->
                <div class="mini-cart-items-list" id="demo-cart-items">
                    <div class="mini-cart-empty" style="text-align:center;padding:48px 20px;">
                        <span style="font-size:48px;display:block;margin-bottom:12px;">🍫</span>
                        <h4 style="margin:0 0 8px;color:#3D2314;font-size:18px"><?php esc_html_e('Your confectionery basket is empty', 'neebites'); ?></h4>
                        <p style="color:#5C4434;font-size:14px;margin:0 0 20px"><?php esc_html_e('Explore our artisan chocolate coated almonds and discover signature flavours.', 'neebites'); ?></p>
                        <a href="<?php echo esc_url(home_url('/shop')); ?>" class="btn btn-primary" style="display:inline-block;padding:10px 24px;border-radius:30px;background:#3D2314;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600"><?php esc_html_e('Shop All Chocolates &rarr;', 'neebites'); ?></a>
                    </div>
                </div>

                <!-- Footer Summary -->
                <div class="mini-cart-footer" id="demo-cart-footer" style="display:none;">
                    <div class="mini-cart-subtotal">
                        <span><?php esc_html_e('Subtotal:', 'neebites'); ?></span>
                        <strong class="subtotal-amount" id="demo-subtotal-amount">₹0.00</strong>
                    </div>
                    <p class="mini-cart-tax-note"><?php esc_html_e('Taxes and direct temperature-controlled shipping calculated at checkout.', 'neebites'); ?></p>
                    <div class="mini-cart-buttons">
                        <a href="<?php echo esc_url(home_url('/shop')); ?>" class="btn-view-cart"><?php esc_html_e('Continue Indulging', 'neebites'); ?></a>
                        <button type="button" class="btn-checkout" onclick="alert('Proceeding directly to WooCommerce secure checkout!')"><?php esc_html_e('Checkout Now &rarr;', 'neebites'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    <div id="neebites-drawer-backdrop" class="neebites-drawer-backdrop" hidden></div>
    <?php endif; ?>

    <!-- Toast Notification Container (always rendered) -->
    <div id="neebites-toast-container" class="neebites-toast-container" aria-live="polite" aria-atomic="true"></div>
    <?php
}, 50);


