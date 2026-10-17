<?php
/**
 * Widget: TS Section Header — chip, heading, intro text and up to two buttons.
 *
 * Used for every section introduction and, in "Hero" size, as the page hero text
 * block (with the H1). Generic: no page-specific names or content.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Section_Header extends TS_Base_Widget {

	public function get_name() {
		return 'ts-section-header';
	}

	public function get_title() {
		return __( 'TS Section Header', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-heading';
	}

	public function get_keywords() {
		return array( 'heading', 'hero', 'title', 'intro', 'eyebrow', 'chip', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Content', 'travelstore-custom-widgets' ) )
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => __( 'Small label (chip)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Section label', 'travelstore-custom-widgets' ),
				'description' => __( 'Leave empty to hide the chip.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_control(
			'eyebrow_tone',
			array(
				'label'     => __( 'Chip colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'gold',
				'options'   => self::tone_options(),
				'condition' => array( 'eyebrow!' => '' ),
			)
		);
		$this->add_control(
			'show_dot',
			array(
				'label'        => __( 'Show dot in chip', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'eyebrow!' => '' ),
			)
		);
		$this->add_control(
			'eyebrow_look',
			array(
				'label'     => __( 'Label look', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'pill',
				'options'   => array(
					'pill' => __( 'Pill', 'travelstore-custom-widgets' ),
					'line' => __( 'Line and capitals (no pill)', 'travelstore-custom-widgets' ),
					'rule' => __( 'Capitals with a thin line running on after them (full width)', 'travelstore-custom-widgets' ),
				),
				'condition' => array( 'eyebrow!' => '' ),
			)
		);
		$this->add_control(
			'heading',
			array(
				'label'     => __( 'Heading', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'default'   => __( 'A clear, confident section heading.', 'travelstore-custom-widgets' ),
				'dynamic'   => array( 'active' => true ),
				'separator' => 'before',
			)
		);
		$this->add_tag_control( 'heading_tag', __( 'Heading level', 'travelstore-custom-widgets' ), 'h2' );
		// 1.9.0: part of the heading in another colour.
		$this->add_control(
			'heading_words',
			array(
				'label'       => __( 'Words in colour', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Optional. Copy some words from the heading exactly as they are written there, for example: An Oman experience, — they show in the colour set under Style › Text › "Words in colour".', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'description',
			array(
				'label'   => __( 'Intro text', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => __( 'One or two sentences that set up what follows.', 'travelstore-custom-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_button_controls( 'primary', __( 'First button', 'travelstore-custom-widgets' ), 'primary' );
		$this->add_button_controls( 'secondary', __( 'Second button', 'travelstore-custom-widgets' ), 'secondary' );

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
			'size',
			array(
				'label'   => __( 'Size', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'section',
				'options' => array(
					'hero'    => __( 'Hero (page top, large)', 'travelstore-custom-widgets' ),
					'section' => __( 'Section heading', 'travelstore-custom-widgets' ),
					'compact' => __( 'Compact', 'travelstore-custom-widgets' ),
				),
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
			'alignment',
			array(
				'label'     => __( 'Alignment', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array( 'title' => __( 'Left', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-left' ),
					'center' => array( 'title' => __( 'Center', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-center' ),
					'right'  => array( 'title' => __( 'Right', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-right' ),
				),
				'selectors_dictionary' => array(
					'left'   => 'align-items:flex-start;text-align:left;',
					'center' => 'align-items:center;text-align:center;',
					'right'  => 'align-items:flex-end;text-align:right;',
				),
				'selectors' => array( '{{WRAPPER}} .ts-sh' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'max_width',
			array(
				'label'      => __( 'Text width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 240, 'max' => 1200 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'selectors'  => array( '{{WRAPPER}} .ts-sh__inner' => 'max-width: {{SIZE}}{{UNIT}};' ),
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
		$this->add_typography_control( 'eyebrow_typography', __( 'Chip typography', 'travelstore-custom-widgets' ), '.ts-chip' );
		$this->add_color_control( 'eyebrow_color', __( 'Label colour', 'travelstore-custom-widgets' ), '.ts-sh__eyebrow .ts-chip' );
		// 1.9.3: a label colour outside the palette tones (Jabal Shams "stone" label).
		$this->add_color_control( 'eyebrow_bg', __( 'Label background', 'travelstore-custom-widgets' ), '.ts-sh__eyebrow .ts-chip', 'background-color' );
		$this->add_color_control( 'eyebrow_dot', __( 'Label dot colour', 'travelstore-custom-widgets' ), '.ts-sh__eyebrow .ts-chip__dot', 'background-color' );
		$this->add_typography_control( 'heading_typography', __( 'Heading typography', 'travelstore-custom-widgets' ), '.ts-sh__title', 'ts_heading' );
		$this->add_color_control( 'heading_color', __( 'Heading colour', 'travelstore-custom-widgets' ), '.ts-sh__title' );
		$this->add_color_control( 'heading_words_color', __( 'Words in colour', 'travelstore-custom-widgets' ), '.ts-sh__title .ts-sh__mark', 'color', 'ts_terracotta' );
		// 1.9.4: space above the buttons (empty = 30px), e.g. 0 when the header holds only a button.
		$this->add_responsive_control(
			'actions_space',
			array(
				'label'      => __( 'Space above the buttons', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-sh__actions' => 'margin-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		// 1.9.4: the intro can be narrower than the heading (Nizwa fort: 460px).
		$this->add_responsive_control(
			'description_max_width',
			array(
				'label'      => __( 'Intro width (at most)', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 1000 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'selectors'  => array( '{{WRAPPER}} .ts-sh__desc' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_typography_control( 'description_typography', __( 'Intro typography', 'travelstore-custom-widgets' ), '.ts-sh__desc', 'ts_body' );
		$this->add_color_control( 'description_color', __( 'Intro colour', 'travelstore-custom-widgets' ), '.ts-sh__desc' );
		$this->end_controls_section();

		$this->add_button_style_section();
		$this->register_extra_css_controls();
	}

	/**
	 * Escaped heading with line breaks; the first match of $words (any capitals) wrapped for its own colour.
	 */
	private function heading_html( $heading, $words ) {
		$words = trim( (string) $words );
		$at    = '' !== $words ? mb_stripos( $heading, $words ) : false;
		if ( false === $at ) {
			return nl2br( esc_html( $heading ) );
		}
		$len = mb_strlen( $words );

		return nl2br( esc_html( mb_substr( $heading, 0, $at ) ) )
			. '<span class="ts-sh__mark">' . nl2br( esc_html( mb_substr( $heading, $at, $len ) ) ) . '</span>'
			. nl2br( esc_html( mb_substr( $heading, $at + $len ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$eyebrow = isset( $s['eyebrow'] ) ? trim( (string) $s['eyebrow'] ) : '';
		$heading = isset( $s['heading'] ) ? trim( (string) $s['heading'] ) : '';
		$desc    = isset( $s['description'] ) ? trim( (string) $s['description'] ) : '';
		$has_btn = ! empty( $s['primary_text'] ) || ! empty( $s['secondary_text'] );

		if ( '' === $eyebrow && '' === $heading && '' === $desc && ! $has_btn ) {
			$this->render_empty( __( 'Add a heading, intro text or a label to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$size    = isset( $s['size'] ) && in_array( $s['size'], array( 'hero', 'section', 'compact' ), true ) ? $s['size'] : 'section';
		$scheme  = isset( $s['text_scheme'] ) && 'light' === $s['text_scheme'] ? 'light' : 'dark';
		$tag     = $this->safe_tag( isset( $s['heading_tag'] ) ? $s['heading_tag'] : 'h2', 'h2' );
		$classes = array( 'ts-w', 'ts-sh', 'ts-sh--' . $size );
		if ( 'light' === $scheme ) {
			$classes[] = 'ts-sh--light';
			$classes[] = 'ts-on-dark';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<div class="ts-sh__inner">
				<?php
				if ( '' !== $eyebrow ) {
					$look = isset( $s['eyebrow_look'] ) ? (string) $s['eyebrow_look'] : 'pill';
					$line = in_array( $look, array( 'line', 'rule' ), true ); // 1.10.0: "rule" = capitals with a thin line after them (Destinations region labels).
					echo '<div class="ts-sh__eyebrow">' . $this->chip_html( $eyebrow, isset( $s['eyebrow_tone'] ) ? $s['eyebrow_tone'] : 'gold', ! $line && ! empty( $s['show_dot'] ), 'rule' === $look ? 'ts-chip--rule' : ( $line ? 'ts-chip--line' : '' ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				if ( '' !== $heading ) {
					printf(
						'<%1$s class="ts-sh__title">%2$s</%1$s>',
						esc_attr( $tag ),
						$this->heading_html( $heading, isset( $s['heading_words'] ) ? $s['heading_words'] : '' ) // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in heading_html().
					);
				}
				if ( '' !== $desc ) {
					echo '<div class="ts-sh__desc">' . wp_kses_post( $desc ) . '</div>';
				}
				?>
				<?php if ( $has_btn ) : ?>
					<div class="ts-sh__actions">
						<?php
						$this->render_button( $s, 'primary' );
						$this->render_button( $s, 'secondary' );
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
