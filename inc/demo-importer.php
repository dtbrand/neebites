<?php
/**
 * 1-Click Demo Data Importer & Setup Wizard - Artisan Chocolates & Confectionery
 *
 * @package Neebites
 * @version 2.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Register Admin Menu for 1-Click Demo Setup
 */
function neebites_demo_importer_menu() {
    add_theme_page(
        esc_html__('Neebites Demo Setup', 'neebites'),
        esc_html__('Neebites Demo Setup', 'neebites'),
        'manage_options',
        'neebites-demo-setup',
        'neebites_render_demo_importer_page'
    );
}
add_action('admin_menu', 'neebites_demo_importer_menu');

/**
 * Render Demo Importer Admin Page
 */
function neebites_render_demo_importer_page() {
    $imported = false;
    $message  = '';

    if (isset($_POST['neebites_run_demo_import']) && check_admin_referer('neebites_demo_action', 'neebites_demo_nonce')) {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'neebites'));
        }
        $result = neebites_execute_demo_import();
        $imported = $result['success'];
        $message  = $result['message'];
    }
    ?>
    <div class="wrap neebites-demo-wrap" style="max-width:880px;margin-top:30px">
        <div style="background:#ffffff;border-radius:12px;padding:36px;box-shadow:0 4px 20px rgba(0,0,0,0.06);border:1px solid #EAE0D5">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px">
                <span style="font-size:40px;background:#FAF6F0;width:64px;height:64px;display:flex;align-items:center;justify-content:center;border-radius:50%;border:1px solid #EAE0D5">🍫</span>
                <div>
                    <h1 style="font-size:26px;margin:0;color:#3D2314;font-weight:700"><?php esc_html_e('Neebites 1-Click Chocolatier Setup', 'neebites'); ?></h1>
                    <p style="margin:4px 0 0;color:#5C3D2E;font-size:15px"><?php esc_html_e('Transform your store into a luxury Artisan Chocolatier & Gourmet Confectionery shop in seconds.', 'neebites'); ?></p>
                </div>
            </div>

            <?php if ($imported) : ?>
                <div style="background:#FAF6F0;border:1px solid #C59B27;color:#3D2314;padding:16px 20px;border-radius:8px;margin-bottom:24px">
                    <h3 style="margin:0 0 6px"><?php esc_html_e('🎉 Demo Confectionery Catalog Successfully Imported!', 'neebites'); ?></h3>
                    <p style="margin:0 0 12px"><?php echo esc_html($message); ?></p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="button button-primary" target="_blank" style="background:#3D2314;border-color:#3D2314;padding:6px 18px;height:auto;font-size:14px"><?php esc_html_e('View Live Website &rarr;', 'neebites'); ?></a>
                </div>
            <?php endif; ?>

            <div style="background:#FAF6F0;padding:24px;border-radius:8px;border:1px solid #EAE0D5;margin-bottom:28px">
                <h3 style="margin:0 0 14px;color:#3D2314;font-size:17px"><?php esc_html_e('What this 1-Click Setup will configure for you:', 'neebites'); ?></h3>
                <ul style="list-style:disc;margin:0 0 0 20px;color:#444;line-height:1.8;font-size:14px">
                    <li><strong><?php esc_html_e('Complete Chocolatier Homepage:', 'neebites'); ?></strong> <?php esc_html_e('Artisan hero banner, 5 flavour variant category bubbles, craft wisdom bar, and 10% off voucher banner.', 'neebites'); ?></li>
                    <li><strong><?php esc_html_e('Standard Pages:', 'neebites'); ?></strong> <?php esc_html_e('Home, Shop, About Us, Contact, Cart, Checkout, My Account.', 'neebites'); ?></li>
                    <li><strong><?php esc_html_e('Category Structure Hierarchy:', 'neebites'); ?></strong> <?php esc_html_e('Main Category: 🍫 Chocolates & Confectionery > Sub Category: 🥜 Chocolate Coated Nuts > Product Type: Chocolate Coated Almonds > 5 Flavour Variants.', 'neebites'); ?></li>
                    <?php if (class_exists('WooCommerce')) : ?>
                        <li><strong><?php esc_html_e('8 Demo Chocolate Almond Products:', 'neebites'); ?></strong> <?php esc_html_e('Dark Chocolate Almond, Kiwi Chocolate Almond, Milk Chocolate Almond, White Chocolate Almond, Matcha, Caramel, Cinnamon, and Ruby with nutrition notes, SKU, and prices.', 'neebites'); ?></li>
                    <?php else : ?>
                        <li style="color:#d9534f"><strong><?php esc_html_e('Note on WooCommerce:', 'neebites'); ?></strong> <?php esc_html_e('WooCommerce is currently not active. Pages will be created. Install & activate WooCommerce to automatically import demo chocolate products too.', 'neebites'); ?></li>
                    <?php endif; ?>
                    <li><strong><?php esc_html_e('Pre-Configured Theme Options:', 'neebites'); ?></strong> <?php esc_html_e('Artisan cocoa brown palette, free cold-pack shipping threshold (₹500), and top announcement ticker.', 'neebites'); ?></li>
                </ul>
            </div>

            <form method="post" action="">
                <?php wp_nonce_field('neebites_demo_action', 'neebites_demo_nonce'); ?>
                <button type="submit" name="neebites_run_demo_import" class="button button-primary button-hero" style="background:#3D2314;border-color:#3D2314;color:#ffffff;font-weight:700;padding:12px 32px;height:auto;font-size:16px;border-radius:30px;cursor:pointer">
                    <?php esc_html_e('🍫 Import Complete Chocolatier Demo Content (1-Click)', 'neebites'); ?>
                </button>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Execute the 1-Click Demo Import Process
 */
function neebites_execute_demo_import() {
    // 1. Create Homepage with block pattern content
    $home_content = '<!-- wp:pattern {"slug":"neebites/hero-banner"} /-->
<!-- wp:pattern {"slug":"neebites/benefits-bar"} /-->
<!-- wp:pattern {"slug":"neebites/category-circles"} /-->
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"40px","bottom":"40px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:40px;padding-bottom:40px">
    <div style="text-align:center;max-width:650px;margin:0 auto 36px">
        <span style="color:#C59B27;font-weight:700;font-size:13px;letter-spacing:1.5px;text-transform:uppercase">FRESH FROM THE ROASTERY</span>
        <h2 style="font-size:2.2rem;color:#3D2314;margin:6px 0 10px">Artisan Chocolate Almond Drops</h2>
        <p style="color:#5C3D2E;font-size:1.05rem">Handcrafted slow-roasted California almonds enrobed in silky single-origin Belgian chocolate</p>
    </div>
    [products limit="8" columns="4" orderby="popularity"]
</div>
<!-- /wp:group -->
<!-- wp:pattern {"slug":"neebites/plant-care-guide"} /-->
<!-- wp:pattern {"slug":"neebites/customer-reviews"} /-->
<!-- wp:pattern {"slug":"neebites/green-club"} /-->
<!-- wp:pattern {"slug":"neebites/instagram-community"} /-->';

    $home_id = neebites_create_or_get_page('Home', $home_content);

    // 2. Create About Page
    $about_content = '<!-- wp:heading {"level":2} --><h2>Our Chocolatier Heritage</h2><!-- /wp:heading --><!-- wp:paragraph --><p>At Neebites, we craft confectionery masterpieces using pure cocoa butter, single-origin Belgian chocolate, and hand-selected California Nonpareil almonds. Never palm oil. Never compound fats. Just timeless artisan perfection.</p><!-- /wp:paragraph --><!-- wp:pattern {"slug":"neebites/benefits-bar"} /-->';
    $about_id = neebites_create_or_get_page('About Us', $about_content);

    // 3. Create Contact Page
    $contact_content = '<!-- wp:heading {"level":2} --><h2>Get in Touch with Our Master Chocolatiers</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Have a question about our roastery batches, custom corporate gifting, or cold-chain delivery? Our team is at your service 7 days a week.</p><!-- /wp:paragraph --><p><strong>Email:</strong> care@neebites.com<br><strong>WhatsApp Roastery Support:</strong> +91 98765 43210<br><strong>Roastery Hours:</strong> Mon - Sat: 9:00 AM - 7:00 PM</p>';
    $contact_id = neebites_create_or_get_page('Contact', $contact_content);

    // 4. Configure Reading Settings: Set Front Page to Home
    update_option('show_on_front', 'page');
    update_option('page_on_front', $home_id);

    // 5. If WooCommerce is active, create sample products & categories
    $product_count = 0;
    if (class_exists('WooCommerce')) {
        $product_count = neebites_import_demo_products();
    }

    // 6. Setup Primary Navigation Menu
    neebites_setup_chocolate_navigation_menu();

    // 7. Set Default Theme Options
    $default_options = [
        'shipping_bar_enabled'    => true,
        'free_shipping_threshold' => 500,
        'top_bar_text'            => '🍫 Free Insulated Cold-Pack Delivery on orders over ₹500 | 100% Pure Cocoa Butter & Zero Palm Oil | Handcrafted California Roasted Almonds',
        'header_style'            => 'style-1',
        'primary_color'           => '#3D2314',
        'primary_color_light'     => '#6B4226',
        'accent_color'            => '#C59B27',
        'sale_color'              => '#C0392B',
        'bg_color_light'          => '#FAF6F0',
        'text_color'              => '#241408',
        'shop_columns'            => 4,
        'shop_sidebar'            => 'left',
        'sticky_add_to_cart'      => true,
        'footer_copyright'        => '&copy; ' . date('Y') . ' Neebites Artisan Chocolatier & Confectionery. All rights reserved.',
    ];
    update_option('neebites_theme_options', $default_options);

    return [
        'success' => true,
        'message' => sprintf(
            esc_html__('Homepage, about, contact pages and hierarchy navigation configured. %d artisan chocolate products created.', 'neebites'),
            $product_count
        ),
    ];
}

/**
 * Helper to create page if not exists
 */
function neebites_create_or_get_page($title, $content) {
    $existing = get_page_by_title($title);
    if ($existing) {
        return $existing->ID;
    }

    return wp_insert_post([
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_type'    => 'page',
    ]);
}

/**
 * Helper to get or create a taxonomy term with specified parent
 */
function neebites_get_or_create_term($name, $slug, $taxonomy, $parent_id = 0) {
    $term = get_term_by('slug', $slug, $taxonomy);
    if ($term) {
        $update_args = [];
        if ($parent_id > 0 && $term->parent != $parent_id) {
            $update_args['parent'] = $parent_id;
        }
        if ($term->name !== $name) {
            $update_args['name'] = $name;
        }
        if (!empty($update_args)) {
            wp_update_term($term->term_id, $taxonomy, $update_args);
        }
        return (int) $term->term_id;
    }
    $created = wp_insert_term($name, $taxonomy, [
        'slug'   => $slug,
        'parent' => $parent_id,
    ]);
    if (!is_wp_error($created) && isset($created['term_id'])) {
        return (int) $created['term_id'];
    }
    return 0;
}

/**
 * Setup Primary Navigation Menu following User's Exact Category Structure
 */
function neebites_setup_chocolate_navigation_menu() {
    $menu_name = 'Neebites Confectionery Navigation';
    $menu_obj = wp_get_nav_menu_object($menu_name);
    if ($menu_obj) {
        wp_delete_nav_menu($menu_obj->term_id);
    }
    $old_menu = wp_get_nav_menu_object('Neebites Primary Navigation');
    if ($old_menu) {
        wp_delete_nav_menu($old_menu->term_id);
    }

    $menu_id = wp_create_nav_menu($menu_name);
    if (is_wp_error($menu_id) || !$menu_id) {
        return;
    }

    $shop_url = function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0 ? get_permalink(wc_get_page_id('shop')) : home_url('/shop');

    // 1. Home
    wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title'   => __('Home', 'neebites'),
        'menu-item-url'     => home_url('/'),
        'menu-item-status'  => 'publish',
        'menu-item-type'    => 'custom',
    ]);

    // 2. Main Category: 🍫 Chocolates & Confectionery
    $main_cat = get_term_by('slug', 'chocolates-confectionery', 'product_cat');
    $main_cat_url = ($main_cat && !is_wp_error($main_cat)) ? get_term_link($main_cat) : home_url('/product-category/chocolates-confectionery/');
    $main_item_id = wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title'   => __('🍫 Chocolates & Confectionery', 'neebites'),
        'menu-item-url'     => $main_cat_url,
        'menu-item-status'  => 'publish',
        'menu-item-type'    => 'custom',
    ]);

    // 2.1 Sub Category: 🥜 Chocolate Coated Nuts (Child of Main)
    $sub_cat = get_term_by('slug', 'chocolate-coated-nuts', 'product_cat');
    $sub_cat_url = ($sub_cat && !is_wp_error($sub_cat)) ? get_term_link($sub_cat) : home_url('/product-category/chocolate-coated-nuts/');
    $sub_item_id = wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title'       => __('🥜 Chocolate Coated Nuts', 'neebites'),
        'menu-item-url'         => $sub_cat_url,
        'menu-item-status'      => 'publish',
        'menu-item-parent-id'   => $main_item_id,
        'menu-item-type'        => 'custom',
    ]);

    // 2.1.1 Product Type: Chocolate Coated Almonds (Child of Sub)
    $type_cat = get_term_by('slug', 'chocolate-coated-almonds', 'product_cat');
    $type_cat_url = ($type_cat && !is_wp_error($type_cat)) ? get_term_link($type_cat) : home_url('/product-category/chocolate-coated-almonds/');
    $type_item_id = wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title'       => __('Chocolate Coated Almonds', 'neebites'),
        'menu-item-url'         => $type_cat_url,
        'menu-item-status'      => 'publish',
        'menu-item-parent-id'   => $sub_item_id,
        'menu-item-type'        => 'custom',
    ]);

    // 2.1.1.x Flavour Variants (Children of Chocolate Coated Almonds)
    $flavours = [
        'Dark Chocolate Almond'   => 'dark-chocolate-almond',
        'Kiwi Chocolate Almond'   => 'kiwi-chocolate-almond',
        'Milk Chocolate Almond'   => 'milk-chocolate-almond',
        'White Chocolate Almond'  => 'white-chocolate-almond',
        'Other Flavoured Almonds' => 'other-flavoured-almonds',
    ];
    foreach ($flavours as $flavour_name => $flavour_slug) {
        $flavour_term = get_term_by('slug', $flavour_slug, 'product_cat');
        $flavour_url = ($flavour_term && !is_wp_error($flavour_term)) ? get_term_link($flavour_term) : add_query_arg('product_cat', $flavour_slug, $shop_url);
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title'       => $flavour_name,
            'menu-item-url'         => $flavour_url,
            'menu-item-status'      => 'publish',
            'menu-item-parent-id'   => $type_item_id,
            'menu-item-type'        => 'custom',
        ]);
    }

    // 3. Shop Page
    $shop_id = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
    if ($shop_id > 0) {
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title'     => __('Shop', 'neebites'),
            'menu-item-object-id' => $shop_id,
            'menu-item-object'    => 'page',
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
        ]);
    }

    // 4. About Us Page
    $about_page = get_page_by_path('about-us') ?: get_page_by_title('About Us');
    if ($about_page) {
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title'     => __('About Us', 'neebites'),
            'menu-item-object-id' => $about_page->ID,
            'menu-item-object'    => 'page',
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
        ]);
    }

    // 5. Contact Page
    $contact_page = get_page_by_path('contact') ?: get_page_by_title('Contact');
    if ($contact_page) {
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title'     => __('Contact', 'neebites'),
            'menu-item-object-id' => $contact_page->ID,
            'menu-item-object'    => 'page',
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
        ]);
    }

    // Assign to theme locations
    $locations = get_theme_mod('nav_menu_locations', []);
    $locations['primary'] = $menu_id;
    $locations['footer']  = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
}

/**
 * Helper to import realistic confectionery products into WooCommerce
 */
function neebites_import_demo_products() {
    if (!class_exists('WC_Product_Simple')) return 0;

    // Build the User's Website Category Structure Hierarchy:
    // Main Category: 🍫 Chocolates & Confectionery
    $main_cat_id = neebites_get_or_create_term('🍫 Chocolates & Confectionery', 'chocolates-confectionery', 'product_cat', 0);

    // Sub Category: 🥜 Chocolate Coated Nuts
    $sub_cat_id = neebites_get_or_create_term('🥜 Chocolate Coated Nuts', 'chocolate-coated-nuts', 'product_cat', $main_cat_id);

    // Product Type: Chocolate Coated Almonds
    $type_cat_id = neebites_get_or_create_term('Chocolate Coated Almonds', 'chocolate-coated-almonds', 'product_cat', $sub_cat_id);

    // Flavour Variants (Children of Chocolate Coated Almonds)
    $flavour_cats = [
        'Dark Chocolate Almond'   => neebites_get_or_create_term('Dark Chocolate Almond', 'dark-chocolate-almond', 'product_cat', $type_cat_id),
        'Kiwi Chocolate Almond'   => neebites_get_or_create_term('Kiwi Chocolate Almond', 'kiwi-chocolate-almond', 'product_cat', $type_cat_id),
        'Milk Chocolate Almond'   => neebites_get_or_create_term('Milk Chocolate Almond', 'milk-chocolate-almond', 'product_cat', $type_cat_id),
        'White Chocolate Almond'  => neebites_get_or_create_term('White Chocolate Almond', 'white-chocolate-almond', 'product_cat', $type_cat_id),
        'Other Flavoured Almonds' => neebites_get_or_create_term('Other Flavoured Almonds', 'other-flavoured-almonds', 'product_cat', $type_cat_id),
    ];

    $demo_products = [
        [
            'name'        => 'Dark Chocolate Almond (70% Single-Origin Belgian)',
            'sku'         => 'CHOC-ALM-DARK-01',
            'price'       => '449.00',
            'sale_price'  => '349.00',
            'flavour'     => 'Dark Chocolate Almond',
            'tag'         => 'Best Seller',
            'desc'        => 'Slow-roasted premium California Nonpareil almonds enrobed in silky 70% single-origin Belgian dark chocolate. Deep cocoa intensity with a balanced bittersweet finish.',
            'cocoa'       => '70% Belgian Single-Origin Dark',
            'nut'         => 'California Nonpareil Slow-Roasted',
            'dietary'     => '100% Vegetarian, Gluten-Free, Zero Palm Oil',
            'shelf'       => '9 Months (Store at 15°C - 18°C)',
        ],
        [
            'name'        => 'Kiwi Chocolate Almond (Signature Real Fruit Glaze)',
            'sku'         => 'CHOC-ALM-KIWI-02',
            'price'       => '499.00',
            'sale_price'  => '399.00',
            'flavour'     => 'Kiwi Chocolate Almond',
            'tag'         => 'Signature',
            'desc'        => 'Our flagship confectionery creation. Crunchy roasted almonds wrapped in velvety white chocolate infused with an authentic tangy kiwi fruit glaze for an exhilarating burst of flavour.',
            'cocoa'       => 'Velvet White Chocolate & Kiwi Glaze',
            'nut'         => 'California Nonpareil Slow-Roasted',
            'dietary'     => '100% Vegetarian, Gluten-Free, Zero Palm Oil',
            'shelf'       => '9 Months (Store at 15°C - 18°C)',
        ],
        [
            'name'        => 'Milk Chocolate Almond (Creamy 38% Swiss Velvet)',
            'sku'         => 'CHOC-ALM-MILK-03',
            'price'       => '329.00',
            'sale_price'  => '',
            'flavour'     => 'Milk Chocolate Almond',
            'tag'         => 'Top Pick',
            'desc'        => 'Whole roasted almonds drenched in rich 38% Swiss alpine milk chocolate. Creamy dairy richness harmonizes with delightful roasted nutty crunch.',
            'cocoa'       => '38% Swiss Alpine Milk Chocolate',
            'nut'         => 'California Nonpareil Slow-Roasted',
            'dietary'     => '100% Vegetarian, Gluten-Free, Zero Palm Oil',
            'shelf'       => '9 Months (Store at 15°C - 18°C)',
        ],
        [
            'name'        => 'White Chocolate Almond (Pure Bourbon Vanilla)',
            'sku'         => 'CHOC-ALM-WHT-04',
            'price'       => '349.00',
            'sale_price'  => '',
            'flavour'     => 'White Chocolate Almond',
            'tag'         => 'Trending',
            'desc'        => 'Pure cocoa butter white chocolate infused with natural Madagascar Bourbon vanilla beans, lavishly coating crisp roasted Californian almonds.',
            'cocoa'       => 'Pure Cocoa Butter Bourbon Vanilla',
            'nut'         => 'California Nonpareil Slow-Roasted',
            'dietary'     => '100% Vegetarian, Gluten-Free, Zero Palm Oil',
            'shelf'       => '9 Months (Store at 15°C - 18°C)',
        ],
        [
            'name'        => 'Matcha Green Tea Almond (Ceremonial Uji Japanese Matcha)',
            'sku'         => 'CHOC-ALM-MAT-05',
            'price'       => '479.00',
            'sale_price'  => '419.00',
            'flavour'     => 'Other Flavoured Almonds',
            'tag'         => 'Artisan',
            'desc'        => 'Authentic ceremonial stone-ground Uji matcha green tea blended into silky white chocolate and tumbled over golden roasted almonds for an earthy, umami-sweet profile.',
            'cocoa'       => 'Ceremonial Uji Matcha White Chocolate',
            'nut'         => 'California Nonpareil Slow-Roasted',
            'dietary'     => '100% Vegetarian, Gluten-Free, Zero Palm Oil',
            'shelf'       => '9 Months (Store at 15°C - 18°C)',
        ],
        [
            'name'        => 'Salted Caramel Almond (Fleur De Sel French Golden Caramel)',
            'sku'         => 'CHOC-ALM-CAR-06',
            'price'       => '369.00',
            'sale_price'  => '',
            'flavour'     => 'Other Flavoured Almonds',
            'tag'         => 'Gourmet',
            'desc'        => 'Golden caramelized chocolate dusted with hand-harvested French Guérande Fleur de Sel sea salt over crisp roasted almonds. A masterclass in sweet-savory balance.',
            'cocoa'       => 'Golden Caramel & Fleur de Sel',
            'nut'         => 'California Nonpareil Slow-Roasted',
            'dietary'     => '100% Vegetarian, Gluten-Free, Zero Palm Oil',
            'shelf'       => '9 Months (Store at 15°C - 18°C)',
        ],
        [
            'name'        => 'Spiced Honey Cinnamon Almond (Ceylon Cinnamon Roasted)',
            'sku'         => 'CHOC-ALM-CIN-07',
            'price'       => '399.00',
            'sale_price'  => '359.00',
            'flavour'     => 'Other Flavoured Almonds',
            'tag'         => 'Special Batch',
            'desc'        => 'Aromatic warm Ceylon cinnamon and wild wildflower honey glaze paired with rich chocolate over oven-toasted almonds.',
            'cocoa'       => 'Spiced Ceylon Cinnamon & Milk Chocolate',
            'nut'         => 'California Nonpareil Slow-Roasted',
            'dietary'     => '100% Vegetarian, Gluten-Free, Zero Palm Oil',
            'shelf'       => '9 Months (Store at 15°C - 18°C)',
        ],
        [
            'name'        => 'Ruby Raspberry Almond (Natural Ruby Cocoa & Berry Dust)',
            'sku'         => 'CHOC-ALM-RUB-08',
            'price'       => '429.00',
            'sale_price'  => '',
            'flavour'     => 'Other Flavoured Almonds',
            'tag'         => 'Limited Drop',
            'desc'        => 'Naturally pink ruby cocoa beans with lush berry fruitiness, finished with tart freeze-dried raspberry dust over crunchy Californian almonds.',
            'cocoa'       => 'Natural Ruby Cocoa & Freeze-Dried Berry',
            'nut'         => 'California Nonpareil Slow-Roasted',
            'dietary'     => '100% Vegetarian, Gluten-Free, Zero Palm Oil',
            'shelf'       => '9 Months (Store at 15°C - 18°C)',
        ],
    ];

    $count = 0;
    foreach ($demo_products as $item) {
        $existing_id = 0;
        if (function_exists('wc_get_product_id_by_sku')) {
            $existing_id = wc_get_product_id_by_sku($item['sku']);
        }

        if ($existing_id) {
            $product = wc_get_product($existing_id);
        } else {
            $product = new WC_Product_Simple();
        }

        if (!$product) continue;

        $product->set_name($item['name']);
        $product->set_sku($item['sku']);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_regular_price($item['price']);
        if (!empty($item['sale_price'])) {
            $product->set_sale_price($item['sale_price']);
        } else {
            $product->set_sale_price('');
        }
        $product->set_description($item['desc']);
        $product->set_short_description(substr($item['desc'], 0, 120) . '...');
        $product->set_manage_stock(false);
        $product->set_stock_status('instock');

        // Assign to full category hierarchy: Main -> Sub -> Type -> Specific Flavour
        $cat_ids = array_filter([
            $main_cat_id,
            $sub_cat_id,
            $type_cat_id,
            isset($flavour_cats[$item['flavour']]) ? $flavour_cats[$item['flavour']] : 0,
        ]);
        if (!empty($cat_ids)) {
            $product->set_category_ids($cat_ids);
        }

        // Set Tag
        if (!empty($item['tag'])) {
            $tag_term = get_term_by('name', $item['tag'], 'product_tag');
            if (!$tag_term) {
                $tag_term = wp_insert_term($item['tag'], 'product_tag');
                $tag_id = is_array($tag_term) ? $tag_term['term_id'] : 0;
            } else {
                $tag_id = $tag_term->term_id;
            }
            if ($tag_id > 0) {
                $product->set_tag_ids([$tag_id]);
            }
        }

        $prod_id = $product->save();

        if ($prod_id) {
            // Store Confectionery Notes Meta
            update_post_meta($prod_id, '_chocolate_cocoa', sanitize_text_field($item['cocoa']));
            update_post_meta($prod_id, '_chocolate_nut', sanitize_text_field($item['nut']));
            update_post_meta($prod_id, '_chocolate_dietary', sanitize_text_field($item['dietary']));
            update_post_meta($prod_id, '_chocolate_shelf', sanitize_text_field($item['shelf']));

            // Give 5-star rating for social proof
            update_post_meta($prod_id, '_wc_average_rating', '4.95');
            update_post_meta($prod_id, '_wc_review_count', '48');

            $count++;
        }
    }

    return $count;
}

/**
 * Auto-Seed Demo Catalog & Front-Page Configuration
 * Ensures a vibrant shop and catalog out-of-the-box on init
 */
function neebites_auto_seed_demo_data() {
    if (get_option('neebites_chocolate_catalog_seeded_v2')) {
        return;
    }

    // If WooCommerce is active, seed products
    if (class_exists('WooCommerce') && function_exists('wc_get_products') && class_exists('WC_Product_Simple')) {
        neebites_import_demo_products();
    }

    update_option('neebites_chocolate_catalog_seeded_v2', 1);
}
add_action('init', 'neebites_auto_seed_demo_data');

/**
 * Cleanup Legacy Botanical Products and Categories from Live Database
 * Leaves ONLY the 8 artisan chocolate coated almond products & categories
 */
function neebites_cleanup_legacy_plant_catalog() {
    $should_run = !get_option('neebites_legacy_plants_cleaned_v7') || isset($_GET['neebites_force_cleanup']);
    if (!$should_run) {
        return;
    }

    // 1. Delete legacy plant products (IDs without CHOC- SKU)
    $all_products = get_posts([
        'post_type'   => 'product',
        'numberposts' => -1,
        'post_status' => ['publish', 'draft', 'pending', 'private'],
    ]);
    if (!empty($all_products)) {
        foreach ($all_products as $p) {
            $sku = get_post_meta($p->ID, '_sku', true);
            if (!$sku || strpos($sku, 'CHOC-') !== 0) {
                wp_delete_post($p->ID, true); // true = force delete bypass trash
            }
        }
    }

    // 2. Delete legacy plant categories
    $legacy_slugs = ['houseplants', 'baby-plants', 'rare-plants', 'plant-pots', 'plant-care', 'soil-mixes', 'easy-care', 'pet-friendly'];
    foreach ($legacy_slugs as $slug) {
        $term = get_term_by('slug', $slug, 'product_cat');
        if ($term && !is_wp_error($term)) {
            wp_delete_term($term->term_id, 'product_cat');
        }
    }

    // 3. Configure the clean chocolate confectionery navigation menu
    neebites_setup_chocolate_navigation_menu();

    // 4. Purge LiteSpeed Cache if available
    if (class_exists('LiteSpeed_Cache_API')) {
        LiteSpeed_Cache_API::purge_all();
    } elseif (defined('LSCWP_V')) {
        do_action('litespeed_purge_all');
    }

    update_option('neebites_legacy_plants_cleaned_v7', 1);
}
add_action('init', 'neebites_cleanup_legacy_plant_catalog', 20);
