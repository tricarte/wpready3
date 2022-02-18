<?php
/**
 * Remove specified parts from admin bar.
 */
function remove_admin_bar_components() {
    global $wp_admin_bar;
    // WordPress logo.
    $wp_admin_bar->remove_menu( 'wp-logo' );
    // About WordPress link.
    $wp_admin_bar->remove_menu( 'about' );
    // WordPress.org link.
    $wp_admin_bar->remove_menu( 'wporg' );
    // WordPress documentation link.
    $wp_admin_bar->remove_menu( 'documentation' );
    // Support forums link.
    $wp_admin_bar->remove_menu( 'support-forums' );
    // Feedback link.
    $wp_admin_bar->remove_menu( 'feedback' );
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_components' );
