#!/usr/bin/env bash
# wpstarter copies custom-templates/.env.example to the root at installation.
# So don't git add the .env.example file at the project root.
mv .env.example .env
replace -s directory-basename "${PWD##*/}" -- .env
replace -s wp-home "http://${PWD##*/}.test" -- .env

wp db create

wp core install \
    --url="${PWD##*/}.test" \
    --title="${PWD##*/}"  \
    --admin_user="usr${PWD##*/}" \
    --admin_password="pss${PWD##*/}"  \
    --admin_email=info@example.com  \
    --skip-email

wp option update permalink_structure "/%category%/%postname%/"

wp term update category 1 --slug=general --name=General

wp post create --post_title="Front Page" --post_type=page --post_status=publish --post_author=1
wp post create --post_title="Blog Page" --post_type=page --post_status=publish --post_author=1

wp option update page_on_front 2
wp option update page_for_posts 5
wp option update show_on_front "page"
wp option update timezone_string "Europe/Istanbul"
wp option update blogdescription "WP Test Site"
wp option update ping_sites ""
wp option update rss_use_excerpt 1

wp --skip-plugins plugin activate \
    safe-svg \
    custom-post-type-permalinks \
    custom-post-type-ui \
    duplicate-post \
    pre-publish-checklist \
    query-monitor \
    widget-shortcode \
    bulk-delete \
    passwords-evolved \
    better-search-replace \
    wp-helpers \
    admin-menu-search \
    wayfinder \
    rest-api-toolbox \
    icon-block \
    piklist \
    gutenberg

wp --skip-plugins option update piklist_wp_helpers --format=json '{"admin_color_scheme":"","mail_from":"","mail_from_name":"","maintenance_mode":"false","maintenance_mode_message":"","private_site":"false","redirect_to_home":"true","notice_admin":"false","admin_message":"","notice_front":"false","logged_in_front_message":"","notice_user_type":"all","notice_browser_type":"all","notice_color":"danger","all_options":"true","remove_screen_options":"","disable_uprade_notifications":[""],"disallow_file_edit":"","link_manager":"","show_ids":"true","show_featured_image":"","remove_dashboard_widgets_new":{"dashboard_widgets":["dashboard_right_now","dashboard_quick_press","dashboard_primary"]},"hide_admin_bar":"","show_admin_bar_components":["comments","wp-logo"],"change_howdy":"","login_image":[""],"login_background":"","disable_emojis":"true","disable_visual_editor":"false","default_editor":["tinymce"],"excerpt_wysiwyg":"","excerpt_box_height":"","require_featured_image":[""],"screen_layout_columns_post":"default","disable_autosave":"","edit_posts_per_page":"","revisions_to_save":"0","xml_rpc":"true","xml_rpc_methods":[""],"excerpt_length_type":["words"],"excerpt_length":"","private_title_format":"","protected_title_format":"","disable_feeds":"true","delay_feed":{"delay_feed_num":"","delay_feed_time":"minute"},"featured_image_in_feed":"","search_post_types":[""],"redirect_404":{"redirect_to_404":"-1","redirect_status":""},"enhanced_classes":"","clean_header":["wp_generator","feed_links","feed_links_extra","rsd_link","wlwmanifest_link","adjacent_posts_rel_link_wp_head"],"remove_widgets_new":{"widgets":[""]},"shortcodes_in_widgets":"","comments_open_pages":"true","make_clickable":"true","disable_self_ping":"true","image_default_align":"left","image_default_link_type":"file","image_default_size":"thumbnail","show_additional_image_sizes":"true","show_exif_data":"","attachment_taxononmies":[""],"show_system_information":"","add_to_help":"","delete_orphaned_meta":""}'

wp --skip-plugins option update rest-api-toolbox-settings-core --format=json '{"require-authentication|\/wp\/v2\/posts":"1","require-authentication|\/wp\/v2\/pages":"1","require-authentication|\/wp\/v2\/users":"1","require-authentication|\/wp\/v2\/media":"1","require-authentication|\/wp\/v2\/categories":"1","require-authentication|\/wp\/v2\/tags":"1","require-authentication|\/wp\/v2\/comments":"1","require-authentication|\/wp\/v2\/taxonomies":"1","require-authentication|\/wp\/v2\/types":"1","require-authentication|\/wp\/v2\/statuses":"1","require-authentication|\/wp\/v2\/settings":"1"}'

wp --skip-plugins option update duplicate_post_show_notice 0

wp --skip-plugins rewrite flush
