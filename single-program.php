<?php
get_header();

while ( have_posts() ) : the_post();
$product_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
$product_price = get_post_meta(get_the_ID(), 'program_price', true);
$product_length = get_post_meta(get_the_ID(), 'audio_length', true);
$product_id = get_the_ID();
?>

<section class="program-details">
<div class="header-cart detailed-cart">
    <a href="<?php echo esc_url( home_url( '/shop/cart/' ) ); ?>">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-badge">0</span>
        </a>
    </div>
    <div class="back-button">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <i class="fas fa-arrow-left"></i> Back to Homepage
                </a>
            </div>
    <div class="product grid grid--2-cols" data-id="<?php echo get_the_ID(); ?>">
        <div class="product-image-box">
            <img class="product-img" src="<?php echo esc_url($product_image_url); ?>" alt="<?php the_title_attribute(); ?>">
            <div class="product-price">
                <span class="price-amount">Price: $<?php echo esc_html(number_format((float)$product_price, 2)); ?></span>
            </div>
            <p class="product-availability">Available as a MP3 Download</p>
            <p class="product-length">Length: <?php echo esc_html($product_length); ?></p> 
        </div>
        <div class="product-details-box">
            <h1 class="heading-primary"><?php the_title(); ?></h1>
            
            <div class="product-description">
                <?php the_content(); ?>
            </div>
           
            <a href="#" class="btn btn--full btn-details add_to_cart_details" data-id="<?php echo get_the_ID(); ?>" data-price="<?php echo $product_price; ?>">Add to Cart</a>
        </div>
    </div>
</section>

<?php
endwhile;
get_footer();
?>
