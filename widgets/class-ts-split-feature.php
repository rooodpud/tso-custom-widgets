<?php
/**
 * Widget: TS Split Feature — a picture and text side by side.
 *
 * One row per feature; rows can alternate sides (course-by-course lists) or a single
 * row can be a spotlight (a fort, an about section). Each row's text can carry a chip,
 * small caption, title, text, tags, key facts, a stats strip and a button. The picture
 * is a photo, a bundled art scene or a colour blend, shown plain or on a coloured panel.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Split_Feature extends TS_Base_Widget {

	const RATIOS = array( 'natural', '4-3', '3-2', '1-1', '16-9' );

	public function get_name() {
		return 'ts-split-feature';
	}

	public function get_title() {
		return __( 'TS Split Feature', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_keywords() {
		return array( 'split', 'feature', 'image text', 'side by side', 'alternating', 'spotlight', 'about', 'travel' );
	}

	public function get_style_depends() {
		// Tags/facts and the stats strip reuse the Info List and Stats looks.
		return array( 'ts-base', 'ts-stats', 'ts-info-list', $this->get_name() );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Rows', 'travelstore-custom-widgets' ) )
		);

		$r = new Repeater();
		$r->add_control(
			'picture_heading',
			array(
				'label' => __( 'Picture', 'travelstore-custom-widgets' ),
				'type'  => Controls_Manager::HEADING,
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
		// 1.9.4: as TS Art — draw the art in the page so words in it (place names) use the site fonts.
		$r->add_control(
			'art_inline',
			array(
				'label'        => __( 'Use the site fonts for text in the art', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'For art with words in it, such as a map with place names.', 'travelstore-custom-widgets' ),
				'condition'    => array( 'art!' => '' ),
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
				'default'   => '#4A3128',
				'selectors' => array( '{{WRAPPER}} {{CURRENT_ITEM}} .ts-split__frame' => '--ts-a: {{VALUE}};' ),
			)
		);
		$r->add_control(
			'color_b',
			array(
				'label'       => __( 'Colour blend 2', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#2C1D17',
				'description' => __( 'Fills the picture area when there is no photo or art, and colours the panel behind the picture when "Picture look" is Panel.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} {{CURRENT_ITEM}} .ts-split__frame' => '--ts-b: {{VALUE}};' ),
			)
		);
		$r->add_control(
			'text_heading',
			array(
				'label'     => __( 'Text', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$r->add_control(
			'tone',
			array(
				'label'   => __( 'Accent colour (chip and caption)', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'terracotta',
				'options' => self::tone_options(),
			)
		);
		$r->add_control(
			'chip',
			array(
				'label'   => __( 'Small label (chip)', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
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
			'checklist',
			array(
				'label'       => __( 'Checklist', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'description' => __( 'One per line, each with a tick. Shown under the text. Optional.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'tags',
			array(
				'label'       => __( 'Tags', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => __( 'One per line, shown as small pills. Optional.', 'travelstore-custom-widgets' ),
			)
		);
		// Key facts and figures are pairs of plain fields (REQ-015: no typed formats such as "Label | Value").
		$r->add_control(
			'facts_heading',
			array(
				'label'     => __( 'Key facts (optional, shown as rows)', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$fact_examples = array(
			1 => array( __( 'Holes', 'travelstore-custom-widgets' ), '18' ),
			2 => array( __( 'Par', 'travelstore-custom-widgets' ), '72' ),
			3 => array( __( 'Opened', 'travelstore-custom-widgets' ), '2012' ),
			4 => array( __( 'Drive from Muscat', 'travelstore-custom-widgets' ), __( '30 min', 'travelstore-custom-widgets' ) ),
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
			'stats_heading',
			array(
				'label'     => __( 'Figures (optional, shown as big numbers in a row)', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$stat_examples = array(
			1 => array( '15+', __( 'Years operating in Oman', 'travelstore-custom-widgets' ) ),
			2 => array( '20+', __( 'Local guides & drivers', 'travelstore-custom-widgets' ) ),
			3 => array( '4.8★', __( 'Average traveller rating', 'travelstore-custom-widgets' ) ),
			4 => array( '1668', __( 'Year completed', 'travelstore-custom-widgets' ) ),
		);
		for ( $n = 1; $n <= 4; $n++ ) {
			$r->add_control(
				'stat' . $n . '_value',
				array(
					/* translators: %d: figure number. */
					'label'       => sprintf( __( 'Figure %d – number', 'travelstore-custom-widgets' ), $n ),
					'type'        => Controls_Manager::TEXT,
					/* translators: %s: example figure. */
					'placeholder' => sprintf( __( 'For example: %s', 'travelstore-custom-widgets' ), $stat_examples[ $n ][0] ),
				)
			);
			$r->add_control(
				'stat' . $n . '_label',
				array(
					/* translators: %d: figure number. */
					'label'       => sprintf( __( 'Figure %d – words under it', 'travelstore-custom-widgets' ), $n ),
					'type'        => Controls_Manager::TEXT,
					/* translators: %s: example figure label. */
					'placeholder' => sprintf( __( 'For example: %s', 'travelstore-custom-widgets' ), $stat_examples[ $n ][1] ),
				)
			);
		}
		$r->add_control(
			'button_text',
			array(
				'label'       => __( 'Button text', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Leave empty to hide the button.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
				'separator'   => 'before',
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
		// 1.10.0 (Destinations page): arrow, a line of links, a number on the picture, colours for this row.
		$r->add_control(
			'button_arrow',
			array(
				'label'        => __( 'Arrow after the button text', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'The arrow moves a little when the button is hovered.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'pairs',
			array(
				'label'       => __( 'Line under the button (with links)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::WYSIWYG,
				'description' => __( 'Optional. For example: Pairs well with … Use the link button for links.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'badge',
			array(
				'label'       => __( 'Number or word on the picture', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Shown in the corner of the picture. Optional. A number fits the circle; for a few words, choose Style › Number or word on the picture › Shape: Pill.', 'travelstore-custom-widgets' ),
				'separator'   => 'before',
			)
		);
		$r->add_control(
			'row_colors_heading',
			array(
				'label'     => __( 'Colours for this row (optional)', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$r->add_control(
			'kicker_item_color',
			array(
				'label'     => __( 'Caption colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} {{CURRENT_ITEM}} .ts-split__kicker' => 'color: {{VALUE}};' ),
			)
		);
		$r->add_control(
			'button_item_bg',
			array(
				'label'     => __( 'Button colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} {{CURRENT_ITEM}} .ts-btn' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Rows', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'kicker' => __( 'Caption', 'travelstore-custom-widgets' ),
						'title'  => __( 'A feature worth a closer look', 'travelstore-custom-widgets' ),
						'text'   => __( 'Two or three sentences about this place or experience, and why it is worth the trip.', 'travelstore-custom-widgets' ),
						'tags'   => __( "Good for families\nHalf a day", 'travelstore-custom-widgets' ),
					),
					array(
						'kicker' => __( 'Caption', 'travelstore-custom-widgets' ),
						'title'  => __( 'A second feature', 'travelstore-custom-widgets' ),
						'text'   => __( 'Two or three sentences about this place or experience, and why it is worth the trip.', 'travelstore-custom-widgets' ),
						'tone'   => 'turquoise',
					),
				),
			)
		);
		$this->add_tag_control( 'title_tag', __( 'Title level', 'travelstore-custom-widgets' ), 'h3' );
		$this->add_control(
			'picture_side',
			array(
				'label'   => __( 'Picture on the', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => __( 'Left', 'travelstore-custom-widgets' ),
					'right' => __( 'Right', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'alternate',
			array(
				'label'        => __( 'Alternate sides row by row', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'facts_look',
			array(
				'label'       => __( 'Key facts look', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'rows',
				'options'     => array(
					'rows'  => __( 'Rows (name left, value right)', 'travelstore-custom-widgets' ),
					'boxes' => __( 'Small boxes (name above value)', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'How the "Key facts" of each row are shown.', 'travelstore-custom-widgets' ),
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
		$this->add_control(
			'picture_share',
			array(
				'label'                => __( 'Picture : text widths', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => '1-1',
				'options'              => array(
					'1-1'   => __( 'Half and half', 'travelstore-custom-widgets' ),
					'45-55' => __( 'Picture a little narrower (45 : 55)', 'travelstore-custom-widgets' ),
					'40-60' => __( 'Picture narrower (40 : 60)', 'travelstore-custom-widgets' ),
					'55-45' => __( 'Picture a little wider (55 : 45)', 'travelstore-custom-widgets' ),
					'60-40' => __( 'Picture wider (60 : 40)', 'travelstore-custom-widgets' ),
					'1-108' => __( 'Picture 1 : text 1.08 (Destinations rows)', 'travelstore-custom-widgets' ),
					'108-1' => __( 'Picture 1.08 : text 1 (Destinations rows)', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'1-1'   => '--ts-split-pic:1fr;--ts-split-txt:1fr;',
					'45-55' => '--ts-split-pic:.9fr;--ts-split-txt:1.1fr;',
					'40-60' => '--ts-split-pic:.8fr;--ts-split-txt:1.2fr;',
					'55-45' => '--ts-split-pic:1.1fr;--ts-split-txt:.9fr;',
					'60-40' => '--ts-split-pic:1.2fr;--ts-split-txt:.8fr;',
					'1-108' => '--ts-split-pic:1fr;--ts-split-txt:1.08fr;',
					'108-1' => '--ts-split-pic:1.08fr;--ts-split-txt:1fr;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-split' => '{{VALUE}}' ),
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
					'stack' => __( 'Picture and text stacked', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'side'  => 'grid-template-columns:var(--ts-split-cols);--ts-split-media-order:initial;',
					'stack' => 'grid-template-columns:minmax(0,1fr);--ts-split-media-order:var(--ts-split-stack-order,-1);',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-split__row' => '{{VALUE}}' ),
			)
		);
		$this->add_control(
			'stack_picture',
			array(
				'label'                => __( 'When stacked, picture goes', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'first',
				'options'              => array(
					'first' => __( 'Above the text', 'travelstore-custom-widgets' ),
					'last'  => __( 'Below the text', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'first' => '--ts-split-stack-order:-1;',
					'last'  => '--ts-split-stack-order:2;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-split' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'gap',
			array(
				'label'          => __( 'Space between picture and text', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'        => array( 'size' => 52, 'unit' => 'px' ),
				'tablet_default' => array( 'size' => 36, 'unit' => 'px' ),
				'mobile_default' => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .ts-split' => '--ts-split-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'vertical_align',
			array(
				'label'                => __( 'Vertical alignment', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'center',
				'options'              => array(
					'center' => __( 'Centred', 'travelstore-custom-widgets' ),
					'start'  => __( 'Top', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'center' => '--ts-split-align:center;',
					'start'  => '--ts-split-align:start;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-split' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'row_spacing',
			array(
				'label'      => __( 'Space between rows', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 140 ) ),
				'default'    => array( 'size' => 52, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-split' => '--ts-split-row-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'row_dividers',
			array(
				'label'        => __( 'Thin rule between rows', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'divider_color',
			array(
				'label'     => __( 'Rule colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-split' => '--ts-split-line: {{VALUE}};' ),
				'condition' => array( 'row_dividers' => 'yes' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: picture ---------------- */
		$this->start_controls_section(
			'ts_section_picture',
			array(
				'label' => __( 'Picture', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'picture_look',
			array(
				'label'       => __( 'Picture look', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'plain',
				'options'     => array(
					'plain' => __( 'Fill the frame (picture reaches the edges)', 'travelstore-custom-widgets' ),
					'panel' => __( 'Panel (picture on a coloured panel, with space around it)', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'Applies on desktop, tablet and phone. With Panel, the space around the picture is "Panel padding" below, which can differ per device (0 = the picture fills the panel).', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'ratio',
			array(
				'label'   => __( 'Frame shape', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4-3',
				'options' => array(
					'natural' => __( 'Natural (the picture\'s own shape)', 'travelstore-custom-widgets' ),
					'4-3'     => '4 : 3',
					'3-2'     => '3 : 2',
					'1-1'     => __( 'Square', 'travelstore-custom-widgets' ),
					'16-9'    => '16 : 9',
				),
			)
		);
		$this->add_responsive_control(
			'panel_padding',
			array(
				'label'      => __( 'Panel padding (space around the picture)', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-split' => '--ts-split-pad: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'picture_look' => 'panel' ),
			)
		);
		$this->add_responsive_control(
			'radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-split' => '--ts-split-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		// Picture selector strong enough to beat the frame-shape rules in ts-split-feature.css.
		$this->add_image_style_controls( '.ts-split .ts-split__frame .ts-split__img', '.ts-split__frame', '.ts-split .ts-split__frame:hover .ts-split__img' );
		$this->end_controls_section();

		$this->add_image_caption_style_section();

		/* ---------------- Style: text ---------------- */
		$this->start_controls_section(
			'ts_section_text_style',
			array(
				'label' => __( 'Text', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'kicker_typography', __( 'Caption typography', 'travelstore-custom-widgets' ), '.ts-split__kicker' );
		$this->add_color_control( 'kicker_color', __( 'Caption colour (all rows)', 'travelstore-custom-widgets' ), '.ts-split__kicker' );
		$this->add_typography_control( 'title_typography', __( 'Title typography', 'travelstore-custom-widgets' ), '.ts-split__title', 'ts_heading' );
		$this->add_color_control( 'title_color', __( 'Title colour', 'travelstore-custom-widgets' ), '.ts-split__title' );
		$this->add_typography_control( 'text_typography', __( 'Text typography', 'travelstore-custom-widgets' ), '.ts-split__text', 'ts_body' );
		$this->add_color_control( 'text_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-split__text' );
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
				'default'    => array( 'size' => 460, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-split__text' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_typography_control( 'check_typography', __( 'Checklist typography', 'travelstore-custom-widgets' ), '.ts-split__checklist .ts-info__label' );
		$this->add_color_control( 'check_color', __( 'Checklist text colour', 'travelstore-custom-widgets' ), '.ts-split__checklist .ts-info__label' );
		$this->add_color_control( 'check_tick_color', __( 'Tick colour', 'travelstore-custom-widgets' ), '.ts-split__checklist .ts-info__item::before', 'color', 'ts_oasis' );
		$this->add_control(
			'stats_heading',
			array(
				'label'     => __( 'Figures', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		// 1.9.4: the tag pills under the text (drafts differ: golf .cr-fact 12.5px, padding 8px 15px).
		$this->add_typography_control( 'tag_typography', __( 'Tag typography', 'travelstore-custom-widgets' ), '.ts-split__tags .ts-info__pill' );
		$this->add_responsive_control(
			'tag_padding',
			array(
				'label'      => __( 'Tag padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-split__tags .ts-info__pill' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		// 1.9.4: spacing of the figures (empty = 28px both ways).
		$this->add_responsive_control(
			'stats_gap',
			array(
				'label'      => __( 'Space between figures', 'travelstore-custom-widgets' ),
				'description' => __( 'The same space sits on both sides of the dividing line.', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-split__stats' => '--ts-stat-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'stats_row_gap',
			array(
				'label'      => __( 'Space between rows of figures', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-split__stats .ts-stats__list' => 'row-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_typography_control( 'stat_value_typography', __( 'Figure typography', 'travelstore-custom-widgets' ), '.ts-split__stats .ts-stat__value' );
		$this->add_color_control( 'stat_value_color', __( 'Figure colour', 'travelstore-custom-widgets' ), '.ts-split__stats .ts-stat__value' );
		$this->add_typography_control( 'stat_label_typography', __( 'Figure label typography', 'travelstore-custom-widgets' ), '.ts-split__stats .ts-stat__label' ); // 1.9.3
		$this->add_color_control( 'stat_label_color', __( 'Figure label colour', 'travelstore-custom-widgets' ), '.ts-split__stats .ts-stat__label' );
		$this->add_control(
			'colors_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Leave the colours empty to follow "Text colours" (Layout). The defaults come from Site Settings.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: key fact boxes, line under the button, number on the picture (1.10.0) ---------------- */
		$this->start_controls_section(
			'ts_section_extras_style',
			array(
				'label' => __( 'Fact boxes, links line and picture number', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'boxes_heading',
			array(
				'label' => __( 'Fact boxes (Key facts look: Small boxes)', 'travelstore-custom-widgets' ),
				'type'  => Controls_Manager::HEADING,
			)
		);
		$this->add_responsive_control(
			'boxes_columns',
			array(
				'label'          => __( 'Boxes per row', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'mobile_default' => '2',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors'      => array( '{{WRAPPER}} .ts-split__boxes' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ),
			)
		);
		$this->add_responsive_control(
			'boxes_gap',
			array(
				'label'      => __( 'Space between boxes', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-split__boxes' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'box_bg', __( 'Box colour', 'travelstore-custom-widgets' ), '.ts-split__box', 'background-color', 'ts_sand' );
		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Box corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-split__box' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Box padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array( 'top' => 11, 'right' => 13, 'bottom' => 11, 'left' => 13, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array( '{{WRAPPER}} .ts-split__box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_typography_control( 'box_label_typography', __( 'Box name typography', 'travelstore-custom-widgets' ), '.ts-split__box dt' );
		$this->add_color_control( 'box_label_color', __( 'Box name colour', 'travelstore-custom-widgets' ), '.ts-split__box dt' );
		$this->add_typography_control( 'box_value_typography', __( 'Box value typography', 'travelstore-custom-widgets' ), '.ts-split__box dd' );
		$this->add_color_control( 'box_value_color', __( 'Box value colour', 'travelstore-custom-widgets' ), '.ts-split__box dd' );
		$this->add_control(
			'pairs_heading',
			array(
				'label'     => __( 'Line under the button', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_typography_control( 'pairs_typography', __( 'Typography', 'travelstore-custom-widgets' ), '.ts-split__pairs' );
		$this->add_color_control( 'pairs_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-split__pairs' );
		$this->add_color_control( 'pairs_link', __( 'Link colour', 'travelstore-custom-widgets' ), '.ts-split__pairs a' );
		$this->add_color_control( 'pairs_link_line', __( 'Link underline colour', 'travelstore-custom-widgets' ), '.ts-split__pairs a', 'border-color' );
		$this->add_responsive_control(
			'pairs_space',
			array(
				'label'      => __( 'Space above', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-split__pairs' => 'margin-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'badge_heading',
			array(
				'label'     => __( 'Number or word on the picture', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		// 1.11.0: a pill for a few words ("Guided · small groups"); the circle stays the default.
		$this->add_control(
			'badge_shape',
			array(
				'label'   => __( 'Shape', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => array(
					'circle' => __( 'Circle (for a number)', 'travelstore-custom-widgets' ),
					'pill'   => __( 'Pill (for a few words)', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_responsive_control(
			'badge_size',
			array(
				'label'      => __( 'Circle size', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 100 ) ),
				'default'    => array( 'size' => 44, 'unit' => 'px' ),
				'condition'  => array( 'badge_shape!' => 'pill' ),
				'selectors'  => array( '{{WRAPPER}} .ts-split__badge:not(.ts-split__badge--pill)' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'badge_pill_padding',
			array(
				'label'      => __( 'Pill padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'condition'  => array( 'badge_shape' => 'pill' ),
				'selectors'  => array( '{{WRAPPER}} .ts-split__badge--pill' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'badge_offset',
			array(
				'label'      => __( 'Distance from the corner', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-split__badge' => 'top: {{SIZE}}{{UNIT}}; left: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'badge_bg', __( 'Circle / pill colour', 'travelstore-custom-widgets' ), '.ts-split__badge', 'background-color' );
		$this->add_color_control( 'badge_color', __( 'Number / word colour', 'travelstore-custom-widgets' ), '.ts-split__badge' );
		$this->add_typography_control( 'badge_typography', __( 'Number / word typography', 'travelstore-custom-widgets' ), '.ts-split__badge' );
		$this->end_controls_section();

		$this->add_button_style_section();
		$this->register_extra_css_controls();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = isset( $s['items'] ) && is_array( $s['items'] ) ? $s['items'] : array();

		if ( empty( $items ) ) {
			$this->render_empty( __( 'Add at least one row to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$light     = isset( $s['text_scheme'] ) && 'light' === $s['text_scheme'];
		$look      = isset( $s['picture_look'] ) && 'panel' === $s['picture_look'] ? 'panel' : 'plain';
		$ratio     = isset( $s['ratio'] ) && in_array( $s['ratio'], self::RATIOS, true ) ? $s['ratio'] : '4-3';
		$tag       = $this->safe_tag( isset( $s['title_tag'] ) ? $s['title_tag'] : 'h3', 'h3' );
		$first     = isset( $s['picture_side'] ) && 'right' === $s['picture_side'] ? 'right' : 'left';
		$alternate = ! empty( $s['alternate'] );

		$boxes   = isset( $s['facts_look'] ) && 'boxes' === $s['facts_look'];
		$badge_c = isset( $s['badge_shape'] ) && 'pill' === $s['badge_shape'] ? 'ts-split__badge ts-split__badge--pill' : 'ts-split__badge';
		$classes = array( 'ts-w', 'ts-split', 'ts-split--pic-' . $look, 'ts-split--ratio-' . $ratio );
		if ( ! empty( $s['row_dividers'] ) ) {
			$classes[] = 'ts-split--dividers';
		}
		if ( $light ) {
			$classes[] = 'ts-split--light';
			$classes[] = 'ts-on-dark';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php
			foreach ( $items as $i => $item ) {
				$rid  = ! empty( $item['_id'] ) ? sanitize_html_class( $item['_id'] ) : 'i' . $i;
				$side = $first;
				if ( $alternate && 1 === $i % 2 ) {
					$side = 'left' === $first ? 'right' : 'left';
				}
				$tone = $this->safe_tone( isset( $item['tone'] ) ? $item['tone'] : 'terracotta', 'terracotta' );
				?>
				<div class="ts-split__row ts-split__row--pic-<?php echo esc_attr( $side ); ?> ts-tone--<?php echo esc_attr( $tone ); ?> elementor-repeater-item-<?php echo esc_attr( $rid ); ?>">
					<div class="ts-split__media">
						<div class="ts-split__frame"><?php $this->render_picture( $item, $rid ); ?><?php echo isset( $item['badge'] ) && '' !== trim( (string) $item['badge'] ) ? '<span class="' . esc_attr( $badge_c ) . '">' . esc_html( trim( $item['badge'] ) ) . '</span>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<?php echo $this->image_caption( $item, 'image' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in image_caption(). ?>
					</div>
					<div class="ts-split__content">
						<?php
						echo $this->chip_html( isset( $item['chip'] ) ? $item['chip'] : '', $tone, true, 'ts-split__chip' ); // phpcs:ignore WordPress.Security.EscapeOutput
						if ( ! empty( $item['kicker'] ) ) {
							echo '<div class="ts-split__kicker">' . esc_html( $item['kicker'] ) . '</div>';
						}
						if ( ! empty( $item['title'] ) ) {
							printf( '<%1$s class="ts-split__title">%2$s</%1$s>', esc_attr( $tag ), nl2br( esc_html( $item['title'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
						}
						if ( ! empty( $item['text'] ) ) {
							echo '<p class="ts-split__text">' . nl2br( esc_html( $item['text'] ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
						}

						$checks = array();
						foreach ( $this->lines( isset( $item['checklist'] ) ? $item['checklist'] : '' ) as $line ) {
							$checks[] = array( 'label' => $line, 'value' => '', 'highlight' => false );
						}
						if ( $checks ) {
							echo '<div class="ts-split__block ts-split__checklist ts-info-wrap--tick">';
							TS_Info_List::print_list( $checks, 'bullets', $light );
							echo '</div>';
						}

						ob_start(); // 1.10.0: with the boxes look the facts come before the tags (Destinations draft).
						$tags = array();
						foreach ( $this->lines( isset( $item['tags'] ) ? $item['tags'] : '' ) as $line ) {
							$tags[] = array( 'label' => $line, 'value' => '', 'highlight' => false );
						}
						if ( $tags ) {
							echo '<div class="ts-split__block ts-split__tags">';
							TS_Info_List::print_list( $tags, 'tags', $light );
							echo '</div>';
						}

						$tags_html = (string) ob_get_clean();
						if ( ! $boxes ) {
							echo $tags_html; // phpcs:ignore WordPress.Security.EscapeOutput
						}

						$facts = array();
						foreach ( $this->pairs( $item, 'fact', 'label', 'value', 'facts' ) as $pair ) {
							$facts[] = array( 'label' => $pair[0], 'value' => $pair[1], 'highlight' => false );
						}
						if ( $facts && $boxes ) {
							echo '<dl class="ts-split__block ts-split__boxes">';
							foreach ( $facts as $fact ) {
								echo '<div class="ts-split__box"><dt>' . esc_html( $fact['label'] ) . '</dt><dd>' . esc_html( $fact['value'] ) . '</dd></div>';
							}
							echo '</dl>';
							echo $tags_html; // phpcs:ignore WordPress.Security.EscapeOutput
						} elseif ( $facts ) {
							echo '<div class="ts-split__block ts-split__facts">';
							TS_Info_List::print_list( $facts, 'rows', $light );
							echo '</div>';
						}

						if ( $boxes && ! $facts ) {
							echo $tags_html; // phpcs:ignore WordPress.Security.EscapeOutput
						}

						$stats = $this->pairs( $item, 'stat', 'value', 'label', 'stats' );
						if ( $stats ) {
							echo '<div class="ts-split__block ts-split__stats ts-stats ts-stats--dividers' . ( $light ? ' ts-stats--light' : '' ) . '">';
							TS_Stats::print_list( $stats );
							echo '</div>';
						}

						if ( ! empty( $item['button_text'] ) ) {
							echo '<div class="ts-split__actions">';
							ob_start();
							$this->render_button( $item, 'button', '_' . $rid );
							$button = (string) ob_get_clean();
							if ( ! empty( $item['button_arrow'] ) ) {
								$arrow  = '<svg class="ts-btn__arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
								$button = preg_replace( '#</a>\s*$#', $arrow . '</a>', $button, 1 );
								$button = preg_replace( '#class="ts-btn #', 'class="ts-btn ts-btn--arrow ', $button, 1 );
							}
							echo $button; // phpcs:ignore WordPress.Security.EscapeOutput -- built by render_button(), escaped there.
							echo '</div>';
						}
						$pairs = isset( $item['pairs'] ) ? trim( (string) $item['pairs'] ) : '';
						if ( '' !== $pairs ) {
							echo '<div class="ts-split__pairs">' . $this->mark_external_links( wp_kses_post( $pairs ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
						}
						?>
					</div>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Photo first, then bundled art; otherwise the colour blend behind shows through.
	 */
	/**
	 * Filled pairs from the numbered fields ($prefix1_$first … $prefix4_$second), in order.
	 * Items saved before 1.6.2 kept one "a | b" per line in $legacy; that is still read when no field is filled.
	 *
	 * @return array[] Each array( first, second ).
	 */
	private function pairs( $item, $prefix, $first, $second, $legacy ) {
		$out = array();
		for ( $n = 1; $n <= 4; $n++ ) {
			$a = isset( $item[ $prefix . $n . '_' . $first ] ) ? trim( (string) $item[ $prefix . $n . '_' . $first ] ) : '';
			$b = isset( $item[ $prefix . $n . '_' . $second ] ) ? trim( (string) $item[ $prefix . $n . '_' . $second ] ) : '';
			if ( '' !== $a || '' !== $b ) {
				$out[] = array( $a, $b );
			}
		}
		if ( ! $out && ! empty( $item[ $legacy ] ) ) {
			foreach ( $this->lines( $item[ $legacy ] ) as $line ) {
				$parts = array_map( 'trim', explode( '|', $line, 2 ) );
				$out[] = array( $parts[0], isset( $parts[1] ) ? $parts[1] : '' );
			}
		}
		return $out;
	}

	private function render_picture( $item, $rid ) {
		$alt = isset( $item['alt'] ) ? (string) $item['alt'] : '';
		$key = 'ts_split_pic_' . $rid;

		if ( ! empty( $item['image']['id'] ) ) {
			$img = $this->image_tag( $item, 'image', 'large', $alt, array( 'class' => 'ts-split__img', 'loading' => 'lazy' ) );
			echo $this->image_linked( $img, $item, 'image', $key ); // phpcs:ignore WordPress.Security.EscapeOutput
			return;
		}

		$no_photo = empty( $item['image']['url'] ) || false !== strpos( (string) $item['image']['url'], 'placeholder' );
		if ( $no_photo && ! empty( $item['art'] ) && ! empty( $item['art_inline'] ) ) {
			$svg = \TravelStore\TS_Art::inline_svg( $item['art'], $this->get_id() . $rid, $alt );
			if ( '' !== $svg ) {
				echo '<span class="ts-split__img ts-split__svg' . esc_attr( $this->image_anim_class() ) . '">' . $svg . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput -- bundled plugin file, cleaned in inline_svg().
				return;
			}
		}

		$url   = '';
		$size  = array();
		$photo = false;
		if ( ! empty( $item['image']['url'] ) && false === strpos( (string) $item['image']['url'], 'placeholder' ) ) {
			$url   = (string) $item['image']['url'];
			$photo = true;
		} elseif ( ! empty( $item['art'] ) ) {
			$url  = (string) \TravelStore\TS_Art::url( $item['art'] );
			$size = \TravelStore\TS_Art::size( $item['art'] );
		}

		if ( $url ) {
			// Width/height let the browser reserve the space before a lazy picture arrives.
			$img = sprintf(
				'<img class="ts-split__img%4$s" src="%1$s" alt="%2$s"%3$s loading="lazy" decoding="async" />',
				esc_url( $url ),
				esc_attr( $alt ),
				$size ? ' width="' . (int) $size[0] . '" height="' . (int) $size[1] . '"' : '',
				esc_attr( $this->image_anim_class() )
			);
			echo $photo ? $this->image_linked( $img, $item, 'image', $key ) : $img; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
}
