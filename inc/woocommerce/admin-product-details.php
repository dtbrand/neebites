<?php
/**
 * Neebites Artisan Confectionery — WooCommerce Admin Product Details & Specifications
 *
 * Provides comprehensive, WordPress-native, pixel-perfect options in WooCommerce Admin
 * (both in the "Product Data" tabs and in a dedicated "Product Details & Specifications" Meta Box)
 * when adding or editing any product.
 *
 * @package Neebites
 * @version 2.7.0
 */

if (!defined('ABSPATH')) exit;

/**
 * 1. Register "Product Details & Specs" Tab in WooCommerce Product Data Box
 */
add_filter('woocommerce_product_data_tabs', 'neebites_add_product_details_data_tab', 21);
function neebites_add_product_details_data_tab($tabs) {
    $tabs['neebites_product_details'] = [
        'label'    => esc_html__('Product Details & Specs', 'neebites'),
        'target'   => 'neebites_product_details_panel',
        'class'    => ['show_if_simple', 'show_if_variable', 'show_if_grouped', 'show_if_external'],
        'priority' => 21, // Right after General (10) and Inventory (20)
    ];
    return $tabs;
}

/**
 * 2. Guaranteed Admin Styles Injection (admin_head & admin_enqueue_scripts)
 * Ensures styles are 100% active on post.php and post-new.php with zero reliance on missing handles.
 */
add_action('admin_head', 'neebites_print_product_details_admin_styles', 99);
function neebites_print_product_details_admin_styles() {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    $is_product_screen = ($screen && $screen->post_type === 'product');
    
    // Also check global $post_type or query param for new products
    if (!$is_product_screen) {
        global $post_type, $pagenow;
        if (($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'product') ||
            ($pagenow === 'post.php' && $post_type === 'product')) {
            $is_product_screen = true;
        }
    }

    if (!$is_product_screen) {
        return;
    }
    ?>
    <style id="neebites-admin-product-details-css">
        /* -------------------------------------------------------------
         * Neebites Native WordPress / WooCommerce Product Details UI
         * ------------------------------------------------------------- */
        #woocommerce-product-data ul.wc-tabs li.neebites_product_details_options a::before {
            content: "\f163" !important; /* dashicons-list-view */
            font-family: dashicons !important;
        }

        .neebites-admin-wrap {
            margin: 0;
            padding: 16px 20px 20px;
            background: #ffffff;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            box-sizing: border-box;
        }

        /* Top Presets & Toolbar */
        .neebites-admin-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 10px 14px;
            background: #F8F9FA;
            border: 1px solid #DCDCDE;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        .neebites-toolbar-title {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #1D2327;
        }
        .neebites-presets-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }
        .neebites-presets-group .button {
            font-size: 12px !important;
            height: 30px !important;
            line-height: 28px !important;
            padding: 0 10px !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            transition: all 0.15s ease !important;
        }
        .neebites-presets-group .button:hover {
            border-color: #2271b1 !important;
            color: #2271b1 !important;
        }
        .neebites-presets-group .btn-preset-clear {
            color: #b32d2e !important;
            border-color: #e5a4a5 !important;
            background: #ffffff !important;
        }
        .neebites-presets-group .btn-preset-clear:hover {
            background: #b32d2e !important;
            color: #ffffff !important;
            border-color: #b32d2e !important;
        }

        /* Section Visibility Toggle Card */
        .neebites-toggle-card {
            background: #F0F6FC;
            border: 1px solid #C5D9ED;
            border-left: 4px solid #2271B1;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .neebites-toggle-card label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #1D2327;
            cursor: pointer;
            margin: 0;
        }
        .neebites-toggle-card input[type="checkbox"] {
            margin: 0 !important;
            width: 18px !important;
            height: 18px !important;
            border-radius: 3px !important;
            border: 1px solid #8c8f94 !important;
            cursor: pointer !important;
        }
        .neebites-toggle-card .description {
            margin: 4px 0 0 26px !important;
            font-size: 12px !important;
            color: #50575e !important;
        }

        /* Field Card Containers */
        .neebites-card {
            background: #FFFFFF;
            border: 1px solid #DCDCDE;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 18px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .neebites-card:focus-within {
            border-color: #2271b1;
            box-shadow: 0 0 0 1px #2271b1;
        }

        .neebites-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .neebites-card-title {
            font-size: 13px;
            font-weight: 600;
            color: #1D2327;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .neebites-card-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 10px;
            background: #F0F0F1;
            color: #50575e;
        }

        /* Full Width Textarea */
        .neebites-desc-textarea {
            width: 100% !important;
            min-height: 84px !important;
            padding: 8px 12px !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
            border: 1px solid #8c8f94 !important;
            border-radius: 4px !important;
            box-sizing: border-box !important;
            color: #2c3338 !important;
            background: #ffffff !important;
            resize: vertical !important;
        }
        .neebites-desc-textarea:focus {
            border-color: #2271b1 !important;
            box-shadow: 0 0 0 1px #2271b1 !important;
            outline: 2px solid transparent !important;
        }

        /* 6 Pillars Header */
        .neebites-pillars-heading {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1D2327;
            margin: 22px 0 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .neebites-pillars-sub {
            font-size: 12px;
            color: #646970;
            margin: 0 0 14px;
        }

        /* 6 Pillars Responsive Grid (Clean 2-Column WordPress Layout) */
        .neebites-pillars-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 14px !important;
            margin-bottom: 20px !important;
        }
        @media (max-width: 900px) {
            .neebites-pillars-grid {
                grid-template-columns: 1fr !important;
            }
        }

        /* Individual Pillar Card */
        .neebites-pillar-card {
            background: #FDFCFB !important;
            border: 1px solid #DCDCDE !important;
            border-radius: 6px !important;
            padding: 12px 14px !important;
            box-sizing: border-box !important;
            transition: all 0.15s ease !important;
        }
        .neebites-pillar-card:hover {
            border-color: #8c8f94 !important;
            background: #ffffff !important;
        }
        .neebites-pillar-card:focus-within {
            border-color: #2271b1 !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 1px #2271b1 !important;
        }

        .neebites-pillar-label {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.4px !important;
            color: #1D2327 !important;
            margin-bottom: 6px !important;
        }

        /* Full Width Text Input */
        .neebites-pillar-input {
            width: 100% !important;
            height: 38px !important;
            line-height: 36px !important;
            padding: 0 10px !important;
            font-size: 13px !important;
            border: 1px solid #8c8f94 !important;
            border-radius: 4px !important;
            box-sizing: border-box !important;
            color: #2c3338 !important;
            background: #ffffff !important;
            margin: 0 !important;
        }
        .neebites-pillar-input:focus {
            border-color: #2271b1 !important;
            box-shadow: 0 0 0 1px #2271b1 !important;
            outline: 2px solid transparent !important;
        }

        .neebites-pillar-hint {
            font-size: 11px !important;
            color: #646970 !important;
            margin-top: 5px !important;
            line-height: 1.3 !important;
        }

        /* Custom Specs Repeater Table */
        .neebites-custom-specs-box {
            background: #FFFFFF;
            border: 1px solid #DCDCDE;
            border-radius: 6px;
            padding: 16px;
            margin-top: 14px;
        }
        .neebites-table-responsive {
            width: 100%;
            overflow-x: auto;
            margin: 10px 0 12px;
        }
        .neebites-custom-table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .neebites-custom-table th {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #1D2327 !important;
            background: #F0F0F1 !important;
            padding: 8px 12px !important;
            border-bottom: 1px solid #C3C4C7 !important;
            text-align: left !important;
        }
        .neebites-custom-table td {
            padding: 8px 12px !important;
            border-bottom: 1px solid #F0F0F1 !important;
            vertical-align: middle !important;
        }
        .neebites-custom-table tr:hover td {
            background: #F9F9F9 !important;
        }
        .neebites-custom-table input[type="text"] {
            width: 100% !important;
            height: 34px !important;
            padding: 0 8px !important;
            font-size: 13px !important;
            border: 1px solid #8c8f94 !important;
            border-radius: 4px !important;
            box-sizing: border-box !important;
        }
        .neebites-custom-table input[type="text"]:focus {
            border-color: #2271b1 !important;
            box-shadow: 0 0 0 1px #2271b1 !important;
            outline: 2px solid transparent !important;
        }
        .neebites-btn-remove-row {
            background: transparent !important;
            border: none !important;
            color: #b32d2e !important;
            cursor: pointer !important;
            padding: 4px 8px !important;
            border-radius: 3px !important;
            font-size: 18px !important;
            line-height: 1 !important;
            font-weight: bold !important;
        }
        .neebites-btn-remove-row:hover {
            background: #fbeaea !important;
            color: #8a1f11 !important;
        }
        .neebites-btn-add-spec {
            height: 32px !important;
            line-height: 30px !important;
            padding: 0 12px !important;
            font-size: 13px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
        }
    </style>
    <?php
}

/**
 * 3. Guaranteed Admin Scripts Injection (admin_footer)
 */
add_action('admin_footer', 'neebites_print_product_details_admin_scripts', 99);
function neebites_print_product_details_admin_scripts() {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    $is_product_screen = ($screen && $screen->post_type === 'product');

    if (!$is_product_screen) {
        global $post_type, $pagenow;
        if (($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'product') ||
            ($pagenow === 'post.php' && $post_type === 'product')) {
            $is_product_screen = true;
        }
    }

    if (!$is_product_screen) {
        return;
    }
    ?>
    <script id="neebites-admin-product-details-js">
        jQuery(document).ready(function($) {
            // Keep mirrored inputs synchronized across Product Data tab and Meta Box
            $(document).on('input change', '[data-neebites-sync]', function() {
                var key = $(this).data('neebites-sync');
                var val = $(this).val();
                if ($(this).is(':checkbox')) {
                    var checked = $(this).is(':checked');
                    $('[data-neebites-sync="' + key + '"]').not(this).prop('checked', checked);
                } else {
                    $('[data-neebites-sync="' + key + '"]').not(this).val(val);
                }
            });

            // Quick Preset Fillers
            $(document).on('click', '.btn-preset-milk', function(e) {
                e.preventDefault();
                $('[data-neebites-sync="cocoa"]').val('38% Swiss Alpine Milk Chocolate').trigger('change');
                $('[data-neebites-sync="nut"]').val('California Nonpareil Slow-Roasted Almonds').trigger('change');
                $('[data-neebites-sync="glaze"]').val('Velvet Milk Chocolate Glaze & Pure Cocoa Butter').trigger('change');
                $('[data-neebites-sync="dietary"]').val('100% Vegetarian, Gluten-Free, Zero Palm Oil').trigger('change');
                $('[data-neebites-sync="shelf"]').val('9 Months (Store at 15°C - 18°C in cool dry place)').trigger('change');
                $('[data-neebites-sync="packaging"]').val('Airtight Food-Grade Tin with Insulated Thermal Wrap').trigger('change');
                $('[data-neebites-sync="desc"]').val('Creamy 38% Swiss Alpine milk chocolate draped over perfectly slow-roasted California almonds. Indulgent, silky, and balanced for everyday luxury snacking.').trigger('change');
            });

            $(document).on('click', '.btn-preset-kiwi', function(e) {
                e.preventDefault();
                $('[data-neebites-sync="cocoa"]').val('Velvet White Chocolate & Kiwi Confiture').trigger('change');
                $('[data-neebites-sync="nut"]').val('California Nonpareil Slow-Roasted Almonds').trigger('change');
                $('[data-neebites-sync="glaze"]').val('Signature Real Kiwi Fruit Confiture & Pure Cocoa Butter').trigger('change');
                $('[data-neebites-sync="dietary"]').val('100% Vegetarian, Gluten-Free, Zero Palm Oil').trigger('change');
                $('[data-neebites-sync="shelf"]').val('9 Months (Store at 15°C - 18°C in cool dry place)').trigger('change');
                $('[data-neebites-sync="packaging"]').val('Airtight Food-Grade Tin with Insulated Thermal Wrap').trigger('change');
                $('[data-neebites-sync="desc"]').val('Premium almonds coated with rich kiwi-infused velvety white chocolate for a refreshing burst of tangy fruitiness and satisfying almond crunch.').trigger('change');
            });

            $(document).on('click', '.btn-preset-dark', function(e) {
                e.preventDefault();
                $('[data-neebites-sync="cocoa"]').val('70% Single-Origin Belgian Dark Ganache').trigger('change');
                $('[data-neebites-sync="nut"]').val('California Nonpareil Slow-Roasted Almonds').trigger('change');
                $('[data-neebites-sync="glaze"]').val('Bittersweet Noir Glaze & Dutch Cocoa Dust').trigger('change');
                $('[data-neebites-sync="dietary"]').val('100% Vegetarian, Gluten-Free, Zero Palm Oil').trigger('change');
                $('[data-neebites-sync="shelf"]').val('9 Months (Store at 15°C - 18°C in cool dry place)').trigger('change');
                $('[data-neebites-sync="packaging"]').val('Airtight Food-Grade Tin with Insulated Thermal Wrap').trigger('change');
                $('[data-neebites-sync="desc"]').val('Intense 70% single-origin dark chocolate coating crisp California roasted almonds with a touch of bittersweet cocoa dust.').trigger('change');
            });

            $(document).on('click', '.btn-preset-clear', function(e) {
                e.preventDefault();
                if (confirm('Clear all specification fields?')) {
                    $('[data-neebites-sync]').val('').trigger('change');
                }
            });

            // Dynamic Custom Specification Rows
            $(document).on('click', '.neebites-btn-add-spec', function(e) {
                e.preventDefault();
                var table = $(this).closest('.neebites-custom-specs-box').find('.neebites-custom-table tbody');
                var count = table.find('tr').length;
                var row = '<tr>' +
                    '<td><input type="text" name="_neebites_custom_specs[' + count + '][label]" placeholder="e.g., Country of Origin" style="width:100%; box-sizing:border-box;" /></td>' +
                    '<td><input type="text" name="_neebites_custom_specs[' + count + '][val]" placeholder="e.g., Switzerland & USA" style="width:100%; box-sizing:border-box;" /></td>' +
                    '<td style="text-align:center;"><button type="button" class="neebites-btn-remove-row" title="Remove">&times;</button></td>' +
                    '</tr>';
                table.append(row);
            });

            $(document).on('click', '.neebites-btn-remove-row', function(e) {
                e.preventDefault();
                $(this).closest('tr').remove();
            });
        });
    </script>
    <?php
}

/**
 * 4. Render HTML Inputs for Product Details (Shared Clean Architecture)
 */
function neebites_render_product_details_form_fields($post_id, $context = 'tab') {
    $show_specs = get_post_meta($post_id, '_neebites_show_specs', true);
    if ($show_specs === '') $show_specs = 'yes'; // Default enabled

    $custom_desc = get_post_meta($post_id, '_neebites_product_details_desc', true);
    $cocoa       = get_post_meta($post_id, '_chocolate_cocoa', true);
    $nut         = get_post_meta($post_id, '_chocolate_nut', true);
    $glaze       = get_post_meta($post_id, '_chocolate_glaze', true);
    $dietary     = get_post_meta($post_id, '_chocolate_dietary', true);
    $shelf       = get_post_meta($post_id, '_chocolate_shelf', true);
    $packaging   = get_post_meta($post_id, '_chocolate_packaging', true);
    $custom_specs= get_post_meta($post_id, '_neebites_custom_specs', true);
    if (!is_array($custom_specs)) $custom_specs = [];

    wp_nonce_field('neebites_product_details_action', 'neebites_product_details_nonce');
    ?>
    <div class="neebites-admin-wrap" style="padding: 16px 20px; box-sizing: border-box;">
        
        <!-- Quick Presets Toolbar -->
        <div class="neebites-admin-toolbar" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 10px 14px; background: #F8F9FA; border: 1px solid #DCDCDE; border-radius: 6px; margin-bottom: 16px;">
            <div class="neebites-toolbar-title" style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #1D2327;">
                <span class="dashicons dashicons-admin-customizer" style="font-size: 18px; line-height: 18px;"></span>
                <span><?php esc_html_e('Quick Presets:', 'neebites'); ?></span>
            </div>
            <div class="neebites-presets-group" style="display: flex; align-items: center; flex-wrap: wrap; gap: 6px;">
                <button type="button" class="button button-secondary neebites-preset-btn btn-preset-milk"><?php esc_html_e('🍫 Milk Chocolate', 'neebites'); ?></button>
                <button type="button" class="button button-secondary neebites-preset-btn btn-preset-kiwi"><?php esc_html_e('🥝 Kiwi White Choc', 'neebites'); ?></button>
                <button type="button" class="button button-secondary neebites-preset-btn btn-preset-dark"><?php esc_html_e('🍫 70% Dark Noir', 'neebites'); ?></button>
                <button type="button" class="button button-secondary neebites-preset-btn btn-preset-clear"><?php esc_html_e('✖ Clear', 'neebites'); ?></button>
            </div>
        </div>

        <!-- Section Visibility Toggle Card -->
        <div class="neebites-toggle-card" style="background: #F0F6FC; border: 1px solid #C5D9ED; border-left: 4px solid #2271B1; padding: 12px 16px; border-radius: 4px; margin-bottom: 18px;">
            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: #1D2327; cursor: pointer;">
                <input type="checkbox" name="_neebites_show_specs" value="yes" <?php checked($show_specs, 'yes'); ?> data-neebites-sync="show_specs" style="margin: 0; width: 18px; height: 18px;" />
                <span><?php esc_html_e('Display "PRODUCT DETAILS" & Specifications Table on this Product Page', 'neebites'); ?></span>
            </label>
            <p class="description" style="margin: 4px 0 0 26px; font-size: 12px; color: #50575e;">
                <?php esc_html_e('When enabled, the tasting notes description and the 6-pillar confectionery specifications table render on the single product page.', 'neebites'); ?>
            </p>
        </div>

        <!-- Product Details / Tasting Notes Textarea -->
        <div class="neebites-card" style="background: #FFFFFF; border: 1px solid #DCDCDE; border-radius: 6px; padding: 14px 16px; margin-bottom: 18px;">
            <div class="neebites-card-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <h4 class="neebites-card-title" style="font-size: 13px; font-weight: 600; color: #1D2327; margin: 0;">
                    <?php esc_html_e('Product Details Description & Tasting Notes', 'neebites'); ?>
                </h4>
                <span class="neebites-card-badge" style="font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 10px; background: #F0F0F1; color: #50575e;">
                    <?php esc_html_e('Optional Override', 'neebites'); ?>
                </span>
            </div>
            <textarea 
                id="neebites_details_desc_<?php echo esc_attr($context); ?>" 
                name="_neebites_product_details_desc" 
                class="large-text widefat neebites-desc-textarea" 
                rows="3" 
                style="width: 100% !important; min-height: 80px; padding: 8px 10px; font-size: 13px; border: 1px solid #8c8f94; border-radius: 4px; box-sizing: border-box;"
                placeholder="<?php esc_attr_e('Enter rich tasting notes or artisan confection description... (Leave blank to use the standard Product Short Description)', 'neebites'); ?>"
                data-neebites-sync="desc"
            ><?php echo esc_textarea($custom_desc); ?></textarea>
            <p class="description" style="margin: 6px 0 0; font-size: 12px; color: #646970;">
                <?php esc_html_e('This paragraph renders directly beneath the "PRODUCT DETAILS" heading on the single product page.', 'neebites'); ?>
            </p>
        </div>

        <!-- 6 Core Confectionery Specifications Section -->
        <h4 class="neebites-pillars-heading" style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #1D2327; margin: 18px 0 4px;">
            <?php esc_html_e('Core Confectionery Specifications (6 Pillars)', 'neebites'); ?>
        </h4>
        <p class="neebites-pillars-sub" style="font-size: 12px; color: #646970; margin: 0 0 12px;">
            <?php esc_html_e('These 6 pillars populate the customer-facing 2-column specifications table. Values are automatically mapped to product defaults if left empty.', 'neebites'); ?>
        </p>

        <div class="neebites-pillars-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 14px; margin-bottom: 18px;">
            
            <!-- 1. Cocoa Profile -->
            <div class="neebites-pillar-card" style="background: #FDFCFB; border: 1px solid #DCDCDE; border-radius: 6px; padding: 12px 14px; box-sizing: border-box;">
                <label class="neebites-pillar-label" style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1D2327; margin-bottom: 6px;">
                    <span>🍫</span>
                    <span><?php esc_html_e('Cocoa Profile', 'neebites'); ?></span>
                </label>
                <input 
                    type="text" 
                    class="regular-text widefat neebites-pillar-input" 
                    name="_chocolate_cocoa" 
                    value="<?php echo esc_attr($cocoa); ?>" 
                    placeholder="<?php esc_attr_e('e.g., 38% Swiss Alpine Milk Chocolate', 'neebites'); ?>" 
                    data-neebites-sync="cocoa"
                    style="width: 100% !important; height: 38px; padding: 0 10px; font-size: 13px; border: 1px solid #8c8f94; border-radius: 4px; box-sizing: border-box;"
                />
                <div class="neebites-pillar-hint" style="font-size: 11px; color: #646970; margin-top: 4px;">
                    <?php esc_html_e('Specifies cocoa percentage, bean origin, or chocolate type.', 'neebites'); ?>
                </div>
            </div>

            <!-- 2. Nut Roasting -->
            <div class="neebites-pillar-card" style="background: #FDFCFB; border: 1px solid #DCDCDE; border-radius: 6px; padding: 12px 14px; box-sizing: border-box;">
                <label class="neebites-pillar-label" style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1D2327; margin-bottom: 6px;">
                    <span>🌰</span>
                    <span><?php esc_html_e('Nut Roasting', 'neebites'); ?></span>
                </label>
                <input 
                    type="text" 
                    class="regular-text widefat neebites-pillar-input" 
                    name="_chocolate_nut" 
                    value="<?php echo esc_attr($nut); ?>" 
                    placeholder="<?php esc_attr_e('e.g., California Nonpareil Slow-Roasted', 'neebites'); ?>" 
                    data-neebites-sync="nut"
                    style="width: 100% !important; height: 38px; padding: 0 10px; font-size: 13px; border: 1px solid #8c8f94; border-radius: 4px; box-sizing: border-box;"
                />
                <div class="neebites-pillar-hint" style="font-size: 11px; color: #646970; margin-top: 4px;">
                    <?php esc_html_e('Nut variety, batch roasting level, or origin.', 'neebites'); ?>
                </div>
            </div>

            <!-- 3. Glaze & Flavor -->
            <div class="neebites-pillar-card" style="background: #FDFCFB; border: 1px solid #DCDCDE; border-radius: 6px; padding: 12px 14px; box-sizing: border-box;">
                <label class="neebites-pillar-label" style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1D2327; margin-bottom: 6px;">
                    <span>✨</span>
                    <span><?php esc_html_e('Glaze & Flavor', 'neebites'); ?></span>
                </label>
                <input 
                    type="text" 
                    class="regular-text widefat neebites-pillar-input" 
                    name="_chocolate_glaze" 
                    value="<?php echo esc_attr($glaze); ?>" 
                    placeholder="<?php esc_attr_e('e.g., Velvet Milk Chocolate Glaze & Pure Cocoa Butter', 'neebites'); ?>" 
                    data-neebites-sync="glaze"
                    style="width: 100% !important; height: 38px; padding: 0 10px; font-size: 13px; border: 1px solid #8c8f94; border-radius: 4px; box-sizing: border-box;"
                />
                <div class="neebites-pillar-hint" style="font-size: 11px; color: #646970; margin-top: 4px;">
                    <?php esc_html_e('Signature fruit confiture, caramel finish, or ganache glaze.', 'neebites'); ?>
                </div>
            </div>

            <!-- 4. Dietary Integrity -->
            <div class="neebites-pillar-card" style="background: #FDFCFB; border: 1px solid #DCDCDE; border-radius: 6px; padding: 12px 14px; box-sizing: border-box;">
                <label class="neebites-pillar-label" style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1D2327; margin-bottom: 6px;">
                    <span>🌿</span>
                    <span><?php esc_html_e('Dietary Integrity', 'neebites'); ?></span>
                </label>
                <input 
                    type="text" 
                    class="regular-text widefat neebites-pillar-input" 
                    name="_chocolate_dietary" 
                    value="<?php echo esc_attr($dietary); ?>" 
                    placeholder="<?php esc_attr_e('e.g., 100% Vegetarian, Gluten-Free, Zero Palm Oil', 'neebites'); ?>" 
                    data-neebites-sync="dietary"
                    style="width: 100% !important; height: 38px; padding: 0 10px; font-size: 13px; border: 1px solid #8c8f94; border-radius: 4px; box-sizing: border-box;"
                />
                <div class="neebites-pillar-hint" style="font-size: 11px; color: #646970; margin-top: 4px;">
                    <?php esc_html_e('Dietary classifications, allergens, or certifications.', 'neebites'); ?>
                </div>
            </div>

            <!-- 5. Shelf Life & Storage -->
            <div class="neebites-pillar-card" style="background: #FDFCFB; border: 1px solid #DCDCDE; border-radius: 6px; padding: 12px 14px; box-sizing: border-box;">
                <label class="neebites-pillar-label" style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1D2327; margin-bottom: 6px;">
                    <span>⏳</span>
                    <span><?php esc_html_e('Shelf Life & Storage', 'neebites'); ?></span>
                </label>
                <input 
                    type="text" 
                    class="regular-text widefat neebites-pillar-input" 
                    name="_chocolate_shelf" 
                    value="<?php echo esc_attr($shelf); ?>" 
                    placeholder="<?php esc_attr_e('e.g., 9 Months (Store at 15°C - 18°C)', 'neebites'); ?>" 
                    data-neebites-sync="shelf"
                    style="width: 100% !important; height: 38px; padding: 0 10px; font-size: 13px; border: 1px solid #8c8f94; border-radius: 4px; box-sizing: border-box;"
                />
                <div class="neebites-pillar-hint" style="font-size: 11px; color: #646970; margin-top: 4px;">
                    <?php esc_html_e('Optimal temperature and storage shelf-life guidelines.', 'neebites'); ?>
                </div>
            </div>

            <!-- 6. Packaging -->
            <div class="neebites-pillar-card" style="background: #FDFCFB; border: 1px solid #DCDCDE; border-radius: 6px; padding: 12px 14px; box-sizing: border-box;">
                <label class="neebites-pillar-label" style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1D2327; margin-bottom: 6px;">
                    <span>🎁</span>
                    <span><?php esc_html_e('Packaging', 'neebites'); ?></span>
                </label>
                <input 
                    type="text" 
                    class="regular-text widefat neebites-pillar-input" 
                    name="_chocolate_packaging" 
                    value="<?php echo esc_attr($packaging); ?>" 
                    placeholder="<?php esc_attr_e('e.g., Airtight Food-Grade Tin with Insulated Thermal Wrap', 'neebites'); ?>" 
                    data-neebites-sync="packaging"
                    style="width: 100% !important; height: 38px; padding: 0 10px; font-size: 13px; border: 1px solid #8c8f94; border-radius: 4px; box-sizing: border-box;"
                />
                <div class="neebites-pillar-hint" style="font-size: 11px; color: #646970; margin-top: 4px;">
                    <?php esc_html_e('Confectionery tin, gift box, or insulated thermal protection.', 'neebites'); ?>
                </div>
            </div>
        </div>

        <!-- Additional Custom Specifications (Dynamic Repeater) -->
        <div class="neebites-custom-specs-box" style="background: #FFFFFF; border: 1px solid #DCDCDE; border-radius: 6px; padding: 16px;">
            <div class="neebites-card-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                <h4 class="neebites-card-title" style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #1D2327; margin: 0;">
                    <?php esc_html_e('Additional Custom Specifications (Optional)', 'neebites'); ?>
                </h4>
                <span class="neebites-card-badge" style="font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 10px; background: #F0F0F1; color: #50575e;">
                    <?php esc_html_e('Unlimited Rows', 'neebites'); ?>
                </span>
            </div>
            <p class="description" style="font-size: 12px; color: #646970; margin: 0 0 10px;">
                <?php esc_html_e('Add any custom specification rows (e.g., Country of Origin, Net Weight, Allergen Advice, Serving Size) to display in the product specifications grid.', 'neebites'); ?>
            </p>

            <div class="neebites-table-responsive" style="width: 100%; overflow-x: auto; margin: 8px 0 12px;">
                <table class="wp-list-table widefat striped neebites-custom-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 40%; font-size: 12px; font-weight: 600; padding: 8px 10px;"><?php esc_html_e('Specification Label', 'neebites'); ?></th>
                            <th style="width: 50%; font-size: 12px; font-weight: 600; padding: 8px 10px;"><?php esc_html_e('Specification Value', 'neebites'); ?></th>
                            <th style="width: 10%; text-align: center; font-size: 12px; font-weight: 600; padding: 8px 10px;"><?php esc_html_e('Action', 'neebites'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($custom_specs)) : ?>
                            <?php foreach ($custom_specs as $index => $cs) : 
                                $lbl = isset($cs['label']) ? $cs['label'] : '';
                                $val = isset($cs['val']) ? $cs['val'] : '';
                            ?>
                            <tr>
                                <td><input type="text" class="regular-text widefat" name="_neebites_custom_specs[<?php echo esc_attr($index); ?>][label]" value="<?php echo esc_attr($lbl); ?>" placeholder="<?php esc_attr_e('e.g., Country of Origin', 'neebites'); ?>" style="width:100%; box-sizing:border-box;" /></td>
                                <td><input type="text" class="regular-text widefat" name="_neebites_custom_specs[<?php echo esc_attr($index); ?>][val]" value="<?php echo esc_attr($val); ?>" placeholder="<?php esc_attr_e('e.g., Switzerland & USA', 'neebites'); ?>" style="width:100%; box-sizing:border-box;" /></td>
                                <td style="text-align: center;"><button type="button" class="neebites-btn-remove-row" title="<?php esc_attr_e('Remove', 'neebites'); ?>" style="color:#b32d2e; cursor:pointer; background:none; border:none; font-size:18px;">&times;</button></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <button type="button" class="button button-primary neebites-btn-add-spec" style="display: inline-flex; align-items: center; gap: 4px;">
                <span class="dashicons dashicons-plus-alt2" style="font-size: 16px; line-height: 16px;"></span>
                <?php esc_html_e('Add Custom Specification Row', 'neebites'); ?>
            </button>
        </div>
    </div>
    <?php
}

/**
 * 5. Render WooCommerce Product Data Tab Panel
 */
add_action('woocommerce_product_data_panels', 'neebites_render_product_details_panel');
function neebites_render_product_details_panel() {
    global $post;
    if (!$post) return;
    ?>
    <div id="neebites_product_details_panel" class="panel woocommerce_options_panel hidden">
        <?php neebites_render_product_details_form_fields($post->ID, 'wc_tab'); ?>
    </div>
    <?php
}

/**
 * 6. Register Dedicated Meta Box for Product Details & Specifications
 */
add_action('add_meta_boxes_product', 'neebites_register_product_details_metabox');
function neebites_register_product_details_metabox() {
    add_meta_box(
        'neebites_product_details_metabox',
        esc_html__('🍫 PRODUCT DETAILS & SPECIFICATIONS (Neebites Artisan)', 'neebites'),
        'neebites_render_product_details_metabox',
        'product',
        'normal',
        'high'
    );
}

function neebites_render_product_details_metabox($post) {
    if (!$post) return;
    neebites_render_product_details_form_fields($post->ID, 'metabox');
}

/**
 * 7. Save Product Details & Specifications Meta
 */
function neebites_save_product_details_meta_fields($post_id) {
    // Nonce verification
    if (!isset($_POST['neebites_product_details_nonce']) || !wp_verify_nonce($_POST['neebites_product_details_nonce'], 'neebites_product_details_action')) {
        return;
    }

    // Autosave check
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Permission check
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Visibility toggle
    $show_specs = isset($_POST['_neebites_show_specs']) ? 'yes' : 'no';
    update_post_meta($post_id, '_neebites_show_specs', sanitize_text_field($show_specs));

    // Description override
    if (isset($_POST['_neebites_product_details_desc'])) {
        update_post_meta($post_id, '_neebites_product_details_desc', wp_kses_post($_POST['_neebites_product_details_desc']));
    }

    // 6 Core Pillars
    $fields = [
        '_chocolate_cocoa',
        '_chocolate_nut',
        '_chocolate_glaze',
        '_chocolate_dietary',
        '_chocolate_shelf',
        '_chocolate_packaging',
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field(wp_unslash($_POST[$field])));
        }
    }

    // Dynamic Custom Specs Repeater
    if (isset($_POST['_neebites_custom_specs']) && is_array($_POST['_neebites_custom_specs'])) {
        $clean_specs = [];
        foreach ($_POST['_neebites_custom_specs'] as $row) {
            if (!empty($row['label']) || !empty($row['val'])) {
                $clean_specs[] = [
                    'label' => sanitize_text_field(wp_unslash($row['label'])),
                    'val'   => sanitize_text_field(wp_unslash($row['val'])),
                ];
            }
        }
        update_post_meta($post_id, '_neebites_custom_specs', $clean_specs);
    } else {
        delete_post_meta($post_id, '_neebites_custom_specs');
    }
}
add_action('woocommerce_process_product_meta', 'neebites_save_product_details_meta_fields');
add_action('save_post_product', 'neebites_save_product_details_meta_fields');
