<?php
/**
 * Next.js WordPress Plugin: links functionality
 *
 * Handles the modification of various WordPress URLs to integrate with a headless client.
 *
 * @package NextJS_WordPress_Plugin
 * @since 1.0.0
 */

namespace NextJS_WordPress_Plugin;

use WP_Post;
use WP_REST_Response;

/**
 * Modify various WordPress URLs to integrate with a headless client.
 *
 * @author Greg Rickaby
 * @since 1.0.6
 */
class Links {

	/**
	 * Frontend URL.
	 *
	 * @var string|null
	 */
	private $frontend_url;

	/**
	 * Preview secret.
	 *
	 * @var string|null
	 */
	private $preview_secret;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Set the frontend URL and preview secret.
		$this->frontend_url   = defined( 'NEXTJS_FRONTEND_URL' ) ? rtrim( NEXTJS_FRONTEND_URL, '/' ) : null;
		$this->preview_secret = defined( 'NEXTJS_PREVIEW_SECRET' ) ? NEXTJS_PREVIEW_SECRET : null;

		// Apply the hooks and filters.
		$this->hooks();
	}

	/**
	 * Registers hooks for the class.
	 *
	 * @return void
	 */
	public function hooks() {
		add_filter( 'preview_post_link', [ $this, 'set_headless_preview_link' ], 10, 2 );
		add_filter( 'home_url', [ $this, 'set_headless_home_url' ], 10, 3 );
		add_filter( 'rest_prepare_page', [ $this, 'set_headless_rest_preview_link' ], 10, 2 );
		add_filter( 'rest_prepare_post', [ $this, 'set_headless_rest_preview_link' ], 10, 2 );
		add_action( 'save_post', [ $this, 'override_post_links' ] );
	}

	/**
	 * Customize the preview button in the WordPress admin.
	 *
	 * This method modifies the preview link for a post to point to a headless client setup.
	 *
	 * @param string  $link Original WordPress preview link.
	 * @param WP_Post $post Current post object.
	 * @return string Modified headless preview link.
	 */
	public function set_headless_preview_link( string $link, WP_Post $post ): string {

		// Return the original link if the frontend URL or preview secret are not defined.
		if ( ! $this->frontend_url || ! $this->preview_secret ) {
			return $link;
		}

		return add_query_arg(
			[ 'secret' => $this->preview_secret ],
			esc_url_raw( "{$this->frontend_url}/preview/{$post->ID}" )
		);
	}

	/**
	 * Customize the WordPress home URL to point to the headless frontend.
	 *
	 * @param string      $url Original home URL.
	 * @param string      $path Path relative to home URL.
	 * @param string|null $scheme URL scheme.
	 * @return string Modified frontend home URL.
	 */
	public function set_headless_home_url( string $url, string $path, $scheme = null ): string {
		global $current_screen;

		// Do not modify the URL for REST requests.
		if ( 'rest' === $scheme ) {
			return $url;
		}

		// Avoid modifying the URL in the block editor to ensure functionality.
		if ( ( is_string( $current_screen ) || is_object( $current_screen ) ) && method_exists( $current_screen, 'is_block_editor' ) ) {
			return $url;
		}

		// Do not modify the URL outside the WordPress admin.
		if ( ! is_admin() ) {
			return $url;
		}

		// Get the frontend URL.
		$base_url = $this->get_frontend_url();

		// Return the original URL if the frontend URL is not defined.
		if ( ! $base_url ) {
			return $url;
		}

		// Return the modified URL.
		return $path ? "{$base_url}/" . ltrim( $path, '/' ) : $base_url;
	}

	/**
	 * Customize the REST preview link to point to the headless client.
	 *
	 * @param WP_REST_Response $response The REST response object.
	 * @param WP_Post          $post The current post object.
	 * @return WP_REST_Response Modified response object with updated preview link.
	 */
	public function set_headless_rest_preview_link( WP_REST_Response $response, WP_Post $post ): WP_REST_Response {
		if ( 'draft' === $post->post_status ) {
			// Set preview link for draft posts.
			$response->data['link'] = get_preview_post_link( $post );
		} elseif ( 'publish' === $post->post_status ) {
			// Modify the permalink for published posts to point to the frontend.
			$permalink = get_permalink( $post );
			if ( false !== stristr( $permalink, get_site_url() ) ) {
				$response->data['link'] = str_ireplace(
					get_site_url(),
					$this->get_frontend_url(),
					$permalink
				);
			}
		}

		return $response;
	}

	/**
	 * Override links within post content on save to point to the headless client.
	 *
	 * @param int $post_id Post ID.
	 */
	public function override_post_links( int $post_id ): void {
		remove_action( 'save_post', [ $this, 'override_post_links' ] );

		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		$post_content = $post->post_content;
		if ( false !== stripos( $post_content, get_site_url() ) ) {
			$new_post_content = str_ireplace(
				get_site_url(),
				$this->get_frontend_url(),
				$post_content
			);

			// Update the post with the modified content.
			wp_update_post(
				[
					'ID'           => $post_id,
					'post_content' => wp_slash( $new_post_content ),
				]
			);
		}

		add_action( 'save_post', [ $this, 'override_post_links' ] );
	}

	/**
	 * Get the trimmed frontend URL.
	 *
	 * @return string|null Trimmed frontend URL or null if not defined.
	 */
	private function get_frontend_url(): ?string {
		if ( defined( 'NEXTJS_FRONTEND_URL' ) ) {
			return rtrim( NEXTJS_FRONTEND_URL, '/' );
		}
		return null;
	}
}
