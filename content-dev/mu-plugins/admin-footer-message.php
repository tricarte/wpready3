<?php
function wpr_admin_footer_message ()
{
    // echo '<span id="footer-thankyou">Developed by <a href="http://www.designerswebsite.com" target="_blank">Your Name</a></span>';
    echo '<span id="footer-thankyou"><a href="' . get_bloginfo('url') .'" target="_blank">'. get_bloginfo('name') .'</a></span>';
}
add_filter('admin_footer_text', 'wpr_admin_footer_message');
