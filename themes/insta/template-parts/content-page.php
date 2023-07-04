<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 *  @package InstaTheme
 *  @since 1.0.0
 */

$insta_hide_featured_img = get_field( 'hide_featured_image' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<?php ( $insta_hide_featured_img ) ? '' : insta_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'insta-theme' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
