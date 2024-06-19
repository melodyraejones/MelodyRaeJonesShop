<?php
/*
Template Name: Default Page
*/
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <style>
        /* Reset custom theme styles */
        body, html, .site {
            all: unset;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        /* Apply some basic styling */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: inherit !important;
            color: inherit !important;
            margin: 0;
        }
        p, div, span, a {
            font-family: inherit !important;
            color: inherit !important;
            margin: 0;
            padding: 0;
        }
        /* Ensure no gap from the header */
        .site-header {
            margin: 0;
            padding: 0;
        }
        .site-content {
            flex: 1;  /* Allow the content to grow and fill the available space */
            padding: 20px;  /* Optional, add some padding for the content */
            height: auto !important;  /* Override the height to remove extra space */
            min-height: 0 !important;  /* Override the min-height to remove extra space */
        }
        .site-footer {
            margin: 0;
            padding: 20px;  /* Optional, adjust as needed */
            background-color: #f4f4f4;  /* Optional, add background color for visibility */
        }
    </style>
</head>
<body <?php body_class(); ?>>
    <?php get_header(); ?>

    <div id="content" class="site-content">
        <main id="main" class="site-main" role="main">
            <?php
            while (have_posts()) :
                the_post();
                the_content();
            endwhile;
            ?>
        </main><!-- #main -->
    </div><!-- #content -->

    <?php get_footer(); ?>

    <?php wp_footer(); ?>
</body>
</html>
