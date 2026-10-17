<?php
/**
 * 1.9.7: page search in Elementor's link boxes.
 *
 * Elementor's link box (URL control) already suggests content as you type, using WordPress's
 * link search: published items only, matched on the title and text. In the Elementor editor this
 * adds the site's pages matched by name or by slug (e.g. "team", "/team/", "jabal-shams"),
 * Published and Draft, at the top of the list. Every page is linked by its page ID
 * (?page_id=…; 1.12.0, was drafts only), so the link follows the page when its address changes
 * (TS_Page_Links shows the current address).
 */

namespace TravelStore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Link_Search {

	public static function init() {
		add_filter( 'wp_link_query', array( __CLASS__, 'add_pages' ), 20, 2 );
	}

	/**
	 * @param array $results Core results (ID, title, permalink, info).
	 * @param array $query   The WP_Query arguments core used.
	 * @return array
	 */
	public static function add_pages( $results, $query ) {
		// Only the Elementor editor's link box, only the first page of results.
		if ( ! isset( $_POST['editor'] ) || 'elementor' !== $_POST['editor'] || ! empty( $query['offset'] ) || ! current_user_can( 'edit_pages' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- core checked the nonce in wp_ajax_wp_link_ajax().
			return $results;
		}
		$term = isset( $query['s'] ) ? trim( (string) $query['s'] ) : '';
		$slug = sanitize_title( trim( $term, "/ \t" ) );
		if ( '' === $term || '' === $slug ) {
			return $results;
		}

		global $wpdb;
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status IN ('publish','draft')
				 AND ( post_title LIKE %s OR post_name LIKE %s )
				 ORDER BY ( post_name = %s ) DESC, ( post_status = 'publish' ) DESC, post_title ASC LIMIT 20",
				'%' . $wpdb->esc_like( $term ) . '%',
				'%' . $wpdb->esc_like( $slug ) . '%',
				$slug
			)
		);
		if ( ! $ids ) {
			return is_array( $results ) ? $results : array();
		}

		$label = get_post_type_object( 'page' )->labels->singular_name;
		$pages = array();
		foreach ( $ids as $id ) {
			$post    = get_post( (int) $id );
			$pages[] = array(
				'ID'        => $post->ID,
				'title'     => trim( esc_html( wp_strip_all_tags( get_the_title( $post ) ) ) ) . ' (/' . ( '' !== $post->post_name ? $post->post_name : $post->ID ) . '/)',
				// 1.12.0: stored by page ID; TS_Page_Links shows the page's current address on the site.
				'permalink' => TS_Page_Links::stored_url( $post->ID ),
				'info'      => 'draft' === $post->post_status ? $label . ' · ' . __( 'Draft', 'travelstore-custom-widgets' ) : $label,
			);
		}

		// Pages first; core's other matches (trips, posts, pages found only in their text) after.
		$seen = wp_list_pluck( $pages, 'ID' );
		foreach ( (array) $results as $r ) {
			if ( ! in_array( $r['ID'], $seen, true ) ) {
				$pages[] = $r;
			}
		}
		return $pages;
	}
}
