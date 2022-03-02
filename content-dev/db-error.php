<?php
/**
 * Whenever there is a problem connecting to the database this Drop-in
 * plugin shows to the visitor a custom error page and sends to your
 * inbox a notification about the insurrection.
 *
 * Version: 1.0
 *
 *
 * Copyright 2020 Luigi Cavalieri.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * *********************************************************************** */


// Direct script access denied.
if (! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'FROM_EMAIL_ADDRESS', 'your_admin@email.address' );
define( 'TO_EMAIL_ADDRESS', 'your_private@email.address' );
define( 'THIRTY_MINUTES', 1800 );

session_start();

$now = time();

// Timestamp of the last email sent.
// Used to limit the number of notifications sent via email.
//
// It is saved both to a text file and to a session variable,
// so that the text file is read only once per visit.
$last_email_time = 0;

// Full path to the text file where $last_email_time is saved.
$timestamp_file_path = WP_CONTENT_DIR . '/db-error.txt';

if (
    isset( $_SESSION['last_email_time'] ) &&
    is_int( $_SESSION['last_email_time'] )
) {
    $last_email_time = $_SESSION['last_email_time'];
}
else {
    $last_email_time = (int) file_get_contents( $timestamp_file_path );

    $_SESSION['last_email_time'] = $last_email_time;
}

// Makes sure that the inbox doesn't catch fire.
if ( ( $now - $last_email_time ) > THIRTY_MINUTES ) {
    $email_subject = 'Database error';
    $error_message = 'Database unreachable: ' . $_SERVER['REQUEST_URI'];
    $extra_headers = 'From: ' . FROM_EMAIL_ADDRESS;
    $email_sent    = mail(
        TO_EMAIL_ADDRESS,
        $email_subject,
        $error_message,
        $extra_headers
    );

    if ( $email_sent ) {
        $_SESSION['last_email_time'] = $now;

        file_put_contents( $timestamp_file_path, $now );
    }

    error_log( $error_message );
}

// Some HTTP headers for search engines.
header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
header( 'Status: 503 Service Temporarily Unavailable' );
header( 'Retry-After: ' . THIRTY_MINUTES );

// The database connection problem might be just temporary.
header( 'Refresh: 10' );

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Database Error</title>
<style>
body { padding: 20px; background: red; color: white; font-size: 60px; }
</style>
</head>
<body>
  Database is unreachable.
</body>
</html>
