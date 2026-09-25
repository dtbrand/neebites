<?php
/**
 * Template part for displaying results in search pages
 *
 * @package Neebites
 * @version 1.1.1
 */

if (!defined('ABSPATH')) exit;

$post_type = get_post_type();
$is_product = ($post_type === 'product');
$product = ($is_product && function_exists('wc_get_product')) ? wc_get_product(get_the_ID()) : null;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('search-result-card'); ?>>
    <div class="search-result-media">
        <a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
            <?php
            if (has_post_thumbnail()) {
                the_post_thumbnail('medium', ['class' => 'search-result-img', 'loading' => 'lazy', 'decoding' => 'async']);
            } elseif ($product) {
                $sku = $product->get_sku();
                $sku_map = [
                    'CHOC-ALM-DARK-01' => 'choc-dark-almond.svg',
                    'CHOC-ALM-KIWI-02' => 'choc-kiwi-almond.svg',
                    'CHOC-ALM-MILK-03' => 'choc-milk-almond.svg',
                    'CHOC-ALM-WHT-04'  => 'choc-white-almond.svg',
                    'CHOC-ALM-MAT-05'  => 'choc-matcha-almond.svg',
                    'CHOC-ALM-CAR-06'  => 'choc-caramel-almond.svg',
                    'CHOC-ALM-CIN-07'  => 'choc-cinnamon-almond.svg',
                    'CHOC-ALM-RUB-08'  => 'choc-ruby-almond.svg',
                    'PLNT-MONST-01'    => 'choc-dark-almond.svg',
                ];
                $img_name = isset($sku_map[$sku]) ? $sku_map[$sku] : 'choc-dark-almond.svg';
                $img_url = get_template_directory_uri() . '/assets/images/' . $img_name;
                printf(
                    '<img src="%s" alt="%s" class="search-result-img" loading="lazy" decoding="async" width="140" height="140" style="object-fit:contain;background:#FAF6F0;padding:8px;border-radius:10px;" />',
                    esc_url($img_url),
                    esc_attr($product->get_name())
                );
            } else {
                $default_img = get_template_directory_uri() . '/assets/images/choc-dark-almond.svg';
                printf(
                    '<img src="%s" alt="%s" class="search-result-img" loading="lazy" decoding="async" width="140" height="140" style="object-fit:contain;background:#FAF6F0;padding:8px;border-radius:10px;" />',
                    esc_url($default_img),
                    esc_attr(get_the_title())
                );
            }
            ?>
        </a>
    </div>

    <div class="search-result-body">
        <header class="search-result-header">
            <span class="search-result-badge"><?php echo $is_product ? esc_html__('Artisan Confection', 'neebites') : esc_html__('Journal Article', 'neebites'); ?></span>
            <h2 class="search-result-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        </header>

        <div class="search-result-content">
            <?php
            if ($product && function_exists('neebites_product_price')) {
                neebites_product_price($product);
            }
            the_excerpt();
            ?>
        </div>

        <footer class="search-result-footer">
            <a href="<?php the_permalink(); ?>" class="btn btn-sm btn-primary">
                <?php echo $is_product ? esc_html__('View Confection Details', 'neebites') : esc_html__('Read Article', 'neebites'); ?>
            </a>
        </footer>
    </div>
</article>
