<?php
/**
 * WooCommerce Product Loop Customizations (Cards, Badges, Hover Effects)
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

// Remove default loop elements to rebuild the custom plnts.com card
remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

/**
 * Confectionery High-Resolution Photography Image Mapping Helper
 */
function neebites_get_product_fallback_image($product) {
    $default_img = get_template_directory_uri() . '/assets/images/products/choc-dark-almond.jpg';
    if (!$product) return $default_img;

    $sku  = strtoupper((string) $product->get_sku());
    $slug = strtolower((string) $product->get_slug());
    $name = strtolower((string) $product->get_name());

    // 1. Direct SKU mapping to luxury photography
    $map = [
        'CHOC-ALM-DARK-01' => 'choc-dark-almond.jpg',
        'CHOC-ALM-KIWI-02' => 'choc-kiwi-almond.jpg',
        'CHOC-ALM-MILK-03' => 'choc-milk-almond.jpg',
        'CHOC-ALM-WHT-04'  => 'choc-white-almond.jpg',
        'CHOC-ALM-MAT-05'  => 'choc-matcha-almond.jpg',
        'CHOC-ALM-CAR-06'  => 'choc-caramel-almond.jpg',
        'CHOC-ALM-CIN-07'  => 'choc-cinnamon-almond.jpg',
        'CHOC-ALM-RUB-08'  => 'choc-ruby-almond.jpg',
        // Legacy plant SKUs mapped to matching confectionery treats
        'PLNT-MONST-01'    => 'choc-dark-almond.jpg',
        'PLNT-PHIL-03'     => 'choc-caramel-almond.jpg',
        'PLNT-ALOC-02'     => 'choc-milk-almond.jpg',
        'PLNT-FICUS-04'    => 'choc-white-almond.jpg',
        'PLNT-BABY-05'     => 'choc-kiwi-almond.jpg',
        'PLNT-CALA-06'     => 'choc-matcha-almond.jpg',
        'PLNT-SANS-07'     => 'choc-cinnamon-almond.jpg',
        'ACC-POT-08'       => 'choc-ruby-almond.jpg',
    ];

    if (isset($map[$sku])) {
        return get_template_directory_uri() . '/assets/images/products/' . $map[$sku];
    }

    // 2. Keyword mapping in name or slug
    if (strpos($name, 'kiwi') !== false || strpos($slug, 'kiwi') !== false) {
        return get_template_directory_uri() . '/assets/images/products/choc-kiwi-almond.jpg';
    }
    if (strpos($name, 'dark') !== false || strpos($name, 'belgian') !== false || strpos($name, '70%') !== false || strpos($slug, 'dark') !== false) {
        return get_template_directory_uri() . '/assets/images/products/choc-dark-almond.jpg';
    }
    if (strpos($name, 'matcha') !== false || strpos($name, 'tea') !== false || strpos($slug, 'matcha') !== false) {
        return get_template_directory_uri() . '/assets/images/products/choc-matcha-almond.jpg';
    }
    if (strpos($name, 'caramel') !== false || strpos($name, 'fleur') !== false || strpos($slug, 'caramel') !== false) {
        return get_template_directory_uri() . '/assets/images/products/choc-caramel-almond.jpg';
    }
    if (strpos($name, 'white') !== false || strpos($name, 'vanilla') !== false || strpos($slug, 'white') !== false) {
        return get_template_directory_uri() . '/assets/images/products/choc-white-almond.jpg';
    }
    if (strpos($name, 'cinnamon') !== false || strpos($name, 'honey') !== false || strpos($slug, 'cinnamon') !== false) {
        return get_template_directory_uri() . '/assets/images/products/choc-cinnamon-almond.jpg';
    }
    if (strpos($name, 'ruby') !== false || strpos($name, 'raspberry') !== false || strpos($slug, 'ruby') !== false) {
        return get_template_directory_uri() . '/assets/images/products/choc-ruby-almond.jpg';
    }
    if (strpos($name, 'milk') !== false || strpos($name, 'swiss') !== false || strpos($slug, 'milk') !== false) {
        return get_template_directory_uri() . '/assets/images/products/choc-milk-almond.jpg';
    }

    return $default_img;
}

/**
 * Confectionery Flavor Group Classifier for Real Filter Functionality
 *
 * @param WC_Product|int|null $product
 * @return string 'dark'|'kiwi'|'milk'|'white'|'other'
 */
function neebites_get_product_flavor_group($product) {
    if (is_numeric($product) && function_exists('wc_get_product')) {
        $product = wc_get_product($product);
    }
    if (!$product || !is_a($product, 'WC_Product')) {
        return 'other';
    }

    $id   = $product->get_id();
    $name = strtolower($product->get_name());
    $slug = strtolower($product->get_slug());
    $sku  = strtoupper((string) $product->get_sku());
    $cats = strtolower(wp_strip_all_tags(wc_get_product_category_list($id, ' ')));
    $tags = strtolower(wp_strip_all_tags(wc_get_product_tag_list($id, ' ')));
    $text = $name . ' ' . $slug . ' ' . $sku . ' ' . $cats . ' ' . $tags;

    if (strpos($text, 'dark') !== false || strpos($text, '70%') !== false || strpos($text, 'belgian') !== false || strpos($sku, 'DARK') !== false) {
        return 'dark';
    }
    if (strpos($text, 'kiwi') !== false || strpos($sku, 'KIWI') !== false) {
        return 'kiwi';
    }
    if (strpos($text, 'white') !== false || strpos($text, 'vanilla') !== false || strpos($sku, 'WHT') !== false) {
        return 'white';
    }
    if (strpos($text, 'milk') !== false || strpos($text, 'swiss') !== false || strpos($text, '38%') !== false || strpos($sku, 'MILK') !== false) {
        return 'milk';
    }
    return 'other'; // matcha, caramel, cinnamon, ruby, gourmet, etc.
}

/**
 * Custom Neebites Product Card Template for Loop (Shop & Related Products)
 */
function neebites_template_loop_product_card() {
    global $product;
    if (!$product) return;

    $id             = $product->get_id();
    $permalink      = get_permalink($id);
    $title          = get_the_title();
    $attachment_ids = $product->get_gallery_image_ids();
    $secondary_id   = !empty($attachment_ids) ? $attachment_ids[0] : 0;
    $is_in_stock    = $product->is_in_stock();

    // Check wishlist state
    $is_wishlisted = false;
    if (isset($_COOKIE['neebites_wishlist'])) {
        $wishlist = json_decode(stripslashes($_COOKIE['neebites_wishlist']), true);
        if (is_array($wishlist) && in_array($id, $wishlist)) {
            $is_wishlisted = true;
        }
    }

    // Build filter tokens
    $cat_terms = wp_get_post_terms($id, 'product_cat', ['fields' => 'names']);
    $tag_terms = wp_get_post_terms($id, 'product_tag', ['fields' => 'names']);
    $cat_slugs = wp_get_post_terms($id, 'product_cat', ['fields' => 'slugs']);
    $cat_names = is_array($cat_terms) && !is_wp_error($cat_terms) ? implode(' ', $cat_terms) : '';
    $tag_names = is_array($tag_terms) && !is_wp_error($tag_terms) ? implode(' ', $tag_terms) : '';
    $cat_slug_str = is_array($cat_slugs) && !is_wp_error($cat_slugs) ? implode(' ', $cat_slugs) : '';

    $care_light = $product->get_attribute('light') ?: (string) get_post_meta($id, '_plant_light', true);
    $care_water = $product->get_attribute('water') ?: (string) get_post_meta($id, '_plant_water', true);
    $care_pets = $product->get_attribute('pets') ?: (string) get_post_meta($id, '_plant_pets', true);
    $care_difficulty = $product->get_attribute('difficulty') ?: (string) get_post_meta($id, '_plant_difficulty', true);

    $filter_tags = function_exists('neebites_derive_filter_tags')
        ? neebites_derive_filter_tags(trim($cat_names . ' ' . $tag_names), '', $care_difficulty, $care_light, $care_pets, $cat_slug_str)
        : '';

    $flavor_group = neebites_get_product_flavor_group($product);

    // Image resolution: Real High-Res Photography First
    $primary_image = '';
    if (has_post_thumbnail($id)) {
        $thumb_url = get_the_post_thumbnail_url($id, 'large');
        if ($thumb_url && strpos($thumb_url, '.svg') === false && strpos($thumb_url, 'placeholder') === false) {
            $primary_image = (string) $thumb_url;
        }
    }
    if (!$primary_image && $product->get_image_id()) {
        $att_url = wp_get_attachment_image_url($product->get_image_id(), 'large');
        if ($att_url && strpos($att_url, '.svg') === false && strpos($att_url, 'placeholder') === false) {
            $primary_image = (string) $att_url;
        }
    }
    if (!$primary_image) {
        $primary_image = neebites_get_product_fallback_image($product);
    }

    // Pricing Resolution: Consistent, High-Converting Luxury Pricing
    $is_variable = $product->is_type('variable');
    $current_price = (float) $product->get_price();
    $regular_price = (float) $product->get_regular_price();

    if ($is_variable) {
        $prices = $product->get_variation_prices(true);
        if (!empty($prices['price'])) {
            $min_price = current($prices['price']);
            $current_price = (float) $min_price;
            $regular_price = !empty($prices['regular_price']) ? (float) current($prices['regular_price']) : 0;
        }
    }

    if ($regular_price <= $current_price || $regular_price <= 0) {
        $regular_price = round($current_price * 1.25);
    }
    $discount_pct = round((($regular_price - $current_price) / $regular_price) * 100);

    // Clean Single Primary Uppercase Category
    $clean_cat = 'CHOCOLATE COATED ALMONDS';
    if (!empty($cat_terms) && !is_wp_error($cat_terms)) {
        foreach ($cat_terms as $ct) {
            $stripped = trim(preg_replace('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}]/u', '', $ct));
            if ($stripped && stripos($stripped, 'Confectionery') === false && stripos($stripped, 'Coated Nuts') === false) {
                $clean_cat = strtoupper($stripped);
                break;
            }
        }
    }

    // Two-Line Title & Subtitle Parsing
    $main_title = $title;
    $sub_title  = '';
    if (preg_match('/^(.*?)\s*\((.*?)\)$/', $title, $matches)) {
        $main_title = trim($matches[1]);
        $sub_title  = trim($matches[2]);
    } else {
        $flavor_subs = [
            'dark'  => '70% Single-Origin Belgian',
            'kiwi'  => 'Signature Real Fruit Glaze',
            'milk'  => 'Creamy 38% Swiss Velvet',
            'white' => 'Pure Bourbon Vanilla',
            'other' => 'Artisan Roasted Almond'
        ];
        $sub_title = isset($flavor_subs[$flavor_group]) ? $flavor_subs[$flavor_group] : 'Artisan Confection';
    }
    ?>
    <div class="neebites-product-card <?php echo $secondary_id ? 'has-hover-image' : ''; ?>"
        data-product-id="<?php echo esc_attr($id); ?>"
        data-name="<?php echo esc_attr($main_title); ?>"
        data-price="<?php echo esc_attr(number_format($current_price, 2)); ?>"
        data-raw-price="<?php echo esc_attr($current_price); ?>"
        data-image="<?php echo esc_url($primary_image); ?>"
        data-category="<?php echo esc_attr($clean_cat); ?>"
        data-flavor="<?php echo esc_attr($flavor_group); ?>"
        data-filter-tags="<?php echo esc_attr($filter_tags); ?>">
        
        <div class="product-card-media">
            <!-- Product Badges (Top-Left Sleek Pill) -->
            <div class="product-badges">
                <?php if (!$is_in_stock) : ?>
                    <span class="product-badge badge-outofstock"><?php esc_html_e('Sold Out', 'neebites'); ?></span>
                <?php else : ?>
                    <span class="product-badge badge-sale">-<?php echo esc_html($discount_pct); ?>%</span>
                <?php endif; ?>
            </div>

            <!-- Action Buttons: Wishlist & Quick View -->
            <div class="product-card-actions">
                <button type="button" class="btn-card-action btn-wishlist <?php echo $is_wishlisted ? 'active' : ''; ?>" data-product-id="<?php echo esc_attr($id); ?>" aria-label="<?php esc_attr_e('Add to Wishlist', 'neebites'); ?>" title="<?php esc_attr_e('Save to Wishlist', 'neebites'); ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="<?php echo $is_wishlisted ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                </button>
                <button type="button" class="btn-card-action btn-quickview" data-product-id="<?php echo esc_attr($id); ?>" aria-label="<?php esc_attr_e('Quick View', 'neebites'); ?>" title="<?php esc_attr_e('Quick Preview', 'neebites'); ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                </button>
            </div>

            <!-- Product Thumbnails (Main & Secondary Flip) -->
            <a href="<?php echo esc_url($permalink); ?>" class="product-card-image-link" tabindex="-1">
                <img src="<?php echo esc_url($primary_image); ?>" alt="<?php echo esc_attr($main_title); ?>" class="product-primary-img" loading="lazy" width="400" height="400" />
                <?php if ($secondary_id) : ?>
                    <?php echo wp_get_attachment_image($secondary_id, 'woocommerce_thumbnail', false, ['class' => 'product-secondary-img', 'loading' => 'lazy']); ?>
                <?php endif; ?>
            </a>

            <!-- Rating Chip Overlay -->
            <div class="product-card-rating">
                <?php echo neebites_rating_chip_html($product->get_average_rating(), $product->get_rating_count()); ?>
            </div>
        </div>

        <div class="product-card-info">
            <!-- Category Tag -->
            <div class="product-card-category"><?php echo esc_html($clean_cat); ?></div>

            <!-- Title & Subtitle -->
            <h3 class="product-card-title">
                <a href="<?php echo esc_url($permalink); ?>" class="product-title-brand" title="<?php echo esc_attr($main_title); ?>"><?php echo esc_html($main_title); ?></a>
                <span class="product-title-sub"><?php echo esc_html($sub_title); ?></span>
            </h3>

            <!-- Price -->
            <div class="product-card-price">
                <span class="price-current">&#8377;<?php echo number_format($current_price, 2); ?></span>
                <?php if ($discount_pct > 0) : ?>
                    <span class="price-off">(<?php echo esc_html($discount_pct); ?>% OFF)</span>
                <?php endif; ?>
            </div>

            <!-- Visible Next-Level Add to Cart Button -->
            <div class="product-card-action-bar">
                <?php if ($is_in_stock && $product->is_type('simple')) : ?>
                    <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-quantity="1" class="btn-card-add-cart add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr($id); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" aria-label="<?php echo esc_attr($product->add_to_cart_description()); ?>">
                        <span class="btn-cart-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                        </span>
                        <span class="btn-cart-label"><?php esc_html_e('Add to Cart', 'neebites'); ?></span>
                        <span class="btn-spinner" aria-hidden="true"></span>
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url($permalink); ?>" class="btn-card-add-cart btn-card-view-item">
                        <span class="btn-cart-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                        </span>
                        <span class="btn-cart-label"><?php esc_html_e('View Confection', 'neebites'); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
add_action('woocommerce_before_shop_loop_item', 'neebites_template_loop_product_card', 10);

/**
 * Product Price Helper (used in search results and quick view)
 */
function neebites_product_price($product) {
    if (!$product) return;
    echo '<div class="product-price">' . $product->get_price_html() . '</div>';
}

/**
 * Ensure the product loop <li> elements carry flavor and collection classes for real-time instant filtering
 */
add_filter('woocommerce_post_class', function ($classes, $product) {
    if ($product && function_exists('neebites_get_product_flavor_group')) {
        $flavor = neebites_get_product_flavor_group($product);
        $classes[] = 'flavor-' . $flavor;
        if ($flavor === 'milk' || $flavor === 'white') {
            $classes[] = 'flavor-milk-white';
        }
        if ($flavor === 'other') {
            $classes[] = 'flavor-gourmet';
        }
    }
    return $classes;
}, 10, 2);

add_filter('post_class', function ($classes, $class, $post_id) {
    if (get_post_type($post_id) === 'product' && function_exists('wc_get_product') && function_exists('neebites_get_product_flavor_group')) {
        $product = wc_get_product($post_id);
        if ($product) {
            $flavor = neebites_get_product_flavor_group($product);
            $classes[] = 'flavor-' . $flavor;
            if ($flavor === 'milk' || $flavor === 'white') {
                $classes[] = 'flavor-milk-white';
            }
            if ($flavor === 'other') {
                $classes[] = 'flavor-gourmet';
            }
        }
    }
    return $classes;
}, 10, 3);

