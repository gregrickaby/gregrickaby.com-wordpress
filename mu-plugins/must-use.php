<?php
/** Disable image size threshold. */
add_filter( 'big_image_size_threshold', '__return_false' );

/**
 * Allow additional mime types on upload.
 */
function grd_additional_mime_types( $mimes ) {
    $mimes['svg']  = 'image/svg+xml';
    $mimes['avif'] = 'image/avif';
    return $mimes;
}
add_filter( 'upload_mimes', 'grd_additional_mime_types' );
