<?php
/**
 * WooCommerce My Account & Customer Login / Registration Customizations
 *
 * @package Neebites
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Filter and format account navigation items
 */
function neebites_account_menu_items($items) {
    // Add Wishlist link to account menu
    $new_items = [];
    foreach ($items as $key => $title) {
        $new_items[$key] = $title;
        if ($key === 'orders') {
            $new_items['wishlist'] = esc_html__('Saved Plants', 'neebites');
        }
    }
    return $new_items;
}
add_filter('woocommerce_account_menu_items', 'neebites_account_menu_items');

/**
 * Account Menu Item URLs
 */
function neebites_account_menu_item_classes($classes, $endpoint) {
    return $classes;
}
add_filter('woocommerce_account_menu_item_classes', 'neebites_account_menu_item_classes', 10, 2);

/**
 * Render Welcome Hero Banner on Account Dashboard
 */
function neebites_account_dashboard_welcome() {
    if (!is_user_logged_in()) return;
    $current_user = wp_get_current_user();
    $display_name = $current_user->display_name ? $current_user->display_name : $current_user->user_login;
    $registered_year = date('Y', strtotime($current_user->user_registered));
    ?>
    <div class="neebites-account-welcome-banner">
        <div class="welcome-user-info">
            <div class="user-avatar-circle">
                <?php echo get_avatar($current_user->ID, 64); ?>
            </div>
            <div class="user-meta">
                <span class="user-club-badge">🍫 <?php esc_html_e('Cocoa Club VIP Connoisseur', 'neebites'); ?></span>
                <h2 class="user-greeting"><?php printf(esc_html__('Welcome back, %s!', 'neebites'), esc_html($display_name)); ?></h2>
                <p class="user-since"><?php printf(esc_html__('Savoring handcrafted artisan chocolates since %s', 'neebites'), esc_html($registered_year)); ?></p>
            </div>
        </div>
        <div class="welcome-quick-perks">
            <div class="quick-perk-item">
                <span class="perk-icon">📦</span>
                <div class="perk-info">
                    <strong><?php esc_html_e('Insulated Express Delivery', 'neebites'); ?></strong>
                    <span><?php esc_html_e('Cold-pack temperature protected', 'neebites'); ?></span>
                </div>
            </div>
            <div class="quick-perk-item">
                <span class="perk-icon">🍫</span>
                <div class="perk-info">
                    <strong><?php esc_html_e('100% Cocoa Guarantee', 'neebites'); ?></strong>
                    <span><?php esc_html_e('Active on all chocolate orders', 'neebites'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Cards Grid -->
    <div class="neebites-account-quick-cards">
        <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>" class="account-card">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
            </div>
            <div class="card-label">
                <strong><?php esc_html_e('My Orders', 'neebites'); ?></strong>
                <span><?php esc_html_e('Track active shipments', 'neebites'); ?></span>
            </div>
        </a>
        <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-address')); ?>" class="account-card">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            </div>
            <div class="card-label">
                <strong><?php esc_html_e('Shipping Address', 'neebites'); ?></strong>
                <span><?php esc_html_e('Manage delivery points', 'neebites'); ?></span>
            </div>
        </a>
        <a href="#" class="account-card header-wishlist" id="account-card-wishlist">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
            </div>
            <div class="card-label">
                <strong><?php esc_html_e('Saved Wishlist', 'neebites'); ?></strong>
                <span><?php esc_html_e('View bookmarked confections', 'neebites'); ?></span>
            </div>
        </a>
        <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>" class="account-card">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <div class="card-label">
                <strong><?php esc_html_e('Account Details', 'neebites'); ?></strong>
                <span><?php esc_html_e('Password & email preferences', 'neebites'); ?></span>
            </div>
        </a>
    </div>
    <?php
}
add_action('woocommerce_account_dashboard', 'neebites_account_dashboard_welcome', 5);

/**
 * Modern Chocolatier Wrapper for WooCommerce Login & Registration Forms
 */
function neebites_before_customer_login_form() {
    ?>
    <div class="neebites-auth-container">
        <!-- Dual Tab Switcher -->
        <div class="auth-tabs-nav" role="tablist">
            <button type="button" class="auth-tab-btn active" data-target="#customer_login .u-column1" role="tab" aria-selected="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                <span><?php esc_html_e('Sign In', 'neebites'); ?></span>
            </button>
            <?php if ('yes' === get_option('woocommerce_enable_myaccount_registration')) : ?>
            <button type="button" class="auth-tab-btn" data-target="#customer_login .u-column2" role="tab" aria-selected="false">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                <span><?php esc_html_e('Join Cocoa Club', 'neebites'); ?></span>
            </button>
            <?php endif; ?>
        </div>

        <div class="auth-vip-perks-sidebar">
            <div class="vip-perks-card">
                <div class="perks-badge">🍫 <?php esc_html_e('Cocoa Club VIP Benefits', 'neebites'); ?></div>
                <h3 class="perks-title"><?php esc_html_e('Indulge with us & enjoy exclusive chocolatier access', 'neebites'); ?></h3>
                <ul class="perks-list">
                    <li>
                        <span class="perk-check">✓</span>
                        <span><strong><?php esc_html_e('10% Welcome Discount', 'neebites'); ?></strong> <?php esc_html_e('on your first artisan chocolate order', 'neebites'); ?></span>
                    </li>
                    <li>
                        <span class="perk-check">✓</span>
                        <span><strong><?php esc_html_e('VIP Batch Roasts', 'neebites'); ?></strong> <?php esc_html_e('24-hour early access to limited chocolate drops', 'neebites'); ?></span>
                    </li>
                    <li>
                        <span class="perk-check">✓</span>
                        <span><strong><?php esc_html_e('Master Chocolatier Tasting Notes', 'neebites'); ?></strong> <?php esc_html_e('Curated pairings & seasonal flavour previews', 'neebites'); ?></span>
                    </li>
                    <li>
                        <span class="perk-check">✓</span>
                        <span><strong><?php esc_html_e('Cold-Chain Assurance', 'neebites'); ?></strong> <?php esc_html_e('100% melt-free fresh delivery guarantee', 'neebites'); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    <?php
}
add_action('woocommerce_before_customer_login_form', 'neebites_before_customer_login_form', 5);

function neebites_after_customer_login_form() {
    ?>
    </div><!-- /.neebites-auth-container -->
    <?php
}
add_action('woocommerce_after_customer_login_form', 'neebites_after_customer_login_form', 50);
