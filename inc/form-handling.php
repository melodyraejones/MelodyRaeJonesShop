<?php
function handle_custom_contact_form_submission() {
    if (isset($_POST['contact_form_nonce']) && wp_verify_nonce($_POST['contact_form_nonce'], 'custom_contact_form_action')) {
        $name = sanitize_text_field($_POST['full-name']);
        $email = sanitize_email($_POST['email']);
        $source = sanitize_text_field($_POST['select-where']);
        $message = sanitize_textarea_field($_POST['message']);
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->Username = 'akshaysharma581995@gmail.com';
            $mail->Password = 'feulvpnfltokqjkd';
            $mail->setFrom($email, $name);
            $mail->addAddress('akshaysharma581995@gmail.com', 'Akshay');
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body = "From: $name\nEmail: $email\nSource: $source\nMessage: $message";
            $mail->send();
           

            wp_redirect(home_url('/sent'));
            exit;
        } catch (Exception $e) {
            // Output error message
            wp_die('Mailer Error: ' . $mail->ErrorInfo);
        }
    } else {
        // Nonce check failed
        wp_die('Security check failed', 'Error', array( 'response' => 403 ));
    }
}



add_action('admin_post_nopriv_custom_contact_form', 'handle_custom_contact_form_submission');
add_action('admin_post_custom_contact_form', 'handle_custom_contact_form_submission');

