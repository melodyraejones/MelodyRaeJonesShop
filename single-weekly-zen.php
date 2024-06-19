<?php
/*
Template Name: Single Weekly Zen
*/

get_header();

$weekly_zen_page = get_page_by_title('Weekly Zen'); 
$weekly_zen_page_id = $weekly_zen_page ? $weekly_zen_page->ID : 0;

// Fetch the featured image URL of the "Weekly Zen" page
$featured_image_url = get_the_post_thumbnail_url($weekly_zen_page_id, 'full');

if (have_posts()) :
    while (have_posts()) : the_post(); ?>
        <div class="weekly-zen-post-single">
            <div class="image-container">
                <img src="<?php echo get_theme_file_uri('/images/mel_weekly.png'); ?>" class="background-image" alt="Weekly Zen Background">
                <h1 class="post-title"><?php the_title(); ?></h1>
                <?php if ($featured_image_url) : ?>
                    <img src="<?php echo esc_url($featured_image_url); ?>" class="floating-image" alt="Featured Image">
                <?php endif; ?>
            </div>

            <div class="post-content-single">
                <?php the_content(); ?>
            </div>
            <?php
            $weekly_recording = get_field('weekly_recording');
            if ($weekly_recording) : ?>
                <div class="weekly-recording">
                    <audio controls id="weekly-audio">
                        <source src="<?php echo esc_url($weekly_recording['url']); ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const audio = document.getElementById('weekly-audio');
                        const audioClone = audio.cloneNode(true);
                        audioClone.controlsList = "nodownload";
                        audio.parentNode.replaceChild(audioClone, audio);
                    });
                </script>
            <?php else : ?>
                <p>No recording found.</p>
            <?php endif; ?>
        </div>
    <?php
    endwhile;
else :
    echo '<p>No content found</p>';
endif;

get_footer();
?>
