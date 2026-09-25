<?php
/**
 * Single Product Image & Gallery — Myntra-Inspired Luxury E-Commerce Architecture
 *
 * Implements high-converting Myntra PDP Gallery signatures:
 * - 100% Guaranteed Image Visibility (Zero opacity bugs)
 * - Desktop: Pristine Main Hero Stage with smooth hover zoom lens
 * - Desktop: Interactive Myntra Thumbnail Rail with active cocoa border & smooth cross-fade
 * - Mobile: Ultra-smooth touch swipe carousel with scroll snap & photo counter pill (e.g. "1 / 3")
 * - Centered dot pagination indicators
 * - Vibrant floating Sale discount pill badge (-20% OFF / SALE)
 * - Glassmorphism zoom trigger button with fullscreen zero-dependency Lightbox modal
 *
 * @package Neebites
 * @version 2.6.0
 */

defined('ABSPATH') || exit;

global $product;

if (!$product) {
    return;
}

$post_thumbnail_id = $product->get_image_id();
$gallery_image_ids = $product->get_gallery_image_ids();

$all_images = [];
if ($post_thumbnail_id) {
    $all_images[] = (int) $post_thumbnail_id;
}
if (!empty($gallery_image_ids)) {
    foreach ($gallery_image_ids as $gid) {
        $gid = (int) $gid;
        if ($gid && !in_array($gid, $all_images, true)) {
            $all_images[] = $gid;
        }
    }
}

// Build image data structures
$images_data = [];

if (!empty($all_images)) {
    foreach ($all_images as $idx => $img_id) {
        $full_src  = wp_get_attachment_image_url($img_id, 'full');
        $large_src = wp_get_attachment_image_url($img_id, 'woocommerce_single') ?: $full_src;
        $thumb_src = wp_get_attachment_image_url($img_id, 'woocommerce_thumbnail') ?: wp_get_attachment_image_url($img_id, 'thumbnail') ?: $full_src;
        $alt       = get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: $product->get_name();
        
        $images_data[] = [
            'id'    => $img_id,
            'full'  => $full_src ?: $large_src,
            'large' => $large_src,
            'thumb' => $thumb_src,
            'alt'   => $alt,
        ];
    }
} else {
    // Fallback confection artwork for products without uploaded media
    $fallback_url = function_exists('neebites_get_product_fallback_image')
        ? neebites_get_product_fallback_image($product)
        : get_template_directory_uri() . '/assets/images/products/choc-dark-almond.jpg';
    
    $images_data[] = [
        'id'    => 0,
        'full'  => $fallback_url,
        'large' => $fallback_url,
        'thumb' => $fallback_url,
        'alt'   => $product->get_name(),
    ];
}

$total_images = count($images_data);

// Calculate sale discount percentage
$discount_label = __('SALE', 'neebites');
if ($product->is_on_sale()) {
    $regular = (float) $product->get_regular_price();
    $sale    = (float) $product->get_price();
    if ($regular > 0 && $regular > $sale) {
        $pct = round((($regular - $sale) / $regular) * 100);
        $discount_label = sprintf(__('%d%% OFF', 'neebites'), $pct);
    }
}
?>

<div class="myntra-gallery-container woocommerce-product-gallery images" data-columns="4" data-total-images="<?php echo esc_attr($total_images); ?>" style="opacity: 1 !important;">

    <!-- 1. Floating Top Badges -->
    <div class="myntra-gallery-top-bar">
        <?php if ($product->is_on_sale()) : ?>
            <span class="myntra-gallery-badge-sale">
                <?php echo esc_html($discount_label); ?>
            </span>
        <?php endif; ?>

        <button type="button" class="myntra-gallery-zoom-btn" id="myntra-gallery-zoom-trigger" aria-label="<?php esc_attr_e('View fullscreen image', 'neebites'); ?>" title="<?php esc_attr_e('Click to zoom', 'neebites'); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                <line x1="11" y1="8" x2="11" y2="14"></line>
                <line x1="8" y1="11" x2="14" y2="11"></line>
            </svg>
        </button>
    </div>

    <!-- 2. Main Stage Viewport (Desktop Active Display & Mobile Touch-Swipe Track) -->
    <div class="myntra-main-stage" id="myntra-main-stage">
        <div class="myntra-slides-track" id="myntra-slides-track">
            <?php foreach ($images_data as $i => $img) : ?>
                <div class="myntra-slide-item <?php echo $i === 0 ? 'is-active' : ''; ?>" data-slide-index="<?php echo esc_attr($i); ?>" data-full-image="<?php echo esc_url($img['full']); ?>">
                    <div class="myntra-img-wrap">
                        <img 
                            src="<?php echo esc_url($img['large']); ?>" 
                            data-full="<?php echo esc_url($img['full']); ?>"
                            alt="<?php echo esc_attr($img['alt']); ?>" 
                            class="myntra-gallery-img <?php echo $i === 0 ? 'wp-post-image main-featured-img' : ''; ?>" 
                            loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>"
                            decoding="async"
                            <?php if ($i === 0) : ?>fetchpriority="high"<?php endif; ?>
                        />
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Floating Mobile / Quick Photo Counter Pill (e.g. "1 / 3") -->
        <?php if ($total_images > 1) : ?>
            <div class="myntra-counter-pill" id="myntra-counter-pill">
                <span class="current-slide">1</span> / <span class="total-slides"><?php echo esc_html($total_images); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- 3. Desktop & Mobile Pagination Indicator Dots -->
    <?php if ($total_images > 1) : ?>
        <div class="myntra-dots-indicator" id="myntra-dots-indicator" role="tablist" aria-label="<?php esc_attr_e('Image gallery navigation', 'neebites'); ?>">
            <?php for ($d = 0; $d < $total_images; $d++) : ?>
                <button type="button" 
                    class="myntra-dot <?php echo $d === 0 ? 'is-active' : ''; ?>" 
                    data-dot-index="<?php echo esc_attr($d); ?>" 
                    aria-label="<?php echo esc_attr(sprintf(__('Go to photo %d', 'neebites'), $d + 1)); ?>"
                    role="tab">
                </button>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

    <!-- 4. Desktop Myntra Interactive Thumbnails Rail -->
    <?php if ($total_images > 1) : ?>
        <div class="myntra-thumbs-track" id="myntra-thumbs-track" role="tablist" aria-label="<?php esc_attr_e('Product photo thumbnails', 'neebites'); ?>">
            <?php foreach ($images_data as $i => $img) : ?>
                <button type="button" 
                    class="myntra-thumb-card <?php echo $i === 0 ? 'is-active' : ''; ?>" 
                    data-thumb-index="<?php echo esc_attr($i); ?>"
                    aria-label="<?php echo esc_attr(sprintf(__('Select photo %d', 'neebites'), $i + 1)); ?>"
                    role="tab">
                    <img src="<?php echo esc_url($img['thumb']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy" />
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<!-- 5. Zero-Dependency Luxury Fullscreen Lightbox Modal -->
<div class="myntra-gallery-modal" id="myntra-gallery-modal" aria-hidden="true" role="dialog" aria-label="<?php esc_attr_e('Image zoom view', 'neebites'); ?>">
    <div class="myntra-modal-backdrop" id="myntra-modal-backdrop"></div>
    <div class="myntra-modal-dialog">
        <button type="button" class="myntra-modal-close" id="myntra-modal-close" aria-label="<?php esc_attr_e('Close fullscreen view', 'neebites'); ?>">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <?php if ($total_images > 1) : ?>
            <button type="button" class="myntra-modal-nav myntra-modal-prev" id="myntra-modal-prev" aria-label="<?php esc_attr_e('Previous photo', 'neebites'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <button type="button" class="myntra-modal-nav myntra-modal-next" id="myntra-modal-next" aria-label="<?php esc_attr_e('Next photo', 'neebites'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        <?php endif; ?>

        <div class="myntra-modal-content">
            <img src="<?php echo esc_url($images_data[0]['full']); ?>" id="myntra-modal-img" alt="<?php echo esc_attr($images_data[0]['alt']); ?>" />
            <div class="myntra-modal-caption">
                <span id="myntra-modal-counter">1 / <?php echo esc_html($total_images); ?></span>
                <span class="myntra-modal-title"><?php echo esc_html($product->get_name()); ?></span>
            </div>
        </div>
    </div>
</div>
