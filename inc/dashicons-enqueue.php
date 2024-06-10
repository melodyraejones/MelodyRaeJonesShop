<?php
function enqueue_dashicons_front_end() {
    wp_enqueue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'enqueue_dashicons_front_end');