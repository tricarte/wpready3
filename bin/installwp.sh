#!/usr/bin/env bash
# wpstarter copies custom-templates/.env.example to the root at installation.
# So don't git add the .env.example file at the project root.
mv .env.example .env
SITENAME=${PWD##*/}
replace -s directory-basename "$SITENAME" -- .env

if [[ -z "$XDG_CURRENT_DESKTOP" ]]; then
    echo "
[HOST=$SITENAME]
open_basedir = /home/$(whoami)/sites/$SITENAME:/tmp
    " | sudo tee -a /etc/php/7.4/fpm/php.ini
fi

if [[ -n "$XDG_CURRENT_DESKTOP" ]]; then
    ADMIN_USER="usr${SITENAME}"
    ADMIN_PASS="pss${SITENAME}"
else
    ADMIN_USER="$SITENAME-$RANDOM"
    ADMIN_PASS=$(tr -dc A-Za-z0-9 </dev/urandom | head -c 13)

    # Create separate db user and password, change .env file.
    eval $(grep DB_USER .env)
    eval $(grep DB_PASSWORD .env)

    NEW_DB_USER="$SITENAME-$RANDOM"
    NEW_DB_PASSWORD=$(tr -dc A-Za-z0-9 </dev/urandom | head -c 13)
    mysql -u $DB_USER -p$DB_PASSWORD -e"CREATE USER '$NEW_DB_USER'@'localhost' IDENTIFIED BY '$NEW_DB_PASSWORD';"
    mysql -u $DB_USER -p$DB_PASSWORD -e"GRANT ALL PRIVILEGES ON \`$SITENAME\`.* TO '$NEW_DB_USER'@'localhost';"

    # Now change .env with new values
    replace -s DB_USER=$DB_USER "DB_USER=$NEW_DB_USER" -- .env
    replace -s DB_PASSWORD=$DB_PASSWORD "DB_PASSWORD=$NEW_DB_PASSWORD" -- .env
fi

if [[ -n "$XDG_CURRENT_DESKTOP" ]]; then
    replace -s wp-home "http://${SITENAME}.test" -- .env
    URL="$SITENAME.test"
else
    replace -s wp-home "https://${SITENAME}" -- .env
    URL="$SITENAME"
fi

wp db create

wp core install \
    --url="$URL" \
    --title="${SITENAME}"  \
    --admin_user="$ADMIN_USER" \
    --admin_password="$ADMIN_PASS"  \
    --admin_email=info@example.com  \
    --skip-email

# If this is a server, change fs permissions accordingly.
if [[ -z "$XDG_CURRENT_DESKTOP" ]]; then
    if [[ -d "/home/$(whoami)/sites/$SITENAME/public/content" ]]; then
        chmod -R o+w "/home/$(whoami)/sites/$SITENAME/public/content"
    fi
fi

wp user update 1 --display_name="John Doe" --user_nicename="john-doe" --nickname="Editor"

wp option update permalink_structure "/%category%/%postname%/"

wp term update category 1 --slug=general --name=General

wp post create --post_title="Front Page" --post_type=page --post_status=publish --post_author=1
wp post create --post_title="Blog Page" --post_type=page --post_status=publish --post_author=1

wp option update page_on_front 2
wp option update page_for_posts 5
wp option update show_on_front "page"
wp option update timezone_string "Europe/Istanbul"
wp option update blogdescription "WP Test Site" # Tagline
wp option update ping_sites ""
wp option update rss_use_excerpt 1

# Disable comments
wp option update default_pingback_flag 0
wp option update default_ping_status 0
wp option update default_comment_status 0
wp option update comment_registration 1
wp option update close_comments_for_old_posts 1

wp --skip-plugins plugin activate \
    safe-svg \
    custom-post-type-permalinks \
    duplicate-post \
    pre-publish-checklist \
    widget-shortcode \
    passwords-evolved \
    admin-menu-search \
    rest-api-toolbox \
    icon-block \
    rollback-update-failure \
    reveal-ids-for-wp-admin-25 \
    clarity-ad-blocker \
    apcu-manager \
    surge \
    fluent-smtp \
    log-http-requests \
    gutenberg

if [[ -z "$XDG_CURRENT_DESKTOP" ]]; then
    wp --skip-plugins plugin activate patchstack
fi

if [[ -n "$XDG_CURRENT_DESKTOP" ]]; then
    wp --skip-plugins plugin activate \
        query-monitor \
        custom-post-type-ui \
        bulk-delete \
        better-search-replace \
        wayfinder \
        block-xray-attributes \
        wpready-playground \
        show-hooks \
        wp-mailhog-smtp \
        rewrite-rules-inspector
fi

# wp --skip-plugins option update piklist_wp_helpers --format=json '{"admin_color_scheme":"","mail_from":"","mail_from_name":"","maintenance_mode":"false","maintenance_mode_message":"","private_site":"false","redirect_to_home":"true","notice_admin":"false","admin_message":"","notice_front":"false","logged_in_front_message":"","notice_user_type":"all","notice_browser_type":"all","notice_color":"danger","all_options":"true","remove_screen_options":"","disable_uprade_notifications":[""],"disallow_file_edit":"","link_manager":"","show_ids":"true","show_featured_image":"","remove_dashboard_widgets_new":{"dashboard_widgets":["dashboard_right_now","dashboard_quick_press","dashboard_primary"]},"hide_admin_bar":"","show_admin_bar_components":["comments","wp-logo"],"change_howdy":"","login_image":[""],"login_background":"","disable_emojis":"true","disable_visual_editor":"false","default_editor":["tinymce"],"excerpt_wysiwyg":"","excerpt_box_height":"","require_featured_image":[""],"screen_layout_columns_post":"default","disable_autosave":"","edit_posts_per_page":"","revisions_to_save":"0","xml_rpc":"true","xml_rpc_methods":[""],"excerpt_length_type":["words"],"excerpt_length":"","private_title_format":"","protected_title_format":"","disable_feeds":"true","delay_feed":{"delay_feed_num":"","delay_feed_time":"minute"},"featured_image_in_feed":"","search_post_types":[""],"redirect_404":{"redirect_to_404":"-1","redirect_status":""},"enhanced_classes":"","clean_header":["wp_generator","feed_links","feed_links_extra","rsd_link","wlwmanifest_link","adjacent_posts_rel_link_wp_head"],"remove_widgets_new":{"widgets":[""]},"shortcodes_in_widgets":"","comments_open_pages":"true","make_clickable":"true","disable_self_ping":"true","image_default_align":"left","image_default_link_type":"file","image_default_size":"thumbnail","show_additional_image_sizes":"true","show_exif_data":"","attachment_taxononmies":[""],"show_system_information":"","add_to_help":"","delete_orphaned_meta":""}'

wp --skip-plugins option update rest-api-toolbox-settings-core --format=json '{"require-authentication|\/wp\/v2\/posts":"1","require-authentication|\/wp\/v2\/pages":"1","require-authentication|\/wp\/v2\/users":"1","require-authentication|\/wp\/v2\/media":"1","require-authentication|\/wp\/v2\/categories":"1","require-authentication|\/wp\/v2\/tags":"1","require-authentication|\/wp\/v2\/comments":"1","require-authentication|\/wp\/v2\/taxonomies":"1","require-authentication|\/wp\/v2\/types":"1","require-authentication|\/wp\/v2\/statuses":"1","require-authentication|\/wp\/v2\/settings":"1"}'

# This admin notice always appear.
# wp --skip-plugins option update duplicate_post_show_notice 0

# wp --skip-plugins user meta update 1 dismissed_wp_pointers "piklist_demos,custom-post-type-permalinks-settings"
wp --skip-plugins user meta update 1 dismissed_wp_pointers "custom-post-type-permalinks-settings"

CPTP_VERSION=$(wp --skip-plugins plugin get custom-post-type-permalinks --format=table --field=version)
wp --skip-plugins option update cptp_permalink_checked "$CPTP_VERSION" --autoload=yes

# APCU Manager options
wp --skip-plugins option update apcm_adminbar 0
wp --skip-plugins option update apcm_earlyloading 1
wp --skip-plugins option update apcm_analytics 0
wp --skip-plugins option update apcm_gc 0

wp --skip-plugins rewrite flush

# Install images
if [[ -n "$XDG_CURRENT_DESKTOP" ]]; then
    wp --skip-plugins media import ./sample-images/* --user="usr${SITENAME}"
fi

# Create link to adminer that can be accessible from http://site/adminer
if [[ -n "$XDG_CURRENT_DESKTOP" ]]; then
    ln -rs ./vendor/dg/adminer-custom/ ./public/adminer
fi

echo "WordPress installation finished."
if [[ -z "$XDG_CURRENT_DESKTOP" ]]; then
    echo "WP Admin Username: $ADMIN_USER"
    echo "WP Admin Password: $ADMIN_PASS"
fi
