<?php
/**
 * Plugin Name: Custom Must Use
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
add_filter('acf/settings/remove_wp_meta_box', '__return_false');
