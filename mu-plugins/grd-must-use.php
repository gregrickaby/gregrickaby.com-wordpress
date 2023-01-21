<?php
/**
 * Plugin Name: GRD - Must Use
 * Description: This plugin contains code that is required for gregrickaby.com.
 * Version: 1.0.0
 * Author: Greg Rickaby
 * Author URI: https://gregrickaby.com
 */

/**
 * Disable image size threshold.
 */
add_filter( 'big_image_size_threshold', '__return_false' );

/**
 * Allow additional mime types on upload.
 */
function grd_additional_mime_types( $mimes ) {
    $mimes['svg']  = 'image/svg+xml';
    $mimes['avif'] = 'image/avif';
    return $mimes;
}
add_filter( 'upload_mimes', 'grd_additional_mime_types' );

/**
 * Don't remove the custom field meta box.
 */
add_filter( 'acf/settings/remove_wp_meta_box', '__return_false' );


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
