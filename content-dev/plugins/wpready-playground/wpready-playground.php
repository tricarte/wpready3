<?php
/*
Plugin Name: wpready playground
Plugin URI:
Description:
Version:
Author:
Author URI:
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
        echo 'This is your playground when tinkering with WP.';
        die;
    }
} );

// Add menu link to the playground
function playground_menu_link() {
    add_menu_page( 'Playground', 'Playground', 'edit_posts', WP_HOME . '/playground', '', 'dashicons-editor-code', 22 );
}
add_action( 'admin_menu', 'playground_menu_link' );
