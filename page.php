<?php 
get_header(); 

while (have_posts()) : the_post(); 
    $header = get_post_meta(get_the_ID(), '_neebites_page_header', true); 
    if ($header !== 'none' && !is_front_page()) { 
        $subtitle = get_post_meta(get_the_ID(), '_neebites_page_subtitle', true); 
        neebites_page_header(get_the_title(), $subtitle); 
    } 
?>
<div class="page-wrapper<?php echo is_front_page() ? ' front-page-wrapper' : ''; ?>">
    <div class="<?php echo is_front_page() ? 'full-width-container' : 'container'; ?>">
        <div class="page-content">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="entry-content">
                    <?php 
                    the_content(); 
                    wp_link_pages(['before' => '<div class="page-links">' . esc_html__('Pages:', 'neebites'), 'after' => '</div>']); 
                    ?>
                </div>
            </article>
            <?php if (comments_open() || get_comments_number()) comments_template(); ?>
        </div>
    </div>
</div>
<?php 
endwhile; 
get_footer(); 
?>
