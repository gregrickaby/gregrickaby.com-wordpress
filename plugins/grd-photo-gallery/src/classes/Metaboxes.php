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
use Grd\Photo_Gallery\Metadata;
use WP_Post;
use Exception;

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
	 * Nonce label.
	 *
	 * @var string
	 */
	private $nonce_label;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->nonce_label = 'grd_rescan_nonce';
		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	private function hooks(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 9999 );
		add_action( 'add_meta_boxes', [ $this, 'custom_metabox' ] );
		add_action( 'attachment_submitbox_misc_actions', [ $this, 'add_rescan_button_to_submitbox' ], 9999 );
		add_action( 'wp_ajax_rescan_metadata', [ $this, 'rescan_metadata_ajax_handler' ] );
		add_action( 'admin_notices', [ $this, 'display_rescan_notices' ] );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_script(
			'grd-rescan-script',
			GRD_PHOTO_GALLERY_URL . 'src/assets/admin/js/rescan.js',
			[ 'jquery' ],
			GRD_PHOTO_GALLERY_VERSION,
			true
		);

		wp_localize_script(
			'grd-rescan-script',
			'ajax_object',
			[
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'attachment_id' => get_the_ID(),
				'nonce'         => wp_create_nonce( $this->nonce_label ),
			]
		);
	}

	/**
	 * Add a rescan button to the submitbox.
	 *
	 * @return void
	 */
	public function add_rescan_button_to_submitbox(): void {
		echo '<div class="misc-pub-section misc-pub-rescan-metadata" style="display: flex; align-items: center; gap: 8px;">';
		echo '<button class="button" id="rescan-metadata">Rescan Metadata</button>';
		echo '</div>';
	}

	/**
	 * Register a custom metabox for image attachments.
	 *
	 * @return void
	 */
	public function custom_metabox(): void {
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
	 *
	 * @return void
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

	/**
	 * AJAX handler for rescanning metadata.
	 *
	 * @throws Exception If nonce verification fails or no attachment ID is provided.
	 *
	 * @return void
	 */
	public function rescan_metadata_ajax_handler(): void {

		try {

			// Sanitize and verify nonce.
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce, $this->nonce_label ) ) {
				throw new Exception( 'Nonce verification failed.' );
			}

			// Sanitize and verify attachment ID.
			$attachment_id = isset( $_POST['attachment_id'] ) ? absint( $_POST['attachment_id'] ) : false;
			if ( ! $attachment_id ) {
				throw new Exception( 'No attachment ID provided.' );
			}

			// Instantiate the metadata class.
			$metadata = new Metadata();

			// Rescan metadata.
			$current_metadata = wp_get_attachment_metadata( $attachment_id );

			// Update the post's excerpt (caption) with the title.
			$metadata->set_alt_caption_description( $current_metadata, $attachment_id );

			// Add extended image metadata.
			$metadata->add_extended_image_meta( $current_metadata, $attachment_id );

			// Set a transient to display a success message.
			set_transient( 'grd_rescan_success', true, 5 );

		} catch ( Exception $e ) {
			error_log( 'Error in ' . __METHOD__ . ': ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log

			// Set a transient to display an error message.
			set_transient( 'grd_rescan_error', true, 5 );
		}

		// Kill the process.
		wp_die();
	}

	/**
	 * Display rescan notices.
	 *
	 * @return void
	 */
	public function display_rescan_notices(): void {

		// Display a success message.
		if ( get_transient( 'grd_rescan_success' ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>Success! Image metadata was successfully scanned and saved.</p></div>';
			delete_transient( 'grd_rescan_success' );
		}

		// Display an error message.
		if ( get_transient( 'grd_rescan_error' ) ) {
			echo '<div class="notice notice-error is-dismissible"><p>Error! There was an error scanning the image.</p></div>';
			delete_transient( 'grd_rescan_error' );
		}
	}
}
