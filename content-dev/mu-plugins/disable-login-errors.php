<?php
// Override login error messages
function login_error_override()
{
    return 'Incorrect login details.';
}
add_filter('login_errors', 'login_error_override');

// Disable error shake
function my_login_footer() {
    remove_action('login_footer', 'wp_shake_js', 12);
}
add_action('login_footer', 'my_login_footer');
