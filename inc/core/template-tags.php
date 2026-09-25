<?php
/**
 * Custom Template Tags
 *
 * @package Neebites
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Prints HTML with meta information for the current post-date/time.
 */
function neebites_posted_on() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if (get_the_time('U') !== get_the_modified_time('U')) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
    }

    $time_string = sprintf(
        $time_string,
        esc_attr(get_the_date(DATE_W3C)),
        esc_html(get_the_date())
    );

    echo '<span class="posted-on"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> ' . $time_string . '</span>';
}

/**
 * Prints HTML with meta information for the author.
 */
function neebites_posted_by() {
    echo '<span class="byline"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> <span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span></span>';
}

/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function neebites_entry_footer() {
    if ('post' === get_post_type()) {
        $categories_list = get_the_category_list(', ');
        if ($categories_list) {
            printf('<span class="cat-links"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg> ' . esc_html__('Posted in %1$s', 'neebites') . '</span>', $categories_list);
        }

        $tags_list = get_the_tag_list('', ', ');
        if ($tags_list) {
            printf('<span class="tags-links"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg> ' . esc_html__('Tagged %1$s', 'neebites') . '</span>', $tags_list);
        }
    }

    if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
        echo '<span class="comments-link"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> ';
        comments_popup_link(
            sprintf(wp_kses(__('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'neebites'), ['span' => ['class' => []]]), wp_kses_post(get_the_title())),
            esc_html__('1 Comment', 'neebites'),
            esc_html__('% Comments', 'neebites')
        );
        echo '</span>';
    }
}

/**
 * Displays an optional post thumbnail.
 */
function neebites_post_thumbnail($size = 'neebites-blog') {
    if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
        return;
    }

    if (is_singular()) :
        ?>
        <div class="post-thumbnail featured-image">
            <?php the_post_thumbnail($size, ['loading' => 'eager', 'fetchpriority' => 'high']); ?>
        </div>
    <?php else : ?>
        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php
            the_post_thumbnail($size, [
                'alt' => the_title_attribute(['echo' => false]),
                'loading' => 'lazy',
            ]);
            ?>
        </a>
    <?php
    endif;
}
