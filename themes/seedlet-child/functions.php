<?php
/**
 * Theme functions and definitions.
 *
 *  @package Seedlet Child
 */

namespace SeedletChild;

/**
 * Enqueue child styles and scripts.
 */
function child_scripts() {
	\wp_enqueue_style(
		'seedlet-child-styles',
		get_stylesheet_uri(),
		[ 'seedlet-style' ],
		wp_get_theme()->get( 'Version' )
	);

	\wp_enqueue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\child_scripts' );
