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
 * @param array  $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id The post ID the block is rendering content against.
 * @param array  $context The context provided to the block by the post or its parent block.
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

<section id="grd-photo-gallery-gallery" data-testid="grd-photo-gallery">
	<div class="grd-photo-gallery-grid" data-testid="grd-photo-gallery-grid">
	<?php
	$image_index = 1;

	// Loop through photos.
	foreach ( $photos as $photo ) :
		$photo_id          = $photo['ID'];
		$srcset            = wp_get_attachment_image_srcset( $photo_id );
		$metadata          = wp_get_attachment_metadata( $photo_id );
		$extended_metadata = get_post_meta( $photo_id, 'extended_image_meta' );

		// Define image attributes, escaping them once at assignment.
		$alt_text   = esc_attr( $photo['alt'] ?? '' );
		$title_text = esc_attr( $photo['title'] ?? '' );
		$caption    = esc_html( $photo['caption'] ?? $title_text ?? $alt_text );

		// Get EXIF data, with a fallback to formatted metadata if not present.
		$exif = ! empty( $extended_metadata['exif_string'] )
			? esc_html( $extended_metadata['exif_string'] )
			: Formatting::format_exif_string( $extended_metadata, $metadata );

		// Build Fancybox caption.
		$fancy_caption = sprintf(
			'<p>%s</p> <span class="exif">%s</span>',
			wp_kses_post( $caption ),
			wp_kses_post( $exif )
		);
		?>
		<figure class="grd-photo-gallery-figure" data-image-number="<?php echo esc_attr( $image_index ); ?>" data-testid="image-<?php echo esc_attr( $image_index ); ?>" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
			<a
				aria-label="<?php echo esc_attr( 'View full-size image of ' . $caption ); ?>"
				class="grd-photo-gallery-image-link"
				data-caption="<?php echo esc_attr( $fancy_caption ); ?>"
				data-fancybox
				data-slug="<?php echo esc_attr( $photo_id ); ?>"
				data-testid="image-link"
				href="<?php echo esc_url( wp_get_original_image_url( $photo_id ) ); ?>"
			>
				<?php
				// Output image.
				echo wp_get_attachment_image(
					$photo_id,
					'medium',
					false,
					array(
						'alt'   => $alt_text,
						'class' => 'grd-photo-gallery-image',
					)
				);
				?>
			</a>
			<?php if ( $caption ) : ?>
				<figcaption class="grd-photo-gallery-image-caption" data-testid="image-caption">
					<a
						class="grd-photo-gallery-image-caption-link"
						data-caption="<?php echo esc_attr( $fancy_caption ); ?>"
						data-slug="<?php echo esc_attr( $photo_id ); ?>"
						data-testid="image-caption-link"
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
		++$image_index;
	endforeach;
	?>
	</div>
</section>
