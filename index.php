<?php get_header(); ?>
<div class="blog-wrapper"><div class="container"><div class="blog-content">
    <?php if (is_home() && !is_front_page()) neebites_page_header(single_post_title('', false)); ?>
    <div class="posts-grid">
        <?php while (have_posts()) : the_post(); get_template_part('templates/parts/content', get_post_type()); endwhile; ?>
    </div>
    <?php neebites_pagination(); ?>
</div><?php get_sidebar(); ?></div></div>
<?php get_footer(); ?>
