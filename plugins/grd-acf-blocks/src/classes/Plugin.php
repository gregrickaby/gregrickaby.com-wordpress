<?php
/**
 * The main plugin class.
 *
 * @see https://www.advancedcustomfields.com/resources/including-acf-within-a-plugin-or-theme/
 * @package Grd\Acf\Blocks
 * @since 1.0.0
 */

namespace Grd\ACF_Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Grd\ACF_Blocks\ACF;
use Grd\ACF_Blocks\Metadata;

/**
 * Plugin class.
 *
 * This class is responsible for instantiating the plugin.
 *
 * @author Greg Rickaby
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		new ACF();
		new Metadata();
	}
}
