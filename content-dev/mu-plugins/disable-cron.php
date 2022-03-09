<?php
/*
Why disable it? Because WP checks cron jobs on every page visit. After
disabling it, you have to run wp-cron manually. For example auto update wp core
does not work after this setting nor ’wp-updates-notifier’ plugin. But that
plugin has system cron support. Requesting https://example.com/wp-cron.php runs
cron jobs even if the above constant is set true.
 */
add_action( 'plugins_loaded', function() {
    if ( defined( 'DOING_CRON' ) && DOING_CRON && php_sapi_name() != 'cli' )
        die();
});
