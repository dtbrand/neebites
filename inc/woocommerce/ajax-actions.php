<?php
/**
 * WooCommerce AJAX Endpoints (Live Search, Quick View, Wishlist, Mini-Cart Quantity)
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Live Instant Product Search AJAX Handler
 */
function neebites_ajax_search_products() {
    // Public storefront search - safe read-only query
    if (!empty($_REQUEST['nonce'])) {
        wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), 'neebites_nonce');
    }

    $keyword = isset($_GET['term']) ? sanitize_text_field(wp_unslash($_GET['term'])) : '';
    if (empty($keyword) || mb_strlen($keyword) < 2) {
        wp_send_json_success(['results' => [], 'total' => 0]);
    }

    $results = [];
    $seen_titles = [];

    // 1. Query WooCommerce real published products
    if (function_exists('wc_get_product')) {
        $query_args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 6,
            's'              => $keyword,
        ];
        $query = new WP_Query($query_args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());
                if (!$product) continue;

                $title = html_entity_decode(get_the_title(), ENT_QUOTES, 'UTF-8');
                $seen_titles[strtolower($title)] = true;

                $thumb = $product->get_image('thumbnail');
                if (empty($thumb) || strpos($thumb, 'placeholder') !== false) {
                    $thumb = '<img src="' . esc_url(get_template_directory_uri() . '/assets/images/choc-dark-almond.svg') . '" width="54" height="54" style="object-fit:contain;background:#FAF6F0;border-radius:10px;padding:4px" alt="' . esc_attr($title) . '" />';
                }

                $cat_raw = function_exists('wc_get_product_category_list') ? strip_tags(wc_get_product_category_list($product->get_id(), ', ')) : 'Confectionery';
                $cat_clean = html_entity_decode(html_entity_decode($cat_raw, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                $cat_clean = str_replace('&amp;', '&', $cat_clean);

                $results[] = [
                    'id'        => $product->get_id(),
                    'title'     => $title,
                    'url'       => get_permalink(),
                    'thumb'     => $thumb,
                    'price'     => $product->get_price_html(),
                    'badge'     => $product->is_on_sale() ? 'Sale' : 'In Stock',
                    'category'  => $cat_clean,
                    'in_stock'  => $product->is_in_stock(),
                ];
            }
            wp_reset_postdata();
        }
    }

    // 2. Query botanical catalog demo products as fallback / supplement
    if (count($results) < 6 && function_exists('neebites_get_demo_products')) {
        $demo_products = neebites_get_demo_products();
        $kw_lower = strtolower($keyword);

        foreach ($demo_products as $demo_id => $dp) {
            if (count($results) >= 6) break;

            $name_lower = strtolower($dp['name']);
            $cat_lower  = strtolower($dp['category'] ?? '');
            $desc_lower = strtolower($dp['desc'] ?? '');

            if (strpos($name_lower, $kw_lower) !== false || 
                strpos($cat_lower, $kw_lower) !== false || 
                strpos($desc_lower, $kw_lower) !== false) {

                if (!empty($seen_titles[$name_lower])) continue;
                $seen_titles[$name_lower] = true;

                $product_url = home_url('/shop');
                $slug = sanitize_title($dp['name']);
                $existing_post = get_page_by_path($slug, OBJECT, 'product');
                if ($existing_post) {
                    $product_url = get_permalink($existing_post->ID);
                } else {
                    $product_url = add_query_arg(['s' => $dp['name'], 'post_type' => 'product'], home_url('/'));
                }

                $thumb_html = '<img src="' . esc_url($dp['image']) . '" width="54" height="54" style="object-fit:cover;border-radius:10px" alt="' . esc_attr($dp['name']) . '" />';

                $results[] = [
                    'id'        => $dp['id'],
                    'title'     => html_entity_decode($dp['name'], ENT_QUOTES, 'UTF-8'),
                    'url'       => $product_url,
                    'thumb'     => $thumb_html,
                    'price'     => '<span class="price-val">' . esc_html($dp['price']) . '</span>',
                    'badge'     => $dp['badge'] ?? 'Artisan Small Batch',
                    'category'  => html_entity_decode($dp['category'] ?? 'Chocolate Coated Almonds', ENT_QUOTES, 'UTF-8'),
                    'in_stock'  => true,
                ];
            }
        }
    }

    wp_send_json_success([
        'results'  => $results,
        'total'    => count($results),
        'view_all' => add_query_arg(['s' => $keyword, 'post_type' => 'product'], home_url('/')),
    ]);
}
add_action('wp_ajax_neebites_search_products', 'neebites_ajax_search_products');
add_action('wp_ajax_nopriv_neebites_search_products', 'neebites_ajax_search_products');

/**
 * Quick View Modal AJAX Handler
 */
function neebites_ajax_quick_view() {
    check_ajax_referer('neebites_nonce', 'nonce');

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid product ID']);
    }

    $product = function_exists('wc_get_product') ? wc_get_product($product_id) : false;
    if (!$product) {
        $all_demos = function_exists('neebites_get_demo_products') ? neebites_get_demo_products() : [];
        if (isset($all_demos[$product_id])) {
            $p = $all_demos[$product_id];
            $demo = [
                'name'     => $p['name'],
                'price'    => !empty($p['regular_price']) ? '<del style="color:#888;font-size:16px;font-weight:normal;margin-right:6px">' . esc_html($p['regular_price']) . '</del> ' . esc_html($p['price']) : esc_html($p['price']),
                'raw_price'=> $p['raw_price'],
                'desc'     => $p['desc'],
                'image'    => $p['image'],
                'light'    => $p['light'],
                'water'    => $p['water'],
                'humidity' => $p['difficulty'],
                'badge'    => $p['badge'],
            ];
        } else {
            $demo = [
                'name'     => 'Dark Chocolate Almond (70% Single-Origin Belgian)',
                'price'    => '₹349.00',
                'raw_price'=> 349.00,
                'desc'     => 'Whole slow-roasted California almonds drenched in silky 70% dark Belgian chocolate with deep cocoa intensity and balanced bittersweet finish.',
                'image'    => get_template_directory_uri() . '/assets/images/choc-dark-almond.svg',
                'light'    => '70% Belgian Single-Origin Dark',
                'water'    => 'California Nonpareil Roasted',
                'humidity' => '100% Vegetarian & Pure Cocoa Butter',
                'badge'    => 'Best Seller',
            ];
        }

        ob_start();
        ?>
        <div class="quickview-container product" id="product-demo-<?php echo esc_attr($product_id); ?>" style="display:grid;grid-template-columns:1fr 1fr;gap:36px;align-items:center">
            <div class="quickview-gallery">
                <div class="quickview-main-image" style="background:#FAF6F0;border-radius:12px;padding:20px;text-align:center">
                    <img src="<?php echo esc_url($demo['image']); ?>" alt="<?php echo esc_attr($demo['name']); ?>" style="max-height:360px;width:auto;margin:0 auto;display:block" />
                </div>
            </div>
            <div class="quickview-summary summary entry-summary">
                <span style="display:inline-block;padding:3px 12px;border-radius:20px;background:#F5EBE1;color:#3D2314;font-size:12px;font-weight:700;margin-bottom:8px"><?php echo esc_html($demo['badge']); ?></span>
                <h2 class="product_title entry-title" style="font-size:24px;color:#3D2314;margin:0 0 10px"><?php echo esc_html($demo['name']); ?></h2>
                <div class="quickview-rating-price" style="margin-bottom:14px">
                    <span class="star-rating" style="color:#C59B27;font-size:14px">★★★★★</span>
                    <span style="font-size:13px;color:#6B4226;margin-left:6px">(Artisan Chocolatier Batch)</span>
                    <p class="price" style="font-size:22px;font-weight:700;color:#3D2314;margin:8px 0"><?php echo wp_kses_post($demo['price']); ?></p>
                </div>
                <div class="woocommerce-product-details__short-description" style="color:#5C3A21;font-size:14px;line-height:1.6;margin-bottom:16px">
                    <p><?php echo esc_html($demo['desc']); ?></p>
                </div>
                <div style="background:#FAF6F0;border:1px solid #EAE0D5;border-radius:10px;padding:12px 16px;margin-bottom:20px">
                    <div style="font-size:12px;color:#6B4226;font-weight:700;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px">🍫 Confectionery Profile:</div>
                    <div style="font-size:13px;color:#444;line-height:1.6">
                        <div>🍫 <strong>Cocoa:</strong> <?php echo esc_html($demo['light']); ?></div>
                        <div>🌰 <strong>Nut:</strong> <?php echo esc_html($demo['water']); ?></div>
                        <div>✨ <strong>Purity:</strong> <?php echo esc_html($demo['humidity']); ?></div>
                    </div>
                </div>
                <button type="button" class="btn btn-primary demo-add-to-basket" 
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-name="<?php echo esc_attr($demo['name']); ?>" 
                    data-price="<?php echo esc_attr(strip_tags($demo['price'])); ?>" 
                    data-raw-price="<?php echo esc_attr(isset($demo['raw_price']) ? $demo['raw_price'] : '349.00'); ?>"
                    data-image="<?php echo esc_url($demo['image']); ?>"
                    style="width:100%;padding:12px 24px;border-radius:30px;background:#3D2314;color:#FAF6F0;border:none;font-weight:700;cursor:pointer">
                    Add to Chocolate Basket 🍫
                </button>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
        wp_send_json_success(['html' => $html]);
    }

    // Set global post/product for standard template functions
    global $post;
    $post = get_post($product_id);
    setup_postdata($post);

    ob_start();
    ?>
    <div class="quickview-container product" id="product-<?php echo esc_attr($product_id); ?>">
        <div class="quickview-gallery">
            <div class="quickview-main-image">
                <?php echo $product->get_image('large'); ?>
            </div>
            <?php
            $attachment_ids = $product->get_gallery_image_ids();
            if (!empty($attachment_ids)) : ?>
                <div class="quickview-thumbnails">
                    <?php
                    // Main image thumb
                    echo '<div class="thumb-item active" data-image="' . esc_url(wp_get_attachment_image_url($product->get_image_id(), 'large')) . '">' . $product->get_image('thumbnail') . '</div>';
                    foreach ($attachment_ids as $att_id) {
                        echo '<div class="thumb-item" data-image="' . esc_url(wp_get_attachment_image_url($att_id, 'large')) . '">' . wp_get_attachment_image($att_id, 'thumbnail') . '</div>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="quickview-summary summary entry-summary">
            <h2 class="product_title entry-title"><?php echo esc_html($product->get_name()); ?></h2>

            <div class="quickview-rating-price">
                <?php
                if ($product->get_rating_count() > 0) {
                    echo wc_get_rating_html($product->get_average_rating(), $product->get_rating_count());
                }
                ?>
                <p class="price"><?php echo $product->get_price_html(); ?></p>
            </div>

            <div class="woocommerce-product-details__short-description">
                <?php echo wpautop(wp_kses_post($product->get_short_description())); ?>
            </div>

            <?php if ($product->is_in_stock()) : ?>
                <?php woocommerce_template_single_add_to_cart(); ?>
            <?php else : ?>
                <p class="stock out-of-stock"><?php esc_html_e('Out of stock', 'neebites'); ?></p>
            <?php endif; ?>

            <div class="quickview-view-full">
                <a href="<?php echo esc_url($product->get_permalink()); ?>" class="view-full-details-link">
                    <?php esc_html_e('View Complete Product Details & Tasting Notes &rarr;', 'neebites'); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_neebites_quick_view', 'neebites_ajax_quick_view');
add_action('wp_ajax_nopriv_neebites_quick_view', 'neebites_ajax_quick_view');

/**
 * Mini-Cart Quantity Update AJAX Handler
 */
function neebites_ajax_update_cart_quantity() {
    check_ajax_referer('neebites_nonce', 'nonce');

    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';
    $quantity      = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;

    if (!$cart_item_key || !function_exists('WC') || !WC()->cart) {
        wp_send_json_error(['message' => 'Invalid request']);
    }

    try {
        $cart = WC()->cart->get_cart();
        if (!isset($cart[$cart_item_key])) {
            wp_send_json_error(['message' => 'Cart item not found']);
        }

        if ($quantity > 0) {
            WC()->cart->set_quantity($cart_item_key, $quantity, true);
        } else {
            WC()->cart->remove_cart_item($cart_item_key);
        }

        WC()->cart->calculate_totals();

        $mini_cart_html = '';
        if (function_exists('neebites_render_mini_cart_content')) {
            ob_start();
            neebites_render_mini_cart_content();
            $mini_cart_html = ob_get_clean();
        }

        wp_send_json_success([
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'subtotal'   => WC()->cart->get_cart_subtotal(),
            'html'       => $mini_cart_html,
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}
add_action('wp_ajax_neebites_update_cart_quantity', 'neebites_ajax_update_cart_quantity');
add_action('wp_ajax_nopriv_neebites_update_cart_quantity', 'neebites_ajax_update_cart_quantity');

/**
 * Toggle Wishlist AJAX Handler
 */
function neebites_ajax_toggle_wishlist() {
    check_ajax_referer('neebites_nonce', 'nonce');

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid product']);
    }

    $wishlist = [];
    if (isset($_COOKIE['neebites_wishlist'])) {
        $decoded = json_decode(stripslashes($_COOKIE['neebites_wishlist']), true);
        if (is_array($decoded)) {
            $wishlist = $decoded;
        }
    }

    $added = false;
    if (in_array($product_id, $wishlist)) {
        // Remove from wishlist
        $wishlist = array_diff($wishlist, [$product_id]);
        $added = false;
    } else {
        // Add to wishlist
        $wishlist[] = $product_id;
        $added = true;
    }

    $wishlist = array_values(array_unique($wishlist));
    setcookie('neebites_wishlist', json_encode($wishlist), time() + (86400 * 30), '/');

    wp_send_json_success([
        'added' => $added,
        'count' => count($wishlist),
    ]);
}
add_action('wp_ajax_neebites_toggle_wishlist', 'neebites_ajax_toggle_wishlist');
add_action('wp_ajax_nopriv_neebites_toggle_wishlist', 'neebites_ajax_toggle_wishlist');

/**
 * Get Wishlist Drawer HTML AJAX Handler
 */
function neebites_ajax_get_wishlist_drawer() {
    ob_start();
    if (function_exists('neebites_render_wishlist_drawer_content')) {
        neebites_render_wishlist_drawer_content();
    }
    $html = ob_get_clean();
    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_neebites_get_wishlist_drawer', 'neebites_ajax_get_wishlist_drawer');
add_action('wp_ajax_nopriv_neebites_get_wishlist_drawer', 'neebites_ajax_get_wishlist_drawer');

/**
 * Get Mini Cart Drawer HTML AJAX Handler
 */
function neebites_ajax_get_mini_cart() {
    ob_start();
    if (function_exists('neebites_render_mini_cart_content')) {
        neebites_render_mini_cart_content();
    }
    $html = ob_get_clean();
    $count = 0;
    if (function_exists('WC') && WC()->cart) {
        $count = WC()->cart->get_cart_contents_count();
    }
    wp_send_json_success([
        'html'       => $html,
        'cart_count' => $count,
    ]);
}
add_action('wp_ajax_neebites_get_mini_cart', 'neebites_ajax_get_mini_cart');
add_action('wp_ajax_nopriv_neebites_get_mini_cart', 'neebites_ajax_get_mini_cart');

/**
 * Render Quick View Modal Container Markup into Footer
 */
function neebites_render_quick_view_modal() {
    ?>
    <div id="neebites-quickview-modal" class="neebites-modal" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Quick View', 'neebites'); ?>" hidden>
        <div class="neebites-modal-backdrop"></div>
        <div class="neebites-modal-dialog">
            <button type="button" class="neebites-modal-close" aria-label="<?php esc_attr_e('Close Dialog', 'neebites'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <div class="neebites-modal-content" id="neebites-quickview-content">
                <div class="neebites-loading-spinner" aria-hidden="true"></div>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'neebites_render_quick_view_modal', 60);
