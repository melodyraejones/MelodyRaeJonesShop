<?php
/*
Template Name: Expand Wisdom Toolkit
*/
get_header();

// Enqueue the custom CSS file for this template
wp_enqueue_style('expand-wisdom-toolkit', get_template_directory_uri() . '/css/program-details.css');

// ID of the page with the content
$page_id = 984;

// Fetch the page content
$page = get_post($page_id);

// Get the featured image URL
$featured_image_url = get_the_post_thumbnail_url($page_id, 'full');

// Get the product price from ACF field
$product_price = get_field('program_price', $page_id);
?>

<section class="program-details">
    <div class="header-cart detailed-cart">
        <a href="<?php echo esc_url(home_url('/shop/cart/')); ?>">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-badge">0</span>
        </a>
    </div>
    <div class="product grid grid--2-cols">
        <div class="product-image-box product-image-box-wisdom ">
            <?php if ($featured_image_url) : ?>
                <img class="product-img" src="<?php echo esc_url($featured_image_url); ?>" alt="<?php echo esc_attr($page->post_title); ?>">
            <?php else : ?>
                <img class="product-img" src="<?php echo get_theme_file_uri('./images/mel_faves.png'); ?>" alt="Expand Your Wisdom Toolkit">
            <?php endif; ?>
            <?php if ($product_price) : ?>
                <p class="product-price wisdom-program-price">Price: $<?php echo esc_html(number_format((float)$product_price, 2)); ?></p> 
            <?php endif; ?>
        </div>
        <div class="product-details-box">
            <h3 class="heading-primary heading-wisdom"><?php echo esc_html($page->post_title); ?></h3>
            <div class="product-description wisdom-description">
                <?php echo apply_filters('the_content', $page->post_content); ?>
            </div>
            <?php if ($product_price) : ?>
                <a href="#" class="btn btn--full btn-details add_to_cart_details" data-id="<?php echo $page_id; ?>" data-price="<?php echo esc_attr($product_price); ?>">Add to Cart</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
get_footer();
?>
