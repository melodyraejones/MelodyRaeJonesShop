<?php

// Custom wp_mail function for logging
add_filter('wp_mail', 'custom_wp_mail');
function custom_wp_mail($args) {
    // Set default headers if not present
    if (empty($args['headers'])) {
        $args['headers'] = array(
            'From: Your Name <your-email@example.com>',
            'Content-Type: text/html; charset=UTF-8'
        );
    }

    // Log email arguments for debugging
    error_log(print_r($args, true));

    // Return modified arguments
    return $args;
}

// Custom new user notification function
if (!function_exists('wp_new_user_notification')) {
    function wp_new_user_notification($user_id, $notify = 'both') {
        global $wpdb, $wp_hasher;

        // Get user data
        $user = get_userdata($user_id);
        if (!$user) {
            error_log("Failed to get user data for user ID: $user_id");
            return;
        }

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);

        // Generate password reset key
        $key = wp_generate_password(20, false);
        do_action('retrieve_password_key', $user->user_login, $key);

        // Send the password reset link email
        $message = sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
        $message .= __('To set your password, visit the following address:') . "\r\n\r\n";
        $message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n\r\n";

        // Email headers
        $headers = array('Content-Type: text/plain; charset=UTF-8');

        // Log email details for debugging
        error_log("Sending new user notification to: $user_email");

        // Use wp_mail to send the email
        $sent = wp_mail($user_email, sprintf(__('[%s] Login Details'), get_option('blogname')), $message, $headers);

        if ($sent) {
            error_log("New user notification email sent to: $user_email");
        } else {
            error_log("Failed to send new user notification email to: $user_email");
        }
    }
}

// Hook into user registration
add_action('user_register', 'wp_new_user_notification');

// Disable default password change notification
if (!function_exists('wp_password_change_notification')) {
    function wp_password_change_notification($user) {
        return; // Disable the default notification for password change
    }
}


