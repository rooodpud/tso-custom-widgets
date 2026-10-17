<?php
/**
 * Widget: TS Tabs — a row of tab buttons, one panel per tab.
 *
 * Each panel is text (caption, title, text, a highlight line, a button) beside a picture
 * (photo, bundled art scene or colour blend). Built for "pick a starting point" choices such as
 * the Home route tabs, but any set of alternatives fits. Keyboard and screen-reader friendly
 * (ARIA tabs pattern); one small script switches panels, and the first panel is readable
 * without it.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Tabs extends TS_Base_Widget {

	public function get_name() {
		return 'ts-tabs';
	}

	public function get_title() {
		return __( 'TS Tabs', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_keywords() {
		return array( 'tabs', 'routes', 'options', 'switch', 'offerings', 'travel' );
	}

	public function get_script_depends() {
		return array( 'ts-tabs' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Tabs', 'travelstore-custom-widgets' ) )
		);

		$r = new Repeater();
		$r->add_control(
			'tab_label',
			array(
				'label'   => __( 'Tab button text', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Tab', 'travelstore-custom-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$r->add_control(
			'text_heading',
			array(
				'label'     => __( 'Panel text', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$r->add_control(
			'kicker',
			array(
				'label'       => __( 'Small caption', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Shown in capitals above the title. Optional.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'dynamic' => array( 'active' => true ),
			)
		);
		$r->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 5,
				'dynamic' => array( 'active' => true ),
			)
		);
		$r->add_control(
			'note',
			array(
				'label'       => __( 'Highlight line', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'A short line in a soft box, for example: 🕌 7 Days · The Classic Loop. Optional.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'button_text',
			array(
				'label'       => __( 'Button text', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Leave empty to hide the button.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'button_link',
			array(
				'label'       => __( 'Button link', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'Type a page name or paste a link', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'button_style',
			array(
				'label'   => __( 'Button look', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'primary',
				'options' => $this->button_style_options(),
			)
		);
		$r->add_control(
			'picture_heading',
			array(
				'label'     => __( 'Panel picture', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$r->add_control(
			'image',
			array(
				'label'       => __( 'Photo', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Optional. A photo replaces the art scene and the colour blend.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_image_options( $r );
		$r->add_control(
			'art',
			array(
				'label'   => __( 'Art scene (optional)', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => \TravelStore\TS_Art::options(),
			)
		);
		$r->add_control(
			'alt',
			array(
				'label'       => __( 'Picture description (screen readers)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Leave empty if the picture is decorative.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'color_a',
			array(
				'label'     => __( 'Colour blend 1', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#807A67',
				'selectors' => array( '{{WRAPPER}} {{CURRENT_ITEM}} .ts-tabs__frame' => '--ts-a: {{VALUE}};' ),
			)
		);
		$r->add_control(
			'color_b',
			array(
				'label'       => __( 'Colour blend 2', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#382520',
				'description' => __( 'The colour blend fills the picture area when there is no photo or art.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} {{CURRENT_ITEM}} .ts-tabs__frame' => '--ts-b: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Tabs', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ tab_label }}}',
				'default'     => array(
					array(
						'tab_label' => __( 'First option', 'travelstore-custom-widgets' ),
						'kicker'    => __( 'First option', 'travelstore-custom-widgets' ),
						'title'     => __( 'What this option is about', 'travelstore-custom-widgets' ),
						'text'      => __( 'Two or three sentences that explain who this option suits and what it includes.', 'travelstore-custom-widgets' ),
						'note'      => __( '7 Days · A sample route', 'travelstore-custom-widgets' ),
					),
					array(
						'tab_label' => __( 'Second option', 'travelstore-custom-widgets' ),
						'kicker'    => __( 'Second option', 'travelstore-custom-widgets' ),
						'title'     => __( 'A different way to travel', 'travelstore-custom-widgets' ),
						'text'      => __( 'Two or three sentences that explain who this option suits and what it includes.', 'travelstore-custom-widgets' ),
						'color_a'   => '#0392CC',
						'color_b'   => '#0D76AB',
					),
				),
			)
		);
		$this->add_tag_control( 'title_tag', __( 'Title level', 'travelstore-custom-widgets' ), 'h3' );
		$this->add_control(
			'active_tab',
			array(
				'label'       => __( 'Tab open at the start', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'step'        => 1,
				'default'     => 1,
				'description' => __( '1 = the first tab. Also handy in the editor to see another tab while you edit it.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'picture_side',
			array(
				'label'   => __( 'Picture on the', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'left'  => __( 'Left', 'travelstore-custom-widgets' ),
					'right' => __( 'Right', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'bar_label',
			array(
				'label'       => __( 'Name of the tab list (screen readers)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Optional, for example: Ways to travel.', 'travelstore-custom-widgets' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: tab buttons ---------------- */
		$this->start_controls_section(
			'ts_section_tabs_style',
			array(
				'label' => __( 'Tab buttons', 'travelstore-custom-widgets' ),
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
			'tab_align',
			array(
				'label'                => __( 'Buttons alignment', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'    => array( 'title' => __( 'Left', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-left' ),
					'center'  => array( 'title' => __( 'Center', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-center' ),
					'right'   => array( 'title' => __( 'Right', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-right' ),
					'stretch' => array( 'title' => __( 'Fill the width', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-justify' ),
				),
				'selectors_dictionary' => array(
					'left'    => 'justify-content:flex-start;--ts-tab-grow:0;',
					'center'  => 'justify-content:center;--ts-tab-grow:0;',
					'right'   => 'justify-content:flex-end;--ts-tab-grow:0;',
					'stretch' => 'justify-content:flex-start;--ts-tab-grow:1;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-tabs__bar' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'tab_gap',
			array(
				'label'      => __( 'Space between buttons', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-tabs' => '--ts-tabs-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'bar_spacing',
			array(
				'label'      => __( 'Space below the buttons', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 36, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-tabs' => '--ts-tabs-bar-space: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_typography_control( 'tab_typography', __( 'Typography', 'travelstore-custom-widgets' ), '.ts-tabs__tab' );
		$this->add_responsive_control(
			'tab_padding',
			array(
				'label'      => __( 'Padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-tabs__tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'tab_radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-tabs__tab' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'tab_border_width',
			array(
				'label'      => __( 'Border width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 6 ) ),
				'default'    => array( 'size' => 2, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-tabs__tab' => 'border-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->start_controls_tabs( 'tab_colors' );
		$this->start_controls_tab( 'tab_colors_normal', array( 'label' => __( 'Normal', 'travelstore-custom-widgets' ) ) );
		$this->add_color_control( 'tab_text_color', __( 'Text', 'travelstore-custom-widgets' ), '.ts-tabs', '--ts-tab-fg' );
		$this->add_color_control( 'tab_bg_color', __( 'Background', 'travelstore-custom-widgets' ), '.ts-tabs', '--ts-tab-bg' );
		$this->add_color_control( 'tab_border_color', __( 'Border', 'travelstore-custom-widgets' ), '.ts-tabs', '--ts-tab-bd' );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_colors_active', array( 'label' => __( 'Open tab', 'travelstore-custom-widgets' ) ) );
		$this->add_color_control( 'tab_active_text_color', __( 'Text', 'travelstore-custom-widgets' ), '.ts-tabs', '--ts-tab-on-fg' );
		$this->add_color_control( 'tab_active_bg_color', __( 'Background', 'travelstore-custom-widgets' ), '.ts-tabs', '--ts-tab-on-bg' );
		$this->add_color_control( 'tab_active_border_color', __( 'Border', 'travelstore-custom-widgets' ), '.ts-tabs', '--ts-tab-on-bd' );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'tab_colors_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Leave the colours empty to follow "Text colours". The defaults come from Site Settings.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: panel layout ---------------- */
		$this->start_controls_section(
			'ts_section_panel_style',
			array(
				'label' => __( 'Panel layout', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'picture_share',
			array(
				'label'                => __( 'Text : picture widths', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => '1-1',
				'options'              => array(
					'1-1'   => __( 'Half and half', 'travelstore-custom-widgets' ),
					'55-45' => __( 'Text a little wider (55 : 45)', 'travelstore-custom-widgets' ),
					'60-40' => __( 'Text wider (60 : 40)', 'travelstore-custom-widgets' ),
					'45-55' => __( 'Picture a little wider (45 : 55)', 'travelstore-custom-widgets' ),
					'40-60' => __( 'Picture wider (40 : 60)', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'1-1'   => '--ts-tabs-txt:1fr;--ts-tabs-pic:1fr;',
					'55-45' => '--ts-tabs-txt:1.1fr;--ts-tabs-pic:.9fr;',
					'60-40' => '--ts-tabs-txt:1.2fr;--ts-tabs-pic:.8fr;',
					'45-55' => '--ts-tabs-txt:.9fr;--ts-tabs-pic:1.1fr;',
					'40-60' => '--ts-tabs-txt:.8fr;--ts-tabs-pic:1.2fr;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-tabs' => '{{VALUE}}' ),
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
					'side'  => __( 'Side by side', 'travelstore-custom-widgets' ),
					'stack' => __( 'Text and picture stacked', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'side'  => 'grid-template-columns:var(--ts-tabs-cols);--ts-tabs-media-order:initial;',
					'stack' => 'grid-template-columns:minmax(0,1fr);--ts-tabs-media-order:var(--ts-tabs-stack-order,2);',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-tabs__panel' => '{{VALUE}}' ),
			)
		);
		$this->add_control(
			'stack_picture',
			array(
				'label'                => __( 'When stacked, picture goes', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'last',
				'options'              => array(
					'first' => __( 'Above the text', 'travelstore-custom-widgets' ),
					'last'  => __( 'Below the text', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'first' => '--ts-tabs-stack-order:-1;',
					'last'  => '--ts-tabs-stack-order:2;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-tabs' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'panel_gap',
			array(
				'label'          => __( 'Space between text and picture', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'        => array( 'size' => 44, 'unit' => 'px' ),
				'tablet_default' => array( 'size' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .ts-tabs' => '--ts-tabs-panel-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'vertical_align',
			array(
				'label'                => __( 'Vertical alignment', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'center',
				'options'              => array(
					'center'  => __( 'Centred', 'travelstore-custom-widgets' ),
					'start'   => __( 'Top', 'travelstore-custom-widgets' ),
					'stretch' => __( 'Picture as tall as the text', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'center'  => '--ts-tabs-valign:center;',
					'start'   => '--ts-tabs-valign:start;',
					'stretch' => '--ts-tabs-valign:stretch;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-tabs' => '{{VALUE}}' ),
			)
		);
		$this->add_control(
			'picture_style_heading',
			array(
				'label'     => __( 'Picture', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_responsive_control(
			'picture_height',
			array(
				'label'      => __( 'Picture height', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 120, 'max' => 700 ) ),
				'default'    => array( 'size' => 280, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-tabs' => '--ts-tabs-pic-h: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'picture_fit',
			array(
				'label'                => __( 'Object Fit', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'cover',
				'options'              => array(
					'cover'      => __( 'Cover (fill the area, edges may be cropped)', 'travelstore-custom-widgets' ),
					'contain'    => __( 'Contain (show the whole picture)', 'travelstore-custom-widgets' ),
					'fill'       => __( 'Fill (stretch, may distort)', 'travelstore-custom-widgets' ),
					'scale-down' => __( 'Scale Down (never larger than the photo itself)', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'cover'      => '--ts-tabs-fit:cover;',
					'contain'    => '--ts-tabs-fit:contain;',
					'fill'       => '--ts-tabs-fit:fill;',
					'scale-down' => '--ts-tabs-fit:scale-down;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-tabs' => '{{VALUE}}' ),
			)
		);
		$this->add_control(
			'radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-tabs' => '--ts-tabs-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_image_style_controls( '.ts-tabs__img', '.ts-tabs__frame', '.ts-tabs__frame:hover .ts-tabs__img', array( 'fit' => false ) );
		$this->end_controls_section();

		$this->add_image_caption_style_section();

		/* ---------------- Style: panel text ---------------- */
		$this->start_controls_section(
			'ts_section_text_style',
			array(
				'label' => __( 'Panel text', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'kicker_typography', __( 'Caption typography', 'travelstore-custom-widgets' ), '.ts-tabs__kicker' );
		$this->add_color_control( 'kicker_color', __( 'Caption colour', 'travelstore-custom-widgets' ), '.ts-tabs__kicker' );
		$this->add_typography_control( 'title_typography', __( 'Title typography', 'travelstore-custom-widgets' ), '.ts-tabs__title', 'ts_heading' );
		$this->add_color_control( 'title_color', __( 'Title colour', 'travelstore-custom-widgets' ), '.ts-tabs__title' );
		$this->add_typography_control( 'text_typography', __( 'Text typography', 'travelstore-custom-widgets' ), '.ts-tabs__text', 'ts_body' );
		$this->add_color_control( 'text_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-tabs__text' );
		$this->add_responsive_control(
			'text_max_width',
			array(
				'label'      => __( 'Text width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 900 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'size' => 440, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-tabs__text' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'note_heading',
			array(
				'label'     => __( 'Highlight line', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_typography_control( 'note_typography', __( 'Typography', 'travelstore-custom-widgets' ), '.ts-tabs__note' );
		$this->add_color_control( 'note_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-tabs__note' );
		$this->add_color_control( 'note_bg', __( 'Box colour', 'travelstore-custom-widgets' ), '.ts-tabs__note', 'background-color' );
		$this->add_control(
			'note_radius',
			array(
				'label'      => __( 'Box corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-tabs__note' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'text_colors_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Leave the colours empty to follow "Text colours" (Tab buttons). The defaults come from Site Settings.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->end_controls_section();

		$this->add_button_style_section();
		$this->register_extra_css_controls();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = isset( $s['items'] ) && is_array( $s['items'] ) ? array_values( $s['items'] ) : array();

		if ( empty( $items ) ) {
			$this->render_empty( __( 'Add at least one tab to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$light  = isset( $s['text_scheme'] ) && 'light' === $s['text_scheme'];
		$tag    = $this->safe_tag( isset( $s['title_tag'] ) ? $s['title_tag'] : 'h3', 'h3' );
		$side   = isset( $s['picture_side'] ) && 'left' === $s['picture_side'] ? 'left' : 'right';
		$active = isset( $s['active_tab'] ) ? (int) $s['active_tab'] - 1 : 0;
		if ( $active < 0 || $active >= count( $items ) ) {
			$active = 0;
		}
		$uid = 'ts-tabs-' . $this->get_id();

		$classes = array( 'ts-w', 'ts-tabs' );
		if ( $light ) {
			$classes[] = 'ts-tabs--light';
			$classes[] = 'ts-on-dark';
		}

		$bar_label = isset( $s['bar_label'] ) ? trim( (string) $s['bar_label'] ) : '';
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<div class="ts-tabs__bar" role="tablist"<?php echo '' !== $bar_label ? ' aria-label="' . esc_attr( $bar_label ) . '"' : ''; ?>>
				<?php
				foreach ( $items as $i => $item ) {
					$rid = $this->row_id( $item, $i );
					$on  = $i === $active;
					printf(
						'<button type="button" class="ts-tabs__tab" role="tab" id="%1$s-tab-%2$s" aria-controls="%1$s-panel-%2$s" aria-selected="%3$s" tabindex="%4$s">%5$s</button>',
						esc_attr( $uid ),
						esc_attr( $rid ),
						$on ? 'true' : 'false',
						$on ? '0' : '-1',
						esc_html( isset( $item['tab_label'] ) && '' !== trim( (string) $item['tab_label'] ) ? $item['tab_label'] : sprintf( /* translators: %d: tab number */ __( 'Tab %d', 'travelstore-custom-widgets' ), $i + 1 ) )
					);
				}
				?>
			</div>
			<?php
			foreach ( $items as $i => $item ) {
				$rid = $this->row_id( $item, $i );
				?>
				<div class="ts-tabs__panel ts-tabs__panel--pic-<?php echo esc_attr( $side ); ?> elementor-repeater-item-<?php echo esc_attr( $rid ); ?>" role="tabpanel" id="<?php echo esc_attr( $uid . '-panel-' . $rid ); ?>" aria-labelledby="<?php echo esc_attr( $uid . '-tab-' . $rid ); ?>" tabindex="0"<?php echo $i === $active ? '' : ' hidden'; ?>>
					<div class="ts-tabs__copy">
						<?php
						if ( ! empty( $item['kicker'] ) ) {
							echo '<div class="ts-tabs__kicker">' . esc_html( $item['kicker'] ) . '</div>';
						}
						if ( ! empty( $item['title'] ) ) {
							printf( '<%1$s class="ts-tabs__title">%2$s</%1$s>', esc_attr( $tag ), nl2br( esc_html( $item['title'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
						}
						if ( ! empty( $item['text'] ) ) {
							echo '<p class="ts-tabs__text">' . nl2br( esc_html( $item['text'] ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
						}
						if ( ! empty( $item['note'] ) ) {
							echo '<div class="ts-tabs__note">' . esc_html( $item['note'] ) . '</div>';
						}
						if ( ! empty( $item['button_text'] ) ) {
							echo '<div class="ts-tabs__actions">';
							$this->render_button( $item, 'button', '_' . $rid );
							echo '</div>';
						}
						?>
					</div>
					<div class="ts-tabs__media">
						<div class="ts-tabs__frame"><?php $this->render_picture( $item, $rid ); ?></div>
						<?php echo $this->image_caption( $item, 'image' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in image_caption(). ?>
					</div>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}

	private function row_id( $item, $i ) {
		return ! empty( $item['_id'] ) ? sanitize_html_class( $item['_id'] ) : 'i' . $i;
	}

	/**
	 * Photo first, then bundled art; otherwise the colour blend behind shows through.
	 */
	private function render_picture( $item, $rid ) {
		$alt = isset( $item['alt'] ) ? (string) $item['alt'] : '';

		if ( ! empty( $item['image']['id'] ) ) {
			$img = $this->image_tag( $item, 'image', 'large', $alt, array( 'class' => 'ts-tabs__img', 'loading' => 'lazy' ) );
			echo $this->image_linked( $img, $item, 'image', 'ts_tabs_pic_' . $rid ); // phpcs:ignore WordPress.Security.EscapeOutput
			return;
		}

		$url   = '';
		$photo = false;
		if ( ! empty( $item['image']['url'] ) && false === strpos( (string) $item['image']['url'], 'placeholder' ) ) {
			$url   = (string) $item['image']['url'];
			$photo = true;
		} elseif ( ! empty( $item['art'] ) ) {
			$url = (string) \TravelStore\TS_Art::url( $item['art'] );
		}

		if ( $url ) {
			$img = sprintf( '<img class="ts-tabs__img%3$s" src="%1$s" alt="%2$s" loading="lazy" decoding="async" />', esc_url( $url ), esc_attr( $alt ), esc_attr( $this->image_anim_class() ) );
			echo $photo ? $this->image_linked( $img, $item, 'image', 'ts_tabs_pic_' . $rid ) : $img; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
}
