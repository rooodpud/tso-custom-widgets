<?php
/**
 * 1.12.0: page links that follow the page.
 *
 * A link to a page of this site is stored by the page's ID (…/?page_id=213, optionally with
 * #part), not by its address. When a widget is shown, every such link in its HTML is replaced
 * with the page's current address, so links stay right when a page gets a parent, a new slug or
 * moves. Without this class the stored form still works: WordPress forwards ?page_id= to the
 * page's address.
 *
 * - Works for every Elementor widget's output (TS widgets and Elementor's own), including links
 *   typed inside formatted text.
 * - Elementor keeps a copy of each widget's HTML (element cache); it is cleared whenever a page's
 *   address changes (slug, parent or status), so the copies never keep an old address.
 * - The link boxes' page search (TS_Link_Search) inserts this form.
 */

namespace TravelStore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Page_Links {

	public static function init() {
		add_filter( 'elementor/widget/render_content', array( __CLASS__, 'filter_html' ), 20 );
		add_action( 'post_updated', array( __CLASS__, 'maybe_clear_cache' ), 10, 3 );
	}

	/**
	 * The stored form of a link to a page: this site's address + ?page_id=N (+ #part).
	 */
	public static function stored_url( $post_id, $fragment = '' ) {
		return home_url( '/?page_id=' . (int) $post_id ) . ( '' !== $fragment ? '#' . ltrim( $fragment, '#' ) : '' );
	}

	/**
	 * The page a stored link points to (0 when the URL is not a ?page_id= link of this site).
	 */
	public static function page_id( $url ) {
		$url  = html_entity_decode( (string) $url, ENT_QUOTES );
		$home = wp_parse_url( home_url( '/' ) );
		$u    = wp_parse_url( $url );
		if ( empty( $u['query'] ) || ! empty( $u['host'] ) && ( strtolower( $u['host'] ) !== strtolower( $home['host'] ) ) ) {
			return 0;
		}
		$path = isset( $u['path'] ) ? untrailingslashit( $u['path'] ) : '';
		if ( '' !== $path && untrailingslashit( isset( $home['path'] ) ? $home['path'] : '' ) !== $path ) {
			return 0;
		}
		parse_str( $u['query'], $q );
		return 1 === count( $q ) && isset( $q['page_id'] ) && ctype_digit( (string) $q['page_id'] ) ? (int) $q['page_id'] : 0;
	}

	/**
	 * Current address for a stored page link; the URL unchanged when it is not one, or the page is gone.
	 */
	public static function resolve( $url ) {
		$id = self::page_id( $url );
		if ( ! $id || ! get_post( $id ) ) {
			return $url;
		}
		$link = get_permalink( $id );
		if ( ! $link ) {
			return $url;
		}
		$hash = strpos( $url, '#' );
		return $link . ( false !== $hash ? substr( $url, $hash ) : '' );
	}

	/**
	 * Replace stored page links in a widget's HTML with the pages' current addresses.
	 */
	public static function filter_html( $html ) {
		if ( false === strpos( (string) $html, 'page_id=' ) ) {
			return $html;
		}
		return preg_replace_callback(
			'/\shref=(["\'])([^"\']*page_id=\d+[^"\']*)\1/i',
			static function ( $m ) {
				$new = self::resolve( $m[2] );
				return $new === $m[2] ? $m[0] : ' href=' . $m[1] . esc_url( $new ) . $m[1];
			},
			$html
		);
	}

	/**
	 * A page's address changed: drop Elementor's saved copies of widget HTML (and its CSS files,
	 * which Elementor rebuilds on the next visit), so pages linking to it show the new address.
	 */
	public static function maybe_clear_cache( $post_id, $after, $before ) {
		if ( ! $after instanceof \WP_Post || ! $before instanceof \WP_Post || ! is_post_type_viewable( $after->post_type ) ) {
			return;
		}
		if ( $after->post_name === $before->post_name && (int) $after->post_parent === (int) $before->post_parent && $after->post_status === $before->post_status ) {
			return;
		}
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}
}
