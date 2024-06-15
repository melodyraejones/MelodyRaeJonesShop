<?php

get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$weeklyZen = new WP_Query(array(
    'posts_per_page' => 8, // Number of posts per page
    'post_type' => 'weeklyZen',
    'paged' => $paged
));

echo '<div id="content" class="site-content">';
if ($weeklyZen->have_posts()) {
    echo '<h2 class="weekly-zen-heading">Weekly Zens</h2>';
    echo '<div class="weekly-zen-archive">';
    while ($weeklyZen->have_posts()) {
        $weeklyZen->the_post(); ?>
        <div class="weekly-zen-post">
            <div class="post-header">
                <div class="post-date">
                    <i class="fas fa-clock"></i> <?php the_time('F j, Y'); ?>
                </div>
                <h2 class="post-title"><?php the_title(); ?></h2>
            </div>
            <div class="post-content">
                <?php the_excerpt(); ?>
                <a class="read-more" href="<?php the_permalink(); ?>">Read More &rarr;</a>
            </div>
        </div>
        <?php
    }
    echo '</div>';

    // Pagination
    $big = 999999999; // need an unlikely integer
    echo '<div class="pagination">';
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $weeklyZen->max_num_pages
    ));
    echo '</div>';

} else {
    echo '<p>No posts found.</p>';
}
echo '</div>';

get_footer();
