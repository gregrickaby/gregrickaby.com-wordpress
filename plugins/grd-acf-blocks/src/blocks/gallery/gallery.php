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
 * @see https://www.advancedcustomfields.com/resources/acf-blocks-with-block-json/
 * @see https://www.advancedcustomfields.com/resources/acf_register_block_type/
 * @package SeedletChild
 * @since 1.0.0
 */

namespace Grd\Acf\Blocks\Gallery;

use Phospr\Fraction;

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
		$alt     = $photo['alt'] ? $photo['alt'] : $photo['title'];
		$caption = $photo['caption'] ? $photo['caption'] : $alt;

		// Get Exif data.
		$image_meta    = get_post_meta( $photo['id'], '_wp_attachment_metadata' );
		$camera        = $image_meta[0]['image_meta']['camera'] ? $image_meta[0]['image_meta']['camera'] : '';
		$aperture      = $image_meta[0]['image_meta']['aperture'] ? $image_meta[0]['image_meta']['aperture'] : '';
		$shutter_speed = $image_meta[0]['image_meta']['shutter_speed'] ? Fraction::fromFloat( $image_meta[0]['image_meta']['shutter_speed'] ) : '';
		$focal_length  = $image_meta[0]['image_meta']['focal_length'] ? $image_meta[0]['image_meta']['focal_length'] : '';
		$iso           = $image_meta[0]['image_meta']['iso'] ? $image_meta[0]['image_meta']['iso'] : '';

		// Build Exif string.
		$exif = "ƒ/{$aperture} | {$focal_length}mm | {$shutter_speed} sec | ISO {$iso} | {$camera}";
		?>

		<figure class="grd-acf-block-gallery-item">
			<a
				data-caption='&lt;p&gt;<?php echo esc_html( $caption ); ?>&lt;/p&gt; &lt;span class="exif"&gt;<?php echo esc_html( $exif ); ?>&lt;/span&gt;'
				data-fancybox="gallery"
				href="<?php echo esc_url( wp_get_original_image_url( $photo['ID'] ) ); ?>"
			>
			<?php echo wp_get_attachment_image( $photo['ID'] ); ?>
			<?php if ( $caption ) : ?>
				<figcaption class="grd-acf-block-gallery-item__caption"><?php echo esc_html( $caption ); ?></figcaption>
			<?php endif; ?>
			</a>
		</figure>

	<?php endforeach; ?>
</div>
<?php
