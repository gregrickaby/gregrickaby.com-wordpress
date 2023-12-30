<?php
/**
 * Metadata class.
 *
 * @package Grd\Acf\Blocks
 * @since 1.11.0
 */

namespace Grd\ACF_Blocks;

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
		add_filter( 'wp_generate_attachment_metadata', [ $this, 'modify_image_metadata' ], 10, 2 );
		add_filter( 'wp_generate_attachment_metadata', [ $this, 'generate_caption' ], 11, 2 );
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
			'side',
			'default'
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
		$metadata = array_merge( wp_get_attachment_metadata( $post->ID ), get_post_meta( $post->ID ) );

		echo '<div class="grd-metabox">';

		// Camera Details.
		echo '<h3>Camera Details</h3>';
		echo '<div>';
		echo '<p><strong>Make:</strong> ' . esc_html( $metadata['make'][0] ?? 'Not available' ) . '</p>';
		echo '<p><strong>Model:</strong> ' . esc_html( $metadata['image_meta']['camera'] ?? 'Not available' ) . '</p>';
		echo '<p><strong>Lens:</strong> ' . esc_html( $metadata['lens'][0] ?? 'Not available' ) . '</p>';
		echo '</div>';

		// EXIF data.
		echo '<h3>EXIF</h3>';
		echo '<div>';
		echo '<p><strong>Focal Length:</strong> ' . esc_html( $metadata['image_meta']['focal_length'] ?? 'Not available' ) . 'mm</p>';
		echo '<p><strong>Aperture:</strong> f/' . esc_html( $metadata['image_meta']['aperture'] ?? 'Not available' ) . '</p>';
		echo '<p><strong>Shutter Speed:</strong> ' . esc_html( Fraction::fromFloat( $metadata['image_meta']['shutter_speed'] ) ?? 'Not available' ) . 's</p>';
		echo '<p><strong>ISO:</strong> ' . esc_html( $metadata['image_meta']['iso'] ?? 'Not available' ) . '</p>';
		echo '</div>';

		// Location details.
		echo '<h3>GPS</h3>';
		if ( ! empty( $metadata['latitude'][0] ) && ! empty( $metadata['longitude'][0] ) ) {
			echo '<p><strong>Latitude:</strong> ' . esc_html( $metadata['latitude'][0] ) . '</p>';
			echo '<p><strong>Longitude:</strong> ' . esc_html( $metadata['longitude'][0] ) . '</p>';
			echo '<p><a href="' . esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $metadata['latitude'][0] ) . ',' . rawurlencode( $metadata['longitude'][0] ) ) . '" target="_blank">View on Google Maps</a></p>';
		} else {
			echo '<p>GPS data not available.</p>';
		}
		echo '</div>';

		// Other.
		echo '<h3>Other</h3>';
		echo '<div>';
		echo '<p><strong>Photographer:</strong> ' . esc_html( $metadata['image_meta']['credit'] ?? 'Not available' ) . '</p>';
		echo '<p><strong>Copyright:</strong> ' . esc_html( $metadata['image_meta']['copyright'] ?? 'Not available' ) . '</p>';
		echo '<p><strong>Software:</strong> ' . esc_html( $metadata['software'][0] ?? 'Not available' ) . '</p>';
		echo '</div>';

		// All generated image sizes.
		echo '<h3>All Image Sizes</h3>';
		echo '<div>';
		foreach ( $metadata['sizes'] as $size => $data ) {
			echo '<p><strong>' . esc_html( $size ) . ':</strong> ' . esc_html( $data['width'] ) . 'x' . esc_html( $data['height'] ) . '</p>';
		}
		echo '</div>';

		echo '</div>';
	}

	/**
	 * Modify image metadata.
	 *
	 * @param array $metadata The attachment metadata.
	 * @param int   $attachment_id The attachment ID.
	 * @return array The modified attachment metadata.
	 */
	public function modify_image_metadata( array $metadata, int $attachment_id ): array {

		// Check if the attachment is an image.
		if ( ! $this->is_image( $attachment_id ) ) {
			return $metadata;
		}

		// Retrieve existing alt text and EXIF data.
		$existing_alt_text = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		$exif_data         = $this->get_image_exif_data( get_attached_file( $attachment_id ) );

		// Set the alt text to the image title if none is set.
		if ( empty( $existing_alt_text ) || ! empty( $metadata['image_meta']['title'] ) ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $metadata['image_meta']['title'] ) );
		}

		// Set the camera make.
		if ( isset( $exif_data['make'] ) ) {
			update_post_meta( $attachment_id, 'make', sanitize_text_field( $exif_data['make'] ) );
		}

		// Set the lens.
		if ( isset( $exif_data['lens'] ) ) {
			update_post_meta( $attachment_id, 'lens', sanitize_text_field( $exif_data['lens'] ) );
		}

		// Set the software.
		if ( isset( $exif_data['software'] ) ) {
			update_post_meta( $attachment_id, 'software', sanitize_text_field( $exif_data['software'] ) );
		}

		// Set the latitude.
		if ( isset( $exif_data['latitude'] ) ) {
			update_post_meta( $attachment_id, 'latitude', sanitize_text_field( $exif_data['latitude'] ) );
		}

		// Set the longitude.
		if ( isset( $exif_data['longitude'] ) ) {
			update_post_meta( $attachment_id, 'longitude', sanitize_text_field( $exif_data['longitude'] ) );
		}

		return $metadata;
	}

	/**
	 * Generate and set the image caption if none is set.
	 *
	 * @param array $metadata The attachment metadata.
	 * @param int   $attachment_id The attachment ID.
	 *
	 * @return array The modified attachment metadata.
	 */
	public function generate_caption( array $metadata, int $attachment_id ): array {

		// Check if the attachment is an image.
		if ( ! $this->is_image( $attachment_id ) ) {
			return $metadata;
		}

		// Check if a caption is already set.
		$existing_caption = get_post_field( 'post_excerpt', $attachment_id );
		if ( ! empty( $existing_caption ) ) {
			return $metadata;
		}

		// Create the caption string.
		$caption = $this->create_caption_string( $metadata, $attachment_id );

		// If no caption could be generated, return.
		if ( empty( $caption ) ) {
			return $metadata;
		}

		// Update the post excerpt (caption).
		wp_update_post(
			[
				'ID'           => $attachment_id,
				'post_excerpt' => sanitize_text_field( $caption ),
			]
		);

		return $metadata;
	}

	/**
	 * Create a caption string from image metadata.
	 *
	 * @param array $metadata        The attachment metadata.
	 * @param int   $attachment_id   The attachment ID.
	 *
	 * @return string The generated caption string.
	 */
	private function create_caption_string( array $metadata, int $attachment_id ): string {

		// Extract metadata components.
		$make          = get_post_meta( $attachment_id, 'make', true ) ?? '';
		$camera        = $metadata['image_meta']['camera'] ?? '';
		$lens          = get_post_meta( $attachment_id, 'lens', true ) ?? '';
		$aperture      = $metadata['image_meta']['aperture'] ?? '';
		$iso           = $metadata['image_meta']['iso'] ?? '';
		$focal_length  = $metadata['image_meta']['focal_length'] ?? '';
		$shutter_speed = $metadata['image_meta']['shutter_speed'] ? Fraction::fromFloat( $metadata['image_meta']['shutter_speed'] ) : '';

		// Create the caption string.
		$caption_parts = array_filter(
			[
				'camera'        => $make . ' ' . $camera,
				'lens'          => $lens,
				'focal_length'  => $focal_length ? $focal_length . 'mm' : '',
				'aperture'      => $aperture ? 'f/' . $aperture : '',
				'shutter_speed' => $shutter_speed ? $shutter_speed . 's' : '',
				'iso'           => $iso ? 'ISO' . $iso : '',
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
	private function get_image_exif_data( $file_path ): array {

		// Set default EXIF data.
		$exif_data = [];

		// Attempt to retrieve EXIF data using Imagick.
		if ( class_exists( 'Imagick' ) && file_exists( $file_path ) ) {
			try {
				$imagick            = new Imagick( $file_path );
				$imagick_properties = $imagick->getImageProperties( 'exif:*' );

				$exif_data['make']      = $imagick_properties['exif:Make'] ? ucfirst( strtolower( $imagick_properties['exif:Make'] ) ) : null;
				$exif_data['lens']      = $imagick_properties['exif:LensModel'] ?? null;
				$exif_data['software']  = $imagick_properties['exif:Software'] ?? null;
				$exif_data['latitude']  = $imagick_properties['exif:GPSLatitude'] ? $this->dms_to_decimal( $imagick_properties['exif:GPSLatitude'], $imagick_properties['exif:GPSLatitudeRef'] ) : null;
				$exif_data['longitude'] = $imagick_properties['exif:GPSLongitude'] ? $this->dms_to_decimal( $imagick_properties['exif:GPSLongitude'], $imagick_properties['exif:GPSLongitudeRef'] ) : null;

			} catch ( Exception $e ) {
				error_log( $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
		} elseif ( function_exists( 'exif_read_data' ) ) {
			$php_exif_data = exif_read_data( $file_path );

			$exif_data['lens']      = $php_exif_data['UndefinedTag:0xA434'] ?? null;
			$exif_data['software']  = $php_exif_data['Software'] ?? null;
			$exif_data['latitude']  = $php_exif_data['GPSLatitude'] ? $this->dms_to_decimal( $php_exif_data['GPSLatitude'], $php_exif_data['GPSLatitudeRef'] ) : null;
			$exif_data['longitude'] = $php_exif_data['GPSLongitude'] ? $this->dms_to_decimal( $php_exif_data['GPSLongitude'], $php_exif_data['GPSLongitudeRef'] ) : null;
		}

		return $exif_data;
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
