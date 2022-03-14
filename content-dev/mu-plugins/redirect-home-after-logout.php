<?php
add_filter( 'logout_redirect', function() {
    return esc_url( home_url() );
} );
