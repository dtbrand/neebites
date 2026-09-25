<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('neebites-blog', ['loading' => 'lazy']); ?></a>
            <?php $cats = get_the_category(); if ($cats) : ?><div class="post-category"><a href="<?php echo get_category_link($cats[0]); ?>"><?php echo $cats[0]->name; ?></a></div><?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="post-content">
        <header class="post-header">
            <div class="post-meta"><span class="post-date"><time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time></span><span class="post-author">By <?php the_author_posts_link(); ?></span><span class="post-read-time"><?php echo neebites_get_read_time(); ?></span></div>
            <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        </header>
        <div class="post-excerpt"><?php the_excerpt(); ?></div>
        <footer class="post-footer"><a href="<?php the_permalink(); ?>" class="read-more">Read More <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></a></footer>
    </div>
</article>
