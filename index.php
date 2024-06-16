<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main full-width-height">

    <?php

    if (!have_posts()) {
        echo 'No posts found.';
    } else {
        while (have_posts()) : the_post();

            the_content();

        endwhile;
    }
    ?>

    </main>
</div>

<?php get_footer(); ?>
