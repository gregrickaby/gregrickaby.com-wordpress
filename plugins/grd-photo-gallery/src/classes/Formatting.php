<?php
/**
 * Formatting class.
 *
 * @package Grd\Photo_Gallery
 * @since 1.12.0
 */

namespace Grd\Photo_Gallery;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formatting class.
 *
 * This class provides helper methods for formatting EXIF data.
 *
 * @author Greg Rickaby
 * @since 1.12.0
 */
class Formatting {

	/**
	 * Format a single EXIF string from all image metadata.
	 *
	 * This method is useful for displaying EXIF data in a gallery.
	 *
	 * @param array $extended_image_meta The extended image metadata.
	 * @param array $metadata        The attachment metadata.
	 *
	 * @return string The generated exif string.
	 */
	public static function format_exif_string( array $extended_image_meta, array $metadata ): string {

		// Extract metadata components.
		$aperture      = $metadata['image_meta']['aperture'] ?? '';
		$camera        = $metadata['image_meta']['camera'] ?? '';
		$focal_length  = $metadata['image_meta']['focal_length'] ?? '';
		$iso           = $metadata['image_meta']['iso'] ?? '';
		$lens          = $extended_image_meta['lens'] ?? '';
		$make          = $extended_image_meta['make'] ?? '';
		$shutter_speed = $metadata['image_meta']['shutter_speed'] ? self::format_shutter_speed( $metadata['image_meta']['shutter_speed'] ) : '';
		$software      = $extended_image_meta['software'] ?? '';

		// Create the caption string.
		$caption_parts = array_filter(
			[
				'camera'        => $make . ' ' . $camera,
				'lens'          => $lens,
				'focal_length'  => $focal_length ? $focal_length . 'mm' : '',
				'aperture'      => $aperture ? 'ƒ/' . $aperture : '',
				'shutter_speed' => $shutter_speed ? $shutter_speed . 's' : '',
				'iso'           => $iso ? 'ISO' . $iso : '',
				'software'      => $software,
			]
		);

		return implode( ' | ', $caption_parts );
	}

	/**
	 * Format shutter speed.
	 *
	 * This method converts shutter speed to a fraction if it's a decimal.
	 *
	 * @param string $shutter_speed The shutter speed.
	 *
	 * @return string The formatted shutter speed.
	 */
	public static function format_shutter_speed( string $shutter_speed ): string {

		// Bail if shutter speed is empty.
		if ( empty( $shutter_speed ) ) {
			return '';
		}

		// Convert to float if it's not already a fraction.
		if ( strpos( $shutter_speed, '/' ) === false ) {
			$shutter_speed = (float) $shutter_speed;
		}

		// For very fast shutter speeds (less than 1 second).
		if ( $shutter_speed < 1 && $shutter_speed > 0 ) {
			// Invert the decimal to get a fraction.
			$fraction = 1 / $shutter_speed;

			// Round to the nearest whole number to simplify the fraction.
			$rounded_fraction = round( $fraction );

			return "1/{$rounded_fraction}";
		} elseif ( $shutter_speed >= 1 ) {
			return round( $shutter_speed, 2 );
		}

		// Return original shutter speed if it's already a fraction.
		return $shutter_speed;
	}

	/**
	 * Convert DMS (Degrees, Minutes, Seconds) format to decimal degrees.
	 *
	 * @param string $dms The DMS string (e.g., "31/1, 251797205/10000000, 0/1").
	 * @param string $hemisphere The hemisphere ('N', 'S', 'E', 'W').
	 *
	 * @return float The coordinate in decimal degrees.
	 */
	public static function dms_to_decimal( string $dms, string $hemisphere ): float {

		// Bail if DMS or hemisphere is empty.
		if ( empty( $dms ) || empty( $hemisphere ) ) {
			return 0;
		}

		// Split the DMS string into parts (degrees, minutes, seconds).
		$parts = explode( ', ', $dms );

		// Convert each part to a float. If a part is missing, default to 0.
		$degrees = count( $parts ) > 0 ? self::convert_to_float( $parts[0] ) : 0;
		$minutes = count( $parts ) > 1 ? self::convert_to_float( $parts[1] ) : 0;
		$seconds = count( $parts ) > 2 ? self::convert_to_float( $parts[2] ) : 0;

		// Determine if the coordinates should be negated based on the hemisphere.
		$flip = ( 'W' === $hemisphere || 'S' === $hemisphere ) ? -1 : 1;

		// Calculate the decimal degrees.
		return ( $degrees + $minutes / 60 + $seconds / 3600 ) * $flip;
	}

	/**
	 * Convert a fractional string to a float.
	 *
	 * @param string $fraction The fractional string.
	 *
	 * @return float The float value of the fraction.
	 */
	private static function convert_to_float( string $fraction ): float {

		// Bail if the fraction is empty.
		if ( empty( $fraction ) ) {
			return 0;
		}

		// Split the fraction into numerator and denominator.
		$fraction_parts = explode( '/', $fraction );

		// If the fraction is valid (has two parts), divide numerator by denominator.
		if ( count( $fraction_parts ) === 2 ) {
			return (float) $fraction_parts[0] / (float) $fraction_parts[1];
		}

		// If it's not a valid fraction, return the float value of the string.
		return (float) $fraction;
	}
}
