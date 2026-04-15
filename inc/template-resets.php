<?php
/**
 * Reset or removes functionality that is not required for the bfc theme.
 *
 * @package Beech_ACF_Boilerplate
 */


/**
 * Disable Guttenberg for all post types and force the classic editor
 */
 function bfc_force_classic_editor() {
    // Disable the Gutenberg editor for all post types
    add_filter('use_block_editor_for_post', '__return_false', 10);
    add_filter('use_block_editor_for_post_type', '__return_false', 10);

    // Optionally, remove the Gutenberg-specific styles
    remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');
}
add_action('init', 'bfc_force_classic_editor');

/* 
	Remove all the extra styles and junk WP adds
*/
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

// Remove JQuery migrate
function bfc_remove_jquery_migrate( $scripts ) {
	if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		 $script = $scripts->registered['jquery'];
		 
		if ( $script->deps ) { 
			// Check whether the script has any dependencies
			$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
		}	
  	}
}
 add_action( 'wp_default_scripts', 'bfc_remove_jquery_migrate' );


 // Remove Gutenberg Block Library CSS from loading on the frontend
 function bfc_remove_wp_block_library_css(){
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-blocks-style' ); // Remove WooCommerce block CSS
	wp_dequeue_style( 'classic-theme-styles-css' );
   } 
 add_action( 'wp_enqueue_scripts', 'bfc_remove_wp_block_library_css', 100 );



 /**
  * Completely disable comments
  * @from https://www.wpbeginner.com/wp-tutorials/how-to-completely-disable-comments-in-wordpress/
  */

  add_action('admin_init', function () {
    // Redirect any user trying to access comments page
    global $pagenow;
     
    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }
 
    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
 
    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});
 
// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
 
// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);
 
// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});
 
// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});

/**
 * Add support for SVGs
 */
function bfc_enable_svg_uploads($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'bfc_enable_svg_uploads');

function bcf_sanitize_svg($file) {
    $filetype = wp_check_filetype($file['name']);
    if ($filetype['ext'] !== 'svg') {
        return $file;
    }

    // Check SVG for potential security issues and sanitize
    $svg = file_get_contents($file['tmp_name']);
    $svg = bcf_sanitize_svg_content($svg);
    file_put_contents($file['tmp_name'], $svg);

    return $file;
}
add_filter('wp_handle_upload_prefilter', 'bcf_sanitize_svg');

function bcf_sanitize_svg_content($svg) {
    // Simple example: strip out script tags
    $svg = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $svg);
    return $svg;
}


/* Filter out the default archive title */
function bcf_custom_archive_archive_title($title) {
    if ( is_tax() || is_category() ) {
        $title = single_cat_title( '', false );
    } elseif ( is_tag() ) {
        $title = single_tag_title( '', false );
    } elseif ( is_author() ) {
        $title = '' . get_the_author() . '' ;
    }
    return $title;
}
add_filter( 'get_the_archive_title', 'bcf_custom_archive_archive_title');

/*
    Set the length of the excerpt for the theme
*/
function bfc_excerpt_length( $length ){ return 15; } 
add_filter('excerpt_length', 'bfc_excerpt_length', 999);



/**
 * Hide the content editor from pages
 */
add_action('add_meta_boxes', 'remove_page_content_editor');

function remove_page_content_editor() {
    remove_post_type_support('page', 'editor');
}

add_action('admin_init', 'remove_page_content_editor_on_page');

function remove_page_content_editor_on_page() {
    if (is_admin()) {
        $post_id = isset($_GET['post']) ? $_GET['post'] : null;
        $post_type = get_post_type($post_id);
        
        // Only remove the editor for the "page" post type
        if ($post_type == 'page') {
            remove_post_type_support('page', 'editor');
        }
    }
}