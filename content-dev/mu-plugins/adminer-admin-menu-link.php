<?php
/**
 * Top level menu link to Adminer
 */
function adminer_menu_link() {
    if ( defined('WP_ENVIRONMENT_TYPE') && 'development' == WP_ENVIRONMENT_TYPE && is_dir(ABSPATH . '../adminer')) {
        add_menu_page( 'Adminer', 'Adminer', 'edit_posts', WP_HOME . '/adminer/?username=admin&db=' . get_bloginfo( 'name' ) , '', 'dashicons-database', 22 );
    }
}
add_action( 'admin_menu', 'adminer_menu_link' );
