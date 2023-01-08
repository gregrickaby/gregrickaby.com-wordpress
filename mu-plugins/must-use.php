<?php
/** Disable image size threshold. */
add_filter( 'big_image_size_threshold', '__return_false' );

/**
 * Allow additional mime types on upload.
 */
function grd_additional_mime_types( $mimes ) {
   $mimes['svg'] = 'image/svg+xml';
   $mimes['webp'] = 'image/webp';
   $mimes['heic'] = 'image/heic';
   $mimes['heif'] = 'image/heif';
   $mimes['heics'] = 'image/heic-sequence';
   $mimes['heifs'] = 'image/heif-sequence';
   $mimes['avif'] = 'image/avif';
   $mimes['avis'] = 'image/avif-sequence';
    return $mimes;
}
add_filter( 'upload_mimes', 'grd_additional_mime_types' );
