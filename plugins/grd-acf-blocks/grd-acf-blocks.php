<?php
/**
 * Plugin Name:       GRD ACF Blocks
 * Description:       Custom ACF Blocks for WordPress.
 * Requires at least: 6.1
 * Requires PHP:      8.0
 * Version:           1.0.0
 * Author:            Greg Rickaby
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       grd-acf-blocks
 *
 * @package           Grd\Acf\Blocks
 */

namespace Grd\Acf\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoload classes.
 */
require_once dirname( __FILE__ ) . '/vendor/autoload.php';

/**
 * Register ACF blocks.
 *
 * @see https://www.advancedcustomfields.com/resources/acf-blocks-with-block-json/
 * @see https://www.advancedcustomfields.com/resources/whats-new-with-acf-blocks-in-acf-6/#blockjson-support
 */
function register_acf_blocks(): void {
	\register_block_type( __DIR__ . '/build/blocks/gallery' );
}
add_action( 'acf/init', __NAMESPACE__ . '\register_acf_blocks' );
