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
	\wp_enqueue_style(
		'seedlet-child-styles',
		get_stylesheet_uri(),
		[ 'seedlet-style' ],
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\child_scripts' );

// Enable carousel stats.
add_filter( 'jetpack_enable_carousel_stats', '__return_true' );
