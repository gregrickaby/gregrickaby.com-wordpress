<?php
/**
 * Displays header navigation.
 *
 * @package SeedletChild
 * @since 1.0.4
 */

$seedlet_has_primary_nav       = has_nav_menu( 'primary' );
$seedlet_has_primary_nav_items = wp_nav_menu(
	[
		'theme_location' => 'primary',
		'fallback_cb'    => false,
		'echo'           => false,
	]
);

if ( $seedlet_has_primary_nav && $seedlet_has_primary_nav_items ) : ?>
	<nav id="site-navigation" class="primary-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Main', 'seedlet' ); ?>">
		<button id="primary-close-menu" class="button close">
			<span class="dropdown-icon close"><?php esc_html_e( 'Close', 'seedlet' ); ?> <?php echo seedlet_get_icon_svg( 'close' ); //phpcs:ignore ?></span>
			<span class="hide-visually collapsed-text"><?php esc_html_e( 'collapsed', 'seedlet' ); ?></span>
		</button>
		<?php
		$seedlet_location_name = 'primary';
		$seedlet_locations     = get_nav_menu_locations();
		$seedlet_menu_id       = $seedlet_locations[ $seedlet_location_name ];
		$seedlet_menu_obj      = wp_get_nav_menu_object( $seedlet_menu_id );

		wp_nav_menu(
			[
				'theme_location'  => 'primary',
				'menu_class'      => 'menu-wrapper',
				'container_class' => 'primary-menu-container',
				'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			]
		);
		?>
	</nav><!-- #site-navigation -->
<?php endif; ?>

<div class="menu-button-container">
	<?php if ( $seedlet_has_primary_nav && $seedlet_has_primary_nav_items ) : ?>
		<button id="primary-open-menu" class="button open">
			<span class="dropdown-icon open"><?php esc_html_e( 'Menu', 'seedlet' ); ?> <?php echo seedlet_get_icon_svg( 'menu' ); //phpcs:ignore ?></span>
			<span class="hide-visually expanded-text"><?php esc_html_e( 'expanded', 'seedlet' ); ?></span>
		</button>
	<?php endif; ?>
</div>
