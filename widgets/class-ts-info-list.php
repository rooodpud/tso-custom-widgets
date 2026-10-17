<?php
/**
 * Widget: TS Info List — short facts in four looks.
 *
 *  - Rows:    "Label ........ Value" with thin rules (practical info, key facts).
 *  - Tags:    small pills (what to bring, course facts, car categories).
 *  - Flow:    pills joined by arrows, one can be highlighted (Muscat → Nizwa → Wahiba).
 *  - Bullets: a simple dotted list.
 *  - Details: icon, small label above the value (contact details; 1.7.0). The value can be a
 *             phone, WhatsApp, email or web link.
 *  - Checklist: items the visitor can tick off, with a counter underneath (1.8.0; packing list).
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Info_List extends TS_Base_Widget {

	const MODES = array( 'rows', 'tags', 'flow', 'bullets', 'listing', 'details', 'checklist' );

	public function get_name() {
		return 'ts-info-list';
	}

	public function get_title() {
		return __( 'TS Info List', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	public function get_keywords() {
		return array( 'list', 'facts', 'tags', 'chips', 'flow', 'journey', 'bullets', 'practical', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Items', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'mode',
			array(
				'label'   => __( 'Look', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rows',
				'options' => array(
					'rows'    => __( 'Rows: label and value', 'travelstore-custom-widgets' ),
					'tags'    => __( 'Tags (small pills)', 'travelstore-custom-widgets' ),
					'flow'    => __( 'Flow (pills joined by arrows)', 'travelstore-custom-widgets' ),
					'bullets' => __( 'Bullet list', 'travelstore-custom-widgets' ),
					'listing' => __( 'Listing: title and sentence, pill on the right', 'travelstore-custom-widgets' ),
					'details' => __( 'Details: icon, small label, value underneath (contact details)', 'travelstore-custom-widgets' ),
					'checklist' => __( 'Checklist: visitors can tick items off', 'travelstore-custom-widgets' ),
				),
			)
		);

		$r = new Repeater();
		$r->add_control(
			'icon',
			array(
				'label'       => __( 'Icon (Details look)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
			)
		);
		$r->add_control(
			'label',
			array(
				'label'       => __( 'Text', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'value',
			array(
				'label'       => __( 'Value', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => __( 'Rows: shown on the right. Tags, bullets and checklist: shown in bold after the text. Listing: the pill on the right. Details: under the label (a new line here starts a new line on the page). Optional.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'description',
			array(
				'label'       => __( 'Sentence (Listing look)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => __( 'Shown under the text in the Listing look. Optional.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_contact_link_controls( $r, 'link', __( 'Leave empty to use the Value.', 'travelstore-custom-widgets' ) );
		$r->add_control(
			'link',
			array(
				'label'       => __( 'Link (Tags, Listing and Details looks)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'Type a page name or paste a link', 'travelstore-custom-widgets' ),
				'description' => __( 'Tags: makes the pill a link, for example to a part of this page (#golf) or another page. Listing: makes the pill a link, for example "See Nizwa →". Details: makes the value a link. Optional.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
				'condition'   => array( 'link_kind' => 'url' ),
			)
		);
		$r->add_control(
			'highlight',
			array(
				'label'        => __( 'Highlight this item', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'For tags and flows: shows this pill in the highlight colour (for example "you are here").', 'travelstore-custom-widgets' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Items', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array(
						'label' => __( 'Best time to go', 'travelstore-custom-widgets' ),
						'value' => __( 'October – March', 'travelstore-custom-widgets' ),
					),
					array(
						'label' => __( 'From Muscat', 'travelstore-custom-widgets' ),
						'value' => __( 'About 2 hours', 'travelstore-custom-widgets' ),
					),
					array(
						'label' => __( 'Good for', 'travelstore-custom-widgets' ),
						'value' => __( 'Families, first visits', 'travelstore-custom-widgets' ),
					),
				),
			)
		);
		$this->add_control(
			'arrow',
			array(
				'label'     => __( 'Arrow between pills', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '→',
				'condition' => array( 'mode' => 'flow' ),
			)
		);
		$this->add_tag_control( 'title_tag', __( 'Title level (Listing look)', 'travelstore-custom-widgets' ), 'h3', array( 'condition' => array( 'mode' => 'listing' ) ) );
		$this->add_control(
			'bullet_look',
			array(
				'label'     => __( 'Bullet look', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'dot',
				'options'   => array(
					'dot'  => __( 'Dot', 'travelstore-custom-widgets' ),
					'tick' => __( 'Tick', 'travelstore-custom-widgets' ),
				),
				'condition' => array( 'mode' => 'bullets' ),
			)
		);
		$this->add_control(
			'check_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Visitors tick items off as they go. The ticks are not saved: the list starts empty again when the page is reloaded.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'mode' => 'checklist' ),
			)
		);
		$this->add_control(
			'check_counter',
			array(
				'label'        => __( 'Show a counter under the list', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'For example "3 of 11 packed". The two numbers are filled in automatically.', 'travelstore-custom-widgets' ),
				'condition'    => array( 'mode' => 'checklist' ),
			)
		);
		$this->add_control(
			'check_middle',
			array(
				'label'     => __( 'Counter: word between the two numbers', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'of', 'travelstore-custom-widgets' ),
				'condition' => array( 'mode' => 'checklist', 'check_counter' => 'yes' ),
			)
		);
		$this->add_control(
			'check_word',
			array(
				'label'     => __( 'Counter: word after the numbers', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'ticked', 'travelstore-custom-widgets' ),
				'condition' => array( 'mode' => 'checklist', 'check_counter' => 'yes' ),
			)
		);
		$this->add_control(
			'check_done',
			array(
				'label'       => __( 'Message when everything is ticked', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'All done!', 'travelstore-custom-widgets' ),
				'description' => __( 'Shown in place of the counter. Leave empty to keep the counter.', 'travelstore-custom-widgets' ),
				'condition'   => array( 'mode' => 'checklist', 'check_counter' => 'yes' ),
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
			'max_width',
			array(
				'label'      => __( 'Width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 160, 'max' => 1200 ),
					'%'  => array( 'min' => 10, 'max' => 100 ),
				),
				'selectors'  => array( '{{WRAPPER}} .ts-info' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'spacing',
			array(
				'label'       => __( 'Spacing', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'description' => __( 'Rows and checklist: space above and below each row. Tags, flow and bullets: space between items.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-info' => '--ts-info-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'alignment',
			array(
				'label'                => __( 'Alignment (tags, flow)', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'   => array( 'title' => __( 'Left', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-left' ),
					'center' => array( 'title' => __( 'Center', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-center' ),
				),
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-info--tags .ts-info__list, {{WRAPPER}} .ts-info--flow .ts-info__list' => 'justify-content: {{VALUE}};' ),
				'condition'            => array( 'mode' => array( 'tags', 'flow' ) ),
			)
		);
		$this->add_control(
			'line_color',
			array(
				'label'     => __( 'Rule colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-info' => '--ts-info-line: {{VALUE}};' ),
				'condition' => array( 'mode' => array( 'rows', 'listing', 'details', 'checklist' ) ),
			)
		);
		$this->add_control(
			'check_heading',
			array(
				'label'     => __( 'Checklist', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'mode' => 'checklist' ),
			)
		);
		$this->add_control(
			'check_frame',
			array(
				'label'        => __( 'Box around the list', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'mode' => 'checklist' ),
			)
		);
		$this->add_color_control( 'check_frame_bg', __( 'Box colour', 'travelstore-custom-widgets' ), '.ts-info--framed .ts-info__list', 'background-color', '', array( 'condition' => array( 'mode' => 'checklist', 'check_frame' => 'yes' ) ) );
		$this->add_color_control( 'check_frame_border', __( 'Box border colour', 'travelstore-custom-widgets' ), '.ts-info--framed .ts-info__list', 'border-color', '', array( 'condition' => array( 'mode' => 'checklist', 'check_frame' => 'yes' ) ) );
		$this->add_responsive_control(
			'check_frame_radius',
			array(
				'label'      => __( 'Box corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-info--framed .ts-info__list' => 'border-radius: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'mode' => 'checklist', 'check_frame' => 'yes' ),
			)
		);
		$this->add_control(
			'check_tick',
			array(
				'label'     => __( 'Ticked box colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => 'globals/colors?id=ts_oasis' ),
				'selectors' => array( '{{WRAPPER}} .ts-check__box' => 'accent-color: {{VALUE}};' ),
				'condition' => array( 'mode' => 'checklist' ),
			)
		);
		$this->add_responsive_control(
			'check_size',
			array(
				'label'      => __( 'Tick box size', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 14, 'max' => 32 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-check__box' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'mode' => 'checklist' ),
			)
		);
		$this->add_control(
			'check_strike',
			array(
				'label'        => __( 'Cross out ticked items', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'mode' => 'checklist' ),
			)
		);
		$this->add_typography_control( 'check_count_typography', __( 'Counter typography', 'travelstore-custom-widgets' ), '.ts-check__count', '', array( 'condition' => array( 'mode' => 'checklist', 'check_counter' => 'yes' ) ) );
		$this->add_color_control( 'check_count_color', __( 'Counter colour', 'travelstore-custom-widgets' ), '.ts-check__count', 'color', '', array( 'condition' => array( 'mode' => 'checklist', 'check_counter' => 'yes' ) ) );
		$this->end_controls_section();

		/* ---------------- Style: icons (Details look) ---------------- */
		$this->start_controls_section(
			'ts_section_icons',
			array(
				'label'     => __( 'Icons', 'travelstore-custom-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'mode' => 'details' ),
			)
		);
		$this->add_color_control( 'icon_color', __( 'Icon colour', 'travelstore-custom-widgets' ), '.ts-info__icon', 'color' );
		$this->add_color_control( 'icon_tile', __( 'Tile colour', 'travelstore-custom-widgets' ), '.ts-info__icon', 'background-color' );
		$this->add_responsive_control(
			'icon_tile_size',
			array(
				'label'      => __( 'Tile size', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-info__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon size', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 48 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-info__icon' => 'font-size: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'icon_radius',
			array(
				'label'      => __( 'Tile corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-info__icon' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'icon_space',
			array(
				'label'      => __( 'Space beside the icon', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-info__detail' => 'gap: {{SIZE}}{{UNIT}};' ),
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
		$this->add_typography_control( 'label_typography', __( 'Text typography', 'travelstore-custom-widgets' ), '.ts-info__label' );
		$this->add_color_control( 'label_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-info__label' );
		$this->add_typography_control( 'value_typography', __( 'Value typography', 'travelstore-custom-widgets' ), '.ts-info__value' );
		$this->add_color_control( 'value_color', __( 'Value colour', 'travelstore-custom-widgets' ), '.ts-info__value' );
		$this->add_color_control( 'bullet_color', __( 'Bullet colour', 'travelstore-custom-widgets' ), '.ts-info__item::before', 'background-color' );
		$this->add_color_control( 'tick_color', __( 'Tick colour', 'travelstore-custom-widgets' ), '.ts-info-wrap--tick .ts-info__item::before', 'color', '', array( 'condition' => array( 'mode' => 'bullets', 'bullet_look' => 'tick' ) ) );
		$this->add_control(
			'listing_heading',
			array(
				'label'     => __( 'Listing look', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'mode' => 'listing' ),
			)
		);
		$this->add_typography_control( 'title_typography', __( 'Title typography', 'travelstore-custom-widgets' ), '.ts-info__title', 'ts_heading', array( 'condition' => array( 'mode' => 'listing' ) ) );
		$this->add_color_control( 'title_color', __( 'Title colour', 'travelstore-custom-widgets' ), '.ts-info__title', 'color', '', array( 'condition' => array( 'mode' => 'listing' ) ) );
		$this->add_typography_control( 'desc_typography', __( 'Sentence typography', 'travelstore-custom-widgets' ), '.ts-info__desc', 'ts_body', array( 'condition' => array( 'mode' => 'listing' ) ) );
		$this->add_color_control( 'desc_color', __( 'Sentence colour', 'travelstore-custom-widgets' ), '.ts-info__desc', 'color', '', array( 'condition' => array( 'mode' => 'listing' ) ) );
		$this->add_responsive_control(
			'desc_max_width',
			array(
				'label'      => __( 'Sentence width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 1000 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'size' => 620, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-info__desc' => 'max-width: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'mode' => 'listing' ),
			)
		);
		$this->add_control(
			'colors_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Leave the colours empty to follow "Text colours" (Layout). The defaults come from Site Settings.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: pills ---------------- */
		$this->start_controls_section(
			'ts_section_pills',
			array(
				'label'     => __( 'Pills', 'travelstore-custom-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'mode' => array( 'tags', 'flow', 'listing' ) ),
			)
		);
		$this->add_color_control( 'link_pill_text', __( 'Link pill text colour', 'travelstore-custom-widgets' ), '.ts-info__pill.is-link', 'color', 'ts_terracotta_dark' );
		$this->add_color_control( 'link_pill_border', __( 'Link pill border colour', 'travelstore-custom-widgets' ), '.ts-info__pill.is-link', 'border-color' );
		$this->add_color_control( 'pill_bg', __( 'Pill colour', 'travelstore-custom-widgets' ), '.ts-info__pill', 'background-color' );
		$this->add_color_control( 'pill_border', __( 'Pill border colour', 'travelstore-custom-widgets' ), '.ts-info__pill', 'border-color' );
		$this->add_color_control( 'pill_text', __( 'Pill text colour', 'travelstore-custom-widgets' ), '.ts-info__pill' );
		$this->add_color_control( 'highlight_bg', __( 'Highlighted pill colour', 'travelstore-custom-widgets' ), '.ts-info__pill.is-highlight', 'background-color' );
		$this->add_color_control( 'highlight_text', __( 'Highlighted pill text', 'travelstore-custom-widgets' ), '.ts-info__pill.is-highlight' );
		$this->add_color_control( 'arrow_color', __( 'Arrow colour', 'travelstore-custom-widgets' ), '.ts-info__arrow' );
		$this->add_responsive_control(
			'pill_padding',
			array(
				'label'      => __( 'Pill padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-info__pill' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = array();
		foreach ( isset( $s['items'] ) && is_array( $s['items'] ) ? $s['items'] : array() as $item ) {
			$label = isset( $item['label'] ) ? trim( (string) $item['label'] ) : '';
			$value = isset( $item['value'] ) ? trim( (string) $item['value'] ) : '';
			if ( '' !== $label || '' !== $value ) {
				$items[] = array(
					'label'       => $label,
					'value'       => $value,
					'highlight'   => ! empty( $item['highlight'] ),
					'description' => isset( $item['description'] ) ? trim( (string) $item['description'] ) : '',
					'link'        => $this->contact_link( $item, 'link', $value ),
					'icon'        => isset( $item['icon'] ) && is_array( $item['icon'] ) ? $item['icon'] : array(),
					'_id'         => isset( $item['_id'] ) ? (string) $item['_id'] : '',
				);
			}
		}

		if ( empty( $items ) ) {
			$this->render_empty( __( 'Add at least one item to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$mode  = isset( $s['mode'] ) && in_array( $s['mode'], self::MODES, true ) ? $s['mode'] : 'rows';
		$light = isset( $s['text_scheme'] ) && 'light' === $s['text_scheme'];

		$tick = 'bullets' === $mode && isset( $s['bullet_look'] ) && 'tick' === $s['bullet_look'];
		echo '<div class="ts-w ts-info-wrap' . ( $light ? ' ts-on-dark' : '' ) . ( $tick ? ' ts-info-wrap--tick' : '' ) . '">';
		if ( 'listing' === $mode ) {
			$this->print_listing( $items, $light, $this->safe_tag( isset( $s['title_tag'] ) ? $s['title_tag'] : 'h3', 'h3' ) );
		} elseif ( 'details' === $mode ) {
			$this->print_details( $items, $light );
		} elseif ( 'checklist' === $mode ) {
			$this->print_checklist( $items, $light, $s );
		} else {
			self::print_list( $items, $mode, $light, isset( $s['arrow'] ) ? (string) $s['arrow'] : '→' );
		}
		echo '</div>';
	}

	/**
	 * The checklist script loads only where the Checklist look is used (and always in the editor preview).
	 */
	public function get_script_depends() {
		$plugin = \Elementor\Plugin::$instance;
		if ( ( $plugin->editor && $plugin->editor->is_edit_mode() ) || ( $plugin->preview && $plugin->preview->is_preview_mode() ) ) {
			return array( 'ts-checklist' );
		}

		return 'checklist' === $this->get_settings( 'mode' ) ? array( 'ts-checklist' ) : array();
	}

	/**
	 * Checklist look: a tick box per item and a counter underneath ("0 of 11 packed").
	 */
	private function print_checklist( $items, $light, $s ) {
		$classes = 'ts-info ts-info--checklist' . ( $light ? ' ts-info--light' : '' );
		if ( ! isset( $s['check_frame'] ) || 'yes' === $s['check_frame'] ) {
			$classes .= ' ts-info--framed';
		}
		if ( ! isset( $s['check_strike'] ) || 'yes' === $s['check_strike'] ) {
			$classes .= ' ts-info--strike';
		}
		echo '<div class="' . esc_attr( $classes ) . '"><ul class="ts-info__list">';
		foreach ( $items as $item ) {
			$text = '<span class="ts-info__label">' . esc_html( $item['label'] ) . '</span>';
			if ( '' !== $item['value'] ) {
				$text .= ' <b class="ts-info__value">' . esc_html( $item['value'] ) . '</b>';
			}
			echo '<li class="ts-info__item"><label class="ts-check"><input type="checkbox" class="ts-check__box"><span class="ts-check__text">' . $text . '</span></label></li>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</ul>';
		if ( ! isset( $s['check_counter'] ) || 'yes' === $s['check_counter'] ) {
			$middle = isset( $s['check_middle'] ) ? trim( (string) $s['check_middle'] ) : '';
			$word   = isset( $s['check_word'] ) ? trim( (string) $s['check_word'] ) : '';
			$done   = isset( $s['check_done'] ) ? trim( (string) $s['check_done'] ) : '';
			$start  = trim( preg_replace( '/\s+/', ' ', '0 ' . $middle . ' ' . count( $items ) . ' ' . $word ) );
			printf(
				'<p class="ts-check__count" aria-live="polite" data-middle="%1$s" data-word="%2$s" data-done="%3$s">%4$s</p>',
				esc_attr( $middle ),
				esc_attr( $word ),
				esc_attr( $done ),
				esc_html( $start )
			);
		}
		echo '</div>';
	}

	/**
	 * Listing look: title + sentence on the left, a pill (optionally a link) on the right.
	 */
	private function print_listing( $items, $light, $tag ) {
		echo '<ul class="ts-info ts-info--listing' . ( $light ? ' ts-info--light' : '' ) . '">';
		foreach ( $items as $i => $item ) {
			echo '<li class="ts-info__entry">';
			echo '<div class="ts-info__main">';
			if ( '' !== $item['label'] ) {
				echo '<' . esc_attr( $tag ) . ' class="ts-info__title">' . esc_html( $item['label'] ) . '</' . esc_attr( $tag ) . '>';
			}
			if ( '' !== $item['description'] ) {
				echo '<p class="ts-info__desc">' . nl2br( esc_html( $item['description'] ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			echo '</div>';
			if ( '' !== $item['value'] ) {
				if ( ! empty( $item['link']['url'] ) ) {
					$key = 'ts_info_link_' . ( '' !== $item['_id'] ? sanitize_html_class( $item['_id'] ) : $i );
					$this->add_link_attributes( $key, $item['link'] );
					$this->add_render_attribute( $key, 'class', array( 'ts-info__pill', 'ts-info__value', 'is-link' ) );
					echo '<a ' . $this->get_render_attribute_string( $key ) . '>' . esc_html( $item['value'] ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
				} else {
					echo '<span class="ts-info__pill ts-info__value">' . esc_html( $item['value'] ) . '</span>';
				}
			}
			echo '</li>';
		}
		echo '</ul>';
	}

	/**
	 * Details look: icon tile, small label, value underneath (a link when one is set).
	 */
	private function print_details( $items, $light ) {
		echo '<ul class="ts-info ts-info--details' . ( $light ? ' ts-info--light' : '' ) . '">';
		foreach ( $items as $i => $item ) {
			$has_icon = ! empty( $item['icon']['value'] );
			echo '<li class="ts-info__detail">';
			if ( $has_icon ) {
				echo '<span class="ts-info__icon" aria-hidden="true">';
				\Elementor\Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) );
				echo '</span>';
			}
			echo '<div class="ts-info__main">';
			if ( '' !== $item['label'] ) {
				echo '<div class="ts-info__label">' . esc_html( $item['label'] ) . '</div>';
			}
			if ( '' !== $item['value'] ) {
				$value = nl2br( esc_html( $item['value'] ) );
				if ( '' !== $item['link']['url'] ) {
					$key = 'ts_info_link_' . ( '' !== $item['_id'] ? sanitize_html_class( $item['_id'] ) : $i );
					$this->add_link_attributes( $key, $item['link'] );
					$this->add_render_attribute( $key, 'class', array( 'ts-info__value', 'is-link' ) );
					echo '<div class="ts-info__value-wrap"><a ' . $this->get_render_attribute_string( $key ) . '>' . $value . '</a></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
				} else {
					echo '<div class="ts-info__value">' . $value . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
			}
			echo '</div></li>';
		}
		echo '</ul>';
	}

	/**
	 * Shared markup, also used by TS Split Feature.
	 *
	 * @param array  $items array( array( 'label' => , 'value' => , 'highlight' => bool ), ... ).
	 * @param string $mode  rows | tags | flow | bullets.
	 */
	public static function print_list( $items, $mode, $light = false, $arrow = '→' ) {
		$classes = 'ts-info ts-info--' . $mode . ( $light ? ' ts-info--light' : '' );

		if ( 'rows' === $mode ) {
			echo '<dl class="' . esc_attr( $classes ) . '">';
			foreach ( $items as $item ) {
				echo '<div class="ts-info__row">';
				echo '<dt class="ts-info__label">' . esc_html( $item['label'] ) . '</dt>';
				echo '<dd class="ts-info__value">' . esc_html( $item['value'] ) . '</dd>';
				echo '</div>';
			}
			echo '</dl>';
			return;
		}

		$tag   = 'flow' === $mode ? 'ol' : 'ul';
		$count = count( $items );
		echo '<div class="' . esc_attr( $classes ) . '"><' . $tag . ' class="ts-info__list">'; // phpcs:ignore WordPress.Security.EscapeOutput
		foreach ( $items as $i => $item ) {
			$text = '<span class="ts-info__label">' . esc_html( $item['label'] ) . '</span>';
			if ( '' !== $item['value'] ) {
				$text .= ' <b class="ts-info__value">' . esc_html( $item['value'] ) . '</b>';
			}

			if ( 'bullets' === $mode ) {
				echo '<li class="ts-info__item">' . $text . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
				continue;
			}

			$pill = '<span class="ts-info__pill' . ( $item['highlight'] ? ' is-highlight' : '' ) . '">' . $text . '</span>';
			// 1.11.0: in the Tags look a pill with a link is a link (quick links at the top of a page).
			// This method is static (also called by TS Split Feature, whose tags have no link): no $this, and a missing link is fine.
			if ( 'tags' === $mode && ! empty( $item['link']['url'] ) ) {
				$link  = $item['link'];
				$attrs = ' class="ts-info__pill is-link' . ( ! empty( $item['highlight'] ) ? ' is-highlight' : '' ) . '" href="' . esc_url( $link['url'] ) . '"';
				if ( ! empty( $link['is_external'] ) ) {
					$attrs .= ' target="_blank"';
				}
				$rel = array();
				if ( ! empty( $link['is_external'] ) ) {
					$rel[] = 'noopener';
				}
				if ( ! empty( $link['nofollow'] ) ) {
					$rel[] = 'nofollow';
				}
				if ( $rel ) {
					$attrs .= ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"';
				}
				$pill = '<a' . $attrs . '>' . $text . '</a>';
			}
			$sep  = ( 'flow' === $mode && $i < $count - 1 && '' !== trim( $arrow ) ) ? '<span class="ts-info__arrow" aria-hidden="true">' . esc_html( $arrow ) . '</span>' : '';
			echo '<li class="ts-info__item">' . $pill . $sep . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</' . $tag . '></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
