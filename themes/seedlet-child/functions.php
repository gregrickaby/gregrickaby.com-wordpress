<?php
/**
 * Theme functions and definitions.
 *
 *  @package SeedletChild
 *  @since 1.0.0
 */

namespace SeedletChild\Functions;

/**
 * Define constants.
 */
define( 'SEEDLET_CHILD_VERSION', '1.0.0' );
define( 'SEEDLET_CHILD_DIR', \trailingslashit( \get_stylesheet_directory() ) );
define( 'SEEDLET_CHILD_URL', \trailingslashit( \get_stylesheet_directory_uri() ) );

/**
 * Enqueue child theme styles and scripts.
 */
function scripts() {

	$asset_file = require SEEDLET_CHILD_DIR . 'build/index.asset.php';

	\wp_enqueue_style(
		'seedlet-child-styles',
		SEEDLET_CHILD_URL . 'build/index.css',
		[ 'seedlet-style' ],
		$asset_file['version']
	);

	\wp_enqueue_script(
		'seedlet-child-scripts',
		SEEDLET_CHILD_URL . 'build/index.js',
		[],
		$asset_file['version'],
		true
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\scripts' );
