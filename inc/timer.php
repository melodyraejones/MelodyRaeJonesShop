<?php
// Schedule event to check for expired posts
if (!wp_next_scheduled('delete_expired_weekly_zen_posts')) {
    wp_schedule_event(time(), 'hourly', 'delete_expired_weekly_zen_posts');
}

add_action('delete_expired_weekly_zen_posts', 'delete_expired_weekly_zen_posts');
function delete_expired_weekly_zen_posts() {
    $args = array(
        'post_type' => 'weekly-zen',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_expiration_time',
                'value' => time(),
                'compare' => '<',
                'type' => 'NUMERIC'
            )
        )
    );
    $expired_posts = new WP_Query($args);
    if ($expired_posts->have_posts()) {
        while ($expired_posts->have_posts()) {
            $expired_posts->the_post();
            wp_delete_post(get_the_ID(), true);
        }
    }
    wp_reset_postdata();
}

// Add expiration time meta field on post publish
function add_expiration_time_meta($post_id, $post, $update) {
    if ($post->post_type == 'weekly-zen' && $post->post_status == 'publish' && !$update) {
        // $expiration_time = time() + 48 * 3600; // 48 hours from now
        $expiration_time = time() + 60; // 48 hours from now
        update_post_meta($post_id, '_expiration_time', $expiration_time);
    }
}
add_action('save_post', 'add_expiration_time_meta', 10, 3);


// Schedule the cron event
if (!wp_next_scheduled('delete_expired_weekly_zen_posts')) {
    wp_schedule_event(time(), 'hourly', 'delete_expired_weekly_zen_posts');
}

// Hook the cron event to our custom function
add_action('delete_expired_weekly_zen_posts', 'delete_expired_weekly_zen_posts_callback');

function delete_expired_weekly_zen_posts_callback() {
    global $wpdb;

    // Query for posts that are older than 48 hours
    $args = array(
        'post_type' => 'weekly-zen',
        'date_query' => array(
            array(
                'column' => 'post_date',
                'before' => '48 hours ago',
            ),
        ),
        'posts_per_page' => -1,
        'fields' => 'ids',
    );

    $expired_posts = get_posts($args);

    // Delete the expired posts
    foreach ($expired_posts as $post_id) {
        wp_delete_post($post_id, true);
    }
}
