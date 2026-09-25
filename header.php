<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <a class="skip-link" href="#main-content"><?php esc_html_e('Skip to main content', 'neebites'); ?></a>
    
    <!-- Top Announcement / Shipping Ticker Bar -->
    <?php neebites_shipping_bar(); ?>
    
    <header id="masthead" class="site-header header-<?php echo esc_attr(neebites_get_option('header_style', 'style-1')); ?>" role="banner">
        <div class="container">
            <div class="header-inner">
                <!-- Mobile Menu Button -->
                <button class="mobile-menu-toggle" aria-expanded="false" aria-controls="mobile-menu-panel" aria-label="<?php esc_attr_e('Toggle navigation menu', 'neebites'); ?>">
                    <span class="hamburger"><span></span><span></span><span></span></span>
                </button>

                <!-- Site Logo -->
                <div class="site-branding">
                    <?php 
                    $logo_id = get_theme_mod('custom_logo');
                    if ($logo_id) {
                        echo wp_get_attachment_image($logo_id, 'full', false, [
                            'class'   => 'site-logo',
                            'alt'     => get_bloginfo('name'),
                            'loading' => 'eager',
                        ]);
                    } else {
                        echo '<a href="' . esc_url(home_url('/')) . '" class="site-logo text-logo" rel="home">';
                        echo '<span class="logo-leaf">🍫</span> ' . esc_html(get_bloginfo('name'));
                        echo '</a>';
                    }
                    ?>
                </div>
                
                <!-- Primary Navigation -->
                <nav id="primary-navigation" class="primary-navigation" role="navigation" aria-label="<?php esc_attr_e('Primary menu', 'neebites'); ?>">
                    <?php 
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'menu_class'     => 'primary-nav menu',
                        'container'      => false,
                        'fallback_cb'    => 'neebites_fallback_menu',
                        'walker'         => class_exists('Neebites_Nav_Walker') ? new Neebites_Nav_Walker() : '',
                    ]); 
                    ?>
                </nav>
                
                <!-- Header Actions: Search, Wishlist, Cart Drawer, Account -->
                <div class="header-actions">
                    <!-- Search Button -->
                    <button class="header-search-toggle" aria-expanded="false" aria-controls="header-search" aria-label="<?php esc_attr_e('Open Search', 'neebites'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="M21 21l-4.35-4.35"></path></svg>
                    </button>

                    <?php if (neebites_is_woocommerce_active()) : 
                        $cart_count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
                        $wishlist_count = neebites_get_wishlist_count_total();
                        $account_url = function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('dashboard') : wp_login_url();
                        $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/');
                    ?>
                        <!-- Wishlist Trigger -->
                        <a href="<?php echo esc_url($account_url); ?>" class="header-wishlist" id="header-wishlist-trigger" aria-label="<?php esc_attr_e('Wishlist', 'neebites'); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                            <span class="wishlist-count"><?php echo esc_html($wishlist_count); ?></span>
                        </a>

                        <!-- Off-canvas Cart Drawer Trigger -->
                        <button type="button" class="header-cart header-cart-trigger" aria-controls="neebites-cart-drawer" aria-label="<?php esc_attr_e('View Basket', 'neebites'); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            <span class="cart-count"><?php echo esc_html($cart_count); ?></span>
                        </button>

                        <!-- Account Link -->
                        <a href="<?php echo esc_url($account_url); ?>" class="header-account" aria-label="<?php echo is_user_logged_in() ? esc_attr__('My Account', 'neebites') : esc_attr__('Login / Register', 'neebites'); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </a>
                    <?php else : ?>
                        <!-- Standard WP Account Link -->
                        <a href="<?php echo esc_url(is_user_logged_in() ? admin_url() : wp_login_url()); ?>" class="header-account" aria-label="<?php esc_attr_e('Account', 'neebites'); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <!-- AJAX Live Search Modal / Overlay (True Fullscreen Confectionery Search) -->
    <div id="header-search" class="header-search-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Search products', 'neebites'); ?>" hidden>
        <div class="header-search-inner">
            <form role="search" method="get" class="header-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <label for="header-search-input" class="screen-reader-text"><?php esc_html_e('Search artisan chocolates and confections', 'neebites'); ?></label>
                <div class="search-input-group">
                    <span class="search-icon-left">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><path d="M21 21l-4.35-4.35"></path></svg>
                    </span>
                    <input type="search" id="header-search-input" name="s" placeholder="<?php esc_attr_e('Search dark chocolate, kiwi almond, roasted nuts, gift boxes...', 'neebites'); ?>" autocomplete="off" />
                    <input type="hidden" name="post_type" value="product" />
                    <button type="button" class="header-search-close" aria-label="<?php esc_attr_e('Close search', 'neebites'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </form>

            <!-- Quick Suggestions & Trending Tags (Shown before search input) -->
            <div class="search-suggestions-box" id="search-suggestions-box">
                <div class="suggestions-header">
                    <span class="suggestions-label">🔥 <?php esc_html_e('Trending Confections', 'neebites'); ?></span>
                </div>
                <div class="suggestions-tags">
                    <button type="button" class="search-tag-pill" data-query="Dark Chocolate Almond">🍫 Dark Chocolate Almond</button>
                    <button type="button" class="search-tag-pill" data-query="Kiwi Chocolate Almond">🥝 Kiwi Chocolate Almond</button>
                    <button type="button" class="search-tag-pill" data-query="Milk Chocolate Almond">🥛 Milk Chocolate Almond</button>
                    <button type="button" class="search-tag-pill" data-query="White Chocolate Almond">🤍 White Chocolate Almond</button>
                    <button type="button" class="search-tag-pill" data-query="Salted Caramel">🍯 Salted Caramel Almond</button>
                    <button type="button" class="search-tag-pill" data-query="Matcha Green Tea">🍵 Matcha Chocolate Almond</button>
                </div>
            </div>

            <!-- Instant AJAX search results dropdown -->
            <div id="header-search-results" class="header-search-results" hidden></div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobile-menu-panel" class="mobile-menu-panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Mobile menu', 'neebites'); ?>" hidden>
        <div class="mobile-menu-header">
            <span class="mobile-menu-title">🍫 <?php esc_html_e('Confectionery Menu', 'neebites'); ?></span>
            <button class="mobile-menu-close" aria-label="<?php esc_attr_e('Close menu', 'neebites'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="mobile-menu-content">
            <?php 
            wp_nav_menu([
                'theme_location' => 'primary',
                'menu_class'     => 'mobile-menu',
                'container'      => false,
                'fallback_cb'    => 'neebites_fallback_menu',
            ]); 
            ?>
            <div class="mobile-menu-footer">
                <?php if (neebites_is_woocommerce_active()) : 
                    $account_url = function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('dashboard') : wp_login_url();
                ?>
                    <div class="mobile-menu-account">
                        <?php if (is_user_logged_in()) : ?>
                            <a href="<?php echo esc_url($account_url); ?>" class="btn btn-outline btn-block"><?php esc_html_e('My Sweet Account', 'neebites'); ?></a>
                            <a href="<?php echo esc_url(wp_logout_url()); ?>" class="logout-link"><?php esc_html_e('Sign Out', 'neebites'); ?></a>
                        <?php else : ?>
                            <a href="<?php echo esc_url($account_url); ?>" class="btn btn-primary btn-block"><?php esc_html_e('Sign In / Register', 'neebites'); ?></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="mobile-menu-overlay" class="mobile-menu-overlay" hidden></div>
    
    <main id="main-content" class="site-main" role="main">
