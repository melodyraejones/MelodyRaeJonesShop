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
            font-family: "Arimo", sans-serif;
            line-height: 1.6;
            background-color: #ffffff; /* Optional: set a background color */
        
        }
        h1 {
            font-family: "AvanteGarde", sans-serif;
            font-size: 24px;
            color: #6a9c07;
            margin-top: 20px; /* Add some space above the title */
            text-align: center; /* Center align the title */
        }
        p, div, span, a {
            font-family: "Arimo", sans-serif;
            font-size: 18px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .site-header, .site-footer {
            margin: 0 auto;
            width: 90%;
        }
        .site-content {
            flex: 1;
            padding: 20px;
            margin: 0 auto;
            width: 90%;
        }
        /* Optional: Add additional styling as needed */
    </style>
</head>
<body <?php body_class(); ?>>
    <?php get_header(); ?>

    <div id="content" class="site-content">
        <main id="main" class="site-main" role="main">
            <?php
            while (have_posts()) :
                the_post();
                echo '<h1>' . get_the_title() . '</h1>';
                the_content();
            endwhile;
            ?>
        </main>
    </div>

    <?php get_footer(); ?>

    <?php wp_footer(); ?>
</body>
</html>
