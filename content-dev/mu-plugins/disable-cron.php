<?php
add_action( 'plugins_loaded', function() {
    if ( defined( 'DOING_CRON' ) && DOING_CRON && php_sapi_name() != 'cli' )
        die();
});
