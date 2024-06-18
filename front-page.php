<?php 
/*
Template Name: Shop
*/

get_header(); 
?>

<section class="section-programs">
    <div class="header-cart">
        <div class="notification" id="notification"></div>
        <a href="<?php echo esc_url( home_url( '/shop/cart/' ) ); ?>" class="cart-link">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-badge">0</span>
        </a>
    </div>
    
    <div class="shop-section">
        <div class="shop-text-box">
            <h1 class="heading-primary">STORE ITEMS</h1>
            <div>
                <div>
                    <h3 class="shop-list">Soul Notes Meditations</h3>
                    <a href="" class="shop-link"><i class="fas fa-pray"></i> Daily Practices Series's</a>       
                </div>
                <a href="<?php echo esc_url(home_url('/mels-favorite')); ?>" class="shop-link shop-link-faves"><i class="fas fa-heart"></i> Mel's Faves</a>       
                <div>
                    <h3 class="shop-list">Online Programs</h3>
                    <a href="<?php echo esc_url(home_url('/the-expand-your-wisdom-toolkit')); ?>" class="shop-link">
                      <i class="fas fa-book-open"></i> Expand Your Wisdom Toolkit
                    </a>             
                    
                    <div>

                </div>
    <?php 
    if (is_user_logged_in()) { // Check if user is logged in
        // Use get_page_by_title to fetch the page created with the specific template
        $page = get_page_by_title('Audio Files');
        if ($page) { // Check if the page exists
            $page_url = get_permalink($page->ID);
            ?>
            <div class="my-programs-access">
                <a class="my-programs" href="<?php echo esc_url($page_url); ?>">
                    <span class="dashicons dashicons-welcome-learn-more my-programs-icon"></span>
                    My Programs
                </a>
            </div>
            <?php
        } else {
            echo '<a href="#">Page not found</a>'; // Fallback link or message if page does not exist
        }
    }
    ?>
</div>


            </div>
            <div class="site-header__util">
        <?php if(is_user_logged_in()) {?>

          <a href="<?php echo wp_logout_url(); ?>" class="btn btn--small btn-out">
          <!-- <span class="site-header__avatar"><?php echo get_avatar(get_current_user_id(),60); ?></span> -->
          <span class="btn__text logout-btn">Log Out</span>
        </a>
        <?php } else{?>
          <a href="<?php echo wp_login_url(); ?>" class="btn btn--small btn--purple">Login</a>
          <a href="<?php echo wp_registration_url(); ?>" class="btn btn--small btn--dark-purple">Sign Up</a>
        <?php }?>
       
   
      </div>
        </div>
        <div class="shop-items container grid grid--3-cols">
            <?php 
            $args = array(
                'post_type' => 'program',
                'posts_per_page' => -1  // Retrieve all posts
            );
            $program_query = new WP_Query($args);

            if ($program_query->have_posts()) : 
                while ($program_query->have_posts()) : $program_query->the_post();
                    $program_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
                    $program_price = get_post_meta(get_the_ID(), 'program_price', true);
                    
                    // Add data attribute if the program is part of Mel's Faves
                    $is_fav = has_term('mels-faves', 'program_category', get_the_ID());
                    ?>
                   <div class="program" data-id="<?php echo get_the_ID(); ?>" <?php echo $is_fav ? 'data-is-fav="true"' : ''; ?>>
                        <img src="<?php echo esc_url($program_image); ?>" class="program-img" alt="<?php the_title(); ?>" />
                        <div class="program-content">
                            <p class="program-title"><?php the_title(); ?></p>
                          
                            <p class="program-price">Price: $<?php echo esc_html(number_format((float)$program_price, 2, '.', '')); ?></p>

                            <div class="button-container">
                        
                                <div class="icons">
                                <!-- <span class="dashicons dashicons-plus add_to_cart" data-id="<?php echo get_the_ID(); ?>"></span> -->
                                <a href="#" class="btn add_to_cart" data-id="<?php echo get_the_ID(); ?>">+</a>
                               <span class="product-quantity">0</span>
                                <!-- <span class="dashicons dashicons-minus remove_from_cart" data-id="<?php echo get_the_ID(); ?>"></span> -->
                                <a href="#" class="btn remove_from_cart" data-id="<?php echo get_the_ID(); ?>">-</a>
                            </div>
                                <a href="<?php the_permalink(); ?>" class="btn btn--full details">More Details</a>

                                <!-- <a href="#" class="btn btn--full add_to_cart" data-id="<?php echo get_the_ID(); ?>">Add to Cart</a> -->
                                <!-- <a href="#" class="btn btn--full remove_from_cart" data-id="<?php echo get_the_ID(); ?>">Remove</a> -->
                            </div>
                        </div>
                    </div>
                    
                    <?php 
                endwhile;
                wp_reset_postdata(); 
            endif;
            ?>
        </div>
    </div>
</section>

<?php 

get_footer(); 
?>
