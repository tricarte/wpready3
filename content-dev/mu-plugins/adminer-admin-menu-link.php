<?php
/**
 * Top level menu link to Adminer
 */
function adminer_menu_link() {
    add_menu_page( 'Adminer', 'Adminer', 'edit_posts', WP_HOME . '/adminer/?username=root&db=' . get_bloginfo( 'name' ) , '', 'dashicons-editor-table', 22 );
}
add_action( 'admin_menu', 'adminer_menu_link' );
