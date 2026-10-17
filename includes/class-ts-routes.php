<?php
/**
 * Route library — map routes bundled inside the plugin (assets/routes/*.json).
 *
 * Each file holds one route already drawn for the TS Route Story widget: the whole line,
 * the stop positions (with label placement) and, per leg, the line on the big map and a
 * small fitted drawing for the "on the road" panel. Files are made by the route converter
 * (_ts-tools/route-converter) from a KML export; editors only pick a route from a dropdown.
 */

namespace TravelStore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Routes {

	/**
	 * Is this a valid, existing route slug?
	 *
	 * @param mixed $slug Candidate slug.
	 * @return bool
	 */
	public static function exists( $slug ) {
		return is_string( $slug )
			&& preg_match( '/^[a-z0-9-]+$/', $slug )
			&& is_readable( TSW_PATH . 'assets/routes/' . $slug . '.json' );
	}

	/**
	 * Slug => label for a SELECT control (every route file found).
	 *
	 * @return array
	 */
	public static function options() {
		$options = array();
		foreach ( (array) glob( TSW_PATH . 'assets/routes/*.json' ) as $file ) {
			$slug  = basename( $file, '.json' );
			$route = self::get( $slug );
			if ( $route ) {
				$options[ $slug ] = $route['name'];
			}
		}

		return $options;
	}

	/**
	 * Decoded route data, or null when the file is missing or malformed.
	 *
	 * @param string $slug Route slug.
	 * @return array|null
	 */
	public static function get( $slug ) {
		static $cache = array();

		if ( ! self::exists( $slug ) ) {
			return null;
		}
		if ( ! array_key_exists( $slug, $cache ) ) {
			$data = json_decode( (string) file_get_contents( TSW_PATH . 'assets/routes/' . $slug . '.json' ), true );
			$ok   = is_array( $data ) && ! empty( $data['line'] ) && ! empty( $data['stops'] ) && ! empty( $data['legs'] )
				&& count( $data['legs'] ) === count( $data['stops'] ) - 1;
			if ( $ok && empty( $data['name'] ) ) {
				$data['name'] = $slug;
			}
			$cache[ $slug ] = $ok ? $data : null;
		}

		return $cache[ $slug ];
	}
}
