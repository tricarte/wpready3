<?php
add_filter( 'image_editor_output_format', function( $formats ) {
    // Write now, using avif for jpegs
    // $formats['image/jpeg'] = 'image/webp';
    $formats['image/png'] = 'image/webp';
    $formats['image/bmp'] = 'image/webp';

    return $formats;
} );
