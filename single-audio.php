<?php
// single-audio.php
get_header();

if (is_user_logged_in()) {
    global $wpdb;
    $current_user_id = get_current_user_id();

    while (have_posts()) : the_post();
        $audio_id = get_the_ID();

        // Fetching the related programs from the ACF field
        $related_programs = get_field('related_programs', $audio_id);
        $program_ids = [];

        // Handling both single and multiple related programs
        if (!empty($related_programs)) {
            if (is_array($related_programs)) {
                // If it's an array, extract all IDs
                foreach ($related_programs as $program) {
                    $program_ids[] = $program->ID;
                }
            } else {
                // If it's a single object, just get its ID
                $program_ids[] = $related_programs->ID;
            }
        }

        // Initialize access check
        $has_access = false;

        // Check access for each related program ID
        foreach ($program_ids as $program_id) {
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}user_program_access WHERE user_id = %d AND program_id = %d AND access_granted = 1",
                $current_user_id, $program_id
            );
            if ($wpdb->get_var($query)) {
                $has_access = true;
                break; // If access is granted for any related program, break the loop
            }
        }

        if ($has_access) {
            // Fetch audio URLs from ACF fields
            $intro_audio_url = get_field('intro_audio');
            $main_audio_url = get_field('audio_file');
            $disclaimer_audio_url = get_field('disclaimer_audio');
            ?>
            <section class="section-audios">
                <article class="audio-content" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                    <div class="container">
                        <div class="music-player">
                        <nav>
                        <div class="circle back" data-url="<?php echo home_url('/audio-files/'); ?>">
                            <span class="dashicons dashicons-arrow-left-alt2"></span>
                        </div>
                        <div class="circle">
                        <span class="dashicons dashicons-download"></span>
                        </div>
                    </nav>
                            <!-- Display and controls for the audio files -->
                            <img class="audio-image" src="<?php echo get_theme_file_uri('./images/checkout.jpg'); ?>" alt="audio-file">
                            <h1><?php the_title(); ?></h1>
                            <p>Melody Rae Jones</p>
                            <?php if ($intro_audio_url): ?>
                                <audio id="intro-audio" >
                                    <source src="<?php echo esc_url($intro_audio_url); ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            <?php endif; ?>
                            <?php if ($main_audio_url): ?>
                                <audio id="main-audio" >
                                    <source src="<?php echo esc_url($main_audio_url); ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            <?php endif; ?>
                            <?php if ($disclaimer_audio_url): ?>
                                <audio id="disclaimer-audio" >
                                    <source src="<?php echo esc_url($disclaimer_audio_url); ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            <?php endif; ?>
                            <input type="range" id="progress" value="0">
                            <div class="controls">
                                <div><span class="dashicons dashicons-controls-back"></span></div>
                                <div class="playIcon"><span class="dashicons dashicons-controls-play" id="ctrlIcon"></span></div>
                                <div><span class="dashicons dashicons-controls-forward"></span></div>
                            </div>
                        </div>
                    </div>
                </article>
            </section>
            <?php
        } else {
            echo '<p class="center-text">You do not have access to this audio. Please check our <a href="/shop">shop page</a> for more options or contact support.</p>';
        }
    endwhile;
} else {
    echo '<p class="center-text">Please <a href="' . wp_login_url(get_permalink()) . '">log in</a> to access this content.</p>';
}

get_footer();
