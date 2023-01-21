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
$class_name = 'grd-acf-block-gallery';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

// Get photos.
$photos = get_field( 'photos' );

// Return early if no photos.
if ( ! $photos ) {
	return;
}
?>

<div <?php echo esc_attr( $anchor ); ?> class="<?php echo esc_attr( $class_name ); ?>">
	<?php

	// Loop through photos.
	foreach ( $photos as $photo ) :

		// Get photo data.
		$image_meta       = get_post_meta( $photo['id'], '_wp_attachment_metadata' );
		$alt              = $photo['alt'] ? $photo['alt'] : $photo['title'];
		$caption          = $photo['caption'] ? $photo['caption'] : $alt;
		$thumbnail        = $photo['sizes']['thumbnail'];
		$thumbnail_width  = $photo['sizes']['thumbnail-width'];
		$thumbnail_height = $photo['sizes']['thumbnail-height'];
		$url              = $photo['url'];
		$original_image   = $image_meta[0]['original_image'];
		$dominant_color   = $image_meta[0]['dominant_color'];
		$transparency     = $image_meta[0]['transparency'];

		// Get Exif data.
		$timestamp     = $image_meta[0]['image_meta']['created_timestamp'];
		$camera        = $image_meta[0]['image_meta']['camera'];
		$aperture      = $image_meta[0]['image_meta']['aperture'];
		$shutter_speed = $image_meta[0]['image_meta']['shutter_speed'];
		$focal_length  = $image_meta[0]['image_meta']['focal_length'];
		$iso           = $image_meta[0]['image_meta']['iso'];
		$keywords      = $image_meta[0]['image_meta']['keywords'];

		?>

		<figure class="grd-acf-block-gallery-item">
			<a
				data-caption="<?php echo esc_html( $caption ); ?>"
				data-fancybox="gallery"
				href="<?php echo esc_url( $url ); ?>"
			>
				<img
					alt="<?php echo esc_attr( $alt ); ?>"
					class="grd-acf-block-gallery-item__image <?php echo esc_attr( $transparency ? 'transparent' : 'not-transparent' ); ?>"
					data-dominant-color="<?php echo esc_attr( $dominant_color ); ?>"
					data-has-transparency="<?php echo esc_attr( $transparency ? 'true' : 'false' ); ?>"
					data-id="<?php echo esc_attr( $photo['id'] ); ?>"
					decode="async"
					height="<?php echo esc_attr( $thumbnail_height ); ?>"
					loading="lazy"
					src="<?php echo esc_url( $photo['sizes']['thumbnail'] ); ?>"
					style="--dominant-color: #<?php echo esc_attr( $dominant_color ); ?>;"
					width="<?php echo esc_attr( $thumbnail_width ); ?>"
				/>
			<?php if ( $caption ) : ?>
				<figcaption class="grd-acf-block-gallery-item__caption"><?php echo esc_html( $caption ); ?></figcaption>
			<?php endif; ?>
			</a>
		</figure>

	<?php endforeach; ?>
</div>
<?php
