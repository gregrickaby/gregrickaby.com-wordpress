<?php
/**
 * Theme functions and definitions.
 *
 *  @package Child Theme
 */

namespace ChildTheme;

/**
 * Enqueue parent theme styles.
 */
function enqueue_parent_styles() {
	\wp_enqueue_style(
		'child-style',
		get_stylesheet_uri(),
		[ 'seedlet' ],
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_parent_styles' );
