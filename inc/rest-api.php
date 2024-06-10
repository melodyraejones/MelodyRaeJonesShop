<?php


//direct checkout route:

function mrj_direct_stripe_checkout(WP_REST_Request $request) {
    // Set your Stripe API key
    \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

    $product_id = $request->get_param('product_id');
    $product_price = get_post_meta($product_id, 'program_price', true) * 100; // Convert to cents
    $product_name = get_the_title($product_id);

    // Create a Stripe Checkout session for the product
    try {
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => $product_name],
                    'unit_amount' => $product_price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => home_url('/success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => home_url('/cancel'),
        ]);

        return new WP_REST_Response(['id' => $session->id, 'url' => $session->url], 200);
    } catch (Exception $e) {
        return new WP_REST_Response(['error' => $e->getMessage()], 500);
    }
}

add_action('rest_api_init', function() {
    register_rest_route('mrj/v1', '/direct-checkout', [
        'methods' => 'POST',
        'callback' => 'mrj_direct_stripe_checkout',
        'permission_callback' => '__return_true'
    ]);
});