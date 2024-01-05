<?php
/**
 * Metadata class.
 *
 * @package Grd\Photo_Gallery
 * @since 1.11.0
 */

namespace Grd\Photo_Gallery;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Grd\Photo_Gallery\Formatting;
use Grd\Photo_Gallery\Cloudinary;
use Imagick;
use Exception;

/**
 * Metadata class.
 *
 * This class is responsible for dealing with image metadata on upload.
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
		add_filter( 'wp_generate_attachment_metadata', [ $this, 'set_alt_caption_description' ], 10, 2 );
		add_filter( 'wp_generate_attachment_metadata', [ $this, 'add_extended_image_meta' ], 11, 2 );
	}

	/**
	 * Use the title as both alt text and caption if none is set.
	 *
	 * @param array $metadata The attachment metadata.
	 * @param int   $attachment_id The attachment ID.
	 *
	 * @return array The modified attachment metadata.
	 */
	public function set_alt_caption_description( array $metadata, int $attachment_id ): array {
		// If not an image, bail.
		if ( ! $this->is_image( $attachment_id ) ) {
			return $metadata;
		}

		// Retrieve existing alt text, if any.
		$existing_alt_text = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

		// No alt text? Generate alt text from Cloudinary.
		if ( empty( $existing_alt_text ) ) {

			// Get the full image URL.
			$image_url = wp_get_attachment_url( $attachment_id );

			// Instantiate the AI class.
			$cloudinary = new Cloudinary();

			// Get the description from Cloudinary.
			$description = $cloudinary->get_description( $image_url );

			// Save the description as alt text.
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $description ) );

			// Save the description as the description.
			wp_update_post(
				[
					'ID'           => $attachment_id,
					'post_content' => sanitize_text_field( $description ),
				]
			);
		}

		// Retrieve existing caption, if any.
		$existing_caption = get_post_field( 'post_excerpt', $attachment_id );

		// If there is already a caption, bail.
		if ( ! empty( $existing_caption ) ) {
			return $metadata;
		}

		// Update the post's excerpt (caption) with the title.
		wp_update_post(
			[
				'ID'           => $attachment_id,
				'post_excerpt' => sanitize_text_field( $metadata['image_meta']['title'] ),
			]
		);

		return $metadata;
	}

	/**
	 * Add extended image metadata.
	 *
	 * @param array $metadata The attachment metadata.
	 * @param int   $attachment_id The attachment ID.
	 *
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
		$exif_string = Formatting::format_exif_string( $extended_image_meta, $metadata );

		// Append to the extended image metadata array.
		$extended_image_meta['exif_string'] = $exif_string;

		// Update the post meta.
		update_post_meta( $attachment_id, 'extended_image_meta', $extended_image_meta );

		return $metadata;
	}

	/**
	 * Get image EXIF data using Imagick or PHP's native function as a fallback.
	 *
	 * @param string $file_path The image file path.
	 *
	 * @return array EXIF data.
	 */
	private function get_extended_exif_data( string $file_path ): array {
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
	private function process_exif_data( array $exif_data ): array {
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
			$processed_data['latitude'] = Formatting::dms_to_decimal( $exif_data['exif:GPSLatitude'], $exif_data['exif:GPSLatitudeRef'] );
		}
		if ( isset( $exif_data['exif:GPSLongitude'], $exif_data['exif:GPSLongitudeRef'] ) ) {
			$processed_data['longitude'] = Formatting::dms_to_decimal( $exif_data['exif:GPSLongitude'], $exif_data['exif:GPSLongitudeRef'] );
		}

		return $processed_data;
	}

	/**
	 * Check if the attachment is an image.
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return bool True if the attachment is an image, false otherwise.
	 */
	private function is_image( int $attachment_id ): bool {
		return strpos( get_post_mime_type( $attachment_id ), 'image/' ) !== false;
	}
}
