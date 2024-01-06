<?php
/**
 * Plugin Name:       Photo Gallery
 * Description:       An ACF photo gallery block which displays photos in Fancybox. This plugin also includes robust support for EXIF data.
 * Requires at least: 6.1
 * Requires PHP:      8.1
 * Version:           1.14.0
 * Author:            Greg Rickaby
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       grd-photo-gallery
 *
 * @package           Grd\Photo_Gallery
 */

namespace Grd\Photo_Gallery;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define constants.
 */
define( 'GRD_PHOTO_GALLERY_VERSION', '1.14.0' );
define( 'GRD_PHOTO_GALLERY_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'GRD_PHOTO_GALLERY_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Autoload classes.
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Initialize the plugin.
 */
$grd_photo_gallery = new Plugin();
