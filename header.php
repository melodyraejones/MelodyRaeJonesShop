<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <link rel="icon" href="<?php echo get_theme_file_uri('/images/mrj_logo.png'); ?>" type="image/x-icon" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <?php wp_head(); ?>
</head>
<body>
  <header class="main-header">
    <!-- Logo -->
    <div class="header-upper">
      <div class="auto-container">
        <div class="clearfix">
          <div align="center">
            <div class="logo">
              <?php if (get_theme_mod('mrj_logo')) : ?>
                <img src="<?php echo esc_url(get_theme_mod('mrj_logo')); ?>" alt="company-logo">
              <?php else : ?>
                <img src="<?php echo get_theme_file_uri('./images/logo_white.png'); ?>" alt="company-logo">
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Logo -->
    <!-- Header Lower -->
    <div class="header-lower">
      <div class="auto-container clearfix">
        <div class="nav-outer clearfix">
          <!-- Main Menu -->
          <nav class="main-menu">
            <div class="navbar-header">
              <!-- Toggle Button -->
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </div>
            <div class="navbar-collapse collapse clearfix">
              <ul class="navigation clearfix">
                <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                <li class="dropdown"><a href="#">About</a>
                  <ul>
                    <!-- <li><a href="https://melodyraejones.com/offerings/main.html">Meet Melody</a></li>
                    <li><a href="https://melodyraejones.com/about/approach.html">My Approach</a></li>
                    <li><a href="https://melodyraejones.com/about/philosophy.html">My Philosophy</a></li> -->
                    <?php
                      // Add dynamic submenu items under About
                      $about_submenu = get_theme_mod('mrj_about_submenu', '');
                      if (!empty($about_submenu)) {
                          $submenu_items = explode(',', $about_submenu);
                          foreach ($submenu_items as $submenu_item) {
                              list($url, $label) = explode('|', $submenu_item);
                              echo '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
                          }
                      }
                    ?>
                  </ul>
                </li>
                <li><a href="https://melodyraejones.com/offerings/main.html">Offerings</a></li>
                <li><a href="https://melodyraejones.com/events/upcoming.html">Events</a></li>
                <li class="dropdown current"><a href="#">Products</a>
                  <ul>
                    <!-- <li><a href="https://melody-rae-jones-consulting.square.site/">Online Meditations</a></li>
                    <li><a href="products/online_programs.html">Online Programs</a></li>
                    <li><a href="products/free_resources.html">Free Resources</a></li>
                    <li><a href="products/courses/protect/expand_wisdom-login.html" target="_blank">Course Login</a></li> -->
                    <?php
                      // Add dynamic submenu items under Products
                      $products_submenu = get_theme_mod('mrj_products_submenu', '');
                      if (!empty($products_submenu)) {
                          $submenu_items = explode(',', $products_submenu);
                          foreach ($submenu_items as $submenu_item) {
                              list($url, $label) = explode('|', $submenu_item);
                              echo '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
                          }
                      }
                    ?>
                  </ul>
                </li>
                <li><a href="https://melodyraejones.com/ecards/inspirations.html">e-Cards</a></li>
                <li><a href="https://melodyraejones.com/testimonials.html">Praise</a></li>
                <li><a href="https://melodyraejones.com/blog/articles.html">Blog</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="https://melodyraejones.com/members/login.html" target="_blank">Member Login</a></li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
    </div>
    <button class="btn-mobile-nav"><span class="dashicons dashicons-menu icon-mobile-navigation" name="menu"></span><span class="dashicons dashicons-no-alt close-menu" name="close-menu"></span></button>
  </header>
  <?php wp_footer(); ?>
</body>
</html>