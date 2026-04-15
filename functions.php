<?php
/**
 * Beech Flexible Content functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Beech_ACF_Boilerplate (BFC = Beech Flexible Content)
 */
$THEME_VERSION = wp_get_theme()->get('Version');

if ( ! defined( 'IS_DEV' ) ) {
    define( 'IS_DEV', wp_get_environment_type() !== 'production' );
}

$GLOBALS['THEME_COLORS'] = array(
    'black' => "000000",
    'white' => "FFFFFF",
	'blue' => "494FFF",
	'space1' => 'ffffff',
	'space2' => 'ffffff',
    'light-blue' => "6680FF",
    'dark-blue' => "2227B3",
    'darkest-blue' => "262986",

    'light-grey' => "F5F5F5",
    'mid-grey' => "EFEFEF",
    'line-grey' => "D2D2D2",
    'dark-grey' => "989898",

	'space3' => 'ffffff',

    'error' => "E30000",
    'error-bg' => "FFF2F4"
);

// The name of the block to register the script for, don't include block__
$BLOCK_SCRIPTS = array();

if( ! defined( 'BLOCK_SCRIPTS')) {
	define( 'BLOCK_SCRIPTS', $BLOCK_SCRIPTS );
}

if ( ! defined( '_BFC_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_BFC_VERSION', $THEME_VERSION );
}


add_filter( 'gform_disable_css', '__return_true' );


// LQIP images
function remove_default_image_sizes($sizes) {
    //unset($sizes['thumbnail']);
    unset($sizes['medium']);
    unset($sizes['medium_large']);
    unset($sizes['large']);
    unset($sizes['1536x1536']);
    unset($sizes['2048x2048']);
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'remove_default_image_sizes');

add_image_size('lqip', 20, 0, false); // 20px wide, height auto
add_filter('image_size_names_choose', function($sizes) {
    return array_merge($sizes, ['lqip' => 'lqip']);
});

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function bfc_setup() {

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
	* Enable support for Post Thumbnails on posts and pages.
	*
	* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'header' => esc_html__( 'Header', 'bfc' ),
			'footer' => esc_html__( 'Footer', 'bfc' ),
			'full-menu' => esc_html__( 'Full/Mega Menu', 'bfc' ),
		)
	);

	/*
	* Switch default core markup for search form, comment form, and comments
	* to output valid HTML5.
	*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'bfc_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'bfc_setup' );


// Purge dashicons
function remove_all_dashicons() {
    wp_dequeue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'remove_all_dashicons');


/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function bfc_widgets_init() {
	// No widgets for now.
}
add_action( 'widgets_init', 'bfc_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function bfc_scripts() {
	wp_enqueue_style( 'bfc-style-theme', get_stylesheet_uri(), array(), _BFC_VERSION );
	wp_enqueue_style( 'bfc-style', get_template_directory_uri() . '/assets/css/styles.css', array(), _BFC_VERSION );
	
	wp_enqueue_script( 'bfc-main', get_template_directory_uri() . '/assets/js/main.js', array(), _BFC_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'bfc_scripts' );

/**
 * Add goodness to deal with defering scripts
 */
add_filter( 'script_loader_tag', 'bfc_defer_scripts', 10, 3 );
function bfc_defer_scripts( $tag, $handle, $src ) {

	// The handles of the enqueued scripts we want to defer
	$defer_scripts = array( 'blocks' );

    if ( in_array( $handle, $defer_scripts ) ) {
        return '<script src="' . $src . '" defer="defer" type="text/javascript"></script>' . "\n";
    }
    
    return $tag;
} 

/**
 * Registers an editor stylesheet for the theme.
 */
function bfc_theme_add_editor_styles() {
    add_editor_style( get_stylesheet_directory_uri().'/assets/css/admin/editor-style.css?v='._BFC_VERSION );
}
add_action( 'admin_init', 'bfc_theme_add_editor_styles' );

/**
 * Registers an admin stylesheet for the theme.
 */
function bfc_admin_style() {
  wp_enqueue_style( 'admin-style', get_stylesheet_directory_uri() . '/assets/css/admin/admin-style.css?v='._BFC_VERSION );
}
add_action( 'admin_enqueue_scripts', 'bfc_admin_style');


function add_security_headers() {
    header('Cross-Origin-Opener-Policy: same-origin-allow-popups');
    header('X-Frame-Options: SAMEORIGIN');
}
add_action('send_headers', 'add_security_headers');

/**
 * Functions that strip out unnecessary Wordpress guff
 */
require get_template_directory() . '/inc/template-resets.php';


/**
 * General bits and bobs to centralise and remove logic from template pages
 */
require get_template_directory() . '/inc/general-utils.php';

/**
 * Standard Wordpress search is really limited. This makes it better
 */
require get_template_directory() . '/inc/custom-search.php';

/**
 * Change the structure of the navigation to enable a mega menu
 */
require get_template_directory() . '/inc/custom-nav.php';

/**
 * Wrapper around the standard WP query to make things easier and more flexible and centralised
 */
require get_template_directory() . '/inc/custom-query.php';

/**
 * Improve the content editor and wp admin experience
 */
require get_template_directory() . '/inc/template-editor.php';

/**
 * Make like better by making data easier to access in templates
 */
require get_template_directory() . '/inc/get-data-utils.php';

/**
 * Outputing consistent elements made easy
 */
require get_template_directory() . '/inc/the-output-functions.php';

/**
 * Focal point functionality
 */
require get_template_directory() . '/inc/focal-point.php';


/**
 * For development when extra logging is required.
 */
require get_template_directory() . '/inc/logger.php';


/**
 * Handle the theme updater
 */
require get_stylesheet_directory().'/inc/updater.php';



$updater = new Alpha_Theme_Updater( __FILE__ );
$updater->set_username( 'BeechAgency' );
$updater->set_repository( 'cso-master-child-head-office' );
$updater->set_theme('cso-master-child-head-office'); 

if( $update_key ) {
    $updater->authorize($update_key);    
}

$updater->initialize();