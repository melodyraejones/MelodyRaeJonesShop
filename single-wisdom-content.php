<?php
/*
Template Name: Single Wisdom Toolkit Content
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

    // Fetch the IDs of "The Expand Your Wisdom Toolkit" and "The Expand Your Wisdom Toolkit Discounted" pages
    $wisdom_toolkit_id = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'page' AND post_status = 'publish'",
        'The Expand Your Wisdom Toolkit'
    ));

    $wisdom_toolkit_discounted_id = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'page' AND post_status = 'publish'",
        'The Expand Your Wisdom Toolkit Discounted'
    ));

    // Check if the user has access to either the full or discounted version
    $wisdom_toolkit_access_granted = in_array($wisdom_toolkit_id, $granted_access_program_ids) || in_array($wisdom_toolkit_discounted_id, $granted_access_program_ids);

    if ($wisdom_toolkit_access_granted) {
        ?>
        <div class="module-container">
            <h1 class="main-heading content-heading">The Expand Your Wisdom Toolkit – Online Program</h1>

            <?php
            // Fetch the current post
            if (have_posts()) :
                while (have_posts()) : the_post();
                    // Get current post ID
                    $current_post_id = get_the_ID();

                    // Fetch all posts in this custom post type
                    $all_posts = new WP_Query(array(
                        'posts_per_page' => -1,
                        'post_type' => 'wisdom-content', // Ensure correct post type
                        'order' => 'ASC'
                    ));

                    $post_ids = wp_list_pluck($all_posts->posts, 'ID');
                    wp_reset_postdata();

                    // Get the current post index
                    $current_index = array_search($current_post_id, $post_ids);

                    // Determine the previous and next post IDs
                    $prev_post_id = ($current_index > 0) ? $post_ids[$current_index - 1] : null;
                    $next_post_id = ($current_index < count($post_ids) - 1) ? $post_ids[$current_index + 1] : null;

                    // Check if the current post is Module 6
                    $is_module_6 = get_the_title() === 'Module 6: In Conclusion';
                    ?>

                    <?php if ($is_module_6) : ?>
                        <div class="card_bkg">
                            <div class="card_box" style="background:#EDEBEC">
                                <div class="card_cont">
                                    <div align="center"><?php the_post_thumbnail(); ?></div>
                                    <br>
                                    <br>
                                    <p>You have within you two guiding forces – your logic and your intuition.</p>
                                    <p><strong>Logic serves to tell you what has been.</strong> It is your connection to the present moment and uses past experience as a guide to determine what makes sense.</p>
                                    <p>In contrast, <strong>intuition is a powerful force that shows you what could be.</strong> It is your connection to the potential and possibility that exists beyond your current circumstances.</p>
                                    <p><em><strong>While logic serves a very powerful role, it cannot understand intuition.</strong></em></p>
                                    <p>Does this sound familiar? You get an intuitive hit, say a gut feeling or a sense of knowing, and you run it through your logic. Since your logic cannot understand it, from a logical perspective your intuitive idea may seem irrational, silly, or simply wrong… and following it, downright scary. I call this <strong>logicizing</strong>.</p>
                                    <p>Logicizing is where you try to understand new possibilities (your intuition) using the part of you that manages your present reality (logic). It just doesn’t work. And yet for many, not taking a chance to follow your intuitive wisdom, becomes dependent on logically understanding it first.</p>
                                    <p>Now I’m going to offer a suggestion that may feel illogical (literally!). Next time you have an intuitive hit that involves taking action or making a change that you can do without too much risk or worry, <strong>give yourself permission to act on it, without judgement or attachment to the outcome</strong>. Allow yourself to follow it even if it doesn't make logical sense.</p>
                                    <p>Does that sound scary – good! That means you are willing to <strong>challenge your own perspective</strong> and change your old story… and that is where powerful change comes from.</p>
                                    <p><strong>I wish to remind you that you are incredibly wise and intuitive!</strong> Even if you have forgotten, or wish you trusted it more often, your link to your intuitive wisdom is alive and well within you, just waiting for opportunities to be allowed and acted upon.</p>
                                    <p>Many Blessings…</p>
                                    <img src="<?php echo get_theme_file_uri('./images/logo_signature.png'); ?>" alt="signature">
                                </div><!--End: Card Cont-->

                                <div class="card_foot">
                                    <div style="font-size:12px">To view this e-Card online click <a href="https://melodyraejones.com/ecards/quotes/intuition.html" target="_blank" class="links1">here</a>.</div>
                                </div><!--End: Card Foot-->
                            </div><!--End: Card Box-->
                        </div>

                        <?php if (get_field('module_audio')) : ?>
                            <div class="file-item">
                                <div class="p5"><?php the_field('module_title'); ?></div>
                                <a class="theme-btn btn-style-six" href="<?php echo esc_url(get_field('module_audio')['url']); ?>" class="btn btn--full" target="_blank"><?php echo esc_html(get_field('module_title')); ?></a>
                            </div>
                            <div class="module-description">
                                <?php the_content(); ?>
                            </div>
                        <?php endif; ?>

                    <?php else : ?>
                        <div class="module-content">
                            <h2 class="module-title"><?php the_title(); ?></h2>
                            <div class="module-thumbnail">
                                <?php the_post_thumbnail(); ?>
                            </div>
                            <div class="module-files">
                                <?php for ($i = 0; $i <= 1; $i++) : // Loop to handle multiple files ?>
                                    <?php
                                    $audio_field = get_field('module_audio' . ($i ? "_$i" : ''));
                                    $audio_title = get_field('module_title' . ($i ? "_$i" : ''));
                                    $pdf_field = get_field('module_file_pdf' . ($i ? "_$i" : ''));
                                    $pdf_name = get_field('module_file_name' . ($i ? "_$i" : ''));
                                    ?>
                                    <?php if ($audio_field) : ?>
                                        <div class="file-item">
                                            <div class="p5"><?php echo esc_html($audio_title); ?></div>
                                            <a class="theme-btn btn-style-six" href="<?php echo esc_url($audio_field['url']); ?>" class="btn btn--full" target="_blank"><?php echo esc_html($audio_title); ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($pdf_field) : ?>
                                        <div class="file-item">
                                            <div class="p5"><?php echo esc_html($pdf_name); ?></div>
                                            <a class="theme-btn btn-style-six" href="<?php echo esc_url($pdf_field['url']); ?>" class="btn btn--full" target="_blank"><?php echo esc_html($pdf_name); ?></a>
                                        </div>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <div class="module-description">
                                <?php the_content(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>No content found</p>';
            endif;
            ?>
        </div>
        <div style="height:12px;border-top:#ccc 1px solid;clear:both;margin-top:7px;"></div>
        <div class="service-detail-section">
            <div class="auto-container">
                <div class="mixitup-gallery" style="margin-bottom:20px">
                    <div class="filters text-center clearfix">
                        <ul class="filter-tabs filter-btns clearfix">
                            <?php if ($prev_post_id) : ?>
                                <li class="filter" data-role="button">
                                    <a class="module-link" href="<?php echo get_permalink($prev_post_id); ?>" style="color: #fff">Module <?php echo $current_index; ?> &lt;&lt;</a>
                                </li>
                            <?php endif; ?>
                            <li class="filter" data-role="button">
                                <a class="module-link" href="<?php echo home_url('/'); ?>" style="color: #fff">Main Page</a>
                            </li>
                            <?php if ($next_post_id) : ?>
                                <li class="filter" data-role="button">
                                    <a class="module-link" href="<?php echo get_permalink($next_post_id); ?>" style="color: #fff">Module <?php echo $current_index + 2; ?> &gt;&gt;</a>
                                </li>
                            <?php endif; ?>
                        </ul>                            
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo '<p class="center-text no-access-audios">You do not have access to this content at this time.</p>';
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
