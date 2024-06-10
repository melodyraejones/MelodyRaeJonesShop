<?php
// Send an Email with Default Password for protected content
function send_protected_page_access($user_email) {
    $protected_page_url = get_permalink(get_page_by_title('Protected Content'));
    $default_password = 'temporary-password';

    $subject = 'Access Your Protected Content';
    $message = "Dear user,\n\nThank you for your purchase. You can access the protected content using the following password: $default_password\n\n$protected_page_url\n\nPlease reset your password using the form on the protected page.";
    
    wp_mail($user_email, $subject, $message);
}


function handle_reset_password() {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);
        $user = get_user_by('email', $email);

        if ($user) {
            wp_set_password($password, $user->ID);
            wp_redirect(home_url('/password-reset-success'));
        } else {
            wp_redirect(home_url('/password-reset-error'));
        }
        exit;
    }
}
add_action('admin_post_nopriv_reset_password', 'handle_reset_password');
add_action('admin_post_reset_password', 'handle_reset_password');
