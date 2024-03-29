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
	 *
	 * @return void
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

		// Check if the attachment is an image.
		if ( ! $this->is_image( $attachment_id ) ) {
			return $metadata;
		}

		// Set the alt text.
		$this->set_alt_text( $attachment_id );

		// Set the caption.
		$this->set_caption( $attachment_id, $metadata );

		return $metadata;
	}

	/**
	 * Set the alt text for an image.
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return void
	 */
	private function set_alt_text( int $attachment_id ): void {

		// Check if the alt text is already set.
		$existing_alt_text = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

		// If the alt text is already set, bail.
		if ( ! empty( $existing_alt_text ) ) {
			return;
		}

		// Get the image URL.
		$image_url = wp_get_attachment_url( $attachment_id );

		// Get the image description from Cloudinary.
		$cloudinary  = new Cloudinary();
		$description = $cloudinary->get_description( $image_url );

		// If the description is empty, bail.
		if ( empty( $description ) ) {
			return;
		}

		// Save the alt text field.
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $description ) );

		// Save the image description field.
		wp_update_post(
			[
				'ID'           => $attachment_id,
				'post_content' => sanitize_text_field( $description ),
			]
		);
	}

	/**
	 * Set the caption for an image.
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return void
	 */
	private function set_caption( int $attachment_id ): void {

		// Check if the caption is already set.
		$existing_caption = get_post_field( 'post_excerpt', $attachment_id );

		// If the caption is already set, bail.
		if ( ! empty( $existing_caption ) ) {
			return;
		}

		// Check if the image has a title.
		$image_title = get_the_title( $attachment_id );

		// If the image does not have a title, bail.
		if ( empty( $image_title ) ) {
			return;
		}

		// Save the caption field.
		wp_update_post(
			[
				'ID'           => $attachment_id,
				'post_excerpt' => sanitize_text_field( $image_title ),
			]
		);
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
	 * @throws Exception If there's an error retrieving EXIF data.
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
				error_log( 'Error in ' . __METHOD__ . ': ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}

			// Attempt to retrieve EXIF data using PHP's native function.
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
	 *
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
