<?php
/**
 * Extra CSS escape hatch (developer-only) for every TS widget.
 *
 * Elementor Free has no Custom CSS control (it is Pro-only), and pages must never
 * depend on custom CSS (DEC-015). This is the optional safety valve: a collapsed
 * "Extra CSS" box in each widget's Advanced tab, administrators only. The value is
 * written into Elementor's generated post CSS file (not inline in the page).
 * `selector` in the box stands for this widget's root element.
 *
 * The acceptance scan flags any page whose widgets carry a non-empty value.
 */

namespace TravelStore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Extra_CSS {

	const SETTING = 'ts_extra_css';

	/**
	 * Hook into Elementor's per-element CSS generation.
	 */
	public static function init() {
		add_action( 'elementor/element/parse_css', array( __CLASS__, 'parse_css' ), 10, 2 );
	}

	/**
	 * Add an element's Extra CSS to the generated stylesheet.
	 *
	 * @param \Elementor\Core\Files\CSS\Post $post_css Post CSS file being built.
	 * @param \Elementor\Element_Base        $element  Element being parsed.
	 */
	public static function parse_css( $post_css, $element ) {
		if ( ! $post_css instanceof \Elementor\Core\Files\CSS\Post ) {
			return;
		}

		$data = $element->get_data();
		$css  = isset( $data['settings'][ self::SETTING ] ) ? (string) $data['settings'][ self::SETTING ] : '';
		$css  = self::clean( $css );

		if ( '' === $css ) {
			return;
		}

		$css = str_replace( 'selector', $post_css->get_element_unique_selector( $element ), $css );
		$post_css->get_stylesheet()->add_raw_css( $css );
	}

	/**
	 * Strip anything that is not plain CSS declarations/rules.
	 *
	 * @param string $css Raw CSS from the control.
	 * @return string
	 */
	public static function clean( $css ) {
		$css = wp_strip_all_tags( (string) $css );
		$css = preg_replace( '/@import[^;]*;?/i', '', $css );
		$css = preg_replace( '/(expression\s*\(|javascript\s*:|vbscript\s*:|-moz-binding|behavior\s*:)/i', '', $css );

		return trim( substr( $css, 0, 20000 ) );
	}
}
