<?php
add_filter( 'image_editor_output_format', function( $formats ) {
        $formats['image/jpeg'] = 'image/webp';
        $formats['image/png'] = 'image/webp';
        $formats['image/bmp'] = 'image/webp';

        return $formats;
} );
