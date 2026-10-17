<?php
/**
 * Widget: TS Trip Cards — WP Travel Engine trips as Travel Store cards.
 *
 * The drafts mark the tour and trip cards as WP Travel Engine slots, shown as a grid
 * (never a carousel). This widget reads the trips from WP Travel Engine (title, photo,
 * duration, price, link, categories, Trip Facts) and draws them with the Media Cards look,
 * so the cards follow the site design without page CSS (DEC-015).
 * If WP Travel Engine is not active the widget prints nothing on the public site.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Trip_Cards extends TS_Base_Widget {

	const TAXONOMIES = array( 'destination', 'activities', 'trip_types', 'difficulty' );

	public function get_name() {
		return 'ts-trip-cards';
	}

	public function get_title() {
		return __( 'TS Trip Cards (WP Travel Engine)', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_keywords() {
		return array( 'trips', 'tours', 'travel engine', 'wte', 'packages', 'price', 'travel' );
	}

	public function get_style_depends() {
		return array( 'ts-base', 'ts-media-cards', $this->get_name() );
	}

	private static function wte_active() {
		return post_type_exists( 'trip' ) && function_exists( 'wp_travel_engine_get_actual_trip_price' );
	}

	/**
	 * Published trips for the picker (editor only).
	 */
	private function trip_options() {
		if ( ! post_type_exists( 'trip' ) ) {
			return array();
		}
		$options = array();
		foreach ( get_posts( array( 'post_type' => 'trip', 'post_status' => 'publish', 'numberposts' => 300, 'orderby' => 'title', 'order' => 'ASC', 'suppress_filters' => false ) ) as $post ) {
			$options[ (string) $post->ID ] = get_the_title( $post );
		}

		return $options;
	}

	/**
	 * Categories for the picker, as "taxonomy:term id" => "Taxonomy: Term".
	 */
	private function term_options() {
		$options = array();
		foreach ( self::TAXONOMIES as $tax ) {
			$object = get_taxonomy( $tax );
			if ( ! $object ) {
				continue;
			}
			$terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false, 'number' => 300 ) );
			if ( is_wp_error( $terms ) ) {
				continue;
			}
			foreach ( $terms as $term ) {
				$options[ $tax . ':' . $term->term_id ] = $object->labels->singular_name . ': ' . $term->name;
			}
		}

		return $options;
	}

	protected function register_controls() {
		$editing = is_admin() || ( \Elementor\Plugin::$instance->editor && \Elementor\Plugin::$instance->editor->is_edit_mode() );

		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Trips', 'travelstore-custom-widgets' ) )
		);
		if ( ! self::wte_active() ) {
			$this->add_control(
				'wte_missing',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => esc_html__( 'WP Travel Engine is not active, so there are no trips to show.', 'travelstore-custom-widgets' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				)
			);
		}
		$this->add_control(
			'source',
			array(
				'label'   => __( 'Which trips', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'pick',
				'options' => array(
					'pick'   => __( 'Trips I choose', 'travelstore-custom-widgets' ),
					'terms'  => __( 'Trips in these categories', 'travelstore-custom-widgets' ),
					'latest' => __( 'The newest trips', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'trips',
			array(
				'label'       => __( 'Trips', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $editing ? $this->trip_options() : array(),
				'description' => __( 'Shown in the order chosen here. Only published trips are listed.', 'travelstore-custom-widgets' ),
				'condition'   => array( 'source' => 'pick' ),
			)
		);
		$this->add_control(
			'terms',
			array(
				'label'       => __( 'Categories', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $editing ? $this->term_options() : array(),
				'description' => __( 'Destinations, activities, trip types or difficulty levels from WP Travel Engine. A trip in any of them is shown.', 'travelstore-custom-widgets' ),
				'condition'   => array( 'source' => 'terms' ),
			)
		);
		$this->add_control(
			'count',
			array(
				'label'     => __( 'Number of trips', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3,
				'min'       => 1,
				'max'       => 24,
				'condition' => array( 'source!' => 'pick' ),
			)
		);
		$this->add_control(
			'orderby',
			array(
				'label'     => __( 'Order', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'date'       => __( 'Newest first', 'travelstore-custom-widgets' ),
					'title'      => __( 'By name (A–Z)', 'travelstore-custom-widgets' ),
					'menu_order' => __( 'Custom order (WP Travel Engine)', 'travelstore-custom-widgets' ),
				),
				'condition' => array( 'source' => 'terms' ),
			)
		);
		$this->add_control(
			'empty_text',
			array(
				'label'       => __( 'Text when no trip matches', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'description' => __( 'Optional. Leave empty to show nothing on the page.', 'travelstore-custom-widgets' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Content: what each card shows ---------------- */
		$this->start_controls_section(
			'ts_card_content',
			array( 'label' => __( 'Card content', 'travelstore-custom-widgets' ) )
		);
		// The photo is the trip's Featured Image in WP Travel Engine; Elementor's Image Resolution applies to it.
		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			array(
				'name'        => 'image',
				'default'     => 'large',
				'description' => __( 'Size of the trip photo (the trip\'s Featured Image).', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'show_badge',
			array(
				'label'        => __( 'Duration badge on the picture', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'kicker_source',
			array(
				'label'   => __( 'Small caption', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'activities_difficulty',
				'options' => array(
					''                      => __( 'None', 'travelstore-custom-widgets' ),
					'destination'           => __( 'Destination', 'travelstore-custom-widgets' ),
					'activities'            => __( 'Activity', 'travelstore-custom-widgets' ),
					'trip_types'            => __( 'Trip type', 'travelstore-custom-widgets' ),
					'difficulty'            => __( 'Difficulty', 'travelstore-custom-widgets' ),
					'activities_difficulty' => __( 'Activity · Difficulty', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_tag_control( 'title_tag', __( 'Title level', 'travelstore-custom-widgets' ), 'h3' );
		$this->add_control(
			'show_excerpt',
			array(
				'label'        => __( 'Short description', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'The trip\'s excerpt (or the start of its description).', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'excerpt_words',
			array(
				'label'     => __( 'Maximum words', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 24,
				'min'       => 5,
				'max'       => 80,
				'condition' => array( 'show_excerpt' => 'yes' ),
			)
		);
		$this->add_control(
			'show_facts',
			array(
				'label'        => __( 'Key facts (from the trip\'s Trip Facts)', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'For example Departs, Best time, Swimming, Suits — as entered in WP Travel Engine.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'facts_max',
			array(
				'label'     => __( 'Number of facts', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'min'       => 1,
				'max'       => 8,
				'condition' => array( 'show_facts' => 'yes' ),
			)
		);
		$this->add_control(
			'show_price',
			array(
				'label'        => __( 'Price', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'price_prefix',
			array(
				'label'     => __( 'Text above the price', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'From', 'travelstore-custom-widgets' ),
				'condition' => array( 'show_price' => 'yes' ),
			)
		);
		$this->add_control(
			'no_price_text',
			array(
				'label'       => __( 'Text when a trip has no price', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'For example: On request. Leave empty to leave the price out.', 'travelstore-custom-widgets' ),
				'condition'   => array( 'show_price' => 'yes' ),
			)
		);
		$this->add_control(
			'button_text',
			array(
				'label'       => __( 'Button text', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'View tour', 'travelstore-custom-widgets' ),
				'description' => __( 'Opens the trip page. Leave empty to hide the button (the title still links to the trip).', 'travelstore-custom-widgets' ),
				'separator'   => 'before',
			)
		);
		$this->add_control(
			'button_style',
			array(
				'label'   => __( 'Button look', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'primary',
				'options' => $this->button_style_options(),
			)
		);
		$this->add_control(
			'color_a',
			array(
				'label'       => __( 'Placeholder colour 1', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#807A67',
				'separator'   => 'before',
				'description' => __( 'Shown as a colour blend when a trip has no featured image.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-mcard__media' => '--ts-a: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'color_b',
			array(
				'label'     => __( 'Placeholder colour 2', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2C8828',
				'selectors' => array( '{{WRAPPER}} .ts-mcard__media' => '--ts-b: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Content: button under the grid ---------------- */
		$this->start_controls_section(
			'ts_more',
			array( 'label' => __( 'Button under the cards', 'travelstore-custom-widgets' ) )
		);
		$this->add_button_controls( 'more', __( 'Button', 'travelstore-custom-widgets' ), 'secondary' );
		$this->end_controls_section();

		/* ---------------- Style: layout ---------------- */
		$this->start_controls_section(
			'ts_section_layout',
			array(
				'label' => __( 'Layout', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
				'selectors'      => array( '{{WRAPPER}} .ts-mcards__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ),
			)
		);
		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Space between cards', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 22, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-mcards__grid' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'media_height',
			array(
				'label'      => __( 'Picture height', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 60, 'max' => 500 ) ),
				'default'    => array( 'size' => 170, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-mcard__media' => 'height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'card_bg', __( 'Card background', 'travelstore-custom-widgets' ), '.ts-mcard', 'background-color', 'ts_sand' );
		$this->add_color_control( 'card_border', __( 'Card border colour', 'travelstore-custom-widgets' ), '.ts-mcard', 'border-color' );
		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Card corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-mcard' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'label'    => __( 'Card shadow', 'travelstore-custom-widgets' ),
				'selector' => '{{WRAPPER}} .ts-mcard',
			)
		);
		$this->add_responsive_control(
			'body_padding',
			array(
				'label'      => __( 'Text padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-mcard__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'badge_position',
			array(
				'label'   => __( 'Badge position', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => array(
					'top-left'     => __( 'Top left', 'travelstore-custom-widgets' ),
					'top-right'    => __( 'Top right', 'travelstore-custom-widgets' ),
					'bottom-left'  => __( 'Bottom left', 'travelstore-custom-widgets' ),
					'bottom-right' => __( 'Bottom right', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_responsive_control(
			'more_space',
			array(
				'label'      => __( 'Space above the button under the cards', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-trips__more' => 'margin-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: trip photo (Elementor's Image options) ---------------- */
		$this->start_controls_section(
			'ts_section_image_style',
			array(
				'label' => __( 'Trip photo', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_image_style_controls( '.ts-mcard__img', '.ts-mcard__media', '.ts-mcard:hover .ts-mcard__img' );
		$this->end_controls_section();

		/* ---------------- Style: text ---------------- */
		$this->start_controls_section(
			'ts_section_text_style',
			array(
				'label' => __( 'Text', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'kicker_typography', __( 'Caption typography', 'travelstore-custom-widgets' ), '.ts-mcard__kicker' );
		$this->add_color_control( 'kicker_color', __( 'Caption colour', 'travelstore-custom-widgets' ), '.ts-mcard__kicker', 'color', 'ts_terracotta_dark' );
		$this->add_typography_control( 'title_typography', __( 'Title typography', 'travelstore-custom-widgets' ), '.ts-mcard__title', 'ts_heading' );
		$this->add_color_control( 'title_color', __( 'Title colour', 'travelstore-custom-widgets' ), '.ts-mcard__title, {{WRAPPER}} .ts-mcard__title a', 'color', 'ts_espresso' );
		$this->add_typography_control( 'text_typography', __( 'Text typography', 'travelstore-custom-widgets' ), '.ts-mcard__text', 'ts_body' );
		$this->add_color_control( 'text_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-mcard__text', 'color', 'ts_espresso_70' );
		$this->add_typography_control( 'fact_label_typography', __( 'Fact label typography', 'travelstore-custom-widgets' ), '.ts-mcard__fact dt' );
		$this->add_color_control( 'fact_label_color', __( 'Fact label colour', 'travelstore-custom-widgets' ), '.ts-mcard__fact dt' );
		$this->add_typography_control( 'fact_value_typography', __( 'Fact value typography', 'travelstore-custom-widgets' ), '.ts-mcard__fact dd' );
		$this->add_color_control( 'fact_value_color', __( 'Fact value colour', 'travelstore-custom-widgets' ), '.ts-mcard__fact dd' );
		$this->add_typography_control( 'price_label_typography', __( '"From" typography', 'travelstore-custom-widgets' ), '.ts-trip__price' );
		$this->add_color_control( 'price_label_color', __( '"From" colour', 'travelstore-custom-widgets' ), '.ts-trip__price' );
		$this->add_typography_control( 'price_typography', __( 'Price typography', 'travelstore-custom-widgets' ), '.ts-trip__price b' );
		$this->add_color_control( 'price_color', __( 'Price colour', 'travelstore-custom-widgets' ), '.ts-trip__price b' );
		$this->add_color_control( 'foot_line', __( 'Line above the price', 'travelstore-custom-widgets' ), '.ts-trip__foot', 'border-top-color' );
		$this->add_typography_control( 'badge_typography', __( 'Badge typography', 'travelstore-custom-widgets' ), '.ts-mcard__badge' );
		$this->add_color_control( 'badge_color', __( 'Badge text colour', 'travelstore-custom-widgets' ), '.ts-mcard__badge' );
		$this->add_color_control( 'badge_bg', __( 'Badge background colour', 'travelstore-custom-widgets' ), '.ts-mcard__badge', 'background-color', 'ts_espresso' );
		$this->add_typography_control( 'empty_typography', __( '"No trips" text typography', 'travelstore-custom-widgets' ), '.ts-trips__empty' );
		$this->add_color_control( 'empty_color', __( '"No trips" text colour', 'travelstore-custom-widgets' ), '.ts-trips__empty' );
		$this->end_controls_section();

		$this->add_button_style_section();
		$this->register_extra_css_controls();
	}

	/**
	 * Trip ids to show, in order.
	 */
	private function query_trips( $s ) {
		$source = isset( $s['source'] ) ? $s['source'] : 'pick';
		$count  = max( 1, min( 24, isset( $s['count'] ) ? (int) $s['count'] : 3 ) );

		if ( 'pick' === $source ) {
			$ids = array_values( array_filter( array_map( 'absint', isset( $s['trips'] ) && is_array( $s['trips'] ) ? $s['trips'] : array() ) ) );
			if ( empty( $ids ) ) {
				return array();
			}
			$found = get_posts( array( 'post_type' => 'trip', 'post_status' => 'publish', 'post__in' => $ids, 'orderby' => 'post__in', 'numberposts' => count( $ids ), 'fields' => 'ids' ) );

			return array_map( 'intval', $found );
		}

		$args = array(
			'post_type'   => 'trip',
			'post_status' => 'publish',
			'numberposts' => $count,
			'fields'      => 'ids',
			'orderby'     => 'date',
			'order'       => 'DESC',
		);
		if ( 'terms' === $source ) {
			$orderby = isset( $s['orderby'] ) && in_array( $s['orderby'], array( 'date', 'title', 'menu_order' ), true ) ? $s['orderby'] : 'date';
			$args['orderby'] = $orderby;
			$args['order']   = 'date' === $orderby ? 'DESC' : 'ASC';
			$by_tax          = array();
			foreach ( isset( $s['terms'] ) && is_array( $s['terms'] ) ? $s['terms'] : array() as $value ) {
				$parts = explode( ':', (string) $value );
				if ( 2 === count( $parts ) && in_array( $parts[0], self::TAXONOMIES, true ) && absint( $parts[1] ) ) {
					$by_tax[ $parts[0] ][] = absint( $parts[1] );
				}
			}
			if ( empty( $by_tax ) ) {
				return array();
			}
			$tax_query = array( 'relation' => 'OR' );
			foreach ( $by_tax as $tax => $term_ids ) {
				$tax_query[] = array( 'taxonomy' => $tax, 'field' => 'term_id', 'terms' => $term_ids );
			}
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery
		}

		return array_map( 'intval', get_posts( $args ) );
	}

	private function term_names( $trip_id, $tax ) {
		$terms = get_the_terms( $trip_id, $tax );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return '';
		}

		return implode( ', ', wp_list_pluck( $terms, 'name' ) );
	}

	private function kicker( $trip_id, $source ) {
		if ( 'activities_difficulty' === $source ) {
			return implode( ' · ', array_filter( array( $this->term_names( $trip_id, 'activities' ), $this->term_names( $trip_id, 'difficulty' ) ) ) );
		}

		return in_array( $source, self::TAXONOMIES, true ) ? $this->term_names( $trip_id, $source ) : '';
	}

	private function duration( $trip_id ) {
		if ( function_exists( 'wptravelengine_get_trip_duration_arr' ) ) {
			$parts = wptravelengine_get_trip_duration_arr( $trip_id, 'days' );
			if ( ! empty( $parts ) ) {
				return implode( ' ', array_map( 'wp_strip_all_tags', $parts ) );
			}
		}

		return '';
	}

	private function price( $trip_id ) {
		$amount = (float) wp_travel_engine_get_actual_trip_price( $trip_id );
		if ( $amount <= 0 ) {
			return '';
		}

		return function_exists( 'wte_get_formated_price' ) ? wp_strip_all_tags( html_entity_decode( (string) wte_get_formated_price( $amount ), ENT_QUOTES, 'UTF-8' ) ) : (string) $amount;
	}

	/**
	 * The trip's own Trip Facts (label => text), in the order set in WP Travel Engine.
	 */
	private function facts( $trip_id, $max ) {
		if ( ! function_exists( 'wptravelengine_get_trip_facts_options' ) ) {
			return array();
		}
		$settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
		$facts    = isset( $settings['trip_facts'] ) && is_array( $settings['trip_facts'] ) ? $settings['trip_facts'] : array();
		$global   = wptravelengine_get_trip_facts_options();
		$out      = array();
		foreach ( isset( $facts['field_type'] ) && is_array( $facts['field_type'] ) ? $facts['field_type'] : array() as $key => $type ) {
			if ( empty( $global['field_id'][ $key ] ) || empty( $facts[ $key ][ $key ] ) || is_array( $facts[ $key ][ $key ] ) ) {
				continue;
			}
			if ( isset( $global['enabled'][ $key ] ) && function_exists( 'wptravelengine_toggled' ) && ! wptravelengine_toggled( $global['enabled'][ $key ] ) ) {
				continue;
			}
			$out[] = array( wp_strip_all_tags( (string) $global['field_id'][ $key ] ), wp_strip_all_tags( (string) $facts[ $key ][ $key ] ) );
			if ( count( $out ) >= $max ) {
				break;
			}
		}

		return $out;
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		if ( ! self::wte_active() ) {
			$this->render_empty( __( 'WP Travel Engine is not active, so there are no trips to show.', 'travelstore-custom-widgets' ) );
			return;
		}

		$ids = $this->query_trips( $s );
		if ( empty( $ids ) ) {
			$empty = isset( $s['empty_text'] ) ? trim( (string) $s['empty_text'] ) : '';
			if ( '' !== $empty ) {
				echo '<div class="ts-w ts-trips"><p class="ts-trips__empty">' . esc_html( $empty ) . '</p></div>';
			} else {
				$this->render_empty( __( 'No published trips match yet. Add trips in WP Travel Engine, then choose them here.', 'travelstore-custom-widgets' ) );
			}
			return;
		}

		$tag        = $this->safe_tag( isset( $s['title_tag'] ) ? $s['title_tag'] : 'h3', 'h3' );
		$badge_pos  = isset( $s['badge_position'] ) && in_array( $s['badge_position'], array( 'top-left', 'top-right', 'bottom-left', 'bottom-right' ), true ) ? $s['badge_position'] : 'top-right';
		$styles     = $this->button_style_options();
		$btn_style  = isset( $s['button_style'], $styles[ $s['button_style'] ] ) ? $s['button_style'] : 'primary';
		$btn_text   = isset( $s['button_text'] ) ? trim( (string) $s['button_text'] ) : '';
		$show_price = ! empty( $s['show_price'] );
		$prefix     = isset( $s['price_prefix'] ) ? trim( (string) $s['price_prefix'] ) : '';
		$no_price   = isset( $s['no_price_text'] ) ? trim( (string) $s['no_price_text'] ) : '';
		$words      = max( 5, min( 80, isset( $s['excerpt_words'] ) ? (int) $s['excerpt_words'] : 24 ) );
		$facts_max  = max( 1, min( 8, isset( $s['facts_max'] ) ? (int) $s['facts_max'] : 4 ) );
		?>
		<div class="ts-w ts-mcards ts-mcards--stacked ts-mcards--ov-off ts-trips">
			<ul class="ts-mcards__grid">
				<?php foreach ( $ids as $trip_id ) : ?>
					<?php
					$url      = get_permalink( $trip_id );
					$title    = get_the_title( $trip_id );
					$badge    = ! empty( $s['show_badge'] ) ? $this->duration( $trip_id ) : '';
					$kicker   = $this->kicker( $trip_id, isset( $s['kicker_source'] ) ? (string) $s['kicker_source'] : '' );
					$excerpt  = '';
					if ( ! empty( $s['show_excerpt'] ) ) {
						$post    = get_post( $trip_id );
						$raw     = has_excerpt( $trip_id ) ? $post->post_excerpt : strip_shortcodes( $post->post_content );
						$excerpt = wp_trim_words( wp_strip_all_tags( $raw ), $words, '…' );
					}
					$facts    = ! empty( $s['show_facts'] ) ? $this->facts( $trip_id, $facts_max ) : array();
					$price    = $show_price ? $this->price( $trip_id ) : '';
					$price_ln = '' !== $price ? $price : ( $show_price ? $no_price : '' );
					?>
					<li class="ts-mcards__item">
						<article class="ts-mcard ts-trip">
							<div class="ts-mcard__media">
								<?php
								if ( has_post_thumbnail( $trip_id ) ) {
									$photo = array(
										'image'                  => array( 'id' => (int) get_post_thumbnail_id( $trip_id ) ),
										'image_size'             => isset( $s['image_size'] ) ? $s['image_size'] : 'large',
										'image_custom_dimension' => isset( $s['image_custom_dimension'] ) ? $s['image_custom_dimension'] : array(),
									);
									echo $this->image_tag( $photo, 'image', 'large', '', array( 'class' => 'ts-mcard__img', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
								}
								?>
								<?php if ( '' !== $badge ) : ?>
									<span class="ts-mcard__badge ts-mcard__badge--<?php echo esc_attr( $badge_pos ); ?>"><?php echo esc_html( $badge ); ?></span>
								<?php endif; ?>
							</div>
							<div class="ts-mcard__body">
								<?php if ( '' !== $kicker ) : ?>
									<span class="ts-mcard__kicker"><?php echo esc_html( $kicker ); ?></span>
								<?php endif; ?>
								<<?php echo esc_attr( $tag ); ?> class="ts-mcard__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></<?php echo esc_attr( $tag ); ?>>
								<?php if ( '' !== $excerpt ) : ?>
									<p class="ts-mcard__text"><?php echo esc_html( $excerpt ); ?></p>
								<?php endif; ?>
								<?php if ( $facts ) : ?>
									<dl class="ts-mcard__facts">
										<?php foreach ( $facts as $fact ) : ?>
											<div class="ts-mcard__fact"><dt><?php echo esc_html( $fact[0] ); ?></dt><dd><?php echo esc_html( $fact[1] ); ?></dd></div>
										<?php endforeach; ?>
									</dl>
								<?php endif; ?>
								<?php if ( '' !== $price_ln || '' !== $btn_text ) : ?>
									<div class="ts-trip__foot">
										<?php if ( '' !== $price_ln ) : ?>
											<div class="ts-trip__price"><?php echo '' !== $price && '' !== $prefix ? esc_html( $prefix ) : ''; ?><b><?php echo esc_html( $price_ln ); ?></b></div>
										<?php endif; ?>
										<?php if ( '' !== $btn_text ) : ?>
											<a class="ts-btn ts-btn--<?php echo esc_attr( $btn_style ); ?> ts-trip__btn" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $btn_text . ': ' . $title ); ?>"><?php echo esc_html( $btn_text ); ?></a>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</article>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( ! empty( $s['more_text'] ) ) : ?>
				<div class="ts-trips__more"><?php $this->render_button( $s, 'more' ); ?></div>
			<?php endif; ?>
		</div>
		<?php
	}
}
