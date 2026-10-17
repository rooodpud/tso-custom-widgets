<?php
/**
 * Widget: TS Stats — a row of figures, each a value with a short label.
 *
 * Covers the stat strips in the page drafts: "1668 · Completed" beside a fort,
 * "15+ · Years in Oman" in an about section, centred figures over a picture, or a
 * quick-facts strip. Optional thin dividers between the figures.
 * 1.9.8: optional picture behind the figures (a drawing that comes with the site or any image)
 * with an overlay; the figures are always the top layer.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Stats extends TS_Base_Widget {

	public function get_name() {
		return 'ts-stats';
	}

	public function get_title() {
		return __( 'TS Stats', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-counter';
	}

	public function get_keywords() {
		return array( 'stats', 'numbers', 'figures', 'facts', 'counter', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Figures', 'travelstore-custom-widgets' ) )
		);

		$r = new Repeater();
		$r->add_control(
			'value',
			array(
				'label'       => __( 'Figure', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'For example: 1668, 15+, 36m, 4.8★', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'label',
			array(
				'label'       => __( 'Label', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'A few words under the figure. Optional.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Figures', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ value }}} {{{ label }}}',
				'default'     => array(
					array(
						'value' => '15+',
						'label' => __( 'Years operating', 'travelstore-custom-widgets' ),
					),
					array(
						'value' => '20+',
						'label' => __( 'Local guides', 'travelstore-custom-widgets' ),
					),
					array(
						'value' => '4.8★',
						'label' => __( 'Traveller rating', 'travelstore-custom-widgets' ),
					),
				),
			)
		);
		$this->end_controls_section();

		// 1.9.8: a picture behind the figures. Layers, bottom to top: picture → overlay → figures.
		$this->start_controls_section(
			'ts_content_bg',
			array( 'label' => __( 'Background picture', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'bg_art',
			array(
				'label'       => __( 'Drawing', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array( '' => __( 'None', 'travelstore-custom-widgets' ) ) + \TravelStore\TS_Art::options(),
				'description' => __( 'Optional. Illustrations that come with the site, shown behind the figures.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'bg_image',
			array(
				'label'       => __( 'Or choose your own picture', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'A photo (JPG, PNG or WebP). It replaces the drawing above. Add an overlay to keep the figures readable: Style › Background overlay. For light figures choose Style › Layout › Text colours › Light text.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		// Decorative picture behind the figures: no caption and no link.
		$this->add_image_options( $this, 'bg_image', array( 'size' => 'full', 'caption' => false, 'link' => false ) );
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
		$this->add_responsive_control(
			'direction',
			array(
				'label'                => __( 'Arrangement', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'row',
				'options'              => array(
					'row'    => __( 'Side by side', 'travelstore-custom-widgets' ),
					'column' => __( 'One under another', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'row'    => 'flex-direction:row;--ts-stat-bl:var(--ts-stat-line-w);--ts-stat-bt:0px;--ts-stat-pl:var(--ts-stat-gap);--ts-stat-pt:0px;',
					'column' => 'flex-direction:column;--ts-stat-bl:0px;--ts-stat-bt:var(--ts-stat-line-w);--ts-stat-pl:0px;--ts-stat-pt:var(--ts-stat-gap);',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-stats__list' => '{{VALUE}}' ),
			)
		);
		$this->add_control(
			'distribution',
			array(
				'label'                => __( 'Width of each figure', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'content',
				'options'              => array(
					'content' => __( 'As wide as its text', 'travelstore-custom-widgets' ),
					'equal'   => __( 'Equal widths (fill the row)', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'content' => 'flex:0 1 auto;',
					'equal'   => 'flex:1 1 0;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-stat' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'per_row',
			array(
				'label'                => __( 'Figures per row', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => '',
				'options'              => array(
					''  => __( 'Automatic', 'travelstore-custom-widgets' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'description'          => __( 'For example 2 on phones for a 2 × 2 grid. Automatic follows "Width of each figure".', 'travelstore-custom-widgets' ),
				'selectors_dictionary' => array(
					'1' => 'flex:0 0 100%;',
					'2' => 'flex:0 0 calc((100% - var(--ts-stat-gap)) / 2);',
					'3' => 'flex:0 0 calc((100% - 2 * var(--ts-stat-gap)) / 3);',
					'4' => 'flex:0 0 calc((100% - 3 * var(--ts-stat-gap)) / 4);',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-stat' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'alignment',
			array(
				'label'                => __( 'Alignment', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'   => array( 'title' => __( 'Left', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-left' ),
					'center' => array( 'title' => __( 'Center', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-center' ),
				),
				'selectors_dictionary' => array(
					'left'   => 'justify-content:flex-start;text-align:left;',
					'center' => 'justify-content:center;text-align:center;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-stats__list' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'spacing',
			array(
				'label'      => __( 'Space between figures', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-stats' => '--ts-stat-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'dividers',
			array(
				'label'        => __( 'Thin line between figures', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_responsive_control(
			'divider_color',
			array(
				'label'       => __( 'Line colour', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'Can differ per device; choose a fully transparent colour to hide the lines on phones.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-stats' => '--ts-stat-line: {{VALUE}};' ),
				'condition'   => array( 'dividers' => 'yes' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: text ---------------- */
		$this->start_controls_section(
			'ts_section_text_style',
			array(
				'label' => __( 'Text', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'value_typography', __( 'Figure typography', 'travelstore-custom-widgets' ), '.ts-stat__value', 'ts_heading' );
		$this->add_color_control( 'value_color', __( 'Figure colour', 'travelstore-custom-widgets' ), '.ts-stat__value' );
		$this->add_typography_control( 'label_typography', __( 'Label typography', 'travelstore-custom-widgets' ), '.ts-stat__label' );
		$this->add_color_control( 'label_color', __( 'Label colour', 'travelstore-custom-widgets' ), '.ts-stat__label' );
		$this->add_control(
			'colors_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Leave the colours empty to follow "Text colours" (Layout). The defaults come from Site Settings.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->end_controls_section();

		$this->register_background_controls();
		$this->register_extra_css_controls();
	}

	/**
	 * 1.9.8: Style sections for the picture behind the figures — the box (height, padding, where the
	 * figures sit), the picture (as TS Panel 1.9.5 plus Custom X / Y as in 1.9.6) and Elementor's own
	 * Background Overlay controls, drawn on the box's ::before. The figures always stay on top.
	 */
	private function register_background_controls() {
		$has_bg = array(
			'relation' => 'or',
			'terms'    => array(
				array( 'name' => 'bg_art', 'operator' => '!==', 'value' => '' ),
				array( 'name' => 'bg_image[url]', 'operator' => '!==', 'value' => '' ),
			),
		);
		$box = $this->scoped( '.ts-stats--layered' );

		$this->start_controls_section(
			'ts_section_box',
			array(
				'label'      => __( 'Box with the picture', 'travelstore-custom-widgets' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $has_bg,
			)
		);
		$this->add_responsive_control(
			'box_min_height',
			array(
				'label'       => __( 'Minimum height', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'vh' ),
				'range'       => array(
					'px' => array( 'min' => 0, 'max' => 900 ),
					'vh' => array( 'min' => 0, 'max' => 100 ),
				),
				'description' => __( 'The box grows taller by itself when the figures need more room, for example on phones.', 'travelstore-custom-widgets' ),
				'selectors'   => array( $box => 'min-height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'box_v_align',
			array(
				'label'                => __( 'Figures position', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'top'    => array( 'title' => __( 'Top', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-top' ),
					'middle' => array( 'title' => __( 'Middle', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-middle' ),
					'bottom' => array( 'title' => __( 'Bottom', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-bottom' ),
				),
				'default'              => 'middle',
				'selectors_dictionary' => array(
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				),
				'selectors'            => array( $box => 'justify-content: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'description' => __( 'Space between the edge of the picture and the figures.', 'travelstore-custom-widgets' ),
				'selectors'  => array( $box => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Border Radius', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'selectors'  => array( $box => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: background picture ---------------- */
		$bg_img       = $this->scoped( '.ts-stats__bg-img' );
		$bg_img_hover = $this->scoped( '.ts-stats--layered:hover .ts-stats__bg-img' );
		$this->start_controls_section(
			'ts_section_bg_image',
			array(
				'label'      => __( 'Background picture', 'travelstore-custom-widgets' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $has_bg,
			)
		);
		$this->add_responsive_control(
			'bg_object_fit',
			array(
				'label'       => __( 'Object Fit', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''           => __( 'Default', 'travelstore-custom-widgets' ),
					'cover'      => __( 'Cover (fill the box, edges may be cropped)', 'travelstore-custom-widgets' ),
					'contain'    => __( 'Contain (show the whole picture)', 'travelstore-custom-widgets' ),
					'fill'       => __( 'Fill (stretch, may distort)', 'travelstore-custom-widgets' ),
					'scale-down' => __( 'Scale Down (never larger than the photo itself)', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'Default: Cover; drawings made to stretch (such as the Wahiba dune band) fill the box.', 'travelstore-custom-widgets' ),
				'selectors'   => array( $bg_img => 'object-fit: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'bg_object_position',
			array(
				'label'                => __( 'Object Position', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => '',
				'options'              => array(
					''              => __( 'Default (Center Center)', 'travelstore-custom-widgets' ),
					'center center' => __( 'Center Center', 'travelstore-custom-widgets' ),
					'center left'   => __( 'Center Left', 'travelstore-custom-widgets' ),
					'center right'  => __( 'Center Right', 'travelstore-custom-widgets' ),
					'top center'    => __( 'Top Center', 'travelstore-custom-widgets' ),
					'top left'      => __( 'Top Left', 'travelstore-custom-widgets' ),
					'top right'     => __( 'Top Right', 'travelstore-custom-widgets' ),
					'bottom center' => __( 'Bottom Center', 'travelstore-custom-widgets' ),
					'bottom left'   => __( 'Bottom Left', 'travelstore-custom-widgets' ),
					'bottom right'  => __( 'Bottom Right', 'travelstore-custom-widgets' ),
					'custom'        => __( 'Custom (X and Y below)', 'travelstore-custom-widgets' ),
				),
				'description'          => __( 'Which part of the picture stays in view when it is cropped.', 'travelstore-custom-widgets' ),
				'selectors_dictionary' => array( 'custom' => 'var(--ts-obj-x, 50%) var(--ts-obj-y, 50%)' ),
				'selectors'            => array( $bg_img => 'object-position: {{VALUE}};' ),
			)
		);
		foreach ( array( 'x' => __( 'X Position (left to right)', 'travelstore-custom-widgets' ), 'y' => __( 'Y Position (top to bottom)', 'travelstore-custom-widgets' ) ) as $axis => $label ) {
			$this->add_responsive_control(
				'bg_object_position_' . $axis,
				array(
					'label'      => $label,
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'range'      => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
					'default'    => array( 'size' => 50, 'unit' => '%' ),
					'selectors'  => array( $bg_img => '--ts-obj-' . $axis . ': {{SIZE}}{{UNIT}};' ),
					'condition'  => array( 'bg_object_position' => 'custom' ),
				)
			);
		}
		$this->start_controls_tabs( 'bg_image_effects', array( 'separator' => 'before' ) );
		$this->start_controls_tab( 'bg_image_normal', array( 'label' => __( 'Normal', 'travelstore-custom-widgets' ) ) );
		$this->add_control(
			'bg_opacity',
			array(
				'label'     => __( 'Opacity', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.01 ) ),
				'selectors' => array( $bg_img => 'opacity: {{SIZE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'bg_css_filters',
				'selector' => $bg_img,
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'bg_image_hover', array( 'label' => __( 'Hover', 'travelstore-custom-widgets' ) ) );
		$this->add_control(
			'bg_hover_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Used while the pointer is anywhere over the box.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_control(
			'bg_opacity_hover',
			array(
				'label'     => __( 'Opacity', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.01 ) ),
				'selectors' => array( $bg_img_hover => 'opacity: {{SIZE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'bg_css_filters_hover',
				'selector' => $bg_img_hover,
			)
		);
		$this->add_control(
			'bg_transition',
			array(
				'label'     => __( 'Transition Duration', 'travelstore-custom-widgets' ) . ' (s)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ) ),
				'selectors' => array( $bg_img => 'transition-duration: {{SIZE}}s;' ),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		/* ---------------- Style: background overlay ----------------
		 * The Background Type sets --ts-stats-overlay, so the layer exists only once a type is chosen
		 * (as Elementor does for a container's Background Overlay). */
		$ov       = $this->scoped( '.ts-stats--layered::before' );
		$ov_hover = $this->scoped( '.ts-stats--layered:hover::before' );
		$ov_terms = array(
			'relation' => 'or',
			'terms'    => array(
				array( 'name' => 'overlay_background', 'operator' => 'in', 'value' => array( 'classic', 'gradient' ) ),
				array( 'name' => 'overlay_hover_background', 'operator' => 'in', 'value' => array( 'classic', 'gradient' ) ),
			),
		);
		$this->start_controls_section(
			'ts_section_overlay',
			array(
				'label'      => __( 'Background overlay', 'travelstore-custom-widgets' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $has_bg,
			)
		);
		$this->add_control(
			'overlay_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'A colour or colour blend over the background picture. The figures always stay on top.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->start_controls_tabs( 'overlay_tabs' );

		$this->start_controls_tab( 'overlay_tab_normal', array( 'label' => __( 'Normal', 'travelstore-custom-widgets' ) ) );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'overlay',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => $ov,
				'fields_options' => array(
					'background' => array( 'selectors' => array( $ov => '--ts-stats-overlay: \'\';' ) ),
				),
			)
		);
		$this->add_responsive_control(
			'overlay_opacity',
			array(
				'label'     => __( 'Opacity', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array( 'size' => 0.5 ),
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.01 ) ),
				'selectors' => array( $box => '--ts-stats-ov-opacity: {{SIZE}};' ),
				'condition' => array( 'overlay_background' => array( 'classic', 'gradient' ) ),
			)
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'       => 'overlay_css_filters',
				'selector'   => $ov,
				'conditions' => $ov_terms,
			)
		);
		$this->add_control(
			'overlay_blend_mode',
			array(
				'label'       => __( 'Blend Mode', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''            => __( 'Normal', 'travelstore-custom-widgets' ),
					'multiply'    => __( 'Multiply', 'travelstore-custom-widgets' ),
					'screen'      => __( 'Screen', 'travelstore-custom-widgets' ),
					'overlay'     => __( 'Overlay', 'travelstore-custom-widgets' ),
					'darken'      => __( 'Darken', 'travelstore-custom-widgets' ),
					'lighten'     => __( 'Lighten', 'travelstore-custom-widgets' ),
					'color-dodge' => __( 'Color Dodge', 'travelstore-custom-widgets' ),
					'saturation'  => __( 'Saturation', 'travelstore-custom-widgets' ),
					'color'       => __( 'Color', 'travelstore-custom-widgets' ),
					'luminosity'  => __( 'Luminosity', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'How the overlay mixes with the picture under it. The figures are never mixed.', 'travelstore-custom-widgets' ),
				'selectors'   => array( $box => '--ts-stats-ov-blend: {{VALUE}};' ),
				'conditions'  => $ov_terms,
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'overlay_tab_hover', array( 'label' => __( 'Hover', 'travelstore-custom-widgets' ) ) );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'overlay_hover',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => $ov_hover,
				'fields_options' => array(
					'background' => array( 'selectors' => array( $ov_hover => '--ts-stats-overlay: \'\';' ) ),
				),
			)
		);
		$this->add_responsive_control(
			'overlay_hover_opacity',
			array(
				'label'     => __( 'Opacity', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array( 'size' => 0.5 ),
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.01 ) ),
				'selectors' => array( $this->scoped( '.ts-stats--layered:hover' ) => '--ts-stats-ov-opacity: {{SIZE}};' ),
				'condition' => array( 'overlay_hover_background' => array( 'classic', 'gradient' ) ),
			)
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'       => 'overlay_css_filters_hover',
				'selector'   => $ov_hover,
				'conditions' => $ov_terms,
			)
		);
		$this->add_control(
			'overlay_transition',
			array(
				'label'      => __( 'Transition Duration', 'travelstore-custom-widgets' ) . ' (s)',
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => 0.3 ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ) ),
				'selectors'  => array( $ov => 'transition-duration: {{SIZE}}s;' ),
				'conditions' => $ov_terms,
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	/**
	 * The picture behind the figures ('' when none): a chosen image wins over a bundled drawing.
	 * Decorative (empty alt, hidden from screen readers): the figures say what the box is about.
	 */
	private function background_html( $s ) {
		$img = $this->image_tag( $s, 'bg_image', 'full', '', array( 'class' => 'ts-stats__bg-img', 'loading' => 'lazy', 'aria-hidden' => 'true' ) );
		if ( '' === $img && ! empty( $s['bg_art'] ) ) {
			$src = \TravelStore\TS_Art::url( $s['bg_art'] );
			if ( '' !== $src ) {
				$class = 'ts-stats__bg-img' . ( \TravelStore\TS_Art::stretches( $s['bg_art'] ) ? ' ts-stats__bg-img--stretch' : '' );
				$img   = '<img src="' . esc_url( $src ) . '" class="' . esc_attr( $class ) . '" alt="" aria-hidden="true" loading="lazy" decoding="async" />';
			}
		}
		return '' === $img ? '' : '<div class="ts-stats__bg">' . $img . '</div>';
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = array();
		foreach ( isset( $s['items'] ) && is_array( $s['items'] ) ? $s['items'] : array() as $item ) {
			$value = isset( $item['value'] ) ? trim( (string) $item['value'] ) : '';
			$label = isset( $item['label'] ) ? trim( (string) $item['label'] ) : '';
			if ( '' !== $value || '' !== $label ) {
				$items[] = array( $value, $label );
			}
		}

		if ( empty( $items ) ) {
			$this->render_empty( __( 'Add at least one figure to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$classes = array( 'ts-w', 'ts-stats' );
		if ( ! empty( $s['dividers'] ) ) {
			$classes[] = 'ts-stats--dividers';
		}
		if ( isset( $s['alignment'] ) && 'center' === $s['alignment'] ) {
			$classes[] = 'ts-stats--center';
		}
		if ( isset( $s['text_scheme'] ) && 'light' === $s['text_scheme'] ) {
			$classes[] = 'ts-stats--light';
			$classes[] = 'ts-on-dark';
		}

		$bg = $this->background_html( $s );
		if ( '' !== $bg ) {
			$classes[] = 'ts-stats--layered';
		}

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		if ( '' !== $bg ) {
			// Picture, then the figures in their own layer (the dividers' clipping stays inside it).
			echo $bg; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts.
			echo '<div class="ts-stats__body">';
			self::print_list( $items );
			echo '</div>';
		} else {
			self::print_list( $items );
		}
		echo '</div>';
	}

	/**
	 * Shared markup, also used by TS Split Feature. $items = array( array( value, label ), ... ).
	 */
	public static function print_list( $items ) {
		echo '<ul class="ts-stats__list">';
		foreach ( $items as $item ) {
			echo '<li class="ts-stat">';
			if ( '' !== $item[0] ) {
				echo '<span class="ts-stat__value">' . esc_html( $item[0] ) . '</span>';
			}
			if ( '' !== $item[1] ) {
				echo '<span class="ts-stat__label">' . esc_html( $item[1] ) . '</span>';
			}
			echo '</li>';
		}
		echo '</ul>';
	}
}
