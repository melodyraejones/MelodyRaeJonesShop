<?php
/*
Template Name: Wisdom Toolkit
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

    // Fetch the IDs of "The Expand Your Wisdom Toolkit" and "The Expand Your Wisdom Offer" pages
    $wisdom_toolkit_id = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'page' AND post_status = 'publish'",
        'The Expand Your Wisdom Toolkit'
    ));

    $wisdom_toolkit_discounted_id = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'page' AND post_status = 'publish'",
        'The Expand Your Wisdom Offer'
    ));

    // Check if the user has access to either the full or discounted version
    $wisdom_toolkit_access_granted = in_array($wisdom_toolkit_id, $granted_access_program_ids) || in_array($wisdom_toolkit_discounted_id, $granted_access_program_ids);

    if ($wisdom_toolkit_access_granted) {
        // Query posts from the custom post type 'Expand Your Wisdom Toolkit Content'
        $wisdom_toolkit_posts = new WP_Query(array(
            'posts_per_page' => -1,
            'post_type' => 'wisdom-content',
            'post_status' => array('publish', 'private', 'acf-disabled'),
            'order' => 'ASC'
        ));

        if ($wisdom_toolkit_posts->have_posts()) {
            echo '<section class="section-program-audios service-detail-section background-tool"><div class="container center-text"><h2 class="wisdom-heading" style="margin-bottom:15px">Welcome!</h2>';
            echo '<div class="text" style="padding-bottom:12px">
            <p>Expanding your connection to your intuition is one of the most powerful and foundational things that you can do for yourself. It allows you to re-create yourself each and every day to include intuitively-guided choices and actions that support the highest view for you and your life.<br><br>
            It also provides a strong foundation for making decisions that more closely align with your heart, soul and life purpose, thereby allowing you to increase your personal effectiveness within all areas of your life.<br><br>
            Your relationship with your Inner Wisdom is one to be cultivated daily. Just as any skill or task becomes sharper with regular use, recognizing and following the patterns in which your intuition comes to you, allows you to increase your ability to receive and trust your intuitive guidance.<br><br>
            I encourage you to be gentle with yourself as you undertake this divine expansion of your Inner Wisdom… you are weaving a deeper relationship with your True Nature, as you increase your ability to be divinely guided.</p>
            </div>';
            echo '<div class="hilite3" style="margin-bottom:25px">
            <div class="text">
            <p><span class="p2"><strong>Instructions:</strong></span><br>
            This program has been divided into 6 modules that you can move through at your own pace. Spend as much time as you need with each module to ensure that you have completed and integrated its information before moving on. The modules have been designed to build on each other, allowing you to become more comfortable with recognizing and trusting your intuitive guidance as you go along. I hope you enjoy this exploration and expansion of your Inner Wisdom.</p>
            </div><!--End: text-->
            </div><!--End: hilite box-->
            </div>';
          
            echo '<div class="container grid grid--3-cols margin-bottom-md">';

            $index = 1; // Initialize the index
            while ($wisdom_toolkit_posts->have_posts()) {
                $wisdom_toolkit_posts->the_post();

                // Get the custom ACF field for the image
                $program_image = get_field('expand_wisdom_toolkit_image');
                $program_image_url = $program_image ? esc_url($program_image['url']) : get_stylesheet_directory_uri() . '/images/path_to_default_image.jpg';
                $toolkit_link = get_permalink(); // Get the permalink for the module page
                
                echo "<div class='audio-file'>";
                echo "<img src='" . esc_url($program_image_url) . "' class='program-img' alt='Program Image'/>";
                echo "<div class='card-content'>";
                echo "<p class='audio-title'>" . get_the_title() . "</p>";
                echo "<a class='audio-link btn btn--full details module-align-center' href='" . esc_url($toolkit_link) . "'> Module  $index</a>";
                echo "</div></div>";  // Close card-content and audio-file divs

                $index++; // Increment the index
            }

            echo '</div>';  // Close grid

            // Link to the home page
            echo '<hr>';
            echo '<div class="all-programs" style="text-align: center; margin: 20px 0;">';
            echo '<a href="' . home_url() . '" class="btn btn--full btn-explore" style="max-width: 200px; display: inline-block;">Explore Programs</a>';
            echo '</div>';  // Center the button
            echo '</div>';  // Close center-text div

            echo '</section>';  // Close section-program-audios
        } else {
            echo '<p class="center-text">No audio files are currently available. Please check back later or explore other programs.</p>';
            echo '<div class="center-text"><a href="' . home_url() . '" class="btn btn--full">Explore Home</a></div>';
        }
        wp_reset_postdata();
    } else {
        echo '<p class="center-text no-access-audios">You do not have access to any audio programs at this time.</p>';
        echo '<div class="no-program-access">';
        echo '<a href="' . home_url() . '" class="btn btn--full btn-no-program">Explore Programs</a>';
        echo '</div>';
        echo '<hr>';
    }
} else {
    echo '<p class="center-text no-access-audios-login">You must be logged in to view this content.</p>';
    echo '<div class="no-program-access">';
    echo '<a href="' . wp_login_url() . '" class="btn btn--full btn-no-program">Login</a>';
    echo '</div>';
    echo '<hr>';
}
get_footer();
?>
