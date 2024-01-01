<?php
/**
 * Plugin class.
 *
 * @package Grd\Photo_Gallery
 * @since 1.0.0
 */

namespace Grd\Photo_Gallery;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Grd\Photo_Gallery\ACF;
use Grd\Photo_Gallery\Metadata;
use Grd\Photo_Gallery\Metaboxes;

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
		new Metaboxes();
	}
}
