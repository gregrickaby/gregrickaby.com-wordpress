<?php
/**
 * Metadata class.
 *
 * @package Grd\Acf\Blocks
 * @since 1.11.0
 */

namespace Grd\ACF_Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Phospr\Fraction;
use WP_Post;
use Imagick;
use Exception;

/**
 * Metadata class.
 *
 * This class is responsible for generating and displaying image metadata.
 *
 * @author Greg Rickaby
 * @since 1.11.0
 */
class Metadata {

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
		add_filter( 'wp_generate_attachment_metadata', [ $this, 'title_to_alt_and_caption' ], 10, 2 );
		add_filter( 'wp_generate_attachment_metadata', [ $this, 'add_extended_image_meta' ], 11, 2 );
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
	public function render_custom_metabox( WP_Post $post ) {
		// Check if the attachment is an image.
		if ( ! $this->is_image( $post->ID ) ) {
			echo 'This metadata is available for images only.';
			return;
		}

		// Merge the two arrays.
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
		echo '<p><strong>Shutter Speed:</strong> ' . esc_html( Fraction::fromFloat( $metadata['image_meta']['shutter_speed'] ) ?? 'Not available' ) . 's</p>';
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

	/**
	 * Set the alt text and caption to title if none is set.
	 *
	 * @param array $metadata The attachment metadata.
	 * @param int   $attachment_id The attachment ID.
	 *
	 * @return array The modified attachment metadata.
	 */
	public function title_to_alt_and_caption( array $metadata, int $attachment_id ): array {
		// Check if the attachment is an image and the title is set.
		if ( ! $this->is_image( $attachment_id ) || empty( $metadata['image_meta']['title'] ) ) {
			// If not an image or title is empty, return the metadata as is.
			return $metadata;
		}

		// Retrieve existing alt text, if any.
		$existing_alt_text = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

		// If alt text is not already set, use the title as alt text.
		if ( empty( $existing_alt_text ) ) {
			// Update the alt text with sanitized title.
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $metadata['image_meta']['title'] ) );
		}

		// Retrieve existing caption, if any.
		$existing_caption = get_post_field( 'post_excerpt', $attachment_id );

		// If a caption is not already set, use the title as caption.
		if ( empty( $existing_caption ) ) {
			// Update the post's excerpt (caption) with sanitized title.
			wp_update_post(
				[
					'ID'           => $attachment_id,
					'post_excerpt' => sanitize_text_field( $metadata['image_meta']['title'] ),
				]
			);
		}

		// Return the updated metadata.
		return $metadata;
	}

	/**
	 * Add extended image metadata.
	 *
	 * @param array $metadata The attachment metadata.
	 * @param int   $attachment_id The attachment ID.
	 * @return array The modified attachment metadata.
	 */
	public function add_extended_image_meta( array $metadata, int $attachment_id ): array {

		// Check if the attachment is an image.
		if ( ! $this->is_image( $attachment_id ) ) {
			return $metadata;
		}

		// Retrieve extended EXIF data.
		$exif_data = $this->get_extended_exif_data( get_attached_file( $attachment_id ) );

		// Define the EXIF fields to save as extended image metadata.
		$fields_to_save = [ 'make', 'lens', 'software', 'latitude', 'longitude' ];

		// Initialize extended image metadata array.
		$extended_image_meta = [];

		// Loop over each field and add to the array.
		foreach ( $fields_to_save as $field ) {
			if ( isset( $exif_data[ $field ] ) ) {
				$extended_image_meta[ $field ] = sanitize_text_field( $exif_data[ $field ] );
			}
		}

		// If the extended image metadata array is not empty, bail.
		if ( empty( $extended_image_meta ) ) {
			return $metadata;
		}

		// Generate a complete EXIF string from the image metadata.
		$exif_string = $this->generate_single_exif_string( $extended_image_meta, $metadata );

		// Append to the extended image metadata array.
		$extended_image_meta['exif_string'] = $exif_string;

		// Update the post meta.
		update_post_meta( $attachment_id, 'extended_image_meta', $extended_image_meta );

		return $metadata;
	}

	/**
	 * Create a single EXIF string from the image metadata.
	 *
	 * This is useful for displaying EXIF data in a gallery.
	 *
	 * @param array $extended_image_meta The extended image metadata.
	 * @param array $metadata        The attachment metadata.
	 *
	 * @return string The generated exif string.
	 */
	private function generate_single_exif_string( array $extended_image_meta, array $metadata ): string {

		// Extract metadata components.
		$aperture      = $metadata['image_meta']['aperture'] ?? '';
		$camera        = $metadata['image_meta']['camera'] ?? '';
		$focal_length  = $metadata['image_meta']['focal_length'] ?? '';
		$iso           = $metadata['image_meta']['iso'] ?? '';
		$lens          = $extended_image_meta['lens'] ?? '';
		$make          = $extended_image_meta['make'] ?? '';
		$shutter_speed = $metadata['image_meta']['shutter_speed'] ? Fraction::fromFloat( $metadata['image_meta']['shutter_speed'] ) : '';
		$software      = $extended_image_meta['software'] ?? '';

		// Create the caption string.
		$caption_parts = array_filter(
			[
				'camera'        => $make . ' ' . $camera,
				'lens'          => $lens,
				'focal_length'  => $focal_length ? $focal_length . 'mm' : '',
				'aperture'      => $aperture ? 'ƒ/' . $aperture : '',
				'shutter_speed' => $shutter_speed ? $shutter_speed . 's' : '',
				'iso'           => $iso ? 'ISO' . $iso : '',
				'software'      => $software,
			]
		);

		return implode( ' | ', $caption_parts );
	}

	/**
	 * Get image EXIF data using Imagick or PHP's native function as a fallback.
	 *
	 * @param string $file_path The image file path.
	 * @return array EXIF data.
	 */
	private function get_extended_exif_data( $file_path ): array {
		// Set default EXIF data.
		$exif_data = [];

		// Attempt to retrieve EXIF data using Imagick.
		if ( class_exists( 'Imagick' ) && file_exists( $file_path ) ) {
			try {
				$imagick            = new Imagick( $file_path );
				$imagick_properties = $imagick->getImageProperties( 'exif:*' );
				$exif_data          = $this->process_exif_data( $imagick_properties );
			} catch ( Exception $e ) {
				error_log( $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
		} elseif ( function_exists( 'exif_read_data' ) ) {
			$php_exif_data = exif_read_data( $file_path );
			$exif_data     = $this->process_exif_data( $php_exif_data );
		}

		return $exif_data;
	}

	/**
	 * Process and extract required fields from EXIF data.
	 *
	 * @param array $exif_data Raw EXIF data.
	 * @return array Processed EXIF data with required fields.
	 */
	private function process_exif_data( $exif_data ): array {
		$processed_data = [];

		$fields = [
			'make'     => $exif_data['exif:Make'] ?? null,
			'lens'     => $exif_data['exif:LensModel'] ?? null,
			'software' => $exif_data['exif:Software'] ?? null,
		];

		foreach ( $fields as $key => $value ) {
			if ( null !== $value ) {
				$processed_data[ $key ] = sanitize_text_field( $value );
			}
		}

		// Processing for GPS data.
		if ( isset( $exif_data['exif:GPSLatitude'], $exif_data['exif:GPSLatitudeRef'] ) ) {
			$processed_data['latitude'] = $this->dms_to_decimal( $exif_data['exif:GPSLatitude'], $exif_data['exif:GPSLatitudeRef'] );
		}
		if ( isset( $exif_data['exif:GPSLongitude'], $exif_data['exif:GPSLongitudeRef'] ) ) {
			$processed_data['longitude'] = $this->dms_to_decimal( $exif_data['exif:GPSLongitude'], $exif_data['exif:GPSLongitudeRef'] );
		}

		return $processed_data;
	}

	/**
	 * Convert DMS (Degrees, Minutes, Seconds) format to decimal degrees.
	 *
	 * @param string $dms The DMS string (e.g., "31/1, 251797205/10000000, 0/1").
	 * @param string $hemisphere The hemisphere ('N', 'S', 'E', 'W').
	 *
	 * @return float The coordinate in decimal degrees.
	 */
	private function dms_to_decimal( $dms, $hemisphere ): float {
		$parts   = explode( ', ', $dms );
		$degrees = count( $parts ) > 0 ? Fraction::fromString( $parts[0] )->toFloat() : 0;
		$minutes = count( $parts ) > 1 ? Fraction::fromString( $parts[1] )->toFloat() : 0;
		$seconds = count( $parts ) > 2 ? Fraction::fromString( $parts[2] )->toFloat() : 0;

		$flip = ( 'W' === $hemisphere || 'S' === $hemisphere ) ? -1 : 1;

		return ( $degrees + $minutes / 60 + $seconds / 3600 ) * $flip;
	}

	/**
	 * Check if the attachment is an image.
	 *
	 * @param int $attachment_id The attachment ID.
	 * @return bool True if the attachment is an image, false otherwise.
	 */
	private function is_image( int $attachment_id ): bool {
		return strpos( get_post_mime_type( $attachment_id ), 'image/' ) !== false;
	}
}
