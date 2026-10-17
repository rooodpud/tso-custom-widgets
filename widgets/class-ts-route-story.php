<?php
/**
 * Widget: TS Route Story — a multi-day route told as you scroll.
 *
 * Wide screens: the section stays in place while the days play on the left (day card, then an
 * "on the road" panel with a small drawing of each drive) and the whole route fills in on a map
 * on the right. Phones: one column; the overview map shrinks into a corner while the days play,
 * then grows back into the finished route. Visitors who turn animation off get a plain list.
 *
 * The map comes from a route bundled in the plugin (assets/routes/*.json, see TS_Routes);
 * everything else — days, drives, texts, colours, fonts — is a control. One script
 * (assets/js/ts-route-story.js) drives every Route Story on the page.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use TravelStore\TS_Routes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Route_Story extends TS_Base_Widget {

	public function get_name() {
		return 'ts-route-story';
	}

	public function get_title() {
		return __( 'TS Route Story', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-map-pin';
	}

	public function get_keywords() {
		return array( 'route', 'itinerary', 'map', 'days', 'scroll', 'journey', 'travel' );
	}

	public function get_script_depends() {
		return array( 'ts-route-story' );
	}

	/** The Classic Loop days, as on the Home page. */
	private function default_days() {
		$rows = array(
			array( 'Muscat', 'Grand Mosque, the corniche and a first Omani dinner.', 'rgba(56,37,32,.05)', '', '', '' ),
			array( 'Nizwa', "Nizwa Fort, the Friday goat souq, and Bahla's mudbrick walls.", 'rgba(243,105,42,.07)', 'West-southwest', '171 km', '~1h 50min' ),
			array( 'Jabal Akhdar', 'Terraced villages, rose gardens and mountain-edge views.', 'rgba(56,168,51,.08)', 'Northeast, via mountain switchbacks', '56 km', '~50 min' ),
			array( 'Wahiba Sands', 'Dune driving into camp, dinner under a genuinely dark sky.', 'rgba(255,195,0,.10)', 'East-southeast', '200 km', '~2h 55min' ),
			array( 'Wadi Bani Khalid', 'A swim in turquoise pools on the way to the coast.', 'rgba(3,146,204,.08)', 'Northeast', '51 km', '~1 hr' ),
			array( 'Sur', 'Dhow-building yards and a slow coastal drive home.', 'rgba(13,118,171,.08)', 'East', '81 km', '~1h 40min' ),
			array( 'Muscat', 'A free morning before your flight out.', 'rgba(56,37,32,.05)', 'Northwest', '218 km', '~2h 40min' ),
		);
		return array_map(
			function ( $r ) {
				return array( 'place' => $r[0], 'desc' => $r[1], 'tint' => $r[2], 'bearing' => $r[3], 'km' => $r[4], 'time' => $r[5] );
			},
			$rows
		);
	}

	protected function register_controls() {
		/* ---------------- Content: route and days ---------------- */
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Route and days', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'route',
			array(
				'label'       => __( 'Route (map)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'classic-loop',
				'options'     => TS_Routes::options(),
				'description' => __( 'Routes are drawn from a KML file and bundled with the plugin. Each stop on the route is one day below, in order.', 'travelstore-custom-widgets' ),
			)
		);

		$r = new Repeater();
		$r->add_control(
			'place',
			array(
				'label'       => __( 'Place', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Also the label on the map.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'desc',
			array(
				'label'   => __( 'Description', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'dynamic' => array( 'active' => true ),
			)
		);
		$r->add_control(
			'photo',
			array(
				'label'       => __( 'Photo (optional)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Shown softly behind the day while it is on screen. Without a photo the colour tint is used.', 'travelstore-custom-widgets' ),
			)
		);
		// The photo is a faded background behind the scene, so only the resolution applies (no caption or link).
		$this->add_image_options( $r, 'photo', array( 'caption' => false, 'link' => false ) );
		$r->add_control(
			'tint',
			array(
				'label'   => __( 'Colour tint', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::COLOR,
				'default' => 'rgba(56,37,32,.05)',
			)
		);
		$r->add_control(
			'drive_heading',
			array(
				'label'       => __( 'Drive to this place', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::HEADING,
				'separator'   => 'before',
				'description' => __( 'Leave empty on the first day.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'bearing',
			array(
				'label'       => __( 'Direction', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Northeast', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'km',
			array(
				'label'       => __( 'Distance', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '171 km',
			)
		);
		$r->add_control(
			'time',
			array(
				'label'       => __( 'Drive time', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '~1h 50min',
			)
		);
		$this->add_control(
			'days',
			array(
				'label'       => __( 'Days', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ place }}}',
				'default'     => $this->default_days(),
			)
		);
		// Day label and arrival line are built from plain words (REQ-015: no "{n}" placeholders to type).
		$this->add_control(
			'day_word',
			array(
				'label'       => __( 'Day label – word before the day number', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Day', 'travelstore-custom-widgets' ),
				'separator'   => 'before',
			)
		);
		$this->add_control(
			'day_of_word',
			array(
				'label'       => __( 'Day label – word before the number of days', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'of', 'travelstore-custom-widgets' ),
				'description' => __( 'The label reads, for example: Day 3 of 7. Leave this empty to show only "Day 3".', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'first_day_line',
			array(
				'label'   => __( 'First-day line', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Starting point', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'arrival_before',
			array(
				'label'   => __( 'Arrival line – words before the distance', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Arrived ·', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'arrival_from',
			array(
				'label'       => __( 'Arrival line – word before the place before', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'from', 'travelstore-custom-widgets' ),
				'description' => __( 'The line reads, for example: Arrived · 171 km from Nizwa. The distance and the place come from each day.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'travel_label',
			array(
				'label'   => __( 'Drive panel label', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'On the road:', 'travelstore-custom-widgets' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Content: intro ---------------- */
		$this->start_controls_section(
			'ts_intro',
			array( 'label' => __( 'Intro above the route', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'show_intro',
			array(
				'label'        => __( 'Show the intro', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$intro_cond = array( 'show_intro' => 'yes' );
		$this->add_control(
			'intro_chip',
			array(
				'label'     => __( 'Small label (chip)', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'A classic route, day by day', 'travelstore-custom-widgets' ),
				'condition' => $intro_cond,
			)
		);
		$this->add_control(
			'intro_chip_tone',
			array(
				'label'     => __( 'Chip colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'oasis',
				'options'   => self::tone_options(),
				'condition' => $intro_cond,
			)
		);
		$this->add_control(
			'intro_heading',
			array(
				'label'     => __( 'Heading', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'default'   => __( '7 Days: The Classic Loop', 'travelstore-custom-widgets' ),
				'condition' => $intro_cond,
			)
		);
		$this->add_control(
			'intro_heading_tag',
			array(
				'label'     => __( 'Heading level', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => $this->heading_tag_options(),
				'condition' => $intro_cond,
			)
		);
		$this->add_control(
			'intro_text',
			array(
				'label'     => __( 'Text', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'default'   => __( "Keep scrolling — you'll travel it one leg at a time, the way our guests actually do.", 'travelstore-custom-widgets' ),
				'condition' => $intro_cond,
			)
		);
		$this->add_control(
			'intro_hint',
			array(
				'label'       => __( 'Scroll hint', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Scroll to travel the route', 'travelstore-custom-widgets' ),
				'description' => __( 'Shown with a small bouncing arrow. Leave empty to hide.', 'travelstore-custom-widgets' ),
				'condition'   => $intro_cond,
			)
		);
		$this->end_controls_section();

		/* ---------------- Content: map and phone texts ---------------- */
		$this->start_controls_section(
			'ts_texts',
			array( 'label' => __( 'Map and phone texts', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'map_title',
			array(
				'label'   => __( 'Map title (wide screens)', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'The whole loop', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'map_subtitle',
			array(
				'label'   => __( 'Map line beside the title', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '7 days · ≈780 km · Muscat → Muscat', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'map_label',
			array(
				'label'       => __( 'Map description (screen readers)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Full route from Muscat via Nizwa, Jabal Akhdar, Wahiba Sands, Wadi Bani Khalid and Sur, back to Muscat', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'phone_heading',
			array(
				'label'     => __( 'Phones: start', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'phone_intro_over',
			array(
				'label'   => __( 'Small line', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Muscat → Muscat', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'phone_intro_title',
			array(
				'label'   => __( 'Title', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( "Here's the whole loop.", 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'phone_intro_text',
			array(
				'label'   => __( 'Text', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( "A circle of the north — old forts, mountain terraces, desert dunes and the coast — and you'll drive it day by day.", 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'phone_intro_hint',
			array(
				'label'   => __( 'Scroll hint', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Start scrolling', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'phone_end_heading',
			array(
				'label'     => __( 'Phones: end', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'phone_finale_over',
			array(
				'label'   => __( 'Small line', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Day 7', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'phone_finale_title',
			array(
				'label'   => __( 'Title', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Back where you started.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'phone_finale_caption',
			array(
				'label'       => __( 'Route line', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Muscat → Nizwa → Jabal Akhdar → Wahiba → Wadi Bani Khalid → Sur → Muscat', 'travelstore-custom-widgets' ),
				'description' => __( 'Leave empty to list the places automatically.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'phone_map_heading',
			array(
				'label'     => __( 'Phones: corner map', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'mini_caption',
			array(
				'label'   => __( 'Caption under the corner map', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'you are here', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'mini_on',
			array(
				'label'   => __( 'Map button (map shown)', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'map on', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'mini_off',
			array(
				'label'   => __( 'Map button (map hidden)', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'map off', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'static_heading',
			array(
				'label'     => __( 'Reduced motion', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'static_title',
			array(
				'label'       => __( 'Title of the plain list', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'The Classic Loop, at a glance', 'travelstore-custom-widgets' ),
				'description' => __( 'Visitors who switch animation off see the days as a plain list.', 'travelstore-custom-widgets' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: layout and scrolling ---------------- */
		$this->start_controls_section(
			'ts_section_layout_style',
			array(
				'label' => __( 'Layout and scrolling', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'switch_width',
			array(
				'label'       => __( 'Phone layout below (px)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 600,
				'max'         => 1600,
				'step'        => 10,
				'default'     => 1100,
				'description' => __( 'Below this screen width the one-column phone layout is used.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'scroll_per_day',
			array(
				'label'       => __( 'Scrolling per day', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0.5, 'max' => 2, 'step' => 0.1 ) ),
				'default'     => array( 'size' => 1, 'unit' => 'px' ),
				'description' => __( 'In screen heights: 1 = one screen of scrolling for each day.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'header_offset',
			array(
				'label'       => __( 'Space for a sticky header (px)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 300,
				'default'     => 0,
				'description' => __( 'If the site header stays on screen while scrolling, enter its height. The WordPress admin bar is allowed for automatically.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'photo_strength',
			array(
				'label'      => __( 'Photo strength', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array( '%' => array( 'min' => 5, 'max' => 100 ) ),
				'default'    => array( 'size' => 22, 'unit' => '%' ),
				'selectors'  => array( '{{WRAPPER}} .tsr' => '--tsr-photo: {{SIZE}}%;' ),
				'description' => __( 'How strongly a day photo shows behind the text and map.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_responsive_control(
			'intro_padding',
			array(
				'label'      => __( 'Intro padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .tsr-intro' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};' ),
				'allowed_dimensions' => array( 'top', 'bottom' ),
				'condition'  => array( 'show_intro' => 'yes' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: colours ---------------- */
		$this->start_controls_section(
			'ts_section_colors_style',
			array(
				'label' => __( 'Colours', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_color_control( 'bg_color', __( 'Background', 'travelstore-custom-widgets' ), '.tsr', '--tsr-bg', 'ts_sand' );
		$this->add_color_control( 'accent_color', __( 'Route and progress colour', 'travelstore-custom-widgets' ), '.tsr', '--tsr-accent', 'ts_terracotta' );
		$this->add_color_control( 'accent_dark_color', __( 'Day label colour', 'travelstore-custom-widgets' ), '.tsr', '--tsr-accent-d', 'ts_terracotta_dark' );
		$this->add_color_control( 'ink_color', __( 'Titles and map text', 'travelstore-custom-widgets' ), '.tsr', '--tsr-ink', 'ts_espresso' );
		$this->add_color_control( 'muted_color', __( 'Body text', 'travelstore-custom-widgets' ), '.tsr', '--tsr-muted', 'ts_espresso_70' );
		$this->add_color_control( 'soft_color', __( 'Small labels', 'travelstore-custom-widgets' ), '.tsr', '--tsr-soft' );
		$this->add_color_control( 'chip_text_color', __( 'Arrival line text', 'travelstore-custom-widgets' ), '.tsr', '--tsr-chip-fg', 'ts_turquoise_deep' );
		$this->add_color_control( 'chip_bg_color', __( 'Arrival line background', 'travelstore-custom-widgets' ), '.tsr', '--tsr-chip-bg' );
		$this->add_color_control( 'paper_color', __( 'Map stops and corner map', 'travelstore-custom-widgets' ), '.tsr', '--tsr-paper', 'ts_cream' );
		$this->add_color_control( 'line_color', __( 'Thin lines', 'travelstore-custom-widgets' ), '.tsr', '--tsr-line' );
		$this->end_controls_section();

		/* ---------------- Style: text ---------------- */
		$this->start_controls_section(
			'ts_section_text_style',
			array(
				'label' => __( 'Text', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'intro_heading_typography', __( 'Intro heading', 'travelstore-custom-widgets' ), '.tsr-intro__title', 'ts_heading' );
		$this->add_typography_control( 'intro_text_typography', __( 'Intro text', 'travelstore-custom-widgets' ), '.tsr-intro__text', 'ts_body' );
		$this->add_typography_control( 'daytag_typography', __( 'Day label', 'travelstore-custom-widgets' ), '.tsr-daytag' );
		$this->add_typography_control( 'place_typography', __( 'Place', 'travelstore-custom-widgets' ), '.tsr-place', 'ts_heading' );
		$this->add_typography_control( 'desc_typography', __( 'Description', 'travelstore-custom-widgets' ), '.tsr-desc', 'ts_body' );
		$this->add_typography_control( 'dist_typography', __( 'Arrival line', 'travelstore-custom-widgets' ), '.tsr-dist' );
		$this->add_typography_control( 'mapcap_typography', __( 'Map title', 'travelstore-custom-widgets' ), '.tsr-mapcap__title' );
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	/* ------------------------------------------------------------------ */

	private function fill( $pattern, $vars ) {
		return strtr( (string) $pattern, $vars );
	}

	/** Joins the non-empty parts with spaces. */
	private function words( $parts ) {
		return implode( ' ', array_filter( array_map( 'trim', array_map( 'strval', $parts ) ), 'strlen' ) );
	}

	/**
	 * "Day 3 of 7". Widgets saved before 1.6.2 may hold a typed "{n} … {total}" pattern; that is still used.
	 */
	private function day_tag( $s, $raw, $day, $total ) {
		if ( isset( $raw['day_tag_pattern'] ) && '' !== (string) $raw['day_tag_pattern'] ) {
			return $this->fill( $raw['day_tag_pattern'], array( '{n}' => $day, '{total}' => $total ) );
		}
		$of = isset( $s['day_of_word'] ) ? trim( (string) $s['day_of_word'] ) : '';
		return $this->words( array( isset( $s['day_word'] ) ? $s['day_word'] : '', $day, '' !== $of ? $of . ' ' . $total : '' ) );
	}

	/**
	 * "Arrived · 171 km from Nizwa". Older typed "{distance} … {previous}" patterns are still used.
	 */
	private function arrival_line( $s, $raw, $km, $prev ) {
		if ( isset( $raw['arrival_pattern'] ) && '' !== (string) $raw['arrival_pattern'] ) {
			return $this->fill( $raw['arrival_pattern'], array( '{distance}' => $km, '{previous}' => $prev ) );
		}
		$from = isset( $s['arrival_from'] ) ? trim( (string) $s['arrival_from'] ) : '';
		return $this->words( array( isset( $s['arrival_before'] ) ? $s['arrival_before'] : '', $km, '' !== trim( $prev ) ? $this->words( array( $from, $prev ) ) : '' ) );
	}

	/**
	 * The route's line as one continuous path (the reveal needs a single subpath).
	 */
	private function continuous_line( $line ) {
		// "… L x,y M x,y L …" → "… L x,y L …": each leg starts where the previous one ended.
		return preg_replace( '/\s+M[^L]*L/', ' L', trim( (string) $line ) );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		// Stored settings as saved, to read the typed patterns of widgets saved before 1.6.2 (no longer controls).
		$raw   = (array) $this->get_data( 'settings' );
		$route = TS_Routes::get( isset( $s['route'] ) ? $s['route'] : '' );
		$days  = isset( $s['days'] ) && is_array( $s['days'] ) ? array_values( $s['days'] ) : array();

		if ( ! $route ) {
			$this->render_empty( __( 'Choose a route for this widget.', 'travelstore-custom-widgets' ) );
			return;
		}
		$stops = count( $route['stops'] );
		if ( count( $days ) !== $stops ) {
			/* translators: 1: stops on the route, 2: days entered */
			$this->render_empty( sprintf( __( 'This route has %1$d stops, so it needs %1$d days (there are %2$d). Add or remove days to match.', 'travelstore-custom-widgets' ), $stops, count( $days ) ) );
			return;
		}

		$n    = $stops;
		$data = array();
		foreach ( $days as $i => $day ) {
			$place = isset( $day['place'] ) ? (string) $day['place'] : '';
			$prev  = $i > 0 && isset( $days[ $i - 1 ]['place'] ) ? (string) $days[ $i - 1 ]['place'] : '';
			$photo = $this->image_url( $day, 'photo', 'large' );
			$data[] = array(
				'tag'   => $this->day_tag( $s, $raw, $i + 1, $n ),
				'place' => $place,
				'desc'  => isset( $day['desc'] ) ? (string) $day['desc'] : '',
				'dist'  => 0 === $i ? (string) $s['first_day_line'] : $this->arrival_line( $s, $raw, isset( $day['km'] ) ? (string) $day['km'] : '', $prev ),
				'tint'  => isset( $day['tint'] ) && '' !== $day['tint'] ? (string) $day['tint'] : 'transparent',
				'photo' => $photo ? esc_url_raw( $photo ) : '',
			);
		}
		$legs = array();
		for ( $k = 0; $k < $n - 1; $k++ ) {
			$next   = $days[ $k + 1 ];
			$legs[] = array(
				'bearing' => isset( $next['bearing'] ) ? (string) $next['bearing'] : '',
				'km'      => isset( $next['km'] ) ? (string) $next['km'] : '',
				'time'    => isset( $next['time'] ) ? (string) $next['time'] : '',
				'mini'    => (string) $route['legs'][ $k ]['mini'],
				'end'     => (float) $route['legs'][ $k ]['share_end'],
			);
		}
		$pts = array();
		foreach ( $route['stops'] as $st ) {
			$pts[] = array(
				'x'  => (float) $st['x'],
				'y'  => (float) $st['y'],
				'dx' => isset( $st['label']['dx'] ) ? (float) $st['label']['dx'] : 0,
				'dy' => isset( $st['label']['dy'] ) ? (float) $st['label']['dy'] : -12,
				'a'  => isset( $st['label']['anchor'] ) ? (string) $st['label']['anchor'] : 'middle',
			);
		}
		$config = array(
			'days'   => $data,
			'legs'   => $legs,
			'stops'  => $pts,
			'aspect' => (float) $route['width'] / (float) $route['height'],
			'bp'     => max( 600, (int) ( isset( $s['switch_width'] ) ? $s['switch_width'] : 1100 ) ),
			'scroll' => isset( $s['scroll_per_day']['size'] ) && '' !== $s['scroll_per_day']['size'] ? max( 0.5, min( 2, (float) $s['scroll_per_day']['size'] ) ) : 1,
			'offset' => isset( $s['header_offset'] ) ? max( 0, (int) $s['header_offset'] ) : 0,
			'miniOn' => (string) $s['mini_on'],
			'miniOff' => (string) $s['mini_off'],
		);

		$box     = '0 0 ' . (float) $route['width'] . ' ' . (float) $route['height'];
		$line    = (string) $route['line'];
		$cont    = $this->continuous_line( $line );
		$first   = $data[0];
		$first_l = $legs ? $legs[0] : array( 'bearing' => '', 'km' => '', 'time' => '' );
		$finale  = trim( (string) $s['phone_finale_caption'] );
		if ( '' === $finale ) {
			$finale = implode( ' → ', wp_list_pluck( $data, 'place' ) );
		}
		$dots = str_repeat( '<span></span>', $n );
		$hint_svg = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12l7 7 7-7" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></svg>';
		?>
		<div class="ts-w tsr" data-tsr="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
			<?php if ( ! empty( $s['show_intro'] ) ) : ?>
				<div class="tsr-intro">
					<div class="tsr-wrap">
						<div class="tsr-intro__head">
							<?php
							echo $this->chip_html( $s['intro_chip'], $s['intro_chip_tone'], true, 'tsr-intro__chip' ); // phpcs:ignore WordPress.Security.EscapeOutput
							if ( '' !== trim( (string) $s['intro_heading'] ) ) {
								$tag = $this->safe_tag( $s['intro_heading_tag'], 'h2' );
								printf( '<%1$s class="tsr-intro__title">%2$s</%1$s>', esc_attr( $tag ), nl2br( esc_html( $s['intro_heading'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
							}
							if ( '' !== trim( (string) $s['intro_text'] ) ) {
								echo '<p class="tsr-intro__text">' . nl2br( esc_html( $s['intro_text'] ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
							}
							if ( '' !== trim( (string) $s['intro_hint'] ) ) {
								echo '<div class="tsr-hint">' . $hint_svg . esc_html( $s['intro_hint'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
							}
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<div class="tsr-route">
				<?php /* Wide screens: two columns. */ ?>
				<div class="tsr-desk" aria-label="<?php esc_attr_e( 'Route overview and day-by-day itinerary', 'travelstore-custom-widgets' ); ?>">
					<div class="tsr-scroller">
						<div class="tsr-pin">
							<?php $this->render_backdrop( $data ); ?>
							<div class="tsr-dwrap tsr-pin-inner">
								<div class="tsr-grid">
									<div class="tsr-colleft">
										<div class="tsr-dots" aria-hidden="true"><?php echo $dots; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
										<div class="tsr-stage tsr-dstage">
											<?php $this->render_stage( $first, $first_l, $data, $s ); ?>
										</div>
									</div>
									<div class="tsr-colright">
										<div class="tsr-mapcap">
											<div class="tsr-mapcap__title"><?php echo esc_html( $s['map_title'] ); ?></div>
											<div class="tsr-mapcap__sub"><?php echo esc_html( $s['map_subtitle'] ); ?></div>
										</div>
										<div class="tsr-mapframe">
											<svg class="tsr-map tsr-map--desk" viewBox="<?php echo esc_attr( $box ); ?>" role="img" aria-label="<?php echo esc_attr( $s['map_label'] ); ?>">
												<path class="tsr-map-line" d="<?php echo esc_attr( $line ); ?>"></path>
												<path class="tsr-map-fill" d="<?php echo esc_attr( $cont ); ?>"></path>
											</svg>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php /* Phones: one continuous journey. */ ?>
				<div class="tsr-mob" aria-label="<?php esc_attr_e( 'Full route overview, then day-by-day itinerary', 'travelstore-custom-widgets' ); ?>">
					<div class="tsr-scene">
						<div class="tsr-pin">
							<?php $this->render_backdrop( $data ); ?>
							<div class="tsr-minner">
								<div class="tsr-mintro">
									<div class="tsr-big-over"><?php echo esc_html( $s['phone_intro_over'] ); ?></div>
									<h3 class="tsr-big-title"><?php echo esc_html( $s['phone_intro_title'] ); ?></h3>
									<p class="tsr-big-sub"><?php echo esc_html( $s['phone_intro_text'] ); ?></p>
									<?php if ( '' !== trim( (string) $s['phone_intro_hint'] ) ) : ?>
										<div class="tsr-hint tsr-hint--center"><?php echo $hint_svg . esc_html( $s['phone_intro_hint'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
									<?php endif; ?>
								</div>
								<div class="tsr-stagepad" style="opacity:0;">
									<div class="tsr-rail">
										<div class="tsr-dots" aria-hidden="true"><?php echo $dots; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
										<button type="button" class="tsr-mini-toggle" aria-pressed="true"><?php echo esc_html( $s['mini_on'] ); ?></button>
									</div>
									<div class="tsr-stage tsr-mstage">
										<?php $this->render_stage( $first, $first_l, $data, $s ); ?>
									</div>
								</div>
								<div class="tsr-miniwrap">
									<svg class="tsr-map tsr-map--mini is-small" viewBox="<?php echo esc_attr( $box ); ?>" role="img" aria-label="<?php echo esc_attr( $s['map_label'] ); ?>">
										<path class="tsr-map-line" d="<?php echo esc_attr( $line ); ?>"></path>
										<path class="tsr-map-fill" d="<?php echo esc_attr( $cont ); ?>"></path>
									</svg>
									<div class="tsr-mini-cap"><?php echo esc_html( $s['mini_caption'] ); ?></div>
								</div>
								<div class="tsr-finale">
									<div class="tsr-big-over"><?php echo esc_html( $s['phone_finale_over'] ); ?></div>
									<h3 class="tsr-big-title tsr-big-title--end"><?php echo esc_html( $s['phone_finale_title'] ); ?></h3>
									<div class="tsr-big-caption"><?php echo esc_html( $finale ); ?></div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php /* Reduced motion (and no script): the days as a plain list. */ ?>
				<div class="tsr-static">
					<div class="tsr-wrap tsr-wrap--narrow">
						<?php if ( '' !== trim( (string) $s['static_title'] ) ) : ?>
							<div class="tsr-static__head"><?php echo $this->chip_html( $s['static_title'], $s['intro_chip_tone'], true ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<?php endif; ?>
						<ol class="tsr-static__list">
							<?php foreach ( $data as $i => $day ) : ?>
								<li class="tsr-static__day">
									<div class="tsr-static__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></div>
									<div>
										<div class="tsr-static__top">
											<h4 class="tsr-static__place"><?php echo esc_html( $day['place'] ); ?></h4>
											<span class="tsr-static__tag"><?php echo esc_html( $day['tag'] ); ?></span>
										</div>
										<p class="tsr-static__desc"><?php echo esc_html( $day['desc'] ); ?></p>
										<span class="tsr-static__chip"><?php echo esc_html( $day['dist'] ); ?></span>
									</div>
								</li>
							<?php endforeach; ?>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Background of a pinned scene: the day's colour tint, and its photo (if any) faded on top.
	 */
	private function render_backdrop( $data ) {
		echo '<div class="tsr-bg" style="background-color:' . esc_attr( $data[0]['tint'] ) . '">';
		foreach ( $data as $i => $day ) {
			if ( '' !== $day['photo'] ) {
				printf( '<div class="tsr-photo" data-day="%d"></div>', (int) $i );
			}
		}
		echo '</div>';
	}

	/**
	 * Day card + drive panel, shown with the first day (the script updates them while scrolling).
	 */
	private function render_stage( $first, $leg, $data, $s ) {
		?>
		<div class="tsr-stage-wrap">
			<div class="tsr-card">
				<div class="tsr-daytag"><?php echo esc_html( $first['tag'] ); ?></div>
				<h3 class="tsr-place"><?php echo esc_html( $first['place'] ); ?></h3>
				<p class="tsr-desc"><?php echo esc_html( $first['desc'] ); ?></p>
				<div class="tsr-dist"><?php echo esc_html( $first['dist'] ); ?></div>
			</div>
			<div class="tsr-travel" style="opacity:0;">
				<div class="tsr-travel__label"><?php echo esc_html( $s['travel_label'] ); ?> <b class="tsr-from"><?php echo esc_html( $first['place'] ); ?></b> → <b class="tsr-to"><?php echo esc_html( isset( $data[1] ) ? $data[1]['place'] : '' ); ?></b></div>
				<div class="tsr-svgwrap">
					<svg viewBox="0 0 240 220" aria-hidden="true">
						<path class="tsr-leg-bg" d=""></path>
						<path class="tsr-leg-fill" d=""></path>
						<circle class="tsr-leg-ring" r="9" cx="0" cy="0"></circle>
						<circle class="tsr-leg-dot" r="5" cx="0" cy="0"></circle>
					</svg>
				</div>
				<div class="tsr-travel__meta">
					<span class="tsr-bearing"><?php echo esc_html( $leg['bearing'] ); ?></span>
					<span class="tsr-travel__sub"><span class="tsr-km"><?php echo esc_html( $leg['km'] ); ?></span> · <span class="tsr-time"><?php echo esc_html( $leg['time'] ); ?></span></span>
				</div>
			</div>
		</div>
		<?php
	}
}
