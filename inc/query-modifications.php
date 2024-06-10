<?php
function modify_audio_query($query) {
    // Check if it's the correct query to modify
    if (!is_admin() && $query->is_main_query() && $query->get('post_type') === 'audio') {
        // Set the author to 1
        $query->set('author', 1);
    }
}

add_action('pre_get_posts', 'modify_audio_query');