<?php
/**
 * Plugin Name:       GRD ACF Blocks
 * Description:       Custom ACF Blocks for WordPress.
 * Requires at least: 6.1
 * Requires PHP:      8.0
 * Version:           1.0.5
 * Author:            Greg Rickaby
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       grd-acf-blocks
 *
 * @package           Grd\Acf\Blocks
 */

namespace Grd\Acf\Blocks;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define constants.
 */
define( 'GRD_ACF_BLOCKS_VERSION', '1.0.5' );
define( 'GRD_ACF_BLOCKS_DIR', \trailingslashit( \plugin_dir_path( __FILE__ ) ) );

/**
 * Autoload classes.
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Initialize the plugin.
 */
$grd_acf_blocks = new Plugin();
