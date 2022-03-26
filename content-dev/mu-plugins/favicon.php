<?php
function hook_favicon() {
?>
<link rel="shortcut icon" type="image/png" href="/favicon.png">
<link rel="shortcut icon" sizes="192x192" href="/favicon.png">
<link rel="apple-touch-icon" href="/favicon.png">
<?php
}
add_action('wp_head', 'hook_favicon');
