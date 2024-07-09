<?php
function mrjCartTotal(WP_REST_Request $request) {
     // Nonce verification
     $nonce = $request->get_param('security');
     if (!wp_verify_nonce($nonce, 'cart_total_nonce')) {
         return new WP_REST_Response(['error' => 'Invalid nonce'], 403);
     }
 
    if (!is_user_logged_in()) {
        return new WP_Error('no_auth', 'Authorization required', ['status' => 401]);
    }

    $user_id = get_current_user_id(); // Ensure we're getting the current user ID correctly

    $args = [
        'post_type' => 'cart',
        'post_status' => 'private', // Only fetch private posts
        'author' => $user_id, // Only fetch posts belonging to the logged-in user
        'numberposts' => -1 // Get all posts
    ];

    $cart_items = get_posts($args);
    $total = 0;
    $items = [];
    foreach ($cart_items as $item) {
        $price = (float) get_post_meta($item->ID, 'program_price', true);
        $quantity = (int) get_post_meta($item->ID, 'program_quantity', true);
        $product_id = get_post_meta($item->ID, 'product_id', true); // Fetch the product_id post meta
        $related_programs = maybe_unserialize(get_post_meta($item->ID, 'related_programs', true)); // Fetch the related_programs meta data

        // Ensure $related_programs is an array
        if (!$related_programs) {
            $related_programs = [];
        }

        $total += $price * $quantity;

        $items[] = [
            'name' => get_the_title($item->ID),
            'price' => $price,
            'quantity' => $quantity,
            'cart_item_id' => $item->ID,
            'product_id' => $product_id, // Include the product_id in the response
            'relatedPrograms' => $related_programs // Include related programs
        ];
    }

    return new WP_REST_Response([
        'cartTotal' => $total,
        'items' => $items,
        'date' => current_time('mysql')
    ], 200);
}

add_action('rest_api_init', function () {
    register_rest_route('mrj/v1', '/cart-total', array(
        'methods' => 'GET',
        'callback' => 'mrjCartTotal',
        'permission_callback' => '__return_true'
    ));
});
?>
