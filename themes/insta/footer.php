<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 *  @package InstaTheme
 *  @since 1.0.0
 */

?>

		<footer id="colophon" class="site-footer">

			<div class="site-info">
				<p>&copy; 2008-<?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>

				<p class="license">
					<?php
						printf(
							/* translators: %s: creative commons link. */
							esc_html__( 'Unless otherwise noted, all content on this site is licensed under %s', 'insta-theme' ),
							'<a href="http://creativecommons.org/licenses/by-nc-nd/4.0/" rel="license">Creative Commons BY-NC-ND 4.0</a>'
						);
						?>
				</p>

				<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'insta-theme' ) ); ?>">
					<?php
						/* translators: %s: CMS name, i.e. WordPress. */
						printf( esc_html__( 'Proudly powered by %s', 'insta-theme' ), 'WordPress' );
					?>
				</a>
			</div><!-- .site-info -->
		</footer><!-- #colophon -->
	</div><!-- .right -->

<?php wp_footer(); ?>

</body>
</html>
