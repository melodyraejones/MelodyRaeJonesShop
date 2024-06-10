<?php
/*
Template Name: Mel's Favorites
*/

get_header();


// Query to get Favorite Programs posts
$args = array(
    'post_type' => 'favPrograms',
    'posts_per_page' => -1, // Get all posts
);

$query = new WP_Query($args);

if ($query->have_posts()) :
    while ($query->have_posts()) : $query->the_post();
        // Get related programs using the ACF relationship field
        $related_programs = get_field('related_programs');
        $original_price = get_field('original_price');
        $discounted_price = get_field('discount_price');
        
        // Initialize total audio length
        $total_audio_length_seconds = 0;
        $related_programs_length = count($related_programs);
        $related_programs_data = [];

        if ($related_programs) {
            foreach ($related_programs as $program) {
                $audio_length = get_field('audio_length', $program->ID);

                if ($audio_length) {
                    list($minutes, $seconds) = explode(':', $audio_length);
                    $minutes = intval($minutes);
                    $seconds = intval($seconds);
                    $total_audio_length_seconds += ($minutes * 60) + $seconds;
                }

                $related_programs_data[] = [
                    'id' => $program->ID,
                    'title' => get_the_title($program->ID),
                    'price' => get_field('original_price', $program->ID),
                    'length' => $audio_length
                ];
            }

            if ($related_programs_length > 0) {
                $related_programs_length += 1;
            }
        }

        // Convert total audio length to mm:ss format
        $total_audio_minutes = floor($total_audio_length_seconds / 60);
        $total_audio_seconds = $total_audio_length_seconds % 60;
        $total_audio_length_formatted = sprintf('%02d:%02d', $total_audio_minutes, $total_audio_seconds);
        ?>

        <section class="program-details">
            <div class="header-cart detailed-cart">
                <a href="<?php echo esc_url(home_url('/shop/cart/')); ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">0</span>
                </a>
            </div>
            <div class="back-button">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <i class="fas fa-arrow-left"></i> Back to Homepage
                </a>
            </div>
            <div class="product grid grid--2-cols" data-id="<?php echo get_the_ID(); ?>">
                <div class="product-image-box fav-image-box">
                    <div class="best-value-label">Best Value</div>
                    <img class="product-img fav-img" src="<?php echo get_theme_file_uri('./images/mel_faves.png'); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <p class="product-price">
                        <span class="original-price">$<?php echo esc_html(number_format((float)$original_price, 2)); ?></span>
                        <span class="discounted-price">$<?php echo esc_html(number_format((float)$discounted_price, 2)); ?></span>
                    </p> 
                    <p class="product-availability">Available as a MP3 Download</p>
                    <p class="product-length">Total Length: <?php echo esc_html($total_audio_length_formatted); ?></p> 
                </div>
                <div class="product-details-box">
                    <h1 class="heading-primary">Melody's Faves Meditations</h1>
                    <div class="product-description">
                        <p>Enjoy a package with <?php echo $related_programs_length ?> of my favourite meditations that will support you in infusing more heart, light, love and expansion into your life:</p>
                        <?php if ($related_programs) : ?>
                            <ul class="fav-programs-list">
                                <?php foreach ($related_programs as $program) : ?>
                                    <li>
                                        <a href="<?php echo get_permalink($program->ID); ?>">
                                            <?php echo get_the_title($program->ID); ?>
                                        </a>
                                        (<?php echo get_field('audio_length', $program->ID); ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <p>These meditations are energetically infused to assist you with dissolving old patterns, moving through inner resistance and re-connecting back into your purpose, passion and guidance. Done regularly, they will activate a greater sense of peace, clarity and inner connection – the more you listen, the deeper their impact within you.</p>
                        <?php endif; ?>
                    </div>
                    <a href="#" class="btn btn--full btn-details add_to_cart_details cart-btn" 
                       data-id="<?php echo get_the_ID(); ?>" 
                       data-price="<?php echo esc_attr($discounted_price); ?>"
                       data-related-programs='<?php echo json_encode($related_programs_data); ?>'>Add to Cart</a>
                </div>
            </div>
        </section>

    <?php
    endwhile;
else :
    echo '<p>No favorite programs found.</p>';
endif;

wp_reset_postdata();
get_footer();
?>
