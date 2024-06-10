<?php
//Customize login screen
add_filter('login_headerurl','headerUrl');

function headerUrl(){
    return esc_url(site_url('/'));
}
add_action('login_enqueue_scripts','loginCSS');

function loginCSS(){
    wp_enqueue_style('mrj_extra_styles', get_theme_file_uri('/css/main.css')); 
}
add_filter('login_headertitle', 'loginTitle');

function loginTitle(){
return get_bloginfo('name');
}
