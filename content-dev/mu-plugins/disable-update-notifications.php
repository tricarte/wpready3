<?php
// Hide update notifications
function wpr_remove_core_updates(){
    global $wp_version;
    return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}
add_filter('pre_site_transient_update_core','wpr_remove_core_updates'); //hide updates for WordPress itself
add_filter('pre_site_transient_update_plugins','wpr_remove_core_updates'); //hide updates for all plugins
add_filter('pre_site_transient_update_themes','wpr_remove_core_updates'); //hide updates for all themes
