<?php
//redirect subscriber account out of admin to the homepage
add_action('admin_init','redirectSubsToFrontend');

function redirectSubsToFrontend(){
$currentUser = wp_get_current_user();
    if(count($currentUser -> roles) == 1 AND $currentUser-> roles[0] == 'subscriber' ){
            wp_redirect(site_url('/'));
            exit;
}
}
//hide dashboard for users
add_action('wp_loaded','noSubsAdminBar');

function noSubsAdminBar(){
$currentUser = wp_get_current_user();
    if(count($currentUser -> roles) == 1 AND $currentUser-> roles[0] == 'subscriber' ){
           show_admin_bar(false);
}
}