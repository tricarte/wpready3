<?php
/**
 * Add an admin menu link to options.php.
 */
function wpr_admin_menu_link_to_options() {
    add_submenu_page( 'options-general.php', 'Settings', 'All', 'manage_options', 'options.php', NULL, 0 );
}
add_action( 'admin_menu', 'wpr_admin_menu_link_to_options' );
