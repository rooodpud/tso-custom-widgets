<?php
/**
 * Widget: TS Media Cards — cards led by a picture (or a two-colour gradient).
 *
 * Each card shows a photo, a bundled art scene, or a gradient placeholder, with an
 * optional badge, small caption, title, text and link. "Stacked" puts the picture
 * on top; "Text over picture" lays the text on the picture.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Media_Cards extends TS_Base_Widget {

	public function get_name() {
		return 'ts-media-cards';
	}

	public function get_title() {
		return __( 'TS Media Cards', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_keywords() {
		return array( 'cards', 'image', 'photo', 'destinations', 'routes', 'gallery', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Cards', 'travelstore-custom-widgets' ) )
		);

		$r = new Repeater();
		$r->add_control(
			'image',
			array(
				'label'       => __( 'Photo', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Optional. A photo replaces the art scene and the colours below.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_image_options( $r, 'image', array( 'link_note' => __( 'Not used when the whole card is clickable ("Link (makes the whole card clickable)", further down).', 'travelstore-custom-widgets' ) ) );
		// Per-card picture position (same options as Elementor's Image widget). Overrides Style > Image > Object Position for this card only.
		$pic = '{{WRAPPER}} {{CURRENT_ITEM}} .ts-mcard__img';
		$r->add_responsive_control(
			'card_object_position',
			array(
				'label'                => __( 'Picture Position', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => '',
				'options'              => array(
					''              => __( 'Default (follows Style > Image)', 'travelstore-custom-widgets' ),
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
				'description'          => __( 'Which part of this card’s photo stays in view when it is cropped.', 'travelstore-custom-widgets' ),
				'selectors_dictionary' => array( 'custom' => 'var(--ts-card-obj-x, 50%) var(--ts-card-obj-y, 50%)' ),
				'selectors'            => array( $pic => 'object-position: {{VALUE}};' ),
			)
		);
		foreach ( array( 'x' => __( 'Picture X Position (left to right)', 'travelstore-custom-widgets' ), 'y' => __( 'Picture Y Position (top to bottom)', 'travelstore-custom-widgets' ) ) as $axis => $label ) {
			$r->add_responsive_control(
				'card_object_position_' . $axis,
				array(
					'label'      => $label,
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'range'      => array( '%' => array( 'min' => 0, 'max' => 100, 'step' => 0.5 ) ),
					'default'    => array( 'size' => 50, 'unit' => '%' ),
					'selectors'  => array( $pic => '--ts-card-obj-' . $axis . ': {{SIZE}}{{UNIT}};' ),
					'condition'  => array( 'card_object_position' => 'custom' ),
				)
			);
		}
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
			'color_a',
			array(
				'label'     => __( 'Placeholder colour 1', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#38A833',
				'selectors' => array( '{{WRAPPER}} {{CURRENT_ITEM}} .ts-mcard__media' => '--ts-a: {{VALUE}};' ),
			)
		);
		$r->add_control(
			'color_b',
			array(
				'label'       => __( 'Placeholder colour 2', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#2C8828',
				'description' => __( 'Shown as a colour blend until you add a photo or art scene.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} {{CURRENT_ITEM}} .ts-mcard__media' => '--ts-b: {{VALUE}};' ),
			)
		);
		// 1.8.1: letters on the colour blend, for example initials until a portrait arrives.
		$r->add_control(
			'initials',
			array(
				'label'       => __( 'Letters on the colour blend', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Optional, for example a person’s initials: MK. Shown only while there is no photo or art scene.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'badge',
			array(
				'label'       => __( 'Badge on the picture', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'For example: 45 min. Optional.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
				'separator'   => 'before',
			)
		);
		$r->add_control(
			'kicker',
			array(
				'label'   => __( 'Small caption', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
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
		// 1.8.1: tags (pills) with an optional small heading, for example "Ask me about".
		$r->add_control(
			'tags_label',
			array(
				'label'       => __( 'Small heading above the tags', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Optional, for example: Ask me about.', 'travelstore-custom-widgets' ),
				'separator'   => 'before',
			)
		);
		$r->add_control(
			'tags',
			array(
				'label'       => __( 'Tags', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => __( 'One per line. Shown as small pills. Optional.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'facts_heading',
			array(
				'label'     => __( 'Key facts (optional, shown as a small grid)', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$r->add_control(
			'rating',
			array(
				'label'       => __( 'Rating (bars)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => self::rating_options(),
				'description' => __( 'How many bars are filled on this card, for example the effort level. Shown first in the key facts. The number of bars and the level names are set under "Rating bars".', 'travelstore-custom-widgets' ),
			)
		);
		$fact_examples = array(
			1 => array( __( 'Duration', 'travelstore-custom-widgets' ), __( '3–4 hours', 'travelstore-custom-widgets' ) ),
			2 => array( __( 'Best time', 'travelstore-custom-widgets' ), __( 'Oct – Apr', 'travelstore-custom-widgets' ) ),
			3 => array( __( 'Suits', 'travelstore-custom-widgets' ), __( 'Most walkers', 'travelstore-custom-widgets' ) ),
			4 => array( __( 'Departs', 'travelstore-custom-widgets' ), __( 'Muscat', 'travelstore-custom-widgets' ) ),
		);
		for ( $n = 1; $n <= 4; $n++ ) {
			$r->add_control(
				'fact' . $n . '_label',
				array(
					/* translators: %d: fact number. */
					'label'       => sprintf( __( 'Fact %d – name', 'travelstore-custom-widgets' ), $n ),
					'type'        => Controls_Manager::TEXT,
					/* translators: %s: example fact name. */
					'placeholder' => sprintf( __( 'For example: %s', 'travelstore-custom-widgets' ), $fact_examples[ $n ][0] ),
				)
			);
			$r->add_control(
				'fact' . $n . '_value',
				array(
					/* translators: %d: fact number. */
					'label'       => sprintf( __( 'Fact %d – text', 'travelstore-custom-widgets' ), $n ),
					'type'        => Controls_Manager::TEXT,
					/* translators: %s: example fact text. */
					'placeholder' => sprintf( __( 'For example: %s', 'travelstore-custom-widgets' ), $fact_examples[ $n ][1] ),
				)
			);
		}
		$r->add_control(
			'group',
			array(
				'label'       => __( 'Filter group', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'separator'   => 'before',
				'description' => __( 'Only needed with "Filter buttons" on. Type the group this card belongs to, for example: Mountains. Every card with the same group name appears under one button (capital letters do not matter).', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'tip',
			array(
				'label'       => __( 'Tip box', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => __( 'Optional box at the bottom of the card. Put a bold start before a | , for example: Tip: | Go around sunset.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'link_text',
			array(
				'label'       => __( 'Link text', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'For example: See the route →. Shown when a link is set.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'link',
			array(
				'label'       => __( 'Link (makes the whole card clickable)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'Type a page name or paste a link', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
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
						'badge'   => __( '45 min', 'travelstore-custom-widgets' ),
						'kicker'  => __( 'Caption', 'travelstore-custom-widgets' ),
						'title'   => __( 'First card', 'travelstore-custom-widgets' ),
						'text'    => __( 'A short description of this place or experience.', 'travelstore-custom-widgets' ),
						'color_a' => '#38A833',
						'color_b' => '#2C8828',
					),
					array(
						'badge'   => __( '75 min', 'travelstore-custom-widgets' ),
						'kicker'  => __( 'Caption', 'travelstore-custom-widgets' ),
						'title'   => __( 'Second card', 'travelstore-custom-widgets' ),
						'text'    => __( 'A short description of this place or experience.', 'travelstore-custom-widgets' ),
						'color_a' => '#0392CC',
						'color_b' => '#0D76AB',
					),
					array(
						'badge'   => __( '35 min', 'travelstore-custom-widgets' ),
						'kicker'  => __( 'Caption', 'travelstore-custom-widgets' ),
						'title'   => __( 'Third card', 'travelstore-custom-widgets' ),
						'text'    => __( 'A short description of this place or experience.', 'travelstore-custom-widgets' ),
						'color_a' => '#B6712A',
						'color_b' => '#8C4210',
					),
				),
			)
		);
		$this->add_tag_control( 'title_tag', __( 'Title level', 'travelstore-custom-widgets' ), 'h3' );
		$this->end_controls_section();

		/* ---------------- Content: rating bars and their key ---------------- */
		$this->start_controls_section(
			'ts_rating',
			array( 'label' => __( 'Rating bars', 'travelstore-custom-widgets' ) )
		);
		$this->add_rating_content_controls();
		$this->add_control(
			'rating_key_show',
			array(
				'label'        => __( 'Show a key of the levels above the cards', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
				'description'  => __( 'Shows the bars of each level with its name, for example the bars for Easy, Moderate and Challenging.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'rating_key_filter',
			array(
				'label'        => __( 'Visitors can click a level to show only those cards', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Off: the key only explains the bars. On: each level is a button; clicking it again shows all levels. Works together with the filter buttons.', 'travelstore-custom-widgets' ),
				'condition'    => array( 'rating_key_show' => 'yes' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Content: filter buttons ---------------- */
		$this->start_controls_section(
			'ts_filter',
			array( 'label' => __( 'Filter buttons', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'filter_show',
			array(
				'label'        => __( 'Show filter buttons', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Buttons above the cards that show one group at a time, plus an "All" button. Type each card’s group in the card ("Filter group"); a button appears for every group.', 'travelstore-custom-widgets' ),
			)
		);
		$fb = new Repeater();
		$fb->add_control(
			'label',
			array(
				'label'       => __( 'Group name (the same as in the cards)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$fb->add_control(
			'dot',
			array(
				'label'     => __( 'Dot colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} {{CURRENT_ITEM}} .ts-mcards__dot' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'filter_buttons',
			array(
				'label'       => __( 'Button order and dot colours (optional)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $fb->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(),
				'description' => __( 'Add a group here only to put its button in a set order or give it a coloured dot. Groups not listed still get a button, after these.', 'travelstore-custom-widgets' ),
				'condition'   => array( 'filter_show' => 'yes' ),
			)
		);
		$this->add_control(
			'filter_all',
			array(
				'label'     => __( '"All" button text', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'All', 'travelstore-custom-widgets' ),
				'condition' => array( 'filter_show' => 'yes' ),
			)
		);
		$this->add_control(
			'filter_label',
			array(
				'label'       => __( 'Name of the button group', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Filter the cards', 'travelstore-custom-widgets' ),
				'description' => __( 'Read out by screen readers, for example: Filter activities by terrain.', 'travelstore-custom-widgets' ),
				'condition'   => array( 'filter_show' => 'yes' ),
			)
		);
		$this->add_control(
			'count_show',
			array(
				'label'        => __( 'Show how many cards are shown', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
				'description'  => __( 'A line such as "Showing 6 activities" that updates when a button is clicked.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'count_before',
			array(
				'label'     => __( 'Word before the number', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Showing', 'travelstore-custom-widgets' ),
				'condition' => array( 'count_show' => 'yes' ),
			)
		);
		$this->add_control(
			'count_word_many',
			array(
				'label'       => __( 'Word after the number (several)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'cards', 'travelstore-custom-widgets' ),
				'description' => __( 'For example: activities, which gives "Showing 6 activities".', 'travelstore-custom-widgets' ),
				'condition'   => array( 'count_show' => 'yes' ),
			)
		);
		$this->add_control(
			'count_word_one',
			array(
				'label'       => __( 'Word after the number (one)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'card', 'travelstore-custom-widgets' ),
				'description' => __( 'For example: activity, which gives "Showing 1 activity".', 'travelstore-custom-widgets' ),
				'condition'   => array( 'count_show' => 'yes' ),
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
			'layout',
			array(
				'label'   => __( 'Layout', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'stacked',
				'options' => array(
					'stacked' => __( 'Picture on top, text below', 'travelstore-custom-widgets' ),
					'overlay' => __( 'Text over the picture', 'travelstore-custom-widgets' ),
					'beside'  => __( 'Picture beside the text', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_responsive_control(
			'beside_direction',
			array(
				'label'                => __( 'Picture and text', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'row',
				'mobile_default'       => 'column',
				'options'              => array(
					'row'    => __( 'Side by side', 'travelstore-custom-widgets' ),
					'column' => __( 'Picture on top (stacked)', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'row'    => '--ts-mb-dir:row;--ts-mb-w:var(--ts-mcard-side,130px);--ts-mb-h:auto;',
					'column' => '--ts-mb-dir:column;--ts-mb-w:100%;--ts-mb-h:var(--ts-mcard-stack-h,90px);',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-mcards--beside' => '{{VALUE}}' ),
				'condition'            => array( 'layout' => 'beside' ),
			)
		);
		$this->add_responsive_control(
			'side_width',
			array(
				'label'      => __( 'Picture width (side by side)', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 60, 'max' => 400 ),
					'%'  => array( 'min' => 10, 'max' => 60 ),
				),
				'default'    => array( 'size' => 130, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-mcards--beside' => '--ts-mcard-side: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'layout' => 'beside' ),
			)
		);
		$this->add_responsive_control(
			'stack_height',
			array(
				'label'      => __( 'Picture height (stacked)', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 40, 'max' => 400 ) ),
				'default'    => array( 'size' => 90, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-mcards--beside' => '--ts-mcard-stack-h: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'layout' => 'beside' ),
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
				'options'        => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ),
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
				'label'       => __( 'Picture height', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 60, 'max' => 500 ) ),
				'default'     => array( 'size' => 170, 'unit' => 'px' ),
				'description' => __( 'A short height (around 90–120px) suits a plain colour. A photo usually looks better taller (170px or more).', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-mcards--stacked .ts-mcard__media' => 'height: {{SIZE}}{{UNIT}};' ),
				'condition'   => array( 'layout' => 'stacked' ),
			)
		);
		$this->add_responsive_control(
			'card_height',
			array(
				'label'      => __( 'Card height', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 160, 'max' => 700 ) ),
				'default'    => array( 'size' => 280, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-mcards--overlay .ts-mcard' => 'min-height: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'layout' => 'overlay' ),
			)
		);
		$this->add_color_control( 'card_bg', __( 'Card background', 'travelstore-custom-widgets' ), '.ts-mcards--stacked .ts-mcard, {{WRAPPER}} .ts-mcards--beside .ts-mcard', 'background-color', 'ts_sand' );
		$this->add_color_control( 'card_border', __( 'Card border colour', 'travelstore-custom-widgets' ), '.ts-mcard', 'border-color' );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'label'    => __( 'Card shadow', 'travelstore-custom-widgets' ),
				'selector' => '{{WRAPPER}} .ts-mcard',
			)
		);
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
		$this->add_responsive_control(
			'body_padding',
			array(
				'label'      => __( 'Text padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-mcard__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		// Text position (1.6.4): every control is per device (desktop / tablet / phone icons next to the label).
		$this->add_control(
			'text_position_heading',
			array(
				'label'       => __( 'Text position', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::HEADING,
				'separator'   => 'before',
				'description' => __( 'Each setting can differ for desktop, tablet and phone: click the screen icon next to its name.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_responsive_control(
			'text_v_position',
			array(
				'label'                => __( 'Up / down', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'top'    => array( 'title' => __( 'Top', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-top' ),
					'middle' => array( 'title' => __( 'Middle', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-middle' ),
					'bottom' => array( 'title' => __( 'Bottom', 'travelstore-custom-widgets' ), 'icon' => 'eicon-v-align-bottom' ),
				),
				'description'          => __( 'Text over the picture: where the text sits on the card (bottom when nothing is chosen). Other layouts: where the text sits in the text area.', 'travelstore-custom-widgets' ),
				'selectors_dictionary' => array(
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				),
				'selectors'            => array(
					'{{WRAPPER}} .ts-mcards--overlay .ts-mcard'                 => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .ts-mcards--stacked .ts-mcard__body, {{WRAPPER}} .ts-mcards--beside .ts-mcard__body' => 'justify-content: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'text_h_position',
			array(
				'label'                => __( 'Left / right', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'   => array( 'title' => __( 'Left', 'travelstore-custom-widgets' ), 'icon' => 'eicon-h-align-left' ),
					'center' => array( 'title' => __( 'Centre', 'travelstore-custom-widgets' ), 'icon' => 'eicon-h-align-center' ),
					'right'  => array( 'title' => __( 'Right', 'travelstore-custom-widgets' ), 'icon' => 'eicon-h-align-right' ),
				),
				'description'          => __( 'Moves the text block across the card. Works best with "Text block width" below.', 'travelstore-custom-widgets' ),
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-mcards--overlay .ts-mcard' => 'align-items: {{VALUE}};' ),
				'condition'            => array( 'layout' => 'overlay' ),
			)
		);
		$this->add_responsive_control(
			'text_width',
			array(
				'label'      => __( 'Text block width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array( 'min' => 20, 'max' => 100 ),
					'px' => array( 'min' => 100, 'max' => 800 ),
				),
				'selectors'  => array( '{{WRAPPER}} .ts-mcards--overlay .ts-mcard__body' => 'width: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'layout' => 'overlay' ),
			)
		);
		$this->add_responsive_control(
			'text_offset',
			array(
				'label'       => __( 'Distance from the card edges', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array( 'px', '%' ),
				'description' => __( 'Fine-tunes the position: for example Top 40 moves the text 40px down from the top edge.', 'travelstore-custom-widgets' ),
				// Padding on the card (the picture is absolutely placed, so it still fills the whole card).
				'selectors'   => array( '{{WRAPPER}} .ts-mcards--overlay .ts-mcard' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
				'condition'   => array( 'layout' => 'overlay' ),
			)
		);
		$this->add_responsive_control(
			'text_align',
			array(
				'label'     => __( 'Text alignment', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array( 'title' => __( 'Left', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-left' ),
					'center' => array( 'title' => __( 'Centre', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-center' ),
					'right'  => array( 'title' => __( 'Right', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-right' ),
				),
				'selectors' => array( '{{WRAPPER}} .ts-mcard__body' => 'text-align: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: picture (Elementor's Image options) ---------------- */
		$this->start_controls_section(
			'ts_section_image_style',
			array(
				'label' => __( 'Picture', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_image_style_controls( '.ts-mcard__img', '.ts-mcard__media', '.ts-mcard:hover .ts-mcard__img' );
		$this->add_control(
			'initials_heading',
			array(
				'label'     => __( 'Letters on the colour blend', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_typography_control( 'initials_typography', __( 'Letters typography', 'travelstore-custom-widgets' ), '.ts-mcard__initials' );
		$this->add_color_control( 'initials_color', __( 'Letters colour', 'travelstore-custom-widgets' ), '.ts-mcard__initials' );
		$this->end_controls_section();

		$this->add_image_caption_style_section();

		/* ---------------- Style: picture overlay and text background ---------------- */
		// Layers, bottom to top: picture → overlay → text background → text.
		$this->start_controls_section(
			'ts_section_layers',
			array(
				'label' => __( 'Picture overlay and text background', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'overlay_heading',
			array(
				'label' => __( 'Overlay on the picture', 'travelstore-custom-widgets' ),
				'type'  => Controls_Manager::HEADING,
			)
		);
		$this->add_control(
			'overlay_show',
			array(
				'label'       => __( 'Picture overlay', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''    => __( 'Hide', 'travelstore-custom-widgets' ),
					'yes' => __( 'Show', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'A colour layer between the picture and the text. Useful when the text sits directly on the picture (text background opacity at 0%).', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array( '{{WRAPPER}}' => '--ts-mcard-ov-color: {{VALUE}};' ),
				'condition' => array( 'overlay_show'  => 'yes' ),
			)
		);
		$this->add_control(
			'overlay_opacity',
			array(
				'label'      => __( 'Overlay opacity (%)', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 62, 'unit' => '%' ),
				'selectors'  => array( '{{WRAPPER}}' => '--ts-mcard-ov-opacity: calc({{SIZE}} / 100);' ),
				'condition'  => array( 'overlay_show'  => 'yes' ),
			)
		);
		$this->add_control(
			'overlay_area',
			array(
				'label'     => __( 'Overlay covers', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => array(
					'full'   => __( 'The whole picture', 'travelstore-custom-widgets' ),
					'bottom' => __( 'The bottom of the picture', 'travelstore-custom-widgets' ),
					'top'    => __( 'The top of the picture', 'travelstore-custom-widgets' ),
				),
				'condition' => array( 'overlay_show'  => 'yes' ),
			)
		);
		$this->add_responsive_control(
			'overlay_size',
			array(
				'label'       => __( 'Overlay height (%)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'range'       => array( '%' => array( 'min' => 10, 'max' => 100 ) ),
				'default'     => array( 'size' => 65, 'unit' => '%' ),
				'description' => __( '50% is exactly half the picture.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}}' => '--ts-mcard-ov-size: {{SIZE}}%;' ),
				'condition'   => array(
					'overlay_show'  => 'yes',
					'overlay_area'  => array( 'bottom', 'top' ),
				),
			)
		);
		$this->add_control(
			'overlay_style',
			array(
				'label'     => __( 'Overlay edge', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => array(
					'fade'  => __( 'Soft fade', 'travelstore-custom-widgets' ),
					'solid' => __( 'Solid, even colour', 'travelstore-custom-widgets' ),
				),
				'condition' => array( 'overlay_show'  => 'yes' ),
			)
		);

		$this->add_control(
			'text_bg_heading',
			array(
				'label'     => __( 'Background behind the text', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'text_bg_color',
			array(
				'label'       => __( 'Text background colour', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'global'      => array( 'default' => 'globals/colors?id=ts_sand' ),
				'description' => __( 'Set the opacity to 0% for no background: the text then sits directly on the picture in "Text over the picture" (or on the card background in "Picture on top"). Text straight on a photo usually needs lighter text colours (Text section) and a Picture overlay.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}}' => '--ts-mcard-textbg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'text_bg_opacity',
			array(
				'label'      => __( 'Text background opacity (%)', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 100, 'unit' => '%' ),
				'selectors'  => array( '{{WRAPPER}}' => '--ts-mcard-textbg-opacity: calc({{SIZE}} / 100);' ),
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
		$this->add_typography_control( 'title_typography', __( 'Title typography', 'travelstore-custom-widgets' ), '.ts-mcard__title', 'ts_heading' );
		$this->add_color_control( 'title_color', __( 'Title colour', 'travelstore-custom-widgets' ), '.ts-mcard__title', 'color', 'ts_espresso' );
		$this->add_typography_control( 'text_typography', __( 'Text typography', 'travelstore-custom-widgets' ), '.ts-mcard__text', 'ts_body' );
		$this->add_color_control( 'text_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-mcard__text', 'color', 'ts_espresso_70' );
		$this->add_typography_control( 'kicker_typography', __( 'Caption typography', 'travelstore-custom-widgets' ), '.ts-mcard__kicker' );
		$this->add_color_control( 'kicker_color', __( 'Caption colour', 'travelstore-custom-widgets' ), '.ts-mcard__kicker', 'color', 'ts_turquoise_deep' );

		$this->add_control(
			'tip_heading',
			array(
				'label'     => __( 'Tip box', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_typography_control( 'tip_typography', __( 'Tip typography', 'travelstore-custom-widgets' ), '.ts-mcard__tip' );
		$this->add_color_control( 'tip_color', __( 'Tip text colour', 'travelstore-custom-widgets' ), '.ts-mcard__tip', 'color', 'ts_espresso_70' );
		$this->add_color_control( 'tip_lead_color', __( 'Tip bold start colour', 'travelstore-custom-widgets' ), '.ts-mcard__tip b', 'color', 'ts_terracotta_dark' );
		$this->add_color_control( 'tip_bg', __( 'Tip box colour', 'travelstore-custom-widgets' ), '.ts-mcard__tip', 'background-color', 'ts_sand' );
		$this->add_responsive_control(
			'tip_radius',
			array(
				'label'      => __( 'Tip box corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-mcard__tip' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'tags_heading',
			array(
				'label'     => __( 'Tags', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_typography_control( 'tags_label_typography', __( 'Small heading typography', 'travelstore-custom-widgets' ), '.ts-mcard__tags-label' );
		$this->add_color_control( 'tags_label_color', __( 'Small heading colour', 'travelstore-custom-widgets' ), '.ts-mcard__tags-label' );
		$this->add_typography_control( 'tag_typography', __( 'Tag typography', 'travelstore-custom-widgets' ), '.ts-mcard__chips li' );
		$this->add_color_control( 'tag_bg', __( 'Tag colour', 'travelstore-custom-widgets' ), '.ts-mcard__chips li', 'background-color' );
		$this->add_color_control( 'tag_text', __( 'Tag text colour', 'travelstore-custom-widgets' ), '.ts-mcard__chips li' );
		$this->add_color_control( 'tag_border', __( 'Tag border colour', 'travelstore-custom-widgets' ), '.ts-mcard__chips li', 'border-color' );

		$this->add_control(
			'facts_heading',
			array(
				'label'     => __( 'Key facts', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_responsive_control(
			'facts_columns',
			array(
				'label'     => __( 'Facts per row', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''  => __( 'Default (2)', 'travelstore-custom-widgets' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
				'selectors' => array( '{{WRAPPER}} .ts-mcard__facts' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ),
			)
		);
		$this->add_typography_control( 'fact_label_typography', __( 'Fact label typography', 'travelstore-custom-widgets' ), '.ts-mcard__fact dt' );
		$this->add_color_control( 'fact_label_color', __( 'Fact label colour', 'travelstore-custom-widgets' ), '.ts-mcard__fact dt' );
		$this->add_typography_control( 'fact_value_typography', __( 'Fact value typography', 'travelstore-custom-widgets' ), '.ts-mcard__fact dd' );
		$this->add_color_control( 'fact_value_color', __( 'Fact value colour', 'travelstore-custom-widgets' ), '.ts-mcard__fact dd' );
		$this->add_color_control( 'facts_line', __( 'Line above the facts', 'travelstore-custom-widgets' ), '.ts-mcard__facts', 'border-top-color' );
		$this->add_rating_style_controls();

		$this->add_control(
			'link_heading',
			array(
				'label'     => __( 'Link', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_typography_control( 'link_typography', __( 'Link typography', 'travelstore-custom-widgets' ), '.ts-mcard__link' );
		$this->add_control(
			'link_color',
			array(
				'label'       => __( 'Link colour', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'global'      => array( 'default' => 'globals/colors?id=ts_terracotta_dark' ),
				'selectors'   => array( '{{WRAPPER}}' => '--ts-mcard-link-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'badge_heading',
			array(
				'label'     => __( 'Badge', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'badge_position',
			array(
				'label'       => __( 'Badge position', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''             => __( 'Automatic (matches the layout)', 'travelstore-custom-widgets' ),
					'top-left'     => __( 'Top left', 'travelstore-custom-widgets' ),
					'top-right'    => __( 'Top right', 'travelstore-custom-widgets' ),
					'bottom-left'  => __( 'Bottom left', 'travelstore-custom-widgets' ),
					'bottom-right' => __( 'Bottom right', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'Set the same position on every Media Cards widget on the page to keep badges aligned with each other.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_typography_control( 'badge_typography', __( 'Badge typography', 'travelstore-custom-widgets' ), '.ts-mcard__badge' );
		$this->add_color_control( 'badge_color', __( 'Badge text colour', 'travelstore-custom-widgets' ), '.ts-mcard__badge' );
		$this->add_color_control( 'badge_bg', __( 'Badge background colour', 'travelstore-custom-widgets' ), '.ts-mcard__badge', 'background-color' );
		$this->end_controls_section();

		/* ---------------- Style: filter buttons, count and rating key ---------------- */
		$this->start_controls_section(
			'ts_section_filter_style',
			array(
				'label' => __( 'Filter buttons and rating key', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'filter_typography', __( 'Button typography', 'travelstore-custom-widgets' ), '.ts-mcards__filter' );
		$this->add_color_control( 'filter_text', __( 'Button text', 'travelstore-custom-widgets' ), '.ts-mcards__filter' );
		$this->add_color_control( 'filter_border', __( 'Button border', 'travelstore-custom-widgets' ), '.ts-mcards__filter', 'border-color' );
		$this->add_color_control( 'filter_active_bg', __( 'Chosen button colour', 'travelstore-custom-widgets' ), '.ts-mcards__filter[aria-pressed="true"]', 'background-color', 'ts_espresso' );
		$this->add_color_control( 'filter_active_text', __( 'Chosen button text', 'travelstore-custom-widgets' ), '.ts-mcards__filter[aria-pressed="true"]' );
		$this->add_responsive_control(
			'filter_space',
			array(
				'label'      => __( 'Space under the buttons', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-mcards__bar' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_typography_control( 'count_typography', __( 'Count line typography', 'travelstore-custom-widgets' ), '.ts-mcards__count' );
		$this->add_color_control( 'count_color', __( 'Count line colour', 'travelstore-custom-widgets' ), '.ts-mcards__count' );
		$this->add_typography_control( 'key_typography', __( 'Rating key typography', 'travelstore-custom-widgets' ), '.ts-mcards__key' );
		$this->add_color_control( 'key_color', __( 'Rating key colour', 'travelstore-custom-widgets' ), '.ts-mcards__key' );
		$this->add_color_control( 'key_active_color', __( 'Chosen level colour (clickable key)', 'travelstore-custom-widgets' ), '.ts-mcards__level[aria-pressed="true"]', 'color', '', array( 'condition' => array( 'rating_key_filter' => 'yes' ) ) );
		$this->add_color_control( 'key_active_border', __( 'Chosen level outline (clickable key)', 'travelstore-custom-widgets' ), '.ts-mcards__level[aria-pressed="true"]', 'border-color', '', array( 'condition' => array( 'rating_key_filter' => 'yes' ) ) );
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	/**
	 * The filter script loads only where it is needed (and always in the editor preview).
	 */
	public function get_script_depends() {
		$plugin = \Elementor\Plugin::$instance;
		if ( ( $plugin->editor && $plugin->editor->is_edit_mode() ) || ( $plugin->preview && $plugin->preview->is_preview_mode() ) ) {
			return array( 'ts-media-cards' );
		}

		$filters = 'yes' === $this->get_settings( 'filter_show' ) || ( 'yes' === $this->get_settings( 'rating_key_show' ) && 'yes' === $this->get_settings( 'rating_key_filter' ) );

		return $filters ? array( 'ts-media-cards' ) : array();
	}

	/**
	 * Filter buttons in order: the listed buttons first, then any other group used by a card.
	 *
	 * @return array[] each: key, label, rid (repeater id of the listed button or '').
	 */
	private function filter_groups( $s, $items ) {
		$groups = array();
		foreach ( isset( $s['filter_buttons'] ) && is_array( $s['filter_buttons'] ) ? $s['filter_buttons'] : array() as $b ) {
			$label = isset( $b['label'] ) ? trim( (string) $b['label'] ) : '';
			$key   = sanitize_title( $label );
			if ( '' !== $key && ! isset( $groups[ $key ] ) ) {
				$groups[ $key ] = array( 'label' => $label, 'rid' => isset( $b['_id'] ) ? sanitize_html_class( $b['_id'] ) : '' );
			}
		}
		foreach ( $items as $item ) {
			$label = isset( $item['group'] ) ? trim( (string) $item['group'] ) : '';
			$key   = sanitize_title( $label );
			if ( '' !== $key && ! isset( $groups[ $key ] ) ) {
				$groups[ $key ] = array( 'label' => $label, 'rid' => '' );
			}
		}

		return $groups;
	}

	/**
	 * "Showing 6 activities" from the three count words.
	 */
	private function count_text( $before, $n, $many, $one ) {
		return trim( implode( ' ', array_filter( array( trim( (string) $before ), (string) $n, trim( (string) ( 1 === $n ? $one : $many ) ) ), 'strlen' ) ) );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = isset( $s['items'] ) && is_array( $s['items'] ) ? $s['items'] : array();

		if ( empty( $items ) ) {
			$this->render_empty( __( 'Add at least one card to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$layout  = isset( $s['layout'] ) && in_array( $s['layout'], array( 'overlay', 'beside' ), true ) ? $s['layout'] : 'stacked';
		$tag     = $this->safe_tag( isset( $s['title_tag'] ) ? $s['title_tag'] : 'h3', 'h3' );
		$classes = array( 'ts-w', 'ts-mcards', 'ts-mcards--' . $layout );

		$valid_badge_positions = array( 'top-left', 'top-right', 'bottom-left', 'bottom-right' );
		$badge_position        = isset( $s['badge_position'] ) ? $s['badge_position'] : '';
		if ( ! in_array( $badge_position, $valid_badge_positions, true ) ) {
			// Automatic: matches what each layout looked like before this was a control.
			$badge_position = 'overlay' === $layout ? 'top-left' : 'bottom-left';
		}

		// Picture overlay: off unless switched on (both layouts match the design by default).
		$overlay_on   = isset( $s['overlay_show'] ) && 'yes' === $s['overlay_show'];
		$classes[]    = $overlay_on ? 'ts-mcards--ov-on' : 'ts-mcards--ov-off';
		if ( $overlay_on ) {
			$area      = isset( $s['overlay_area'] ) && in_array( $s['overlay_area'], array( 'full', 'bottom', 'top' ), true ) ? $s['overlay_area'] : 'bottom';
			$edge      = isset( $s['overlay_style'] ) && 'solid' === $s['overlay_style'] ? 'solid' : 'fade';
			$classes[] = 'ts-mcards--ov-' . $area;
			$classes[] = 'ts-mcards--ov-' . $edge;
		}

		$rlabel     = isset( $s['rating_label'] ) ? trim( (string) $s['rating_label'] ) : '';
		$rscale     = $this->rating_scale( $s );
		$filter_on  = isset( $s['filter_show'] ) && 'yes' === $s['filter_show'];
		$groups     = $filter_on ? $this->filter_groups( $s, $items ) : array();
		$key_on     = isset( $s['rating_key_show'] ) && 'yes' === $s['rating_key_show'];
		$key_filter = $key_on && isset( $s['rating_key_filter'] ) && 'yes' === $s['rating_key_filter'];
		if ( ! empty( $groups ) || $key_filter ) {
			wp_enqueue_script( 'ts-media-cards' );
		}
		$key_rows = array();
		if ( $key_on ) {
			for ( $n = 1; $n <= $rscale; $n++ ) {
				$key_rows[] = array( $n, $this->rating_html( $n, $rscale ), $this->rating_name( $s, $n ) );
			}
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php if ( ! empty( $groups ) || ! empty( $key_rows ) ) : ?>
				<div class="ts-mcards__bar">
					<?php if ( ! empty( $groups ) ) : ?>
						<div class="ts-mcards__filters" role="group" aria-label="<?php echo esc_attr( isset( $s['filter_label'] ) ? $s['filter_label'] : '' ); ?>">
							<button type="button" class="ts-mcards__filter" data-filter="all" aria-pressed="true"><?php echo esc_html( isset( $s['filter_all'] ) && '' !== trim( (string) $s['filter_all'] ) ? $s['filter_all'] : __( 'All', 'travelstore-custom-widgets' ) ); ?></button>
							<?php foreach ( $groups as $gkey => $g ) : ?>
								<button type="button" class="ts-mcards__filter<?php echo '' !== $g['rid'] ? ' elementor-repeater-item-' . esc_attr( $g['rid'] ) : ''; ?>" data-filter="<?php echo esc_attr( $gkey ); ?>" aria-pressed="false"><?php echo '' !== $g['rid'] ? '<span class="ts-mcards__dot" aria-hidden="true"></span>' : ''; ?><?php echo esc_html( $g['label'] ); ?></button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $key_rows ) && $key_filter ) : ?>
						<?php /* translators: %s: what the bars show, e.g. Effort. */ ?>
						<div class="ts-mcards__key ts-mcards__key--buttons" role="group" aria-label="<?php echo esc_attr( sprintf( __( 'Show only one level of %s', 'travelstore-custom-widgets' ), '' !== $rlabel ? $rlabel : __( 'rating', 'travelstore-custom-widgets' ) ) ); ?>">
							<?php foreach ( $key_rows as $row ) : ?>
								<button type="button" class="ts-mcards__level" data-level="<?php echo (int) $row[0]; ?>" aria-pressed="false"><span class="ts-mcards__key-bars" aria-hidden="true"><?php echo $row[1]; // phpcs:ignore WordPress.Security.EscapeOutput ?></span> <?php echo esc_html( $row[2] ); ?></button>
							<?php endforeach; ?>
						</div>
					<?php elseif ( ! empty( $key_rows ) ) : ?>
						<div class="ts-mcards__key" aria-hidden="true">
							<?php foreach ( $key_rows as $row ) : ?>
								<span class="ts-mcards__key-item"><?php echo $row[1]; // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $row[2] ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php
			$count_on = ( ! empty( $groups ) || $key_filter ) && ( ! isset( $s['count_show'] ) || 'yes' === $s['count_show'] );
			if ( $count_on ) {
				$before = isset( $s['count_before'] ) ? (string) $s['count_before'] : '';
				$many   = isset( $s['count_word_many'] ) ? (string) $s['count_word_many'] : '';
				$one    = isset( $s['count_word_one'] ) && '' !== trim( (string) $s['count_word_one'] ) ? (string) $s['count_word_one'] : $many;
				printf(
					'<p class="ts-mcards__count" aria-live="polite" data-before="%1$s" data-many="%2$s" data-one="%3$s">%4$s</p>',
					esc_attr( $before ),
					esc_attr( $many ),
					esc_attr( $one ),
					esc_html( $this->count_text( $before, count( $items ), $many, $one ) )
				);
			}
			?>
			<ul class="ts-mcards__grid">
				<?php
				foreach ( $items as $i => $item ) {
					$rid      = ! empty( $item['_id'] ) ? sanitize_html_class( $item['_id'] ) : 'i' . $i;
					$has_link = ! empty( $item['link']['url'] );
					$el       = $has_link ? 'a' : 'div';
					$ckey     = 'ts_mcard_' . $rid;
					$this->add_render_attribute( $ckey, 'class', array( 'ts-mcard', 'elementor-repeater-item-' . $rid ) );
					if ( $has_link ) {
						$this->add_link_attributes( $ckey, $item['link'] );
					}
					?>
					<?php $rlevel = $this->rating_level( isset( $item['rating'] ) ? $item['rating'] : '', $rscale ); ?>
					<li class="ts-mcards__item"<?php echo $filter_on && ! empty( $item['group'] ) ? ' data-group="' . esc_attr( sanitize_title( $item['group'] ) ) . '"' : ''; ?><?php echo $key_filter && $rlevel ? ' data-level="' . (int) $rlevel . '"' : ''; ?>>
						<<?php echo esc_attr( $el ); ?> <?php echo $this->get_render_attribute_string( $ckey ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
							<div class="ts-mcard__media">
								<?php $this->render_picture( $item, $has_link ? '' : 'ts_mcard_pic_' . $rid ); ?>
								<?php if ( ! empty( $item['badge'] ) ) : ?>
									<span class="ts-mcard__badge ts-mcard__badge--<?php echo esc_attr( $badge_position ); ?>"><?php echo esc_html( $item['badge'] ); ?></span>
								<?php endif; ?>
							</div>
							<?php echo $this->image_caption( $item, 'image' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in image_caption(). ?>
							<div class="ts-mcard__body">
								<?php if ( ! empty( $item['kicker'] ) ) : ?>
									<span class="ts-mcard__kicker"><?php echo esc_html( $item['kicker'] ); ?></span>
								<?php endif; ?>
								<?php if ( ! empty( $item['title'] ) ) : ?>
									<<?php echo esc_attr( $tag ); ?> class="ts-mcard__title"><?php echo esc_html( $item['title'] ); ?></<?php echo esc_attr( $tag ); ?>>
								<?php endif; ?>
								<?php if ( ! empty( $item['text'] ) ) : ?>
									<p class="ts-mcard__text"><?php echo nl2br( esc_html( $item['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
								<?php endif; ?>
								<?php
								$tags = $this->lines( isset( $item['tags'] ) ? $item['tags'] : '' );
								if ( $tags ) {
									$tags_label = isset( $item['tags_label'] ) ? trim( (string) $item['tags_label'] ) : '';
									echo '<div class="ts-mcard__tags">';
									if ( '' !== $tags_label ) {
										echo '<p class="ts-mcard__tags-label">' . esc_html( $tags_label ) . '</p>';
									}
									echo '<ul class="ts-mcard__chips"><li>' . implode( '</li><li>', array_map( 'esc_html', $tags ) ) . '</li></ul></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
								}
								?>
								<?php
								$facts  = array();
								$rating = $this->rating_html( $rlevel, $rscale, $rlabel, $rlevel ? $this->rating_name( $s, $rlevel ) : '' );
								if ( '' !== $rating ) {
									$facts[] = '<dt>' . esc_html( $rlabel ) . '</dt><dd>' . $rating . '</dd>';
								}
								for ( $fn = 1; $fn <= 4; $fn++ ) {
									$fl = isset( $item[ 'fact' . $fn . '_label' ] ) ? trim( (string) $item[ 'fact' . $fn . '_label' ] ) : '';
									$fv = isset( $item[ 'fact' . $fn . '_value' ] ) ? trim( (string) $item[ 'fact' . $fn . '_value' ] ) : '';
									if ( '' !== $fl || '' !== $fv ) {
										$facts[] = '<dt>' . esc_html( $fl ) . '</dt><dd>' . esc_html( $fv ) . '</dd>';
									}
								}
								if ( $facts ) {
									echo '<dl class="ts-mcard__facts"><div class="ts-mcard__fact">' . implode( '</div><div class="ts-mcard__fact">', $facts ) . '</div></dl>'; // phpcs:ignore WordPress.Security.EscapeOutput
								}

								$tip = isset( $item['tip'] ) ? trim( (string) $item['tip'] ) : '';
								if ( '' !== $tip ) {
									$parts = array_map( 'trim', explode( '|', $tip, 2 ) );
									echo '<div class="ts-mcard__tip">';
									echo isset( $parts[1] ) ? '<b>' . esc_html( $parts[0] ) . '</b> ' . nl2br( esc_html( $parts[1] ) ) : nl2br( esc_html( $parts[0] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
									echo '</div>';
								}
								?>
								<?php if ( $has_link && ! empty( $item['link_text'] ) ) : ?>
									<span class="ts-mcard__link"><?php echo esc_html( $item['link_text'] ); ?></span>
								<?php endif; ?>
							</div>
						</<?php echo esc_attr( $el ); ?>>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Photo first, then bundled art, otherwise the gradient behind shows through.
	 */
	/**
	 * @param string $link_key Render key for the picture's own link; '' when the card itself is a link.
	 */
	private function render_picture( $item, $link_key ) {
		$anim = $this->image_anim_class();

		if ( ! empty( $item['image']['id'] ) ) {
			$img = $this->image_tag( $item, 'image', 'large', null, array( 'class' => 'ts-mcard__img', 'loading' => 'lazy' ) );
			echo '' !== $link_key ? $this->image_linked( $img, $item, 'image', $link_key ) : $img; // phpcs:ignore WordPress.Security.EscapeOutput
			return;
		}

		if ( ! empty( $item['image']['url'] ) && false === strpos( (string) $item['image']['url'], 'placeholder' ) ) {
			$img = sprintf( '<img class="ts-mcard__img%2$s" src="%1$s" alt="" loading="lazy" decoding="async" />', esc_url( $item['image']['url'] ), esc_attr( $anim ) );
			echo '' !== $link_key ? $this->image_linked( $img, $item, 'image', $link_key ) : $img; // phpcs:ignore WordPress.Security.EscapeOutput
			return;
		}

		if ( ! empty( $item['art'] ) ) {
			$url = \TravelStore\TS_Art::url( $item['art'] );
			if ( $url ) {
				printf( '<img class="ts-mcard__img%2$s" src="%1$s" alt="" loading="lazy" decoding="async" />', esc_url( $url ), esc_attr( $anim ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				return;
			}
		}

		// Colour blend only: optional letters (hidden from screen readers; the card title says who it is).
		$initials = isset( $item['initials'] ) ? trim( (string) $item['initials'] ) : '';
		if ( '' !== $initials ) {
			echo '<span class="ts-mcard__initials" aria-hidden="true">' . esc_html( $initials ) . '</span>';
		}
	}
}
