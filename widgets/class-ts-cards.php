<?php
/**
 * Widget: TS Cards — a responsive grid of content cards.
 *
 * One widget for every "cards" pattern in the page drafts: icon cards, numbered
 * or time-labelled cards, and info cards with a label, chips, facts and a note.
 * Each card has optional fields; empty fields simply do not appear.
 * 1.8.0: a live clock in place of a card's title, tick / cross bullet points, a per-card title colour.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Cards extends TS_Base_Widget {

	public function get_name() {
		return 'ts-cards';
	}

	public function get_title() {
		return __( 'TS Cards', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_keywords() {
		return array( 'cards', 'grid', 'icons', 'features', 'reasons', 'tiers', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Cards', 'travelstore-custom-widgets' ) )
		);

		$r = new Repeater();
		$r->add_control(
			'icon',
			array(
				'label'       => __( 'Icon', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
			)
		);
		$r->add_control(
			'tone',
			array(
				'label'       => __( 'Icon colour set', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'gold',
				'options'     => self::tone_options( true ),
				'description' => __( 'Colours the icon and its tile together. Not used when this card’s own "Icon colour" or "Icon tile colour" (under "Colours for this card") or the widget’s "Icon colour (all cards)" / "Tile colour (all cards)" are set.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'rating',
			array(
				'label'       => __( 'Rating (bars)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => self::rating_options(),
				'description' => __( 'Optional. How many bars are filled on this card, for example an effort level. The number of bars and the level names are set under "Rating bars".', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'marker',
			array(
				'label'       => __( 'Number or time label', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'For example 01 or 06:00. Optional.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
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
				'label'       => __( 'Title', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'clock',
			array(
				'label'       => __( 'Live clock in place of the title', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => self::clock_options(),
				'description' => __( 'Shows the time right now in that place and keeps it up to date while the page is open. The Title above is shown only if the clock cannot run.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'clock_format',
			array(
				'label'     => __( 'Clock format', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h24',
				'options'   => array(
					'h24' => __( '24-hour (17:05)', 'travelstore-custom-widgets' ),
					'h12' => __( '12-hour (5:05 pm)', 'travelstore-custom-widgets' ),
				),
				'condition' => array( 'clock!' => 'none' ),
			)
		);
		$r->add_control(
			'subtitle',
			array(
				'label'       => __( 'Line under the title (bold)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'For example a phone number or email address. Optional.', 'travelstore-custom-widgets' ),
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
			'bullets',
			array(
				'label'       => __( 'Bullet points', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => __( 'One per line. Shown as a bulleted list under the text. Optional.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'bullet_look',
			array(
				'label'       => __( 'Bullet look', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'dot',
				'options'     => array(
					'dot'   => __( 'Dot', 'travelstore-custom-widgets' ),
					'small' => __( 'Small dot, no indent', 'travelstore-custom-widgets' ), // 1.9.2: Musandam panels.
					'tick'  => __( 'Tick (green)', 'travelstore-custom-widgets' ),
					'cross' => __( 'Cross (orange)', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'For example ticks on a "Do" card and crosses on a "Don’t" card.', 'travelstore-custom-widgets' ),
				'condition'   => array( 'bullets!' => '' ),
			)
		);
		$r->add_control(
			'chips',
			array(
				'label'       => __( 'Tags', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => __( 'One per line. Shown as small pills. Optional.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'facts',
			array(
				'label'       => __( 'Key facts', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => __( 'One per line as Label | Value, for example: Stay | 1 night. Optional.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'meta',
			array(
				'label'       => __( 'Bottom note', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'A short line at the bottom of the card. Optional.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		// 1.10.0 (Destinations page): a bottom note that can hold links, and a list of stops.
		$r->add_control(
			'meta_rich',
			array(
				'label'       => __( 'Bottom note with links (replaces the plain note)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::WYSIWYG,
				'description' => __( 'Optional. Use the link button for links.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'stops_heading',
			array(
				'label'       => __( 'Stops (a list joined by a dotted line, optional)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::HEADING,
				'separator'   => 'before',
			)
		);
		for ( $n = 1; $n <= 6; $n++ ) {
			$r->add_control(
				'stop' . $n . '_name',
				array(
					/* translators: %d: stop number. */
					'label' => sprintf( __( 'Stop %d – name', 'travelstore-custom-widgets' ), $n ),
					'type'  => Controls_Manager::TEXT,
				)
			);
			$r->add_control(
				'stop' . $n . '_note',
				array(
					/* translators: %d: stop number. */
					'label'       => sprintf( __( 'Stop %d – small line under it', 'travelstore-custom-widgets' ), $n ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => __( 'For example: 1–2 nights', 'travelstore-custom-widgets' ),
				)
			);
			$r->add_control(
				'stop' . $n . '_air',
				array(
					/* translators: %d: stop number. */
					'label'        => sprintf( __( 'Stop %d – reached by air (blue marker)', 'travelstore-custom-widgets' ), $n ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);
		}
		$r->add_control(
			'link_text',
			array(
				'label'       => __( 'Link text', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Can be left empty when "Make the whole card clickable" is on (Content > Cards, under the list).', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_contact_link_controls( $r, 'link', __( 'Leave empty to use the "Line under the title".', 'travelstore-custom-widgets' ) );
		$r->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'Type a page name or paste a link', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
				'condition'   => array( 'link_kind' => 'url' ),
			)
		);
		$r->add_control(
			'dark',
			array(
				'label'        => __( 'Highlight this card (dark)', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$r->add_control(
			'accent',
			array(
				'label'        => __( 'Highlight this card (coloured border)', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'For example the plan you recommend. Colour: Style > Card box > Highlight border colour.', 'travelstore-custom-widgets' ),
			)
		);
		// Per-card colours (optional). The selector includes the grid so a card's own colour beats the widget-wide ones.
		$r->add_control(
			'colors_heading',
			array(
				'label'     => __( 'Colours for this card (optional)', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$r->add_control(
			'bg_a',
			array(
				'label'     => __( 'Card colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-cards__grid {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}; --ts-card-a: {{VALUE}};' ),
			)
		);
		$r->add_control(
			'bg_b',
			array(
				'label'       => __( 'Card colour blends to', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'Set a second colour for a colour blend.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-cards__grid {{CURRENT_ITEM}}' => 'background-image: linear-gradient(150deg, var(--ts-card-a, transparent), {{VALUE}});' ),
			)
		);
		$r->add_control(
			'border_c',
			array(
				'label'       => __( 'Border colour', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'With the "Coloured bar on top" look this is the bar colour.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-cards__grid {{CURRENT_ITEM}}' => 'border-color: {{VALUE}};' ),
			)
		);
		$r->add_control(
			'title_c',
			array(
				'label'     => __( 'Title colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-cards__grid {{CURRENT_ITEM}} .ts-card__title' => 'color: {{VALUE}};' ),
			)
		);
		$r->add_control(
			'kicker_c',
			array(
				'label'     => __( 'Caption colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-cards__grid {{CURRENT_ITEM}} .ts-card__kicker' => 'color: {{VALUE}};' ),
			)
		);
		$r->add_control(
			'icon_c',
			array(
				'label'       => __( 'Icon colour (replaces the colour set)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'Clear it to go back to "Icon colour set".', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-cards__grid {{CURRENT_ITEM}} .ts-card__icon' => 'color: {{VALUE}};' ),
			)
		);
		$r->add_control(
			'icon_bg_c',
			array(
				'label'     => __( 'Icon tile colour (replaces the colour set)', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-cards__grid {{CURRENT_ITEM}} .ts-card__icon' => 'background-color: {{VALUE}};' ),
			)
		);
		$r->add_control(
			'light',
			array(
				'label'        => __( 'Light text (for a dark card colour)', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Cards', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'tone'  => 'gold',
						'title' => __( 'First card', 'travelstore-custom-widgets' ),
						'text'  => __( 'A short description that explains this card.', 'travelstore-custom-widgets' ),
					),
					array(
						'tone'  => 'terracotta',
						'title' => __( 'Second card', 'travelstore-custom-widgets' ),
						'text'  => __( 'A short description that explains this card.', 'travelstore-custom-widgets' ),
					),
					array(
						'tone'  => 'oasis',
						'title' => __( 'Third card', 'travelstore-custom-widgets' ),
						'text'  => __( 'A short description that explains this card.', 'travelstore-custom-widgets' ),
					),
				),
			)
		);
		$this->add_tag_control( 'title_tag', __( 'Title level', 'travelstore-custom-widgets' ), 'h3' );
		$this->add_control(
			'link_whole',
			array(
				'label'        => __( 'Make the whole card clickable', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'A click anywhere on a card opens that card’s link (set in each card under "Link goes to").', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'link_hover',
			array(
				'label'     => __( 'When the pointer is over a card', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'lift',
				'options'   => array(
					'lift' => __( 'Lift it slightly, with a soft shadow', 'travelstore-custom-widgets' ),
					'none' => __( 'No effect', 'travelstore-custom-widgets' ),
				),
				// 1.9.0: only cards that have a link lift, so a card without a link never looks clickable.
				'description' => __( 'Only cards that have a link react; cards without a link stay still.', 'travelstore-custom-widgets' ),
				'condition' => array( 'link_whole' => 'yes' ),
			)
		);
		$this->add_control(
			'marker_style',
			array(
				'label'   => __( 'Number / time label look', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dot',
				'options' => array(
					'dot'    => __( 'Orange with a dot (times)', 'travelstore-custom-widgets' ),
					'number' => __( 'Small spaced caps (numbers)', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'facts_look',
			array(
				'label'       => __( 'Key facts look', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'inline',
				'options'     => array(
					'inline'    => __( 'Label then bold value, at the bottom of the card', 'travelstore-custom-widgets' ),
					'timetable' => __( 'Timetable: coloured label in a column, text beside it', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'Timetable suits plans, for example: Morning | Grand Mosque at opening time.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'icon_position',
			array(
				'label'   => __( 'Icon position', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'above',
				'options' => array(
					'above'  => __( 'Above the text', 'travelstore-custom-widgets' ),
					'beside' => __( 'Beside the text', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'icon_valign',
			array(
				'label'                => __( 'Icon beside the text – up / down', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'top'    => array( 'title' => __( 'Level with the top of the text', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-top' ),
					'middle' => array( 'title' => __( 'Level with the middle of the text', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-middle' ),
				),
				'selectors_dictionary' => array(
					'top'    => 'flex-start',
					'middle' => 'center',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-cards' => '--ts-card-iv: {{VALUE}};' ),
				'condition'            => array( 'icon_position' => 'beside' ),
			)
		);
		$this->add_control(
			'bullet_lines',
			array(
				'label'        => __( 'Thin lines between bullet points', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->end_controls_section();

		/* ---------------- Content: rating bars ---------------- */
		$this->start_controls_section(
			'ts_rating',
			array( 'label' => __( 'Rating bars', 'travelstore-custom-widgets' ) )
		);
		$this->add_rating_content_controls();
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
				'default'        => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ),
				'selectors'      => array( '{{WRAPPER}} .ts-cards__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ),
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
				'selectors'  => array( '{{WRAPPER}} .ts-cards__grid' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'card_style',
			array(
				'label'   => __( 'Card look', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'filled',
				'options' => array(
					'filled'   => __( 'Filled (sand)', 'travelstore-custom-widgets' ),
					'outlined' => __( 'Outlined (cream)', 'travelstore-custom-widgets' ),
					'plain'    => __( 'Plain (no box)', 'travelstore-custom-widgets' ),
					'topbar'   => __( 'Plain with a coloured bar on top', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_responsive_control(
			'bar_width',
			array(
				'label'      => __( 'Bar thickness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 16 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-cards--topbar .ts-card' => 'border-top-width: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'card_style' => 'topbar' ),
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
			'dividers',
			array(
				'label'                => __( 'Lines between cards', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => '',
				'options'              => array(
					''        => __( 'Default (as on larger screens)', 'travelstore-custom-widgets' ),
					'none'    => __( 'None', 'travelstore-custom-widgets' ),
					'columns' => __( 'Between cards side by side', 'travelstore-custom-widgets' ),
					'rows'    => __( 'Between cards one under another', 'travelstore-custom-widgets' ),
				),
				'description'          => __( 'Suits the "Plain" look with no space between cards. Choose per device: side by side on desktop, one under another on phones.', 'travelstore-custom-widgets' ),
				'selectors_dictionary' => array(
					'none'    => '--ts-cdiv-l:0px;--ts-cdiv-t:0px;',
					'columns' => '--ts-cdiv-l:1px;--ts-cdiv-t:0px;',
					'rows'    => '--ts-cdiv-l:0px;--ts-cdiv-t:1px;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-cards' => '{{VALUE}}' ),
			)
		);
		$this->add_control(
			'divider_color',
			array(
				'label'     => __( 'Line colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-cards' => '--ts-cdiv-c: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'equal_height',
			array(
				'label'        => __( 'Same height in a row', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_responsive_control(
			'text_align',
			array(
				'label'     => __( 'Text alignment', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array( 'title' => __( 'Left', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-left' ),
					'center' => array( 'title' => __( 'Center', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-center' ),
				),
				'selectors' => array( '{{WRAPPER}} .ts-card' => 'text-align: {{VALUE}};' ),
			)
		);
		$this->add_color_control( 'card_bg', __( 'Card background', 'travelstore-custom-widgets' ), '.ts-card', 'background-color' );
		// Skips highlighted cards, so "Highlight border colour" (a Site Settings default, written with a weaker selector) still shows.
		$this->add_color_control( 'card_border', __( 'Card border colour', 'travelstore-custom-widgets' ), '.ts-card:not(.ts-card--accent)', 'border-color' );
		$this->add_color_control( 'accent_border', __( 'Highlight border colour', 'travelstore-custom-widgets' ), '.ts-card.ts-card--accent', 'border-color', 'ts_terracotta' );
		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Card corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-card' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Card padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: icon ---------------- */
		$this->start_controls_section(
			'ts_section_icon',
			array(
				'label' => __( 'Icon', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'icon_tile',
			array(
				'label'      => __( 'Tile size', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-card__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon size', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 64 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-card__icon' => 'font-size: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'icon_radius',
			array(
				'label'      => __( 'Tile corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ), '%' => array( 'min' => 0, 'max' => 50 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-card__icon' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'icon_gap',
			array(
				'label'      => __( 'Space beside the icon', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-cards--icon-beside .ts-card' => 'column-gap: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'icon_position' => 'beside' ),
			)
		);
		// 1.9.4: icon above the text: the space under it (empty = 18px).
		$this->add_responsive_control(
			'icon_space_below',
			array(
				'label'      => __( 'Space below the icon', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-cards:not(.ts-cards--icon-beside) .ts-card__icon' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'icon_color', __( 'Icon colour (all cards)', 'travelstore-custom-widgets' ), '.ts-card__icon', 'color', '', array( 'description' => __( 'Replaces each card’s "Icon colour set". A card’s own "Icon colour" still wins.', 'travelstore-custom-widgets' ) ) );
		$this->add_color_control( 'icon_bg', __( 'Tile colour (all cards)', 'travelstore-custom-widgets' ), '.ts-card__icon', 'background-color', '', array( 'description' => __( 'Replaces each card’s "Icon colour set" tile. A card’s own "Icon tile colour" still wins.', 'travelstore-custom-widgets' ) ) );
		$this->end_controls_section();

		/* ---------------- Style: text ---------------- */
		$this->start_controls_section(
			'ts_section_text_style',
			array(
				'label' => __( 'Text', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'title_typography', __( 'Title typography', 'travelstore-custom-widgets' ), '.ts-card__title', 'ts_heading' );
		$this->add_color_control( 'title_color', __( 'Title colour', 'travelstore-custom-widgets' ), '.ts-card__title' );
		$this->add_typography_control( 'subtitle_typography', __( 'Line under the title – typography', 'travelstore-custom-widgets' ), '.ts-card__sub' );
		$this->add_color_control( 'subtitle_color', __( 'Line under the title – colour', 'travelstore-custom-widgets' ), '.ts-card__sub' );
		$this->add_typography_control( 'text_typography', __( 'Text typography', 'travelstore-custom-widgets' ), '.ts-card__text', 'ts_body' );
		$this->add_color_control( 'text_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-card__text' );
		// 1.9.4: space under the caption and under the title (empty = the widget's own spacing).
		$this->add_responsive_control(
			'kicker_space',
			array(
				'label'      => __( 'Space under the caption', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-card__kicker' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'title_space',
			array(
				'label'      => __( 'Space under the title', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-card__title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		// 1.9.2: keep long text to a narrower measure than the card (Musandam panels: 400px).
		$this->add_responsive_control(
			'text_max_width',
			array(
				'label'      => __( 'Text width (at most)', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 160, 'max' => 900 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'selectors'  => array( '{{WRAPPER}} .ts-card__text' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_typography_control( 'kicker_typography', __( 'Caption typography', 'travelstore-custom-widgets' ), '.ts-card__kicker' );
		$this->add_color_control( 'kicker_color', __( 'Caption colour', 'travelstore-custom-widgets' ), '.ts-card__kicker' );
		$this->add_typography_control( 'marker_typography', __( 'Number / time typography', 'travelstore-custom-widgets' ), '.ts-card__marker' );
		$this->add_color_control( 'marker_color', __( 'Number / time colour', 'travelstore-custom-widgets' ), '.ts-card__marker' );
		$this->add_typography_control( 'details_typography', __( 'Facts, tags and note typography', 'travelstore-custom-widgets' ), '.ts-card__facts, .ts-card__chips, .ts-card__meta' );
		// 1.9.3: the note at the bottom of the card (Adventure effort cards: paler, no line above).
		$this->add_color_control( 'meta_color', __( 'Note colour', 'travelstore-custom-widgets' ), '.ts-card__meta' );
		$this->add_control(
			'meta_plain',
			array(
				'label'        => __( 'Note without the line above', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'selectors'    => array(
					'{{WRAPPER}} .ts-card__meta'                 => 'margin-top: 12px; padding-top: 0; border-top: 0;',
					'{{WRAPPER}} .ts-card > :has(+ .ts-card__meta), {{WRAPPER}} .ts-card__body > :has(+ .ts-card__meta)' => 'margin-bottom: 0;',
				),
			)
		);
		$this->add_typography_control( 'bullets_typography', __( 'Bullet points typography', 'travelstore-custom-widgets' ), '.ts-card__list' );
		$this->add_color_control( 'bullets_color', __( 'Bullet points colour', 'travelstore-custom-widgets' ), '.ts-card__list' );
		// 1.9.2: spacing of the bullet points (empty = 20px above, 6px between).
		$this->add_responsive_control(
			'bullets_space_above',
			array(
				'label'      => __( 'Space above bullet points', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .ts-card__text:has(+ .ts-card__list)' => 'margin-bottom: 0;',
					'{{WRAPPER}} .ts-card__list' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'bullets_gap',
			array(
				'label'      => __( 'Space between bullet points', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-card__list li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		// 1.9.0: link line under the text (empty = the default dark orange).
		$this->add_typography_control( 'link_typography', __( 'Link typography', 'travelstore-custom-widgets' ), '.ts-card__link' );
		$this->add_color_control( 'link_color', __( 'Link colour', 'travelstore-custom-widgets' ), '.ts-card__link' );
		$this->add_control(
			'chips_heading',
			array(
				'label'     => __( 'Tags', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_typography_control( 'chip_typography', __( 'Tag typography', 'travelstore-custom-widgets' ), '.ts-card__chips li' );
		$this->add_color_control( 'chip_bg', __( 'Tag colour', 'travelstore-custom-widgets' ), '.ts-card__chips li', 'background-color' );
		$this->add_color_control( 'chip_text', __( 'Tag text colour', 'travelstore-custom-widgets' ), '.ts-card__chips li' );
		$this->add_color_control( 'chip_border', __( 'Tag border colour', 'travelstore-custom-widgets' ), '.ts-card__chips li', 'border-color' );
		$this->add_responsive_control(
			'chip_padding',
			array(
				'label'      => __( 'Tag padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-card__chips li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'chip_gap',
			array(
				'label'      => __( 'Space between tags', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-card__chips' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'chip_top',
			array(
				'label'      => __( 'Space above the tags', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-card__chips' => 'margin-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'timetable_heading',
			array(
				'label'     => __( 'Key facts as a timetable', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'facts_look' => 'timetable' ),
			)
		);
		$this->add_responsive_control(
			'fact_label_width',
			array(
				'label'      => __( 'Label column width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 40, 'max' => 200 ) ),
				'default'    => array( 'size' => 78, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-cards--facts-timetable .ts-card__fact' => 'grid-template-columns: {{SIZE}}{{UNIT}} minmax(0, 1fr);' ),
				'condition'  => array( 'facts_look' => 'timetable' ),
			)
		);
		$this->add_responsive_control(
			'fact_gap',
			array(
				'label'      => __( 'Space between rows', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-cards--facts-timetable .ts-card__facts' => 'gap: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'facts_look' => 'timetable' ),
			)
		);
		$this->add_typography_control( 'fact_label_typography', __( 'Label typography', 'travelstore-custom-widgets' ), '.ts-cards--facts-timetable .ts-card__fact-label', '', array( 'condition' => array( 'facts_look' => 'timetable' ) ) );
		$this->add_color_control( 'fact_label_color', __( 'Label colour', 'travelstore-custom-widgets' ), '.ts-cards--facts-timetable .ts-card__fact-label', 'color', 'ts_turquoise_deep', array( 'condition' => array( 'facts_look' => 'timetable' ) ) );
		$this->add_typography_control( 'fact_value_typography', __( 'Text typography', 'travelstore-custom-widgets' ), '.ts-cards--facts-timetable .ts-card__fact b', '', array( 'condition' => array( 'facts_look' => 'timetable' ) ) );
		$this->add_color_control( 'fact_value_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-cards--facts-timetable .ts-card__fact b', 'color', 'ts_espresso_70', array( 'condition' => array( 'facts_look' => 'timetable' ) ) );
		$this->add_rating_style_controls();
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	/**
	 * Places for the live clock (value = the place's time zone name, which browsers understand).
	 */
	protected static function clock_options() {
		return array(
			'none'                => __( 'No clock', 'travelstore-custom-widgets' ),
			'Asia/Muscat'         => __( 'Muscat (Oman)', 'travelstore-custom-widgets' ),
			'Asia/Dubai'          => __( 'Dubai', 'travelstore-custom-widgets' ),
			'Asia/Riyadh'         => __( 'Riyadh', 'travelstore-custom-widgets' ),
			'Europe/London'       => __( 'London', 'travelstore-custom-widgets' ),
			'Europe/Berlin'       => __( 'Berlin', 'travelstore-custom-widgets' ),
			'Europe/Rome'         => __( 'Rome', 'travelstore-custom-widgets' ),
			'Europe/Paris'        => __( 'Paris', 'travelstore-custom-widgets' ),
			'America/New_York'    => __( 'New York', 'travelstore-custom-widgets' ),
			'America/Los_Angeles' => __( 'Los Angeles', 'travelstore-custom-widgets' ),
			'Asia/Kolkata'        => __( 'India', 'travelstore-custom-widgets' ),
			'Asia/Singapore'      => __( 'Singapore', 'travelstore-custom-widgets' ),
			'Australia/Sydney'    => __( 'Sydney', 'travelstore-custom-widgets' ),
		);
	}

	/**
	 * The clock script loads only where a card shows a clock (and always in the editor preview).
	 */
	public function get_script_depends() {
		$plugin = \Elementor\Plugin::$instance;
		if ( ( $plugin->editor && $plugin->editor->is_edit_mode() ) || ( $plugin->preview && $plugin->preview->is_preview_mode() ) ) {
			return array( 'ts-clock' );
		}
		foreach ( (array) $this->get_settings( 'items' ) as $item ) {
			if ( ! empty( $item['clock'] ) && 'none' !== $item['clock'] ) {
				return array( 'ts-clock' );
			}
		}

		return array();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = isset( $s['items'] ) && is_array( $s['items'] ) ? $s['items'] : array();

		if ( empty( $items ) ) {
			$this->render_empty( __( 'Add at least one card to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$style   = isset( $s['card_style'] ) && in_array( $s['card_style'], array( 'filled', 'outlined', 'plain', 'topbar' ), true ) ? $s['card_style'] : 'filled';
		$marker  = isset( $s['marker_style'] ) && 'number' === $s['marker_style'] ? 'number' : 'dot';
		$tag     = $this->safe_tag( isset( $s['title_tag'] ) ? $s['title_tag'] : 'h3', 'h3' );
		$beside  = isset( $s['icon_position'] ) && 'beside' === $s['icon_position'];
		$rlabel  = isset( $s['rating_label'] ) ? trim( (string) $s['rating_label'] ) : '';
		$rscale  = $this->rating_scale( $s );
		$classes = array( 'ts-w', 'ts-cards', 'ts-cards--' . $style, 'ts-cards--marker-' . $marker );
		if ( empty( $s['equal_height'] ) ) {
			$classes[] = 'ts-cards--auto-height';
		}
		$classes[] = isset( $s['facts_look'] ) && 'timetable' === $s['facts_look'] ? 'ts-cards--facts-timetable' : 'ts-cards--facts-inline';
		if ( $beside ) {
			$classes[] = 'ts-cards--icon-beside';
		}
		if ( ! empty( $s['bullet_lines'] ) ) {
			$classes[] = 'ts-cards--bullet-lines';
		}
		$clocks = self::clock_options();
		$whole = ! empty( $s['link_whole'] );
		if ( $whole ) {
			$classes[] = 'ts-cards--linked';
			if ( ! isset( $s['link_hover'] ) || 'none' !== $s['link_hover'] ) {
				$classes[] = 'ts-cards--hover-lift';
			}
		}
		if ( isset( $s['text_scheme'] ) && 'light' === $s['text_scheme'] ) {
			$classes[] = 'ts-cards--light';
			$classes[] = 'ts-on-dark';
		}
		foreach ( array( 'dividers', 'dividers_tablet', 'dividers_mobile' ) as $dkey ) {
			if ( ! empty( $s[ $dkey ] ) && 'none' !== $s[ $dkey ] ) {
				$classes[] = 'ts-cards--divided';
				break;
			}
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<ul class="ts-cards__grid">
				<?php
				foreach ( $items as $i => $item ) {
					$rid       = ! empty( $item['_id'] ) ? sanitize_html_class( $item['_id'] ) : 'i' . $i;
					$tone      = $this->safe_tone( isset( $item['tone'] ) ? $item['tone'] : 'gold', 'gold' );
					$card_cls  = array( 'ts-card', 'elementor-repeater-item-' . $rid, 'ts-tone--' . $tone );
					$has_dark  = ! empty( $item['dark'] );
					if ( $has_dark ) {
						$card_cls[] = 'ts-card--dark';
						$card_cls[] = 'ts-on-dark';
					}
					if ( ! empty( $item['accent'] ) ) {
						$card_cls[] = 'ts-card--accent';
					}
					if ( ! empty( $item['light'] ) && ! $has_dark ) {
						$card_cls[] = 'ts-card--light';
						$card_cls[] = 'ts-on-dark';
					}
					$has_icon = ! empty( $item['icon']['value'] );
					$bullets  = $this->lines( isset( $item['bullets'] ) ? $item['bullets'] : '' );
					$rlevel   = $this->rating_level( isset( $item['rating'] ) ? $item['rating'] : '', $rscale );
					$rating   = $this->rating_html( $rlevel, $rscale, $rlabel, $rlevel ? $this->rating_name( $s, $rlevel ) : '' );
					$facts = array();
					foreach ( $this->lines( isset( $item['facts'] ) ? $item['facts'] : '' ) as $line ) {
						$parts   = array_map( 'trim', explode( '|', $line, 2 ) );
						$facts[] = array( $parts[0], isset( $parts[1] ) ? $parts[1] : '' );
					}
					$chips = $this->lines( isset( $item['chips'] ) ? $item['chips'] : '' );
					?>
					<li class="<?php echo esc_attr( implode( ' ', $card_cls ) ); ?>">
						<?php if ( $has_icon ) : ?>
							<div class="ts-card__icon"><?php \Elementor\Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ); ?></div>
						<?php endif; ?>
						<?php if ( $beside && $has_icon ) : ?>
							<div class="ts-card__body">
						<?php endif; ?>
						<?php if ( '' !== $rating ) : ?>
							<div class="ts-card__rating"><?php echo $rating; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $item['marker'] ) ) : ?>
							<div class="ts-card__marker"><?php echo esc_html( $item['marker'] ); ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $item['kicker'] ) ) : ?>
							<div class="ts-card__kicker"><?php echo esc_html( $item['kicker'] ); ?></div>
						<?php endif; ?>
						<?php
						$clock = isset( $item['clock'] ) && 'none' !== $item['clock'] && isset( $clocks[ $item['clock'] ] ) ? (string) $item['clock'] : '';
						$title = isset( $item['title'] ) ? (string) $item['title'] : '';
						?>
						<?php if ( '' !== $clock ) : ?>
							<<?php echo esc_attr( $tag ); ?> class="ts-card__title"><span class="ts-clock" data-ts-clock="<?php echo esc_attr( $clock ); ?>" data-ts-clock-format="<?php echo isset( $item['clock_format'] ) && 'h12' === $item['clock_format'] ? 'h12' : 'h24'; ?>"><?php echo esc_html( $title ); ?></span></<?php echo esc_attr( $tag ); ?>>
						<?php elseif ( '' !== $title ) : ?>
							<<?php echo esc_attr( $tag ); ?> class="ts-card__title"><?php echo esc_html( $title ); ?></<?php echo esc_attr( $tag ); ?>>
						<?php endif; ?>
						<?php if ( ! empty( $item['subtitle'] ) ) : ?>
							<div class="ts-card__sub"><?php echo esc_html( $item['subtitle'] ); ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $item['text'] ) ) : ?>
							<p class="ts-card__text"><?php echo nl2br( esc_html( $item['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $bullets ) ) : ?>
							<?php
							$look       = isset( $item['bullet_look'] ) ? $item['bullet_look'] : 'dot';
							$list_class = in_array( $look, array( 'tick', 'cross' ), true ) ? ' ts-card__list--mark ts-card__list--' . $look : ( 'small' === $look ? ' ts-card__list--small' : '' );
							?>
							<ul class="ts-card__list<?php echo esc_attr( $list_class ); ?>">
								<?php foreach ( $bullets as $bullet ) : ?>
									<li><?php echo esc_html( $bullet ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<?php
						$stops = array();
						for ( $n = 1; $n <= 6; $n++ ) {
							$stop_name = isset( $item[ 'stop' . $n . '_name' ] ) ? trim( (string) $item[ 'stop' . $n . '_name' ] ) : '';
							if ( '' !== $stop_name ) {
								$stops[] = array(
									'name' => $stop_name,
									'note' => isset( $item[ 'stop' . $n . '_note' ] ) ? trim( (string) $item[ 'stop' . $n . '_note' ] ) : '',
									'air'  => ! empty( $item[ 'stop' . $n . '_air' ] ),
								);
							}
						}
						?>
						<?php if ( ! empty( $stops ) ) : ?>
							<ol class="ts-card__stops">
								<?php foreach ( $stops as $stop ) : ?>
									<li<?php echo $stop['air'] ? ' class="is-air"' : ''; ?>><?php echo esc_html( $stop['name'] ); ?><?php echo '' !== $stop['note'] ? '<small>' . esc_html( $stop['note'] ) . '</small>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
								<?php endforeach; ?>
							</ol>
						<?php endif; ?>
						<?php if ( ! empty( $chips ) ) : ?>
							<ul class="ts-card__chips">
								<?php foreach ( $chips as $chip ) : ?>
									<li><?php echo esc_html( $chip ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<?php
						$link      = $this->contact_link( $item, 'link', isset( $item['subtitle'] ) ? $item['subtitle'] : '' );
						$link_text = isset( $item['link_text'] ) ? trim( (string) $item['link_text'] ) : '';
						$lkey      = 'ts_card_link_' . $rid;
						if ( '' !== $link_text ) {
							// A web link to another website is marked with an arrow and opens in a new tab (1.8.0).
							$other = ( ! isset( $item['link_kind'] ) || 'url' === $item['link_kind'] ) && self::is_other_site( $link['url'] );
							if ( $other ) {
								$link['is_external'] = 'on';
							}
							if ( '' !== $link['url'] ) {
								$this->add_link_attributes( $lkey, $link );
							} else {
								$this->add_render_attribute( $lkey, 'href', '#' );
							}
							$this->add_render_attribute( $lkey, 'class', 'ts-card__link' . ( $whole && '' !== $link['url'] ? ' ts-card__link--stretch' : '' ) . ( $other ? ' ts-ext' : '' ) );
							if ( $other ) {
								$this->add_render_attribute( $lkey, 'rel', 'noopener' );
							}
							printf( '<a %s>%s%s</a>', $this->get_render_attribute_string( $lkey ), esc_html( $link_text ), $other ? self::external_note() : '' ); // phpcs:ignore WordPress.Security.EscapeOutput
						} elseif ( $whole && '' !== $link['url'] ) {
							// No link text: an empty link over the card, named after the card for screen readers.
							$this->add_link_attributes( $lkey, $link );
							$this->add_render_attribute( $lkey, 'class', 'ts-card__link--stretch' );
							$name = trim( ( isset( $item['title'] ) ? $item['title'] : '' ) . ( ! empty( $item['subtitle'] ) ? ' – ' . $item['subtitle'] : '' ) );
							$this->add_render_attribute( $lkey, 'aria-label', '' !== $name ? $name : $link['url'] );
							printf( '<a %s></a>', $this->get_render_attribute_string( $lkey ) ); // phpcs:ignore WordPress.Security.EscapeOutput
						}
						?>
						<?php if ( ! empty( $facts ) ) : ?>
							<div class="ts-card__facts">
								<?php foreach ( $facts as $fact ) : ?>
									<div class="ts-card__fact"><span class="ts-card__fact-label"><?php echo esc_html( $fact[0] ); ?></span><?php echo '' !== $fact[1] ? ' <b>' . esc_html( $fact[1] ) . '</b>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<?php $meta_rich = isset( $item['meta_rich'] ) ? trim( (string) $item['meta_rich'] ) : ''; ?>
						<?php if ( '' !== $meta_rich ) : ?>
							<div class="ts-card__meta ts-card__meta--rich"><?php echo $this->mark_external_links( wp_kses_post( $meta_rich ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<?php elseif ( ! empty( $item['meta'] ) ) : ?>
							<div class="ts-card__meta"><?php echo esc_html( $item['meta'] ); ?></div>
						<?php endif; ?>
						<?php if ( $beside && $has_icon ) : ?>
							</div>
						<?php endif; ?>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
		<?php
	}
}
