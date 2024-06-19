<?php
/*
Template Name: Expand Wisdom Toolkit Discounted
*/
get_header();

// Enqueue the custom CSS file for this template
wp_enqueue_style('expand-wisdom-toolkit', get_template_directory_uri() . '/css/program-details.css');

// Fetch the page ID dynamically by title
$page = get_page_by_title('The Expand Your Wisdom Offer');
$page_id = 1637; // Replace with your actual page ID
// Fetch the page content
$page = get_post($page_id);

// Check if page content is fetched correctly
if (!$page) {
    echo 'Failed to retrieve page content.';
    get_footer();
    exit;
}

// Get the featured image URL
$featured_image_url = get_the_post_thumbnail_url($page_id, 'full');

// Get the discounted price from ACF field on this page
$discounted_price = get_field('program_price', $page_id); // Assuming 'program_price' is the ACF field name for the discounted price

// Get the actual price from the Expand Wisdom Toolkit page
$actual_price_page = get_page_by_title('Expand Wisdom Toolkit'); // Adjust the title to match the exact page title

// Check if the actual price page is retrieved correctly
if ($actual_price_page) {
    $actual_price_page_id = $actual_price_page->ID;
    $actual_price = get_field('program_price', $actual_price_page_id); // Assuming 'program_price' is the ACF field name for the actual price
} else {
    $actual_price = null;
}

// Fallback if actual price is not set
if (!$actual_price) {
    $actual_price = 97; // Default actual price
}
?>

<section class="program-details">
    <div class="notification" id="notification"></div>
    <div class="header-cart detailed-cart">
        <a href="<?php echo esc_url(home_url('/shop/cart/')); ?>">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-badge">0</span>
        </a>
    </div>
    <div class="back-button">
        <a href="<?php echo esc_url(home_url('/')); ?>">
            <i class="fas fa-arrow-left"></i> Back to Homepage
        </a>
    </div>
    <div class="product grid grid--2-cols">
        <div class="product-image-box product-image-box-wisdom">
            <?php if ($featured_image_url) : ?>
                <img class="product-img" src="<?php echo esc_url($featured_image_url); ?>" alt="<?php echo esc_attr($page->post_title); ?>">
            <?php else : ?>
                <img class="product-img" src="<?php echo get_theme_file_uri('./images/mel_faves.png'); ?>" alt="Expand Your Wisdom Toolkit">
            <?php endif; ?>
            <?php if ($actual_price && $discounted_price) : ?>
                <div class="price-container">
                    <p class="price-icon">$</p>
                    <p class="wisdom-program-price original-price" style="text-decoration: line-through;"><?php echo esc_html(number_format((float)$actual_price, 2)); ?></p>
                </div>
                <div class="price-container">
                    <p class="price-icon">$</p>
                    <p class="wisdom-program-price discounted-price">$<?php echo esc_html(number_format((float)$discounted_price, 2)); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <div class="product-details-box">
            <h3 class="heading-primary heading-wisdom"><?php echo esc_html($page->post_title); ?></h3>
            <div class="product-description wisdom-description">
                <?php echo apply_filters('the_content', $page->post_content); ?>
            </div>
            <?php if ($discounted_price) : ?>
                <a href="#" class="btn btn--full btn-details add_to_cart_details" data-id="<?php echo $page_id; ?>" data-price="<?php echo esc_attr($discounted_price); ?>">Add to Cart</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
get_footer();
?>
