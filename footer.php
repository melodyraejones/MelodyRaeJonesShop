<?php
enqueue_footer_styles();
?>

<footer class="main-footer">
    <div class="auto-container">
        <div class="footer-row">
            <div class="footer-column">
                <div class="footer-widget links-widget">
                    <h2>Connect With Me</h2>
                    <div class="widget-content" style="margin-left:15px">
                        <div class="footer-row clearfix">
                            <ul class="contact-info">
                                <li class="address"><span class="icon fa fa-map-marker"></span>
                                    Location: <br />
                                    Regina, Saskatchewan Canada
                                </li>
                                <li style="margin-bottom:10px"><span class="icon fa fa-volume-control-phone"></span>
                                    Phone (Voice Only &ndash; No Texting): <br />
                                    306.757.9300
                                </li>
                                <li><span class="icon fa fa-envelope"></span>
                                    <a href="mailto:melody@melodyraejones.com" style="color:#fff">Email: <br />
                                    melody@melodyraejones.com</a>
                                </li>                            
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-column">
                <div class="footer-widget links-widget">
                    <h2>Quick Links</h2>
                    <div class="widget-content">
                        <ul class="list" style="margin-left:10px">
                            <?php
                            $quick_links = get_theme_mod('mrj_quick_links', ''); 
                            $quick_links = explode(',', $quick_links);
                            foreach ($quick_links as $link) {
                                list($url, $label) = explode('|', $link);
                                echo '<li><a href="' . esc_url($url) . '" target="_blank">' . esc_html($label) . '</a></li>';
                            }
                            ?>
                        </ul>                                        
                    </div>
                </div>
            </div>
            <div class="footer-column">
                <div class="footer-widget social-widget clearfix">
                    <h2>Follow Me</h2>
                    <div class="text">
                        <div class="social2" style="margin-top:-5px"><br />
                            <?php
                            $social_links = array(
                                'facebook' => get_theme_mod('mrj_facebook_link', ''),
                                'youtube' => get_theme_mod('mrj_youtube_link', ''),
                                'instagram' => get_theme_mod('mrj_instagram_link', '')
                            );
                            if ($social_links['facebook']) {
                                echo '<a href="' . esc_url($social_links['facebook']) . '" title="Find me on Facebook" target="_blank" style="text-decoration:none"><i class="icon-facebook-squared"></i></a>';
                            }
                            if ($social_links['youtube']) {
                                echo '<a href="' . esc_url($social_links['youtube']) . '" title="Check out my Youtube Channel" target="_blank" style="text-decoration:none"><i class="icon-youtube"></i></a>';
                            }
                            if ($social_links['instagram']) {
                                echo '<a href="' . esc_url($social_links['instagram']) . '" title="Follow me on Instagram" target="_blank" style="text-decoration:none"><i class="icon-instagram"></i></a>';
                            }
                           
                            ?>
                        </div>
                    </div>
                </div>
                <div class="footer-widget newsletter-widget">
                    <h2 style="margin-bottom:15px">Join My Community</h2>
                    <div class="widget-content">
                        <div class="newsletter-one">
                            <div class="inner-box" align="left">
                                <a href="https://visitor.r20.constantcontact.com/d.jsp?llr=ioa4ttbab&amp;p=oi&amp;m=ioa4ttbab&amp;sit=cn4ee6hbb&amp;f=44cd2606-9177-4d92-bc18-84b9969481d6" class="theme-btn btn-style-two" target="_blank">Sign Me Up!</a>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>                
        </div>
    </div>
    <div class="footer-bottom">
        <div class="auto-container">
            <div class="copyright-text">
                <h4>Copyright &copy; 2006 &ndash; <script language="JavaScript" type="text/javascript">document.write((new Date()).getFullYear());</script> Melody Rae Jones &ndash; All Rights Reserved<br />
                    <a href="https://melodyraejones.com/disclaimer.html" target="_blank">Disclaimer</a></h4>
            </div>
        </div>
    </div>
    <div class="scroll-to-top scroll-to-target" data-target="html"><span class="icon fa fa-angle-double-up"></span></div>
</footer>

<?php wp_footer();?>

</body>
</html>
