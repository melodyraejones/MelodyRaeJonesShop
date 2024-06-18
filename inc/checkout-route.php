<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Access the Stripe secret keys from the wp-config.php
$stripeSecretKey = defined('STRIPE_SECRET_KEY') ? STRIPE_SECRET_KEY : '';
$webhookSecretKey = defined('STRIPE_WEBHOOK_SECRET') ? STRIPE_WEBHOOK_SECRET : '';

// Set the Stripe API key
\Stripe\Stripe::setApiKey($stripeSecretKey);

// Normalize product names by replacing non-breaking spaces with regular spaces
function normalize_product_name($name) {
    return str_replace("\u{00A0}", ' ', html_entity_decode(trim($name), ENT_QUOTES, 'UTF-8'));
}

// Function to create a Stripe Checkout Session
function mrj_create_stripe_checkout_session(WP_REST_Request $request) {
    $user_id = get_current_user_id();
    $user_info = get_userdata($user_id);
    if (!$user_info) {
        return new WP_REST_Response(['error' => 'User not logged in'], 401);
    }

    $validated_data = $request->get_json_params();
    $product_names = array_map(function($item) {
        return normalize_product_name($item['name']);
    }, $validated_data['items']);

    // Check if the user has already purchased "The Expand Your Wisdom Toolkit"
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_program_access';
    $wisdom_toolkit_purchased = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND program_name = %s AND access_granted = 1",
        $user_id, 'The Expand Your Wisdom Toolkit'
    ));

    if ($wisdom_toolkit_purchased > 0 && in_array('The Expand Your Wisdom Toolkit', $product_names)) {
        return new WP_REST_Response(['error' => 'You have already purchased The Expand Your Wisdom Toolkit.'], 400);
    }

    // Check if the user has already purchased any of the products
    $purchased_programs = $wpdb->get_col($wpdb->prepare(
        "SELECT program_name FROM $table_name WHERE user_id = %d AND program_name IN (" . implode(',', array_fill(0, count($product_names), '%s')) . ") AND access_granted = 1",
        array_merge([$user_id], $product_names)
    ));

    if (!empty($purchased_programs)) {
        return new WP_REST_Response(['error' => 'You have already purchased: ' . implode(', ', $purchased_programs)], 400);
    }

    $line_items = array_map(function($item) {
        return [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => ['name' => $item['name']],
                'unit_amount' => $item['price'] * 100,
            ],
            'quantity' => $item['quantity'],
        ];
    }, $validated_data['items']);

    $related_programs = isset($validated_data['relatedPrograms']) ? $validated_data['relatedPrograms'] : [];
    $is_wisdom_toolkit_purchased = in_array('The Expand Your Wisdom Toolkit', $product_names);

    try {
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => home_url('/success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => home_url('/cancel'),
            'metadata' => [
                'user_id' => $user_id,
                'username' => $user_info->user_login,
                'email' => $user_info->user_email,
                'product_names' => json_encode($product_names),
                'related_programs' => json_encode($related_programs),
                'is_wisdom_toolkit_purchased' => $is_wisdom_toolkit_purchased ? 'true' : 'false'
            ]
        ]);
        return new WP_REST_Response(['url' => $session->url], 200);
    } catch (Exception $e) {
        error_log("Stripe Checkout Session creation failed: " . $e->getMessage());
        return new WP_REST_Response(['error' => $e->getMessage()], 500);
    }
}

// Register the /checkout route
add_action('rest_api_init', function () {
    register_rest_route('mrj/v1', '/checkout', array(
        'methods' => 'POST',
        'callback' => 'mrj_create_stripe_checkout_session',
        'permission_callback' => '__return_true'
    ));
});

// Register the /webhook route
add_action('rest_api_init', function () {
    register_rest_route('mrj/v1', '/webhook', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'mrj_handle_stripe_webhook',
        'permission_callback' => '__return_true'
    ));
});

// Function to handle Stripe webhooks
function mrj_handle_stripe_webhook() {
    global $webhookSecretKey;

    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

    try {
        $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $webhookSecretKey);
    } catch (\UnexpectedValueException $e) {
        http_response_code(400);
        error_log("Invalid payload: " . $e->getMessage());
        exit();
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        http_response_code(400);
        error_log("Invalid signature: " . $e->getMessage());
        exit();
    }

    if ($event->type == 'checkout.session.completed') {
        $session = $event->data->object;

        $user_id = $session->metadata->user_id;
        $product_names = json_decode($session->metadata->product_names);
        $user_email = $session->metadata->email;
        $related_programs = isset($session->metadata->related_programs) ? json_decode($session->metadata->related_programs, true) : [];
        $is_wisdom_toolkit_purchased = isset($session->metadata->is_wisdom_toolkit_purchased) ? $session->metadata->is_wisdom_toolkit_purchased : 'false';

        error_log("Webhook received for user ID: $user_id, email: $user_email, products: " . implode(", ", $product_names));

        global $wpdb;
        $table_name = $wpdb->prefix . 'user_program_access';
        $toolkit_table = $wpdb->prefix . 'wisdom_toolkit_access';

        // Insert or update access in user_program_access table for purchased products
        foreach ($product_names as $product_name) {
            // Use WP_Query to get the program ID
            $program_query = new WP_Query([
                'post_type' => 'program',
                's' => $product_name,
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'fields' => 'ids'
            ]);

            error_log("Querying program ID for product: $product_name");

            if ($program_query->have_posts()) {
                $program_query->the_post();
                $program_id = get_the_ID();
                wp_reset_postdata();
                error_log("Found program ID: $program_id for product: $product_name");
            } else {
                $program_id = null;
                error_log("Program ID not found for product: $product_name");
            }

            if ($program_id) {
                $exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND program_name = %s",
                    $user_id,
                    $product_name
                ));

                if ($exists) {
                    $updated = $wpdb->update(
                        $table_name,
                        ['access_granted' => 1, 'program_id' => $program_id],
                        [
                            'user_id' => $user_id,
                            'program_name' => $product_name
                        ]
                    );
                    if ($updated === false) {
                        error_log("Failed to update program access for user ID: $user_id, program: $product_name");
                    }
                } else {
                    $inserted = $wpdb->insert($table_name, [
                        'user_id' => $user_id,
                        'user_email' => $user_email,
                        'program_name' => $product_name,
                        'program_id' => $program_id,
                        'access_granted' => 1,
                        'created_at' => current_time('mysql', 1)
                    ]);
                    if ($inserted === false) {
                        error_log("Failed to insert program access for user ID: $user_id, program: $product_name");
                    }
                }
            }
        }

        // Handle related programs
        foreach ($related_programs as $program) {
            $program_name = $program['title'];
            $program_id = $program['id'];

            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND program_name = %s",
                $user_id,
                $program_name
            ));

            if ($exists) {
                $updated = $wpdb->update(
                    $table_name,
                    ['access_granted' => 1, 'program_id' => $program_id],
                    [
                        'user_id' => $user_id,
                        'program_name' => $program_name
                    ]
                );
                if ($updated === false) {
                    error_log("Failed to update related program access for user ID: $user_id, program: $program_name");
                }
            } else {
                $inserted = $wpdb->insert($table_name, [
                    'user_id' => $user_id,
                    'user_email' => $user_email,
                    'program_id' => $program_id,
                    'program_name' => $program_name,
                    'access_granted' => 1,
                    'created_at' => current_time('mysql', 1)
                ]);
                if ($inserted === false) {
                    error_log("Failed to insert related program access for user ID: $user_id, program: $program_name");
                }
            }
        }

        // Check if Wisdom Toolkit was purchased and handle separately
        if ($is_wisdom_toolkit_purchased === 'true') {
            handle_wisdom_toolkit_purchase($user_id, $user_email);
        }

        // Clear the cart items for the user if the table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}cart'") == "{$wpdb->prefix}cart") {
            if (function_exists('clear_user_cart_items')) {
                clear_user_cart_items($user_id);
            }
        }

        // Send email for general program access
        $subject = "Access Your Purchased Programs";
        $message = "Congratulations on your purchase! You can now access your purchased programs. Here is the link to access your programs:\n\n";
        $message .= home_url('/your-programs');
        $headers = [
            'From: Your Name <melody@melodyraejones.com>',
            'Content-Type: text/html; charset=UTF-8'
        ];

        wp_mail($user_email, $subject, $message, $headers);
    }

    http_response_code(200);
    exit();
}

// Function to handle the purchase of the Wisdom Toolkit
function handle_wisdom_toolkit_purchase($user_id, $user_email) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_program_access';

    // Use WP_Query to get the ID of "The Expand Your Wisdom Toolkit" page
    $toolkit_query = new WP_Query([
        'post_type' => 'page',
        'title' => 'The Expand Your Wisdom Toolkit',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids'
    ]);

    if ($toolkit_query->have_posts()) {
        $toolkit_query->the_post();
        $toolkit_id = get_the_ID();
        wp_reset_postdata();
        error_log("Found toolkit ID: $toolkit_id");
    } else {
        $toolkit_id = null;
        error_log("No toolkit found for 'The Expand Your Wisdom Toolkit'");
    }

    if ($toolkit_id) {
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND program_id = %d",
            $user_id,
            $toolkit_id
        ));

        if ($exists) {
            $updated = $wpdb->update(
                $table_name,
                ['access_granted' => 1],
                [
                    'user_id' => $user_id,
                    'program_id' => $toolkit_id
                ]
            );
            if ($updated === false) {
                error_log("Failed to update Wisdom Toolkit access for user ID: $user_id");
            }
        } else {
            $inserted = $wpdb->insert($table_name, [
                'user_id' => $user_id,
                'user_email' => $user_email,
                'program_id' => $toolkit_id,
                'program_name' => 'The Expand Your Wisdom Toolkit',
                'access_granted' => 1,
                'created_at' => current_time('mysql', 1)
            ]);
            if ($inserted === false) {
                error_log("Failed to insert Wisdom Toolkit access for user ID: $user_id");
            }
        }
    } else {
        error_log('Program not found: The Expand Your Wisdom Toolkit');
    }
}

// Ensure the function `clear_user_cart_items` is defined only once
if (!function_exists('clear_user_cart_items')) {
    function clear_user_cart_items($user_id) {
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'cart', ['user_id' => $user_id]);
    }
}
