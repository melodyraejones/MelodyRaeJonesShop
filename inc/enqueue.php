<?php
// Enqueue Font Awesome
function mrj_enqueue_scripts() {
    // Enqueue Google Fonts
    wp_enqueue_style('google-font-courgette', 'https://fonts.googleapis.com/css2?family=Courgette&display=swap', [], null);
    wp_enqueue_style('google-font-arimo', 'https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400..700;1,400..700&display=swap', [], null);
    
    // Enqueue Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', [], null);

    // Enqueue other styles and scripts
    wp_enqueue_style('mrj_extra_styles', get_theme_file_uri('/css/main.css'));
    wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', [], null, true);
    wp_enqueue_script('mrj-index-js', get_theme_file_uri('./build/index.js'), array('jquery'), '1.0', true);
    wp_script_add_data('mrj-index-js', 'type', 'module');
    wp_localize_script('mrj-index-js', 'mrjData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}
add_action('wp_enqueue_scripts', 'mrj_enqueue_scripts');





function mrj_files() {
    // Enqueue a CSS file
    wp_enqueue_style('mrj_extra_styles', get_theme_file_uri('/css/main.css'));

    // This script adds support for various browsers that don't support ES modules or certain modern JavaScript features.
    add_action('wp_footer', function () {
        echo '<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>';
        echo '<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>';
    }, 100);
}

add_action('wp_enqueue_scripts', 'mrj_files');
