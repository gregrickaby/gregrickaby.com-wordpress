<?php
/**
 * Insta Theme Customizer
 *
 *  @package InstaTheme
 *  @since 1.0.0
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function insta_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'insta_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'insta_customize_partial_blogdescription',
			)
		);
	}
}
add_action( 'customize_register', 'insta_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function insta_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function insta_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function insta_customize_preview_js() {
	wp_enqueue_script( 'insta-customizer', get_template_directory_uri() . '/src/js/customizer.js', array( 'customize-preview' ), INSTA_THEME_VERSION, true );
}
add_action( 'customize_preview_init', 'insta_customize_preview_js' );
