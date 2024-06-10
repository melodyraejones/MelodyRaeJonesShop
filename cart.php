<?php
/*
Template Name: Cart
*/

if (!is_user_logged_in()) {
    wp_redirect(esc_url(site_url('/wp-login.php')));
    exit;
}

get_header();
?>

<h1 class="heading-primary cart-heading">Your Cart</h1>

<section class="cart-details container">
    <h2 class="heading-secondary" id="cart">Your Order Summary</h2>
    <div class="cart-items" id="cart-items">
        <?php 
        $userItems = new WP_Query(array(
            'post_type' => 'cart',
            'posts_per_page' => -1,
            'author' => get_current_user_id()
        ));

        while($userItems->have_posts()) {
            $userItems->the_post();
            $relatedPrograms = maybe_unserialize(get_post_meta(get_the_ID(), 'related_programs', true));
            ?>
            <div class="cart-item">
                <div class="item-details">
                    <h3 class="product-name"><?php the_title(); ?></h3>
                    <p>Price: $<?php echo get_post_meta(get_the_ID(), 'program_price', true); ?></p>
                    <p>Quantity: <?php echo get_post_meta(get_the_ID(), 'program_quantity', true); ?></p>
                    <?php if (!empty($relatedPrograms)) { ?>
                        <h4>Related Programs:</h4>
                        <ul>
                            <?php foreach ($relatedPrograms as $program) { ?>
                                <li><?php echo esc_html($program['title']); ?> - $<?php echo esc_html(number_format((float)$program['price'], 2)); ?></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
                <div class="item-delete">
                    <button class="delete-item"><i class="fas fa-trash"></i> Delete</button>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="cart-checkout">
        <div class="cart-total">
            <p id="cart-total"></p>
            <!-- JavaScript will dynamically update the total here -->
        </div>
        <div class="checkout-buttons">
            <button type="submit" class="pay-button">Pay</button>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="continue-shopping-button">Continue Shopping</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
