<?php
// Hide update notifications
function wpr_remove_core_updates(){
    global $wp_version;
    return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}

if('cli' != php_sapi_name()){
    // Hide updates for WordPress core
    add_filter('pre_site_transient_update_core','wpr_remove_core_updates');
    // Hide updates for plugins
    add_filter('pre_site_transient_update_plugins','wpr_remove_core_updates');
    // Hide updates for themes
    add_filter('pre_site_transient_update_themes','wpr_remove_core_updates');
}
