<?php
/**
 * Block User Enumeration
 * https://www.kevinleary.net/preventing-possible-attempt-enumerate-users-solved/
 */
function wpr_block_user_enumeration_attempts() {
    if ( is_admin() ) return;

    $author_by_id = ( isset( $_REQUEST['author'] ) && is_numeric( $_REQUEST['author'] ) );

    if ( $author_by_id )
        wp_die( 'Author archives have been disabled.' );
}
add_action( 'redirect_canonical', 'wpr_block_user_enumeration_attempts' );
