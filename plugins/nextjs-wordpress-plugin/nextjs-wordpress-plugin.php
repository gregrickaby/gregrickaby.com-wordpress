<?php
/**
 * Plugin Name: Next.js WordPress Plugin
 * Plugin URI:  https://github.com/gregrickaby/nextjs-wordpress-plugin
 * Description: A plugin to help turn WordPress into a headless CMS.
 * Version:     1.0.6
 * Author:      Greg Rickaby <greg@gregrickaby.com>
 * Author URI:  https://gregrickaby.com
 * License:     MIT
 *
 * @package NextJS_WordPress_Plugin
 */

namespace NextJS_WordPress_Plugin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Define constants.
define( 'NEXTJS_WORDPRESS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'NEXTJS_WORDPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'NEXTJS_WORDPRESS_PLUGIN_VERSION', '1.0.6' );

// Autoload classes.
require __DIR__ . '/vendor/autoload.php';

// Initialize the plugin.
$nextjs_wordpress_plugin = new Plugin();
