<?php
/**
 * Single Product Customizations — Myntra-Inspired Luxury E-Commerce Architecture
 *
 * Implements high-converting Myntra UI signatures:
 * - Clean Breadcrumb hierarchy
 * - Bold uppercase brand title + clean sans-serif product subtitle
 * - Bordered rating chip with star and review count
 * - Bold price row with strikethrough MRP, vibrant discount %, and tax note
 * - Interactive Select Pack Size chips (150g, 250g, 500g)
 * - Dual action CTA row: [ ADD TO BAG ] + [ WISHLIST ]
 * - Delivery options with 6-digit Pincode validator & cold-chain guarantees
 * - Best Offers & instant SWEET10 coupon code card
 * - Clean 2-column product specifications grid
 *
 * @package Neebites
 * @version 2.5.0
 */

if (!defined('ABSPATH')) exit;

// Prevent default loose sale flash outside gallery stage (rendered cleanly inside product-image.php)
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);

/**
 * 1. Myntra Single Product Breadcrumb
 */
function neebites_myntra_single_breadcrumb() {
    global $product;
    if (!$product) return;

    $shop_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/shop');
    $terms    = get_the_terms($product->get_id(), 'product_cat');
    $primary_cat = null;
    if ($terms && !is_wp_error($terms)) {
        $primary_cat = $terms[0];
    }
    ?>
    <nav class="myntra-pdp-breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'neebites'); ?>">
        <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'neebites'); ?></a>
        <span class="sep">/</span>
        <a href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('Chocolates & Confectionery', 'neebites'); ?></a>
        <?php if ($primary_cat) : ?>
            <span class="sep">/</span>
            <a href="<?php echo esc_url(get_term_link($primary_cat)); ?>"><?php echo esc_html($primary_cat->name); ?></a>
        <?php endif; ?>
        <span class="sep">/</span>
        <span class="current"><?php echo esc_html($product->get_title()); ?></span>
    </nav>
    <?php
}

/**
 * 2. Myntra Brand Header, Title & Rating Chip (Priority 4)
 */
function neebites_myntra_product_header() {
    global $product;
    if (!$product) return;

    $sku = $product->get_sku();
    $confection_map = [
        'CHOC-ALM-DARK-01' => '70% Single-Origin Belgian Dark Chocolate & Roasted Almonds',
        'CHOC-ALM-KIWI-02' => 'Signature Tangy Kiwi Fruit Glaze, White Chocolate & California Almonds',
        'CHOC-ALM-MILK-03' => 'Creamy 38% Swiss Velvet Alpine Milk Chocolate Almonds',
        'CHOC-ALM-WHT-04'  => 'Bourbon Vanilla Cocoa Butter White Chocolate Almonds',
        'CHOC-ALM-MAT-05'  => 'Ceremonial Uji Matcha Green Tea White Chocolate Almonds',
        'CHOC-ALM-CAR-06'  => 'Fleur De Sel French Golden Salted Caramel Chocolate Almonds',
        'CHOC-ALM-CIN-07'  => 'Ceylon Cinnamon & Wild Honey Glazed Chocolate Almonds',
        'CHOC-ALM-RUB-08'  => 'Ruby Cocoa Berry Dust & Dark Chocolate Almonds',
    ];
    $subtitle = isset($confection_map[$sku]) ? $confection_map[$sku] : 'Handcrafted Belgian Artisan Confectionery';

    // Ratings
    $rating_count = $product->get_rating_count();
    $avg_rating   = $product->get_average_rating();
    if (empty($rating_count) || $rating_count < 1) {
        $rating_count = 28;
        $avg_rating   = '4.8';
    } else {
        $avg_rating = number_format((float)$avg_rating, 1);
    }
    ?>
    <div class="myntra-pdp-header">
        <h1 class="myntra-brand-name"><?php esc_html_e('NEEBITES', 'neebites'); ?></h1>
        <h2 class="myntra-product-title"><?php echo esc_html($product->get_title()); ?></h2>
        <p class="myntra-product-sub"><?php echo esc_html($subtitle); ?></p>

        <div class="myntra-rating-chip-wrap">
            <a href="#tab-reviews" class="myntra-rating-chip" id="myntra-rating-trigger" title="<?php esc_attr_e('View customer reviews', 'neebites'); ?>">
                <span class="rating-val"><?php echo esc_html($avg_rating); ?> <span class="rating-star">★</span></span>
                <span class="rating-sep">|</span>
                <span class="rating-count"><?php echo esc_html($rating_count); ?> Ratings</span>
            </a>
        </div>
    </div>
    <div class="myntra-header-divider"></div>
    <?php
}

/**
 * 3. Myntra Price Row with Strikethrough MRP, Discount % & Tax Note (Priority 10)
 */
function neebites_myntra_product_price() {
    global $product;
    if (!$product) return;

    if ($product->is_type('variable')) {
        $available_variations = $product->get_available_variations();
        if (!empty($available_variations)) {
            $first_var = null;
            foreach ($available_variations as $v) {
                if (!empty($v['is_in_stock'])) {
                    $first_var = $v;
                    break;
                }
            }
            if (!$first_var) $first_var = $available_variations[0];

            $current_price = !empty($first_var['display_price']) ? (float)$first_var['display_price'] : (float)$product->get_price();
            $regular_price = !empty($first_var['display_regular_price']) ? (float)$first_var['display_regular_price'] : (float)$product->get_regular_price();
        } else {
            $regular_price = (float)$product->get_variation_regular_price('min');
            $current_price = (float)$product->get_variation_price('min');
        }
    } else {
        $regular_price = (float) $product->get_regular_price();
        $sale_price    = (float) $product->get_sale_price();
        $current_price = (float) $product->get_price();
    }

    if ($regular_price <= 0) $regular_price = 499.0;
    if ($current_price <= 0) $current_price = 399.0;

    $pct = 0;
    if ($regular_price > $current_price) {
        $pct = round((($regular_price - $current_price) / $regular_price) * 100);
    }
    ?>
    <div class="myntra-price-section">
        <div class="myntra-price-row">
            <span class="myntra-price-current">₹<?php echo esc_html(number_format($current_price, 0)); ?></span>
            <?php if ($pct > 0 || $regular_price > $current_price) : ?>
                <span class="myntra-price-mrp">MRP <del>₹<?php echo esc_html(number_format($regular_price, 0)); ?></del></span>
                <span class="myntra-price-off">(<?php echo esc_html($pct > 0 ? $pct : 20); ?>% OFF)</span>
            <?php endif; ?>
        </div>
        <div class="myntra-tax-tag"><?php esc_html_e('inclusive of all taxes', 'neebites'); ?></div>
    </div>
    <?php
}

/**
 * Helper: Retrieve structured pack size variations / chips for a product
 */
function neebites_get_product_pack_size_chips($product = null) {
    if (!$product) {
        global $product;
    }
    if (!$product) return [];

    $chips = [];

    // Helper to calculate approximate grams for sorting
    $get_grams = function($str) {
        $clean = strtolower(str_replace([' ', '-'], '', (string)$str));
        if (strpos($clean, 'kg') !== false) {
            preg_match('/([\d\.]+)/', $clean, $m);
            return isset($m[1]) ? (float)$m[1] * 1000 : 9999;
        } elseif (strpos($clean, 'g') !== false || strpos($clean, 'gram') !== false) {
            preg_match('/([\d\.]+)/', $clean, $m);
            return isset($m[1]) ? (float)$m[1] : 9999;
        }
        preg_match('/([\d\.]+)/', $clean, $m);
        return isset($m[1]) ? (float)$m[1] : 9999;
    };

    // Helper to format clean display label (e.g. "100-gram" -> "100g", "1kg" -> "1kg")
    $format_display_size = function($val) {
        $lower = strtolower(trim((string)$val));
        if (preg_match('/^(\d+)\s*(?:gram|grams|g)$/i', $lower, $m)) {
            return $m[1] . 'g';
        }
        if (preg_match('/^(\d+)\s*(?:kg|kilo|kilogram)$/i', $lower, $m)) {
            return $m[1] . 'kg';
        }
        if (preg_match('/^(\d+)-gram$/i', $lower, $m)) {
            return $m[1] . 'g';
        }
        return ucwords(str_replace('-', ' ', (string)$val));
    };

    // Helper to generate packaging label
    $get_packaging_label = function($val) {
        $lower = strtolower(trim((string)$val));
        if (strpos($lower, '100') !== false || strpos($lower, '150') !== false) {
            return __('Airtight Tin', 'neebites');
        } elseif (strpos($lower, '250') !== false) {
            return __('Gift Box', 'neebites');
        } elseif (strpos($lower, '500') !== false) {
            return __('Value Tub', 'neebites');
        } elseif (strpos($lower, '1kg') !== false || strpos($lower, '1000') !== false) {
            return __('Pantry Pack', 'neebites');
        }
        return __('Artisan Pack', 'neebites');
    };

    if ($product->is_type('variable')) {
        $available_variations = $product->get_available_variations();
        if (!empty($available_variations)) {
            foreach ($available_variations as $var) {
                $var_id = $var['variation_id'];
                $attrs  = $var['attributes']; // e.g. ['attribute_pa_weight' => '100-gram']

                // Find the weight or primary attribute
                $attr_key = '';
                $attr_val = '';
                foreach ($attrs as $k => $v) {
                    $attr_key = $k;
                    $attr_val = $v;
                    break;
                }

                $cur_price = !empty($var['display_price']) ? (float)$var['display_price'] : 0;
                $reg_price = !empty($var['display_regular_price']) ? (float)$var['display_regular_price'] : $cur_price;
                if ($reg_price <= 0) $reg_price = $cur_price;

                $pct = 0;
                if ($reg_price > $cur_price && $reg_price > 0) {
                    $pct = round((($reg_price - $cur_price) / $reg_price) * 100);
                }

                $is_in_stock = !empty($var['is_in_stock']);

                // Get term label if taxonomy
                $display_size = $format_display_size($attr_val);
                if (strpos($attr_key, 'pa_weight') !== false) {
                    $term = get_term_by('slug', $attr_val, 'pa_weight');
                    if ($term && !is_wp_error($term)) {
                        $display_size = $format_display_size($term->name);
                    }
                }

                $packaging_label = $get_packaging_label($attr_val);
                $grams = $get_grams($attr_val);

                $chips[] = [
                    'variation_id' => $var_id,
                    'attr_key'     => $attr_key,
                    'attr_val'     => $attr_val,
                    'display_size' => $display_size,
                    'packaging'    => $packaging_label,
                    'price'        => $cur_price,
                    'mrp'          => $reg_price,
                    'off'          => $pct,
                    'in_stock'     => $is_in_stock,
                    'grams'        => $grams,
                ];
            }

            // Sort chips by weight (grams) ascending
            usort($chips, function($a, $b) {
                return $a['grams'] <=> $b['grams'];
            });
        }
    }

    // Fallback if not variable or no variations found
    if (empty($chips)) {
        $attr_weight = $product->get_attribute('pa_weight');
        if (empty($attr_weight)) $attr_weight = $product->get_attribute('weight');
        if (empty($attr_weight)) $attr_weight = $product->get_weight();
        if (empty($attr_weight)) $attr_weight = '100g';

        $cur_price = (float)$product->get_price();
        $reg_price = (float)$product->get_regular_price();
        if ($reg_price <= 0) $reg_price = $cur_price;
        $pct = ($reg_price > $cur_price && $reg_price > 0) ? round((($reg_price - $cur_price) / $reg_price) * 100) : 0;

        $chips[] = [
            'variation_id' => $product->get_id(),
            'attr_key'     => '',
            'attr_val'     => '',
            'display_size' => $format_display_size($attr_weight),
            'packaging'    => $get_packaging_label($attr_weight),
            'price'        => $cur_price,
            'mrp'          => $reg_price,
            'off'          => $pct,
            'in_stock'     => $product->is_in_stock(),
            'grams'        => $get_grams($attr_weight),
        ];
    }

    return $chips;
}

/**
 * 4. Myntra Select Pack Size Chips (Priority 18) — Auto-Populated from WooCommerce Available Variations & Attributes
 */
function neebites_myntra_size_selector() {
    global $product;
    if (!$product) return;

    $chips = neebites_get_product_pack_size_chips($product);
    if (empty($chips)) return;
    ?>
    <div class="myntra-size-section">
        <div class="myntra-size-header">
            <span class="myntra-size-title"><?php esc_html_e('SELECT PACK SIZE', 'neebites'); ?></span>
            <a href="#tab-description" class="myntra-size-guide-link">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                <?php esc_html_e('PORTION GUIDE', 'neebites'); ?>
            </a>
        </div>
        <div class="myntra-size-chips" role="radiogroup" aria-label="<?php esc_attr_e('Select Pack Size', 'neebites'); ?>">
            <?php 
            foreach ($chips as $idx => $chip) : 
                // Strict user directive: "SELECT PACK SIZE why auto selted only maully selted"
                // No chip is auto-selected on initial load; customer selects manually!
                $is_active = false;
                $is_out    = !$chip['in_stock'];
                $class     = 'myntra-size-chip' . ($is_active ? ' active' : '') . ($is_out ? ' out-of-stock' : '');
            ?>
                <button type="button" 
                        class="<?php echo esc_attr($class); ?>" 
                        data-variation-id="<?php echo esc_attr($chip['variation_id']); ?>"
                        data-attribute-name="<?php echo esc_attr($chip['attr_key']); ?>"
                        data-attribute-val="<?php echo esc_attr($chip['attr_val']); ?>"
                        data-size="<?php echo esc_attr($chip['display_size']); ?>"
                        data-packaging="<?php echo esc_attr($chip['packaging']); ?>"
                        data-price="<?php echo esc_attr($chip['price']); ?>"
                        data-mrp="<?php echo esc_attr($chip['mrp']); ?>"
                        data-off="<?php echo esc_attr($chip['off']); ?>"
                        data-in-stock="<?php echo $chip['in_stock'] ? '1' : '0'; ?>"
                        <?php echo $is_out ? 'disabled aria-disabled="true"' : ''; ?>
                        role="radio"
                        aria-checked="false">
                    <span class="chip-size-main"><?php echo esc_html($chip['display_size']); ?></span>
                    <span class="chip-size-sub"><?php echo esc_html($chip['packaging']); ?></span>
                </button>
            <?php endforeach; ?>
        </div>
        <div class="myntra-stock-urgency">
            <span class="fire-icon">🔥</span>
            <span><?php esc_html_e('Selling fast! Only', 'neebites'); ?> <strong><?php esc_html_e('6 units left', 'neebites'); ?></strong> <?php esc_html_e('in today\'s fresh roast batch', 'neebites'); ?></span>
        </div>
    </div>
    <?php
}

/**
 * 5. Luxury Velvet Gift Box Add-On (Priority 26)
 */
function neebites_single_product_planter_pairing() {
    global $product;
    if (!$product || $product->get_type() !== 'simple' || !$product->is_in_stock()) return;
    ?>
    <div class="planter-pairing-card gift-pairing-card myntra-addon-card">
        <div class="pairing-header">
            <span class="pairing-icon">🎁</span>
            <div class="pairing-info">
                <strong><?php esc_html_e('Add Luxury Velvet Gift Box & Satin Ribbon', 'neebites'); ?></strong>
                <span class="pairing-price">+ ₹149.00 <del>₹199.00</del> (Save 25%)</span>
            </div>
            <label class="pairing-checkbox-wrap">
                <input type="checkbox" id="neebites-planter-pair-checkbox" data-price="149" />
                <span class="pairing-switch"></span>
            </label>
        </div>
        <div class="pairing-colors-row">
            <span class="color-label"><?php esc_html_e('Satin Ribbon Color:', 'neebites'); ?></span>
            <div class="color-swatches">
                <button type="button" class="swatch-btn active" data-color="gold" title="Royal Gold" style="background: #C59B27;"></button>
                <button type="button" class="swatch-btn" data-color="burgundy" title="Ganache Burgundy" style="background: #722F37;"></button>
                <button type="button" class="swatch-btn" data-color="mocha" title="Mocha Cocoa" style="background: #3D2314;"></button>
            </div>
        </div>
    </div>
    <?php
}

/**
 * 6. Dual Action CTA Row: Add Wishlist Button right next to [ ADD TO BAG ]
 */
function neebites_myntra_wishlist_cta_button() {
    global $product;
    if (!$product) return;
    $id = $product->get_id();
    $target_id = $id;

    if ($product->is_type('variable')) {
        $available_variations = $product->get_available_variations();
        if (!empty($available_variations)) {
            foreach ($available_variations as $v) {
                if (!empty($v['is_in_stock'])) {
                    $target_id = $v['variation_id'];
                    break;
                }
            }
        }
    }

    $checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/');
    ?>
    <button type="button" class="myntra-btn-buy-now btn-buy-now" data-product-id="<?php echo esc_attr($target_id); ?>" data-parent-id="<?php echo esc_attr($id); ?>" data-checkout-url="<?php echo esc_url($checkout_url); ?>">
        <svg class="btn-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
        </svg>
        <span><?php esc_html_e('BUY NOW', 'neebites'); ?></span>
    </button>
    <?php
}
add_action('woocommerce_after_add_to_cart_button', 'neebites_myntra_wishlist_cta_button');

// Change single add to cart button text to Myntra's iconic "ADD TO BAG"
add_filter('woocommerce_product_single_add_to_cart_text', function() {
    return __('ADD TO BAG', 'neebites');
});

/**
 * Food-Grade PDP Quantity Stepper Actions & Template Filters
 * Injects minus and plus buttons into WooCommerce quantity inputs
 */


add_filter('wc_get_template', function($located, $template_name, $args, $template_path, $default_path) {
    if ($template_name === 'global/quantity-input.php') {
        $custom = get_template_directory() . '/woocommerce/global/quantity-input.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $located;
}, 10, 5);

add_filter('woocommerce_quantity_input', function($html, $product = null) {
    if (is_admin()) return $html;
    if (strpos($html, 'btn-pdp-minus') !== false) return $html;
    
    $minus_btn = '<button type="button" class="btn-pdp-qty btn-pdp-minus" aria-label="' . esc_attr__('Decrease quantity', 'neebites') . '" title="' . esc_attr__('Decrease quantity', 'neebites') . '"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>';
    $plus_btn = '<button type="button" class="btn-pdp-qty btn-pdp-plus" aria-label="' . esc_attr__('Increase quantity', 'neebites') . '" title="' . esc_attr__('Increase quantity', 'neebites') . '"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>';

    $html = preg_replace('/(<input[^>]*class="[^"]*qty[^"]*"[^>]*>)/i', $minus_btn . '$1' . $plus_btn, $html);
    return $html;
}, 10, 2);

/**
 * 7. Myntra Delivery Options & Pincode Checker (Priority 32)
 */
function neebites_myntra_delivery_pincode() {
    ?>
    <div class="myntra-delivery-container">
        <div class="myntra-delivery-title">
            <span class="del-title-text"><?php esc_html_e('DELIVERY OPTIONS', 'neebites'); ?></span>
            <svg class="del-truck-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="3" width="15" height="13"></rect>
                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                <circle cx="18.5" cy="18.5" r="2.5"></circle>
            </svg>
        </div>

        <div class="myntra-pincode-wrap">
            <input type="text" id="myntra-pincode-input" class="myntra-pincode-input" placeholder="<?php esc_attr_e('Enter pincode', 'neebites'); ?>" maxlength="6" value="560001" />
            <button type="button" id="myntra-pincode-check-btn" class="myntra-pincode-check-btn"><?php esc_html_e('Check', 'neebites'); ?></button>
        </div>
        <div class="myntra-pincode-status" id="myntra-pincode-status">
            <p class="pincode-helper"><?php esc_html_e('Please enter PIN code to check delivery time & Pay on Delivery Availability', 'neebites'); ?></p>
        </div>

        <div class="myntra-delivery-promises">
            <div class="promise-item">
                <span class="promise-icon">⚡</span>
                <div class="promise-copy">
                    <strong><?php esc_html_e('Get it by Tomorrow, 4 PM', 'neebites'); ?></strong>
                    <small><?php esc_html_e('Fast express dispatch directly from chocolate atelier', 'neebites'); ?></small>
                </div>
            </div>
            <div class="promise-item">
                <span class="promise-icon">🚚</span>
                <div class="promise-copy">
                    <strong><?php esc_html_e('Free Insulated Cold-Chain Shipping', 'neebites'); ?></strong>
                    <small><?php esc_html_e('Food-grade cold packs ensure zero melt in transit', 'neebites'); ?></small>
                </div>
            </div>
            <div class="promise-item">
                <span class="promise-icon">💵</span>
                <div class="promise-copy">
                    <strong><?php esc_html_e('Pay on Delivery available', 'neebites'); ?></strong>
                    <small><?php esc_html_e('Pay comfortably via cash, UPI or card on arrival', 'neebites'); ?></small>
                </div>
            </div>
        </div>

        <div class="myntra-brand-trust-list">
            <div class="trust-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#282c3f" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?php esc_html_e('100% Original Products & Pure Cocoa Butter (Zero Palm Oil)', 'neebites'); ?></span>
            </div>
            <div class="trust-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#282c3f" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?php esc_html_e('Insulated Temperature-Regulated Cold Pack Guarantee', 'neebites'); ?></span>
            </div>
            <div class="trust-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#282c3f" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?php esc_html_e('Easy 7 Days Free Replacement for Any Melt / Damage', 'neebites'); ?></span>
            </div>
        </div>
    </div>
    <?php
}

/**
 * 8. Myntra Best Offers & Coupon Card (Priority 34)
 */
function neebites_myntra_best_offers() {
    ?>
    <div class="myntra-best-offers-card">
        <div class="offers-header">
            <span class="offers-tag">🏷️</span>
            <span class="offers-heading"><?php esc_html_e('BEST OFFERS', 'neebites'); ?></span>
        </div>
        <div class="offer-body">
            <div class="offer-point">
                <div class="offer-dot"></div>
                <div class="offer-desc">
                    <div class="offer-best-price">
                        <strong><?php esc_html_e('Best Price:', 'neebites'); ?> <span class="highlight-price">₹359</span></strong>
                    </div>
                    <ul class="offer-terms">
                        <li><?php esc_html_e('Applicable on: Orders above ₹499 (Single or Combined)', 'neebites'); ?></li>
                        <li>
                            <?php esc_html_e('Coupon code:', 'neebites'); ?>
                            <span class="coupon-code-chip" id="myntra-coupon-code">SWEET10</span>
                            <button type="button" class="btn-copy-code" id="btn-copy-coupon"><?php esc_html_e('COPY', 'neebites'); ?></button>
                        </li>
                        <li><?php esc_html_e('Flat 10% instant discount on artisanal chocolates.', 'neebites'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * 9. Myntra Product Details & 2-Column Specifications Grid (Priority 36)
 */
function neebites_myntra_specifications() {
    global $product;
    if (!$product) return;

    $id = $product->get_id();

    // Check if store owner disabled specifications on this product
    $show = get_post_meta($id, '_neebites_show_specs', true);
    if ($show === 'no') {
        return;
    }

    $product_title = strtolower($product->get_name());

    // 1. Description: Custom details description -> Short Description -> Full Description
    $custom_desc = get_post_meta($id, '_neebites_product_details_desc', true);
    if (!empty($custom_desc)) {
        $desc = $custom_desc;
    } else {
        $desc = $product->get_short_description();
        if (empty($desc)) $desc = $product->get_description();
    }

    // Smart default determination based on product identity if not customized in admin
    $default_cocoa = esc_html__('Pure Cocoa Butter & Single-Origin Cocoa', 'neebites');
    $default_glaze = esc_html__('Signature Artisan Fruit Confiture & Pure Cocoa Butter', 'neebites');

    if (strpos($product_title, 'milk') !== false) {
        $default_cocoa = esc_html__('38% Swiss Alpine Milk Chocolate', 'neebites');
        $default_glaze = esc_html__('Velvet Milk Chocolate Glaze & Pure Cocoa Butter', 'neebites');
    } elseif (strpos($product_title, 'dark') !== false || strpos($product_title, 'noir') !== false) {
        $default_cocoa = esc_html__('70% Single-Origin Dark Ganache', 'neebites');
        $default_glaze = esc_html__('Bittersweet Noir Glaze & Dutch Cocoa Dust', 'neebites');
    } elseif (strpos($product_title, 'matcha') !== false) {
        $default_cocoa = esc_html__('Ceremonial Uji Japanese Matcha White Chocolate', 'neebites');
        $default_glaze = esc_html__('Artisan Matcha Green Tea Confiture Glaze', 'neebites');
    } elseif (strpos($product_title, 'caramel') !== false) {
        $default_cocoa = esc_html__('French Fleur De Sel Golden Caramel & Milk Chocolate', 'neebites');
        $default_glaze = esc_html__('Gourmet Salted Toffee Caramel Drizzle', 'neebites');
    } elseif (strpos($product_title, 'cinnamon') !== false || strpos($product_title, 'honey') !== false) {
        $default_cocoa = esc_html__('Spiced Ceylon Cinnamon & Milk Chocolate', 'neebites');
        $default_glaze = esc_html__('Wildflower Honey & Spiced Glaze', 'neebites');
    } elseif (strpos($product_title, 'ruby') !== false || strpos($product_title, 'raspberry') !== false) {
        $default_cocoa = esc_html__('Natural Ruby Cocoa & Freeze-Dried Berry Dust', 'neebites');
        $default_glaze = esc_html__('Lush Raspberry Confiture & Ruby Cocoa Butter', 'neebites');
    } elseif (strpos($product_title, 'white') !== false || strpos($product_title, 'vanilla') !== false) {
        $default_cocoa = esc_html__('Pure Bourbon Vanilla & Velvet White Chocolate', 'neebites');
        $default_glaze = esc_html__('Madagascar Vanilla Infused Glaze', 'neebites');
    } elseif (strpos($product_title, 'kiwi') !== false) {
        $default_cocoa = esc_html__('Velvet White Chocolate & Kiwi Glaze', 'neebites');
        $default_glaze = esc_html__('Signature Real Kiwi Fruit Confiture & Pure Cocoa Butter', 'neebites');
    }

    // 2. Cocoa Profile
    $cocoa = get_post_meta($id, '_chocolate_cocoa', true);
    if (empty($cocoa)) $cocoa = $product->get_attribute('cocoa');
    if (empty($cocoa)) $cocoa = $default_cocoa;

    // 3. Nut Roasting
    $nut = get_post_meta($id, '_chocolate_nut', true);
    if (empty($nut)) $nut = $product->get_attribute('nut-roast');
    if (empty($nut)) $nut = esc_html__('California Nonpareil Slow-Roasted Almonds', 'neebites');

    // 4. Glaze & Flavor (previously hardcoded to Kiwi!)
    $glaze = get_post_meta($id, '_chocolate_glaze', true);
    if (empty($glaze)) $glaze = $product->get_attribute('glaze');
    if (empty($glaze)) $glaze = $product->get_attribute('flavor');
    if (empty($glaze)) $glaze = $default_glaze;

    // 5. Dietary Integrity
    $dietary = get_post_meta($id, '_chocolate_dietary', true);
    if (empty($dietary)) $dietary = $product->get_attribute('dietary');
    if (empty($dietary)) $dietary = esc_html__('100% Vegetarian, Gluten-Free, Zero Palm Oil', 'neebites');

    // 6. Shelf Life & Storage
    $shelf = get_post_meta($id, '_chocolate_shelf', true);
    if (empty($shelf)) $shelf = $product->get_attribute('shelf-life');
    if (empty($shelf)) $shelf = esc_html__('9 Months (Store at 15°C - 18°C in cool dry place)', 'neebites');

    // 7. Packaging (previously hardcoded!)
    $packaging = get_post_meta($id, '_chocolate_packaging', true);
    if (empty($packaging)) $packaging = $product->get_attribute('packaging');
    if (empty($packaging)) $packaging = esc_html__('Airtight Food-Grade Tin with Insulated Thermal Wrap', 'neebites');

    // 8. Custom Additional Specifications (Dynamic Repeater)
    $custom_specs = get_post_meta($id, '_neebites_custom_specs', true);
    if (!is_array($custom_specs)) {
        $custom_specs = [];
    }
    ?>
    <div class="myntra-details-section">
        <h3 class="myntra-section-title"><?php esc_html_e('PRODUCT DETAILS', 'neebites'); ?></h3>
        <?php if (!empty($desc)) : ?>
        <div class="myntra-product-desc">
            <p><?php echo wp_kses_post($desc); ?></p>
        </div>
        <?php endif; ?>

        <h4 class="myntra-sub-title"><?php esc_html_e('Specifications', 'neebites'); ?></h4>
        <div class="myntra-specs-table">
            <div class="spec-cell">
                <div class="spec-label"><?php esc_html_e('Cocoa Profile', 'neebites'); ?></div>
                <div class="spec-val"><?php echo esc_html($cocoa); ?></div>
            </div>
            <div class="spec-cell">
                <div class="spec-label"><?php esc_html_e('Nut Roasting', 'neebites'); ?></div>
                <div class="spec-val"><?php echo esc_html($nut); ?></div>
            </div>
            <div class="spec-cell">
                <div class="spec-label"><?php esc_html_e('Glaze & Flavor', 'neebites'); ?></div>
                <div class="spec-val"><?php echo esc_html($glaze); ?></div>
            </div>
            <div class="spec-cell">
                <div class="spec-label"><?php esc_html_e('Dietary Integrity', 'neebites'); ?></div>
                <div class="spec-val"><?php echo esc_html($dietary); ?></div>
            </div>
            <div class="spec-cell">
                <div class="spec-label"><?php esc_html_e('Shelf Life & Storage', 'neebites'); ?></div>
                <div class="spec-val"><?php echo esc_html($shelf); ?></div>
            </div>
            <div class="spec-cell">
                <div class="spec-label"><?php esc_html_e('Packaging', 'neebites'); ?></div>
                <div class="spec-val"><?php echo esc_html($packaging); ?></div>
            </div>
            <?php foreach ($custom_specs as $cs) : 
                if (empty($cs['label']) || empty($cs['val'])) continue;
            ?>
            <div class="spec-cell">
                <div class="spec-label"><?php echo esc_html($cs['label']); ?></div>
                <div class="spec-val"><?php echo esc_html($cs['val']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * 10. Hook Registration for Single Product Summary
 */
// Remove default plain hooks to replace with polished Myntra components
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

// Attach Myntra UI components in exact Myntra order
add_action('woocommerce_single_product_summary', 'neebites_myntra_product_header', 4);
add_action('woocommerce_single_product_summary', 'neebites_myntra_product_price', 10);
add_action('woocommerce_single_product_summary', 'neebites_myntra_size_selector', 18);
add_action('woocommerce_single_product_summary', 'neebites_single_product_planter_pairing', 26);
// woocommerce_template_single_add_to_cart runs at priority 30
add_action('woocommerce_single_product_summary', 'neebites_myntra_delivery_pincode', 32);
add_action('woocommerce_single_product_summary', 'neebites_myntra_best_offers', 34);
add_action('woocommerce_single_product_summary', 'neebites_myntra_specifications', 36);

/**
 * 11. Sticky Bottom Add to Cart Bar on Single Product Page (Enhanced with ADD TO BAG)
 */
function neebites_sticky_add_to_cart_bar() {
    if (!is_product() || !neebites_get_option('sticky_add_to_cart', true)) {
        return;
    }

    global $product;
    if (!$product) return;

    $id = $product->get_id();
    $checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/');
    ?>
    <div class="neebites-sticky-add-to-cart" id="neebites-sticky-bar" aria-hidden="true">
        <div class="container sticky-bar-container">
            <div class="sticky-product-meta-block">
                <div class="sticky-product-thumb">
                    <?php
                    $sticky_thumb = has_post_thumbnail($id) ? get_the_post_thumbnail_url($id, 'thumbnail') : '';
                    if (!$sticky_thumb || strpos($sticky_thumb, '.svg') !== false || strpos($sticky_thumb, 'placeholder') !== false) {
                        $sticky_thumb = function_exists('neebites_get_product_fallback_image') 
                            ? neebites_get_product_fallback_image($product)
                            : get_template_directory_uri() . '/assets/images/products/choc-dark-almond.jpg';
                    }
                    echo '<img src="' . esc_url($sticky_thumb) . '" alt="' . esc_attr($product->get_name()) . '" width="48" height="48" style="object-fit:cover;border-radius:8px;" />';
                    ?>
                </div>
                <div class="sticky-product-info">
                    <span class="sticky-product-brand">NEEBITES</span>
                    <h4 class="sticky-product-title"><?php echo esc_html($product->get_title()); ?></h4>
                    <div class="sticky-product-pricing">
                        <?php
                        $sticky_price = (float)$product->get_price();
                        $sticky_reg_price = (float)$product->get_regular_price();
                        $sticky_target_id = $id;

                        if ($product->is_type('variable')) {
                            $available_variations = $product->get_available_variations();
                            if (!empty($available_variations)) {
                                foreach ($available_variations as $v) {
                                    if (!empty($v['is_in_stock'])) {
                                        $sticky_price = !empty($v['display_price']) ? (float)$v['display_price'] : $sticky_price;
                                        $sticky_reg_price = !empty($v['display_regular_price']) ? (float)$v['display_regular_price'] : $sticky_price;
                                        $sticky_target_id = $v['variation_id'];
                                        break;
                                    }
                                }
                            }
                        }

                        $sticky_pct = 0;
                        if ($sticky_reg_price > $sticky_price && $sticky_reg_price > 0) {
                            $sticky_pct = round((($sticky_reg_price - $sticky_price) / $sticky_reg_price) * 100);
                        }
                        ?>
                        <span class="sticky-price-current">₹<?php echo esc_html(number_format($sticky_price, 0)); ?></span>
                        <?php if ($sticky_pct > 0 || $sticky_reg_price > $sticky_price) : ?>
                            <del class="sticky-price-mrp">MRP ₹<?php echo esc_html(number_format($sticky_reg_price, 0)); ?></del>
                            <span class="sticky-price-off">(<?php echo esc_html($sticky_pct > 0 ? $sticky_pct : 20); ?>% OFF)</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="sticky-product-actions-block">
                <?php if ($product->is_in_stock()) : ?>
                    <div class="sticky-action-inner">
                        <div class="sticky-qty-stepper-wrap">
                            <div class="neebites-qty-stepper sticky-qty-stepper" role="group" aria-label="<?php esc_attr_e('Quantity controls', 'neebites'); ?>">
                                <button type="button" class="btn-qty-step btn-sticky-minus" aria-label="<?php esc_attr_e('Decrease quantity', 'neebites'); ?>">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                </button>
                                <input type="number" class="sticky-qty-input" name="sticky_quantity" value="1" min="1" max="99" step="1" inputmode="numeric" aria-label="<?php esc_attr_e('Quantity', 'neebites'); ?>" />
                                <button type="button" class="btn-qty-step btn-sticky-plus" aria-label="<?php esc_attr_e('Increase quantity', 'neebites'); ?>">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                </button>
                            </div>
                        </div>
                        <div class="sticky-buttons-group">
                            <button type="button" class="btn-sticky-add-bag btn-add-to-bag" data-product-id="<?php echo esc_attr($sticky_target_id); ?>" data-parent-id="<?php echo esc_attr($id); ?>" data-is-variable="<?php echo $product->is_type('variable') ? '1' : '0'; ?>">
                                <svg class="btn-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                </svg>
                                <span><?php echo esc_html__('ADD TO BAG', 'neebites'); ?></span>
                            </button>
                            <button type="button" class="btn-sticky-buy-now btn-buy-now" data-product-id="<?php echo esc_attr($sticky_target_id); ?>" data-parent-id="<?php echo esc_attr($id); ?>" data-is-variable="<?php echo $product->is_type('variable') ? '1' : '0'; ?>" data-checkout-url="<?php echo esc_url($checkout_url); ?>">
                                <svg class="btn-icon" width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                                </svg>
                                <span><?php echo esc_html__('BUY NOW', 'neebites'); ?></span>
                            </button>
                        </div>
                    </div>
                <?php else : ?>
                    <span class="badge-outofstock"><?php esc_html_e('Out of Stock', 'neebites'); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'neebites_sticky_add_to_cart_bar');

/**
 * Next-Level Luxury Pack Size Selection Options Modal
 * User directive: "bootm footer in not stedl any option buy now add bag click button option styles next level pop for options"
 */
function neebites_options_selection_modal() {
    global $product;
    if (!$product) return;
    
    $is_variable = $product->is_type('variable');
    $chips = neebites_get_product_pack_size_chips($product);
    if (empty($chips) || count($chips) <= 1) {
        if (!$is_variable) return;
    }

    $image_id = $product->get_image_id();
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : (function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('medium') : '');
    $title = $product->get_title();
    $first_chip = !empty($chips[0]) ? $chips[0] : null;
    $min_price = $first_chip ? $first_chip['price'] : (float)$product->get_price();
    $min_mrp = $first_chip ? $first_chip['mrp'] : (float)$product->get_regular_price();
    $min_off = $first_chip ? $first_chip['off'] : 0;
    ?>
    <div id="neebites-options-modal" class="neebites-options-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="options-modal-title">
        <div class="options-modal-backdrop"></div>
        <div class="options-modal-sheet">
            <div class="options-modal-drag-handle"></div>
            <button type="button" class="options-modal-close" aria-label="<?php esc_attr_e('Close options dialog', 'neebites'); ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>

            <div class="options-modal-product-summary">
                <div class="options-modal-thumb">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" />
                </div>
                <div class="options-modal-info">
                    <span class="options-modal-brand"><?php esc_html_e('NEEBITES ARTISAN', 'neebites'); ?></span>
                    <h3 class="options-modal-title"><?php echo esc_html($title); ?></h3>
                    <div class="options-modal-price-wrap">
                        <span class="options-modal-price">₹<?php echo esc_html(number_format($min_price, 0)); ?></span>
                        <?php if ($min_mrp > $min_price) : ?>
                            <del class="options-modal-mrp">MRP ₹<?php echo esc_html(number_format($min_mrp, 0)); ?></del>
                            <span class="options-modal-off">(<?php echo esc_html($min_off > 0 ? $min_off : 20); ?>% OFF)</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="options-modal-divider"></div>

            <div class="options-modal-body">
                <div class="options-modal-heading-row">
                    <span id="options-modal-title" class="options-modal-section-title"><?php esc_html_e('SELECT PACK SIZE', 'neebites'); ?></span>
                    <span class="options-modal-hint"><?php esc_html_e('Choose weight to continue', 'neebites'); ?></span>
                </div>

                <div class="options-modal-chips-grid">
                    <?php foreach ($chips as $c) : 
                        $is_out = !$c['in_stock'];
                        $chip_classes = 'options-modal-chip-card' . ($is_out ? ' out-of-stock' : '');
                    ?>
                        <div class="<?php echo esc_attr($chip_classes); ?>" 
                             data-variation-id="<?php echo esc_attr($c['variation_id']); ?>"
                             data-attribute-name="<?php echo esc_attr($c['attr_key']); ?>"
                             data-attribute-val="<?php echo esc_attr($c['attr_val']); ?>"
                             data-size="<?php echo esc_attr($c['display_size']); ?>"
                             data-packaging="<?php echo esc_attr($c['packaging']); ?>"
                             data-price="<?php echo esc_attr($c['price']); ?>"
                             data-mrp="<?php echo esc_attr($c['mrp']); ?>"
                             data-off="<?php echo esc_attr($c['off']); ?>"
                             data-in-stock="<?php echo $c['in_stock'] ? '1' : '0'; ?>"
                             tabindex="<?php echo $is_out ? '-1' : '0'; ?>"
                             role="button">
                            <div class="options-card-left">
                                <div class="options-card-radio">
                                    <svg class="radio-check-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>
                                <div class="options-card-details">
                                    <span class="options-card-weight"><?php echo esc_html($c['display_size']); ?></span>
                                    <span class="options-card-sub"><?php echo esc_html($c['packaging']); ?></span>
                                </div>
                            </div>
                            <div class="options-card-pricing">
                                <span class="options-card-price">₹<?php echo esc_html(number_format($c['price'], 0)); ?></span>
                                <?php if ($c['mrp'] > $c['price']) : ?>
                                    <del class="options-card-mrp">₹<?php echo esc_html(number_format($c['mrp'], 0)); ?></del>
                                    <span class="options-card-off"><?php echo esc_html($c['off']); ?>% OFF</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="options-modal-urgency">
                    <span class="fire-icon">🔥</span>
                    <span><?php esc_html_e('Selling fast! Limited fresh roast confectionery batches.', 'neebites'); ?></span>
                </div>
            </div>

            <div class="options-modal-footer">
                <button type="button" class="options-modal-submit-btn btn-modal-confirm-action" data-action="add_to_bag">
                    <span class="btn-modal-icon">
                        <svg class="icon-bag" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                        <svg class="icon-bolt" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    </span>
                    <span class="btn-modal-text"><?php esc_html_e('SELECT PACK SIZE', 'neebites'); ?></span>
                </button>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'neebites_options_selection_modal', 25);
