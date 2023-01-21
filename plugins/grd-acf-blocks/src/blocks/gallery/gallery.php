<?php
/**
 * Gallery block.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during backend preview render.
 * @param   int $post_id The post ID the block is rendering content against.
 *          This is either the post ID currently being displayed inside a query loop,
 *          or the post ID of the post hosting this block.
 * @param   array $context The context provided to the block by the post or it's parent block.
 * @package SeedletChild
 * @since 1.0.0
 */

namespace Grd\Acf\Blocks\Gallery;

// Support custom "anchor" values.
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
	$anchor = 'id="' . esc_attr( $block['anchor'] ) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'acf-block-gallery';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

// Get photos.
$photos = get_field( 'photos' );
?>

<?php
if ( $photos ) {
	?>
	<div <?php echo esc_attr( $anchor ); ?>class="<?php echo esc_attr( $class_name ); ?>">
		<?php foreach ( $photos as $photo ) : ?>
			<figure class="acf-block-gallery-item">
				<a data-fancybox="gallery" href="<?php echo esc_url( $photo['url'] ); ?>">
					<img alt="<?php echo esc_attr( $photo['alt'] ); ?>" src="<?php echo esc_url( $photo['sizes']['thumbnail'] ); ?>" />
				<img alt="<?php echo esc_attr( $photo['alt'] ); ?>" src="<?php echo esc_url( $photo['url'] ); ?>" />
				<?php if ( $photo['caption'] ) : ?>
					<figcaption class="acf-block-gallery-item__caption"><?php echo esc_html( $photo['caption'] ); ?></figcaption>
				<?php endif; ?>
				</a>
			</figure>
		<?php endforeach; ?>
	</div>
	<?php
} else {
	?>
		<p><?php esc_html_e( 'No photos have been added yet . ', 'grd-acf-blocks' ); ?></p>
	<?php
};
