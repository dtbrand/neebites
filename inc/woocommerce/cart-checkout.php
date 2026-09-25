<?php
/**
 * WooCommerce Cart, Mini-Cart Drawer, Wishlist Drawer & Checkout Customizations
 *
 * @package Neebites
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Render the Free Shipping Goal Progress Bar
 */
function neebites_render_free_shipping_bar() {
    $threshold = (float) neebites_get_option('free_shipping_threshold', 50);
    if ($threshold <= 0) return;

    $subtotal = 0.0;
    if (function_exists('WC') && WC()->cart) {
        $subtotal = (float) WC()->cart->get_displayed_subtotal();
    }
    
    $diff    = max(0, $threshold - $subtotal);
    $percent = min(100, round(($subtotal / $threshold) * 100));
    $diff_html = function_exists('wc_price') ? wc_price($diff) : '$' . number_format($diff, 2);
    ?>
    <div class="neebites-free-shipping-bar <?php echo $percent >= 100 ? 'reached' : ''; ?>">
        <div class="shipping-goal-text">
            <span class="icon-truck">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="1" y="3" width="15" height="13" rx="2"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
            </span>
            <div class="shipping-goal-msg">
                <?php if ($percent >= 100) : ?>
                    <strong class="goal-unlocked"><?php esc_html_e('You unlocked FREE Cold-Pack Confectionery Delivery! 🍫', 'neebites'); ?></strong>
                <?php else : ?>
                    <span><?php printf(esc_html__('Add %s more to unlock', 'neebites'), '<strong class="diff-amount">' . wp_kses_post($diff_html) . '</strong>'); ?> <strong class="goal-highlight"><?php esc_html_e('FREE Shipping', 'neebites'); ?></strong> 🍫</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="shipping-progress-track">
            <div class="shipping-progress-fill" style="width: <?php echo esc_attr($percent); ?>%;"></div>
        </div>
    </div>
    <?php
}

/**
 * Render Mini-Cart Drawer Content (Used in drawer and AJAX cart fragment)
 */
function neebites_render_mini_cart_content() {
    $cart_count = 0;
    $cart_items = [];
    if (function_exists('WC') && WC()->cart) {
        $cart_count = WC()->cart->get_cart_contents_count();
        $cart_items = WC()->cart->get_cart();
    }
    $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
    ?>
    <div class="mini-cart-drawer-content">
        <!-- Free Shipping Bar in Mini Cart -->
        <?php neebites_render_free_shipping_bar(); ?>

        <?php if ($cart_count > 0 && !empty($cart_items) && function_exists('WC') && WC()->cart) : ?>
            <div class="mini-cart-items-list">
                <?php
                $cart_product_ids = [];
                foreach ($cart_items as $cart_item_key => $cart_item) {
                    $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                    if ($product_id) {
                        $cart_product_ids[] = $product_id;
                    }

                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {
                        $product_name      = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                        $thumbnail         = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
                        $product_price     = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                        ?>
                        <div class="mini-cart-item" data-key="<?php echo esc_attr($cart_item_key); ?>">
                            <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>" class="btn-remove-item remove_from_cart_button" data-cart_item_key="<?php echo esc_attr($cart_item_key); ?>" aria-label="<?php esc_attr_e('Remove this item', 'neebites'); ?>" title="<?php esc_attr_e('Remove', 'neebites'); ?>">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </a>

                            <div class="mini-cart-item-main">
                                <div class="mini-cart-thumb">
                                    <?php if ($product_permalink) : ?>
                                        <a href="<?php echo esc_url($product_permalink); ?>"><?php echo $thumbnail; ?></a>
                                    <?php else : ?>
                                        <?php echo $thumbnail; ?>
                                    <?php endif; ?>
                                </div>

                                <div class="mini-cart-details">
                                    <h4 class="mini-cart-item-title">
                                        <?php if ($product_permalink) : ?>
                                            <a href="<?php echo esc_url($product_permalink); ?>" title="<?php echo esc_attr($_product->get_name()); ?>"><?php echo wp_kses_post($product_name); ?></a>
                                        <?php else : ?>
                                            <?php echo wp_kses_post($product_name); ?>
                                        <?php endif; ?>
                                    </h4>

                                    <?php echo wc_get_formatted_cart_item_data($cart_item); ?>

                                    <div class="mini-cart-meta-row">
                                        <div class="mini-cart-price"><?php echo $product_price; ?></div>
                                        <div class="mini-cart-qty-ctrl">
                                            <button type="button" class="btn-qty-minus" data-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="<?php esc_attr_e('Decrease quantity', 'neebites'); ?>">-</button>
                                            <input type="number" class="mini-cart-qty-input" value="<?php echo esc_attr($cart_item['quantity']); ?>" min="1" max="99" data-key="<?php echo esc_attr($cart_item_key); ?>" readonly />
                                            <button type="button" class="btn-qty-plus" data-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="<?php esc_attr_e('Increase quantity', 'neebites'); ?>">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <!-- Complete Confectionery Pairing & Cross-Sells Section -->
            <?php
            $cross_sells = [];
            if (function_exists('wc_get_products')) {
                $cross_sells = wc_get_products([
                    'status' => 'publish',
                    'limit' => 2,
                    'exclude' => $cart_product_ids,
                    'orderby' => 'menu_order',
                    'order' => 'ASC',
                ]);
            }
            if (!empty($cross_sells)) :
            ?>
            <div class="mini-cart-cross-sells">
                <div class="cross-sells-header">
                    <span class="cross-sells-icon">🍫</span>
                    <div class="cross-sells-header-text">
                        <strong><?php esc_html_e('Confectionery Pairings', 'neebites'); ?></strong>
                        <span class="cross-sells-tagline"><?php esc_html_e('Frequently paired by chocolate connoisseurs', 'neebites'); ?></span>
                    </div>
                </div>
                <div class="cross-sells-list">
                    <?php foreach ($cross_sells as $cs_prod) : 
                        $cs_stock = $cs_prod->is_in_stock();
                    ?>
                    <div class="cross-sell-card">
                        <div class="cross-sell-thumb">
                            <a href="<?php echo esc_url($cs_prod->get_permalink()); ?>">
                                <?php echo $cs_prod->get_image('thumbnail'); ?>
                            </a>
                        </div>
                        <div class="cross-sell-info">
                            <a href="<?php echo esc_url($cs_prod->get_permalink()); ?>" class="cross-sell-title"><?php echo esc_html($cs_prod->get_name()); ?></a>
                            <span class="cross-sell-price"><?php echo $cs_prod->get_price_html(); ?></span>
                        </div>
                        <?php if ($cs_stock) : ?>
                            <a href="<?php echo esc_url($cs_prod->add_to_cart_url()); ?>" data-product_id="<?php echo esc_attr($cs_prod->get_id()); ?>" class="btn-cross-sell-add ajax_add_to_cart add_to_cart_button" aria-label="<?php esc_attr_e('Add to basket', 'neebites'); ?>">
                                + <?php esc_html_e('Add', 'neebites'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Mini Cart Footer Summary -->
            <div class="mini-cart-footer">
                <div class="mini-cart-subtotal-row">
                    <span class="subtotal-label"><?php esc_html_e('Estimated Subtotal', 'neebites'); ?>:</span>
                    <span class="subtotal-amount"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
                </div>
                <div class="mini-cart-perk">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span><?php esc_html_e('100% Pure Cocoa Butter Guarantee & Insulated Thermal Packaging', 'neebites'); ?></span>
                </div>
                <div class="mini-cart-buttons">
                    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn btn-primary btn-block btn-checkout">
                        <span><?php esc_html_e('Proceed to Checkout', 'neebites'); ?></span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="btn btn-outline btn-block btn-view-cart">
                        <?php esc_html_e('View Shopping Basket', 'neebites'); ?>
                    </a>
                </div>
            </div>
        <?php else : ?>
            <div class="mini-cart-empty">
                <div class="empty-icon-wrap">
                    <div class="empty-cart-ring">
                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    </div>
                </div>
                <h3 class="mini-cart-empty-title"><?php esc_html_e('Your Confectionery Basket is Empty', 'neebites'); ?></h3>
                <p class="mini-cart-empty-desc"><?php esc_html_e('Your sweet craving is waiting. Discover our handcrafted chocolate coated almonds and confectionery delivered fresh in melt-proof packaging.', 'neebites'); ?></p>
                <a href="<?php echo esc_url($shop_url); ?>" class="btn btn-primary btn-cart-empty-cta">
                    <span><?php esc_html_e('Discover Handcrafted Chocolates', 'neebites'); ?></span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>

                <!-- Quick Exploration Category                 <!-- Quick Exploration Category Tags -->
                <div class="mini-cart-empty-categories">
                    <span class="categories-label"><?php esc_html_e('Popular Confections to Explore:', 'neebites'); ?></span>
                    <div class="category-pills-row">
                        <a href="<?php echo esc_url(add_query_arg('product_cat', 'dark-chocolate-almond', $shop_url)); ?>" class="cat-pill">🍫 Dark Chocolate</a>
                        <a href="<?php echo esc_url(add_query_arg('product_cat', 'kiwi-chocolate-almond', $shop_url)); ?>" class="cat-pill">🥝 Kiwi Almond</a>
                        <a href="<?php echo esc_url(add_query_arg('product_cat', 'milk-chocolate-almond', $shop_url)); ?>" class="cat-pill">🥛 Milk Chocolate</a>
                        <a href="<?php echo esc_url(add_query_arg('product_cat', 'white-chocolate-almond', $shop_url)); ?>" class="cat-pill">🤍 White Vanilla</a>
                    </div>
                </div>

                <!-- Confectionery Drops (Instant 1-Click Discovery) -->
                <?php
                $featured_drops = [];
                if (function_exists('wc_get_products')) {
                    $featured_drops = wc_get_products([
                        'status' => 'publish',
                        'limit' => 2,
                        'orderby' => 'menu_order',
                        'order' => 'ASC',
                    ]);
                }
                if (!empty($featured_drops)) :
                ?>
                <div class="mini-cart-starter-drops">
                    <div class="starter-drops-title">
                        <span>🍫</span>
                        <strong><?php esc_html_e('Trending Chocolate Favorites', 'neebites'); ?></strong>
                    </div>
                    <div class="starter-drops-list">
                        <?php foreach ($featured_drops as $f_prod) : 
                            $f_stock = $f_prod->is_in_stock();
                        ?>
                        <div class="starter-drop-card">
                            <div class="starter-drop-thumb">
                                <a href="<?php echo esc_url($f_prod->get_permalink()); ?>">
                                    <?php echo $f_prod->get_image('thumbnail'); ?>
                                </a>
                            </div>
                            <div class="starter-drop-info">
                                <a href="<?php echo esc_url($f_prod->get_permalink()); ?>" class="starter-drop-name"><?php echo esc_html($f_prod->get_name()); ?></a>
                                <span class="starter-drop-price"><?php echo $f_prod->get_price_html(); ?></span>
                            </div>
                            <?php if ($f_stock) : ?>
                                <a href="<?php echo esc_url($f_prod->add_to_cart_url()); ?>" data-product_id="<?php echo esc_attr($f_prod->get_id()); ?>" class="btn-starter-add ajax_add_to_cart add_to_cart_button" aria-label="<?php esc_attr_e('Add to basket', 'neebites'); ?>">
                                    + <?php esc_html_e('Add', 'neebites'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Bottom Guarantee Perks -->
                <div class="mini-cart-trust-perks">
                    <div class="trust-perk-item">
                        <span>🍫</span>
                        <span>100% Pure Cocoa Butter</span>
                    </div>
                    <div class="trust-perk-item">
                        <span>❄️</span>
                        <span>Cold-Pack Delivery</span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Render Wishlist Drawer Content
 */
function neebites_render_wishlist_drawer_content() {
    $wishlist = [];
    if (isset($_COOKIE['neebites_wishlist'])) {
        $decoded = json_decode(stripslashes($_COOKIE['neebites_wishlist']), true);
        if (is_array($decoded)) {
            $wishlist = array_values(array_filter($decoded));
        }
    }

    $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
    $valid_products = [];
    if (!empty($wishlist) && function_exists('wc_get_product')) {
        foreach ($wishlist as $pid) {
            $product = wc_get_product($pid);
            if ($product && $product->exists()) {
                $valid_products[] = $product;
            }
        }
    }
    $wishlist_count = count($valid_products);
    ?>
    <div class="wishlist-drawer-content" id="neebites-wishlist-drawer-inner">
        <?php if ($wishlist_count > 0) : ?>
            <!-- Sub-header bar showing count -->
            <div class="wishlist-drawer-subhead">
                <span class="subhead-count">
                    <span class="subhead-icon">🍫</span>
                    <?php 
                    /* translators: %d: number of saved chocolates */
                    printf(esc_html(_n('%d confectionery treat saved', '%d confectionery treats saved', $wishlist_count, 'neebites')), $wishlist_count); 
                    ?>
                </span>
                <span class="subhead-badge"><?php esc_html_e('Your Chocolate Box', 'neebites'); ?></span>
            </div>

            <div class="wishlist-items-list">
                <?php 
                foreach ($valid_products as $product) : 
                    $pid = $product->get_id();
                    $in_stock = $product->is_in_stock();
                ?>
                <div class="wishlist-item" data-product-id="<?php echo esc_attr($pid); ?>">
                    <button type="button" class="btn-wishlist-remove" data-product-id="<?php echo esc_attr($pid); ?>" aria-label="<?php esc_attr_e('Remove from wishlist', 'neebites'); ?>" title="<?php esc_attr_e('Remove', 'neebites'); ?>">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>

                    <div class="wishlist-item-main">
                        <div class="wishlist-thumb">
                            <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                <?php echo $product->get_image('thumbnail'); ?>
                            </a>
                        </div>
                        <div class="wishlist-details">
                            <h4 class="wishlist-item-title">
                                <a href="<?php echo esc_url($product->get_permalink()); ?>" title="<?php echo esc_attr($product->get_name()); ?>"><?php echo esc_html($product->get_name()); ?></a>
                            </h4>
                            <div class="wishlist-meta-row">
                                <div class="wishlist-price"><?php echo $product->get_price_html(); ?></div>
                                <span class="wishlist-stock-pill <?php echo $in_stock ? 'in-stock' : 'out-of-stock'; ?>">
                                    <span class="stock-dot"></span>
                                    <?php echo $in_stock ? esc_html__('Fresh Roast In Stock', 'neebites') : esc_html__('Next Batch Roasting', 'neebites'); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <?php if ($in_stock) : ?>
                        <div class="wishlist-item-footer">
                            <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-product_id="<?php echo esc_attr($pid); ?>" class="btn-move-to-cart ajax_add_to_cart add_to_cart_button" aria-label="<?php esc_attr_e('Add to Basket', 'neebites'); ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                <span><?php esc_html_e('Add to Basket', 'neebites'); ?></span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Complete Chocolate Pairing & Discovery Section -->
            <?php
            $cross_sells = [];
            if (function_exists('wc_get_products')) {
                $cross_sells = wc_get_products([
                    'status' => 'publish',
                    'limit' => 2,
                    'exclude' => $wishlist,
                    'orderby' => 'menu_order',
                    'order' => 'ASC',
                ]);
            }
            if (!empty($cross_sells)) :
            ?>
            <div class="wishlist-cross-sells">
                <div class="cross-sells-header">
                    <span class="cross-sells-icon">🍫</span>
                    <div class="cross-sells-header-text">
                        <strong><?php esc_html_e('Complete Your Chocolate Box', 'neebites'); ?></strong>
                        <span class="cross-sells-tagline"><?php esc_html_e('Artisan pairings recommended by our chocolatiers', 'neebites'); ?></span>
                    </div>
                </div>
                <div class="cross-sells-list">
                    <?php foreach ($cross_sells as $cs_prod) : 
                        $cs_stock = $cs_prod->is_in_stock();
                    ?>
                    <div class="cross-sell-card">
                        <div class="cross-sell-thumb">
                            <a href="<?php echo esc_url($cs_prod->get_permalink()); ?>">
                                <?php echo $cs_prod->get_image('thumbnail'); ?>
                            </a>
                        </div>
                        <div class="cross-sell-info">
                            <a href="<?php echo esc_url($cs_prod->get_permalink()); ?>" class="cross-sell-title"><?php echo esc_html($cs_prod->get_name()); ?></a>
                            <span class="cross-sell-price"><?php echo $cs_prod->get_price_html(); ?></span>
                        </div>
                        <?php if ($cs_stock) : ?>
                            <a href="<?php echo esc_url($cs_prod->add_to_cart_url()); ?>" data-product_id="<?php echo esc_attr($cs_prod->get_id()); ?>" class="btn-cross-sell-add ajax_add_to_cart add_to_cart_button" aria-label="<?php esc_attr_e('Add to basket', 'neebites'); ?>">
                                + <?php esc_html_e('Add', 'neebites'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Wishlist Footer Actions & Guarantees -->
            <div class="wishlist-footer">
                <div class="wishlist-footer-buttons">
                    <button type="button" class="btn btn-primary btn-block btn-move-all-wishlist">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                        <span><?php esc_html_e('Move All to Basket', 'neebites'); ?></span>
                    </button>
                    <a href="<?php echo esc_url($shop_url); ?>" class="btn btn-outline btn-block btn-wishlist-explore">
                        <span><?php esc_html_e('Continue Exploring Chocolates', 'neebites'); ?></span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </div>
                <div class="wishlist-perks-row">
                    <div class="wishlist-perk">
                        <span class="perk-ico">🍫</span>
                        <span><?php esc_html_e('100% Pure Cocoa Butter', 'neebites'); ?></span>
                    </div>
                    <div class="wishlist-perk">
                        <span class="perk-ico">❄️</span>
                        <span><?php esc_html_e('Cold-Pack Safe Delivery', 'neebites'); ?></span>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="wishlist-empty">
                <div class="empty-icon-wishlist">
                    <div class="empty-heart-ring">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    </div>
                </div>
                <h3 class="wishlist-empty-title"><?php esc_html_e('Your Chocolate Wishlist is Empty', 'neebites'); ?></h3>
                <p class="wishlist-empty-desc"><?php esc_html_e('Explore our artisan chocolate coated almonds collection and tap the heart icon on any flavour variant to save your favorite treats here.', 'neebites'); ?></p>
                <a href="<?php echo esc_url($shop_url); ?>" class="btn btn-primary btn-wishlist-empty-cta">
                    <span><?php esc_html_e('Explore Artisan Chocolates', 'neebites'); ?></span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Render Both Off-Canvas Drawers (Cart & Wishlist) in wp_footer
 */
function neebites_render_cart_drawer_structure() {
    ?>
    <!-- Mini Cart Off-Canvas Drawer -->
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
            <?php neebites_render_mini_cart_content(); ?>
        </div>
    </aside>

    <!-- Wishlist Off-Canvas Drawer -->
    <aside id="neebites-wishlist-drawer" class="neebites-wishlist-drawer neebites-cart-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Saved Chocolate Wishlist', 'neebites'); ?>" hidden>
        <div class="cart-drawer-header">
            <h3 class="drawer-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                <?php esc_html_e('Your Chocolate Wishlist', 'neebites'); ?>
            </h3>
            <button type="button" class="btn-wishlist-drawer-close btn-drawer-close" aria-label="<?php esc_attr_e('Close Wishlist Drawer', 'neebites'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="cart-drawer-body">
            <?php neebites_render_wishlist_drawer_content(); ?>
        </div>
    </aside>

    <!-- Unified Backdrop -->
    <div id="neebites-drawer-backdrop" class="neebites-drawer-backdrop" hidden></div>
    <?php
}
add_action('wp_footer', 'neebites_render_cart_drawer_structure', 50);

/**
 * =========================================================================
 * WOODMART FASHION-2 CHECKOUT ARCHITECTURE
 * 1:1 Match with Woodmart Reference Photo
 * =========================================================================
 */

/**
 * Woodmart Checkout: Reorder billing fields to match reference photo
 * 1. Email address (wide)
 * 2. First name (half) & Last name (half)
 * 3. Company name (wide)
 * 4. Country / Region (wide)
 * 5. Street address (2 rows)
 * 6. Town / City (wide)
 * 7. State (wide)
 * 8. PIN Code (wide)
 * 9. Phone (wide)
 */
function neebites_woodmart_billing_fields_order( $fields ) {
    if ( isset( $fields['billing_email'] ) ) {
        $fields['billing_email']['priority'] = 1;
        $fields['billing_email']['class'] = array( 'form-row-wide' );
    }
    if ( isset( $fields['billing_first_name'] ) ) {
        $fields['billing_first_name']['priority'] = 10;
        $fields['billing_first_name']['class'] = array( 'form-row-first' );
    }
    if ( isset( $fields['billing_last_name'] ) ) {
        $fields['billing_last_name']['priority'] = 20;
        $fields['billing_last_name']['class'] = array( 'form-row-last' );
    }
    if ( isset( $fields['billing_company'] ) ) {
        $fields['billing_company']['priority'] = 30;
        $fields['billing_company']['class'] = array( 'form-row-wide' );
    }
    if ( isset( $fields['billing_country'] ) ) {
        $fields['billing_country']['priority'] = 40;
        $fields['billing_country']['class'] = array( 'form-row-wide' );
    }
    if ( isset( $fields['billing_address_1'] ) ) {
        $fields['billing_address_1']['priority'] = 50;
        $fields['billing_address_1']['placeholder'] = _x( 'House number and street name', 'placeholder', 'neebites' );
        $fields['billing_address_1']['class'] = array( 'form-row-wide' );
    }
    if ( isset( $fields['billing_address_2'] ) ) {
        $fields['billing_address_2']['priority'] = 60;
        $fields['billing_address_2']['placeholder'] = _x( 'Apartment, suite, unit, etc. (optional)', 'placeholder', 'neebites' );
        $fields['billing_address_2']['class'] = array( 'form-row-wide' );
    }
    if ( isset( $fields['billing_city'] ) ) {
        $fields['billing_city']['priority'] = 70;
        $fields['billing_city']['class'] = array( 'form-row-first' );
    }
    if ( isset( $fields['billing_state'] ) ) {
        $fields['billing_state']['priority'] = 80;
        $fields['billing_state']['class'] = array( 'form-row-last' );
    }
    if ( isset( $fields['billing_postcode'] ) ) {
        $fields['billing_postcode']['priority'] = 90;
        $fields['billing_postcode']['label'] = __( 'PIN Code', 'neebites' );
        $fields['billing_postcode']['class'] = array( 'form-row-first' );
    }
    if ( isset( $fields['billing_phone'] ) ) {
        $fields['billing_phone']['priority'] = 100;
        $fields['billing_phone']['class'] = array( 'form-row-last' );
    }
    return $fields;
}
add_filter( 'woocommerce_billing_fields', 'neebites_woodmart_billing_fields_order', 99 );

/**
 * AJAX handler for updating cart item quantity directly from checkout review table
 */
function neebites_ajax_update_checkout_qty() {
    $cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
    $quantity      = isset( $_POST['quantity'] ) ? intval( $_POST['quantity'] ) : 1;

    if ( ! empty( $cart_item_key ) && function_exists( 'WC' ) && WC()->cart ) {
        if ( $quantity > 0 ) {
            WC()->cart->set_quantity( $cart_item_key, $quantity, true );
        } else {
            WC()->cart->remove_cart_item( $cart_item_key );
        }
        WC()->cart->calculate_totals();
        wp_send_json_success();
    }
    wp_send_json_error();
}
add_action( 'wp_ajax_neebites_update_checkout_qty', 'neebites_ajax_update_checkout_qty' );
add_action( 'wp_ajax_nopriv_neebites_update_checkout_qty', 'neebites_ajax_update_checkout_qty' );

/**
 * Ensure Checkout Page renders Classic Woodmart Checkout
 */
function neebites_ensure_classic_woodmart_checkout( $content ) {
    if ( function_exists( 'is_checkout' ) && is_checkout() && ( ! function_exists( 'is_wc_endpoint_url' ) || ! is_wc_endpoint_url() ) ) {
        if ( false !== strpos( $content, 'wp:woocommerce/checkout' ) || empty( $content ) ) {
            return do_shortcode( '[woocommerce_checkout]' );
        }
    }
    return $content;
}
add_filter( 'the_content', 'neebites_ensure_classic_woodmart_checkout', 1 );

add_action( 'init', function() {
    if ( get_option( 'neebites_woodmart_checkout_v252' ) !== 'yes' && function_exists( 'wc_get_page_id' ) ) {
        $page_id = wc_get_page_id( 'checkout' );
        if ( $page_id > 0 ) {
            $post = get_post( $page_id );
            if ( $post && false !== strpos( $post->post_content, 'wp:woocommerce/checkout' ) ) {
                wp_update_post( array(
                    'ID'           => $page_id,
                    'post_content' => '[woocommerce_checkout]',
                ) );
            }
        }
        update_option( 'neebites_woodmart_checkout_v252', 'yes' );
    }
} );

