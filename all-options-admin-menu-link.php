<?php
/**
 * Add an admin menu link to options.php.
 */
function wpr_admin_menu_link_to_options() {
    // add_menu_page( 'All', 'Settings', 'manage_options', 'options.php', '', 'dashicons-editor-table', 22 );
    add_menu_page( 'options-general.php', 'Settings', 'All', 'manage_options', 'all-settings' );
}
add_action( 'admin_menu', 'wpr_admin_menu_link_to_options' );
