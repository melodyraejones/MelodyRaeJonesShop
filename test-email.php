<?php
// Load WordPress environment
require( dirname(__FILE__) . '/wp-load.php' );

$to = 'akshaysharma5432@gmail.com';
$subject = 'Test Email from WordPress';
$message = 'This is a test email from your WordPress site.';

if (wp_mail($to, $subject, $message)) {
    echo 'Email sent successfully!';
} else {
    echo 'Failed to send email.';
}
