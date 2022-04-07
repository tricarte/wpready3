<?php
/**
* ! These actually stop the HTTP API requests!
* https://developer.wordpress.org/reference/hooks/auto_update_type/#user-contributed-notes
*/
remove_action( 'admin_init', '_maybe_update_core' );
remove_action( 'admin_init', '_maybe_update_plugins' );
remove_action( 'admin_init', '_maybe_update_themes' );
