<?php
/**
 * Neebites Theme Functions and Definitions
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

define('NEEBITES_VERSION', '2.7.3');
define('NEEBITES_THEME_DIR', get_template_directory());
define('NEEBITES_THEME_URI', get_template_directory_uri());
define('NEEBITES_ASSETS_URI', NEEBITES_THEME_URI . '/assets');
define('NEEBITES_INC_DIR', NEEBITES_THEME_DIR . '/inc');

/**
 * Check if WooCommerce is active
 */
if (!function_exists('neebites_is_woocommerce_active')) {
    function neebites_is_woocommerce_active() {
        return class_exists('WooCommerce');
    }
}

/**
 * Setup Theme Features
 */
function neebites_setup() {
    // Make theme available for translation
    load_theme_textdomain('neebites', NEEBITES_THEME_DIR . '/languages');

    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Title tag management
    add_theme_support('title-tag');

    // Post thumbnails
    add_theme_support('post-thumbnails');

    // HTML5 semantic markup
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // WooCommerce support
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
    // Custom Myntra gallery handles zoom, lightbox, and touch carousel with 100% visibility (no opacity 0)

    // Gutenberg Block Supports
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');

    // Color Palette
    add_theme_support('editor-color-palette', neebites_get_color_palette());

    // Navigation Menus
    register_nav_menus([
        'primary' => esc_html__('Primary Menu', 'neebites'),
        'footer'  => esc_html__('Footer Menu', 'neebites'),
        'account' => esc_html__('Account Menu', 'neebites'),
    ]);

    // Custom Image Sizes
    add_image_size('neebites-hero', 1920, 800, true);
    add_image_size('neebites-product', 450, 450, true);
    add_image_size('neebites-product-gallery', 140, 140, true);
    add_image_size('neebites-blog', 800, 480, true);
    add_image_size('neebites-category', 600, 400, true);

    // Custom Logo
    add_theme_support('custom-logo', [
        'height'               => 70,
        'width'                => 240,
        'flex-height'          => true,
        'flex-width'           => true,
        'unlink-homepage-logo' => true,
    ]);
}
add_action('after_setup_theme', 'neebites_setup');

/**
 * Artisan Chocolatier Color Palette for Gutenberg Editor
 */
function neebites_get_color_palette() {
    return [
        ['name' => 'Artisan Dark Chocolate', 'slug' => 'primary',       'color' => '#3D2314'],
        ['name' => 'Milk Chocolate Praline', 'slug' => 'secondary',     'color' => '#6B4226'],
        ['name' => 'Single-Origin Noir',     'slug' => 'primary-dark',  'color' => '#231205'],
        ['name' => 'Caramel Amber Gold',     'slug' => 'accent',        'color' => '#C59B27'],
        ['name' => 'Ruby Ganache',           'slug' => 'sale',          'color' => '#C0392B'],
        ['name' => 'Cocoa Espresso',         'slug' => 'text-dark',     'color' => '#241408'],
        ['name' => 'Warm Vanilla Cream',     'slug' => 'bg-light',      'color' => '#FAF6F0'],
        ['name' => 'Pure Cream White',       'slug' => 'white',         'color' => '#FFFFFF'],
        ['name' => 'Roasted Almond Wafer',   'slug' => 'sand',          'color' => '#F4ECE1'],
        ['name' => 'Biscuit Border',         'slug' => 'border',        'color' => '#EAE0D5'],
    ];
}

/**
 * Enqueue Theme Scripts and Styles
 */
function neebites_enqueue_assets() {
    // Google Fonts (Plus Jakarta Sans & Newsreader)
    wp_enqueue_style(
        'neebites-fonts',
        'https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400..600;1,6..72,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
        [],
        null
    );

    // Main Theme CSS with query string in URL to ensure cache bypass
    wp_enqueue_style('neebites-main', NEEBITES_ASSETS_URI . '/css/main.css?v=' . NEEBITES_VERSION, ['neebites-fonts'], null);

    // Critical resets for mobile dock & footer menu to bypass CDN / caching glitches
    $critical_resets = "
        .footer-menu, ul.footer-menu {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 20px !important;
            list-style: none !important;
            list-style-type: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .footer-menu li, ul.footer-menu li {
            list-style: none !important;
            list-style-type: none !important;
            margin: 0 !important;
            padding: 0 !important;
            display: inline-flex !important;
        }
        .footer-menu li::before, .footer-menu li::marker,
        ul.footer-menu li::before, ul.footer-menu li::marker {
            display: none !important;
            content: none !important;
        }
        .footer-menu a, ul.footer-menu a {
            color: #5C4434 !important;
            font-size: 13px !important;
            text-decoration: none !important;
            font-weight: 500 !important;
        }
        .footer-menu a:hover, ul.footer-menu a:hover {
            color: #3D2314 !important;
        }
        .neebites-mobile-dock {
            display: none !important;
        }
        @media (min-width: 769px) {
            .neebites-mobile-dock {
                display: none !important;
                visibility: hidden !important;
                pointer-events: none !important;
                position: absolute !important;
                left: -9999px !important;
                top: -9999px !important;
                width: 0 !important;
                height: 0 !important;
                opacity: 0 !important;
            }
        }
        @media (max-width: 768px) {
            .neebites-mobile-dock {
                display: flex !important;
                visibility: visible !important;
                pointer-events: auto !important;
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                height: 60px !important;
                z-index: 9000 !important;
                transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease, visibility 0.25s ease !important;
            }
            body.mobile-menu-open .neebites-mobile-dock,
            body.cart-drawer-open .neebites-mobile-dock,
            body.wishlist-drawer-open .neebites-mobile-dock,
            body.shop-filters-open .neebites-mobile-dock,
            body.search-modal-open .neebites-mobile-dock,
            body.search-open .neebites-mobile-dock,
            body.modal-open .neebites-mobile-dock {
                transform: translateY(120%) !important;
                opacity: 0 !important;
                visibility: hidden !important;
                pointer-events: none !important;
            }
            body.mobile-menu-open .neebites-sticky-add-to-cart,
            body.cart-drawer-open .neebites-sticky-add-to-cart,
            body.wishlist-drawer-open .neebites-sticky-add-to-cart,
            body.shop-filters-open .neebites-sticky-add-to-cart,
            body.search-modal-open .neebites-sticky-add-to-cart,
            body.search-open .neebites-sticky-add-to-cart,
            body.modal-open .neebites-sticky-add-to-cart {
                transform: translateY(200%) !important;
                opacity: 0 !important;
                visibility: hidden !important;
                pointer-events: none !important;
            }
            .mobile-menu-panel,
            .neebites-cart-drawer,
            .neebites-wishlist-drawer,
            .shop-sidebar {
                z-index: 999999 !important;
            }
            .mobile-menu-overlay,
            .neebites-drawer-backdrop,
            .shop-filter-backdrop {
                z-index: 999998 !important;
            }
            .neebites-sticky-add-to-cart {
                bottom: 0 !important;
                z-index: 9500 !important;
                border-top: 1px solid #EAE0D5 !important;
                margin: 0 !important;
            }
            body {
                padding-bottom: 72px !important;
            }
            body.single-product {
                padding-bottom: 120px !important;
            }
        }
        .footer-widgets {
            margin-top: 0 !important;
            border-top: 1px solid #EAE0D5 !important;
            background: #FAF6F0 !important;
        }
        /* Top Announcement 1-Line Critical Reset */
        .neebites-announcement-bar {
            background: linear-gradient(90deg, #1C0C04 0%, #2A1307 50%, #1C0C04 100%) !important;
            height: 38px !important;
            line-height: 38px !important;
            padding: 0 !important;
            overflow: hidden !important;
            border-bottom: 1px solid rgba(197, 155, 39, 0.25) !important;
        }
        .announcement-container {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            height: 100% !important;
            padding: 0 20px !important;
            white-space: nowrap !important;
        }
        .announcement-slider-wrap {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex: 1 !important;
            min-width: 0 !important;
            height: 100% !important;
        }
        .announcement-slider-viewport {
            position: relative !important;
            flex: 1 !important;
            min-width: 0 !important;
            height: 100% !important;
            overflow: hidden !important;
            white-space: nowrap !important;
        }
        .announcement-slide {
            position: absolute !important;
            inset: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(100%) !important;
            transition: transform 0.44s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s ease, visibility 0.44s ease !important;
            pointer-events: none !important;
            white-space: nowrap !important;
        }
        .announcement-slide.is-active {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            pointer-events: auto !important;
            z-index: 2 !important;
        }
        .announcement-slide.is-exiting-up {
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(-100%) !important;
            pointer-events: none !important;
        }
        .announcement-slide.is-exiting-down {
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(100%) !important;
            pointer-events: none !important;
        }
        .announcement-slide.is-entering-down {
            transform: translateY(-100%) !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }
        .announcement-link {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            color: #FAF6F0 !important;
            white-space: nowrap !important;
            max-width: 100% !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            text-decoration: none !important;
        }
        .announcement-text {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: 100% !important;
        }
        @media (max-width: 992px) {
            .announcement-side { display: none !important; }
            .announcement-container { justify-content: center !important; padding: 0 10px !important; }
        }
        @media (max-width: 540px) {
            .neebites-announcement-bar { height: 34px !important; line-height: 34px !important; }
            .announcement-nav-btn { width: 18px !important; height: 18px !important; }
            .announcement-nav-btn svg { width: 10px !important; height: 10px !important; }
            .announcement-link { font-size: 11px !important; gap: 5px !important; }
            .announcement-pill { font-size: 8.5px !important; padding: 1px 5px !important; }
        }
        @media (max-width: 380px) {
            .announcement-nav-btn { display: none !important; }
        }
        /* Search Overlay Critical Reset */
        .header-search-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: rgba(35, 18, 5, 0.8) !important;
            -webkit-backdrop-filter: blur(14px) !important;
            backdrop-filter: blur(14px) !important;
            z-index: 999999 !important;
            display: flex !important;
            justify-content: center !important;
            align-items: flex-start !important;
            padding: 70px 20px 30px !important;
            overflow-y: auto !important;
        }
        .header-search-overlay[hidden] {
            display: none !important;
        }
        body.search-modal-open {
            overflow: hidden !important;
        }
        .header-search-inner {
            width: 100% !important;
            max-width: 680px !important;
            background: #ffffff !important;
            border-radius: 26px !important;
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.4), 0 4px 16px rgba(61, 35, 20, 0.15) !important;
            border: 1px solid #EAE0D5 !important;
            overflow: hidden !important;
            margin: 0 auto !important;
        }
        .search-input-group {
            background: #ffffff !important;
            border: none !important;
            border-bottom: 1px solid #EAE0D5 !important;
            display: flex !important;
            align-items: center !important;
            padding: 16px 22px !important;
            gap: 14px !important;
        }
        .search-input-group input#header-search-input {
            flex: 1 !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
            font-size: 17px !important;
            font-weight: 500 !important;
            color: #231205 !important;
            padding: 0 !important;
            -webkit-appearance: none !important;
        }
        /* VIP Cocoa Club Banner Critical Resets */
        .sanctuary-newsletter-section {
            padding: 30px 0 90px !important;
            background: #FAF6F0 !important;
        }
        .newsletter-card {
            background: linear-gradient(145deg, #231205 0%, #3D2314 55%, #1A0D04 100%) !important;
            border-radius: 32px !important;
            padding: 68px 44px !important;
            text-align: center !important;
            color: #ffffff !important;
            box-shadow: 0 24px 60px rgba(35, 18, 5, 0.35) !important;
            border: 1px solid rgba(197, 155, 39, 0.25) !important;
            position: relative !important;
            overflow: hidden !important;
        }
        .newsletter-title {
            font-family: var(--font-heading, 'Newsreader', serif) !important;
            font-size: clamp(26px, 4vw, 42px) !important;
            color: #ffffff !important;
            margin: 0 0 16px !important;
            font-weight: 600 !important;
            line-height: 1.25 !important;
        }
        .title-highlight {
            color: #C59B27 !important;
            font-style: italic !important;
        }
        .newsletter-subtitle {
            color: rgba(255, 255, 255, 0.9) !important;
            font-size: 16px !important;
        }
        @media (max-width: 680px) {
            .newsletter-card {
                padding: 40px 18px !important;
                border-radius: 22px !important;
            }
            .newsletter-input-group {
                flex-direction: column !important;
                border-radius: 18px !important;
                padding: 14px !important;
            }
            .newsletter-input {
                width: 100% !important;
                text-align: center !important;
                font-size: 16px !important;
            }
            .btn-newsletter {
                width: 100% !important;
                border-radius: 12px !important;
            }
        }
        /* Admin Bar Off-Canvas Drawer Alignment */
        body.admin-bar .neebites-cart-drawer,
        body.admin-bar .neebites-wishlist-drawer {
            top: 32px !important;
            height: calc(100vh - 32px) !important;
        }
        @media (max-width: 782px) {
            body.admin-bar .neebites-cart-drawer,
            body.admin-bar .neebites-wishlist-drawer {
                top: 46px !important;
                height: calc(100vh - 46px) !important;
            }
        }
        .neebites-wishlist-drawer,
        .neebites-cart-drawer {
            width: 440px !important;
            max-width: 100vw !important;
        }
        .wishlist-item-title,
        .mini-cart-item-title {
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            white-space: normal !important;
        }
        .mini-cart-item-thumb {
            width: 74px !important;
            height: 74px !important;
            border-radius: 12px !important;
            flex-shrink: 0 !important;
            overflow: hidden !important;
        }
        .mini-cart-item-thumb img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
        .free-shipping-bar {
            display: block !important;
        }
        /* Shop Layout, Sticky Left Sidebar & Empty State Airtight Resets */
        .neebites-no-results[hidden] {
            display: none !important;
        }
        .neebites-no-results {
            display: none !important;
        }
        .neebites-no-results:not([hidden]) {
            display: flex !important;
        }
        @media (min-width: 1025px) {
            .shop-inner-layout {
                display: flex !important;
                gap: 32px !important;
                align-items: flex-start !important;
            }
            .shop-main-content {
                flex: 1 !important;
                min-width: 0 !important;
            }
            .shop-sidebar {
                width: 250px !important;
                flex-shrink: 0 !important;
                position: -webkit-sticky !important;
                position: sticky !important;
                top: 84px !important;
                align-self: flex-start !important;
                z-index: 20 !important;
                border-right: 1px solid #edebef !important;
                padding-right: 20px !important;
                background: transparent !important;
                transform: none !important;
                visibility: visible !important;
            }
            .sidebar-pos-left .shop-inner-layout {
                flex-direction: row-reverse !important;
            }
            .shop-filter-mobile-header,
            .shop-filter-drawer-footer,
            .btn-open-filters,
            .shop-filter-backdrop {
                display: none !important;
            }
            .shop-toolbar {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-end !important;
                padding-bottom: 14px !important;
                margin-bottom: 22px !important;
                border-bottom: 1px solid #edebef !important;
            }
            .shop-toolbar-right {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-end !important;
                gap: 12px !important;
                width: 100% !important;
            }
            .sidebar-pos-left ul.products,
            .sidebar-pos-right ul.products,
            .sidebar-pos-left .columns-4 ul.products,
            .sidebar-pos-right .columns-4 ul.products,
            .sidebar-pos-left ul.products.columns-4,
            .sidebar-pos-right ul.products.columns-4,
            ul.products,
            ul.products.columns-4,
            .columns-4 ul.products,
            .woocommerce.columns-4 ul.products,
            .woocommerce ul.products.columns-4 {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                gap: 20px !important;
            }
        }
        .myntra-sidebar-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding-bottom: 14px !important;
            margin-bottom: 4px !important;
            border-bottom: 1px solid #edebef !important;
        }
        .myntra-sidebar-title {
            font-size: 14px !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
            color: #282c3f !important;
        }
        .myntra-clear-all {
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #ff3f6c !important;
            text-transform: uppercase !important;
            background: none !important;
            border: none !important;
            cursor: pointer !important;
            padding: 0 !important;
        }
        .checkbox-box {
            width: 16px !important;
            height: 16px !important;
            border: 1.5px solid #c3c2c9 !important;
            border-radius: 2px !important;
            background: #ffffff !important;
            flex-shrink: 0 !important;
            position: relative !important;
            display: inline-block !important;
        }
        .filter-list > li.active .checkbox-box,
        .checkbox-filter-label input:checked + .checkbox-box {
            background: #3D2314 !important;
            border-color: #3D2314 !important;
        }
        .filter-list > li.active .checkbox-box::after,
        .checkbox-filter-label input:checked + .checkbox-box::after {
            content: \'\' !important;
            position: absolute !important;
            left: 4.5px !important;
            top: 1px !important;
            width: 4px !important;
            height: 8px !important;
            border: solid #ffffff !important;
            border-width: 0 2px 2px 0 !important;
            transform: rotate(45deg) !important;
        }
        .shop-sidebar .shop-filter-widget {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            padding: 14px 0 !important;
            margin: 0 !important;
            border-bottom: 1px solid #edebef !important;
        }
        .shop-sidebar .filter-name,
        .shop-sidebar .label-text {
            flex: 1 !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #282c3f !important;
            white-space: normal !important;
            line-height: 1.3 !important;
        }
        .shop-toolbar {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            padding-bottom: 14px !important;
            margin-bottom: 22px !important;
            border-bottom: 1px solid #edebef !important;
        }
        .woocommerce-ordering select {
            padding: 8px 30px 8px 12px !important;
            border-radius: 2px !important;
            border: 1px solid #d4d5d9 !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #282c3f !important;
        }
        /* CRITICAL: Suppress WooCommerce clearfix pseudo-elements that create empty grid cells */
        ul.products::before,
        ul.products::after,
        .woocommerce ul.products::before,
        .woocommerce ul.products::after,
        .woocommerce-page ul.products::before,
        .woocommerce-page ul.products::after {
            content: none !important;
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }
        /* Full-Width Artisan Catalog Architecture */
        .sidebar-pos-none .shop-inner-layout {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .sidebar-pos-none .shop-main-content {
            width: 100% !important;
            max-width: 100% !important;
            flex: none !important;
        }
        .sidebar-pos-none #shop-secondary,
        .sidebar-pos-none .widget-area.shop-sidebar {
            display: none !important;
        }
        .shop-filter-tabs {
            margin: 20px auto 0 !important;
            display: inline-flex !important;
            justify-content: center !important;
        }
        .shop-hero-inner {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
            padding: 24px 0 16px !important;
        }
        .neebites-shop-hero-banner {
            background: #FAF7F2 !important;
            border-bottom: 1px solid #ECE3D7 !important;
            margin-bottom: 30px !important;
        }
        .myntra-title-row {
            display: flex !important;
            align-items: baseline !important;
            justify-content: center !important;
            gap: 10px !important;
            margin-bottom: 12px !important;
        }
        .shop-hero-title {
            font-size: 26px !important;
            font-weight: 700 !important;
            color: #2D1609 !important;
            margin: 0 !important;
        }
        .shop-hero-count {
            font-size: 14px !important;
            color: #7A6B5D !important;
            font-weight: 500 !important;
        }
        @media (min-width: 769px) {
            .trending-filter-tabs {
                display: inline-flex !important;
                align-items: center !important;
                background: #FAF6F0 !important;
                border: 1.5px solid #EAE0D5 !important;
                padding: 5px !important;
                border-radius: 999px !important;
                box-shadow: 0 4px 18px rgba(61, 35, 20, 0.06) !important;
                gap: 6px !important;
                margin: 0 auto !important;
            }
        }
        .trending-filter-tabs .tab-pill {
            position: relative !important;
            background: transparent !important;
            border: 1px solid transparent !important;
            padding: 8px 18px !important;
            border-radius: 999px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #5C3D2E !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            transition: all 0.24s cubic-bezier(0.16, 1, 0.3, 1) !important;
            white-space: nowrap !important;
            outline: none !important;
        }
        .trending-filter-tabs .tab-pill:hover {
            color: #241408 !important;
            background: #F2E8DC !important;
            transform: translateY(-1px) !important;
        }
        .trending-filter-tabs .tab-pill.active {
            background: linear-gradient(135deg, #3D2314 0%, #241408 100%) !important;
            color: #FAF6F0 !important;
            border-color: rgba(197, 155, 39, 0.5) !important;
            box-shadow: 0 4px 14px rgba(61, 35, 20, 0.24), inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
        }
        .trending-filter-tabs .tab-pill .tab-count {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            line-height: 1 !important;
            min-width: 18px !important;
            height: 18px !important;
            padding: 0 5px !important;
            border-radius: 999px !important;
            background: rgba(61, 35, 20, 0.08) !important;
            color: #5C3D2E !important;
        }
        .trending-filter-tabs .tab-pill.active .tab-count {
            background: rgba(197, 155, 39, 0.4) !important;
            color: #FFFDF9 !important;
        }
        @media (min-width: 901px) {
            .sidebar-pos-none ul.products,
            .sidebar-none ul.products,
            .sidebar-drawer ul.products,
            .sidebar-pos-left ul.products,
            .sidebar-pos-right ul.products,
            .related.products ul.products,
            .upsells.products ul.products,
            ul.products,
            ul.products.columns-4,
            .columns-4 ul.products,
            .woocommerce.columns-4 ul.products,
            .woocommerce ul.products.columns-4,
            .woocommerce-shop ul.products,
            .woocommerce-page ul.products {
                display: grid !important;
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                gap: 20px !important;
                list-style: none !important;
                padding: 0 !important;
                margin: 0 0 44px !important;
                align-items: stretch !important;
                width: 100% !important;
            }
            ul.products.columns-2, .columns-2 ul.products { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            ul.products.columns-3, .columns-3 ul.products { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            ul.products.columns-5, .columns-5 ul.products { grid-template-columns: repeat(5, minmax(0, 1fr)); }
            ul.products.columns-6, .columns-6 ul.products { grid-template-columns: repeat(6, minmax(0, 1fr)); }
        }
        ul.products > li.product,
        .woocommerce ul.products > li.product,
        .woocommerce-page ul.products > li.product,
        ul.products li.product,
        ul.products li.product-category {
            float: none !important;
            clear: none !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }
        ul.products > li.product.first,
        .woocommerce ul.products > li.product.first {
            clear: none !important;
            margin-left: 0 !important;
        }
        ul.products > li.product.last,
        .woocommerce ul.products > li.product.last {
            margin-right: 0 !important;
        }
        ul.products li.product::before,
        ul.products li.product::after {
            content: none !important;
            display: none !important;
        }
        /* Strict Equal-Size Product Cards */
        .neebites-product-card {
            min-width: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            box-sizing: border-box !important;
            background: #ffffff !important;
            border: 1px solid #ECE3D7 !important;
            border-radius: 14px !important;
            overflow: hidden !important;
            position: relative !important;
            box-shadow: 0 2px 10px rgba(45, 22, 9, 0.04) !important;
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.28s ease !important;
        }
        .neebites-product-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 14px 28px -4px rgba(45, 22, 9, 0.14), 0 4px 12px rgba(0, 0, 0, 0.04) !important;
            border-color: #2D1609 !important;
        }
        .neebites-product-card .product-card-media,
        .product-card-media {
            position: relative !important;
            width: 100% !important;
            aspect-ratio: 1 / 1 !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: hidden !important;
            flex-shrink: 0 !important;
            background: #FAF7F2 !important;
            border-radius: 13px 13px 0 0 !important;
            display: block !important;
            box-sizing: border-box !important;
        }
        .neebites-product-card .product-card-media img,
        .neebites-product-card .product-primary-img,
        .neebites-product-card .product-secondary-img,
        .neebites-product-card .botanical-svg-img {
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: cover !important;
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .neebites-product-card:hover .product-card-media img,
        .neebites-product-card:hover .product-primary-img,
        .neebites-product-card:hover .botanical-svg-img {
            transform: scale(1.05) !important;
        }
        .neebites-product-card .product-badges {
            position: absolute !important;
            top: 10px !important;
            left: 10px !important;
            z-index: 5 !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 6px !important;
            pointer-events: none !important;
        }
        .neebites-product-card .product-badge,
        .neebites-product-card .badge-sale,
        .neebites-product-card .onsale {
            background: #D32F2F !important;
            color: #ffffff !important;
            font-size: 11px !important;
            font-weight: 800 !important;
            letter-spacing: 0.5px !important;
            padding: 4px 8px !important;
            border-radius: 4px !important;
            line-height: 1.2 !important;
            box-shadow: 0 2px 8px rgba(211, 47, 47, 0.3) !important;
            text-transform: uppercase !important;
            border: none !important;
            display: inline-block !important;
        }
        .neebites-product-card .badge-outofstock {
            background: #64748b !important;
            box-shadow: 0 2px 8px rgba(100, 116, 139, 0.3) !important;
        }
        .neebites-product-card .product-card-rating {
            position: absolute !important;
            bottom: 10px !important;
            left: 10px !important;
            z-index: 5 !important;
            margin: 0 !important;
            display: inline-flex !important;
            pointer-events: none !important;
            font-size: 11px !important;
            line-height: 1 !important;
        }
        .neebites-product-card .rating-chip {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 4px 7px !important;
            border-radius: 4px !important;
            background: rgba(255, 255, 255, 0.95) !important;
            -webkit-backdrop-filter: blur(6px) !important;
            backdrop-filter: blur(6px) !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12) !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #282c3f !important;
            white-space: nowrap !important;
            line-height: 1 !important;
        }
        .neebites-product-card .rating-chip .rc-star {
            color: #C59B27 !important;
            font-size: 10.5px !important;
        }
        .neebites-product-card .rating-chip .rc-sep {
            color: #d4d5d9 !important;
            font-weight: 400 !important;
            font-size: 10px !important;
        }
        .neebites-product-card .rating-chip .rc-count {
            color: #535766 !important;
            font-weight: 600 !important;
            font-size: 10.5px !important;
        }
        .neebites-product-card .product-card-info,
        .product-card-info {
            padding: 14px 16px 16px !important;
            display: flex !important;
            flex-direction: column !important;
            flex: 1 1 auto !important;
            justify-content: space-between !important;
            min-width: 0 !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            background: #ffffff !important;
        }
        .product-card-category {
            font-size: 10px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.6px !important;
            color: #8C7E74 !important;
            height: auto !important;
            line-height: 14px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            margin: 0 0 3px !important;
            min-width: 0 !important;
            max-width: 100% !important;
            display: block !important;
        }
        .product-card-category a {
            color: inherit !important;
            text-decoration: none !important;
        }
        .product-card-title {
            margin: 0 0 6px !important;
            min-width: 0 !important;
            max-width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
            height: auto !important;
            min-height: 42px !important;
            max-height: none !important;
            overflow: visible !important;
            font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif !important;
        }
        .product-title-brand {
            display: block !important;
            font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #1A1A1A !important;
            line-height: 1.35 !important;
            height: auto !important;
            min-height: 19px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            text-decoration: none !important;
            margin-bottom: 2px !important;
            min-width: 0 !important;
            max-width: 100% !important;
            letter-spacing: -0.15px !important;
        }
        .product-title-brand:hover,
        .neebites-product-card:hover .product-title-brand {
            color: #2D1609 !important;
        }
        .product-title-sub {
            display: block !important;
            font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif !important;
            font-size: 11.5px !important;
            font-weight: 400 !important;
            color: #7A6C60 !important;
            line-height: 1.35 !important;
            height: auto !important;
            min-height: 16px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            min-width: 0 !important;
            max-width: 100% !important;
            letter-spacing: 0 !important;
        }
        .product-card-care-row,
        .care-chip,
        .product-card-price del,
        .product-card-price .price-mrp,
        .price-mrp {
            display: none !important;
        }
        .product-card-price {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #1A1A1A !important;
            height: auto !important;
            min-height: 22px !important;
            line-height: 1.2 !important;
            margin-top: auto !important;
            margin-bottom: 2px !important;
            padding: 4px 0 2px !important;
            display: flex !important;
            align-items: baseline !important;
            gap: 6px 8px !important;
            flex-wrap: wrap !important;
            overflow: visible !important;
            white-space: normal !important;
            min-width: 0 !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .product-card-price ins,
        .product-card-price .price-current {
            order: 1 !important;
            text-decoration: none !important;
            color: #1A1A1A !important;
            font-weight: 700 !important;
            font-size: 15px !important;
            line-height: 1.2 !important;
            display: inline-block !important;
            vertical-align: baseline !important;
        }
        .product-card-price .price-off,
        .price-off {
            order: 2 !important;
            color: #D32F2F !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            letter-spacing: 0.2px !important;
            display: inline-block !important;
            vertical-align: baseline !important;
        }
        .product-card-action-bar {
            margin-top: 6px !important;
            padding-top: 0 !important;
            width: 100% !important;
            min-width: 0 !important;
        }
        .btn-card-add-cart {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            width: 100% !important;
            height: 36px !important;
            min-height: 36px !important;
            max-height: 36px !important;
            padding: 0 10px !important;
            border-radius: 6px !important;
            background: #FAF7F2 !important;
            color: #2D1609 !important;
            border: 1px solid #2D1609 !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            letter-spacing: 0.4px !important;
            text-transform: uppercase !important;
            text-decoration: none !important;
            cursor: pointer !important;
            box-sizing: border-box !important;
            transition: all 0.2s ease !important;
            line-height: 1 !important;
            min-width: 0 !important;
            max-width: 100% !important;
        }
        .btn-card-add-cart:hover {
            background: #2D1609 !important;
            color: #ffffff !important;
            border-color: #2D1609 !important;
            box-shadow: 0 4px 12px rgba(45, 22, 9, 0.2) !important;
            transform: translateY(-1px) !important;
        }
        .btn-card-add-cart .btn-cart-icon {
            display: inline-flex !important;
            align-items: center !important;
        }
        .btn-card-add-cart.loading .btn-cart-icon,
        .btn-card-add-cart.loading .btn-cart-label {
            display: none !important;
        }
        .btn-card-add-cart.loading .btn-spinner {
            display: inline-block !important;
            width: 13px !important;
            height: 13px !important;
            border: 2px solid rgba(61, 35, 20, 0.3) !important;
            border-top-color: #3D2314 !important;
            border-radius: 50% !important;
            animation: neebitesSpin 0.6s linear infinite !important;
        }
        @media (max-width: 1240px) {
            .sidebar-pos-left ul.products,
            .sidebar-pos-right ul.products,
            ul.products,
            ul.products.columns-4,
            ul.products.columns-5,
            ul.products.columns-6,
            .columns-4 ul.products,
            .columns-5 ul.products {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 18px 14px !important;
            }
        }
        @media (max-width: 1024px) {
            .shop-wrapper {
                padding-bottom: 85px !important;
            }
            .shop-inner-layout,
            .sidebar-pos-left .shop-inner-layout,
            .sidebar-pos-right .shop-inner-layout {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                gap: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .shop-main-content {
                width: 100% !important;
                max-width: 100% !important;
                flex: none !important;
                min-width: 0 !important;
                display: block !important;
            }
            /* Off-canvas mobile filter drawer */
            .shop-sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                bottom: 0 !important;
                width: min(340px, 86vw) !important;
                height: 100vh !important;
                max-height: 100vh !important;
                margin: 0 !important;
                padding: 20px 18px 80px !important;
                z-index: 999999 !important;
                background: #ffffff !important;
                border: none !important;
                border-right: 1px solid #EAE0D5 !important;
                border-radius: 0 20px 20px 0 !important;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25) !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
                transform: translateX(-105%) !important;
                visibility: hidden !important;
                transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s ease !important;
            }
            .shop-sidebar.is-open {
                transform: translateX(0) !important;
                visibility: visible !important;
            }
            .shop-filter-backdrop {
                position: fixed !important;
                inset: 0 !important;
                background: rgba(0, 0, 0, 0.5) !important;
                -webkit-backdrop-filter: blur(2px) !important;
                backdrop-filter: blur(2px) !important;
                z-index: 999998 !important;
                opacity: 0 !important;
                visibility: hidden !important;
                transition: opacity 0.3s ease, visibility 0.3s ease !important;
            }
            .shop-filter-backdrop.is-visible,
            body.shop-filters-open .shop-filter-backdrop {
                opacity: 1 !important;
                visibility: visible !important;
            }
            body.shop-filters-open {
                overflow: hidden !important;
            }
            .shop-filter-mobile-header {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                padding-bottom: 16px !important;
                margin-bottom: 12px !important;
                border-bottom: 1px solid #edebef !important;
            }
            .myntra-sidebar-header {
                display: none !important;
            }
            .btn-close-filter-drawer {
                width: 36px !important;
                height: 36px !important;
                border-radius: 50% !important;
                background: #FAF6F0 !important;
                border: 1px solid #EAE0D5 !important;
                color: #3D2314 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                cursor: pointer !important;
                padding: 0 !important;
            }
            .shop-filter-drawer-footer {
                display: flex !important;
                align-items: center !important;
                gap: 12px !important;
                position: sticky !important;
                bottom: -80px !important;
                margin: 20px -18px -80px !important;
                padding: 14px 18px calc(14px + env(safe-area-inset-bottom, 0px)) !important;
                background: #ffffff !important;
                border-top: 1px solid #edebef !important;
                box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08) !important;
                z-index: 20 !important;
            }
            .btn-clear-all-mobile {
                flex: 1 !important;
                height: 40px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                border-radius: 8px !important;
                background: #FAF6F0 !important;
                border: 1px solid #d4d5d9 !important;
                font-size: 12.5px !important;
                font-weight: 700 !important;
                color: #535766 !important;
                cursor: pointer !important;
                text-transform: uppercase !important;
            }
            .btn-apply-filters {
                flex: 2 !important;
                height: 40px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 4px !important;
                border-radius: 8px !important;
                background: #3D2314 !important;
                border: none !important;
                font-size: 13px !important;
                font-weight: 700 !important;
                color: #ffffff !important;
                cursor: pointer !important;
                box-shadow: 0 4px 12px rgba(61, 35, 20, 0.25) !important;
                text-transform: uppercase !important;
            }
            /* Clean Mobile Toolbar */
            .shop-toolbar {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 10px !important;
                width: 100% !important;
                padding-bottom: 12px !important;
                margin-bottom: 18px !important;
                border-bottom: 1px solid #edebef !important;
            }
            .shop-toolbar-left {
                display: flex !important;
                align-items: center !important;
                flex-shrink: 0 !important;
            }
            .btn-open-filters {
                display: inline-flex !important;
                align-items: center !important;
                gap: 6px !important;
                height: 38px !important;
                padding: 0 14px !important;
                background: #ffffff !important;
                border: 1.5px solid #3D2314 !important;
                border-radius: 8px !important;
                font-family: inherit !important;
                font-size: 13px !important;
                font-weight: 700 !important;
                color: #3D2314 !important;
                cursor: pointer !important;
                box-shadow: 0 1px 3px rgba(61, 35, 20, 0.08) !important;
            }
            .btn-open-filters svg {
                stroke: #3D2314 !important;
            }
            .shop-toolbar-right {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-end !important;
                gap: 8px !important;
                flex: 1 !important;
                min-width: 0 !important;
            }
            .shop-toolbar-right .woocommerce-result-count {
                display: none !important;
            }
            .shop-toolbar-right .woocommerce-ordering {
                margin: 0 !important;
                flex-shrink: 0 !important;
            }
            .shop-toolbar-right .woocommerce-ordering select {
                height: 38px !important;
                padding: 6px 28px 6px 10px !important;
                border-radius: 8px !important;
                border: 1.5px solid #d4d5d9 !important;
                font-size: 13px !important;
                font-weight: 600 !important;
                background-color: #ffffff !important;
                background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%23282c3f' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E\") !important;
                background-repeat: no-repeat !important;
                background-position: right 9px center !important;
                -webkit-appearance: none !important;
                max-width: 175px !important;
                text-overflow: ellipsis !important;
            }
            /* Horizontally scrollable category pills */
            .shop-hero-category-pills {
                display: flex !important;
                align-items: center !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                gap: 8px !important;
                padding: 4px 16px 8px !important;
                margin: 6px -16px 14px !important;
                scrollbar-width: none !important;
            }
            .shop-hero-category-pills::-webkit-scrollbar {
                display: none !important;
            }
            .category-pill {
                flex-shrink: 0 !important;
                white-space: nowrap !important;
            }
            .sidebar-pos-left ul.products,
            .sidebar-pos-right ul.products,
            ul.products,
            ul.products.columns-4,
            ul.products.columns-5,
            ul.products.columns-6,
            .columns-4 ul.products,
            .columns-5 ul.products,
            .related.products ul.products {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                gap: 20px 16px !important;
            }
        }
        @media (max-width: 900px) {
            .sidebar-pos-none ul.products,
            .sidebar-none ul.products,
            .sidebar-drawer ul.products,
            .sidebar-pos-left ul.products,
            .sidebar-pos-right ul.products,
            .related.products ul.products,
            .upsells.products ul.products,
            ul.products,
            ul.products.columns-2,
            ul.products.columns-3,
            ul.products.columns-4,
            ul.products.columns-5,
            ul.products.columns-6,
            .columns-2 ul.products,
            .columns-3 ul.products,
            .columns-4 ul.products,
            .columns-5 ul.products,
            .columns-6 ul.products,
            .woocommerce.columns-4 ul.products,
            .woocommerce ul.products.columns-4,
            .woocommerce-shop ul.products,
            .woocommerce-page ul.products,
            body.woocommerce-shop ul.products,
            body.sidebar-drawer ul.products,
            body.sidebar-none ul.products {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 12px 10px !important;
                margin-bottom: 28px !important;
                width: 100% !important;
            }
        }
        @media (max-width: 768px) {
            /* Mobile Shop Hero Header Polish */
            .neebites-shop-hero-banner {
                padding: 12px 0 8px !important;
                margin-bottom: 8px !important;
                background: #FAF7F2 !important;
                border-bottom: 1px solid #ECE3D7 !important;
            }
            .shop-hero-inner {
                padding: 0 !important;
                text-align: left !important;
                align-items: flex-start !important;
            }
            .shop-hero-breadcrumb {
                font-size: 11px !important;
                gap: 4px !important;
                margin-bottom: 4px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                width: 100% !important;
            }
            .myntra-title-row {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                width: 100% !important;
                margin: 0 0 6px !important;
            }
            .shop-hero-title {
                font-size: 17px !important;
                font-weight: 700 !important;
                color: #2D1609 !important;
                line-height: 1.25 !important;
                margin: 0 !important;
            }
            .shop-hero-count {
                font-size: 11px !important;
                font-weight: 600 !important;
                color: #5C4033 !important;
                background: #ECE4DA !important;
                padding: 2px 8px !important;
                border-radius: 9999px !important;
                white-space: nowrap !important;
            }
            
            /* Horizontal Swipeable Flavour Pills Rail */
            .trending-filter-tabs,
            .shop-filter-tabs {
                display: flex !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                scrollbar-width: none !important;
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 4px 0 6px !important;
                margin: 4px 0 10px !important;
                width: 100% !important;
                gap: 8px !important;
                justify-content: flex-start !important;
            }
            .trending-filter-tabs::-webkit-scrollbar,
            .shop-filter-tabs::-webkit-scrollbar {
                display: none !important;
            }
            .trending-filter-tabs .tab-pill,
            .shop-filter-tabs .tab-pill {
                flex-shrink: 0 !important;
                white-space: nowrap !important;
                height: 34px !important;
                padding: 0 13px !important;
                font-size: 12px !important;
                font-weight: 600 !important;
                border-radius: 9999px !important;
                background: #FFFFFF !important;
                border: 1px solid #D5C9BD !important;
                color: #5C4033 !important;
                box-shadow: 0 1px 3px rgba(45, 22, 9, 0.04) !important;
                display: inline-flex !important;
                align-items: center !important;
                gap: 5px !important;
            }
            .trending-filter-tabs .tab-pill.active,
            .shop-filter-tabs .tab-pill.active {
                background: linear-gradient(135deg, #3D2314 0%, #241408 100%) !important;
                border-color: #241408 !important;
                color: #FFFFFF !important;
                box-shadow: 0 2px 8px rgba(36, 20, 8, 0.22) !important;
            }
            .tab-pill .tab-count {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 10px !important;
                font-weight: 700 !important;
                min-width: 17px !important;
                height: 17px !important;
                padding: 0 4px !important;
                border-radius: 9999px !important;
                background: #ECE4DA !important;
                color: #5C4033 !important;
            }
            .tab-pill.active .tab-count {
                background: rgba(255, 255, 255, 0.22) !important;
                color: #FFFFFF !important;
            }

            /* Mobile Shop Toolbar */
            .shop-toolbar {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 8px !important;
                padding: 6px 0 10px !important;
                margin-bottom: 12px !important;
                border-bottom: 1px solid #ECE3D7 !important;
                width: 100% !important;
            }
            .shop-toolbar-left {
                display: flex !important;
                align-items: center !important;
            }
            .shop-toolbar-right {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-end !important;
            }
            .btn-open-filters {
                display: inline-flex !important;
                align-items: center !important;
                gap: 6px !important;
                height: 34px !important;
                padding: 0 12px !important;
                border-radius: 8px !important;
                background: #FFFFFF !important;
                border: 1px solid #D5C9BD !important;
                color: #2D1609 !important;
                font-size: 11.5px !important;
                font-weight: 700 !important;
                letter-spacing: 0.2px !important;
                box-shadow: 0 1px 3px rgba(45, 22, 9, 0.04) !important;
            }
            .shop-toolbar-right .woocommerce-ordering {
                margin: 0 !important;
            }
            .shop-toolbar-right .woocommerce-ordering select {
                height: 34px !important;
                padding: 0 24px 0 10px !important;
                border-radius: 8px !important;
                font-size: 11.5px !important;
                font-weight: 600 !important;
                border: 1px solid #D5C9BD !important;
                color: #2D1609 !important;
                background-position: right 8px center !important;
                background-size: 10px !important;
            }
            .woocommerce-result-count {
                display: none !important;
            }

            .neebites-product-card {
                border-radius: 12px !important;
            }
            .product-card-info,
            .neebites-product-card .product-card-info {
                padding: 10px 10px 12px !important;
            }
            .product-card-category {
                font-size: 10px !important;
                height: 14px !important;
                line-height: 14px !important;
                margin-bottom: 2px !important;
            }
            .product-card-title {
                display: flex !important;
                flex-direction: column !important;
                justify-content: flex-start !important;
                height: auto !important;
                min-height: 38px !important;
                max-height: none !important;
                overflow: visible !important;
                margin-bottom: 4px !important;
            }
            .product-title-brand {
                font-size: 13.5px !important;
                line-height: 1.35 !important;
                height: auto !important;
                min-height: 17px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            .product-title-sub {
                font-size: 11px !important;
                line-height: 1.35 !important;
                height: auto !important;
                min-height: 15px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            .product-card-care-row,
            .care-chip,
            .product-card-price del,
            .product-card-price .price-mrp,
            .price-mrp {
                display: none !important;
            }
            .product-card-price {
                font-size: 13.5px !important;
                height: auto !important;
                min-height: 20px !important;
                line-height: 1.2 !important;
                padding: 2px 0 !important;
                gap: 4px 6px !important;
                overflow: visible !important;
                flex-wrap: wrap !important;
            }
            .product-card-price ins,
            .product-card-price .price-current {
                font-size: 13.5px !important;
                line-height: 1.2 !important;
            }
            .price-off {
                font-size: 10px !important;
                line-height: 1.2 !important;
            }
            .product-card-action-bar {
                margin-top: 6px !important;
            }
            .btn-card-add-cart {
                height: 34px !important;
                min-height: 34px !important;
                max-height: 34px !important;
                font-size: 11.5px !important;
                gap: 4px !important;
                padding: 0 8px !important;
            }
        }
        @media (max-width: 480px) {
            .sidebar-pos-left ul.products,
            .sidebar-pos-right ul.products,
            ul.products,
            ul.products.columns-2,
            ul.products.columns-3,
            ul.products.columns-4,
            ul.products.columns-5,
            ul.products.columns-6 {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 10px 8px !important;
            }
            .shop-toolbar-right .woocommerce-ordering select {
                max-width: 140px !important;
                font-size: 12px !important;
                padding: 6px 24px 6px 8px !important;
            }
            .btn-open-filters {
                padding: 0 10px !important;
                font-size: 12px !important;
            }
        }
        /* Single Product Instant Resets & Layout Safety */
        .neebites-single-product-container,
        .single-product div.product {
            position: relative !important;
            width: 100% !important;
        }
        .single-product .shop-inner-layout {
            display: block !important;
            width: 100% !important;
            margin-bottom: 40px !important;
        }
        .single-product .shop-main-content {
            width: 100% !important;
            max-width: 100% !important;
            flex: none !important;
        }
        .woocommerce div.product div.images,
        .woocommerce #content div.product div.images,
        .woocommerce-page div.product div.images,
        .single-product .woocommerce-product-gallery,
        .neebites-product-gallery-col,
        .neebites-gallery-wrapper {
            float: none !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .woocommerce div.product div.summary,
        .woocommerce #content div.product div.summary,
        .woocommerce-page div.product div.summary,
        .single-product .summary.entry-summary,
        .neebites-product-summary-col {
            float: none !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .neebites-product-stage {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.08fr) !important;
            column-gap: 52px !important;
            row-gap: 40px !important;
            align-items: start !important;
            margin-bottom: 56px !important;
            width: 100% !important;
        }
        .neebites-product-gallery-col,
        .neebites-product-summary-col {
            width: 100% !important;
            min-width: 0 !important;
        }
        @media (min-width: 861px) {
            .neebites-product-gallery-col,
            .neebites-product-summary-col {
                position: -webkit-sticky !important;
                position: sticky !important;
                top: 96px !important;
                align-self: flex-start !important;
            }
        }
        .single-product div.product:not(.neebites-single-product-container) {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.08fr) !important;
            column-gap: 52px !important;
            row-gap: 40px !important;
            align-items: start !important;
        }
        .single-product div.product:not(.neebites-single-product-container) > .woocommerce-product-gallery {
            grid-column: 1 !important;
            grid-row: 1 !important;
            width: 100% !important;
            margin: 0 !important;
        }
        .single-product div.product:not(.neebites-single-product-container) > .summary.entry-summary {
            grid-column: 2 !important;
            grid-row: 1 !important;
            width: 100% !important;
            margin: 0 !important;
        }
        .myntra-gallery-container,
        .woocommerce-product-gallery {
            position: relative !important;
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            opacity: 1 !important;
            visibility: visible !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .myntra-main-stage {
            position: relative !important;
            width: 100% !important;
            min-height: unset !important;
            max-height: none !important;
            background: #FAF7F2 !important;
            border-radius: 18px !important;
            border: 1px solid #EAE0D5 !important;
            overflow: hidden !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 4px 20px rgba(42, 22, 10, 0.05) !important;
            margin-bottom: 14px !important;
        }
        .myntra-slides-track {
            position: relative !important;
            width: 100% !important;
            height: auto !important;
            min-height: unset !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .myntra-slide-item.is-active {
            position: relative !important;
            opacity: 1 !important;
            visibility: visible !important;
            z-index: 2 !important;
            width: 100% !important;
            height: auto !important;
        }
        .myntra-img-wrap {
            width: 100% !important;
            height: auto !important;
            min-height: unset !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
            cursor: zoom-in !important;
        }
        .myntra-gallery-img {
            width: 100% !important;
            height: auto !important;
            max-height: 580px !important;
            object-fit: contain !important;
            margin: 0 auto !important;
            display: block !important;
            border-radius: 18px !important;
        }
        .myntra-gallery-modal {
            position: fixed !important;
            inset: 0 !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 999999999 !important;
        }
        body.myntra-modal-open .neebites-product-summary-col {
            position: static !important;
            z-index: 0 !important;
            pointer-events: none !important;
        }
        body.myntra-modal-open #neebites-sticky-bar {
            display: none !important;
        }
        .single-product div.product > .product-badge,
        .single-product div.product > .onsale,
        .neebites-gallery-wrapper > .product-badge,
        .neebites-gallery-wrapper > .onsale,
        .woocommerce-product-gallery > .product-badge,
        .woocommerce-product-gallery > .onsale {
            position: absolute !important;
            top: 20px !important;
            left: 20px !important;
            z-index: 25 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: auto !important;
            max-width: max-content !important;
            height: auto !important;
            min-height: 28px !important;
            padding: 6px 14px !important;
            border-radius: 9999px !important;
            font-size: 13px !important;
            font-weight: 800 !important;
            letter-spacing: 0.5px !important;
            line-height: 1 !important;
            background: linear-gradient(135deg, #D32F2F 0%, #B71C1C 100%) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 14px rgba(183, 28, 28, 0.35) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.6) !important;
            pointer-events: none !important;
            margin: 0 !important;
        }
        .single-product div.product > .woocommerce-tabs,
        .single-product div.product > .related.products,
        .single-product div.product > .upsells.products,
        .single-product div.product > .woocommerce-notices-wrapper,
        .neebites-product-details-stage {
            grid-column: 1 / -1 !important;
            width: 100% !important;
        }
        @media (max-width: 860px) {
            .neebites-product-stage,
            .single-product div.product:not(.neebites-single-product-container) {
                grid-template-columns: 1fr !important;
                gap: 28px !important;
            }
            .single-product div.product:not(.neebites-single-product-container) > .woocommerce-product-gallery {
                grid-column: 1 !important;
                grid-row: 1 !important;
            }
            .single-product div.product:not(.neebites-single-product-container) > .summary.entry-summary {
                grid-column: 1 !important;
                grid-row: 2 !important;
                position: static !important;
            }
            .single-product div.product > .product-badge,
            .single-product div.product > .onsale,
            .neebites-gallery-wrapper > .product-badge,
            .neebites-gallery-wrapper > .onsale {
                top: 14px !important;
                left: 14px !important;
                font-size: 11.5px !important;
                padding: 5px 12px !important;
            }
        }

        /* Sticky Bottom Add-to-Cart Bar - Next-Level UI */
        .neebites-sticky-add-to-cart {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: rgba(255, 255, 255, 0.98) !important;
            -webkit-backdrop-filter: blur(14px) !important;
            backdrop-filter: blur(14px) !important;
            border-top: 1px solid #EAE0D5 !important;
            box-shadow: 0 -6px 28px rgba(43, 23, 4, 0.14) !important;
            z-index: 9990 !important;
            padding: 10px 0 !important;
            transform: translateY(115%) !important;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .neebites-sticky-add-to-cart.is-visible {
            transform: translateY(0) !important;
        }
        .sticky-bar-container {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 16px !important;
            max-width: 1240px !important;
            margin: 0 auto !important;
            padding: 0 20px !important;
        }
        .sticky-product-meta-block {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            min-width: 0 !important;
        }
        .sticky-product-thumb {
            width: 48px !important;
            height: 48px !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            background: #FAF7F2 !important;
            border: 1px solid #EAE0D5 !important;
            flex-shrink: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .sticky-product-thumb img {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
        }
        .sticky-product-info {
            min-width: 0 !important;
        }
        .sticky-product-brand {
            font-size: 10px !important;
            font-weight: 800 !important;
            color: #8C5835 !important;
            letter-spacing: 1.2px !important;
            text-transform: uppercase !important;
            display: block !important;
            line-height: 1.2 !important;
        }
        .sticky-product-title {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #201005 !important;
            margin: 2px 0 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: 380px !important;
        }
        .sticky-product-pricing {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-size: 13.5px !important;
        }
        .sticky-price-current {
            font-weight: 800 !important;
            color: #201005 !important;
        }
        .sticky-price-mrp {
            font-size: 12px !important;
            color: #94969f !important;
            text-decoration: line-through !important;
        }
        .sticky-price-off {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #E65100 !important;
        }
        .sticky-product-actions-block {
            flex-shrink: 0 !important;
        }
        .sticky-action-inner {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
        }
        /* Luxury Food-Grade Stepper (Matches Main PDP Stepper) */
        .neebites-qty-stepper.sticky-qty-stepper {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            background: #FAF7F2 !important;
            border: 1.5px solid #D5C9BD !important;
            border-radius: 8px !important;
            height: 52px !important;
            width: 120px !important;
            max-width: 120px !important;
            padding: 0 2px !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        }
        .neebites-qty-stepper.sticky-qty-stepper:hover,
        .neebites-qty-stepper.sticky-qty-stepper:focus-within {
            border-color: #3D2314 !important;
            box-shadow: 0 2px 8px rgba(61, 35, 20, 0.08) !important;
        }
        .sticky-qty-stepper .btn-qty-step {
            width: 36px !important;
            height: 100% !important;
            border: none !important;
            background: transparent !important;
            color: #3D2314 !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            margin: 0 !important;
            outline: none !important;
            transition: background 0.15s ease, color 0.15s ease, transform 0.1s ease !important;
            -webkit-user-select: none !important;
            user-select: none !important;
        }
        .sticky-qty-stepper .btn-qty-step:hover {
            background: #EDE4DA !important;
            color: #1F110B !important;
        }
        .sticky-qty-stepper .btn-qty-step:active {
            background: #DFD3C5 !important;
            transform: scale(0.92) !important;
        }
        .sticky-qty-stepper input.sticky-qty-input {
            width: 44px !important;
            height: 100% !important;
            border: none !important;
            background: transparent !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            color: #2C1810 !important;
            text-align: center !important;
            padding: 0 !important;
            margin: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            -moz-appearance: textfield !important;
            appearance: textfield !important;
        }
        .sticky-qty-stepper input.sticky-qty-input::-webkit-outer-spin-button,
        .sticky-qty-stepper input.sticky-qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }
        /* Sticky Dual Action Buttons (Matches Main PDP Dual CTA Buttons) */
        .sticky-buttons-group {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
        }
        .btn-sticky-add-bag {
            height: 52px !important;
            min-width: 170px !important;
            padding: 0 24px !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            letter-spacing: 0.8px !important;
            text-transform: uppercase !important;
            border-radius: 8px !important;
            background: linear-gradient(135deg, #2B170C 0%, #150A05 100%) !important;
            color: #FFFFFF !important;
            border: none !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 9px !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 4px 18px rgba(33, 16, 7, 0.28) !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
            position: relative !important;
            overflow: hidden !important;
            text-decoration: none !important;
            white-space: nowrap !important;
        }
        .btn-sticky-add-bag:hover {
            background: linear-gradient(135deg, #3A2011 0%, #201007 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 8px 25px rgba(33, 16, 7, 0.4) !important;
            color: #FFFFFF !important;
        }
        .btn-sticky-add-bag:active {
            transform: translateY(0) scale(0.98) !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.4), 0 2px 8px rgba(33, 16, 7, 0.2) !important;
        }
        .btn-sticky-buy-now {
            height: 52px !important;
            min-width: 170px !important;
            padding: 0 24px !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            letter-spacing: 0.8px !important;
            text-transform: uppercase !important;
            border-radius: 8px !important;
            background: linear-gradient(135deg, #FF6F00 0%, #E65100 100%) !important;
            color: #FFFFFF !important;
            border: none !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 9px !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28), 0 4px 18px rgba(230, 81, 0, 0.35) !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
            position: relative !important;
            overflow: hidden !important;
            text-decoration: none !important;
            white-space: nowrap !important;
        }
        .btn-sticky-buy-now:hover {
            background: linear-gradient(135deg, #FF7E14 0%, #F55800 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.38), 0 8px 25px rgba(230, 81, 0, 0.48) !important;
            color: #FFFFFF !important;
            filter: brightness(1.04) !important;
        }
        .btn-sticky-buy-now:active {
            transform: translateY(0) scale(0.98) !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3), 0 2px 8px rgba(230, 81, 0, 0.25) !important;
        }
        @media (max-width: 768px) {
            .neebites-sticky-add-to-cart {
                bottom: 0 !important;
                padding: 10px 14px calc(10px + env(safe-area-inset-bottom, 0px)) !important;
            }
            .sticky-bar-container {
                display: grid !important;
                grid-template-columns: 1fr auto !important;
                grid-template-rows: auto auto !important;
                gap: 10px !important;
                padding: 0 4px !important;
            }
            .sticky-product-meta-block {
                grid-column: 1 / 2 !important;
                grid-row: 1 / 2 !important;
            }
            .sticky-product-actions-block {
                grid-column: 1 / -1 !important;
                grid-row: 2 / 3 !important;
                width: 100% !important;
            }
            .sticky-action-inner {
                width: 100% !important;
                gap: 10px !important;
            }
            .sticky-qty-stepper-wrap {
                flex-shrink: 0 !important;
            }
            .sticky-buttons-group {
                flex: 1 !important;
                gap: 10px !important;
            }
            .neebites-qty-stepper.sticky-qty-stepper {
                height: 48px !important;
                width: 105px !important;
                max-width: 105px !important;
            }
            .sticky-qty-stepper .btn-qty-step {
                width: 32px !important;
            }
            .sticky-qty-stepper input.sticky-qty-input {
                width: 38px !important;
                font-size: 15px !important;
            }
            .btn-sticky-add-bag,
            .btn-sticky-buy-now {
                flex: 1 1 0 !important;
                min-width: 0 !important;
                padding: 0 10px !important;
                height: 48px !important;
                font-size: 13.5px !important;
                letter-spacing: 0.5px !important;
                gap: 6px !important;
            }
            .sticky-product-title {
                max-width: 200px !important;
                font-size: 13px !important;
            }
        }
        @media (max-width: 480px) {
            .sticky-product-title {
                max-width: 160px !important;
            }
            .neebites-qty-stepper.sticky-qty-stepper {
                height: 46px !important;
                width: 95px !important;
                max-width: 95px !important;
            }
            .sticky-qty-stepper .btn-qty-step {
                width: 28px !important;
            }
            .sticky-qty-stepper input.sticky-qty-input {
                width: 34px !important;
                font-size: 14px !important;
            }
            .btn-sticky-add-bag,
            .btn-sticky-buy-now {
                height: 46px !important;
                font-size: 13px !important;
                padding: 0 6px !important;
                gap: 5px !important;
            }
        }

        /* --------------------------------------------------------------------------
           Myntra Single Product (PDP) Luxury Styling
           -------------------------------------------------------------------------- */
        .myntra-pdp-breadcrumb {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
            font-size: 13px !important;
            color: #535665 !important;
            margin-bottom: 22px !important;
            padding: 0 !important;
            line-height: 1.4 !important;
        }
        .myntra-pdp-breadcrumb a {
            color: #282c3f !important;
            text-decoration: none !important;
            font-weight: 500 !important;
            transition: color 0.2s ease !important;
        }
        .myntra-pdp-breadcrumb a:hover {
            color: #ff3e6c !important;
        }
        .myntra-pdp-breadcrumb .sep {
            color: #d4d5d9 !important;
            font-size: 11px !important;
        }
        .myntra-pdp-breadcrumb .current {
            color: #8c7362 !important;
            font-weight: 600 !important;
        }

        /* Header */
        .myntra-pdp-header {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            margin-bottom: 4px !important;
        }
        .myntra-brand-name {
            font-family: var(--font-primary, 'Plus Jakarta Sans', sans-serif) !important;
            font-size: 24px !important;
            font-weight: 700 !important;
            color: #282c3f !important;
            line-height: 1.15 !important;
            letter-spacing: 0.6px !important;
            text-transform: uppercase !important;
            margin: 0 0 6px !important;
        }
        .myntra-product-title {
            font-family: var(--font-primary, 'Plus Jakarta Sans', sans-serif) !important;
            font-size: 19px !important;
            font-weight: 400 !important;
            color: #535665 !important;
            line-height: 1.35 !important;
            margin: 0 0 6px !important;
        }
        .myntra-product-sub {
            font-size: 13.5px !important;
            color: #8C7362 !important;
            font-weight: 500 !important;
            margin: 0 0 14px !important;
            line-height: 1.4 !important;
        }
        .myntra-rating-chip-wrap {
            display: flex !important;
            align-items: center !important;
            margin: 6px 0 16px !important;
        }
        .myntra-rating-chip {
            display: inline-flex !important;
            align-items: center !important;
            gap: 7px !important;
            padding: 4px 10px !important;
            border: 1px solid #eaeaec !important;
            border-radius: 4px !important;
            background: #ffffff !important;
            text-decoration: none !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #282c3f !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
            cursor: pointer !important;
        }
        .myntra-rating-chip:hover {
            border-color: #282c3f !important;
            background: #fbfbfb !important;
        }
        .myntra-rating-chip .rating-val {
            display: inline-flex !important;
            align-items: center !important;
            gap: 3px !important;
        }
        .myntra-rating-chip .rating-star {
            color: #14958f !important;
            font-size: 13px !important;
        }
        .myntra-rating-chip .rating-sep {
            color: #d4d5d9 !important;
            font-weight: 400 !important;
            font-size: 11px !important;
        }
        .myntra-rating-chip .rating-count {
            color: #535665 !important;
            font-weight: 600 !important;
            font-size: 12px !important;
        }
        .myntra-header-divider {
            height: 1px !important;
            background: #eaeaec !important;
            margin: 14px 0 18px !important;
            width: 100% !important;
        }

        /* Price Section */
        .myntra-price-section {
            margin-bottom: 20px !important;
        }
        .myntra-price-row {
            display: flex !important;
            align-items: baseline !important;
            gap: 12px !important;
            margin-bottom: 4px !important;
        }
        .myntra-price-current {
            font-size: 26px !important;
            font-weight: 700 !important;
            color: #282c3f !important;
            letter-spacing: -0.01em !important;
            line-height: 1 !important;
        }
        .myntra-price-mrp {
            font-size: 18px !important;
            font-weight: 400 !important;
            color: #94969f !important;
            line-height: 1 !important;
        }
        .myntra-price-mrp del {
            text-decoration: line-through !important;
            color: inherit !important;
        }
        .myntra-price-off {
            font-size: 18px !important;
            font-weight: 700 !important;
            color: #ff905a !important;
            letter-spacing: 0.2px !important;
            line-height: 1 !important;
        }
        .myntra-tax-tag {
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #03a685 !important;
            letter-spacing: 0.2px !important;
            text-transform: lowercase !important;
        }

        /* Eradicate raw WooCommerce variations table & dropdown - replaced by auto Select Pack Size chips */
        .single-product form.variations_form table.variations {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            max-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            overflow: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        .single-product form.variations_form .reset_variations,
        .single-product form.variations_form .woocommerce-variation.single_variation {
            display: none !important;
        }
        .single-product form.variations_form .single_variation_wrap {
            margin-top: 0 !important;
            padding-top: 0 !important;
            border-top: none !important;
        }

        /* Size Chips */
        .myntra-size-section {
            margin: 22px 0 20px !important;
        }
        .myntra-size-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            margin-bottom: 12px !important;
        }
        .myntra-size-title {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #282c3f !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
        }
        .myntra-size-guide-link {
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #ff3e6c !important;
            text-decoration: none !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
        }
        .myntra-size-chips {
            display: flex !important;
            gap: 12px !important;
            flex-wrap: wrap !important;
            margin-bottom: 10px !important;
        }
        .myntra-size-chip {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 86px !important;
            height: 52px !important;
            padding: 6px 16px !important;
            border-radius: 40px !important;
            border: 1.5px solid #bfc0c6 !important;
            background: #ffffff !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            outline: none !important;
            color: #282c3f !important;
            -webkit-user-select: none !important;
            user-select: none !important;
        }
        .myntra-size-chip:hover:not(.out-of-stock):not([disabled]) {
            border-color: #3D2314 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 8px rgba(61, 35, 20, 0.08) !important;
        }
        .myntra-size-chip.active {
            border-color: #3D2314 !important;
            background: #FAF7F2 !important;
            color: #3D2314 !important;
            box-shadow: 0 2px 8px rgba(61, 35, 20, 0.12), inset 0 0 0 1px #3D2314 !important;
        }
        .myntra-size-chip.out-of-stock,
        .myntra-size-chip[disabled] {
            opacity: 0.45 !important;
            cursor: not-allowed !important;
            background: #f7f7f7 !important;
            border-color: #e0e0e0 !important;
            color: #999999 !important;
            text-decoration: line-through !important;
            pointer-events: none !important;
        }
        .chip-size-main {
            font-size: 14.5px !important;
            font-weight: 700 !important;
            color: inherit !important;
            line-height: 1.1 !important;
        }
        .chip-size-sub {
            font-size: 10px !important;
            font-weight: 600 !important;
            color: #8c7362 !important;
            margin-top: 2px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
        }
        .myntra-stock-urgency {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-size: 12.5px !important;
            color: #e53935 !important;
            margin: 8px 0 0 !important;
            font-weight: 500 !important;
        }

        /* Myntra Add-on Card */
        .myntra-addon-card {
            border: 1px solid #eaeaec !important;
            border-radius: 12px !important;
            background: #fafbfc !important;
            padding: 16px 18px !important;
            margin: 18px 0 22px !important;
        }

        /* Cart Form & Dual CTA Buttons (ADD TO BAG + BUY NOW) */
        .summary.entry-summary form.cart.variations_form {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
        }
        .summary.entry-summary form.cart.variations_form .single_variation_wrap {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        /* Master Action Button Container (Flexbox with 14px gap) */
        .summary.entry-summary form.cart:not(.variations_form),
        .summary.entry-summary form.cart .woocommerce-variation-add-to-cart,
        .summary.entry-summary .woocommerce-variation-add-to-cart,
        .woocommerce-variation-add-to-cart {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
            margin: 22px 0 26px !important;
            width: 100% !important;
            box-sizing: border-box !important;
            clear: both !important;
        }

        /* Luxury Food-Grade PDP Quantity Stepper */
        .summary.entry-summary form.cart .quantity,
        .summary.entry-summary .woocommerce-variation-add-to-cart .quantity,
        .woocommerce-variation-add-to-cart .quantity {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            background: #FAF7F2 !important;
            border: 1.5px solid #D5C9BD !important;
            border-radius: 8px !important;
            height: 52px !important;
            padding: 0 2px !important;
            flex: 0 0 120px !important;
            max-width: 120px !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        }
        .summary.entry-summary form.cart .quantity:hover,
        .summary.entry-summary form.cart .quantity:focus-within {
            border-color: #3D2314 !important;
            box-shadow: 0 2px 8px rgba(61, 35, 20, 0.08) !important;
        }
        .summary.entry-summary form.cart .quantity .btn-pdp-qty {
            width: 36px !important;
            height: 100% !important;
            background: transparent !important;
            border: none !important;
            color: #3D2314 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            padding: 0 !important;
            margin: 0 !important;
            outline: none !important;
            transition: background 0.15s ease, color 0.15s ease, transform 0.1s ease !important;
            user-select: none !important;
            -webkit-user-select: none !important;
        }
        .summary.entry-summary form.cart .quantity .btn-pdp-qty:hover {
            background: #EDE4DA !important;
            color: #1F110B !important;
        }
        .summary.entry-summary form.cart .quantity .btn-pdp-qty:active {
            background: #DFD3C5 !important;
            transform: scale(0.92) !important;
        }
        .summary.entry-summary form.cart .quantity input.qty {
            width: 44px !important;
            height: 100% !important;
            border: none !important;
            background: transparent !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            color: #2C1810 !important;
            text-align: center !important;
            padding: 0 !important;
            margin: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            -moz-appearance: textfield !important;
        }
        .summary.entry-summary form.cart .quantity input.qty::-webkit-outer-spin-button,
        .summary.entry-summary form.cart .quantity input.qty::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }

        /* Primary Action 1: ADD TO BAG (Dark Cocoa Luxury) */
        .summary.entry-summary form.cart .single_add_to_cart_button,
        .summary.entry-summary .woocommerce-variation-add-to-cart .single_add_to_cart_button,
        .woocommerce-variation-add-to-cart .single_add_to_cart_button {
            flex: 1 1 0 !important;
            min-width: 150px !important;
            height: 52px !important;
            background: linear-gradient(135deg, #2B170C 0%, #150A05 100%) !important;
            color: #FFFFFF !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            letter-spacing: 0.8px !important;
            text-transform: uppercase !important;
            border-radius: 8px !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 4px 18px rgba(33, 16, 7, 0.28) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 9px !important;
            padding: 0 16px !important;
            margin: 0 !important;
            float: none !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
            position: relative !important;
            overflow: hidden !important;
            text-decoration: none !important;
        }
        .summary.entry-summary form.cart .single_add_to_cart_button:hover,
        .summary.entry-summary .woocommerce-variation-add-to-cart .single_add_to_cart_button:hover,
        .woocommerce-variation-add-to-cart .single_add_to_cart_button:hover {
            background: linear-gradient(135deg, #3A2011 0%, #201007 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 8px 25px rgba(33, 16, 7, 0.4) !important;
            color: #FFFFFF !important;
        }
        .summary.entry-summary form.cart .single_add_to_cart_button:active,
        .summary.entry-summary .woocommerce-variation-add-to-cart .single_add_to_cart_button:active,
        .woocommerce-variation-add-to-cart .single_add_to_cart_button:active {
            transform: translateY(0) scale(0.98) !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.4), 0 2px 8px rgba(33, 16, 7, 0.2) !important;
        }

        /* Primary Action 2: BUY NOW (Radiant Sunset Amber) */
        .myntra-btn-buy-now,
        .summary.entry-summary form.cart .myntra-btn-buy-now,
        .summary.entry-summary .woocommerce-variation-add-to-cart .myntra-btn-buy-now,
        .woocommerce-variation-add-to-cart .myntra-btn-buy-now {
            flex: 1 1 0 !important;
            min-width: 150px !important;
            height: 52px !important;
            background: linear-gradient(135deg, #FF6F00 0%, #E65100 100%) !important;
            color: #FFFFFF !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            letter-spacing: 0.8px !important;
            text-transform: uppercase !important;
            border-radius: 8px !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28), 0 4px 18px rgba(230, 81, 0, 0.35) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 9px !important;
            padding: 0 16px !important;
            margin: 0 !important;
            float: none !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
            position: relative !important;
            overflow: hidden !important;
            text-decoration: none !important;
        }
        .myntra-btn-buy-now:hover,
        .summary.entry-summary form.cart .myntra-btn-buy-now:hover,
        .summary.entry-summary .woocommerce-variation-add-to-cart .myntra-btn-buy-now:hover,
        .woocommerce-variation-add-to-cart .myntra-btn-buy-now:hover {
            background: linear-gradient(135deg, #FF7E14 0%, #F55800 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.38), 0 8px 25px rgba(230, 81, 0, 0.48) !important;
            color: #FFFFFF !important;
            filter: brightness(1.04) !important;
        }
        .myntra-btn-buy-now:active,
        .summary.entry-summary form.cart .myntra-btn-buy-now:active,
        .summary.entry-summary .woocommerce-variation-add-to-cart .myntra-btn-buy-now:active,
        .woocommerce-variation-add-to-cart .myntra-btn-buy-now:active {
            transform: translateY(0) scale(0.98) !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3), 0 2px 8px rgba(230, 81, 0, 0.25) !important;
        }

        /* Eradicate Wishlist Button per user directive */
        .myntra-btn-wishlist,
        .btn-wishlist {
            display: none !important;
            visibility: hidden !important;
            width: 0 !important;
            height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
        }

        /* Responsive Button Layout for Tablets & Mobile */
        @media (max-width: 768px) {
            .summary.entry-summary form.cart:not(.variations_form),
            .summary.entry-summary form.cart .woocommerce-variation-add-to-cart,
            .summary.entry-summary .woocommerce-variation-add-to-cart,
            .woocommerce-variation-add-to-cart {
                gap: 10px !important;
            }
            .summary.entry-summary form.cart .quantity,
            .summary.entry-summary .woocommerce-variation-add-to-cart .quantity,
            .woocommerce-variation-add-to-cart .quantity {
                flex: 0 0 105px !important;
                width: 105px !important;
                max-width: 105px !important;
                height: 50px !important;
            }
            .summary.entry-summary form.cart .single_add_to_cart_button,
            .summary.entry-summary .woocommerce-variation-add-to-cart .single_add_to_cart_button,
            .woocommerce-variation-add-to-cart .single_add_to_cart_button,
            .myntra-btn-buy-now {
                height: 50px !important;
                font-size: 14px !important;
                min-width: 120px !important;
                padding: 0 10px !important;
                gap: 6px !important;
            }
        }

        @media (max-width: 520px) {
            .summary.entry-summary form.cart:not(.variations_form),
            .summary.entry-summary form.cart .woocommerce-variation-add-to-cart,
            .summary.entry-summary .woocommerce-variation-add-to-cart,
            .woocommerce-variation-add-to-cart {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 10px !important;
                margin: 16px 0 24px !important;
                width: 100% !important;
            }
            .summary.entry-summary form.cart .quantity,
            .summary.entry-summary .woocommerce-variation-add-to-cart .quantity,
            .woocommerce-variation-add-to-cart .quantity {
                grid-column: 1 / -1 !important;
                flex: none !important;
                width: 100% !important;
                max-width: 100% !important;
                height: 48px !important;
                border-radius: 8px !important;
            }
            .summary.entry-summary form.cart .quantity .btn-pdp-qty,
            .summary.entry-summary .woocommerce-variation-add-to-cart .quantity .btn-pdp-qty,
            .woocommerce-variation-add-to-cart .quantity .btn-pdp-qty {
                width: 44px !important;
            }
            .summary.entry-summary form.cart .single_add_to_cart_button,
            .summary.entry-summary .woocommerce-variation-add-to-cart .single_add_to_cart_button,
            .woocommerce-variation-add-to-cart .single_add_to_cart_button {
                grid-column: 1 / 2 !important;
                width: 100% !important;
                min-width: 0 !important;
                height: 50px !important;
                font-size: 13.5px !important;
                letter-spacing: 0.5px !important;
                padding: 0 8px !important;
                gap: 6px !important;
            }
            .myntra-btn-buy-now,
            .summary.entry-summary form.cart .myntra-btn-buy-now,
            .summary.entry-summary .woocommerce-variation-add-to-cart .myntra-btn-buy-now,
            .woocommerce-variation-add-to-cart .myntra-btn-buy-now {
                grid-column: 2 / 3 !important;
                width: 100% !important;
                min-width: 0 !important;
                height: 50px !important;
                font-size: 13.5px !important;
                letter-spacing: 0.5px !important;
                padding: 0 8px !important;
                gap: 6px !important;
            }
        }

        /* Delivery Options & Pincode Checker */
        .myntra-delivery-container {
            border: 1px solid #eaeaec !important;
            border-radius: 12px !important;
            padding: 22px !important;
            margin: 24px 0 !important;
            background: #ffffff !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03) !important;
        }
        .myntra-delivery-title {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            margin-bottom: 14px !important;
        }
        .del-title-text {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #282c3f !important;
            letter-spacing: 0.6px !important;
            text-transform: uppercase !important;
        }
        .del-truck-icon {
            stroke: #282c3f !important;
        }
        .myntra-pincode-wrap {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            margin-bottom: 6px !important;
        }
        .myntra-pincode-input {
            flex: 1 !important;
            height: 42px !important;
            padding: 0 14px !important;
            border: 1.5px solid #d4d5d9 !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #282c3f !important;
            outline: none !important;
            transition: border-color 0.2s !important;
        }
        .myntra-pincode-input:focus {
            border-color: #282c3f !important;
        }
        .myntra-pincode-check-btn {
            height: 42px !important;
            padding: 0 20px !important;
            background: transparent !important;
            border: none !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #ff3e6c !important;
            text-transform: uppercase !important;
            cursor: pointer !important;
            letter-spacing: 0.5px !important;
        }
        .pincode-helper {
            font-size: 12px !important;
            color: #685e57 !important;
            margin: 0 0 14px !important;
        }
        .myntra-delivery-promises {
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            padding: 14px 0 !important;
            border-top: 1px solid #f2ede7 !important;
            border-bottom: 1px solid #f2ede7 !important;
            margin: 12px 0 16px !important;
        }
        .promise-item {
            display: flex !important;
            align-items: flex-start !important;
            gap: 12px !important;
        }
        .promise-icon {
            font-size: 18px !important;
            line-height: 1.2 !important;
        }
        .promise-copy {
            display: flex !important;
            flex-direction: column !important;
            font-size: 13px !important;
            color: #282c3f !important;
        }
        .promise-copy small {
            font-size: 11.5px !important;
            color: #8c7362 !important;
        }
        .myntra-brand-trust-list {
            display: flex !important;
            flex-direction: column !important;
            gap: 8px !important;
        }
        .trust-row {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            font-size: 12.5px !important;
            color: #282c3f !important;
            font-weight: 500 !important;
        }

        /* Best Offers Card */
        .myntra-best-offers-card {
            border: 1px dashed #d4d5d9 !important;
            border-radius: 10px !important;
            padding: 18px !important;
            margin: 20px 0 !important;
            background: #faf9f6 !important;
        }
        .offers-header {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            margin-bottom: 10px !important;
        }
        .offers-heading {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #282c3f !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
        }
        .offer-desc {
            font-size: 13px !important;
            color: #282c3f !important;
        }
        .highlight-price {
            color: #03a685 !important;
            font-weight: 700 !important;
        }
        .offer-terms {
            margin: 8px 0 0 !important;
            padding-left: 18px !important;
            color: #535665 !important;
            font-size: 12.5px !important;
            line-height: 1.6 !important;
        }
        .coupon-code-chip {
            display: inline-flex !important;
            align-items: center !important;
            background: #ffffff !important;
            border: 1px solid #C59B27 !important;
            color: #3D2314 !important;
            font-weight: 800 !important;
            font-size: 12px !important;
            padding: 2px 8px !important;
            border-radius: 4px !important;
            letter-spacing: 0.5px !important;
            margin: 0 6px !important;
        }
        .btn-copy-code {
            padding: 3px 8px !important;
            background: #3D2314 !important;
            color: #fff !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            border: none !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            text-transform: uppercase !important;
            transition: background 0.2s !important;
        }

        /* Product Details & Specifications Grid */
        .myntra-details-section {
            margin-top: 26px !important;
            padding-top: 20px !important;
            border-top: 1px solid #eaeaec !important;
        }
        .myntra-section-title {
            font-size: 14.5px !important;
            font-weight: 700 !important;
            color: #282c3f !important;
            letter-spacing: 0.6px !important;
            text-transform: uppercase !important;
            margin: 0 0 10px !important;
        }
        .myntra-product-desc {
            font-size: 14px !important;
            line-height: 1.6 !important;
            color: #535665 !important;
            margin-bottom: 20px !important;
        }
        .myntra-sub-title {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #282c3f !important;
            margin: 18px 0 12px !important;
        }
        .myntra-specs-table {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 16px 24px !important;
            padding-top: 8px !important;
        }
        .spec-cell {
            display: flex !important;
            flex-direction: column !important;
            gap: 4px !important;
            border-bottom: 1px solid #f2ede7 !important;
            padding-bottom: 12px !important;
        }
        .spec-label {
            font-size: 11.5px !important;
            color: #8c7362 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
            font-weight: 600 !important;
        }
        .spec-val {
            font-size: 13.5px !important;
            color: #282c3f !important;
            font-weight: 600 !important;
        }

        /* Sticky Bottom Bar Brand Tag */
        .sticky-product-brand {
            font-size: 10px !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px !important;
            color: #8c7362 !important;
            text-transform: uppercase !important;
            display: block !important;
        }

        /* Woodmart Architecture: Luxury Dark Steps Banner (SHOPPING CART -> CHECKOUT -> ORDER COMPLETE) */
        .woodmart-steps-dark-banner,
        .neebites-checkout-steps-wrapper.woodmart-steps-dark-banner,
        .neebites-checkout-steps-wrapper {
            background: #1B3B2B !important;
            padding: 34px 0 !important;
            margin-bottom: 35px !important;
            border-bottom: none !important;
            width: 100% !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1) !important;
        }
        .woodmart-steps-dark-banner .wd-checkout-steps,
        .wd-checkout-steps {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 0 !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
            font-family: 'Outfit', sans-serif !important;
        }
        .wd-checkout-steps li {
            display: inline-flex !important;
            align-items: center !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            letter-spacing: 1.5px !important;
            text-transform: uppercase !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .wd-checkout-steps li > :is(a, span) {
            color: rgba(255, 255, 255, 0.6) !important;
            text-decoration: none !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            letter-spacing: 1.5px !important;
            transition: all 0.2s ease !important;
        }
        .wd-checkout-steps li.step-runtitle a:hover {
            color: #FFFFFF !important;
        }
        .wd-checkout-steps li.step-active > span {
            color: #FFFFFF !important;
            font-weight: 700 !important;
            border-bottom: 2px solid #FFFFFF !important;
            padding-bottom: 3px !important;
        }
        .wd-checkout-steps .step-arrow {
            color: rgba(255, 255, 255, 0.4) !important;
            margin: 0 16px !important;
            font-size: 16px !important;
        }
        @media (max-width: 768px) {
            .woodmart-steps-dark-banner,
            .neebites-checkout-steps-wrapper {
                padding: 22px 0 16px !important;
                margin-bottom: 20px !important;
            }
            .wd-checkout-steps {
                font-size: 13px !important;
            }
            .wd-checkout-steps li {
                font-size: 13px !important;
            }
            .wd-checkout-steps li > :is(a, span) {
                font-size: 13px !important;
            }
            .wd-checkout-steps .step-arrow {
                margin: 0 8px !important;
            }
            .wd-checkout-steps .step-inactive {
                display: none !important;
            }
        }

        /* Free Shipping Goal Box with Dashed Border (Reference Photo) */
        .woodmart-free-shipping-box {
            border: 1px dashed #D5C9BD !important;
            background: #FAF8F5 !important;
            border-radius: 6px !important;
            padding: 16px 22px !important;
            margin-bottom: 30px !important;
        }
        .woodmart-free-shipping-box .neebites-free-shipping-bar {
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }
        .woodmart-free-shipping-box .shipping-progress-track {
            height: 8px !important;
            background: #EAE0D5 !important;
            border-radius: 4px !important;
            margin-top: 8px !important;
        }
        .woodmart-free-shipping-box .shipping-progress-fill {
            background: #1B3B2B !important;
            border-radius: 4px !important;
            height: 100% !important;
        }

        /* Woodmart Fashion-2 Architecture: Centered Page Container & Max Widths */
        .wp-block-woocommerce-checkout,
        .wc-block-checkout,
        .woodmart-checkout-wrapper,
        .woocommerce-checkout .page-wrapper,
        .woocommerce-checkout .neebites-checkout-page-content,
        .woocommerce-checkout .woocommerce-notices-wrapper,
        .woocommerce-checkout .woocommerce-form-coupon-toggle,
        .woocommerce-checkout .woocommerce-form-coupon,
        .woocommerce-checkout .woocommerce-form-login-toggle,
        .woocommerce-checkout .woocommerce-form-login {
            max-width: 1200px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding-left: 20px !important;
            padding-right: 20px !important;
            box-sizing: border-box !important;
        }

        /* Top Coupon Toggle & Alerts (Woodmart Minimalist Clean Design — NO UGLY YELLOW BORDER) */
        .woocommerce-checkout .woocommerce-notices-wrapper {
            margin-bottom: 14px !important;
        }
        .woocommerce-checkout .woocommerce-info,
        .woocommerce-checkout .woocommerce-message,
        .woocommerce-checkout .woocommerce-error,
        .woocommerce-form-coupon-toggle .woocommerce-info {
            background: #FAF8F5 !important;
            border: 1px dashed #D5C9BD !important;
            border-top: 1px dashed #D5C9BD !important;
            border-radius: 6px !important;
            padding: 12px 18px !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #4A3A2C !important;
            line-height: 1.5 !important;
            margin: 0 0 16px 0 !important;
            box-shadow: none !important;
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 6px !important;
        }
        .woocommerce-checkout .woocommerce-info::before,
        .woocommerce-checkout .woocommerce-message::before,
        .woocommerce-checkout .woocommerce-error::before,
        .woocommerce-form-coupon-toggle .woocommerce-info::before {
            display: none !important;
            content: none !important;
        }
        .woocommerce-form-coupon-toggle .woocommerce-info a.showcoupon,
        .woocommerce-checkout .woocommerce-info a {
            color: #1B3B2B !important;
            font-weight: 700 !important;
            text-decoration: underline !important;
            cursor: pointer !important;
            transition: color 0.15s ease !important;
        }
        .woocommerce-form-coupon-toggle .woocommerce-info a.showcoupon:hover,
        .woocommerce-checkout .woocommerce-info a:hover {
            color: #0F2218 !important;
        }

        /* Expanded Coupon Form */
        form.checkout_coupon {
            background: #FAF8F5 !important;
            border: 1px solid #D5C9BD !important;
            border-radius: 6px !important;
            padding: 18px 20px !important;
            margin: 0 0 24px 0 !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
            align-items: center !important;
            max-width: 480px !important;
        }
        form.checkout_coupon p {
            margin: 0 !important;
        }
        form.checkout_coupon input.input-text {
            height: 44px !important;
            border: 1px solid #D5C9BD !important;
            border-radius: 4px !important;
            padding: 0 14px !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 13.5px !important;
            color: #242424 !important;
            width: 220px !important;
            box-sizing: border-box !important;
        }
        form.checkout_coupon button.button {
            height: 44px !important;
            background: #1B3B2B !important;
            color: #FFFFFF !important;
            border: none !important;
            border-radius: 4px !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
            padding: 0 20px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }
        form.checkout_coupon button.button:hover {
            background: #142B20 !important;
        }

        /* Free Shipping Goal Indicator */
        .woodmart-free-shipping-box {
            background: #FFFFFF !important;
            border: 1px solid #EAE0D5 !important;
            border-radius: 6px !important;
            padding: 14px 20px !important;
            margin-bottom: 24px !important;
            box-shadow: 0 1px 4px rgba(43, 23, 4, 0.03) !important;
        }
        .woodmart-free-shipping-box .neebites-free-shipping-bar {
            margin: 0 !important;
            padding: 0 !important;
        }
        .woodmart-free-shipping-box .shipping-goal-text {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #1B3B2B !important;
        }
        .woodmart-free-shipping-box .shipping-progress-track {
            height: 6px !important;
            background: #ECE4DA !important;
            border-radius: 3px !important;
            margin-top: 8px !important;
            overflow: hidden !important;
        }
        .woodmart-free-shipping-box .shipping-progress-fill {
            background: #1B3B2B !important;
            border-radius: 3px !important;
            height: 100% !important;
        }

        /* Two-Column Flex Layout */
        .wc-block-components-sidebar-layout,
        .woocommerce-checkout form.checkout,
        .woodmart-checkout-form {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            gap: 30px !important;
            align-items: flex-start !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .wc-block-components-sidebar-layout .wc-block-components-main,
        .wc-block-checkout__main,
        .woodmart-checkout-left,
        .woocommerce-checkout #customer_details {
            flex: 1 1 0 !important;
            max-width: calc(100% - 430px) !important;
            min-width: 0 !important;
            width: 100% !important;
            padding-right: 0 !important;
            box-sizing: border-box !important;
        }
        .wc-block-components-sidebar-layout .wc-block-components-sidebar,
        .wc-block-checkout__sidebar,
        .woodmart-checkout-right,
        .woocommerce-checkout #order_review {
            flex: 0 0 400px !important;
            width: 400px !important;
            max-width: 400px !important;
            padding-left: 0 !important;
            box-sizing: border-box !important;
            position: sticky !important;
            top: 96px !important;
            align-self: flex-start !important;
        }

        /* Section Headings */
        .woocommerce-billing-fields h3,
        .woocommerce-shipping-fields h3,
        .woocommerce-additional-fields h3 {
            font-family: 'Outfit', sans-serif !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            color: #242424 !important;
            letter-spacing: 0.8px !important;
            text-transform: uppercase !important;
            margin: 0 0 16px 0 !important;
            padding-bottom: 12px !important;
            border-bottom: 1px solid #EAE0D5 !important;
            line-height: 1.3 !important;
        }

        /* Form Row Gaps & Floats */
        .woodmart-checkout-form .form-row {
            margin-bottom: 14px !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }
        .woodmart-checkout-form .form-row-first,
        .woodmart-checkout-form .form-row-last {
            width: calc(50% - 8px) !important;
            float: left !important;
            box-sizing: border-box !important;
        }
        .woodmart-checkout-form .form-row-first {
            margin-right: 16px !important;
            clear: left !important;
        }
        .woodmart-checkout-form .form-row-last {
            margin-right: 0 !important;
            clear: none !important;
        }
        .woodmart-checkout-form .form-row-wide {
            width: 100% !important;
            clear: both !important;
            box-sizing: border-box !important;
        }
        .woodmart-checkout-form .woocommerce-billing-fields__field-wrapper::after,
        .woodmart-checkout-form .woocommerce-shipping-fields__field-wrapper::after {
            content: \'\' !important;
            display: table !important;
            clear: both !important;
        }

        /* Labels Above Inputs */
        .woodmart-checkout-form .form-row label,
        .woocommerce-checkout .form-row label {
            display: block !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #242424 !important;
            margin-bottom: 5px !important;
            line-height: 1.3 !important;
            letter-spacing: 0.1px !important;
        }
        .woodmart-checkout-form .form-row label abbr.required,
        .woocommerce-checkout .form-row label abbr.required {
            color: #D32F2F !important;
            text-decoration: none !important;
            border: none !important;
            font-weight: 700 !important;
            margin-left: 2px !important;
        }

        /* Crisp 44px Field Boxes */
        .woodmart-checkout-form input.input-text,
        .woodmart-checkout-form select,
        .woocommerce-checkout input.input-text,
        .woocommerce-checkout select {
            background-color: #FFFFFF !important;
            border: 1px solid #D5C9BD !important;
            border-radius: 4px !important;
            height: 44px !important;
            min-height: 44px !important;
            padding: 0 14px !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #242424 !important;
            box-shadow: none !important;
            box-sizing: border-box !important;
            width: 100% !important;
            outline: none !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        }
        .woodmart-checkout-form input.input-text::placeholder,
        .woocommerce-checkout input.input-text::placeholder {
            color: #8C7A6B !important;
            font-weight: 400 !important;
        }
        .woodmart-checkout-form input.input-text:focus,
        .woodmart-checkout-form select:focus,
        .woocommerce-checkout input.input-text:focus,
        .woocommerce-checkout select:focus {
            border-color: #1B3B2B !important;
            box-shadow: 0 0 0 2px rgba(27, 59, 43, 0.12) !important;
            background-color: #FFFFFF !important;
            outline: none !important;
        }
        .woodmart-checkout-form textarea,
        .woocommerce-checkout textarea {
            background-color: #FFFFFF !important;
            border: 1px solid #D5C9BD !important;
            border-radius: 4px !important;
            padding: 12px 14px !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #242424 !important;
            box-shadow: none !important;
            width: 100% !important;
            min-height: 90px !important;
            box-sizing: border-box !important;
            outline: none !important;
        }
        .woodmart-checkout-form textarea:focus,
        .woocommerce-checkout textarea:focus {
            border-color: #1B3B2B !important;
            box-shadow: 0 0 0 2px rgba(27, 59, 43, 0.12) !important;
        }

        /* Select2 Dropdown Perfection */
        .woodmart-checkout-form .select2-container,
        .woocommerce-checkout .select2-container {
            width: 100% !important;
        }
        .woodmart-checkout-form .select2-container--default .select2-selection--single,
        .woocommerce-checkout .select2-container--default .select2-selection--single {
            background-color: #FFFFFF !important;
            border: 1px solid #D5C9BD !important;
            border-radius: 4px !important;
            height: 44px !important;
            min-height: 44px !important;
            display: flex !important;
            align-items: center !important;
            box-sizing: border-box !important;
            outline: none !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        }
        .woodmart-checkout-form .select2-container--default.select2-container--open .select2-selection--single,
        .woodmart-checkout-form .select2-container--default.select2-container--focus .select2-selection--single,
        .woocommerce-checkout .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #1B3B2B !important;
            box-shadow: 0 0 0 2px rgba(27, 59, 43, 0.12) !important;
        }
        .woodmart-checkout-form .select2-container--default .select2-selection--single .select2-selection__rendered,
        .woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 14px !important;
            padding-right: 36px !important;
            line-height: 42px !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #242424 !important;
        }
        .woodmart-checkout-form .select2-container--default .select2-selection--single .select2-selection__arrow,
        .woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
            width: 36px !important;
            top: 0 !important;
            right: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .woodmart-checkout-form .select2-container--default .select2-selection--single .select2-selection__arrow b,
        .woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #5C4434 transparent transparent transparent !important;
            border-width: 5px 4px 0 4px !important;
        }

        /* Order Review Card (Clean Outer Shell, NO Inner Nested Box) */
        .woodmart-order-review-card {
            background-color: #FAF8F5 !important;
            border: 1px solid #EAE0D5 !important;
            border-radius: 10px !important;
            padding: 26px 22px !important;
            box-shadow: 0 6px 20px rgba(43, 23, 4, 0.04) !important;
            box-sizing: border-box !important;
            width: 100% !important;
        }
        /* Crucial: Prevent duplicate card/border on inner #order_review */
        .woodmart-order-review-card #order_review,
        .woodmart-order-review-card .woocommerce-checkout-review-order {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            width: 100% !important;
        }

        /* Centered Uppercase Order Heading */
        #order_review_heading,
        .woodmart-order-heading {
            font-family: 'Outfit', sans-serif !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #242424 !important;
            letter-spacing: 1.2px !important;
            text-transform: uppercase !important;
            text-align: center !important;
            display: block !important;
            margin: 0 0 18px 0 !important;
            padding-bottom: 14px !important;
            border-bottom: 1px solid #EAE0D5 !important;
        }

        /* Receipt Review Table */
        .woodmart-review-table {
            width: 100% !important;
            border-collapse: collapse !important;
            border: none !important;
            margin: 0 0 16px 0 !important;
        }
        .woodmart-review-table thead th {
            font-family: 'Outfit', sans-serif !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            letter-spacing: 0.8px !important;
            text-transform: uppercase !important;
            color: #888888 !important;
            padding: 0 0 10px 0 !important;
            border-bottom: 1px solid #ECE4DA !important;
        }
        .woodmart-review-table thead th.product-name {
            text-align: left !important;
        }
        .woodmart-review-table thead th.product-total {
            text-align: right !important;
        }
        .woodmart-review-table tbody tr.cart_item {
            border-bottom: 1px solid #ECE4DA !important;
        }
        .woodmart-review-table tbody td {
            padding: 12px 0 !important;
            vertical-align: middle !important;
            border: none !important;
        }
        .woodmart-review-item-flex {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }
        .woodmart-review-thumb-wrap {
            width: 52px !important;
            height: 52px !important;
            flex-shrink: 0 !important;
            border-radius: 4px !important;
            overflow: hidden !important;
            border: 1px solid #EAE0D5 !important;
            background: #FFFFFF !important;
        }
        .woodmart-cart-thumb {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            display: block !important;
        }
        .woodmart-review-content {
            flex: 1 1 auto !important;
            min-width: 0 !important;
        }
        .woodmart-review-title {
            font-family: 'Outfit', sans-serif !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #242424 !important;
            line-height: 1.35 !important;
            margin-bottom: 4px !important;
            word-break: break-word !important;
        }

        /* Polished Inline Quantity Stepper Box [-] [qty] [+] */
        .woodmart-qty-stepper-box {
            display: inline-flex !important;
            align-items: center !important;
            border: 1px solid #D5C9BD !important;
            border-radius: 3px !important;
            background: #FFFFFF !important;
            overflow: hidden !important;
            height: 26px !important;
            vertical-align: middle !important;
            margin-top: 4px !important;
        }
        .woodmart-qty-btn {
            width: 24px !important;
            height: 26px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #555555 !important;
            background: #F7F4F0 !important;
            border: none !important;
            cursor: pointer !important;
            -webkit-user-select: none !important;
            user-select: none !important;
            transition: all 0.15s ease !important;
        }
        .woodmart-qty-btn:hover {
            background: #1B3B2B !important;
            color: #FFFFFF !important;
        }
        .woodmart-qty-btn:active {
            background: #0F2218 !important;
            color: #FFFFFF !important;
            transform: scale(0.92) !important;
        }
        .woodmart-qty-val {
            width: 28px !important;
            text-align: center !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 12.5px !important;
            font-weight: 700 !important;
            color: #242424 !important;
            line-height: 26px !important;
            background: #FFFFFF !important;
        }
        .woodmart-review-table td.product-total {
            font-family: 'Outfit', sans-serif !important;
            font-size: 14.5px !important;
            font-weight: 700 !important;
            color: #242424 !important;
            text-align: right !important;
            white-space: nowrap !important;
        }

        /* Totals Rows */
        .woodmart-review-table tfoot tr {
            border-bottom: 1px solid #ECE4DA !important;
        }
        .woodmart-review-table tfoot th {
            font-family: 'Outfit', sans-serif !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #555555 !important;
            padding: 10px 0 !important;
            text-align: left !important;
            border: none !important;
        }
        .woodmart-review-table tfoot td {
            font-family: 'Outfit', sans-serif !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #242424 !important;
            padding: 10px 0 !important;
            text-align: right !important;
            border: none !important;
        }
        .woodmart-review-table tfoot tr.order-total {
            border-top: 1.5px solid #242424 !important;
            border-bottom: none !important;
        }
        .woodmart-review-table tfoot tr.order-total th {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #242424 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            padding: 14px 0 8px 0 !important;
        }
        .woodmart-review-table tfoot tr.order-total td {
            font-size: 20px !important;
            font-weight: 800 !important;
            color: #1B3B2B !important;
            padding: 14px 0 8px 0 !important;
        }

        /* Payment Methods & Cash On Delivery Box */
        #payment {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            margin-top: 16px !important;
        }
        #payment ul.payment_methods {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 0 16px 0 !important;
            border-top: 1px solid #ECE4DA !important;
            border-bottom: 1px solid #ECE4DA !important;
        }
        #payment ul.payment_methods li {
            padding: 12px 0 !important;
            border-bottom: 1px solid #ECE4DA !important;
            border-radius: 0 !important;
            background: transparent !important;
            margin: 0 !important;
            display: block !important;
        }
        #payment ul.payment_methods li:last-child {
            border-bottom: none !important;
        }
        #payment ul.payment_methods li input[type=\'radio\'] {
            accent-color: #1B3B2B !important;
            width: 16px !important;
            height: 16px !important;
            vertical-align: middle !important;
            margin: 0 8px 0 0 !important;
            cursor: pointer !important;
        }
        #payment ul.payment_methods li label {
            display: inline !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #242424 !important;
            cursor: pointer !important;
            vertical-align: middle !important;
            margin: 0 !important;
        }
        #payment div.payment_box {
            background: #FFFFFF !important;
            border: 1px solid #EAE0D5 !important;
            border-radius: 4px !important;
            padding: 12px 14px !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
            color: #5C4A3E !important;
            margin: 10px 0 4px 0 !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02) !important;
        }
        #payment div.payment_box p {
            margin: 0 !important;
        }
        #payment div.payment_box::before {
            display: none !important;
        }
        #payment div.form-row.place-order {
            padding: 0 !important;
            margin: 16px 0 0 0 !important;
            float: none !important;
        }

        /* Place Order Button — High-Impact Deep Forest Green */
        #place_order,
        .woodmart-checkout-form button[type=\'submit\']#place_order {
            background: #1B3B2B !important;
            color: #FFFFFF !important;
            font-family: 'Outfit', sans-serif !important;
            font-weight: 700 !important;
            font-size: 15px !important;
            letter-spacing: 1px !important;
            text-transform: uppercase !important;
            border: none !important;
            border-radius: 4px !important;
            padding: 0 24px !important;
            width: 100% !important;
            height: 50px !important;
            min-height: 50px !important;
            box-shadow: 0 4px 14px rgba(27, 59, 43, 0.25) !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-top: 14px !important;
        }
        #place_order:hover,
        .woodmart-checkout-form button[type=\'submit\']#place_order:hover {
            background: #142B20 !important;
            box-shadow: 0 6px 18px rgba(27, 59, 43, 0.35) !important;
            transform: translateY(-1px) !important;
        }

        /* -------------------------------------------------------------
         * MOBILE & TABLET RESPONSIVENESS (100% FLUID & TOUCH FRIENDLY)
         * ------------------------------------------------------------- */
        @media (max-width: 992px) {
            .wc-block-components-sidebar-layout,
            .woocommerce-checkout form.checkout,
            .woodmart-checkout-form {
                flex-direction: column !important;
                gap: 24px !important;
            }
            .wc-block-components-sidebar-layout .wc-block-components-main,
            .wc-block-checkout__main,
            .woodmart-checkout-left,
            .woocommerce-checkout #customer_details {
                max-width: 100% !important;
                width: 100% !important;
                flex: 1 1 100% !important;
            }
            .wc-block-components-sidebar-layout .wc-block-components-sidebar,
            .wc-block-checkout__sidebar,
            .woodmart-checkout-right,
            .woocommerce-checkout #order_review {
                max-width: 100% !important;
                width: 100% !important;
                flex: 1 1 100% !important;
                position: static !important;
                top: 0 !important;
            }
        }

        @media (max-width: 768px) {
            .woodmart-steps-dark-banner {
                padding: 14px 16px !important;
            }
            .wd-checkout-steps {
                font-size: 13.5px !important;
                gap: 8px 12px !important;
            }
            .wd-checkout-steps .step-inactive {
                display: none !important;
            }
            .woodmart-checkout-wrapper,
            .woocommerce-checkout .page-wrapper,
            .woocommerce-checkout .woocommerce-notices-wrapper,
            .woocommerce-checkout .woocommerce-form-coupon-toggle {
                padding-left: 14px !important;
                padding-right: 14px !important;
            }
            .woodmart-checkout-form .form-row-first,
            .woodmart-checkout-form .form-row-last {
                width: 100% !important;
                float: none !important;
                margin-right: 0 !important;
                margin-bottom: 12px !important;
            }
            .woodmart-checkout-form input.input-text,
            .woodmart-checkout-form select,
            .woocommerce-checkout input.input-text,
            .woocommerce-checkout select,
            .woodmart-checkout-form .select2-container--default .select2-selection--single {
                height: 46px !important;
                min-height: 46px !important;
                font-size: 14px !important;
            }
            .woodmart-checkout-form .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 44px !important;
            }
            .woodmart-order-review-card {
                padding: 20px 16px !important;
            }
            .woodmart-qty-stepper-box {
                height: 28px !important;
            }
            .woodmart-qty-btn {
                width: 28px !important;
                height: 28px !important;
                font-size: 15px !important;
            }
            .woodmart-qty-val {
                width: 30px !important;
                font-size: 13px !important;
                line-height: 28px !important;
            }
            #place_order {
                height: 52px !important;
                min-height: 52px !important;
                font-size: 15px !important;
            }
        }
    ";
    // WooCommerce & Botanical E-Commerce Grid CSS
    wp_enqueue_style('neebites-woocommerce', NEEBITES_ASSETS_URI . '/css/woocommerce.css?v=' . NEEBITES_VERSION, ['neebites-main'], null);
    wp_add_inline_style('neebites-woocommerce', $critical_resets);
    wp_add_inline_style('neebites-main', $critical_resets);

    // Main Theme JS with explicit cache-busting query parameter
    wp_enqueue_script('neebites-main', NEEBITES_ASSETS_URI . '/js/main.js?v=' . NEEBITES_VERSION, [], null, true);

    // Localize Script for AJAX & Translation Strings
    $free_threshold = (float) neebites_get_option('free_shipping_threshold', 50);
    wp_localize_script('neebites-main', 'neebitesAjax', [
        'ajaxurl'               => admin_url('admin-ajax.php'),
        'nonce'                 => wp_create_nonce('neebites_nonce'),
        'freeShippingThreshold' => $free_threshold,
        'checkoutUrl'           => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/'),
        'currencySymbol'        => function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : '$',
        'strings'               => [
            'loading'        => esc_html__('Loading...', 'neebites'),
            'addToCart'      => esc_html__('Add to Cart', 'neebites'),
            'addedToCart'    => esc_html__('Added to Basket! 🍫', 'neebites'),
            'viewCart'       => esc_html__('View Basket', 'neebites'),
            'quickView'      => esc_html__('Quick View', 'neebites'),
            'wishlistAdd'    => esc_html__('Added to Wishlist', 'neebites'),
            'wishlistRemove' => esc_html__('Removed from Wishlist', 'neebites'),
            'freeShipping'   => esc_html__('You unlocked FREE shipping! 🎉', 'neebites'),
            'resultsCount'   => esc_html__('Showing %1$s of %2$s confections', 'neebites'),
            'noResultsTitle' => esc_html__('No confections match these filters', 'neebites'),
            'noResultsText'  => esc_html__('Try clearing your cocoa profile or flavour filters to explore all chocolate varieties.', 'neebites'),
            'resetFilters'   => esc_html__('Reset All Filters', 'neebites'),
        ],
    ]);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'neebites_enqueue_assets');

/**
 * Enqueue Editor Assets
 */
function neebites_enqueue_editor_assets() {
    wp_enqueue_style('neebites-editor', NEEBITES_ASSETS_URI . '/css/editor-style.css', [], NEEBITES_VERSION);
}
add_action('enqueue_block_editor_assets', 'neebites_enqueue_editor_assets');

// -------------------------------------------------------------
// Load Modular Theme Files
// -------------------------------------------------------------
// 1. Core Functions
foreach (glob(NEEBITES_INC_DIR . '/core/*.php') as $file) {
    require_once $file;
}

// 2. WooCommerce Customizations
if (neebites_is_woocommerce_active()) {
    foreach (glob(NEEBITES_INC_DIR . '/woocommerce/*.php') as $file) {
        require_once $file;
    }

    // Ensure Myntra-grade product image gallery & luxury Related Products template overrides are always loaded
    add_filter('wc_get_template', function($located, $template_name, $args, $template_path, $default_path) {
        if ($template_name === 'single-product/product-image.php') {
            $custom = get_template_directory() . '/woocommerce/single-product/product-image.php';
            if (file_exists($custom)) {
                return $custom;
            }
        } elseif ($template_name === 'single-product/related.php') {
            $custom = get_template_directory() . '/woocommerce/single-product/related.php';
            if (file_exists($custom)) {
                return $custom;
            }
        }
        return $located;
    }, 99, 5);

    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    add_action('woocommerce_before_single_product_summary', 'neebites_render_myntra_product_gallery', 20);

    if (!function_exists('neebites_render_myntra_product_gallery')) {
        function neebites_render_myntra_product_gallery() {
            global $product;
            if (!$product && function_exists('wc_get_product')) {
                $product = wc_get_product(get_the_ID());
            }
            $custom = get_template_directory() . '/woocommerce/single-product/product-image.php';
            if (file_exists($custom)) {
                include $custom;
            } elseif (function_exists('woocommerce_show_product_images')) {
                woocommerce_show_product_images();
            }
        }
    }

    // Modernize Related Products heading and guarantee 4 columns
    add_filter('woocommerce_product_related_products_heading', function() {
        return __('Artisan Confections You’ll Love', 'neebites');
    });

    add_filter('woocommerce_output_related_products_args', function($args) {
        $args['posts_per_page'] = 4;
        $args['columns']        = 4;
        return $args;
    });
} else {
    // In demo preview mode without WooCommerce, load safe ajax handlers (wishlist & quick view)
    if (file_exists(NEEBITES_INC_DIR . '/woocommerce/ajax-actions.php')) {
        require_once NEEBITES_INC_DIR . '/woocommerce/ajax-actions.php';
    }
}

// 3. Customizer
foreach (glob(NEEBITES_INC_DIR . '/customizer/*.php') as $file) {
    require_once $file;
}

// 4. Block Patterns
foreach (glob(NEEBITES_INC_DIR . '/blocks/*.php') as $file) {
    require_once $file;
}

// 5. Performance & Optimizations
foreach (glob(NEEBITES_INC_DIR . '/optimization/*.php') as $file) {
    require_once $file;
}

// 6. 1-Click Demo Importer
if (file_exists(NEEBITES_INC_DIR . '/demo-importer.php')) {
    require_once NEEBITES_INC_DIR . '/demo-importer.php';
}

/**
 * Auto-Migrate Legacy Database Theme Mods to Artisan Chocolatier Defaults
 */
add_action('after_setup_theme', function() {
    delete_option('neebites_theme_options');
    $theme_slug = get_stylesheet();
    $mods = get_option('theme_mods_' . $theme_slug);
    if (!is_array($mods)) {
        $mods = [];
    }
    $mods['primary_color']       = '#3D2314';
    $mods['primary_color_light'] = '#6B4226';
    $mods['primary_color_dark']  = '#231205';
    $mods['accent_color']        = '#C59B27';
    $mods['sale_color']          = '#C0392B';
    $mods['text_color']          = '#241408';
    $mods['text_color_medium']   = '#5C4434';
    $mods['text_color_light']    = '#8C7362';
    $mods['border_color']        = '#EAE0D5';
    $mods['bg_color_light']      = '#FAF6F0';
    $mods['footer_copyright']    = '&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. Luxury Artisan Chocolatier & Confectionery.';
    $mods['ticker_message']      = 'Free cold-pack delivery on orders over ₹500 | 100% Pure Cocoa Butter Guarantee | Direct from Artisan Chocolatier';
    update_option('theme_mods_' . $theme_slug, $mods);
}, 20);

/**
 * Robust Shop Category Query Handler
 * Guarantees that query parameters like ?product_cat=milk-chocolate-almond always display confections
 */
add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query() && (function_exists('is_shop') && (is_shop() || is_product_taxonomy()))) {
        $cat = $query->get('product_cat');
        if (!empty($cat)) {
            $term = get_term_by('slug', $cat, 'product_cat');
            $count = ($term && !is_wp_error($term)) ? (int)$term->count : 0;
            
            if ($count === 0) {
                // Extract clean search term (e.g., 'milk' from 'milk-chocolate-almond')
                $search_keyword = str_replace(['-chocolate-almond', '-almond', '-chocolate'], '', $cat);
                $search_keyword = trim(str_replace('-', ' ', $search_keyword));
                
                $matched_ids = [];
                if (!empty($search_keyword)) {
                    $matched_ids = get_posts([
                        'post_type'      => 'product',
                        'post_status'    => 'publish',
                        'posts_per_page' => 12,
                        'fields'         => 'ids',
                        's'              => $search_keyword,
                    ]);
                }
                
                if (!empty($matched_ids)) {
                    $query->set('product_cat', '');
                    $query->set('tax_query', []);
                    $query->set('post__in', $matched_ids);
                } else {
                    $fallback = get_term_by('slug', 'chocolate-coated-almonds', 'product_cat');
                    if (!$fallback) {
                        $fallback = get_term_by('slug', 'chocolates-confectionery', 'product_cat');
                    }
                    if ($fallback) {
                        $query->set('product_cat', $fallback->slug);
                        $query->set('tax_query', [
                            [
                                'taxonomy'         => 'product_cat',
                                'field'            => 'slug',
                                'terms'            => $fallback->slug,
                                'include_children' => true,
                            ],
                        ]);
                    } else {
                        $query->set('product_cat', '');
                        $query->set('tax_query', []);
                    }
                }
            } else {
                $query->set('tax_query', [
                    [
                        'taxonomy'         => 'product_cat',
                        'field'            => 'slug',
                        'terms'            => $term->slug,
                        'include_children' => true,
                    ],
                ]);
            }
        }
    }
}, 5);

