<?php
/**
 * Disables admin email verification.
 */
add_filter( 'admin_email_check_interval', '__return_false' );
