<?php
/**
 * Metaboxes class.
 *
 * @package Grd\Photo_Gallery
 * @since 1.11.0
 */

namespace Grd\Photo_Gallery;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Grd\Photo_Gallery\Formatting;
use WP_Post;

/**
 * Metadata class.
 *
 * This class is responsible for displaying custom metaboxes.
 *
 * @author Greg Rickaby
 * @since 1.11.0
 */
class Metaboxes {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Register hooks.
	 */
	private function hooks(): void {
		add_action( 'add_meta_boxes', [ $this, 'custom_metabox' ] );
	}

	/**
	 * Register a custom metabox for image attachments.
	 */
	public function custom_metabox() {
		add_meta_box(
			'other_image_data',
			'Other Image Data',
			[ $this, 'render_custom_metabox' ],
			'attachment',
			'normal',
			'low'
		);
	}

	/**
	 * Render the custom attachment metabox.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_custom_metabox( WP_Post $post ): void {

		// Only show this metabox for images.
		if ( strpos( $post->post_mime_type, 'image' ) === false ) {
			return;
		}

		// Fetch metadata.
		$metadata            = wp_get_attachment_metadata( $post->ID );
		$extended_image_meta = get_post_meta( $post->ID, 'extended_image_meta', true );

		echo '<div class="grd-metabox">';

		// Camera Details.
		echo '<h3>Camera Details</h3>';
		echo '<div>';
		echo '<p><strong>Make:</strong> ' . esc_html( $extended_image_meta['make'] ?? 'Not available' ) . '</p>';
		echo '<p><strong>Model:</strong> ' . esc_html( $metadata['image_meta']['camera'] ?? 'Not available' ) . '</p>';
		echo '<p><strong>Lens:</strong> ' . esc_html( $extended_image_meta['lens'] ?? 'Not available' ) . '</p>';
		echo '</div>';

		// EXIF data.
		echo '<h3>EXIF</h3>';
		echo '<div>';
		echo '<p><strong>Focal Length:</strong> ' . esc_html( $metadata['image_meta']['focal_length'] ?? 'Not available' ) . 'mm</p>';
		echo '<p><strong>Aperture:</strong> ƒ/' . esc_html( $metadata['image_meta']['aperture'] ?? 'Not available' ) . '</p>';
		echo '<p><strong>Shutter Speed:</strong> ' . esc_html( Formatting::format_shutter_speed( $metadata['image_meta']['shutter_speed'] ) ?? 'Not available' ) . 's</p>';
		echo '<p><strong>ISO:</strong> ' . esc_html( $metadata['image_meta']['iso'] ?? 'Not available' ) . '</p>';
		echo '</div>';

		// Location details.
		echo '<h3>GPS</h3>';
		if ( ! empty( $extended_image_meta['latitude'] ) && ! empty( $extended_image_meta['longitude'] ) ) {
			echo '<p><strong>Latitude:</strong> ' . esc_html( $extended_image_meta['latitude'] ) . '</p>';
			echo '<p><strong>Longitude:</strong> ' . esc_html( $extended_image_meta['longitude'] ) . '</p>';
			echo '<p><a href="' . esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $extended_image_meta['latitude'] ) . ',' . rawurlencode( $extended_image_meta['longitude'] ) ) . '" target="_blank">View on Google Maps</a></p>';
		} else {
			echo '<p>GPS data not available.</p>';
		}
		echo '</div>';

		// Other.
		echo '<h3>Other</h3>';
		echo '<div>';
		echo '<p><strong>Photographer:</strong> ' . esc_html( $metadata['image_meta']['credit'] ?? 'Not available' ) . '</p>';
		echo '<p><strong>Copyright:</strong> ' . esc_html( $metadata['image_meta']['copyright'] ?? 'Not available' ) . '</p>';
		echo '<p><strong>Software:</strong> ' . esc_html( $extended_image_meta['software'] ?? 'Not available' ) . '</p>';
		echo '</div>';

		// All generated image sizes.
		echo '<h3>All Image Sizes</h3>';
		echo '<div>';
		foreach ( $metadata['sizes'] as $size => $data ) {
			echo '<p><strong>' . esc_html( $size ) . ':</strong> ' . esc_html( $data['width'] ) . 'x' . esc_html( $data['height'] ) . '</p>';
		}
		echo '</div>';

		// Exif string (for use in gallery).
		echo '<h3>EXIF String</h3>';
		echo '<div>';
		echo '<p>' . esc_html( $extended_image_meta['exif_string'] ?? 'Not available' ) . '</p>';
		echo '</div>';

		echo '</div>';
	}
}
