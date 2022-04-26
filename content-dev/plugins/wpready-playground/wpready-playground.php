<?php
/*
Plugin Name: WPREADY Playground
Plugin URI:
Description: A playground URL ( /playground ) that you can use as a virtual page.
Version: 0.1.0
Author: tricarte
Author URI: https://github/tricarte
License:
License URI:
*/

// http://local.test/playground
add_filter( 'generate_rewrite_rules', function ( $wp_rewrite ) {
    $wp_rewrite->rules = array_merge(
        ['playground/?$' => 'index.php?playground=1'],
        $wp_rewrite->rules
    );
} );

add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'playground';
    return $query_vars;
} );

add_action( 'template_redirect', function() {
    $playground = intval( get_query_var( 'playground' ) );
    if ( $playground ) {
        echo 'Edit plugins/wpready-playground/wpready-playground.php file to modify this content.';

?>
<h2>Here are all the constants that are defined:</h2>
    <pre><?php
        print_r(get_defined_constants());
?>
    </pre><?php
        die;
    }
} );

// Add menu link to the playground
function playground_menu_link() {
    add_menu_page( 'Playground', 'Playground', 'edit_posts', WP_HOME . '/playground', '', 'dashicons-editor-code', 22 );
}
add_action( 'admin_menu', 'playground_menu_link' );

function playground_flush() {
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'playground_flush' );
register_deactivation_hook( __FILE__, 'playground_flush' );
