<?php
/*
Template Name: Weekly Zen
*/

get_header();

$weeklyZen = new WP_Query(array(
    'posts_per_page' => 3,
    'post_type' => 'weekly-zen'
));
?>
<div id="content" class="site-content">
    <?php if ($weeklyZen->have_posts()) : ?>
        <h2 class="weekly-zen-heading">Weekly Zens</h2> 
        <div class="weekly-zen-container">
            <?php while ($weeklyZen->have_posts()) : $weeklyZen->the_post(); ?>
                <div class="weekly-zen-post" data-expiration-time="<?php echo strtotime('+48 hours', get_the_time('U')); ?>">
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
                    <div class="countdown-timer"></div>
                </div>
            <?php endwhile; ?>
        </div>
        <p class="view-all">
            <a href="<?php echo home_url('/weekly-zens/'); ?>" class="view-all-link">View All</a>
        </p>
    <?php else : ?>
        <p>No posts found.</p>
    <?php endif; ?>
</div>
<?php
get_footer();
?>
