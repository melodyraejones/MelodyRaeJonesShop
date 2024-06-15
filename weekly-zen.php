<?php
/*
Template Name: Weekly Zen 
*/

get_header();

$weeklyZen = new WP_Query(array(
    'posts_per_page' => 3,
    'post_type' => 'weeklyZen'
));

echo '<div id="content" class="site-content">';
if ($weeklyZen->have_posts()) {
    ?><h2 class="weekly-zen-heading">Weekly Zens</h2> 
    <?php echo '<div class="weekly-zen-container">'; 
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
                <?php echo wp_trim_words(get_the_content(), 10); ?>
                <a class="read-more" href="<?php the_permalink(); ?>">Read More &rarr;</a>
            </div>
        </div>
        <?php
    }
    echo '</div>';
    ?> 
    <p class="view-all">
        <!-- <a href="<?php echo home_url('/weekly-zens/'); ?>" class="view-all-link">View All</a> -->
        <a href="https://melodyraejones.com/shop/weekly-zens" class="view-all-link">View All</a>
    </p>
    <?php
} else {
    echo '<p>No posts found.</p>';
}
echo '</div>';

get_footer();
?>
