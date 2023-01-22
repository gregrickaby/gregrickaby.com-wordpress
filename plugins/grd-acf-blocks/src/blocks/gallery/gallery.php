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
if ( empty( $photos ) ) {
	return;
}
?>

<div <?php echo esc_attr( $anchor ); ?> class="<?php echo esc_attr( $class_name ); ?>">
	<?php

	// Loop through photos.
	foreach ( $photos as $photo ) :

		// Get photo data.
		$photo_id   = $photo['ID'];
		$alt        = $photo['alt'] ?: $photo['title'];
		$caption    = $photo['caption'] ? $photo['caption'] : $alt;
		$srcset     = wp_get_attachment_image_srcset( $photo_id );
		$image_meta = get_post_meta( $photo_id, '_wp_attachment_metadata' );

		// Get Exif data.
		$aperture      = $image_meta[0]['image_meta']['aperture'] ? "ƒ/{$image_meta[0]['image_meta']['aperture']} |" : '';
		$camera        = $image_meta[0]['image_meta']['camera'] ?: '';
		$focal_length  = $image_meta[0]['image_meta']['focal_length'] ? "{$image_meta[0]['image_meta']['focal_length']}mm |" : '';
		$iso           = $image_meta[0]['image_meta']['iso'] ? "ISO {$image_meta[0]['image_meta']['iso']} |" : '';
		$shutter_speed = $image_meta[0]['image_meta']['shutter_speed'] ? Fraction::fromFloat( $image_meta[0]['image_meta']['shutter_speed'] ) . 'sec |' : '';

		// Build Exif string.
		$exif = sprintf(
			'%s %s %s %s %s',
			$focal_length,
			$aperture,
			$shutter_speed,
			$iso,
			$camera
		);

		// Build caption for Fancybox.
		$fancy_caption = sprintf(
			'<p>%s</p><span class="exif">%s</span>',
			$caption,
			$exif
		)
		?>

		<figure class="grd-acf-block-image">
			<a
				data-caption="<?php echo esc_attr( $fancy_caption ); ?>"
				data-fancybox="gallery"
				data-slug="<?php echo esc_attr( $photo_id ); ?>"
				data-srcset="<?php echo esc_attr( $srcset ); ?>"
				href="<?php echo esc_url( wp_get_original_image_url( $photo_id ) ); ?>"
			>
			<?php echo wp_get_attachment_image( $photo_id ); ?>
			<?php if ( $caption ) : ?>
				<figcaption class="grd-acf-block-caption"><?php echo esc_html( $caption ); ?></figcaption>
			<?php endif; ?>
			</a>
		</figure>

	<?php endforeach; ?>
</div>
<?php
