<?php
function wpr_rest_api_prefix ()
{
    return 'api';
}
add_filter( 'rest_url_prefix', 'wpr_rest_api_prefix');
