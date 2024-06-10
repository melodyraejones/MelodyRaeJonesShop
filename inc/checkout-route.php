<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Access the Stripe secret keys from the wp-config.php
$stripeSecretKey = defined('STRIPE_SECRET_KEY') ? STRIPE_SECRET_KEY : '';
$webhookSecretKey = defined('STRIPE_WEBHOOK_SECRET') ? STRIPE_WEBHOOK_SECRET : '';

// Set the Stripe API key
\Stripe\Stripe::setApiKey($stripeSecretKey);

// Function to create a Stripe Checkout Session
function mrj_create_stripe_checkout_session(WP_REST_Request $request) {
    $user_id = get_current_user_id();
    $user_info = get_userdata($user_id);
    if (!$user_info) {
        return new WP_REST_Response(['error' => 'User not logged in'], 401);
    }

    $validated_data = $request->get_json_params();
    
    // Log the entire request payload
    error_log('Received request payload: ' . print_r($validated_data, true));

    $product_names = array_map(function($item) {
        return $item['name'];
    }, $validated_data['items']);

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

    // Ensure relatedPrograms exists in metadata
    $related_programs = isset($validated_data['relatedPrograms']) ? $validated_data['relatedPrograms'] : [];

    // Log relatedPrograms data
    error_log('Creating Stripe session. Related Programs: ' . print_r($related_programs, true));

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
                'related_programs' => json_encode($related_programs)  // Correctly include related programs
            ]
        ]);
        return new WP_REST_Response(['url' => $session->url], 200);
    } catch (Exception $e) {
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

function mrj_handle_stripe_webhook() {
    global $webhookSecretKey;

    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

    try {
        $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $webhookSecretKey);
    } catch (\UnexpectedValueException $e) {
        http_response_code(400);
        exit();
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        http_response_code(400);
        exit();
    }

    if ($event->type == 'checkout.session.completed') {
        $session = $event->data->object;
        $user_id = $session->metadata->user_id;
        $product_names = json_decode($session->metadata->product_names);
        $user_email = $session->metadata->email;
        $related_programs = isset($session->metadata->related_programs) ? json_decode($session->metadata->related_programs, true) : [];

        error_log('Webhook received. User ID: ' . $user_id);
        error_log('Webhook received. Product Names: ' . print_r($product_names, true));
        error_log('Webhook received. Raw Related Programs Metadata: ' . $session->metadata->related_programs);
        error_log('Webhook received. Decoded Related Programs: ' . print_r($related_programs, true));

        global $wpdb;
        $table_name = $wpdb->prefix . 'user_program_access';
        $toolkit_table = $wpdb->prefix . 'wisdom_toolkit_access';

        // Insert or update access in user_program_access table for purchased product
        foreach ($product_names as $product_name) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND program_name = %s",
                $user_id,
                $product_name
            ));

            if ($exists) {
                $updated = $wpdb->update(
                    $table_name,
                    ['access_granted' => 1],
                    [
                        'user_id' => $user_id,
                        'program_name' => $product_name
                    ]
                );
                if ($updated === false) {
                    error_log('Failed to update main product: ' . $wpdb->last_error);
                } else {
                    error_log('Updated main product: ' . $product_name);
                }
            } else {
                $inserted = $wpdb->insert($table_name, [
                    'user_id' => $user_id,
                    'user_email' => $user_email,
                    'program_name' => $product_name,
                    'access_granted' => 1,
                    'created_at' => current_time('mysql', 1)
                ]);
                if ($inserted === false) {
                    error_log('Failed to insert main product: ' . $wpdb->last_error);
                } else {
                    error_log('Inserted main product: ' . $product_name);
                }
            }
        }

        // Handle related programs
        foreach ($related_programs as $program) {
            $program_name = $program['title'];
            $program_id = $program['id'];

            error_log('Processing related program. Program ID: ' . $program_id . ', Program Name: ' . $program_name);

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
                    error_log('Failed to update related program: ' . $wpdb->last_error);
                } else {
                    error_log('Updated related program: ' . $program_name);
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
                    error_log('Failed to insert related program: ' . $wpdb->last_error);
                } else {
                    error_log('Inserted related program: ' . $program_name);
                }
            }
        }

        // Grant access to the Wisdom Toolkit
        $wisdom_toolkit_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $toolkit_table WHERE user_id = %d",
            $user_id
        ));

        if ($wisdom_toolkit_exists) {
            $updated_toolkit = $wpdb->update(
                $toolkit_table,
                ['access_granted' => 1],
                ['user_id' => $user_id]
            );
            if ($updated_toolkit === false) {
                error_log('Failed to update wisdom toolkit access: ' . $wpdb->last_error);
            } else {
                error_log('Updated wisdom toolkit access for user: ' . $user_id);
            }
        } else {
            $inserted_toolkit = $wpdb->insert($toolkit_table, [
                'user_id' => $user_id,
                'user_email' => $user_email,
                'access_granted' => 1,
                'created_at' => current_time('mysql', 1)
            ]);
            if ($inserted_toolkit === false) {
                error_log('Failed to insert wisdom toolkit access: ' . $wpdb->last_error);
            } else {
                error_log('Inserted wisdom toolkit access for user: ' . $user_id);
            }
        }

        // Grant access to all modules of the custom post type 'wisdomtoolkitcontent'
        $modules = get_posts([
            'post_type' => 'wisdomtoolkitcontent',
            'posts_per_page' => -1
        ]);

        foreach ($modules as $module) {
            $program_name = $module->post_title;
            $program_id = $module->ID;

            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND program_id = %d",
                $user_id,
                $program_id
            ));

            if ($exists) {
                $updated = $wpdb->update(
                    $table_name,
                    ['access_granted' => 1],
                    [
                        'user_id' => $user_id,
                        'program_id' => $program_id
                    ]
                );
                if ($updated === false) {
                    error_log('Failed to update module: ' . $wpdb->last_error);
                } else {
                    error_log('Updated module: ' . $program_name);
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
                    error_log('Failed to insert module: ' . $wpdb->last_error);
                } else {
                    error_log('Inserted module: ' . $program_name);
                }
            }
        }

        // Clear the cart items for the user
        clear_user_cart_items($user_id);

        // Send email for general program access
        $subject = "Access Your Purchased Programs";
        $message = "Congratulations on your purchase! You can now access your purchased programs. Here is the link to access your programs:\n\n";
        $message .= home_url('/your-programs');
        $headers = [
            'From: Your Name <melody@melodyraejones.com>',
            'Content-Type: text/html; charset=UTF-8'
        ];

        wp_mail($user_email, $subject, $message, $headers);

        error_log('Email sent to: ' . $user_email);
    }

    http_response_code(200);
    exit();
}
add_action('wp_ajax_nopriv_mrj_handle_stripe_webhook', 'mrj_handle_stripe_webhook');
add_action('wp_ajax_mrj_handle_stripe_webhook', 'mrj_handle_stripe_webhook');