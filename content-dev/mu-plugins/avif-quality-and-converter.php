<?php
// Use a quality setting of 75 for AVIF images.
function filter_avif_quality( $quality, $mime_type ) {
    if ( 'image/avif' === $mime_type ) {
        return 75;
    }
    return $quality;
}
add_filter( 'wp_editor_set_quality', 'filter_avif_quality', 10, 2 );

// Output AVIFs for uploaded JPEGs
function filter_image_editor_output_format( $formats ) {
    $formats['image/jpeg'] = 'image/avif';
    return $formats;
}
add_filter( 'image_editor_output_format', 'filter_image_editor_output_format' );
