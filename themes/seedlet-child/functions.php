<?php
/**
 * Theme functions and definitions.
 *
 *  @package SeedletChild
 *  @since 1.0.0
 */

namespace SeedletChild;

/**
 * Enqueue child styles and scripts.
 */
function child_scripts() {

	$asset_file = require \get_stylesheet_directory() . '/build/index.asset.php';

	\wp_enqueue_style(
		'seedlet-child-styles',
		\get_stylesheet_directory_uri() . '/build/index.css',
		[ 'seedlet-style' ],
		$asset_file['version']
	);

	\wp_enqueue_script(
		'seedlet-child-scripts',
		\get_stylesheet_directory_uri() . '/build/index.js',
		[],
		$asset_file['version'],
		true
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\child_scripts' );
