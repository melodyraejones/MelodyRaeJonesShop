<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name ="viewport" content ="width=device-width, initial-scale=1.0" />
    <?php wp_head(); ?>   
</head>
<body>
  <!-- <div class="company-logo">
    <h1>
      <a href="https://melodyraejones.com/">
        <img src="<?php echo get_theme_file_uri('./images/logo_white.png'); ?>" alt="logo">
      </a>
    </h1>
  </div> -->
  <header class="main-header">       
        
        <!-- Logo -->
        <div class="header-upper">
            <div class="auto-container">
                <div class="clearfix">                    
                  	<div align="center">
						<div class="logo">
						<img src="<?php echo get_theme_file_uri('./images/logo_white.png'); ?>" alt="company-logo" ></div>
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
                            <span class="icon-bar"></span>                            </button>
                        </div>
                       
                        <div class="navbar-collapse collapse clearfix">
                            <ul class="navigation clearfix">
                                <li class="current"><a href="index.html">Home</a>
                                <li>
                                <li class="dropdown"><a href="melody.html">About</a>
                                    <ul>
                                        <li><a href="https://melodyraejones.com/offerings/main.html">Meet Melody</a></li>
                                        <li><a href="https://melodyraejones.com/about/approach.html">My Approach</a></li>
                                        <li><a href="https://melodyraejones.com/about/philosophy.html">My Philosophy</a></li>
                                    </ul>
                              </li>
                                <li><a href="https://melodyraejones.com/offerings/main.html">Offerings</a></li>
                                <li><a href="https://melodyraejones.com/events/upcoming.html">Events</a></li>
                                <li class="dropdown"><a href="products/free_resources.html">Products</a>
                                    <ul>
                                        <li><a href="https://melody-rae-jones-consulting.square.site/">Online Meditations</a></li>
                                        <li><a href="products/online_programs.html">Online Programs</a></li>
                                        <li><a href="products/free_resources.html">Free Resources</a></li>
										<li><a href="products/courses/protect/expand_wisdom-login.html" target="_blank">Course Login</a></li>
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
</body>
</html>
