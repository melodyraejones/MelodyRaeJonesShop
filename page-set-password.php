<?php
/*
Template Name: Set Password
*/
get_header();
?>
<section class="section-cta">
    <div class="container">
    <div class="cta-contact">
    <div class="cta-text-box-contact">
    <h2 class="heading-secondary">Reset Your Password</h2>
    <form class="cta-form" action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
        <input type="hidden" name="action" value="reset_password">
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label for="password">New Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="button">Reset Password</button>
    </form>
</div>
<div class="cta-image-box" role="img" aria-label="Woman meditating"></div>
</div>
</div>
</section>
<?php
get_footer();
?>

