<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Set the Stripe API key
if (isset($_ENV['STRIPE_SECRET_KEY'])) {
    \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
} else {
    error_log('Stripe Secret Key is not set.');
}
?>
