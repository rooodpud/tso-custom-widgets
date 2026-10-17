<?php
/**
 * Widget: TS Steps — a sequence of steps with a number or time marker.
 *
 * Covers "how it works" rows, a day told hour by hour (timeline), a horizontal
 * strip of stops, and numbered checklists. Vertical list or horizontal strip;
 * markers as a soft, outline or filled circle, or a small timeline dot.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Steps extends TS_Base_Widget {

	public function get_name() {
		return 'ts-steps';
	}

	public function get_title() {
		return __( 'TS Steps', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_keywords() {
		return array( 'steps', 'timeline', 'process', 'how it works', 'itinerary', 'schedule', 'numbered', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Steps', 'travelstore-custom-widgets' ) )
		);

		$r = new Repeater();
		$r->add_control(
			'marker',
			array(
				'label'       => __( 'Marker', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Shown in the circle, for example 1, A or a short word. Leave empty to number the steps automatically.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'label',
			array(
				'label'       => __( 'Small label above the title', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'For example a time (06:00) or a day. Optional.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'dynamic' => array( 'active' => true ),
			)
		);
		$r->add_control(
			'details',
			array(
				'label'       => __( 'Extra line', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Optional line under the text. Put a bold start before a | , for example: See: | Al Alam Palace · the forts', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'step_color',
			array(
				'label'       => __( 'Marker colour for this step', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'separator'   => 'before',
				'description' => __( 'Optional. Colours the marker (its ring or fill). With "Line colours: from the steps" the joining line blends through these colours.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-steps__list {{CURRENT_ITEM}}' => '--ts-step-c: {{VALUE}};' ),
			)
		);
		$r->add_control(
			'label_color_item',
			array(
				'label'     => __( 'Label colour for this step', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-steps__list {{CURRENT_ITEM}} .ts-step__label' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Steps', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title' => __( 'First step', 'travelstore-custom-widgets' ),
						'text'  => __( 'A short sentence about what happens here.', 'travelstore-custom-widgets' ),
					),
					array(
						'title' => __( 'Second step', 'travelstore-custom-widgets' ),
						'text'  => __( 'A short sentence about what happens here.', 'travelstore-custom-widgets' ),
					),
					array(
						'title' => __( 'Third step', 'travelstore-custom-widgets' ),
						'text'  => __( 'A short sentence about what happens here.', 'travelstore-custom-widgets' ),
					),
				),
			)
		);
		$this->add_control(
			'numbering',
			array(
				'label'   => __( 'Markers', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => array(
					'auto'   => __( 'Number automatically (01, 02 …) when empty', 'travelstore-custom-widgets' ),
					'custom' => __( 'Only what I type (empty = no text)', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_tag_control( 'title_tag', __( 'Title level', 'travelstore-custom-widgets' ), 'h3' );
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
			'text_scheme',
			array(
				'label'   => __( 'Text colours', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dark',
				'options' => array(
					'dark'  => __( 'Dark text (for light backgrounds)', 'travelstore-custom-widgets' ),
					'light' => __( 'Light text (for dark backgrounds)', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'list',
				'options' => array(
					'list'  => __( 'Vertical list', 'travelstore-custom-widgets' ),
					'strip' => __( 'Horizontal strip (columns)', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ),
				'selectors'      => array( '{{WRAPPER}} .ts-steps--strip .ts-steps__list' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ),
				'condition'      => array( 'layout' => 'strip' ),
			)
		);
		$this->add_control(
			'connector',
			array(
				'label'   => __( 'Between steps', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dividers',
				'options' => array(
					'none'     => __( 'Nothing (just space)', 'travelstore-custom-widgets' ),
					'line'     => __( 'A line joining the markers', 'travelstore-custom-widgets' ),
					'dividers' => __( 'A thin rule between steps', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_responsive_control(
			'spacing',
			array(
				'label'      => __( 'Space between steps', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-steps' => '--ts-step-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		// 1.9.4: spacing inside a step (empty = 4px under the label, 6px under the title, 10px above the extra line).
		$this->add_responsive_control(
			'label_space',
			array(
				'label'      => __( 'Space under the label', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-step__label' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'title_space',
			array(
				'label'      => __( 'Space under the title', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-step__title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'details_space',
			array(
				'label'      => __( 'Space above the extra line', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-step__details' => 'margin-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		// 1.9.3: padding above the first step and below the last (drafts' .stop { padding: 26px 0 } on every stop).
		$this->add_responsive_control(
			'edge_space',
			array(
				'label'      => __( 'Space above the first step and below the last', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .ts-steps--list .ts-step:first-child' => 'padding-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ts-steps--list .ts-step:last-child'  => 'padding-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'line_color',
			array(
				'label'     => __( 'Line colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-steps' => '--ts-step-line: {{VALUE}};' ),
				'condition' => array( 'connector!' => 'none' ),
			)
		);
		$this->add_control(
			'line_color_b',
			array(
				'label'       => __( 'Line fades to (optional)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'Set a second colour to make the joining line a colour blend.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-steps' => '--ts-step-line-b: {{VALUE}};' ),
				'condition'   => array( 'connector' => 'line' ),
			)
		);
		$this->add_control(
			'line_colors',
			array(
				'label'       => __( 'Line colours', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'set',
				'options'     => array(
					'set'   => __( 'The colours set above', 'travelstore-custom-widgets' ),
					'steps' => __( 'From the steps (each step\'s marker colour)', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'For example a climb that changes colour from the bottom to the top.', 'travelstore-custom-widgets' ),
				'condition'   => array( 'connector' => 'line' ),
			)
		);
		$this->add_control(
			'line_join',
			array(
				'label'     => __( 'Line runs', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'gaps',
				'options'   => array(
					'gaps'       => __( 'Between the markers, with small gaps', 'travelstore-custom-widgets' ),
					'continuous' => __( 'Behind the markers, top to bottom', 'travelstore-custom-widgets' ),
				),
				'condition' => array(
					'connector' => 'line',
					'layout'    => 'list',
				),
			)
		);
		$this->add_control(
			'line_style',
			array(
				'label'     => __( 'Line style', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'solid'  => __( 'Solid', 'travelstore-custom-widgets' ),
					'dotted' => __( 'Dotted (one colour)', 'travelstore-custom-widgets' ),
				),
				'condition' => array(
					'connector' => 'line',
					'layout'    => 'list',
				),
			)
		);
		$this->add_responsive_control(
			'line_width',
			array(
				'label'      => __( 'Line thickness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 20 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-steps' => '--ts-step-line-w: {{SIZE}}{{UNIT}};' ),
				'condition'  => array(
					'connector' => 'line',
					'layout'    => 'list',
				),
			)
		);
		$this->add_control(
			'line_rules',
			array(
				'label'        => __( 'Also a thin rule between steps', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'connector' => 'line',
					'layout'    => 'list',
				),
			)
		);
		$this->add_control(
			'rule_color',
			array(
				'label'     => __( 'Rule colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-steps' => '--ts-step-rule: {{VALUE}};' ),
				'condition' => array(
					'connector'  => 'line',
					'layout'     => 'list',
					'line_rules' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'marker_column',
			array(
				'label'       => __( 'Marker column width', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 8, 'max' => 200 ) ),
				'description' => __( 'Optional. A column wider than the marker keeps the marker (and the line) centred in it, like a gauge beside the text.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-steps' => '--ts-step-col: {{SIZE}}{{UNIT}};' ),
				'condition'   => array( 'layout' => 'list' ),
			)
		);
		$this->add_responsive_control(
			'text_position',
			array(
				'label'                => __( 'Text position', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'below',
				'options'              => array(
					'below'  => __( 'Under the title', 'travelstore-custom-widgets' ),
					'beside' => __( 'Beside the title (two columns)', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'below'  => 'display:block;',
					'beside' => 'display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.2fr);align-items:start;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-step__body' => '{{VALUE}}' ),
				'condition'            => array( 'layout' => 'list' ),
			)
		);
		$this->add_responsive_control(
			'beside_gap',
			array(
				'label'      => __( 'Space between title and text columns', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-step__body' => 'column-gap: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'layout' => 'list' ),
			)
		);
		$this->add_responsive_control(
			'marker_gap',
			array(
				'label'      => __( 'Space after the marker', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-steps--list .ts-step' => 'gap: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'layout' => 'list' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: step boxes (1.7.0) ---------------- */
		$this->start_controls_section(
			'ts_section_boxes',
			array(
				'label' => __( 'Step boxes', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'step_box',
			array(
				'label'   => __( 'Box behind each step', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''    => __( 'No box', 'travelstore-custom-widgets' ),
					'box' => __( 'A box (cream, thin border)', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'Usually with "Between steps: Nothing".', 'travelstore-custom-widgets' ),
			)
		);
		$box = array( 'condition' => array( 'step_box' => 'box' ) );
		$this->add_color_control( 'step_box_bg', __( 'Box colour', 'travelstore-custom-widgets' ), '.ts-steps--boxed .ts-step', 'background-color', '', $box );
		$this->add_color_control( 'step_box_border', __( 'Border colour', 'travelstore-custom-widgets' ), '.ts-steps--boxed .ts-step', 'border-color', '', $box );
		$this->add_responsive_control(
			'step_box_radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-steps--boxed .ts-step' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			) + $box
		);
		$this->add_responsive_control(
			'step_box_padding',
			array(
				'label'      => __( 'Padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-steps--boxed .ts-step' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			) + $box
		);
		$this->end_controls_section();

		/* ---------------- Style: marker ---------------- */
		$this->start_controls_section(
			'ts_section_marker',
			array(
				'label' => __( 'Marker', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'marker_style',
			array(
				'label'   => __( 'Marker look', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'soft',
				'options' => array(
					'soft'    => __( 'Soft circle (tinted)', 'travelstore-custom-widgets' ),
					'outline' => __( 'Outlined circle', 'travelstore-custom-widgets' ),
					'filled'  => __( 'Filled circle', 'travelstore-custom-widgets' ),
					'dot'     => __( 'Small dot (timeline, no text)', 'travelstore-custom-widgets' ),
					'none'    => __( 'No marker', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'tone',
			array(
				'label'     => __( 'Marker colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'terracotta',
				'options'   => self::tone_options(),
				'condition' => array( 'marker_style!' => 'none' ),
			)
		);
		$this->add_responsive_control(
			'marker_size',
			array(
				'label'      => __( 'Marker size', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 90 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-steps' => '--ts-step-size: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'marker_style!' => 'none' ),
			)
		);
		$this->add_responsive_control(
			'marker_ring',
			array(
				'label'      => __( 'Ring thickness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 12 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-step__marker' => 'border-width: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'marker_style!' => 'none' ),
			)
		);
		$this->add_typography_control( 'marker_typography', __( 'Marker typography', 'travelstore-custom-widgets' ), '.ts-step__marker' );
		$this->add_color_control( 'marker_text', __( 'Marker text colour', 'travelstore-custom-widgets' ), '.ts-step__marker' );
		$this->add_color_control( 'marker_bg', __( 'Marker fill colour', 'travelstore-custom-widgets' ), '.ts-step__marker', 'background-color' );
		$this->add_color_control( 'marker_border', __( 'Marker ring colour', 'travelstore-custom-widgets' ), '.ts-step__marker', 'border-color' );
		$this->end_controls_section();

		/* ---------------- Style: text ---------------- */
		$this->start_controls_section(
			'ts_section_text_style',
			array(
				'label' => __( 'Text', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'label_typography', __( 'Label typography', 'travelstore-custom-widgets' ), '.ts-step__label' );
		$this->add_color_control( 'label_color', __( 'Label colour', 'travelstore-custom-widgets' ), '.ts-step__label' );
		$this->add_typography_control( 'title_typography', __( 'Title typography', 'travelstore-custom-widgets' ), '.ts-step__title', 'ts_heading' );
		$this->add_color_control( 'title_color', __( 'Title colour', 'travelstore-custom-widgets' ), '.ts-step__title' );
		$this->add_typography_control( 'text_typography', __( 'Text typography', 'travelstore-custom-widgets' ), '.ts-step__text', 'ts_body' );
		$this->add_color_control( 'text_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-step__text' );
		$this->add_responsive_control(
			'text_max_width',
			array(
				'label'      => __( 'Text width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 1000 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'selectors'  => array( '{{WRAPPER}} .ts-step__text' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_typography_control( 'details_typography', __( 'Extra line typography', 'travelstore-custom-widgets' ), '.ts-step__details' );
		$this->add_color_control( 'details_color', __( 'Extra line colour', 'travelstore-custom-widgets' ), '.ts-step__details' );
		$this->add_color_control( 'details_lead_color', __( 'Extra line bold start colour', 'travelstore-custom-widgets' ), '.ts-step__details b' );
		$this->add_control(
			'details_look',
			array(
				'label'   => __( 'Extra line look', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'line',
				'options' => array(
					'line' => __( 'A line of text', 'travelstore-custom-widgets' ),
					'pill' => __( 'A small pill', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_color_control( 'details_bg', __( 'Pill colour', 'travelstore-custom-widgets' ), '.ts-steps--details-pill .ts-step__details', 'background-color', '', array( 'condition' => array( 'details_look' => 'pill' ) ) );
		$this->add_color_control( 'details_border', __( 'Pill border colour', 'travelstore-custom-widgets' ), '.ts-steps--details-pill .ts-step__details', 'border-color', '', array( 'condition' => array( 'details_look' => 'pill' ) ) );
		$this->add_control(
			'colors_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Leave the colours empty to follow "Text colours" (Layout). The defaults come from Site Settings.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = isset( $s['items'] ) && is_array( $s['items'] ) ? $s['items'] : array();

		if ( empty( $items ) ) {
			$this->render_empty( __( 'Add at least one step to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$layout    = isset( $s['layout'] ) && 'strip' === $s['layout'] ? 'strip' : 'list';
		$connector = isset( $s['connector'] ) && in_array( $s['connector'], array( 'none', 'line', 'dividers' ), true ) ? $s['connector'] : 'dividers';
		$marker    = isset( $s['marker_style'] ) && in_array( $s['marker_style'], array( 'soft', 'outline', 'filled', 'dot', 'none' ), true ) ? $s['marker_style'] : 'soft';
		$auto      = ! isset( $s['numbering'] ) || 'custom' !== $s['numbering'];
		$tag       = $this->safe_tag( isset( $s['title_tag'] ) ? $s['title_tag'] : 'h3', 'h3' );
		$tone      = $this->safe_tone( isset( $s['tone'] ) ? $s['tone'] : 'terracotta', 'terracotta' );

		$classes = array( 'ts-w', 'ts-steps', 'ts-steps--' . $layout, 'ts-steps--' . $connector, 'ts-steps--marker-' . $marker );
		if ( isset( $s['text_scheme'] ) && 'light' === $s['text_scheme'] ) {
			$classes[] = 'ts-steps--light';
			$classes[] = 'ts-on-dark';
		}

		// Line options (vertical list with a joining line).
		$is_line    = 'list' === $layout && 'line' === $connector;
		$step_line  = 'line' === $connector && isset( $s['line_colors'] ) && 'steps' === $s['line_colors'];
		$continuous = $is_line && isset( $s['line_join'] ) && 'continuous' === $s['line_join'];
		$dotted     = $is_line && isset( $s['line_style'] ) && 'dotted' === $s['line_style'];
		if ( $continuous ) {
			$classes[] = 'ts-steps--line-continuous';
		}
		if ( $dotted ) {
			$classes[] = 'ts-steps--line-dotted';
		} elseif ( $step_line ) {
			$classes[] = 'ts-steps--line-steps';
		}
		if ( $is_line && ! empty( $s['line_rules'] ) ) {
			$classes[] = 'ts-steps--rules';
		}
		if ( isset( $s['details_look'] ) && 'pill' === $s['details_look'] ) {
			$classes[] = 'ts-steps--details-pill';
		}
		if ( isset( $s['step_box'] ) && 'box' === $s['step_box'] ) {
			$classes[] = 'ts-steps--boxed';
		}

		// "Line colours: from the steps": each step's colour, in order (steps without a colour are skipped).
		$colors = array();
		if ( $step_line && ! $dotted ) {
			foreach ( $items as $item ) {
				$colors[] = $this->item_color( $item, 'step_color' );
			}
		}
		$list_style = '';
		if ( $continuous && count( array_filter( $colors ) ) >= 2 ) {
			$list_style = ' style="--ts-step-grad: linear-gradient(180deg, ' . esc_attr( implode( ', ', array_filter( $colors ) ) ) . ');"';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<ol class="ts-steps__list ts-tone--<?php echo esc_attr( $tone ); ?>"<?php echo $list_style; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php
				$n = 0;
				foreach ( $items as $item ) {
					++$n;
					$mark = isset( $item['marker'] ) ? trim( (string) $item['marker'] ) : '';
					if ( '' === $mark && $auto ) {
						$mark = str_pad( (string) $n, 2, '0', STR_PAD_LEFT );
					}
					$rid = ! empty( $item['_id'] ) ? sanitize_html_class( $item['_id'] ) : 'i' . $n;
					// Segment from this step to the next blends into the next step's colour.
					$next     = isset( $colors[ $n ] ) ? $colors[ $n ] : '';
					$li_style = ( $step_line && ! $continuous && '' !== $next ) ? ' style="--ts-step-next: ' . esc_attr( $next ) . ';"' : '';
					?>
					<li class="ts-step elementor-repeater-item-<?php echo esc_attr( $rid ); ?>"<?php echo $li_style; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<?php if ( 'none' !== $marker ) : ?>
							<div class="ts-step__marker" aria-hidden="true"><?php echo 'dot' === $marker ? '' : esc_html( $mark ); ?></div>
						<?php endif; ?>
						<div class="ts-step__body">
							<div class="ts-step__head">
								<?php if ( ! empty( $item['label'] ) ) : ?>
									<div class="ts-step__label"><?php echo esc_html( $item['label'] ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $item['title'] ) ) : ?>
									<<?php echo esc_attr( $tag ); ?> class="ts-step__title"><?php echo esc_html( $item['title'] ); ?></<?php echo esc_attr( $tag ); ?>>
								<?php endif; ?>
							</div>
							<div class="ts-step__main">
								<?php if ( ! empty( $item['text'] ) ) : ?>
									<p class="ts-step__text"><?php echo nl2br( esc_html( $item['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
								<?php endif; ?>
								<?php
								$details = isset( $item['details'] ) ? trim( (string) $item['details'] ) : '';
								if ( '' !== $details ) {
									$parts = array_map( 'trim', explode( '|', $details, 2 ) );
									echo '<p class="ts-step__details">';
									if ( isset( $parts[1] ) ) {
										echo '<b>' . esc_html( $parts[0] ) . '</b> ' . esc_html( $parts[1] );
									} else {
										echo esc_html( $parts[0] );
									}
									echo '</p>';
								}
								?>
							</div>
						</div>
					</li>
					<?php
				}
				?>
			</ol>
		</div>
		<?php
	}
}
