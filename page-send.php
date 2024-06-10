<?php
/*
Template Name: Send Page
*/
get_he
$response_message = "";
// Check if the form is submitted
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize_text_field($_POST['full-name']);
    $email = sanitize_email($_POST['email']);
    $source = sanitize_text_field($_POST['select-where']);
    $message = sanitize_textarea_field($_POST['message']);
// }
require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail-> Host = "smtp.gmail.com";
$mail-> SMTPSecure =  PHPMailer::ENCRYPTION_STARTTLS;
$mail-> PORT = 587;
$mail-> Username = "akshaysharma581995@gmail.com";
$mail->Password = "Jamesmath;)123";

$mail -> setFrom($email, $name);
$mail->addAddress("dave@example.com", "Dave");

$mail->Subject = $subject;
$mail->Body = $message;
$mail->send();

header("Location: sent.php");