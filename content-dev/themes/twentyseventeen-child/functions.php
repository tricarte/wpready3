<?php
require_once __DIR__ . '/inc/class-walker-category-treeview.php';

function my_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

// Enqueue main.js from child theme's assets directory.
function enqueue_mainjs() {
    wp_enqueue_script( 'mainjs', get_stylesheet_directory_uri() . '/assets/js/main.js', array ( 'jquery' ), 1.1, true);
}
add_action( 'wp_enqueue_scripts', 'enqueue_mainjs' );

function child_enqueue_searchpage(  ) {
    wp_enqueue_script( 'select2', get_stylesheet_directory_uri() . '/assets/js/select2.min.js', array ( 'jquery' ), 1.1, true);
    wp_enqueue_script( 'search_page', get_stylesheet_directory_uri() . '/assets/js/search_page.js', array ( 'jquery', 'select2' ), 1.1, true);
    wp_enqueue_style( 'select2_style', get_stylesheet_directory_uri() . '/assets/css/select2.min.css');
}
function plugin_is_page() {
    if ( is_page( 'search-snippets' ) ) {
        add_action( 'wp_enqueue_scripts', 'child_enqueue_searchpage' );
    }
}
add_action( 'template_redirect', 'plugin_is_page' );

function wpmu_register_widget() {
	register_widget( 'Multi_Col_Cats' );
}
add_action( 'widgets_init', 'wpmu_register_widget' );

// Limit for post type archive (Latest link).
function latest_snippets_limit($query) {
  if(is_post_type_archive('snippets') AND $query->is_main_query() AND !is_admin()) {
    $query->set('posts_per_page', 10);
    $query->set('no_found_rows', true);
    // $query->set('update_post_meta_cache', true);
    // $query->set('update_post_term_cache', true);
  }
}
add_action( 'pre_get_posts', 'latest_snippets_limit' );

// Order for taxonomy archive.
function taxonomy_archive_order($query) {
  if(is_tax('snippet_categories') AND $query->is_main_query() AND !is_admin()) {
    $query->set( 'orderby', 'post_date' );
    $query->set( 'order', 'ASC');
  }
}
add_action( 'pre_get_posts', 'taxonomy_archive_order' );

/**
 * Halt the main query in the case of an empty search
 */
add_filter( 'posts_search', function( $search, \WP_Query $q )
{
    if( ! is_admin() && empty( $search ) && $q->is_search() && $q->is_main_query() )
        $search .=" AND 0=1 ";

    return $search;
}, 10, 2 );

// Remove last trail from breadcrumb navxt in single snippet page.
function remove_title_from_breadcrumb($trail)
{
  if(is_single() && 'snippets' == get_post_type())
  {
    array_shift($trail->breadcrumbs);
  }
}
add_action('bcn_after_fill', 'remove_title_from_breadcrumb');

/*********************************************************************************
Widget
*********************************************************************************/
class Multi_Col_Cats extends WP_Widget {
    function __construct() {
        $widget_options = array(
            'classname' => 'multi_col_cats',
            'description' => 'Add a multi column categories list.'
        );
        parent::__construct( 'multi_col_cats', 'Multi Column Categories', $widget_options );
    }

    // Method to output the widget in frontend.
    function widget( $args, $instance ) {
        wp_list_categories([
            "taxonomy" => "snippet_categories",
            "hierarchical" => true,
            "hide_empty" => false,
            'title_li' => '<h3>' . __( 'Categories', 'textdomain' ) . '</h3><hr>',
            'walker' => new Walker_Category_TreeView()
        ]);
    }
}

/**
 * Get taxonomies terms links.
 *
 * @see get_object_taxonomies()
 */
function wpdocs_custom_taxonomies_terms_links() {
    // Get post by post ID.
    if ( ! $post = get_post() ) {
        return '';
    }

    // Get post type by post.
    $post_type = $post->post_type;

    // Get post type taxonomies.
    $taxonomies = get_object_taxonomies( $post_type, 'objects' );

    $out = array();

    foreach ( $taxonomies as $taxonomy_slug => $taxonomy ){

        // Get the terms related to post.
        $terms = get_the_terms( $post->ID, $taxonomy_slug );

        if ( ! empty( $terms ) ) {
            usort($terms, function($a, $b) {return $a->parent > $b->parent;});
            // $out[] = "<ul class='catlist'>";
            foreach ( $terms as $term ) {
                $out[] = sprintf( '<li><a href="%1$s">%2$s</a></li>',
                    esc_url( get_term_link( $term->slug, $taxonomy_slug ) ),
                    esc_html( $term->name )
                );
            }
        }
    }
    return "<ul class='catlist'>" . implode( ' > ', $out ) . "\n</ul>\n";
}

function get_post_siblings( $term_id = 0,  $limit = 1, $date = '' ) {
   global $wpdb, $post;

  // Custom Category
  $taxonomy = 'snippet_categories';
  // Custom Post Type
  $postType = 'snippets';

  	if( empty( $date ) )
        $date = $post->post_date;

    $limit = absint( $limit );
    if( !$limit )
        return;

    $p = $wpdb->get_results( "
    (
        SELECT
            p1.post_title,
            p1.post_date,
            p1.ID
        FROM
            $wpdb->posts p1
		INNER JOIN wp_term_relationships
		AS tr
		ON p1.ID = tr.object_id
		INNER JOIN wp_term_taxonomy tt
		ON tr.term_taxonomy_id = tt.term_taxonomy_id
        WHERE
            p1.post_date < '$post->post_date' AND
            p1.post_type = '$postType' AND
            p1.post_status = 'publish'
		AND tt.taxonomy = '$taxonomy'
		AND tt.term_id = $term_id
        ORDER BY p1.post_date DESC
        LIMIT
            $limit
    )
    UNION
    (
        SELECT
            p2.post_title,
            p2.post_date,
            p2.ID
        FROM
            $wpdb->posts p2
		 INNER JOIN wp_term_relationships
		AS tr
		ON p2.ID = tr.object_id
		INNER JOIN wp_term_taxonomy tt
		ON tr.term_taxonomy_id = tt.term_taxonomy_id
        WHERE
            p2.post_date > '$post->post_date' AND
            p2.post_type = '$postType' AND
            p2.post_status = 'publish'
		AND tt.taxonomy = '$taxonomy'
		AND tt.term_id = $term_id
        ORDER BY p2.post_date ASC
        LIMIT
            $limit
    )
    ORDER by post_date ASC
    " );
    $adjacents = [];
    if(count($p) == 2) {
        $adjacents['prev'] = $p[0];
        $adjacents['next'] = $p[1];
    } elseif(count($p) == 1) {
        if($p[0]->post_date < $post->post_date)
            $adjacents['prev'] = $p[0];
        else
            $adjacents['next'] = $p[0];
    }

    return $adjacents;
}

/**
 * Simple helper function to make menu item objects
 *
 * @param $title      - menu item title
 * @param $url        - menu item url
 * @param $order      - where the item should appear in the menu
 * @param int $parent - the item's parent item
 * @return \stdClass
 */
function _custom_nav_menu_item( $title, $url, $order, $parent = 0 ){
  $item = new stdClass();
  $item->ID = 1000000 + $order + $parent;
  $item->db_id = $item->ID;
  $item->title = $title;
  $item->url = $url;
  $item->menu_order = $order;
  $item->menu_item_parent = $parent;
  $item->type = '';
  $item->object = '';
  $item->object_id = '';
  $item->classes = array();
  $item->target = '';
  $item->attr_title = '';
  $item->description = '';
  $item->xfn = '';
  $item->status = '';
  return $item;
}

// Add a link to WP Dashboard dynamically to top navigation menu.
function custom_nav_menu_items( $items, $menu ){
  // only add item to a specific menu
  if ( $menu->slug == 'snippets-navigation' ){

    if ( current_user_can('administrator') ){
      $items[] = _custom_nav_menu_item( 'Dashboard', admin_url(), 4 );
    }
  }

  return $items;
}
add_filter( 'wp_get_nav_menu_items', 'custom_nav_menu_items', 20, 2 );

function cptui_register_my_cpts() {

	/**
	 * Post Type: SNIPPETS.
	 */

	$labels = [
		"name" => __( "SNIPPETS", "custom-post-type-ui" ),
		"singular_name" => __( "Snippet", "custom-post-type-ui" ),
		"add_new" => __( "Add Snippet", "custom-post-type-ui" ),
		"add_new_item" => __( "Add New Snippet", "custom-post-type-ui" ),
		"edit_item" => __( "Edit Snippet", "custom-post-type-ui" ),
		"new_item" => __( "New Snippet", "custom-post-type-ui" ),
		"view_item" => __( "View Snippet", "custom-post-type-ui" ),
		"view_items" => __( "View Snippets", "custom-post-type-ui" ),
		"search_items" => __( "Search Snippet", "custom-post-type-ui" ),
		"not_found" => __( "No snippets found!", "custom-post-type-ui" ),
	];

	$args = [
		"label" => __( "SNIPPETS", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "snippets", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "excerpt", "custom-fields", "revisions", "author", "page-attributes" ],
		"taxonomies" => [ "snippet_categories", "snippet_tags" ],
	];

	register_post_type( "snippets", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );

function cptui_register_my_taxes() {

	/**
	 * Taxonomy: Categories.
	 */

	$labels = [
		"name" => __( "Categories", "custom-post-type-ui" ),
		"singular_name" => __( "Category", "custom-post-type-ui" ),
	];

	$args = [
		"label" => __( "Categories", "custom-post-type-ui" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'category', 'with_front' => true,  'hierarchical' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"rest_base" => "snippet_categories",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => true,
		];
	register_taxonomy( "snippet_categories", [ "snippets" ], $args );

	/**
	 * Taxonomy: Tags.
	 */

	$labels = [
		"name" => __( "Tags", "custom-post-type-ui" ),
		"singular_name" => __( "Tag", "custom-post-type-ui" ),
	];

	$args = [
		"label" => __( "Tags", "custom-post-type-ui" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'snippet_tags', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"rest_base" => "snippet_tags",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => true,
		];
	register_taxonomy( "snippet_tags", [ "snippets" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes' );

