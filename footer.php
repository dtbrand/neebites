<?php
/**
 * Theme Footer Template
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;
?>

</main><!-- #main-content -->

<!-- Footer Widgets -->
<section class="footer-widgets" aria-label="<?php esc_attr_e('Footer Navigation', 'neebites'); ?>">
    <div class="container">
        <div class="footer-grid">
            <?php if (is_active_sidebar('footer-1')) : ?>
                <div class="footer-col footer-col-brand"><?php dynamic_sidebar('footer-1'); ?></div>
            <?php else : ?>
                <!-- Col 1: Brand Info & Confectionery Guarantee -->
                <div class="footer-col footer-col-brand">
                    <div class="footer-brand-title-wrap">
                        <h4 class="widget-title">🍫 <?php bloginfo('name'); ?></h4>
                        <span class="brand-sub-badge"><?php esc_html_e('Artisan Confectionery', 'neebites'); ?></span>
                    </div>
                    <p class="footer-brand-desc"><?php esc_html_e('Your luxury artisan confectionery sanctuary. Handcrafted chocolate coated nuts, premium Californian roasted almonds, pure cocoa butter, and temperature-controlled freshness delivered to your door.', 'neebites'); ?></p>
                    <div class="footer-badge-perk">
                        <span class="badge-perk-item">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php esc_html_e('100% Pure Cocoa Butter', 'neebites'); ?>
                        </span>
                        <span class="badge-perk-item">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php esc_html_e('Insulated Cold-Pack Delivery', 'neebites'); ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (is_active_sidebar('footer-2')) : ?>
                <div class="footer-col footer-accordion-col" data-col="shop"><?php dynamic_sidebar('footer-2'); ?></div>
            <?php else : ?>
                <!-- Col 2: Confectionery Shop Links (Collapsible Option on Mobile) -->
                <div class="footer-col footer-accordion-col" data-col="shop">
                    <button type="button" class="footer-accordion-toggle" aria-expanded="false" aria-controls="footer-sub-shop">
                        <span class="widget-title"><?php esc_html_e('Confectionery Shop', 'neebites'); ?></span>
                        <span class="accordion-chevron" aria-hidden="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </span>
                    </button>
                    <div id="footer-sub-shop" class="footer-accordion-content">
                        <ul class="footer-nav-list">
                            <li>
                                <a href="<?php echo esc_url(home_url('/product-category/chocolates-confectionery/')); ?>">
                                    <span class="nav-icon">🍫</span>
                                    <span><?php esc_html_e('Chocolates & Confectionery', 'neebites'); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(home_url('/product-category/chocolate-coated-nuts/')); ?>">
                                    <span class="nav-icon">🥜</span>
                                    <span><?php esc_html_e('Chocolate Coated Nuts', 'neebites'); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(home_url('/product-category/chocolate-coated-almonds/')); ?>">
                                    <span class="nav-icon">🌰</span>
                                    <span><?php esc_html_e('Chocolate Coated Almonds', 'neebites'); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(home_url('/shop/')); ?>">
                                    <span class="nav-icon">🥝</span>
                                    <span><?php esc_html_e('Dark & Kiwi Almond Variants', 'neebites'); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(home_url('/shop/')); ?>">
                                    <span class="nav-icon">✨</span>
                                    <span><?php esc_html_e('Luxury Gift Hampers', 'neebites'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (is_active_sidebar('footer-3')) : ?>
                <div class="footer-col footer-accordion-col" data-col="care"><?php dynamic_sidebar('footer-3'); ?></div>
            <?php else : ?>
                <!-- Col 3: Customer Care & Guides (Collapsible Option on Mobile) -->
                <div class="footer-col footer-accordion-col" data-col="care">
                    <button type="button" class="footer-accordion-toggle" aria-expanded="false" aria-controls="footer-sub-care">
                        <span class="widget-title"><?php esc_html_e('Gourmet Quality & Care', 'neebites'); ?></span>
                        <span class="accordion-chevron" aria-hidden="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </span>
                    </button>
                    <div id="footer-sub-care" class="footer-accordion-content">
                        <ul class="footer-nav-list">
                            <li>
                                <a href="<?php echo esc_url(home_url('/contact')); ?>">
                                    <span class="nav-icon">❄️</span>
                                    <span><?php esc_html_e('Cold-Chain Safe Shipping', 'neebites'); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(home_url('/about-us')); ?>">
                                    <span class="nav-icon">🌱</span>
                                    <span><?php esc_html_e('Cocoa Origin & Sustainability', 'neebites'); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(home_url('/contact')); ?>">
                                    <span class="nav-icon">📖</span>
                                    <span><?php esc_html_e('Storage & Tasting Guide', 'neebites'); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(home_url('/contact')); ?>">
                                    <span class="nav-icon">🎁</span>
                                    <span><?php esc_html_e('Corporate & Wedding Gifting', 'neebites'); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(home_url('/contact')); ?>">
                                    <span class="nav-icon">🛡️</span>
                                    <span><?php esc_html_e('Nut & Allergen Declarations', 'neebites'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (is_active_sidebar('footer-4')) : ?>
                <div class="footer-col footer-accordion-col footer-col-newsletter" data-col="club"><?php dynamic_sidebar('footer-4'); ?></div>
            <?php else : ?>
                <!-- Col 4: Chocolate Club Newsletter (Collapsible Option on Mobile) -->
                <div class="footer-col footer-accordion-col footer-col-newsletter" data-col="club">
                    <button type="button" class="footer-accordion-toggle" aria-expanded="true" aria-controls="footer-sub-club">
                        <span class="widget-title"><?php esc_html_e('Join the Chocolate Club', 'neebites'); ?></span>
                        <span class="accordion-chevron" aria-hidden="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </span>
                    </button>
                    <div id="footer-sub-club" class="footer-accordion-content is-open">
                        <p class="footer-newsletter-desc"><?php esc_html_e('Receive secret batch drops, gourmet tasting notes, and 10% off your first chocolate indulgence.', 'neebites'); ?></p>
                        <form class="footer-newsletter-form" onsubmit="event.preventDefault(); alert('Welcome to the Chocolate Club! Check your inbox for your 10% sweet code.');">
                            <div class="newsletter-input-group">
                                <input type="email" placeholder="<?php esc_attr_e('Your email address...', 'neebites'); ?>" required aria-label="<?php esc_attr_e('Email for newsletter', 'neebites'); ?>" />
                                <button type="submit" aria-label="<?php esc_attr_e('Subscribe to club', 'neebites'); ?>">&rarr;</button>
                            </div>
                        </form>
                        <span class="newsletter-sub-hint"><?php esc_html_e('Zero spam • Unsubscribe anytime with 1-click', 'neebites'); ?></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Footer Colophon & Copyright -->
<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-inner">
                <div class="footer-copyright">
                    <?php 
                    $raw_copy = neebites_get_option('footer_copyright', '&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. Artisan Confectionery Sanctuary. All rights reserved.');
                    $clean_copy = str_ireplace('Botanical Sanctuary', 'Artisan Confectionery Sanctuary', $raw_copy);
                    echo wp_kses_post($clean_copy);
                    ?>
                </div>

                <?php if (neebites_get_option('footer_social', true)) : ?>
                    <div class="footer-social" role="list" aria-label="<?php esc_attr_e('Social links', 'neebites'); ?>">
                        <?php 
                        $socials = ['instagram' => 'Instagram', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinterest' => 'Pinterest', 'youtube' => 'YouTube'];
                        foreach ($socials as $key => $label) :
                            $url = neebites_get_option('social_' . $key);
                            if ($url) :
                        ?>
                                <a href="<?php echo esc_url($url); ?>" class="social-link social-<?php echo esc_attr($key); ?>" aria-label="<?php echo esc_attr($label); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo neebites_get_social_icon($key); ?>
                                </a>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (has_nav_menu('footer')) : ?>
                    <nav class="footer-nav" aria-label="<?php esc_attr_e('Footer legal menu', 'neebites'); ?>">
                        <?php wp_nav_menu([
                            'theme_location' => 'footer',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'depth'          => 1,
                        ]); ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>

<!-- Mobile Bottom Navigation Dock (Fluid Mobile & Tablet Auto-Sizing) -->
<nav class="neebites-mobile-dock" style="display: none;" aria-label="<?php esc_attr_e('Mobile Quick Navigation', 'neebites'); ?>">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="dock-item <?php echo is_front_page() ? 'active' : ''; ?>">
        <span class="dock-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        </span>
        <span class="dock-label"><?php esc_html_e('Home', 'neebites'); ?></span>
    </a>
    
    <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/shop')); ?>" class="dock-item <?php echo (function_exists('is_shop') && (is_shop() || is_product_taxonomy())) ? 'active' : ''; ?>">
        <span class="dock-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
        </span>
        <span class="dock-label"><?php esc_html_e('Shop', 'neebites'); ?></span>
    </a>

    <button type="button" class="dock-item dock-search-trigger header-search-toggle" aria-controls="header-search" aria-label="<?php esc_attr_e('Search', 'neebites'); ?>">
        <span class="dock-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><path d="M21 21l-4.35-4.35"></path></svg>
        </span>
        <span class="dock-label"><?php esc_html_e('Search', 'neebites'); ?></span>
    </button>

    <?php if (neebites_is_woocommerce_active()) : 
        $wishlist_count = neebites_get_wishlist_count_total();
        $cart_count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
        $account_url = function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('dashboard') : wp_login_url();
    ?>
    <a href="<?php echo esc_url($account_url); ?>" class="dock-item dock-wishlist" aria-label="<?php esc_attr_e('Wishlist', 'neebites'); ?>">
        <span class="dock-icon has-badge">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
            <span class="wishlist-count dock-badge"><?php echo esc_html($wishlist_count); ?></span>
        </span>
        <span class="dock-label"><?php esc_html_e('Wishlist', 'neebites'); ?></span>
    </a>

    <button type="button" class="dock-item dock-cart header-cart header-cart-trigger" aria-controls="neebites-cart-drawer" aria-label="<?php esc_attr_e('View Basket', 'neebites'); ?>">
        <span class="dock-icon has-badge">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            <span class="cart-count dock-badge"><?php echo esc_html($cart_count); ?></span>
        </span>
        <span class="dock-label"><?php esc_html_e('Cart', 'neebites'); ?></span>
    </button>
    <?php else : ?>
    <a href="<?php echo esc_url(is_user_logged_in() ? admin_url() : wp_login_url()); ?>" class="dock-item dock-account" aria-label="<?php esc_attr_e('Account', 'neebites'); ?>">
        <span class="dock-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </span>
        <span class="dock-label"><?php esc_html_e('Account', 'neebites'); ?></span>
    </a>
    <?php endif; ?>
</nav>

<?php wp_footer(); ?>
</body>
</html>
