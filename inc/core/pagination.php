<?php
/**
 * Pagination functions
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Render clean accessible numeric pagination
 */
function neebites_pagination($query = null) {
    if (!$query) {
        global $wp_query;
        $query = $wp_query;
    }

    $total_pages = $query->max_num_pages;
    if ($total_pages <= 1) {
        return;
    }

    $current_page = max(1, get_query_var('paged'), get_query_var('page'));

    $links = paginate_links([
        'total'     => $total_pages,
        'current'   => $current_page,
        'prev_text' => '<span class="pagination-arrow" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg></span><span class="screen-reader-text">' . esc_html__('Previous page', 'neebites') . '</span>',
        'next_text' => '<span class="screen-reader-text">' . esc_html__('Next page', 'neebites') . '</span><span class="pagination-arrow" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg></span>',
        'type'      => 'array',
        'end_size'  => 2,
        'mid_size'  => 1,
    ]);

    if (!empty($links)) {
        echo '<nav class="neebites-pagination" aria-label="' . esc_attr__('Pagination', 'neebites') . '">';
        echo '<ul class="pagination-list">';
        foreach ($links as $link) {
            $active = strpos($link, 'current') !== false ? ' active' : '';
            echo '<li class="pagination-item' . $active . '">' . $link . '</li>';
        }
        echo '</ul>';
        echo '</nav>';
    }
}
