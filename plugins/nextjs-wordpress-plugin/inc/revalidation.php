<?php
/**
 * Revalidation functionality.
 *
 * @author Greg Rickaby
 * @since 1.0.0
 * @package NEXTJS_WORDPRESS_PLUGIN
 */

namespace NEXTJS_WORDPRESS_PLUGIN;

/**
 * Handle post status transition.
 *
 * @param string $new_status New status.
 * @param string $old_status Old status.
 * @param object $post       The post object.
 */
function transition_handler( $new_status, $old_status, $post ): void {
	// If the post is a draft, bail.
	if ( 'draft' === $new_status && 'draft' === $old_status ) {
		return;
	}

	// Otherwise, revalidate..
	on_demand_revalidation( $post->post_name );
}
add_action( 'transition_post_status', __NAMESPACE__ . '\transition_handler', 10, 3 );

/**
 * Flush the frontend cache when a post is updated.
 *
 * This function will fire anytime a post is updated.
 * Including: the post status, comments, meta, terms, etc.
 *
 * @usage https://nextjswp.com/api/revalidate?slug=foo-bar-baz
 *
 * @see https://nextjs.org/docs/app/building-your-application/data-fetching/fetching-caching-and-revalidating#on-demand-revalidation
 *
 * @param string $slug The post slug.
 *
 * @return void.
 */
function on_demand_revalidation( $slug ): void {

	// Do not run on autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// No constants or slug? Bail.
	if ( ! defined( 'NEXTJS_FRONTEND_URL' ) || ! defined( 'NEXTJS_REVALIDATION_SECRET' ) || ! $slug ) {
		return;
	}

	// Build the revalidation URL.
	$revalidation_url = add_query_arg(
		'slug',
		$slug,
		esc_url_raw( rtrim( NEXTJS_FRONTEND_URL, '/' ) . '/api/revalidate' )
	);

	// POST to the revalidation URL.
	$response = wp_remote_post(
		$revalidation_url,
		[
			'headers' => [
				'x-vercel-revalidation-secret' => NEXTJS_REVALIDATION_SECRET,
			],
		]
	);

	// Check the response.
	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		error_log( 'Revalidation error: ' . $error_message ); // phpcs:ignore
	}
}
