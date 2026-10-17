<?php
/**
 * Widget: TS Destination Atlas — "pick your month" map with a list of places (1.10.0).
 *
 * A month picker, an illustrated map with one numbered pin per place, and a list of the same
 * places. Choosing a month recolours the pins and rows (great / good with a catch / tough) and
 * shows that place's note for the month. Everything is an editor field: pin positions are
 * sliders (% across and down the map), months are dropdowns, notes are plain text with a
 * from/to month. The map drawing itself is bundled art. Without JavaScript the page shows the
 * starting month. Built for the Destinations page; suits any "best time by place" map.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use TravelStore\TS_Month_Levels;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Atlas extends TS_Base_Widget {

	/** Notes per place: text + from/to month; the first one that covers the month wins. */
	const NOTE_SLOTS = 4;

	public function get_name() {
		return 'ts-atlas';
	}

	public function get_title() {
		return __( 'TS Destination Atlas', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-map-pin';
	}

	public function get_keywords() {
		return array( 'atlas', 'map', 'month', 'best time', 'destinations', 'pins', 'season', 'travel' );
	}

	public function get_script_depends() {
		return array( 'ts-atlas' );
	}

	/**
	 * "m1" => "January" … in the site language, with a first "current" choice for the start month.
	 */
	private function month_dropdown() {
		$names = TS_Month_Levels::month_names();
		$out   = array();
		foreach ( $names[2] as $i => $name ) {
			$out[ 'm' . ( $i + 1 ) ] = $name;
		}

		return $out;
	}

	protected function register_controls() {
		/* ---------------- Content: month picker ---------------- */
		$this->start_controls_section(
			'ts_picker',
			array( 'label' => __( 'Month picker', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'picker_title',
			array(
				'label'       => __( 'Title', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'When are you travelling?', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'picker_hint',
			array(
				'label'       => __( 'Small line under the title', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Tap a month and the map updates', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'start_month',
			array(
				'label'       => __( 'Month shown first', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'current',
				'options'     => array( 'current' => __( 'The visitor\'s current month', 'travelstore-custom-widgets' ) ) + $this->month_dropdown(),
			)
		);
		$this->add_control(
			'name_great',
			array(
				'label'     => __( 'Word for "great"', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Great', 'travelstore-custom-widgets' ),
				'separator' => 'before',
			)
		);
		$this->add_control(
			'name_good',
			array(
				'label'   => __( 'Word for "good"', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Good', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'name_tough',
			array(
				'label'   => __( 'Word for "tough"', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Tough', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'in_word',
			array(
				'label'       => __( 'Word between level and month', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'in', 'travelstore-custom-widgets' ),
				'description' => __( 'Each place shows, for example: Great in Jan.', 'travelstore-custom-widgets' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Content: map ---------------- */
		$this->start_controls_section(
			'ts_map',
			array( 'label' => __( 'Map', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'map_art',
			array(
				'label'   => __( 'Map drawing', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'oman-atlas-map',
				'options' => \TravelStore\TS_Art::options( false ),
			)
		);
		$this->add_control(
			'map_alt',
			array(
				'label'       => __( 'Map description (screen readers)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Map of Oman with seven destinations', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'legend_show',
			array(
				'label'        => __( 'Show the key under the map', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);
		$legend = array(
			'legend_great' => array( __( 'Key: great', 'travelstore-custom-widgets' ), __( 'Great this month', 'travelstore-custom-widgets' ) ),
			'legend_good'  => array( __( 'Key: good', 'travelstore-custom-widgets' ), __( 'Good, with a catch', 'travelstore-custom-widgets' ) ),
			'legend_tough' => array( __( 'Key: tough', 'travelstore-custom-widgets' ), __( 'Tough this month', 'travelstore-custom-widgets' ) ),
			'legend_loop'  => array( __( 'Key: road route drawn on the map', 'travelstore-custom-widgets' ), __( 'Classic loop by road', 'travelstore-custom-widgets' ) ),
			'legend_air'   => array( __( 'Key: flights drawn on the map', 'travelstore-custom-widgets' ), __( 'Usually by air', 'travelstore-custom-widgets' ) ),
		);
		foreach ( $legend as $id => $def ) {
			$this->add_control(
				$id,
				array(
					'label'       => $def[0],
					'type'        => Controls_Manager::TEXT,
					'default'     => $def[1],
					'description' => in_array( $id, array( 'legend_loop', 'legend_air' ), true ) ? __( 'Leave empty to hide this line of the key.', 'travelstore-custom-widgets' ) : '',
					'condition'   => array( 'legend_show' => 'yes' ),
				)
			);
		}
		$this->end_controls_section();

		/* ---------------- Content: places ---------------- */
		$this->start_controls_section(
			'ts_places',
			array( 'label' => __( 'Places', 'travelstore-custom-widgets' ) )
		);

		$r = new Repeater();
		$r->add_control(
			'name',
			array(
				'label'       => __( 'Name', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'sub',
			array(
				'label'       => __( 'Short description', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'For example: Forts, souqs & date gardens', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'time',
			array(
				'label'       => __( 'Time from the start point (bold)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'For example: ~1.5–2 hrs', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'link',
			array(
				'label'       => __( 'Link (pin and list row)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'Type a page name or paste a link', 'travelstore-custom-widgets' ),
				'description' => __( 'For a place further down the same page, type # and its anchor name, for example #d-nizwa.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$r->add_control(
			'pin_heading',
			array(
				'label'       => __( 'Where it sits on the map', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::HEADING,
				'separator'   => 'before',
			)
		);
		$pins = array(
			'pin_x'   => __( 'The place itself: across', 'travelstore-custom-widgets' ),
			'pin_y'   => __( 'The place itself: down', 'travelstore-custom-widgets' ),
			'badge_x' => __( 'Numbered badge: across', 'travelstore-custom-widgets' ),
			'badge_y' => __( 'Numbered badge: down', 'travelstore-custom-widgets' ),
			'label_x' => __( 'Name label: across', 'travelstore-custom-widgets' ),
			'label_y' => __( 'Name label: down', 'travelstore-custom-widgets' ),
		);
		foreach ( $pins as $id => $label ) {
			$r->add_control(
				$id,
				array(
					'label'      => $label,
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'range'      => array( '%' => array( 'min' => 0, 'max' => 100, 'step' => 0.1 ) ),
					'default'    => array( 'size' => 50, 'unit' => '%' ),
				)
			);
		}

		TS_Month_Levels::add_controls( $r, array( 'heading' => __( 'How each month is', 'travelstore-custom-widgets' ) ) );

		$r->add_control(
			'notes_heading',
			array(
				'label'       => __( 'Notes shown under the place', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::HEADING,
				'separator'   => 'before',
			)
		);
		$r->add_control(
			'notes_hint',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Each note applies from one month to another (for example November to February). For a month, the first note that covers it is shown; "Any other month" is the fallback.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$months = array( '' => __( '—', 'travelstore-custom-widgets' ) ) + $this->month_dropdown();
		for ( $n = 1; $n <= self::NOTE_SLOTS; $n++ ) {
			$r->add_control(
				'note' . $n . '_text',
				array(
					/* translators: %d: note number. */
					'label'       => sprintf( __( 'Note %d', 'travelstore-custom-widgets' ), $n ),
					'type'        => Controls_Manager::TEXTAREA,
					'rows'        => 2,
				)
			);
			$r->add_control(
				'note' . $n . '_from',
				array(
					'label'     => __( 'From month', 'travelstore-custom-widgets' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '',
					'options'   => $months,
					'condition' => array( 'note' . $n . '_text!' => '' ),
				)
			);
			$r->add_control(
				'note' . $n . '_to',
				array(
					'label'       => __( 'To month', 'travelstore-custom-widgets' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => '',
					'options'     => array( '' => __( 'Same month as "from"', 'travelstore-custom-widgets' ) ) + $this->month_dropdown(),
					'description' => __( '"November" to "February" runs across the new year.', 'travelstore-custom-widgets' ),
					'condition'   => array( 'note' . $n . '_text!' => '', 'note' . $n . '_from!' => '' ),
				)
			);
		}
		$r->add_control(
			'note_other',
			array(
				'label'     => __( 'Any other month', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'places',
			array(
				'label'       => __( 'Places', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array(
						'name'    => __( 'First place', 'travelstore-custom-widgets' ),
						'sub'     => __( 'What it is known for', 'travelstore-custom-widgets' ),
						'time'    => __( '~2 hrs', 'travelstore-custom-widgets' ),
						'pin_x'   => array( 'size' => 40, 'unit' => '%' ),
						'pin_y'   => array( 'size' => 40, 'unit' => '%' ),
						'badge_x' => array( 'size' => 46, 'unit' => '%' ),
						'badge_y' => array( 'size' => 34, 'unit' => '%' ),
						'label_x' => array( 'size' => 42, 'unit' => '%' ),
						'label_y' => array( 'size' => 28, 'unit' => '%' ),
					),
				),
			)
		);
		$this->add_tag_control( 'name_tag', __( 'Place name level', 'travelstore-custom-widgets' ), 'h3' );
		$this->add_control(
			'foot',
			array(
				'label'       => __( 'Small note under the list', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'separator'   => 'before',
				'description' => __( 'Optional.', 'travelstore-custom-widgets' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: layout ---------------- */
		$this->start_controls_section(
			'ts_section_layout',
			array(
				'label' => __( 'Layout', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'map_share',
			array(
				'label'                => __( 'Map : list widths', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => '102-100',
				'options'              => array(
					'102-100' => __( 'Map a little wider (as designed)', 'travelstore-custom-widgets' ),
					'1-1'     => __( 'Half and half', 'travelstore-custom-widgets' ),
					'6-4'     => __( 'Map much wider (60 : 40)', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'102-100' => '--ts-at-map:1.02fr;--ts-at-list:1fr;',
					'1-1'     => '--ts-at-map:1fr;--ts-at-list:1fr;',
					'6-4'     => '--ts-at-map:1.5fr;--ts-at-list:1fr;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-at' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'arrangement',
			array(
				'label'                => __( 'Arrangement', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'side',
				'tablet_default'       => 'stack',
				'mobile_default'       => 'stack',
				'options'              => array(
					'side'  => __( 'Map and list side by side', 'travelstore-custom-widgets' ),
					'stack' => __( 'Map above the list', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'side'  => 'grid-template-columns:minmax(0,var(--ts-at-map,1.02fr)) minmax(0,var(--ts-at-list,1fr));',
					'stack' => 'grid-template-columns:minmax(0,1fr);',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-at__grid' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => __( 'Space between map and list', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-at__grid' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'bar_space',
			array(
				'label'      => __( 'Space under the month picker', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 22, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-at__bar' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'month_cols',
			array(
				'label'          => __( 'Month buttons per row', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '12',
				'tablet_default' => '6',
				'mobile_default' => '6',
				'options'        => array(
					'12' => '12',
					'6'  => '6',
					'4'  => '4',
					'3'  => '3',
				),
				'selectors'      => array( '{{WRAPPER}} .ts-at__months' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ),
			)
		);
		$this->add_responsive_control(
			'bar_arrangement',
			array(
				'label'                => __( 'Picker title', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'beside',
				'tablet_default'       => 'above',
				'mobile_default'       => 'above',
				'options'              => array(
					'beside' => __( 'Beside the month buttons', 'travelstore-custom-widgets' ),
					'above'  => __( 'Above the month buttons', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'beside' => 'grid-template-columns:auto minmax(0,1fr);gap:20px;',
					'above'  => 'grid-template-columns:minmax(0,1fr);gap:12px;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-at__bar' => '{{VALUE}}' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: month picker ---------------- */
		$this->start_controls_section(
			'ts_section_picker_style',
			array(
				'label' => __( 'Month picker', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_color_control( 'bar_bg', __( 'Box colour', 'travelstore-custom-widgets' ), '.ts-at__bar', 'background-color', 'ts_cream' );
		$this->add_color_control( 'bar_border', __( 'Box border colour', 'travelstore-custom-widgets' ), '.ts-at__bar', 'border-color' );
		$this->add_responsive_control(
			'bar_radius',
			array(
				'label'      => __( 'Box corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-at__bar' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'bar_padding',
			array(
				'label'          => __( 'Box padding', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 20, 'right' => 24, 'bottom' => 20, 'left' => 24, 'unit' => 'px', 'isLinked' => false ),
				'mobile_default' => array( 'top' => 18, 'right' => 18, 'bottom' => 18, 'left' => 18, 'unit' => 'px', 'isLinked' => true ),
				'selectors'      => array( '{{WRAPPER}} .ts-at__bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_typography_control( 'picker_title_typography', __( 'Title typography', 'travelstore-custom-widgets' ), '.ts-at__label' );
		$this->add_color_control( 'picker_title_color', __( 'Title colour', 'travelstore-custom-widgets' ), '.ts-at__label' );
		$this->add_typography_control( 'picker_hint_typography', __( 'Small line typography', 'travelstore-custom-widgets' ), '.ts-at__label small' );
		$this->add_color_control( 'picker_hint_color', __( 'Small line colour', 'travelstore-custom-widgets' ), '.ts-at__label small' );
		$this->add_typography_control( 'month_typography', __( 'Month button typography', 'travelstore-custom-widgets' ), '.ts-at__month' );
		$this->add_color_control( 'month_bg', __( 'Month button colour', 'travelstore-custom-widgets' ), '.ts-at__month', 'background-color' );
		$this->add_color_control( 'month_color', __( 'Month button text', 'travelstore-custom-widgets' ), '.ts-at__month' );
		$this->add_color_control( 'month_border', __( 'Month button border', 'travelstore-custom-widgets' ), '.ts-at__month', 'border-color' );
		$this->add_color_control( 'month_hover', __( 'Month button border on hover', 'travelstore-custom-widgets' ), '.ts-at', '--ts-at-hover', 'ts_terracotta' );
		$this->add_color_control( 'month_on_bg', __( 'Chosen month colour', 'travelstore-custom-widgets' ), '.ts-at', '--ts-at-on-bg', 'ts_espresso' );
		$this->add_color_control( 'month_on_color', __( 'Chosen month text', 'travelstore-custom-widgets' ), '.ts-at', '--ts-at-on-text' );
		$this->add_responsive_control(
			'month_radius',
			array(
				'label'      => __( 'Month button corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-at__month' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: map ---------------- */
		$this->start_controls_section(
			'ts_section_map_style',
			array(
				'label' => __( 'Map', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_color_control( 'map_bg', __( 'Map background', 'travelstore-custom-widgets' ), '.ts-at__map', 'background-color' );
		$this->add_color_control( 'map_border', __( 'Map border colour', 'travelstore-custom-widgets' ), '.ts-at__map', 'border-color' );
		$this->add_responsive_control(
			'map_radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-at__map' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'pin_great', __( 'Pin colour: great', 'travelstore-custom-widgets' ), '.ts-at', '--ts-at-great', 'ts_oasis_deep' );
		$this->add_color_control( 'pin_good', __( 'Pin colour: good', 'travelstore-custom-widgets' ), '.ts-at', '--ts-at-good', 'ts_gold' );
		$this->add_color_control( 'pin_tough', __( 'Pin colour: tough', 'travelstore-custom-widgets' ), '.ts-at', '--ts-at-tough' );
		$this->add_color_control( 'pin_ring', __( 'Pin highlight ring (on hover)', 'travelstore-custom-widgets' ), '.ts-at', '--ts-at-ring', 'ts_terracotta' );
		$this->add_color_control( 'pin_label', __( 'Place name on the map', 'travelstore-custom-widgets' ), '.ts-at__pin .lbl', 'fill' );
		$this->add_color_control( 'legend_bg', __( 'Key background', 'travelstore-custom-widgets' ), '.ts-at__legend', 'background-color', 'ts_cream' );
		$this->add_typography_control( 'legend_typography', __( 'Key typography', 'travelstore-custom-widgets' ), '.ts-at__legend' );
		$this->add_color_control( 'legend_color', __( 'Key colour', 'travelstore-custom-widgets' ), '.ts-at__legend' );
		$this->end_controls_section();

		/* ---------------- Style: list ---------------- */
		$this->start_controls_section(
			'ts_section_list_style',
			array(
				'label' => __( 'List', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => __( 'Space between rows', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-at__list' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'row_bg', __( 'Row colour', 'travelstore-custom-widgets' ), '.ts-at__row', 'background-color', 'ts_cream' );
		$this->add_color_control( 'row_border', __( 'Row border colour', 'travelstore-custom-widgets' ), '.ts-at__row', 'border-color' );
		$this->add_color_control( 'row_hover', __( 'Row border on hover', 'travelstore-custom-widgets' ), '.ts-at', '--ts-at-row-hover', 'ts_terracotta' );
		$this->add_responsive_control(
			'row_radius',
			array(
				'label'      => __( 'Row corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-at__row' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'row_padding',
			array(
				'label'      => __( 'Row padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array( 'top' => 14, 'right' => 16, 'bottom' => 14, 'left' => 16, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array( '{{WRAPPER}} .ts-at__row' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_typography_control( 'name_typography', __( 'Place name typography', 'travelstore-custom-widgets' ), '.ts-at__name' );
		$this->add_color_control( 'name_color', __( 'Place name colour', 'travelstore-custom-widgets' ), '.ts-at__name' );
		$this->add_typography_control( 'sub_typography', __( 'Short description typography', 'travelstore-custom-widgets' ), '.ts-at__sub' );
		$this->add_color_control( 'sub_color', __( 'Short description colour', 'travelstore-custom-widgets' ), '.ts-at__sub' );
		$this->add_typography_control( 'state_typography', __( 'Level chip typography', 'travelstore-custom-widgets' ), '.ts-at__state' );
		$this->add_typography_control( 'note_typography', __( 'Note typography', 'travelstore-custom-widgets' ), '.ts-at__note' );
		$this->add_color_control( 'note_color', __( 'Note colour', 'travelstore-custom-widgets' ), '.ts-at__note' );
		$this->add_typography_control( 'foot_typography', __( 'Small note typography', 'travelstore-custom-widgets' ), '.ts-at__foot' );
		$this->add_color_control( 'foot_color', __( 'Small note colour', 'travelstore-custom-widgets' ), '.ts-at__foot' );
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	/**
	 * The note of a place for every month (index 0 = January).
	 *
	 * @return string[]
	 */
	private function notes_for( $place ) {
		$out = array();
		for ( $m = 1; $m <= 12; $m++ ) {
			$note = isset( $place['note_other'] ) ? trim( (string) $place['note_other'] ) : '';
			for ( $n = 1; $n <= self::NOTE_SLOTS; $n++ ) {
				$text = isset( $place[ 'note' . $n . '_text' ] ) ? trim( (string) $place[ 'note' . $n . '_text' ] ) : '';
				$from = TS_Month_Levels::month_value( isset( $place[ 'note' . $n . '_from' ] ) ? $place[ 'note' . $n . '_from' ] : '' );
				if ( '' === $text || ! $from ) {
					continue;
				}
				$to = TS_Month_Levels::month_value( isset( $place[ 'note' . $n . '_to' ] ) ? $place[ 'note' . $n . '_to' ] : '' );
				if ( ! $to ) {
					$to = $from;
				}
				$hit = $to >= $from ? ( $m >= $from && $m <= $to ) : ( $m >= $from || $m <= $to );
				if ( $hit ) {
					$note = $text;
					break;
				}
			}
			$out[] = $note;
		}

		return $out;
	}

	/**
	 * Numeric part of a slider value.
	 */
	private function slider( $place, $key, $default = 50.0 ) {
		return isset( $place[ $key ]['size'] ) && '' !== $place[ $key ]['size'] ? (float) $place[ $key ]['size'] : $default;
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$places = isset( $s['places'] ) && is_array( $s['places'] ) ? $s['places'] : array();

		if ( empty( $places ) ) {
			$this->render_empty( __( 'Add at least one place to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		list( $long, , $full ) = TS_Month_Levels::month_names();
		$names                 = array(
			0 => isset( $s['name_tough'] ) && '' !== trim( (string) $s['name_tough'] ) ? trim( $s['name_tough'] ) : __( 'Tough', 'travelstore-custom-widgets' ),
			1 => isset( $s['name_good'] ) && '' !== trim( (string) $s['name_good'] ) ? trim( $s['name_good'] ) : __( 'Good', 'travelstore-custom-widgets' ),
			2 => isset( $s['name_great'] ) && '' !== trim( (string) $s['name_great'] ) ? trim( $s['name_great'] ) : __( 'Great', 'travelstore-custom-widgets' ),
		);
		$in_word               = isset( $s['in_word'] ) ? trim( (string) $s['in_word'] ) : 'in';
		$start                 = isset( $s['start_month'] ) ? (string) $s['start_month'] : 'current';
		$start_n               = 'current' === $start ? (int) wp_date( 'n' ) : TS_Month_Levels::month_value( $start );
		$month                 = max( 0, min( 11, $start_n - 1 ) );
		$tag                   = $this->safe_tag( isset( $s['name_tag'] ) ? $s['name_tag'] : 'h3', 'h3' );
		$art                   = isset( $s['map_art'] ) ? (string) $s['map_art'] : 'oman-atlas-map';

		$config = array(
			'start'  => 'current' === $start ? 'current' : $start_n - 1,
			'months' => $long,
			'names'  => array_values( $names ),
			'inWord' => $in_word,
		);

		// Map: bundled drawing with the pins added on top.
		$size = \TravelStore\TS_Art::size( $art );
		$vw   = $size ? (float) $size[0] : 520.0;
		$vh   = $size ? (float) $size[1] : 700.0;
		$svg  = \TravelStore\TS_Art::inline_svg( $art, $this->get_id(), '' );
		$pins = '';
		$rows = '';
		foreach ( $places as $i => $place ) {
			$rid    = ! empty( $place['_id'] ) ? sanitize_html_class( $place['_id'] ) : 'i' . $i;
			$key    = 'p' . $i;
			$name   = isset( $place['name'] ) ? (string) $place['name'] : '';
			$levels = TS_Month_Levels::read( $place );
			$ranks  = array();
			foreach ( $levels as $lv ) {
				$ranks[] = TS_Month_Levels::RANK[ $lv ];
			}
			$notes = $this->notes_for( $place );
			$rank  = $ranks[ $month ];
			$num   = $i + 1;

			$px = round( $this->slider( $place, 'pin_x' ) / 100 * $vw, 1 );
			$py = round( $this->slider( $place, 'pin_y' ) / 100 * $vh, 1 );
			$bx = round( $this->slider( $place, 'badge_x' ) / 100 * $vw, 1 );
			$by = round( $this->slider( $place, 'badge_y' ) / 100 * $vh, 1 );
			$lx = round( $this->slider( $place, 'label_x' ) / 100 * $vw, 1 );
			$ly = round( $this->slider( $place, 'label_y' ) / 100 * $vh, 1 );

			$href  = ! empty( $place['link']['url'] ) ? (string) $place['link']['url'] : '';
			$a_att = '' !== $href ? ' href="' . esc_url( $href ) . '"' : '';
			// SVG anchors use xlink:href: a plain href makes theme scripts that read a.href as text fail (Astra).
			$pin_att = '' !== $href ? ' xlink:href="' . esc_url( $href ) . '"' : '';
			$pins .= sprintf(
				'<a%1$s class="ts-at__pin s%2$d" data-id="%3$s" aria-label="%4$s"><circle cx="%5$s" cy="%6$s" r="4" fill="#382520"/><line x1="%5$s" y1="%6$s" x2="%7$s" y2="%8$s" stroke="#382520" stroke-width="1.5"/><circle class="ring" cx="%7$s" cy="%8$s" r="20"/><circle class="badge" cx="%7$s" cy="%8$s" r="14"/><text class="num" x="%7$s" y="%9$s">%10$d</text><text class="lbl" x="%11$s" y="%12$s">%13$s</text></a>',
				$pin_att,
				$rank,
				esc_attr( $key ),
				esc_attr( $num . '. ' . $name ),
				esc_attr( $px ),
				esc_attr( $py ),
				esc_attr( $bx ),
				esc_attr( $by ),
				esc_attr( $by + 5 ),
				(int) $num,
				esc_attr( $lx ),
				esc_attr( $ly ),
				esc_html( $name )
			);

			// List row.
			$sub  = isset( $place['sub'] ) ? trim( (string) $place['sub'] ) : '';
			$time = isset( $place['time'] ) ? trim( (string) $place['time'] ) : '';
			$cell = '';
			if ( '' !== $sub ) {
				$cell .= esc_html( $sub );
			}
			if ( '' !== $time ) {
				$cell .= ( '' !== $sub ? ' · ' : '' ) . '<b>' . esc_html( $time ) . '</b>';
			}
			$row_tag = '' !== $href ? 'a' : 'div';
			$rows   .= sprintf(
				'<%1$s%2$s class="ts-at__row s%3$d elementor-repeater-item-%4$s" data-id="%5$s" data-levels="%6$s" data-notes="%7$s"><div class="ts-at__num">%8$d</div><div><%9$s class="ts-at__name">%10$s</%9$s><div class="ts-at__sub">%11$s</div></div><span class="ts-at__state s%3$d">%12$s</span><div class="ts-at__note">%13$s</div></%1$s>',
				$row_tag,
				$a_att,
				$rank,
				esc_attr( $rid ),
				esc_attr( $key ),
				esc_attr( implode( ',', $ranks ) ),
				esc_attr( wp_json_encode( $notes ) ),
				(int) $num,
				esc_attr( $tag ),
				esc_html( $name ),
				$cell, // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
				esc_html( $names[ $rank ] . ( '' !== $in_word ? ' ' . $in_word : '' ) . ' ' . $long[ $month ] ),
				esc_html( $notes[ $month ] )
			);
		}
		if ( '' !== $svg ) {
			$label = isset( $s['map_alt'] ) ? (string) $s['map_alt'] : '';
			$svg   = preg_replace( '/^<svg\b([^>]*?)\saria-hidden="true"\sfocusable="false"/', '<svg$1 role="group" aria-label="' . esc_attr( $label ) . '"', $svg, 1 );
			$svg   = preg_replace( '#</svg>\s*$#', '<g class="ts-at__pins">' . $pins . '</g></svg>', $svg, 1 ); // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts above.
		}

		$legend = array();
		if ( ! empty( $s['legend_show'] ) ) {
			foreach ( array( 'great' => 'ts-at__sw--great', 'good' => 'ts-at__sw--good', 'tough' => 'ts-at__sw--tough', 'loop' => 'ts-at__sw--dash', 'air' => 'ts-at__sw--fly' ) as $k => $cls ) {
				$text = isset( $s[ 'legend_' . $k ] ) ? trim( (string) $s[ 'legend_' . $k ] ) : '';
				if ( '' !== $text ) {
					$legend[] = '<span><i class="ts-at__sw ' . $cls . '" aria-hidden="true"></i>' . esc_html( $text ) . '</span>';
				}
			}
		}
		$foot  = isset( $s['foot'] ) ? trim( (string) $s['foot'] ) : '';
		$title = isset( $s['picker_title'] ) ? trim( (string) $s['picker_title'] ) : '';
		$hint  = isset( $s['picker_hint'] ) ? trim( (string) $s['picker_hint'] ) : '';
		?>
		<div class="ts-w ts-at" data-ts-atlas="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
			<div class="ts-at__bar">
				<?php if ( '' !== $title || '' !== $hint ) : ?>
					<div class="ts-at__label"><?php echo esc_html( $title ); ?><?php echo '' !== $hint ? '<small>' . esc_html( $hint ) . '</small>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php endif; ?>
				<div class="ts-at__months" role="group" aria-label="<?php echo esc_attr( '' !== $title ? $title : __( 'Choose a month', 'travelstore-custom-widgets' ) ); ?>">
					<?php for ( $m = 0; $m < 12; $m++ ) : ?>
						<button type="button" class="ts-at__month" data-m="<?php echo (int) $m; ?>" aria-pressed="<?php echo $m === $month ? 'true' : 'false'; ?>" title="<?php echo esc_attr( $full[ $m ] ); ?>"><?php echo esc_html( $long[ $m ] ); ?></button>
					<?php endfor; ?>
				</div>
			</div>
			<div class="ts-at__grid">
				<div class="ts-at__map">
					<?php echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput -- bundled drawing, cleaned in inline_svg(); pins escaped above. ?>
					<?php if ( $legend ) : ?>
						<div class="ts-at__legend"><?php echo implode( '', $legend ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<?php endif; ?>
				</div>
				<div>
					<div class="ts-at__list"><?php echo $rows; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></div>
					<?php if ( '' !== $foot ) : ?>
						<p class="ts-at__foot"><?php echo esc_html( $foot ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
