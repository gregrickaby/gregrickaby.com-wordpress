<?php
/**
 * ACF Block: Gallery
 *
 * @see https://www.advancedcustomfields.com/resources/acf-blocks-with-block-json/
 * @see https://www.advancedcustomfields.com/resources/acf_register_block_type/
 * @see https://fancyapps.com/docs/ui/fancybox
 * @see https://masonry.desandro.com/
 * @see https://imagesloaded.desandro.com/
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during backend preview render.
 * @param   int $post_id The post ID the block is rendering content against.
 *          This is either the post ID currently being displayed inside a query loop,
 *          or the post ID of the post hosting this block.
 * @param   array $context The context provided to the block by the post or it's parent block.
 *
 * @package Grd\Acf\Blocks
 * @since 1.0.0
 */

namespace Grd\ACF_Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Import dependencies.
use Phospr\Fraction;

// Get photos.
$photos = \get_field( 'photos' );

// Return early if no photos.
if ( empty( $photos ) ) {
	return;
}
?>

<section id="grd-acf-block-gallery">
	<div class="grd-acf-block-grid">
		<div class="grd-acf-block-grid-sizer"></div>
	<?php

	// Start counter.
	$i = 1;
	// Loop through photos.
	foreach ( $photos as $photo ) :

		// Get photo data.
		$photo_id   = $photo['ID'];
		$alt        = $photo['alt'] ?: $photo['title'];
		$caption    = $photo['caption'] ? $photo['caption'] : $alt;
		$srcset     = \wp_get_attachment_image_srcset( $photo_id );
		$image_meta = \get_post_meta( $photo_id, '_wp_attachment_metadata' );

		// Get Exif data.
		$aperture      = $image_meta[0]['image_meta']['aperture'] ? "ƒ/{$image_meta[0]['image_meta']['aperture']} |" : '';
		$camera        = $image_meta[0]['image_meta']['camera'] ?: '';
		$focal_length  = $image_meta[0]['image_meta']['focal_length'] ? round( 1 * $image_meta[0]['image_meta']['focal_length'] ) . 'mm |' : '';
		$iso           = $image_meta[0]['image_meta']['iso'] ? "ISO {$image_meta[0]['image_meta']['iso']} |" : '';
		$shutter_speed = $image_meta[0]['image_meta']['shutter_speed'] ? Fraction::fromFloat( $image_meta[0]['image_meta']['shutter_speed'] ) . 's |' : '';

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
		);
		?>

		<figure class="grd-acf-block-image" data-image-number="<?php echo esc_attr( $i ); ?>" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
			<a
				data-caption="<?php echo \esc_attr( $fancy_caption ); ?>"
				data-fancybox
				data-slug="<?php echo \esc_attr( $photo_id ); ?>"
				href="<?php echo \esc_url( \wp_get_original_image_url( $photo_id ) ); ?>"
			>
				<?php echo \wp_get_attachment_image( $photo_id, 'medium' ); ?>
			</a>

			<?php if ( $caption ) : ?>
				<figcaption class="grd-acf-block-image-caption">
					<a
						data-caption="<?php echo \esc_attr( $fancy_caption ); ?>"
						data-slug="<?php echo \esc_attr( $photo_id ); ?>"
						href="<?php echo \esc_url( \wp_get_original_image_url( $photo_id ) ); ?>"
						title="<?php echo \esc_attr( $caption ); ?>"
					>
					<?php echo \esc_html( $caption ); ?>
					</a>
				</figcaption>
			<?php endif; ?>
		</figure>
		<?php
		// Increment counter.
		++$i;

		endforeach;
	?>
	</div>
</section>
<?php
