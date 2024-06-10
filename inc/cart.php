<?php




function handle_add_to_cart_request(WP_REST_Request $request) {
    $params = $request->get_params();
    $title = sanitize_text_field($params['title']);
    $price = floatval($params['price']);
    $productId = sanitize_text_field($params['productId']);
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
    } else {
        $cart_post_id = wp_insert_post([
            'post_title' => $title,
            'post_type' => 'cart',
            'post_status' => 'publish',
        ]);

        if ($cart_post_id === 0 || is_wp_error($cart_post_id)) {
            // Handle the error appropriately
            return new WP_REST_Response([
                'success' => false,
                'error' => 'Failed to create cart item.'
            ], 500);
        }

        update_post_meta($cart_post_id, 'program_price', $price);
        update_post_meta($cart_post_id, 'program_quantity', 1);
        update_post_meta($cart_post_id, 'product_id', $productId);
        update_post_meta($cart_post_id, 'user_id', $userId);
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

/////////////
function handle_add_to_cart_request(WP_REST_Request $request) {
    $params = $request->get_params();
    $title = sanitize_text_field($params['title']);
    $price = floatval($params['price']);
    $productId = sanitize_text_field($params['productId']);
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
    } else {
        $cart_post_id = wp_insert_post([
            'post_title' => $title,
            'post_type' => 'cart',
            'post_status' => 'publish',
        ]);

        if ($cart_post_id === 0 || is_wp_error($cart_post_id)) {
            // Handle the error appropriately
            return new WP_REST_Response([
                'success' => false,
                'error' => 'Failed to create cart item.'
            ], 500);
        }

        update_post_meta($cart_post_id, 'program_price', $price);
        update_post_meta($cart_post_id, 'program_quantity', 1);
        update_post_meta($cart_post_id, 'product_id', $productId);
        update_post_meta($cart_post_id, 'user_id', $userId);
    }

    return new WP_REST_Response([
        'success' => true,
        'cartItemId' => $cart_post_id,
        'productId' => $productId,
        'quantity' => get_post_meta($cart_post_id, 'program_quantity', true)
    ], 200);
}

function my_register_cart_meta() {
    register_rest_field('cart', 'program_price', [
        'get_callback' => function ($object) {
            // Return the post meta
            return get_post_meta($object['id'], 'program_price', true);
        },
        'update_callback' => function ($value, $object) {
            // Update the post meta
            return update_post_meta($object->ID, 'program_price', $value);
        },
        'schema' => null,
    ]);

    register_rest_field('cart', 'program_quantity', [
        'get_callback' => function ($object) {
            // Return the post meta
            return get_post_meta($object['id'], 'program_quantity', true);
        },
        'update_callback' => function ($value, $object) {
            // Update the post meta
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
            // Return the post meta that contains the user ID
            return get_post_meta($object['id'], 'user_id', true);
        },
        'update_callback' => null, // Assuming you don't want this to be updatable via REST
        'schema' => null, // Define the schema if needed
    ]);


      // Register user_login (username) in the REST field
      register_rest_field('cart', 'username', [
        'get_callback' => function ($object) {
            $user_id = get_post_meta($object['id'], 'user_id', true);
            $user_data = get_userdata($user_id);
            return $user_data ? $user_data->user_login : null;
        },
        'update_callback' => null, // Assuming you don't want this to be updatable via REST
        'schema' => null, // Define the schema if needed
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

//force cart post to be private
function makeCartPrivate($data, $postarr) {
    if ($data['post_type'] == 'cart' && $data['post_status'] != 'trash') {
        // Set the post status to 'private' if it's a cart post and not being moved to trash
        $data['post_status'] = 'private';
    }
    return $data;
}
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