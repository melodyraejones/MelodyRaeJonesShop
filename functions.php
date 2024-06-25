<?php 
// Include Composer autoload
require_once get_template_directory() . '/vendor/autoload.php';
require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';


// Include Customizer settings
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/timer.php';

// Cart URL
function get_cart_url() {
    if (defined('WP_ENV') && WP_ENV === 'production') {
        return 'https://melodyraejones.com/contact/cart/';
    } else {
        return 'http://melodyraejones.local/shop/cart/';
    }
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
        $headers = array('Content-Type: text/html; charset=UTF-8');

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

// Add SMTP settings if using PHPMailer directly
add_action('phpmailer_init', 'configure_phpmailer');
function configure_phpmailer($phpmailer) {
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        $smtpHost = defined('SMTP_HOST') ? SMTP_HOST : '';
        $smtpAuth = defined('SMTP_AUTH') ? SMTP_AUTH : false;
        $smtpUsername = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
        $smtpPassword = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
        $smtpSecure = defined('SMTP_SECURE') ? SMTP_SECURE : '';
        $smtpPort = defined('SMTP_PORT') ? SMTP_PORT : 25;

        $phpmailer->isSMTP();
        $phpmailer->Host       = $smtpHost; 
        $phpmailer->SMTPAuth   = $smtpAuth;
        $phpmailer->Username   = $smtpUsername;
        $phpmailer->Password   = $smtpPassword;
        $phpmailer->SMTPSecure = $smtpSecure == 'tls' ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $phpmailer->Port       = $smtpPort;
    } else {
        error_log('PHPMailer class does not exist');
    }
}

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
            $mail->Host = 'smtp.office365.com';
            $mail->Username = 'melody@melodyraejones.com'; // Office 365 email
            $mail->Password = 'nikita55'; // Office 365 email password or app-specific password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Use the verified email address for the 'From' field and include the user's name
            $mail->setFrom('melody@melodyraejones.com', $name . ' via Melody Rae Jones');
            // Set the Reply-To header to the email address from the contact form
            $mail->addReplyTo($email, $name);
            $mail->addAddress('melody@melodyraejones.com', 'Melody Rae Jones'); // Where the email will be sent
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body = "From: $name\nEmail: $email\nSource: $source\nMessage: $message";
            $mail->send();

            wp_redirect(home_url('/sent'));
            exit;
        } catch (Exception $e) {
            // Output error message
            error_log('Mailer Error: ' . $mail->ErrorInfo);
            wp_die('Mailer Error: ' . $mail->ErrorInfo);
        }
    } else {
        // Nonce check failed
        wp_die('Security check failed', 'Error', array('response' => 403));
    }
}
add_action('admin_post_nopriv_custom_contact_form', 'handle_custom_contact_form_submission');
add_action('admin_post_custom_contact_form', 'handle_custom_contact_form_submission');






function enqueue_dashicons_front_end() {
    wp_enqueue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'enqueue_dashicons_front_end');


function modify_audio_query($query) {
    // Check if it's the correct query to modify
    if (!is_admin() && $query->is_main_query() && $query->get('post_type') === 'audio') {
        // Set the author to 1
        $query->set('author', 1);
    }
}

add_action('pre_get_posts', 'modify_audio_query');



//cart-total-route
require get_theme_file_path('/inc/cart-total-route.php');
require get_theme_file_path('/inc/checkout-route.php');



function handle_add_to_cart_request(WP_REST_Request $request) {
    $params = $request->get_params();
    $title = sanitize_text_field($params['title']);
    $price = floatval($params['price']);
    $productId = sanitize_text_field($params['productId']);
    $relatedPrograms = isset($params['relatedPrograms']) ? maybe_serialize($params['relatedPrograms']) : ''; // Serialize related programs
    $userId = get_current_user_id();

    $existing_cart_items = get_posts([
        'post_type' => 'cart',
        'meta_query' => [
            [
                'key' => 'product_id',
                'value' => $productId,
                'compare' => '='
            ]
        ],
        'posts_per_page' => 1
    ]);

    if (count($existing_cart_items) > 0) {
        $cart_post_id = $existing_cart_items[0]->ID;
        $quantity = get_post_meta($cart_post_id, 'program_quantity', true) + 1;
        update_post_meta($cart_post_id, 'program_quantity', $quantity);
    } else {
        $cart_post_id = wp_insert_post([
            'post_title' => $title,
            'post_type' => 'cart',
            'post_status' => 'publish',
        ]);

        if ($cart_post_id === 0 || is_wp_error($cart_post_id)) {
            return new WP_REST_Response([
                'success' => false,
                'error' => 'Failed to create cart item.'
            ], 500);
        }

        update_post_meta($cart_post_id, 'program_price', $price);
        update_post_meta($cart_post_id, 'program_quantity', 1);
        update_post_meta($cart_post_id, 'product_id', $productId);
        update_post_meta($cart_post_id, 'user_id', $userId);
        update_post_meta($cart_post_id, 'related_programs', $relatedPrograms); // Store serialized related programs
    }

    return new WP_REST_Response([
        'success' => true,
        'cartItemId' => $cart_post_id,
        'productId' => $productId,
        'quantity' => get_post_meta($cart_post_id, 'program_quantity', true)
    ], 200);
}




add_action('rest_api_init', function () {
    register_rest_route('wp/v2', '/cart', [
        'methods' => 'POST',
        'callback' => 'handle_add_to_cart_request',
        'permission_callback' => function () {
            return is_user_logged_in() && (current_user_can('manage_options') || current_user_can('edit_posts'));
        }
    ]);
});



function my_register_cart_meta() {
    register_rest_field('cart', 'program_price', [
        'get_callback' => function ($object) {
            return get_post_meta($object['id'], 'program_price', true);
        },
        'update_callback' => function ($value, $object) {
            return update_post_meta($object->ID, 'program_price', $value);
        },
        'schema' => null,
    ]);

    register_rest_field('cart', 'program_quantity', [
        'get_callback' => function ($object) {
            return get_post_meta($object['id'], 'program_quantity', true);
        },
        'update_callback' => function ($value, $object) {
            return update_post_meta($object->ID, 'program_quantity', $value);
        },
        'schema' => null,
    ]);

    register_rest_field('cart', 'product_id', [
        'get_callback' => function ($object) {
            return get_post_meta($object['id'], 'product_id', true);
        },
        'update_callback' => function ($value, $object) {
            if (!empty($value)) {
                return update_post_meta($object->ID, 'product_id', sanitize_text_field($value));
            }
        },
        'schema' => null,
    ]);

    register_rest_field('cart', 'user_id', [
        'get_callback' => function ($object) {
            return get_post_meta($object['id'], 'user_id', true);
        },
        'update_callback' => null,
        'schema' => null,
    ]);

    // Register related programs field
    register_rest_field('cart', 'related_programs', [
        'get_callback' => function ($object) {
            $related_programs = maybe_unserialize(get_post_meta($object['id'], 'related_programs', true));
            return $related_programs ? $related_programs : [];
        },
        'update_callback' => null,
        'schema' => null,
    ]);
}

add_action('rest_api_init', 'my_register_cart_meta');


add_action('rest_api_init', function () {
    register_rest_route('wp/v2', '/cart', [
        'methods' => 'POST',
        'callback' => 'handle_add_to_cart_request',
      'permission_callback' => function () {
    return is_user_logged_in() && (current_user_can('manage_options') || current_user_can('edit_posts'));
}

    ]);
});

function get_user_cart_items() {
    if (!is_user_logged_in()) {
        return [];  // Return empty if user is not logged in
    }
    $args = [
        'post_type' => 'cart',
        'post_status' => ['publish', 'private'],
        'author' => get_current_user_id(),  // Ensure it fetches only the current user's items
        'numberposts' => -1
    ];
    return get_posts($args);
}

add_action('rest_api_init', function () {
    register_rest_route('mrj/v1', '/cart-total', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'mrjCartTotal',
        'permission_callback' => function () {
            return is_user_logged_in(); // Ensure only logged-in users can access
        }
    ));
});
//force cart post to be private
function makeCartPrivate($data, $postarr) {
    if ($data['post_type'] == 'cart' && $data['post_status'] != 'trash') {
        // Set the post status to 'private' if it's a cart post and not being moved to trash
        $data['post_status'] = 'private';
    }
    return $data;
}
add_filter('wp_insert_post_data', 'makeCartPrivate', 10, 2);

add_filter('wp_insert_post_data', 'makeCartPrivate', 10, 2);
//to make cart private
// Modify the WP_Query arguments in the REST request for 'cart' post type to include private posts for logged-in users.
add_filter('rest_cart_query', function ($args, $request) {
    if (is_user_logged_in()) {
        $args['post_status'] = ['publish', 'private'];
        $args['author'] = get_current_user_id();
    }
    return $args;
}, 10, 2);



add_filter('the_title', function($title, $id = null) {
    if (get_post_type($id) == 'cart') {
        return preg_replace('/^Private:\s*/', '', $title);
    }
    return $title;
}, 10, 2);



function mrj_files() {
    if (!is_page_template('page-default.php')) {
        // Enqueue a CSS file
        wp_enqueue_style('mrj_extra_styles', get_theme_file_uri('/css/main.css'));
    }
    
    // This script adds support for various browsers that don't support ES modules or certain modern JavaScript features.
    add_action('wp_footer', function () {
        echo '<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>';
        echo '<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>';
    }, 100);
}

add_action('wp_enqueue_scripts', 'mrj_files');



function mrj_features() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'mrj_features');

function mrj_enqueue_scripts() {
    // Enqueue Google Fonts
    wp_enqueue_style('google-font-courgette', 'https://fonts.googleapis.com/css2?family=Courgette&display=swap', [], null);
    wp_enqueue_style('google-font-arimo', 'https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400..700;1,400..700&display=swap', [], null);
    
    // Enqueue the latest Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', [], null);

    // Enqueue other styles and scripts
    wp_enqueue_style('mrj_extra_styles', get_theme_file_uri('/css/main.css'));
    wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', [], null, true);
    wp_enqueue_script('mrj-index-js', get_theme_file_uri('./build/index.js'), array('jquery'), '1.0', true);
    wp_script_add_data('mrj-index-js', 'type', 'module');
    wp_localize_script('mrj-index-js', 'mrjData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}

add_action('wp_enqueue_scripts', 'mrj_enqueue_scripts');



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
//Customize login screen
add_filter('login_headerurl','headerUrl');

function headerUrl(){
    return esc_url(site_url('/'));
}
add_action('login_enqueue_scripts','loginCSS');

function loginCSS(){
    wp_enqueue_style('mrj_extra_styles', get_theme_file_uri('/css/main.css'));
}
add_filter('login_headertitle', 'loginTitle');

function loginTitle(){
return get_bloginfo('name');
}

//user program access
// function mrj_on_user_register($user_id) {
//     global $wpdb;
//     $user_info = get_userdata($user_id);
//     $table_name = $wpdb->prefix . 'user_program_access';

//     // Fetch all programs
//     $programs = get_posts([
//         'post_type' => 'program',
//         'posts_per_page' => -1
//     ]);

//     // Prepare the programs access data
//     $programs_access = [];
//     foreach ($programs as $program) {
//         $programs_access[] = [
//             'program_id' => $program->ID,
//             'program_name' => $program->post_title,
//             'access' => false  // Default to no access
//         ];
//     }

//     // Insert data into custom table
//     $wpdb->insert($table_name, [
//         'user_id' => $user_id,
//         'user_email' => $user_info->user_email,
//         'programs_access' => json_encode($programs_access)  // Store as JSON
//     ]);
// }
// add_action('user_register', 'mrj_on_user_register');
function mrj_on_user_register($user_id) { 
    global $wpdb;
    $user_info = get_userdata($user_id);
    
    if (!$user_info) {
        error_log('Failed to get user data for user ID: ' . $user_id);
        return;
    }

    $table_name = $wpdb->prefix . 'user_program_access';
    error_log('Registering new user with ID: ' . $user_id);

    $programs = get_posts([
        'post_type' => 'program',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids'  // Only get the IDs to reduce memory usage
    ]);

    if (empty($programs)) {
        error_log('No programs found to register for user.');
        return;
    }

    foreach ($programs as $program_id) {
        $program = get_post($program_id);  // Get the program post object
        error_log('Inserting program: ' . $program->post_title); 

        // Check if the program already has a row for this user
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND program_name = %s",
            $user_id,
            $program->post_title
        ));

        // Only insert if it does not exist
        if (!$exists) {
            $result = $wpdb->insert($table_name, [
                'user_id' => $user_id,
                'program_id' => $program_id, 
                'user_email' => $user_info->user_email,
                'program_name' => $program->post_title,
                'access_granted' => 0,
                'created_at' => current_time('mysql', 1)
            ]);

            if ($result === false) {
                error_log('Failed to insert program access for user. Error: ' . $wpdb->last_error);
            } else {
                error_log('Program access for user ' . $user_id . ' to program ' . $program->post_title . ' added.');
            }
        }
    }
}

// add_action('user_register', 'mrj_on_user_register');


// Hook into user account deletion and clean up custom data
function mrj_on_user_delete($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_program_access';

    // Delete the records where the user ID matches the ID of the user being deleted
    $result = $wpdb->delete($table_name, ['user_id' => $user_id]);

    if (false === $result) {
        error_log("Failed to delete user program access records for user ID: $user_id");
    } else {
        error_log("Deleted user program access records for user ID: $user_id");
    }
}

// Add the hook into WordPress
add_action('delete_user', 'mrj_on_user_delete');

// Function to normalize product names by replacing non-breaking spaces with regular spaces
if (!function_exists('normalize_product_name')) {
    function normalize_product_name($name) {
        return str_replace("\u{00A0}", ' ', html_entity_decode(trim($name), ENT_QUOTES, 'UTF-8'));
    }
}

// Register the /check-purchase route
add_action('rest_api_init', function () {
    register_rest_route('mrj/v1', '/check-purchase/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'mrj_check_if_already_purchased',
        'permission_callback' => '__return_true'
    ));
});

function mrj_check_if_already_purchased(WP_REST_Request $request) {
    $product_id = intval($request['id']);
    $user_id = get_current_user_id();

    if (!$user_id) {
        return new WP_REST_Response(['error' => 'User not logged in'], 401);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'user_program_access';

    $alreadyPurchased = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND program_id = %d AND access_granted = 1",
        $user_id,
        $product_id
    ));

    return new WP_REST_Response(['alreadyPurchased' => $alreadyPurchased > 0], 200);
}


function send_new_program_email_on_publish($new_status, $old_status, $post) {
    // Only send email when the post transitions from any status to 'publish' and it is of type 'program'
    if ($old_status === 'publish' || $new_status !== 'publish' || $post->post_type !== 'program') {
        return;
    }

    // Get all registered users
    $users = get_users(['role__in' => ['subscriber', 'administrator', 'editor', 'author']]);

    // Prepare the email content
    $subject = "New Program Available: " . $post->post_title;
    $message = "
        <html>
        <head>
            <style>
                .email-container {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                }
                .email-header {
                    background-color: #f4f4f4;
                    padding: 10px;
                    text-align: center;
                    font-size: 18px;
                    font-weight: bold;
                }
                .email-body {
                    padding: 20px;
                }
                .email-footer {
                    background-color: #f4f4f4;
                    padding: 10px;
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                }
                a {
                    color: #1a73e8;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>New Program Added</div>
                <div class='email-body'>
                    <p>Hello,</p>
                    <p>A new program has been added: <strong>" . $post->post_title . "</strong></p>
                    <p>You can view the program here: <a href='" . get_permalink($post->ID) . "'>" . get_permalink($post->ID) . "</a></p>
                    <p>Thank you.</p>
                </div>
                <div class='email-footer'>This is an automated message. Please do not reply.</div>
            </div>
        </body>
        </html>
    ";

    // Initialize PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Fetch SMTP settings from wp-config.php
        $smtpHost = defined('SMTP_HOST') ? SMTP_HOST : '';
        $smtpAuth = defined('SMTP_AUTH') ? SMTP_AUTH : false;
        $smtpUsername = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
        $smtpPassword = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
        $smtpSecure = defined('SMTP_SECURE') ? SMTP_SECURE : 'tls';
        $smtpPort = defined('SMTP_PORT') ? SMTP_PORT : 25;

        $mail->isSMTP();
        $mail->SMTPAuth = $smtpAuth;
        $mail->Host = $smtpHost;
        $mail->SMTPSecure = $smtpSecure == 'tls' ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $smtpPort;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtpPassword;
        $mail->setFrom('melody@melodyraejones.com', 'Melody');
        $mail->isHTML(true);  // Set email format to HTML

        // Loop through each user and send the email
        foreach ($users as $user) {
            $mail->addAddress($user->user_email);  // Add a recipient
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            $mail->clearAddresses();  // Clear all addresses for the next iteration
        }
    } catch (PHPMailer\PHPMailer\Exception $e) {
        error_log('PHPMailer error: ' . $e->getMessage());
    }
}

// Hook into the transition_post_status action to send email when a new program is published
add_action('transition_post_status', 'send_new_program_email_on_publish', 10, 3);
