<?php
/**
 * Theme functions and definitions.
 *
 *  @package SeedletChild
 *  @since 1.0.0
 */

namespace SeedletChild\Functions;

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

/**
 * Only load block styles when they're rendered.
 *
 * @link https://make.wordpress.org/core/2021/07/01/block-styles-loading-enhancements-in-wordpress-5-8/
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/**
 * Remove generator meta tags.
 *
 * @link https://developer.wordpress.org/reference/functions/the_generator/
 */
add_filter( 'the_generator', '__return_false' );

/**
 * Disable XML RPC.
 *
 * @link https://developer.wordpress.org/reference/hooks/xmlrpc_enabled/
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Change default REST-API header from "null" to "*".
 *
 * @link https://w3c.github.io/webappsec-cors-for-developers/#avoid-returning-access-control-allow-origin-null
 */
function cors_control() {
	header( 'Access-Control-Allow-Origin: *' );
}
add_action( 'rest_api_init', __NAMESPACE__ . '\cors_control' );
