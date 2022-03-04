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
