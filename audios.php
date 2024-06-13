<?php
/*
Template Name: Audios Listing
*/

get_header();

if (is_user_logged_in()) {
    global $wpdb;
    $current_user_id = get_current_user_id();

    // Fetch the program IDs that have access granted for the current user from the custom table
    $granted_access_program_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT program_id FROM {$wpdb->prefix}user_program_access WHERE user_id = %d AND access_granted = 1",
        $current_user_id
    ));


    // Fetch the ID of "The Expand Your Wisdom Toolkit" page
    $wisdom_toolkit_id = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'page' AND post_status = 'publish'",
        'The Expand Your Wisdom Toolkit'
    ));


    $is_wisdom_toolkit_purchased = in_array($wisdom_toolkit_id, $granted_access_program_ids);

  

    if (!empty($granted_access_program_ids)) {
        // Query audio posts that have a related program which the user has access to
        $allAudios = new WP_Query(array(
            'posts_per_page' => -1,
            'post_type' => 'audio',
            'post_status' => array('publish', 'private', 'acf-disabled')
        ));

        if ($allAudios->have_posts()) {
            echo '<section class="section-program-audios">';
            echo '<div class="container center-text">';
            echo '<span class="subheading audio-sub">Your Playlist</span><h2 class="audio-heading">Dive Into Your Exclusive Audios!</h2>';
            echo '<div class="container grid grid--3-cols margin-bottom-md">';

            while ($allAudios->have_posts()) {
                $allAudios->the_post();
                $related_programs = get_field('related_programs', get_the_ID());

                if ($related_programs && is_array($related_programs)) {
                    foreach ($related_programs as $related_program) {
                        if (in_array($related_program->ID, $granted_access_program_ids)) {
                            $program_image_url = get_the_post_thumbnail_url($related_program->ID, 'program-img') ?: get_stylesheet_directory_uri() . '/images/path_to_default_image.jpg';

                            echo "<div class='audio-file'>";
                            echo "<img src='" . esc_url($program_image_url) . "' class='program-img' alt='Program Image'/>";
                            echo "<div class='card-content'>";
                            echo "<p class='audio-title'>" . get_the_title() . "</p>";
                            echo "<a class='audio-link btn btn--full details' href='" . get_the_permalink() . "'>Listen &rarr;</a>";
                            echo "</div></div>";  // Close card-content and audio-file divs
                            break;  // Exit the loop after finding the first match
                        }
                    }
                }
            }

            // Add the card for "Expand Your Wisdom Toolkit" if purchased
            if ($is_wisdom_toolkit_purchased) {
                $toolkit_image_url = get_the_post_thumbnail_url($wisdom_toolkit_id, 'full') ?: get_stylesheet_directory_uri() . '/images/path_to_default_image.jpg';
                echo "<div class='audio-file wisdom-file'>";
                echo "<img src='" . esc_url($toolkit_image_url) . "' class='program-img wisdom-img' alt='Expand Your Wisdom Toolkit'/>";
                echo "<div class='card-content'>";
                echo "<p class='audio-title'>Expand Your Wisdom Toolkit</p>";
                echo "<a class='audio-link btn btn--full details' href='" . home_url('/wisdom-toolkit-modules/') . "'>Access Toolkit &rarr;</a>";
                echo "</div></div>";  // Close card-content and audio-file divs
            }

            echo '</div>';  // Close grid

            // Link to the home page
            echo '<hr>';
            echo '<div class="all-programs">';
            echo '<a href="' . home_url() . '" class="btn btn--full btn-explore">Explore Programs</a>';
            echo '</div>';  // Close center-text div

            echo '</section>';  // Close section-program-audios
        } else {
            echo '<section class="section-program-audios">';
            echo '<div class="no-program-access">';
            echo '<p class="no-access-audios">You do not have access to any audio programs at this time. <i class="fas fa-sad-tear"></i></p>';
            echo '<p class="meditation-message">Take a deep breath, relax, and explore more programs to find your perfect meditation journey.</p>';
            echo '<a href="' . home_url() . '" class="btn btn--full btn-no-program">Explore Programs</a>';
            echo '</div>';
            echo '</section>';
        }
        wp_reset_postdata();
    } else {
        echo '<section class="section-program-audios">';
        echo '<div class="no-program-access">';
        echo '<p class="no-access-audios">You do not have access to any audio programs at this time. <i class="fas fa-sad-tear"></i></p>';
        echo '<p class="meditation-message">Take a deep breath, relax, and explore more programs to find your perfect meditation journey.</p>';
        echo '<a href="' . home_url() . '" class="btn btn--full btn-no-program">Explore Programs</a>';
        echo '</div>';
        echo '</section>';
    }
} else {
    echo '<p>You must be logged in to view this content.</p>';
}

get_footer();
?>
