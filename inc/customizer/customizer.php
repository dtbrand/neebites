<?php
/**
 * Theme Customizer Settings
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Register Customizer Panels, Sections, and Controls
 */
function neebites_customize_register($wp_customize) {
    // Main Panel
    $wp_customize->add_panel('neebites_theme_options_panel', [
        'title'       => esc_html__('Neebites Theme Options', 'neebites'),
        'description' => esc_html__('Configure branding, colors, typography, shop options, and performance.', 'neebites'),
        'priority'    => 20,
    ]);

    // -------------------------------------------------------------
    // 1. Header & Announcement Bar Section
    // -------------------------------------------------------------
    $wp_customize->add_section('neebites_header_section', [
        'title'    => esc_html__('Header & Announcement Bar', 'neebites'),
        'panel'    => 'neebites_theme_options_panel',
        'priority' => 10,
    ]);

    // Shipping Bar Enabled
    $wp_customize->add_setting('neebites_theme_options[shipping_bar_enabled]', [
        'default'           => true,
        'type'              => 'option',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'neebites_sanitize_checkbox',
    ]);
    $wp_customize->add_control('shipping_bar_enabled', [
        'label'       => esc_html__('Enable Announcement / Shipping Bar', 'neebites'),
        'section'     => 'neebites_header_section',
        'settings'    => 'neebites_theme_options[shipping_bar_enabled]',
        'type'        => 'checkbox',
    ]);

    // Free Shipping Threshold
    $wp_customize->add_setting('neebites_theme_options[free_shipping_threshold]', [
        'default'           => 50,
        'type'              => 'option',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('free_shipping_threshold', [
        'label'       => esc_html__('Free Shipping Goal Threshold ($)', 'neebites'),
        'description' => esc_html__('Amount needed for customers to unlock free shipping in the mini-cart progress meter.', 'neebites'),
        'section'     => 'neebites_header_section',
        'settings'    => 'neebites_theme_options[free_shipping_threshold]',
        'type'        => 'number',
    ]);

    // Top Bar Messages (separated by |)
    $wp_customize->add_setting('neebites_theme_options[top_bar_text]', [
        'default'           => 'Free cold-pack delivery on orders over ₹500 | 100% Pure Cocoa Butter Guarantee | Direct from Artisan Chocolatier',
        'type'              => 'option',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('top_bar_text', [
        'label'       => esc_html__('Announcement Ticker Messages', 'neebites'),
        'description' => esc_html__('Separate ticker messages using a pipe character (|).', 'neebites'),
        'section'     => 'neebites_header_section',
        'settings'    => 'neebites_theme_options[top_bar_text]',
        'type'        => 'textarea',
    ]);

    // Header Style
    $wp_customize->add_setting('neebites_theme_options[header_style]', [
        'default'           => 'style-1',
        'type'              => 'option',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_key',
    ]);
    $wp_customize->add_control('header_style', [
        'label'    => esc_html__('Header Layout Style', 'neebites'),
        'section'  => 'neebites_header_section',
        'settings' => 'neebites_theme_options[header_style]',
        'type'     => 'select',
        'choices'  => [
            'style-1' => esc_html__('Style 1: Modern Artisanal (Logo Left, Nav Center, Actions Right)', 'neebites'),
            'style-2' => esc_html__('Style 2: Centered Logo & Minimalist Navigation', 'neebites'),
        ],
    ]);

    // -------------------------------------------------------------
    // 2. Chocolatier Colors Section
    // -------------------------------------------------------------
    $wp_customize->add_section('neebites_colors_section', [
        'title'    => esc_html__('Chocolatier Colors & Palette', 'neebites'),
        'panel'    => 'neebites_theme_options_panel',
        'priority' => 20,
    ]);

    $colors = [
        'primary_color'       => ['label' => 'Primary Cocoa Brown', 'default' => '#3D2314'],
        'primary_color_light' => ['label' => 'Secondary Warm Cocoa', 'default' => '#6B4226'],
        'accent_color'        => ['label' => 'Accent Caramel Amber', 'default' => '#C59B27'],
        'sale_color'          => ['label' => 'Sale Badge Color', 'default' => '#C0392B'],
        'bg_color_light'      => ['label' => 'Background Cream Tone', 'default' => '#FAF6F0'],
        'text_color'          => ['label' => 'Text Deep Espresso', 'default' => '#231205'],
    ];

    foreach ($colors as $key => $data) {
        $wp_customize->add_setting('neebites_theme_options[' . $key . ']', [
            'default'           => $data['default'],
            'type'              => 'option',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $key, [
            'label'    => esc_html__($data['label'], 'neebites'),
            'section'  => 'neebites_colors_section',
            'settings' => 'neebites_theme_options[' . $key . ']',
        ]));
    }

    // -------------------------------------------------------------
    // 3. Shop & Product Catalog Section
    // -------------------------------------------------------------
    $wp_customize->add_section('neebites_shop_section', [
        'title'    => esc_html__('Shop & WooCommerce Layout', 'neebites'),
        'panel'    => 'neebites_theme_options_panel',
        'priority' => 30,
    ]);

    $wp_customize->add_setting('neebites_theme_options[shop_columns]', [
        'default'           => 4,
        'type'              => 'option',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('shop_columns', [
        'label'    => esc_html__('Shop Grid Columns', 'neebites'),
        'section'  => 'neebites_shop_section',
        'settings' => 'neebites_theme_options[shop_columns]',
        'type'     => 'select',
        'choices'  => [
            2 => '2 Columns',
            3 => '3 Columns',
            4 => '4 Columns',
            5 => '5 Columns',
        ],
    ]);

    $wp_customize->add_setting('neebites_theme_options[shop_sidebar]', [
        'default'           => 'left',
        'type'              => 'option',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_key',
    ]);
    $wp_customize->add_control('shop_sidebar', [
        'label'    => esc_html__('Shop Sidebar Position', 'neebites'),
        'section'  => 'neebites_shop_section',
        'settings' => 'neebites_theme_options[shop_sidebar]',
        'type'     => 'select',
        'choices'  => [
            'right' => esc_html__('Right Sidebar', 'neebites'),
            'left'  => esc_html__('Left Sidebar', 'neebites'),
            'none'  => esc_html__('Full Width (No Sidebar)', 'neebites'),
        ],
    ]);

    $wp_customize->add_setting('neebites_theme_options[sticky_add_to_cart]', [
        'default'           => true,
        'type'              => 'option',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'neebites_sanitize_checkbox',
    ]);
    $wp_customize->add_control('sticky_add_to_cart', [
        'label'    => esc_html__('Enable Sticky Add to Cart Bottom Bar', 'neebites'),
        'section'  => 'neebites_shop_section',
        'settings' => 'neebites_theme_options[sticky_add_to_cart]',
        'type'     => 'checkbox',
    ]);

    // -------------------------------------------------------------
    // 4. Footer & Social Links Section
    // -------------------------------------------------------------
    $wp_customize->add_section('neebites_footer_section', [
        'title'    => esc_html__('Footer & Social Links', 'neebites'),
        'panel'    => 'neebites_theme_options_panel',
        'priority' => 40,
    ]);

    $wp_customize->add_setting('neebites_theme_options[footer_copyright]', [
        'default'           => '&copy; ' . date('Y') . ' Neebites. Luxury Artisan Chocolates & Confectionery.',
        'type'              => 'option',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'wp_kses_post',
    ]);
    $wp_customize->add_control('footer_copyright', [
        'label'    => esc_html__('Copyright Text', 'neebites'),
        'section'  => 'neebites_footer_section',
        'settings' => 'neebites_theme_options[footer_copyright]',
        'type'     => 'text',
    ]);

    $social_networks = ['instagram', 'facebook', 'twitter', 'pinterest', 'youtube'];
    foreach ($social_networks as $net) {
        $wp_customize->add_setting('neebites_theme_options[social_' . $net . ']', [
            'default'           => '',
            'type'              => 'option',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control('social_' . $net, [
            'label'    => sprintf(esc_html__('%s URL', 'neebites'), ucfirst($net)),
            'section'  => 'neebites_footer_section',
            'settings' => 'neebites_theme_options[social_' . $net . ']',
            'type'     => 'url',
        ]);
    }
}
add_action('customize_register', 'neebites_customize_register');
