<?php

// Remove WP generator meta tag
remove_action('wp_head', 'wp_generator');
// Don’t support WP blog clients.
remove_action( 'wp_head', 'rsd_link' );
// Don’t support Windows Live Writer.
remove_action( 'wp_head', 'wlwmanifest_link' );
// Disable any feed other than the main blog feed.
remove_action( 'wp_head', 'feed_links_extra', 3 );
