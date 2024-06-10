<?php get_header(); ?>

<!-- Ensure we are in the PHP mode before the loop -->
<?php while (have_posts()) : the_post(); ?>
    <!-- HTML output needs to be outside of PHP tags or echoed -->
   <!-- Fixed the_title() call, ensure it is closed properly -->
    <?php the_content(); ?>
<?php endwhile; ?>

<?php get_footer(); ?>
