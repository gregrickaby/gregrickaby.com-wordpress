<?php
/**
 * Displays footer site info.
 *
 * @package SeedletChild
 * @since 1.0.0
 */

	$has_social_nav       = has_nav_menu( 'social' );
	$has_social_nav_items = wp_nav_menu(
		array(
			'theme_location' => 'social',
			'fallback_cb'    => false,
			'echo'           => false,
		)
	);
	?>

<?php if ( $has_social_nav && $has_social_nav_items ) : ?>
	<nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Social Links Menu', 'seedlet' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'social',
				'link_before'    => '<span class="screen-reader-text">',
				'link_after'     => '</span>' . seedlet_get_icon_svg( 'link' ),
				'depth'          => 1,
			)
		);
		?>
	</nav><!-- .social-navigation -->
<?php endif; ?>

<div class="site-info">
	<p>
		Copyright &copy; 2008-<?php echo esc_html( gmdate( 'Y' ) ); ?>
		Greg Rickaby.
		&middot;
		<a href="https://gregrickaby.com/feed/">RSS feed</a>
	</p>
	<p>
		Unless otherwise noted, all content on this site is licensed under <a href="http://creativecommons.org/licenses/by-nc-nd/4.0/" rel="license">Creative Commons BY-NC-ND 4.0</a>.
	</p>
</div><!-- .site-info -->
