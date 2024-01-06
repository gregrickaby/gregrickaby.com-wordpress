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
 * @package Grd\Photo_Gallery
 * @since 1.0.0
 */

namespace Grd\Photo_Gallery;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Grd\Photo_Gallery\Formatting;

// Get photos.
$photos = get_field( 'photos' );

// Return early if no photos.
if ( empty( $photos ) ) {
	return;
}
?>

<section id="grd-photo-gallery-gallery">
	<div class="grd-photo-gallery-grid">
		<div class="grd-photo-gallery-grid-sizer"></div>
		<?php
		// Start counter.
		$i = 1;

		// Loop through photos.
		foreach ( $photos as $photo ) :
			$photo_id          = $photo['ID'];
			$srcset            = wp_get_attachment_image_srcset( $photo_id );
			$metadata          = wp_get_attachment_metadata( $photo_id );
			$extended_metadata = get_post_meta( $photo_id, 'extended_image_meta' );

			$alt_text   = $photo['alt'] ?? '';
			$title_text = $photo['title'] ?? '';
			$caption    = $photo['caption'] ?? '';

			// If the photo has a caption use it, otherwise, check for the title or alt text.
			if ( ! $caption ) {
				$caption = $title_text ? $title_text : $alt_text;
			}

			// If the photo has an EXIF string use it, otherwise, build one.
			if ( ! empty( $extended_metadata['exif_string'] ) ) {
				$exif = $extended_metadata['exif_string'];
			} else {
				$exif = Formatting::format_exif_string( $extended_metadata, $metadata );
			}

			// Build fancybox caption.
			$fancy_caption = sprintf( '<p>%s</p><span class="exif">%s</span>', $caption, $exif );
			?>
			<figure class="grd-photo-gallery-image" data-image-number="<?php echo esc_attr( $i ); ?>" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
				<a
					data-caption="<?php echo esc_html( $fancy_caption ); ?>"
					data-fancybox
					data-slug="<?php echo esc_attr( $photo_id ); ?>"
					href="<?php echo esc_url( wp_get_original_image_url( $photo_id ) ); ?>"
				>
					<?php echo wp_get_attachment_image( $photo_id, 'medium' ); ?>
				</a>
				<?php if ( $caption ) : ?>
					<figcaption class="grd-photo-gallery-image-caption">
						<a
							data-caption="<?php echo esc_attr( $fancy_caption ); ?>"
							data-slug="<?php echo esc_attr( $photo_id ); ?>"
							href="<?php echo esc_url( wp_get_original_image_url( $photo_id ) ); ?>"
							title="<?php echo esc_attr( $caption ); ?>"
						>
							<?php echo esc_html( $caption ); ?>
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
