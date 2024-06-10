<?php
/*
Template Name:Wisdom Toolkit Login Page
*/
get_header(); 


// Define a variable to hold response messages

?>

<section class="section-cta">
    <div class="container">
        <div class="cta-contact">
            <div class="cta-text-box-contact">
                <h2 class="heading-secondary">I'd love to hear from you!</h2>
                <p class="cta-text">If you have questions, or would like to chat about my programs or services, please reach out!</p>
                <form class="cta-form" method="POST" action="<?php echo admin_url('admin-post.php'); ?>">
                    <input type="hidden" name="action" value="custom_contact_form">
                  
                    <div>
                        <label for="email">Email Address:</label>
                        <input id="email" name="email" type="email" placeholder="me@example.com" required/>
                    </div>
                  
                   
                    <!-- Add Nonce Field -->
                    <?php wp_nonce_field('custom_contact_form_action', 'contact_form_nonce'); ?>
                    <button class="btn-contact">Send</button>
                </form>
            </div>
            <div class="cta-image-box" role="img" aria-label="Woman meditating"></div>
        </div>
    </div>
</section>


<?php
get_footer(); 
?>

