<?php
/**
 * Cloudinary class.
 *
 * @package Grd\Photo_Gallery
 * @since 1.13.0
 */

namespace Grd\Photo_Gallery;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;

/**
 * Cloudinary class.
 *
 * This class is responsible scanning an image with Cloudinary's AI and returning a description.
 *
 * @see https://cloudinary.com/documentation/cloudinary_ai_content_analysis_addon#ai_based_image_captioning
 *
 * @author Greg Rickaby
 * @since 1.13.0
 */
class Cloudinary {

	/**
	 * Cloudinary API key.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Cloudinary API secret.
	 *
	 * @var string
	 */
	private $api_secret;

	/**
	 * Cloudinary cloud name.
	 *
	 * @var string
	 */
	private $cloud_name;

	/**
	 * The Public ID of the image in Cloudinary.
	 *
	 * @var string
	 */
	private $public_id = 'image_caption';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->api_key    = defined( 'CLOUDINARY_API_KEY' ) ? CLOUDINARY_API_KEY : null;
		$this->api_secret = defined( 'CLOUDINARY_API_SECRET' ) ? CLOUDINARY_API_SECRET : null;
		$this->cloud_name = defined( 'CLOUDINARY_CLOUD_NAME' ) ? CLOUDINARY_CLOUD_NAME : null;
	}

	/**
	 * Scan an image URL with Cloudinary's AI to generate a description.
	 *
	 * @param string $image_url The URL to the image.
	 *
	 * @return string The AI generated image description.
	 */
	public function get_description( string $image_url ): string {

		// Replace .test with .com for local development.
		$image_url = str_replace( '.test', '.com', $image_url );

		// Upload the image to Cloudinary.
		$response = $this->upload_image( $image_url );

		// Extract the caption from the response.
		$caption = $this->extract_caption_from_response( $response );

		// Delete the image from Cloudinary.
		$this->delete_image();

		return $caption;
	}

	/**
	 * Upload the image to Cloudinary.
	 *
	 * @see https://cloudinary.com/documentation/image_upload_api_reference
	 *
	 * @param string $image_url URL of the image to upload.
	 *
	 * @throws Exception If there's an error uploading the image.
	 *
	 * @return array The response from Cloudinary.
	 */
	private function upload_image( string $image_url ): array {

		try {

			// Set a timestamp.
			$timestamp = time();

			// Parameters to sign for the API request.
			$signature_params = [
				'detection' => 'captioning',
				'public_id' => $this->public_id,
				'timestamp' => $timestamp,
			];

			// Parameters for the API request.
			$params = [
				'file'      => $image_url,
				'api_key'   => $this->api_key,
				'detection' => 'captioning',
				'public_id' => $this->public_id,
				'timestamp' => $timestamp,
				'signature' => $this->generate_signature( $signature_params, $timestamp ),
			];

			// Set the upload URL.
			$url = 'https://api.cloudinary.com/v1_1/' . $this->cloud_name . '/image/upload';

			// Build the API url.
			$api_url = add_query_arg( $params, $url );

			// Upload the image to Cloudinary.
			$response = wp_remote_post( $api_url );

			// If there's an error or the response code isn't 200, bail.
			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
				throw new Exception();
			}

			// Return the response body.
			return json_decode( wp_remote_retrieve_body( $response ), true );

		} catch ( Exception $e ) {
			error_log( 'Error in ' . __METHOD__ . ': ' . $e->getMessage() ); // phpcs:ignore
			if ( is_wp_error( $response ) ) {
				error_log( 'WP Error: ' . wp_remote_retrieve_response_message( $response ) ); // phpcs:ignore
			}
		}
	}

	/**
	 * Delete the image from Cloudinary.
	 *
	 * When an image is uploaded to Cloudinary for scanning, a copy is stored in
	 * the user's account. This method deletes that copy.
	 *
	 * @see https://cloudinary.com/documentation/image_upload_api_reference#destroy
	 *
	 * @throws Exception If there's an error deleting the image.
	 *
	 * @return void
	 */
	private function delete_image(): void {

		try {

			// Set a timestamp.
			$timestamp = time();

			// Parameters to sign for the API request.
			$signature_params = [
				'public_id' => $this->public_id,
				'timestamp' => $timestamp,
			];

			// Set the params for the API request.
			$params = [
				'api_key'   => $this->api_key,
				'timestamp' => time(),
				'public_id' => $this->public_id,
				'signature' => $this->generate_signature( $signature_params, $timestamp ),
			];

			// Set the delete URL endpoint.
			$url = 'https://api.cloudinary.com/v1_1/' . $this->cloud_name . '/image/destroy';

			// Build the API URL.
			$api_url = add_query_arg( $params, $url );

			// Delete the image from Cloudinary.
			$response = wp_remote_post( $api_url );

			// If there's an error or the response code isn't 200, throw an exception.
			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
				throw new Exception();
			}
		} catch ( Exception $e ) {
			error_log( 'Error in ' . __METHOD__ . ': ' . $e->getMessage() ); // phpcs:ignore
			if ( is_wp_error( $response ) ) {
				error_log( 'WP Error: ' . wp_remote_retrieve_response_message( $response ) ); // phpcs:ignore
			}
		}
	}

	/**
	 * Generate upload signature  Cloudinary API.
	 *
	 * Cloudinary requires a signature to be used to authenticate each API request.
	 * The signature should include all request parameters, including the timestamp.
	 *
	 * @see https://cloudinary.com/documentation/upload_images#authenticated_requests
	 * @see https://cloudinary.com/documentation/authentication_signatures#manual_signature_generation
	 *
	 * @param array $params The parameters to sign.
	 *
	 * @return string The generated signature.
	 */
	private function generate_signature( array $params ): string {

		// Params *must* be sorted alphabetically.
		ksort( $params );

		// Build query string from parameters.
		$query_string = http_build_query( $params );

		// Append the API secret at the end of the query string.
		$query_string .= $this->api_secret;

		// Hash and return signature.
		return hash( 'sha256', $query_string );
	}

	/**
	 * Extract the caption from the Cloudinary response.
	 *
	 * @param array $response The Cloudinary response.
	 *
	 * @return string The extracted caption.
	 */
	private function extract_caption_from_response( array $response ): string {

		// No caption? Bail.
		if ( ! isset( $response['info']['detection']['captioning']['data']['caption'] ) ) {
			return '';
		}

		return $response['info']['detection']['captioning']['data']['caption'];
	}
}
