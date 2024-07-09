

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <link rel="icon" data-src="<?php echo get_theme_file_uri('/images/mrj_icon.png'); ?>" type="image/x-icon" />
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
              <img data-src="<?php echo get_theme_file_uri('./images/logo_white.png'); ?>" alt="company-logo" class="lazyload">
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
                <?php
                // Fetch main menu items
                $main_menu_items = get_theme_mod('mrj_main_menu_items', 'https://melodyraejones.com/|Home,melody.html|About,https://melodyraejones.com/offerings/main.html|Offerings,https://melodyraejones.com/events/upcoming.html|Products,|Events,https://melodyraejones.com/ecards/inspirations.html|e-Cards,https://melodyraejones.com/testimonials.html|Praise,https://melodyraejones.com/blog/articles.html|Blog,contact.html|Contact,https://melodyraejones.com/members/login.html|Member Login');
                $menu_items = explode(',', $main_menu_items);
                foreach ($menu_items as $item) {
                  list($url, $label) = explode('|', $item);
                  // Check for About and Products dropdowns
                  if (strpos($label, 'About') !== false) {
                    echo '<li class="dropdown"><a href="' . esc_url($url) . '">' . esc_html($label) . '</a><ul>';
                    // Fetch About submenu items
                    $about_submenu_items = get_theme_mod('mrj_about_submenu', 'https://melodyraejones.com/offerings/main.html|Meet Melody,https://melodyraejones.com/about/approach.html|My Approach,https://melodyraejones.com/about/philosophy.html|My Philosophy');
                    $about_submenu_items = explode(',', $about_submenu_items);
                    foreach ($about_submenu_items as $sub_item) {
                      list($sub_url, $sub_label) = explode('|', $sub_item);
                      echo '<li><a href="' . esc_url($sub_url) . '">' . esc_html($sub_label) . '</a></li>';
                    }
                    echo '</ul></li>';
                  } elseif (strpos($label, 'Products') !== false) {
                    echo '<li class="dropdown current"><a href="#">' . esc_html($label) . '</a><ul>'; // <--- Add 'current' class here
                    // Fetch Products submenu items
                    $products_submenu_items = get_theme_mod('mrj_products_submenu', 'https://melody-rae-jones-consulting.square.site/|Online Meditations,products/online_programs.html|Online Programs,products/free_resources.html|Free Resources,products/courses/protect/expand_wisdom-login.html|Course Login');
                    $products_submenu_items = explode(',', $products_submenu_items);
                    foreach ($products_submenu_items as $sub_item) {
                      list($sub_url, $sub_label) = explode('|', $sub_item);
                      echo '<li><a href="' . esc_url($sub_url) . '">' . esc_html($sub_label) . '</a></li>';
                    }
                    echo '</ul></li>';
                  } else {
                    echo '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
                  }
                }
                ?>
              </ul>
            </div>
          </nav>
        </div>
      </div>
    </div>       
    <button class="btn-mobile-nav icon-mobile-navigation"><span class="dashicons dashicons-menu" name="menu"></span><span class="dashicons dashicons-no-alt close-menu" name="close-menu"></span></button>
  </header>
</body>
</html>

