<?php
/*
Template Name: Contact Page
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
                        <label for="full-name">Full Name:</label>
                        <input id="full-name" name="full-name" type="text" placeholder="John Smith" required/>
                    </div>
                    <div>
                        <label for="email">Email Address:</label>
                        <input id="email" name="email" type="email" placeholder="me@example.com" required/>
                    </div>
                    <div>
                        <label for="select-where">Where did you hear about us?</label>
                        <select id="select-where" name="select-where" required>
                            <option value="">Please choose one option</option>
                            <option value="Social Media">Social Media</option>
                            <option value="Meditation Session">Meditation Session</option>
                            <option value="Friends And Family">Friends And Family</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div>
                        <label for="message">Message:</label>
                        <textarea id="message" name="message" placeholder="Your message here..." rows="10" required></textarea>
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

