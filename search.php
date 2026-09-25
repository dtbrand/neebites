<?php
/**
 * Search Results Template
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

get_header();

global $wp_query;
$found_count = $wp_query ? $wp_query->found_posts : 0;
$subtitle    = sprintf(_n('%d confection found', '%d confections found', $found_count, 'neebites'), $found_count);

neebites_page_header(sprintf(esc_html__('Search Results for: "%s"', 'neebites'), esc_html(get_search_query())), $subtitle);
?>

<div class="search-wrapper">
    <div class="container">
        <div class="search-inner-layout">
            <div class="search-main-content">
                <?php if (have_posts()) : ?>
                    <div class="search-results-grid">
                        <?php
                        while (have_posts()) :
                            the_post();
                            get_template_part('templates/parts/content', 'search');
                        endwhile;
                        ?>
                    </div>
                    <?php neebites_pagination(); ?>
                <?php else : ?>
                    <?php get_template_part('templates/parts/content', 'none'); ?>
                <?php endif; ?>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php
get_footer();
