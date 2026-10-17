<?php
/**
 * Widget: TS Panel — a call-to-action or highlight panel.
 *
 * Chip, heading, text and up to two buttons on a gradient, light or dark panel.
 * Use it for the closing call to action and for "already part of our route" boxes.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Panel extends TS_Base_Widget {

	public function get_name() {
		return 'ts-panel';
	}

	public function get_title() {
		return __( 'TS Panel', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_keywords() {
		return array( 'cta', 'call to action', 'panel', 'banner', 'button', 'highlight', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Content', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'chip',
			array(
				'label'       => __( 'Small label (chip)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Optional. Leave empty to hide it.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_control(
			'chip_tone',
			array(
				'label'     => __( 'Chip colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'gold',
				'options'   => self::tone_options(),
				'condition' => array( 'chip!' => '' ),
			)
		);
		$this->add_control(
			'heading',
			array(
				'label'     => __( 'Heading', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'default'   => __( 'Ready to plan your trip?', 'travelstore-custom-widgets' ),
				'dynamic'   => array( 'active' => true ),
				'separator' => 'before',
			)
		);
		$this->add_tag_control( 'heading_tag', __( 'Heading level', 'travelstore-custom-widgets' ), 'h2' );
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'Tell us your dates and we will take care of the rest.', 'travelstore-custom-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		// 1.9.0: small round pictures before the text (photo, or letters on a colour until a photo arrives).
		$av = new Repeater();
		$av->add_control(
			'image',
			array(
				'label'       => __( 'Photo', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Optional. A photo replaces the letters and the colour.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_image_options( $av, 'image', array( 'size' => 'thumbnail', 'caption' => false, 'link' => false ) );
		$av->add_control(
			'letters',
			array(
				'label'       => __( 'Letters', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'For example a person’s initials: KA. Shown while there is no photo.', 'travelstore-custom-widgets' ),
			)
		);
		$av->add_control(
			'color',
			array(
				'label'     => __( 'Colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F3692A',
				'selectors' => array( '{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'avatars',
			array(
				'label'       => __( 'Round pictures (optional)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $av->get_controls(),
				'title_field' => '{{{ letters }}}',
				'default'     => array(),
				'separator'   => 'before',
				'description' => __( 'Small overlapping circles before the text, for example the team. Leave empty for none.', 'travelstore-custom-widgets' ),
			)
		);

		$this->add_button_controls( 'primary', __( 'First button', 'travelstore-custom-widgets' ), 'light', array(), __( 'Start planning', 'travelstore-custom-widgets' ) );
		$this->add_button_controls( 'secondary', __( 'Second button', 'travelstore-custom-widgets' ), 'secondary' );
		$this->end_controls_section();

		// 1.9.5: a picture behind everything. Layers, bottom to top: panel colour → picture → overlay → text.
		$this->start_controls_section(
			'ts_content_bg',
			array( 'label' => __( 'Background picture', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'bg_image',
			array(
				'label'       => __( 'Picture', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Optional. Fills the whole panel behind the text. Add an overlay to keep the text readable: Style › Background overlay.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		// Decorative picture behind the text: no caption and no link (the buttons are the links).
		$this->add_image_options( $this, 'bg_image', array( 'size' => 'full', 'caption' => false, 'link' => false ) );
		$this->end_controls_section();

		/* ---------------- Style ---------------- */
		$this->start_controls_section(
			'ts_section_layout',
			array(
				'label' => __( 'Panel', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'panel_style',
			array(
				'label'   => __( 'Panel look', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'gradient',
				'options' => array(
					'gradient' => __( 'Colour blend (orange)', 'travelstore-custom-widgets' ),
					'light'    => __( 'Light box', 'travelstore-custom-widgets' ),
					'dark'     => __( 'Dark (brown)', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'color_a',
			array(
				'label'     => __( 'Blend colour 1', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-panel' => '--ts-panel-a: {{VALUE}};' ),
				'condition' => array( 'panel_style' => 'gradient' ),
			)
		);
		$this->add_control(
			'color_b',
			array(
				'label'     => __( 'Blend colour 2', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-panel' => '--ts-panel-b: {{VALUE}};' ),
				'condition' => array( 'panel_style' => 'gradient' ),
			)
		);
		$this->add_control(
			'panel_bg',
			array(
				'label'     => __( 'Box colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-panel' => 'background: {{VALUE}};' ),
				'condition' => array( 'panel_style!' => 'gradient' ),
			)
		);
		$this->add_control(
			'panel_border',
			array(
				'label'     => __( 'Border colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-panel' => 'border-color: {{VALUE}};' ),
				'condition' => array( 'panel_style' => 'light' ),
			)
		);
		$this->add_responsive_control(
			'edge_width',
			array(
				'label'       => __( 'Coloured left edge', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 20 ) ),
				'description' => __( 'Optional thick edge on the left, for example 6px on a notice box.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-panel' => 'border-left: {{SIZE}}{{UNIT}} solid var(--ts-panel-edge, var(--ts-terra));' ),
			)
		);
		$this->add_control(
			'edge_color',
			array(
				'label'     => __( 'Edge colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => 'globals/colors?id=ts_terracotta' ),
				'selectors' => array( '{{WRAPPER}} .ts-panel' => '--ts-panel-edge: {{VALUE}};' ),
				'condition' => array( 'edge_width[size]!' => '' ),
			)
		);
		// Per device since 1.6.6. The values write the variables described at the top of ts-panel.css.
		// Empty = "Automatic", the behaviour before 1.6.6 (side by side, stacked and centred up to 820px wide).
		$this->add_responsive_control(
			'layout',
			array(
				'label'                => __( 'Arrangement', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => '',
				'options'              => array(
					''      => __( 'Automatic (side by side; stacked and centred on small screens)', 'travelstore-custom-widgets' ),
					'row'   => __( 'Text left, buttons right', 'travelstore-custom-widgets' ),
					'stack' => __( 'Stacked (buttons under the text)', 'travelstore-custom-widgets' ),
				),
				'description'          => __( 'Can differ for desktop, tablet and phone: click the screen icon next to the name.', 'travelstore-custom-widgets' ),
				'selectors_dictionary' => array(
					'row'   => '--ts-panel-dir:row;--ts-panel-align:var(--ts-panel-v,center);--ts-panel-justify:space-between;--ts-panel-copy-flex:1 1 320px;--ts-panel-copy-w:auto;--ts-panel-btn-self:var(--ts-panel-btn-y,auto);--ts-panel-btn-justify:flex-start;',
					'stack' => '--ts-panel-dir:column;--ts-panel-align:var(--ts-panel-items,flex-start);--ts-panel-justify:var(--ts-panel-v,flex-start);--ts-panel-copy-flex:none;--ts-panel-copy-w:100%;--ts-panel-btn-self:var(--ts-panel-btn-x,var(--ts-panel-items,flex-start));--ts-panel-btn-justify:var(--ts-panel-btn-x,var(--ts-panel-items,flex-start));',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-panel' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'buttons_x',
			array(
				'label'                => __( 'Buttons – left / right (when stacked)', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'   => array( 'title' => __( 'Left', 'travelstore-custom-widgets' ), 'icon' => 'eicon-h-align-left' ),
					'center' => array( 'title' => __( 'Centre', 'travelstore-custom-widgets' ), 'icon' => 'eicon-h-align-center' ),
					'right'  => array( 'title' => __( 'Right', 'travelstore-custom-widgets' ), 'icon' => 'eicon-h-align-right' ),
				),
				'description'          => __( 'Nothing chosen = follows "Text alignment".', 'travelstore-custom-widgets' ),
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-panel' => '--ts-panel-btn-x: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'buttons_y',
			array(
				'label'                => __( 'Buttons – up / down (when side by side)', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'top'    => array( 'title' => __( 'Top', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-top' ),
					'middle' => array( 'title' => __( 'Middle', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-middle' ),
					'bottom' => array( 'title' => __( 'Bottom', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-bottom' ),
				),
				'selectors_dictionary' => array(
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-panel' => '--ts-panel-btn-y: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'alignment',
			array(
				'label'     => __( 'Text alignment', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array( 'title' => __( 'Left', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-left' ),
					'center' => array( 'title' => __( 'Center', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-center' ),
				),
				// Centre also centres the heading block and the buttons (used with "Stacked").
				'selectors_dictionary' => array(
					'left'   => 'text-align:left;--ts-panel-items:flex-start;--ts-panel-mx:0;',
					'center' => 'text-align:center;--ts-panel-items:center;--ts-panel-mx:auto;',
				),
				'selectors' => array( '{{WRAPPER}} .ts-panel' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'title_max_width',
			array(
				'label'      => __( 'Heading width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 1000 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'selectors'  => array( '{{WRAPPER}} .ts-panel .ts-panel__title' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);
		// 1.9.4: space between the heading and the text (empty = 10px; most drafts' .cta p: 14px).
		$this->add_responsive_control(
			'text_space',
			array(
				'label'      => __( 'Space above the text', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-panel__text' => 'margin-top: {{SIZE}}{{UNIT}};' ),
			)
		);
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
				'selectors'  => array( '{{WRAPPER}} .ts-panel .ts-panel__text' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'actions_gap',
			array(
				'label'       => __( 'Space between the text and the buttons', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'description' => __( 'When the buttons are under the text (stacked, or side by side on a narrow screen). Default 36px.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-panel' => 'row-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'panel_min_height',
			array(
				'label'       => __( 'Minimum height', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'vh' ),
				'range'       => array(
					'px' => array( 'min' => 0, 'max' => 800 ),
					'vh' => array( 'min' => 0, 'max' => 100 ),
				),
				'description' => __( 'Leave empty for the height of the content. The panel is never shorter than its content; use Padding to make it shorter.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-panel' => 'min-height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'content_v',
			array(
				'label'                => __( 'Content – up / down (with a minimum height)', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'top'    => array( 'title' => __( 'Top', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-top' ),
					'middle' => array( 'title' => __( 'Middle', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-middle' ),
					'bottom' => array( 'title' => __( 'Bottom', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-bottom' ),
				),
				'selectors_dictionary' => array(
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-panel' => '--ts-panel-v: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'panel_padding',
			array(
				'label'      => __( 'Padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'panel_radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-panel' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		// 1.9.0: each corner on its own; registered after "Corner roundness", so it wins when filled.
		$this->add_responsive_control(
			'panel_corners',
			array(
				'label'       => __( 'Corner roundness – each corner', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array( 'px', '%' ),
				'description' => __( 'Optional; replaces "Corner roundness" when filled. Top = top-left corner, Right = top-right, Bottom = bottom-right, Left = bottom-left. For example 0, 18, 18, 0 gives square corners on the left.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: background picture (1.9.5) ---------------- */
		$bg_img       = $this->scoped( '.ts-panel__bg-img' );
		$bg_img_hover = $this->scoped( '.ts-panel:hover .ts-panel__bg-img' );
		$this->start_controls_section(
			'ts_section_bg_image',
			array(
				'label'     => __( 'Background picture', 'travelstore-custom-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'bg_image[url]!' => '' ),
			)
		);
		$this->add_responsive_control(
			'bg_object_fit',
			array(
				'label'     => __( 'Object Fit', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''           => __( 'Default (Cover)', 'travelstore-custom-widgets' ),
					'cover'      => __( 'Cover (fill the panel, edges may be cropped)', 'travelstore-custom-widgets' ),
					'contain'    => __( 'Contain (show the whole picture)', 'travelstore-custom-widgets' ),
					'fill'       => __( 'Fill (stretch, may distort)', 'travelstore-custom-widgets' ),
					'scale-down' => __( 'Scale Down (never larger than the photo itself)', 'travelstore-custom-widgets' ),
				),
				'selectors' => array( $bg_img => 'object-fit: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'bg_object_position',
			array(
				'label'       => __( 'Object Position', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
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
				),
				'description' => __( 'Which part of the photo stays in view when it is cropped.', 'travelstore-custom-widgets' ),
				'selectors'   => array( $bg_img => 'object-position: {{VALUE}};' ),
			)
		);
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
				'raw'             => esc_html__( 'Used while the pointer is anywhere over the panel.', 'travelstore-custom-widgets' ),
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

		/* ---------------- Style: background overlay (1.9.5) ----------------
		 * Elementor's own Background Overlay controls (as on a container), drawn on the panel's ::before.
		 * The Background Type sets --ts-panel-overlay, so the layer exists only once a type is chosen. */
		$ov       = $this->scoped( '.ts-panel::before' );
		$ov_hover = $this->scoped( '.ts-panel:hover::before' );
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
				'label' => __( 'Background overlay', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'overlay_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'A colour or colour blend over the background picture (or over the panel colour when there is no picture). The text always stays on top.', 'travelstore-custom-widgets' ),
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
					'background' => array( 'selectors' => array( $ov => '--ts-panel-overlay: \'\';' ) ),
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
				'selectors' => array( $this->scoped( '.ts-panel' ) => '--ts-panel-ov-opacity: {{SIZE}};' ),
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
				'description' => __( 'How the overlay mixes with the picture under it. The text is never mixed.', 'travelstore-custom-widgets' ),
				'selectors'   => array( $this->scoped( '.ts-panel' ) => '--ts-panel-ov-blend: {{VALUE}};' ),
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
					'background' => array( 'selectors' => array( $ov_hover => '--ts-panel-overlay: \'\';' ) ),
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
				'selectors' => array( $this->scoped( '.ts-panel:hover' ) => '--ts-panel-ov-opacity: {{SIZE}};' ),
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
				'label'     => __( 'Transition Duration', 'travelstore-custom-widgets' ) . ' (s)',
				'type'      => Controls_Manager::SLIDER,
				'default'   => array( 'size' => 0.3 ),
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ) ),
				'selectors' => array( $ov => 'transition-duration: {{SIZE}}s;' ),
				'conditions' => $ov_terms,
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();

		/* ---------------- Style: round pictures (1.9.0) ---------------- */
		$this->start_controls_section(
			'ts_section_avatars',
			array(
				'label' => __( 'Round pictures', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'avatar_size',
			array(
				'label'      => __( 'Size', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 120 ) ),
				'default'    => array( 'size' => 56, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-panel' => '--ts-panel-av: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'avatar_overlap',
			array(
				'label'      => __( 'Overlap', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-panel' => '--ts-panel-av-overlap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'avatar_ring', __( 'Ring colour', 'travelstore-custom-widgets' ), '.ts-panel__av', 'border-color', 'ts_sand' );
		$this->add_typography_control( 'avatar_typography', __( 'Letters typography', 'travelstore-custom-widgets' ), '.ts-panel__av' );
		$this->add_color_control( 'avatar_text_color', __( 'Letters colour', 'travelstore-custom-widgets' ), '.ts-panel__av' );
		$this->end_controls_section();

		$this->start_controls_section(
			'ts_section_text_style',
			array(
				'label' => __( 'Text', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'heading_typography', __( 'Heading typography', 'travelstore-custom-widgets' ), '.ts-panel__title', 'ts_heading' );
		$this->add_color_control( 'heading_color', __( 'Heading colour', 'travelstore-custom-widgets' ), '.ts-panel__title' );
		$this->add_typography_control( 'text_typography', __( 'Text typography', 'travelstore-custom-widgets' ), '.ts-panel__text', 'ts_body' );
		$this->add_color_control( 'text_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-panel__text' );
		$this->end_controls_section();

		$this->add_button_style_section();
		$this->register_extra_css_controls();
	}

	/**
	 * Round pictures (decorative: the text next to them says who they are).
	 */
	private function render_avatars( $items ) {
		$out = '';
		foreach ( $items as $i => $item ) {
			$rid     = ! empty( $item['_id'] ) ? sanitize_html_class( $item['_id'] ) : 'a' . $i;
			$img     = $this->image_tag( $item, 'image', 'thumbnail', '', array( 'class' => 'ts-panel__av-img', 'loading' => 'lazy' ) );
			$letters = isset( $item['letters'] ) ? trim( (string) $item['letters'] ) : '';
			if ( '' === $img && '' === $letters ) {
				continue;
			}
			$out .= '<span class="ts-panel__av elementor-repeater-item-' . esc_attr( $rid ) . '">' . ( '' !== $img ? $img : esc_html( $letters ) ) . '</span>';
		}
		if ( '' !== $out ) {
			echo '<div class="ts-panel__avatars" aria-hidden="true">' . $out . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts.
		}
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$chip    = isset( $s['chip'] ) ? trim( (string) $s['chip'] ) : '';
		$heading = isset( $s['heading'] ) ? trim( (string) $s['heading'] ) : '';
		$text    = isset( $s['text'] ) ? trim( (string) $s['text'] ) : '';
		$has_btn = ! empty( $s['primary_text'] ) || ! empty( $s['secondary_text'] );

		if ( '' === $chip && '' === $heading && '' === $text && ! $has_btn ) {
			$this->render_empty( __( 'Add a heading, text or a button to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$look    = isset( $s['panel_style'] ) && in_array( $s['panel_style'], array( 'gradient', 'light', 'dark' ), true ) ? $s['panel_style'] : 'gradient';
		$layout  = isset( $s['layout'] ) && 'stack' === $s['layout'] ? 'stack' : 'row';
		$tag     = $this->safe_tag( isset( $s['heading_tag'] ) ? $s['heading_tag'] : 'h2', 'h2' );
		$classes = array( 'ts-w', 'ts-panel', 'ts-panel--' . $look, 'ts-panel--' . $layout );
		if ( 'light' !== $look ) {
			$classes[] = 'ts-on-dark';
		}
		// Background picture: decorative (empty alt, hidden from screen readers); the text says what the panel is about.
		$bg = $this->image_tag( $s, 'bg_image', 'full', '', array( 'class' => 'ts-panel__bg-img', 'loading' => 'lazy', 'aria-hidden' => 'true' ) );
		if ( '' !== $bg ) {
			$classes[] = 'ts-panel--has-bg';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php
			if ( '' !== $bg ) {
				echo '<div class="ts-panel__bg">' . $bg . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built by wp_get_attachment_image / image_tag.
			}
			$this->render_avatars( isset( $s['avatars'] ) && is_array( $s['avatars'] ) ? $s['avatars'] : array() );
			?>
			<div class="ts-panel__copy">
				<?php
				if ( '' !== $chip ) {
					echo '<div class="ts-panel__chip">' . $this->chip_html( $chip, isset( $s['chip_tone'] ) ? $s['chip_tone'] : 'gold', false ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				if ( '' !== $heading ) {
					printf( '<%1$s class="ts-panel__title">%2$s</%1$s>', esc_attr( $tag ), nl2br( esc_html( $heading ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				}
				if ( '' !== $text ) {
					echo '<p class="ts-panel__text">' . nl2br( esc_html( $text ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				?>
			</div>
			<?php if ( $has_btn ) : ?>
				<div class="ts-panel__actions">
					<?php
					$this->render_button( $s, 'primary' );
					$this->render_button( $s, 'secondary' );
					?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
