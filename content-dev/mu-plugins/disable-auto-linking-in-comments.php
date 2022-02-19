<?php
// https://www.wpbeginner.com/wp-tutorials/how-to-disable-auto-linking-of-urls-in-wordpress-comments/
remove_filter( 'comment_text', 'make_clickable', 9 );
