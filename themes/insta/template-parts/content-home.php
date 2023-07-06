<?php
/**
 * Template part for displaying posts on the homepage.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 *  @package InstaTheme
 *  @since 1.0.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			?>
			<div class="entry-meta">
				<?php
				insta_posted_on();
				insta_posted_by();
				?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php insta_post_thumbnail(); ?>

	<div class="entry-content">
		<?php the_excerpt(); ?>
		<i class="fa-regular fa-hand-point-right"></i><a class="continue-reading" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Continue reading', 'insta-theme' ); ?>...</a>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php insta_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
