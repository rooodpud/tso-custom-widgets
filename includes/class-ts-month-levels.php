<?php
/**
 * Month levels — the "great / good / tough" rating of twelve months, shared by
 * TS Destination Atlas and TS Season Matrix (1.10.0).
 *
 * Each month is one dropdown (lv1 … lv12) named after the month, so editors never type a
 * code such as "2,2,1,0". Values are words (great, good, tough) so Elementor's empty-first
 * dropdown rule is not an issue.
 */

namespace TravelStore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Month_Levels {

	/** Level key => numeric level (the draft's 2 / 1 / 0). */
	const RANK = array(
		'great' => 2,
		'good'  => 1,
		'tough' => 0,
	);

	/**
	 * Dropdown options.
	 */
	public static function options() {
		return array(
			'great' => __( 'Great', 'travelstore-custom-widgets' ),
			'good'  => __( 'Good, with a catch', 'travelstore-custom-widgets' ),
			'tough' => __( 'Tough (very hot, or closed)', 'travelstore-custom-widgets' ),
		);
	}

	/**
	 * Month names in the site language: array( long, short, letter ), each indexed 0–11.
	 * "long" is the three-letter name (Jan), as the drafts show it.
	 */
	public static function month_names() {
		global $wp_locale;
		$long   = array();
		$full   = array();
		$letter = array();
		for ( $m = 1; $m <= 12; $m++ ) {
			$name     = $wp_locale ? $wp_locale->get_month( $m ) : gmdate( 'F', gmmktime( 0, 0, 0, $m, 1 ) );
			$abbr     = $wp_locale ? $wp_locale->get_month_abbrev( $name ) : gmdate( 'M', gmmktime( 0, 0, 0, $m, 1 ) );
			$full[]   = $name;
			$long[]   = $abbr;
			$letter[] = function_exists( 'mb_substr' ) ? mb_substr( $abbr, 0, 1 ) : substr( $abbr, 0, 1 );
		}

		return array( $long, $letter, $full );
	}

	/**
	 * Twelve month dropdowns (lv1 … lv12) on a repeater or widget.
	 *
	 * @param \Elementor\Controls_Stack $target Repeater (or widget).
	 * @param array                     $args   heading: label of the group heading.
	 */
	public static function add_controls( $target, $args = array() ) {
		$args = array_merge( array( 'heading' => __( 'How each month is', 'travelstore-custom-widgets' ) ), $args );
		$full = self::month_names();
		$full = $full[2];
		$target->add_control(
			'levels_heading',
			array(
				'label'     => $args['heading'],
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		for ( $m = 1; $m <= 12; $m++ ) {
			$target->add_control(
				'lv' . $m,
				array(
					'label'   => $full[ $m - 1 ],
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 'great',
					'options' => self::options(),
				)
			);
		}
	}

	/**
	 * The twelve level keys of one item, index 0 = January.
	 *
	 * @param array $item Repeater item.
	 * @return string[] great|good|tough ×12.
	 */
	public static function read( $item ) {
		$out = array();
		for ( $m = 1; $m <= 12; $m++ ) {
			$v     = isset( $item[ 'lv' . $m ] ) ? (string) $item[ 'lv' . $m ] : 'great';
			$out[] = isset( self::RANK[ $v ] ) ? $v : 'great';
		}

		return $out;
	}

	/**
	 * Month number 1–12 from "m3" style dropdown values or plain numbers; 0 = none.
	 */
	public static function month_value( $value ) {
		$n = (int) ltrim( (string) $value, 'm' );

		return ( $n >= 1 && $n <= 12 ) ? $n : 0;
	}
}
