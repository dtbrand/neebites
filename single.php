<?php get_header(); while (have_posts()) : the_post(); $header = get_post_meta(get_the_ID(), '_neebites_post_header', true); if ($header !== 'none') neebites_page_header(get_the_title(), get_the_excerpt()); ?>
<div class="single-post-wrapper"><div class="container"><div class="single-post-content">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
            <?php $cats = get_the_category(); if ($cats) : ?><div class="entry-categories"><?php foreach ($cats as $cat) echo '<a href="' . get_category_link($cat) . '" class="entry-category">' . $cat->name . '</a>'; ?></div><?php endif; ?>
            <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
            <div class="entry-meta"><span class="entry-author">By <?php the_author_posts_link(); ?></span><span class="entry-date"><time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time></span><span class="entry-read-time"><?php echo neebites_get_read_time(); ?></span></div>
        </header>
        <?php if (has_post_thumbnail()) : ?><div class="entry-featured-image"><?php the_post_thumbnail('neebites-blog', ['loading' => 'eager', 'fetchpriority' => 'high']); ?></div><?php endif; ?>
        <div class="entry-content"><?php the_content(); wp_link_pages(['before' => '<div class="page-links">Pages:', 'after' => '</div>']); ?></div>
        <footer class="entry-footer">
            <?php $tags = get_the_tags(); if ($tags) : ?><div class="entry-tags">Tags: <?php foreach ($tags as $tag) echo '<a href="' . get_tag_link($tag) . '" rel="tag">' . $tag->name . '</a> '; ?></div><?php endif; neebites_social_share(); ?>
        </footer>
    </article>
    <?php neebites_author_box(); neebites_post_navigation(); neebites_related_posts(); if (comments_open() || get_comments_number()) comments_template(); ?>
</div><?php get_sidebar(); ?></div></div>
<?php endwhile; get_footer(); ?>
