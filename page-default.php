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
        /* Scoped Reset Styles for Default Page Template */
        .default-page * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .default-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: "Arimo", sans-serif;
            line-height: 1.6;
            background-color: #ffffff; /* Optional: set a background color */
        }

        .default-page h1 {
            font-family: "Courgette", sans-serif;
            font-size: 30px;
            color: #562973;
            margin-top: 20px; /* Add some space above the title */
            text-align: center; /* Center align the title */
        }

        .default-page p, .default-page div, .default-page span, .default-page a {
            font-family: "Arimo", sans-serif;
            font-size: 18px;
            color: #333;
        }

        .default-page .site-header, .default-page .site-footer {
            margin: 0 auto;
            width: 90%;
        }

        .default-page .site-content {
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

    <div id="content" class="default-page site-content">
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
