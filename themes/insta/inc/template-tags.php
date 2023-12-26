<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 *  @package InstaTheme
 *  @since 1.0.0
 */

if ( ! function_exists( 'insta_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function insta_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( 'Posted on %s', 'post date', 'insta-theme' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

endif;

if ( ! function_exists( 'insta_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function insta_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', 'insta-theme' ),
			'<span class="author vcard">' . esc_html( get_the_author() ) . '</span>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

endif;

if ( ! function_exists( 'insta_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function insta_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'insta-theme' ) );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<p class="cat-links"><i class="fa-solid fa-folder-open"></i>%1$s</p>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'insta-theme' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<p class="tags-links"><i class="fa-solid fa-tag"></i>%1$s</p>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<p class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( '<i class="fa-solid fa-comment"></i>Comment<span class="screen-reader-text"> on %s</span>', 'insta-theme' ),
						[
							'i'    => array(
								'class' => array(),
							),
							'span' => array(
								'class' => array(),
							),
						]
					),
					wp_kses_post( get_the_title() )
				)
			);
			echo '</p>';
		}
	}
endif;

if ( ! function_exists( 'insta_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function insta_post_thumbnail() {

		// Has the admin checked the box to hide the featured image?
		$insta_hide_featured_img = get_field( 'hide_featured_image' );

		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() || $insta_hide_featured_img ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php
					the_post_thumbnail(
						'post-thumbnail',
						array(
							'alt' => the_title_attribute(
								array(
									'echo' => false,
								)
							),
						)
					);
				?>
			</a>

			<?php
		endif; // End is_singular().
	}
endif;

if ( ! function_exists( 'insta_post_navigation' ) ) :
	/**
	 * Post navigation (previous / next post) for single posts.
	 */
	function insta_post_navigation() {
		the_posts_navigation(
			[
				'prev_text' => __( '<i class="fa-solid fa-angle-left"></i>Older Posts', 'insta-theme' ),
				'next_text' => __( 'Newer Posts<i class="fa-solid fa-angle-right"></i>', 'insta-theme' ),
			]
		);
	}
endif;
