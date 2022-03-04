<?php
/**
 * Redirect to homepage in the case of an empty search
 */
add_filter( 'posts_search', function( $search, \WP_Query $q )
{
    if( ! is_admin() && empty( $search ) && $q->is_search() && $q->is_main_query() ) {
        wp_redirect( home_url() );
        exit;
    }

    return $search;
}, 10, 2 );
