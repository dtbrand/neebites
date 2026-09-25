<?php
/**
 * The sidebar containing the Next-Level WooCommerce Shop Filters
 *
 * @package Neebites
 * @version 1.3.0
 */

if (!defined('ABSPATH')) exit;

$sidebar_pos = neebites_get_option('shop_sidebar', 'drawer');

$shop_url     = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/shop');
$cat_rows     = function_exists('neebites_shop_category_counts') ? neebites_shop_category_counts() : [];
$total_count  = function_exists('neebites_shop_total_product_count') ? neebites_shop_total_product_count() : 0;
$current_cat  = is_tax('product_cat') ? get_queried_object() : null;
?>

<aside id="shop-secondary" class="widget-area shop-sidebar" role="complementary" aria-label="<?php esc_attr_e('Shop Filters Sidebar', 'neebites'); ?>">

    <!-- Mobile Drawer Header -->
    <div class="shop-filter-mobile-header">
        <h3 class="filter-mobile-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
            <?php esc_html_e('Confectionery Filters', 'neebites'); ?>
        </h3>
        <button type="button" class="btn-close-filter-drawer" aria-label="<?php esc_attr_e('Close Filter Drawer', 'neebites'); ?>">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Myntra Desktop Filters Header -->
    <div class="myntra-sidebar-header">
        <span class="myntra-sidebar-title"><?php esc_html_e('FILTERS', 'neebites'); ?></span>
        <button type="button" class="btn-clear-all-filters myntra-clear-all"><?php esc_html_e('CLEAR ALL', 'neebites'); ?></button>
    </div>

    <!-- Active filters summary (populated by main.js) -->
    <div class="shop-active-filters" hidden>
        <div class="active-filters-head">
            <span class="active-filters-title"><?php esc_html_e('Active Filters', 'neebites'); ?></span>
            <button type="button" class="btn-clear-all-filters"><?php esc_html_e('Clear All', 'neebites'); ?></button>
        </div>
        <div class="active-filter-chips"></div>
    </div>

    <?php if (is_active_sidebar('shop-sidebar')) : ?>
        <?php dynamic_sidebar('shop-sidebar'); ?>
    <?php else : ?>

        <!-- 1. Confectionery Categories -->
        <div class="shop-filter-widget filter-categories" data-filter-group="category">
            <h4 class="filter-widget-title"><?php esc_html_e('Categories', 'neebites'); ?></h4>
            <ul class="filter-list category-filter-list">
                <li class="<?php echo $current_cat ? '' : 'active'; ?>">
                    <a href="<?php echo esc_url($shop_url); ?>" class="filter-link">
                        <span class="checkbox-box" aria-hidden="true"></span>
                        <span class="filter-name"><?php esc_html_e('All Confections', 'neebites'); ?></span>
                        <span class="filter-count" data-count-for="all">(<?php echo esc_html($total_count); ?>)</span>
                    </a>
                </li>
                <?php foreach ($cat_rows as $row) : ?>
                    <li class="<?php echo ($current_cat && $current_cat->slug === $row['slug']) ? 'active' : ''; ?>">
                        <a href="<?php echo esc_url(add_query_arg('product_cat', $row['slug'], $shop_url)); ?>" class="filter-link">
                            <span class="checkbox-box" aria-hidden="true"></span>
                            <span class="filter-name"><?php echo esc_html($row['name']); ?></span>
                            <span class="filter-count" data-count-for="<?php echo esc_attr($row['slug']); ?>">(<?php echo esc_html($row['count']); ?>)</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- 2. Cocoa Profile Filter -->
        <div class="shop-filter-widget filter-light" data-filter-group="light">
            <h4 class="filter-widget-title"><?php esc_html_e('Cocoa Profile', 'neebites'); ?></h4>
            <div class="filter-chips-wrap">
                <button type="button" class="filter-chip active" data-light="all"><?php esc_html_e('All Types', 'neebites'); ?></button>
                <button type="button" class="filter-chip" data-light="dark">🍫 <?php esc_html_e('Dark (70%+)', 'neebites'); ?></button>
                <button type="button" class="filter-chip" data-light="milk">🥛 <?php esc_html_e('Milk (38%)', 'neebites'); ?></button>
                <button type="button" class="filter-chip" data-light="white">🤍 <?php esc_html_e('White & Kiwi', 'neebites'); ?></button>
            </div>
        </div>

        <!-- 3. Dietary Purity Toggle Filter -->
        <div class="shop-filter-widget filter-pets" data-filter-group="pets">
            <h4 class="filter-widget-title"><?php esc_html_e('Dietary & Purity', 'neebites'); ?></h4>
            <label class="pet-friendly-toggle">
                <input type="checkbox" id="filter-pet-friendly" data-filter="pets" />
                <span class="toggle-slider" aria-hidden="true"></span>
                <span class="toggle-label">🍫 <?php esc_html_e('100% Vegetarian & Pure Cocoa Butter', 'neebites'); ?></span>
                <span class="filter-count" data-count-for="petfriendly"></span>
            </label>
        </div>

        <!-- 4. Flavour Variants Filter -->
        <div class="shop-filter-widget filter-difficulty" data-filter-group="difficulty">
            <h4 class="filter-widget-title"><?php esc_html_e('Flavour Variants', 'neebites'); ?></h4>
            <ul class="filter-list">
                <li>
                    <label class="checkbox-filter-label">
                        <input type="checkbox" data-filter="difficulty" value="dark" />
                        <span class="checkbox-box" aria-hidden="true"></span>
                        <span class="label-text"><?php esc_html_e('Dark Chocolate Almond', 'neebites'); ?></span>
                        <span class="filter-count" data-count-for="dark"></span>
                    </label>
                </li>
                <li>
                    <label class="checkbox-filter-label">
                        <input type="checkbox" data-filter="difficulty" value="kiwi" />
                        <span class="checkbox-box" aria-hidden="true"></span>
                        <span class="label-text"><?php esc_html_e('Kiwi Chocolate Almond', 'neebites'); ?></span>
                        <span class="filter-count" data-count-for="kiwi"></span>
                    </label>
                </li>
                <li>
                    <label class="checkbox-filter-label">
                        <input type="checkbox" data-filter="difficulty" value="milk" />
                        <span class="checkbox-box" aria-hidden="true"></span>
                        <span class="label-text"><?php esc_html_e('Milk Chocolate Almond', 'neebites'); ?></span>
                        <span class="filter-count" data-count-for="milk"></span>
                    </label>
                </li>
                <li>
                    <label class="checkbox-filter-label">
                        <input type="checkbox" data-filter="difficulty" value="white" />
                        <span class="checkbox-box" aria-hidden="true"></span>
                        <span class="label-text"><?php esc_html_e('White Chocolate Almond', 'neebites'); ?></span>
                        <span class="filter-count" data-count-for="white"></span>
                    </label>
                </li>
                <li>
                    <label class="checkbox-filter-label">
                        <input type="checkbox" data-filter="difficulty" value="other" />
                        <span class="checkbox-box" aria-hidden="true"></span>
                        <span class="label-text"><?php esc_html_e('Other Flavoured Almonds', 'neebites'); ?></span>
                        <span class="filter-count" data-count-for="other"></span>
                    </label>
                </li>
            </ul>
        </div>

        <!-- 5. Price Range Visual Slider -->
        <div class="shop-filter-widget filter-price" data-filter-group="price">
            <h4 class="filter-widget-title"><?php esc_html_e('Price Range', 'neebites'); ?></h4>
            <div class="price-range-slider-wrap">
                <input type="range" min="0" max="1000" value="1000" step="10" class="price-slider-input" id="price-slider" data-auto-bounds="1" aria-label="<?php esc_attr_e('Maximum price', 'neebites'); ?>" />
                <div class="price-range-labels">
                    <span class="price-min">₹0</span>
                    <span class="price-current" id="price-current-val"><?php esc_html_e('All Prices', 'neebites'); ?></span>
                </div>
            </div>
        </div>

        <!-- 6. Confectionery Perks Guarantee Card -->
        <div class="shop-filter-widget filter-perks-card">
            <div class="shop-perks-inner">
                <span class="perks-badge">🍫 <?php esc_html_e('The Neebites Promise', 'neebites'); ?></span>
                <h5><?php esc_html_e('Melt-Proof Delivery Guarantee', 'neebites'); ?></h5>
                <p><?php esc_html_e('Every batch is packed in insulated thermal chill-packs with food-grade gel packs to guarantee your chocolates arrive in solid, velvety temper.', 'neebites'); ?></p>
                <div class="perks-icons">
                    <span>❄️ <?php esc_html_e('Cold-Chain Pack', 'neebites'); ?></span>
                    <span>🌰 <?php esc_html_e('California Almonds', 'neebites'); ?></span>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <div class="shop-filter-drawer-footer">
        <button type="button" class="btn-clear-all-filters btn-clear-all-mobile"><?php esc_html_e('Clear All', 'neebites'); ?></button>
        <button type="button" class="btn-apply-filters">
            <?php esc_html_e('Show', 'neebites'); ?> <span class="apply-count"><?php echo esc_html($total_count); ?></span> <?php esc_html_e('Items', 'neebites'); ?>
        </button>
    </div>
</aside>
