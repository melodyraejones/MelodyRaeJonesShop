<?php get_header(); ?>

<div id="primary" class="content-area full-width-height">
    <main id="main" class="site-main full-width-height">
        <?php
        if (!have_posts()) {
            echo 'No posts found.';
        } else {
            while (have_posts()) : the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                    </header>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php
            endwhile;
        }
        ?>
    </main>
</div>

<?php get_footer(); ?>
