<?php
function mrj_enqueue_styles() {
    wp_enqueue_style('mrj-main-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'mrj_enqueue_styles');

function mrj_customize_register($wp_customize) {
    // Add Section for Header
    $wp_customize->add_section('mrj_header_section', array(
        'title' => __('Header Settings', 'mrj_theme'),
        'priority' => 30,
    ));
    
    // Add Setting for Logo
    $wp_customize->add_setting('mrj_logo', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    // Add Control for Logo
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'mrj_logo', array(
        'label' => __('Upload Logo', 'mrj_theme'),
        'section' => 'mrj_header_section',
        'settings' => 'mrj_logo',
    )));
    
    // Add Setting for Header Background Color
    $wp_customize->add_setting('mrj_header_bg_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    // Add Control for Header Background Color
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mrj_header_bg_color', array(
        'label' => __('Header Background Color', 'mrj_theme'),
        'section' => 'mrj_header_section',
        'settings' => 'mrj_header_bg_color',
    )));
    
    // Add Setting for Header Text Color
    $wp_customize->add_setting('mrj_header_text_color', array(
        'default' => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    // Add Control for Header Text Color
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mrj_header_text_color', array(
        'label' => __('Header Text Color', 'mrj_theme'),
        'section' => 'mrj_header_section',
        'settings' => 'mrj_header_text_color',
    )));

    // Add Setting for Header Hover Color
    $wp_customize->add_setting('mrj_header_hover_color', array(
        'default' => '#6a9c07',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    // Add Control for Header Hover Color
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mrj_header_hover_color', array(
        'label' => __('Header Hover Color', 'mrj_theme'),
        'section' => 'mrj_header_section',
        'settings' => 'mrj_header_hover_color',
    )));

    // Add Setting for About Submenu
    $wp_customize->add_setting('mrj_about_submenu', array(
        'default' => 'https://melodyraejones.com/offerings/main.html|Meet Melody,https://melodyraejones.com/about/approach.html|My Approach,https://melodyraejones.com/about/philosophy.html|My Philosophy',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    // Add Control for About Submenu
    $wp_customize->add_control('mrj_about_submenu', array(
        'label' => __('About Submenu Items (comma separated URLs and Labels)', 'mrj_theme'),
        'section' => 'mrj_header_section',
        'settings' => 'mrj_about_submenu',
        'type' => 'textarea',
    ));

    // Add Setting for Products Submenu
    $wp_customize->add_setting('mrj_products_submenu', array(
        'default' => 'https://melody-rae-jones-consulting.square.site/|Online Meditations,products/online_programs.html|Online Programs,products/free_resources.html|Free Resources,products/courses/protect/expand_wisdom-login.html|Course Login',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    // Add Control for Products Submenu
    $wp_customize->add_control('mrj_products_submenu', array(
        'label' => __('Products Submenu Items (comma separated URLs and Labels)', 'mrj_theme'),
        'section' => 'mrj_header_section',
        'settings' => 'mrj_products_submenu',
        'type' => 'textarea',
    ));
    
    // Add Section for Footer
    $wp_customize->add_section('mrj_footer_section', array(
        'title' => __('Footer Settings', 'mrj_theme'),
        'priority' => 40,
    ));
    
    // Add Setting for Footer Background Color
    $wp_customize->add_setting('mrj_footer_bg_color', array(
        'default' => '#643482',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    // Add Control for Footer Background Color
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mrj_footer_bg_color', array(
        'label' => __('Footer Background Color', 'mrj_theme'),
        'section' => 'mrj_footer_section',
        'settings' => 'mrj_footer_bg_color',
    )));
    
    // Add Setting for Footer Text Color
    $wp_customize->add_setting('mrj_footer_text_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    // Add Control for Footer Text Color
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mrj_footer_text_color', array(
        'label' => __('Footer Text Color', 'mrj_theme'),
        'section' => 'mrj_footer_section',
        'settings' => 'mrj_footer_text_color',
    )));
    
    // Add Setting for Footer Bottom Background Color
    $wp_customize->add_setting('mrj_footer_bottom_bg_color', array(
        'default' => '#643482',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    // Add Control for Footer Bottom Background Color
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mrj_footer_bottom_bg_color', array(
        'label' => __('Footer Bottom Background Color', 'mrj_theme'),
        'section' => 'mrj_footer_section',
        'settings' => 'mrj_footer_bottom_bg_color',
    )));
    
    // Add Setting for Footer Bottom Text Color
    $wp_customize->add_setting('mrj_footer_bottom_text_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    // Add Control for Footer Bottom Text Color
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mrj_footer_bottom_text_color', array(
        'label' => __('Footer Bottom Text Color', 'mrj_theme'),
        'section' => 'mrj_footer_section',
        'settings' => 'mrj_footer_bottom_text_color',
    )));
    
    // Add Setting for Sign Me Up Button Background Color
    $wp_customize->add_setting('mrj_signup_button_bg_color', array(
        'default' => '#562973',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    // Add Control for Sign Me Up Button Background Color
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mrj_signup_button_bg_color', array(
        'label' => __('Sign Me Up Button Background Color', 'mrj_theme'),
        'section' => 'mrj_footer_section',
        'settings' => 'mrj_signup_button_bg_color',
    )));
    
    // Add Setting for Sign Me Up Button Link Color
    $wp_customize->add_setting('mrj_signup_button_link_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    // Add Control for Sign Me Up Button Link Color
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mrj_signup_button_link_color', array(
        'label' => __('Sign Me Up Button Link Color', 'mrj_theme'),
        'section' => 'mrj_footer_section',
        'settings' => 'mrj_signup_button_link_color',
    )));
}

add_action('customize_register', 'mrj_customize_register');

function mrj_customizer_css() {
    $header_bg_color = get_theme_mod('mrj_header_bg_color', '#ffffff');
    $header_text_color = get_theme_mod('mrj_header_text_color', '#000000');
    $header_hover_color = get_theme_mod('mrj_header_hover_color', '#6a9c07');
    $footer_bg_color = get_theme_mod('mrj_footer_bg_color', '#643482');
    $footer_text_color = get_theme_mod('mrj_footer_text_color', '#ffffff');
    $footer_button_color = get_theme_mod('mrj_footer_button_color', '#643482');
    $footer_bottom_bg_color = get_theme_mod('mrj_footer_bottom_bg_color', '#643482');
    $footer_bottom_text_color = get_theme_mod('mrj_footer_bottom_text_color', '#ffffff');
    $signup_button_bg_color = get_theme_mod('mrj_signup_button_bg_color', '#562973');
    $signup_button_link_color = get_theme_mod('mrj_signup_button_link_color', '#ffffff');
    
    $custom_css = "
        .header-lower {
            background-color: {$header_bg_color} !important;
        }
        header.main-header, 
        header.main-header a, 
        header.main-header .navigation > li > a {
            color: {$header_text_color} !important;
        }
        .main-menu .navigation > li > ul > li > a {
            background-color: {$header_bg_color} !important;
        }
        .main-menu .navigation > li > ul > li:hover > a {
            background-color: {$header_hover_color} !important;
        }
        .main-menu .navigation > li > ul {
            background-color: {$header_bg_color} !important;
        }
        .main-menu .navigation > li > ul > li {
            background-color: {$header_bg_color} !important;
        }
        footer.main-footer {
            background-color: {$footer_bg_color} !important;
            color: {$footer_text_color} !important;
        }
        .btn.btn--full {
            color: {$header_text_color} !important;
            background-color: {$header_bg_color} !important;
        }
        .back-button a {
            color: {$header_bg_color} !important;
        }
        .back-button a:hover {
            color: {$header_hover_color} !important;
        }
        footer.main-footer a,
        footer.main-footer .contact-info li .icon,
        footer.main-footer .list li:before,
        footer.main-footer .social2 a,
        footer.main-footer .footer-widget h2,
        footer.main-footer .contact-info li,
        footer.main-footer .contact-info li a {
            color: {$footer_text_color} !important;
        }
        .theme-btn.btn-style-two {
            background-color: {$signup_button_bg_color} !important;
            color: {$signup_button_link_color} !important;
        }
        .theme-btn.btn-style-two:hover {
            color: #ffffff !important;
        }
        .footer-bottom {
            background-color: {$footer_bottom_bg_color} !important;
            color: {$footer_bottom_text_color} !important;
        }
        .footer-bottom a {
            color: {$footer_bottom_text_color} !important;
        }
        div.footer-bottom h4 {
            color: {$footer_bottom_text_color} !important;
        }
    ";
    
    wp_add_inline_style('mrj-main-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'mrj_customizer_css');
?>