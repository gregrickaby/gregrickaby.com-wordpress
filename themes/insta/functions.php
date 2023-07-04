<?php
/**
 * Theme functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 *  @package InstaTheme
 *  @since 1.0.0
 */

namespace InstaTheme\Functions;

/**
 * Define constants.
 */
define( 'INSTA_THEME_VERSION', '1.0.0' );
define( 'INSTA_THEME_DIR', trailingslashit( get_stylesheet_directory() ) );
define( 'INSTA_THEME_URL', trailingslashit( get_stylesheet_directory_uri() ) );

/**
 * Enqueue styles and scripts.
 */
function scripts() {

	$asset_file = require INSTA_THEME_DIR . 'build/index.asset.php';

	wp_enqueue_style(
		'insta-theme-styles',
		INSTA_THEME_URL . 'build/index.css',
		[],
		$asset_file['version']
	);

	wp_enqueue_script(
		'insta-theme-scripts',
		INSTA_THEME_URL . 'build/index.js',
		[],
		$asset_file['version'],
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\scripts', 9999 );

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function insta_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on insta, use a find and replace
	 * to change 'insta' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'insta', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

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
		[
			'menu-1' => esc_html__( 'Primary', 'insta-theme' ),
		]
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		[
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		]
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'insta_custom_background_args',
			[
				'default-color' => 'ffffff',
				'default-image' => '',
			]
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
		[
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		]
	);
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\insta_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function insta_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'insta_content_width', 640 );
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\insta_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function insta_widgets_init() {
	register_sidebar(
		[
			'name'          => esc_html__( 'Sidebar', 'insta-theme' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'insta-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		]
	);
}
add_action( 'widgets_init', __NAMESPACE__ . '\insta_widgets_init' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

