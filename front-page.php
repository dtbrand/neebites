<?php
/**
 * The Front Page Template - Luxury Artisan Chocolatier Experience
 *
 * @package Neebites
 * @version 2.1.0
 */

if (!defined('ABSPATH')) exit;

get_header();

$theme_uri = get_template_directory_uri();
?>

<div id="primary" class="front-page-sanctuary">

    <!-- 1. Hero Artisan Chocolatier Banner -->
    <section class="sanctuary-hero" aria-label="<?php esc_attr_e('Hero Banner', 'neebites'); ?>">
        <div class="container hero-container">
            <div class="hero-content-col">
                <div class="hero-rating-badge">
                    <span class="rating-stars">★★★★★</span>
                    <span class="rating-text"><?php esc_html_e('4.9/5 from 18,000+ Chocolate Enthusiasts', 'neebites'); ?></span>
                </div>
                <h1 class="hero-title">
                    <?php esc_html_e('Handcrafted Artisan Chocolates & Confectionery', 'neebites'); ?>
                </h1>
                <p class="hero-description">
                    <?php esc_html_e('Silky single-origin Belgian chocolate, California slow-roasted almonds, and signature fruit glazes delivered to your door in insulated cold-pack freshness.', 'neebites'); ?>
                </p>
                <div class="hero-actions">
                    <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/shop')); ?>" class="btn btn-primary btn-lg">
                        <span class="btn-text"><?php esc_html_e('Explore Chocolates', 'neebites'); ?></span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                    </a>
                    <a href="#trending-confections" class="btn btn-outline btn-lg">
                        <?php esc_html_e('Flavour Variants 🍫', 'neebites'); ?>
                    </a>
                </div>

                <!-- Trust Perks Bar -->
                <div class="hero-trust-bar">
                    <div class="trust-item">
                        <span class="trust-icon">🛡️</span>
                        <span class="trust-label"><?php esc_html_e('Melt-Proof Guarantee', 'neebites'); ?></span>
                    </div>
                    <div class="trust-item">
                        <span class="trust-icon">❄️</span>
                        <span class="trust-label"><?php esc_html_e('Insulated Cold Packaging', 'neebites'); ?></span>
                    </div>
                    <div class="trust-item">
                        <span class="trust-icon">🚚</span>
                        <span class="trust-label"><?php esc_html_e('Free Delivery over ₹500', 'neebites'); ?></span>
                    </div>
                </div>
            </div>

            <div class="hero-media-col">
                <div class="hero-artwork-wrapper">
                    <div class="hero-blob-bg"></div>
                    <img src="<?php echo esc_url($theme_uri . '/assets/images/hero-chocolate.svg'); ?>" alt="<?php esc_attr_e('Artisan Chocolate Coated Almonds', 'neebites'); ?>" class="hero-primary-plant hero-primary-chocolate" width="460" height="460" loading="eager" fetchpriority="high" />
                    
                    <!-- Floating Specimen Feature Cards -->
                    <div class="floating-plant-card card-top-right">
                        <span class="float-emoji">🍫</span>
                        <div>
                            <strong><?php esc_html_e('100% Pure Cocoa Butter', 'neebites'); ?></strong>
                            <small><?php esc_html_e('Zero palm oil & zero compound fat', 'neebites'); ?></small>
                        </div>
                    </div>

                    <div class="floating-plant-card card-bottom-left">
                        <span class="float-emoji">🌰</span>
                        <div>
                            <strong><?php esc_html_e('California Roasted Almonds', 'neebites'); ?></strong>
                            <small><?php esc_html_e('Slow-roasted to golden crunch', 'neebites'); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Circular Category Bubbles Navigation (Website Hierarchy) -->
    <section class="sanctuary-categories-section">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-tag"><?php esc_html_e('Curated Collections', 'neebites'); ?></span>
                <h2 class="section-title"><?php esc_html_e('Shop by Confectionery Collection', 'neebites'); ?></h2>
            </div>

            <div class="category-bubbles-grid">
                <?php
                $shop_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/shop');
                $categories = [
                    ['name' => __('Dark Chocolate Almond', 'neebites'), 'img' => 'choc-dark-almond.svg', 'count' => '70% Belgian', 'url' => add_query_arg('product_cat', 'dark-chocolate-almond', $shop_url)],
                    ['name' => __('Kiwi Chocolate Almond', 'neebites'), 'img' => 'choc-kiwi-almond.svg', 'count' => 'Signature Fruit', 'url' => add_query_arg('product_cat', 'kiwi-chocolate-almond', $shop_url)],
                    ['name' => __('Milk Chocolate Almond', 'neebites'), 'img' => 'choc-milk-almond.svg', 'count' => '38% Swiss Milk', 'url' => add_query_arg('product_cat', 'milk-chocolate-almond', $shop_url)],
                    ['name' => __('White Chocolate Almond', 'neebites'), 'img' => 'choc-white-almond.svg', 'count' => 'Bourbon Vanilla', 'url' => add_query_arg('product_cat', 'white-chocolate-almond', $shop_url)],
                    ['name' => __('Matcha Green Tea Almond', 'neebites'), 'img' => 'choc-matcha-almond.svg', 'count' => 'Uji Ceremonial', 'url' => add_query_arg('product_cat', 'other-flavoured-almonds', $shop_url)],
                    ['name' => __('Salted Caramel Almond', 'neebites'), 'img' => 'choc-caramel-almond.svg', 'count' => 'Fleur de Sel', 'url' => add_query_arg('product_cat', 'other-flavoured-almonds', $shop_url)],
                ];

                foreach ($categories as $cat) :
                    $img_src = $theme_uri . '/assets/images/' . $cat['img'];
                ?>
                    <a href="<?php echo esc_url($cat['url']); ?>" class="category-bubble-card">
                        <div class="category-bubble-thumb" style="background:#FAF6F0">
                            <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($cat['name']); ?>" loading="lazy" decoding="async" width="90" height="90" />
                        </div>
                        <h4 class="category-bubble-title"><?php echo esc_html($cat['name']); ?></h4>
                        <span class="category-bubble-count"><?php echo esc_html($cat['count']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 3. Curated Trending Confectionery Drops -->
    <section id="trending-confections" class="sanctuary-trending-section">
        <div class="container">
            <div class="trending-section-header">
                <div>
                    <span class="section-tag"><?php esc_html_e('Fresh From The Roastery', 'neebites'); ?></span>
                    <h2 class="section-title"><?php esc_html_e('Artisan Chocolate Almond Drops', 'neebites'); ?></h2>
                </div>

                <!-- Interactive Filter Tabs -->
                <div class="trending-filter-tabs" role="tablist" aria-label="<?php esc_attr_e('Filter Confections by Flavour', 'neebites'); ?>">
                    <button type="button" class="tab-pill active" data-filter="all" role="tab" aria-selected="true"><?php esc_html_e('All Confections', 'neebites'); ?></button>
                    <button type="button" class="tab-pill" data-filter="dark" role="tab" aria-selected="false"><?php esc_html_e('Dark Chocolate 🍫', 'neebites'); ?></button>
                    <button type="button" class="tab-pill" data-filter="kiwi" role="tab" aria-selected="false"><?php esc_html_e('Kiwi Signature 🥝', 'neebites'); ?></button>
                    <button type="button" class="tab-pill" data-filter="milk,white" role="tab" aria-selected="false"><?php esc_html_e('Milk & White 🥛', 'neebites'); ?></button>
                    <button type="button" class="tab-pill" data-filter="other,gourmet" role="tab" aria-selected="false"><?php esc_html_e('Gourmet Flavours ✨', 'neebites'); ?></button>
                </div>
            </div>

            <!-- Products Loop Grid -->
            <div id="trending-products-wrap" class="trending-products-wrapper">
                <?php
                if (function_exists('woocommerce_product_loop_start') && neebites_is_woocommerce_active()) {
                    $cat_term = get_term_by('slug', 'chocolates-confectionery', 'product_cat');
                    $args = [
                        'post_type'      => 'product',
                        'posts_per_page' => 8,
                        'post_status'    => 'publish',
                        'orderby'        => 'menu_order title',
                        'order'          => 'ASC',
                    ];
                    if ($cat_term && !is_wp_error($cat_term)) {
                        $args['tax_query'] = [
                            [
                                'taxonomy'         => 'product_cat',
                                'field'            => 'term_id',
                                'terms'            => $cat_term->term_id,
                                'include_children' => true,
                            ],
                        ];
                    }
                    $loop = new WP_Query($args);
                    if ($loop->have_posts()) {
                        woocommerce_product_loop_start();
                        while ($loop->have_posts()) {
                            $loop->the_post();
                            wc_get_template_part('content', 'product');
                        }
                        woocommerce_product_loop_end();
                        wp_reset_postdata();
                    } else {
                        echo do_shortcode('[neebites_demo_plants]');
                    }
                } else {
                    echo do_shortcode('[neebites_demo_plants]');
                }
                ?>

                <!-- Next-Level Empty Filter State -->
                <div class="trending-filter-empty" style="display: none;" role="status" aria-live="polite">
                    <div class="filter-empty-icon">🍫</div>
                    <h3 class="filter-empty-title"><?php esc_html_e('No confections found in this selection', 'neebites'); ?></h3>
                    <p class="filter-empty-desc"><?php esc_html_e('Explore our other freshly roasted chocolate almond creations or view the entire catalog.', 'neebites'); ?></p>
                    <button type="button" class="btn btn-outline btn-sm btn-reset-confection-filter">
                        <?php esc_html_e('Show All Confections', 'neebites'); ?>
                    </button>
                </div>
            </div>

            <div class="trending-bottom-cta text-center">
                <a href="<?php echo esc_url($shop_url); ?>" class="btn btn-outline btn-lg">
                    <?php esc_html_e('View Complete Confectionery Catalog (8 Flavours) &rarr;', 'neebites'); ?>
                </a>
            </div>
        </div>
    </section>

    <!-- 4. Chocolate Craft Wisdom & Roasting Feature Grid -->
    <section class="sanctuary-care-wisdom-section">
        <div class="container">
            <div class="care-wisdom-banner">
                <div class="wisdom-text-col">
                    <span class="wisdom-badge"><?php esc_html_e('Artisan Craftsmanship', 'neebites'); ?></span>
                    <h2 class="wisdom-title"><?php esc_html_e('The Alchemy of Pure Cocoa & Roasted Almonds', 'neebites'); ?></h2>
                    <p class="wisdom-desc"><?php esc_html_e('Every batch begins with premium nonpareil almonds roasted to peak aroma, followed by delicate pan-enrobing with single-origin Belgian chocolate.', 'neebites'); ?></p>
                    
                    <div class="wisdom-perks-list">
                        <div class="wisdom-perk">
                            <span class="perk-icon">🍫</span>
                            <div>
                                <strong><?php esc_html_e('100% Pure Cocoa Butter', 'neebites'); ?></strong>
                                <p><?php esc_html_e('We never use palm oil, compound fats, or hydrogenated oils. Just authentic cocoa butter velvet melt.', 'neebites'); ?></p>
                            </div>
                        </div>
                        <div class="wisdom-perk">
                            <span class="perk-icon">🥜</span>
                            <div>
                                <strong><?php esc_html_e('California Nonpareil Almonds', 'neebites'); ?></strong>
                                <p><?php esc_html_e('Uniform, sweet-kernel almonds batch-roasted for signature satisfying snap.', 'neebites'); ?></p>
                            </div>
                        </div>
                        <div class="wisdom-perk">
                            <span class="perk-icon">❄️</span>
                            <div>
                                <strong><?php esc_html_e('Cold-Chain Safe Delivery', 'neebites'); ?></strong>
                                <p><?php esc_html_e('Melt-proof insulated shipping ensures arrival in factory-fresh temper.', 'neebites'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wisdom-visual-col">
                    <div class="wisdom-card-preview">
                        <div class="preview-header">
                            <span class="preview-tag"><?php esc_html_e('Master Chocolatier Card', 'neebites'); ?></span>
                            <span class="preview-status"><?php esc_html_e('Fresh Roast Batch', 'neebites'); ?></span>
                        </div>
                        <div class="preview-plant-row">
                            <img src="<?php echo esc_url($theme_uri . '/assets/images/choc-kiwi-almond.svg'); ?>" alt="Kiwi Chocolate Almond" width="80" height="80" />
                            <div>
                                <h4><?php esc_html_e('Kiwi Chocolate Almond', 'neebites'); ?></h4>
                                <span class="spec-difficulty"><?php esc_html_e('Signature • Real Fruit Glaze', 'neebites'); ?></span>
                            </div>
                        </div>
                        <div class="preview-meters">
                            <div class="meter-row">
                                <span><?php esc_html_e('Cocoa Butter Purity', 'neebites'); ?></span>
                                <div class="meter-bar"><div class="meter-fill" style="width: 100%;"></div></div>
                            </div>
                            <div class="meter-row">
                                <span><?php esc_html_e('Almond Roast Crunch', 'neebites'); ?></span>
                                <div class="meter-bar"><div class="meter-fill" style="width: 95%;"></div></div>
                            </div>
                        </div>
                        <a href="<?php echo esc_url($shop_url); ?>" class="btn btn-primary btn-sm btn-block">
                            <?php esc_html_e('Explore All Flavours &rarr;', 'neebites'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Community Social Proof / Reviews -->
    <section class="sanctuary-reviews-section" aria-label="<?php esc_attr_e('Customer Reviews', 'neebites'); ?>">
        <div class="container">
            <div class="reviews-header-wrap">
                <div class="reviews-title-block">
                    <span class="section-tag"><?php esc_html_e('Loved by 18,000+ Confectionery Lovers', 'neebites'); ?></span>
                    <h2 class="section-title"><?php esc_html_e('From Our Chocolate Connoisseurs', 'neebites'); ?></h2>
                    
                    <!-- Trust Summary Bar -->
                    <div class="reviews-trust-bar">
                        <div class="trust-stars-chip">
                            <span class="trust-stars">★★★★★</span>
                            <span class="trust-numeric">4.9 / 5.0</span>
                        </div>
                        <span class="trust-dot" aria-hidden="true">•</span>
                        <span class="trust-count"><?php esc_html_e('1,840+ Verified Reviews', 'neebites'); ?></span>
                        <span class="trust-dot" aria-hidden="true">•</span>
                        <span class="trust-guarantee">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php esc_html_e('Insulated Cold-Pack Verified', 'neebites'); ?>
                        </span>
                    </div>
                </div>

                <!-- Carousel Controls (Desktop & Mobile) -->
                <div class="reviews-nav-controls">
                    <button type="button" class="btn-reviews-nav btn-reviews-prev" aria-label="<?php esc_attr_e('Previous review', 'neebites'); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <button type="button" class="btn-reviews-nav btn-reviews-next" aria-label="<?php esc_attr_e('Next review', 'neebites'); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
            </div>

            <!-- Single-Line Scrolling Reviews Track on Mobile -->
            <div class="reviews-track-container">
                <div class="reviews-grid" id="connoisseur-reviews-track">
                    <!-- Review 1: Kiwi Signature -->
                    <div class="review-card" data-index="0">
                        <div class="review-card-top">
                            <div class="review-stars-row">
                                <span class="review-stars">★★★★★</span>
                                <span class="review-score-badge">5.0</span>
                            </div>
                            <span class="review-flavor-pill pill-kiwi">
                                <span class="flavor-dot"></span>
                                <?php esc_html_e('Kiwi Signature', 'neebites'); ?>
                            </span>
                        </div>
                        <div class="review-verified-tag">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php esc_html_e('Verified Purchase', 'neebites'); ?>
                        </div>
                        <p class="review-text">"The Kiwi Chocolate Almond is unlike anything I've ever experienced! The tangy fruit glaze combined with the silky white chocolate and roasted almond crunch is an absolute triumph."</p>
                        <div class="review-author">
                            <div class="author-avatar avatar-kiwi">🥝</div>
                            <div class="author-meta">
                                <strong>Emma S.</strong>
                                <small><?php esc_html_e('London, UK • Verified Buyer', 'neebites'); ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Review 2: Dark Chocolate 70% -->
                    <div class="review-card" data-index="1">
                        <div class="review-card-top">
                            <div class="review-stars-row">
                                <span class="review-stars">★★★★★</span>
                                <span class="review-score-badge">5.0</span>
                            </div>
                            <span class="review-flavor-pill pill-dark">
                                <span class="flavor-dot"></span>
                                <?php esc_html_e('70% Belgian Dark', 'neebites'); ?>
                            </span>
                        </div>
                        <div class="review-verified-tag">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php esc_html_e('Verified Purchase', 'neebites'); ?>
                        </div>
                        <p class="review-text">"The 70% Dark Chocolate Almond is perfection. Genuine Belgian cocoa with zero bitterness and roasted almonds that taste like they came straight out of the roaster."</p>
                        <div class="review-author">
                            <div class="author-avatar avatar-dark">🍫</div>
                            <div class="author-meta">
                                <strong>Marcus V.</strong>
                                <small><?php esc_html_e('Geneva, Switzerland • Connoisseur', 'neebites'); ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Review 3: Luxury 8-Flavour Tasting Box -->
                    <div class="review-card" data-index="2">
                        <div class="review-card-top">
                            <div class="review-stars-row">
                                <span class="review-stars">★★★★★</span>
                                <span class="review-score-badge">5.0</span>
                            </div>
                            <span class="review-flavor-pill pill-tasting">
                                <span class="flavor-dot"></span>
                                <?php esc_html_e('8-Drop Tasting Box', 'neebites'); ?>
                            </span>
                        </div>
                        <div class="review-verified-tag">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php esc_html_e('Verified Purchase', 'neebites'); ?>
                        </div>
                        <p class="review-text">"Delivered in scorching summer weather completely solid and cool inside the insulated chill pack. Neebites sets the gold standard for luxury online confections."</p>
                        <div class="review-author">
                            <div class="author-avatar avatar-tasting">✨</div>
                            <div class="author-meta">
                                <strong>Sophie L.</strong>
                                <small><?php esc_html_e('Paris, France • VIP Collector', 'neebites'); ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Review 4: Swiss Velvet Milk -->
                    <div class="review-card" data-index="3">
                        <div class="review-card-top">
                            <div class="review-stars-row">
                                <span class="review-stars">★★★★★</span>
                                <span class="review-score-badge">5.0</span>
                            </div>
                            <span class="review-flavor-pill pill-milk">
                                <span class="flavor-dot"></span>
                                <?php esc_html_e('38% Swiss Velvet', 'neebites'); ?>
                            </span>
                        </div>
                        <div class="review-verified-tag">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php esc_html_e('Verified Purchase', 'neebites'); ?>
                        </div>
                        <p class="review-text">"Incredible texture! The Swiss milk chocolate is so silky and creamy, and the California nonpareil almonds have that fresh roastery aroma. Reordering the family gift box right now."</p>
                        <div class="review-author">
                            <div class="author-avatar avatar-milk">🥛</div>
                            <div class="author-meta">
                                <strong>Kavita M.</strong>
                                <small><?php esc_html_e('Mumbai, India • Verified Buyer', 'neebites'); ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Review 5: Salted Golden Caramel -->
                    <div class="review-card" data-index="4">
                        <div class="review-card-top">
                            <div class="review-stars-row">
                                <span class="review-stars">★★★★★</span>
                                <span class="review-score-badge">5.0</span>
                            </div>
                            <span class="review-flavor-pill pill-caramel">
                                <span class="flavor-dot"></span>
                                <?php esc_html_e('Fleur De Sel Caramel', 'neebites'); ?>
                            </span>
                        </div>
                        <div class="review-verified-tag">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php esc_html_e('Verified Purchase', 'neebites'); ?>
                        </div>
                        <p class="review-text">"The salted French caramel with the crunchy almond center hits that sweet-savory balance that most chocolatiers miss. Melt-in-the-mouth perfection with pure cocoa butter."</p>
                        <div class="review-author">
                            <div class="author-avatar avatar-caramel">🍯</div>
                            <div class="author-meta">
                                <strong>Julian R.</strong>
                                <small><?php esc_html_e('San Francisco, USA • Verified Buyer', 'neebites'); ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Review 6: Ceremonial Uji Matcha -->
                    <div class="review-card" data-index="5">
                        <div class="review-card-top">
                            <div class="review-stars-row">
                                <span class="review-stars">★★★★★</span>
                                <span class="review-score-badge">5.0</span>
                            </div>
                            <span class="review-flavor-pill pill-matcha">
                                <span class="flavor-dot"></span>
                                <?php esc_html_e('Uji Ceremonial Matcha', 'neebites'); ?>
                            </span>
                        </div>
                        <div class="review-verified-tag">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php esc_html_e('Verified Purchase', 'neebites'); ?>
                        </div>
                        <p class="review-text">"As someone deeply particular about authentic matcha, the stone-ground Uji green tea infused with ivory white chocolate is masterful. Elegant, earthy, and perfectly roasted."</p>
                        <div class="review-author">
                            <div class="author-avatar avatar-matcha">🍵</div>
                            <div class="author-meta">
                                <strong>Aiko T.</strong>
                                <small><?php esc_html_e('Kyoto, Japan • Connoisseur', 'neebites'); ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination Dots & Mobile Swipe Indicator -->
                <div class="reviews-bottom-bar">
                    <div class="mobile-swipe-indicator" aria-hidden="true">
                        <span class="swipe-arrow">←</span>
                        <span class="swipe-text"><?php esc_html_e('Swipe Connoisseur Reviews', 'neebites'); ?></span>
                        <span class="swipe-arrow">→</span>
                    </div>
                    <div class="reviews-dots" role="tablist" aria-label="<?php esc_attr_e('Review slides', 'neebites'); ?>">
                        <button type="button" class="review-dot active" data-index="0" role="tab" aria-selected="true" aria-label="Review 1"></button>
                        <button type="button" class="review-dot" data-index="1" role="tab" aria-selected="false" aria-label="Review 2"></button>
                        <button type="button" class="review-dot" data-index="2" role="tab" aria-selected="false" aria-label="Review 3"></button>
                        <button type="button" class="review-dot" data-index="3" role="tab" aria-selected="false" aria-label="Review 4"></button>
                        <button type="button" class="review-dot" data-index="4" role="tab" aria-selected="false" aria-label="Review 5"></button>
                        <button type="button" class="review-dot" data-index="5" role="tab" aria-selected="false" aria-label="Review 6"></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. VIP Chocolate Sanctuary Club -->
    <section class="sanctuary-newsletter-section" aria-label="<?php esc_attr_e('VIP Chocolate Club', 'neebites'); ?>">
        <div class="container">
            <div class="newsletter-card">
                <div class="newsletter-glow-orb orb-left"></div>
                <div class="newsletter-glow-orb orb-right"></div>
                <span class="newsletter-leaf-decor decor-top-left" aria-hidden="true">🍫</span>
                <span class="newsletter-leaf-decor decor-bottom-right" aria-hidden="true">✨</span>

                <div class="newsletter-content">
                    <div class="club-badge-row">
                        <span class="club-badge">✨ <?php esc_html_e('VIP Chocolate Club', 'neebites'); ?></span>
                        <span class="club-stat-pill">🍫 <?php esc_html_e('50,000+ Members', 'neebites'); ?></span>
                    </div>

                    <h2 class="newsletter-title">
                        <?php esc_html_e('Get', 'neebites'); ?> 
                        <span class="title-highlight"><?php esc_html_e('10% Off', 'neebites'); ?></span> 
                        <?php esc_html_e('Your First Chocolate Box', 'neebites'); ?>
                    </h2>

                    <p class="newsletter-subtitle">
                        <?php esc_html_e('Join our society of chocolate lovers. Enjoy exclusive early access to secret batch roasts, seasonal fruit glazes, and private gifting promotions.', 'neebites'); ?>
                    </p>

                    <!-- Feature Perk Chips -->
                    <div class="newsletter-perks-chips">
                        <span class="perk-chip">🎁 <?php esc_html_e('Instant 10% Welcome Code', 'neebites'); ?></span>
                        <span class="perk-chip">🍫 <?php esc_html_e('Early Access to Limited Batches', 'neebites'); ?></span>
                        <span class="perk-chip">❄️ <?php esc_html_e('Free Cold-Pack Insulated Delivery', 'neebites'); ?></span>
                    </div>

                    <!-- Interactive Form (Smooth In-Card Confirmation) -->
                    <div id="newsletter-action-wrap">
                        <form class="newsletter-form" id="sanctuary-newsletter-form" onsubmit="handleNewsletterSubmit(event)">
                            <div class="newsletter-input-group">
                                <span class="newsletter-icon-left" aria-hidden="true">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                </span>
                                <input type="email" id="newsletter-email-field" placeholder="<?php esc_attr_e('Enter your email for instant 10% code...', 'neebites'); ?>" required class="newsletter-input" autocomplete="email" />
                                <button type="submit" class="btn btn-primary btn-newsletter">
                                    <span><?php esc_html_e('Claim 10% Off', 'neebites'); ?></span>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </form>

                        <div id="newsletter-success-state" class="newsletter-success-state" hidden>
                            <div class="success-pill">🎉 <?php esc_html_e('Welcome to the Chocolate Club!', 'neebites'); ?></div>
                            <h3 class="success-heading"><?php esc_html_e('Your 10% Voucher Code is Active', 'neebites'); ?></h3>
                            <div class="coupon-box" onclick="copyNewsletterCode(this)" title="<?php esc_attr_e('Click to copy code', 'neebites'); ?>">
                                <code id="voucher-code-txt">CHOCO10</code>
                                <span class="copy-badge" id="copy-badge-label"><?php esc_html_e('Tap to Copy 📋', 'neebites'); ?></span>
                            </div>
                            <div class="success-actions">
                                <a href="<?php echo esc_url(home_url('/shop')); ?>" class="btn btn-primary btn-shop-voucher">
                                    <?php esc_html_e('Shop Chocolates with 10% Off →', 'neebites'); ?>
                                </a>
                            </div>
                        </div>

                        <div class="newsletter-trust-row">
                            <span class="trust-pill"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg> <?php esc_html_e('Zero spam. Unsubscribe anytime with 1-click.', 'neebites'); ?></span>
                            <span class="trust-pill"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg> <?php esc_html_e('Instant coupon delivered immediately', 'neebites'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    function handleNewsletterSubmit(e) {
        e.preventDefault();
        var form = document.getElementById('sanctuary-newsletter-form');
        var successState = document.getElementById('newsletter-success-state');
        if (form && successState) {
            form.style.display = 'none';
            successState.hidden = false;
        }
    }
    function copyNewsletterCode(el) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText('CHOCO10').then(function() {
                var badge = document.getElementById('copy-badge-label');
                if (badge) badge.innerText = 'Copied! ✓';
                if (el) el.style.borderColor = '#C59B27';
            });
        }
    }
    </script>

</div>

<?php
get_footer();
