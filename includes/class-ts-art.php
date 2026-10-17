<?php
/**
 * Art library — SVG scenes bundled inside the plugin (assets/art/*.svg).
 *
 * Widgets offer these in a dropdown, so editors never handle SVG code and SVG
 * uploads can stay disabled on the site (DEC-015). Labels come from
 * assets/art/art.json; a file only appears in the dropdown if it exists.
 */

namespace TravelStore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Art {

	/**
	 * Slug => label map from art.json.
	 *
	 * @return array
	 */
	public static function labels() {
		static $labels = null;

		if ( null === $labels ) {
			$labels = array();
			$file   = TSW_PATH . 'assets/art/art.json';
			if ( is_readable( $file ) ) {
				$json = json_decode( (string) file_get_contents( $file ), true );
				if ( is_array( $json ) ) {
					$labels = $json;
				}
			}
		}

		return $labels;
	}

	/**
	 * Options for a SELECT control.
	 *
	 * @param bool $with_none Include an empty "None" option first.
	 * @return array
	 */
	public static function options( $with_none = true ) {
		$options = $with_none ? array( '' => __( 'None', 'travelstore-custom-widgets' ) ) : array();

		foreach ( self::labels() as $slug => $label ) {
			if ( self::exists( $slug ) ) {
				$options[ $slug ] = $label;
			}
		}

		return $options;
	}

	/**
	 * Is this a valid, existing art slug?
	 *
	 * @param mixed $slug Candidate slug.
	 * @return bool
	 */
	public static function exists( $slug ) {
		return is_string( $slug )
			&& preg_match( '/^[a-z0-9-]+$/', $slug )
			&& is_readable( TSW_PATH . 'assets/art/' . $slug . '.svg' );
	}

	/**
	 * Public URL of an art file, or '' when the slug is not valid.
	 *
	 * @param string $slug Art slug.
	 * @return string
	 */
	public static function url( $slug ) {
		// 1.9.4: the file date in the address, so an updated drawing is not served from a browser's cache.
		return self::exists( $slug ) ? TSW_URL . 'assets/art/' . $slug . '.svg?ver=' . filemtime( TSW_PATH . 'assets/art/' . $slug . '.svg' ) : '';
	}

	/**
	 * Intrinsic size from the SVG viewBox, so <img> can reserve its space.
	 *
	 * @param string $slug Art slug.
	 * @return array{0:int,1:int}|array Empty when unknown.
	 */
	public static function size( $slug ) {
		if ( ! self::exists( $slug ) ) {
			return array();
		}

		$svg = (string) file_get_contents( TSW_PATH . 'assets/art/' . $slug . '.svg' );
		if ( preg_match( '/viewBox="\s*[\d.\-]+\s+[\d.\-]+\s+([\d.]+)\s+([\d.]+)\s*"/', $svg, $m ) ) {
			return array( (int) round( (float) $m[1] ), (int) round( (float) $m[2] ) );
		}

		return array();
	}

	/**
	 * Whether the drawing is made to stretch to any box (preserveAspectRatio="none", e.g. the Salalah trail line).
	 *
	 * @param string $slug Art slug.
	 * @return bool
	 */
	public static function stretches( $slug ) {
		return self::exists( $slug )
			&& false !== strpos( (string) file_get_contents( TSW_PATH . 'assets/art/' . $slug . '.svg' ), 'preserveAspectRatio="none"' );
	}

	/**
	 * The art file's markup for placing it directly in the page (so text in it uses the site fonts).
	 * Only files shipped in assets/art/ are read. IDs are prefixed per widget so two drawings on one
	 * page cannot share gradients; scripts and event attributes are removed as a precaution.
	 *
	 * @param string $slug   Art slug.
	 * @param string $prefix Unique prefix (the widget ID).
	 * @param string $label  Screen-reader description; empty = decorative.
	 * @return string '' when the file is missing or not an SVG.
	 */
	public static function inline_svg( $slug, $prefix, $label = '' ) {
		if ( ! self::exists( $slug ) ) {
			return '';
		}

		$svg = (string) file_get_contents( TSW_PATH . 'assets/art/' . $slug . '.svg' );
		$svg = preg_replace( '/<\?xml.*?\?>|<!--.*?-->/s', '', $svg );
		$svg = preg_replace( '#<script\b.*?</script>#is', '', $svg );
		$svg = preg_replace( '/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $svg );
		$svg = trim( $svg );
		if ( 0 !== strpos( $svg, '<svg' ) ) {
			return '';
		}

		$p   = 'tsa' . preg_replace( '/[^a-z0-9]/i', '', (string) $prefix ) . '-';
		$svg = preg_replace( '/\bid="([^"]+)"/', 'id="' . $p . '$1"', $svg );
		$svg = preg_replace( '/url\(#([^)]+)\)/', 'url(#' . $p . '$1)', $svg );
		$svg = preg_replace( '/(xlink:)?href="#([^"]+)"/', '$1href="#' . $p . '$2"', $svg );

		// Accessibility attributes replace any the file carries.
		$svg  = preg_replace( '/^<svg\b([^>]*?)\s(role|aria-label|aria-hidden)="[^"]*"/', '<svg$1', $svg );
		$svg  = preg_replace( '/^<svg\b([^>]*?)\s(role|aria-label|aria-hidden)="[^"]*"/', '<svg$1', $svg );
		$attr = '' !== $label ? ' role="img" aria-label="' . esc_attr( $label ) . '"' : ' aria-hidden="true" focusable="false"';

		return (string) preg_replace( '/^<svg\b/', '<svg' . $attr, $svg, 1 );
	}
}
