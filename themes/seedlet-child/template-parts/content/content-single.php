<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Seedlet
 * @since 1.0.3
 */

$seedlet_hide_featured_img = get_field( 'hide_featured_image' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header default-max-width">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		<?php if ( ! is_page() ) : ?>
		<div class="entry-meta">
			<?php seedlet_entry_meta_header(); ?>
		</div><!-- .meta-info -->
		<?php endif; ?>
	</header>

	<?php ( $seedlet_hide_featured_img ) ? '' : seedlet_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					esc_html__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'seedlet' ),
					[
						'span' => [
							'class' => [],
						],
					]
				),
				get_the_title()
			)
		);

		wp_link_pages(
			[
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'seedlet' ),
				'after'  => '</div>',
			]
		);
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer default-max-width">
		<?php seedlet_entry_meta_footer(); ?>
	</footer><!-- .entry-footer -->

	<?php seedlet_author_bio(); ?>

</article><!-- #post-${ID} -->
