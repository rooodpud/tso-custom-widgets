<?php
/**
 * Widget: TS Season Calendar — a month-by-month chart of what happens when.
 *
 * Twelve month columns and one row per item (a harvest, a season, an event), each
 * with one or more coloured bars over its months. Built as a real table so screen
 * readers can read it; month names shorten to one letter on phones (per device).
 * First used for the Jabal Akhdar harvest calendar; suits any "best time to visit".
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Season_Calendar extends TS_Base_Widget {

	const EN_MONTHS = array( 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec' );

	public function get_name() {
		return 'ts-season-calendar';
	}

	public function get_title() {
		return __( 'TS Season Calendar', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	public function get_keywords() {
		return array( 'calendar', 'season', 'months', 'harvest', 'best time', 'weather', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Calendar', 'travelstore-custom-widgets' ) )
		);

		$r = new Repeater();
		$r->add_control(
			'label',
			array(
				'label'       => __( 'Row name', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);
		$months = self::month_options();
		$r->add_control(
			'from',
			array(
				'label'   => __( 'From', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'm1',
				'options' => $months,
			)
		);
		$r->add_control(
			'to',
			array(
				'label'       => __( 'To', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'm3',
				'options'     => $months,
				'description' => __( 'The bar covers both months and everything between. "November" to "February" runs across the new year.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'from2',
			array(
				'label'     => __( 'Second period: from (optional)', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array( '' => __( 'No second period', 'travelstore-custom-widgets' ) ) + $months,
				'separator' => 'before',
			)
		);
		$r->add_control(
			'to2',
			array(
				'label'       => __( 'Second period: to', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array( '' => __( 'Same month as "from"', 'travelstore-custom-widgets' ) ) + $months,
				'description' => __( 'For a row with two separate bars, for example January–February and October–December.', 'travelstore-custom-widgets' ),
				'condition'   => array( 'from2!' => '' ),
			)
		);
		$r->add_control(
			'color',
			array(
				'label'     => __( 'Bar colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ts-cal__table {{CURRENT_ITEM}}' => '--ts-cal-bar: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'rows',
			array(
				'label'       => __( 'Rows', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array(
						'label' => __( 'Best weather', 'travelstore-custom-widgets' ),
						'from'  => 'm10',
						'to'    => 'm3',
						'color' => '#38A833',
					),
					array(
						'label' => __( 'Hot season', 'travelstore-custom-widgets' ),
						'from'  => 'm5',
						'to'    => 'm9',
						'color' => '#F3692A',
					),
				),
			)
		);
		$this->add_control(
			'months_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Month names come from the site language (Settings > General). Phones show the first letter (Style > Layout > Month names).', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_control(
			'caption',
			array(
				'label'       => __( 'Table title (screen readers)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'What happens when, month by month', 'travelstore-custom-widgets' ),
				'description' => __( 'Not shown on the page; read out before the table.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'note',
			array(
				'label'       => __( 'Note under the calendar', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => __( 'Optional small print, for example: Approximate — harvests shift by a few weeks each year.', 'travelstore-custom-widgets' ),
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
		$this->add_responsive_control(
			'names_look',
			array(
				'label'                => __( 'Month names', 'travelstore-custom-widgets' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'long',
				'mobile_default'       => 'short',
				'options'              => array(
					'long'  => __( 'Full', 'travelstore-custom-widgets' ),
					'short' => __( 'Short', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'long'  => '--ts-cal-long:inline;--ts-cal-short:none;',
					'short' => '--ts-cal-long:none;--ts-cal-short:inline;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-cal' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'label_width',
			array(
				'label'          => __( 'Row name column width', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px', '%' ),
				'range'          => array(
					'px' => array( 'min' => 40, 'max' => 320 ),
					'%'  => array( 'min' => 10, 'max' => 50 ),
				),
				'default'        => array( 'size' => 150, 'unit' => 'px' ),
				'mobile_default' => array( 'size' => 78, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .ts-cal__labelcol' => 'width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'col_gap',
			array(
				'label'          => __( 'Space between months', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array( 'px' => array( 'min' => 0, 'max' => 12 ) ),
				'default'        => array( 'size' => 4, 'unit' => 'px' ),
				'mobile_default' => array( 'size' => 2, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .ts-cal' => '--ts-cal-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'row_padding',
			array(
				'label'      => __( 'Row height (space above and below)', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-cal' => '--ts-cal-row: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'bar_height',
			array(
				'label'      => __( 'Bar height', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 40 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-cal__bar' => 'height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'bar_radius',
			array(
				'label'      => __( 'Bar corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 20 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-cal__bar' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'line_color', __( 'Line between rows', 'travelstore-custom-widgets' ), '.ts-cal', '--ts-cal-line' );
		$this->add_color_control( 'default_bar', __( 'Bar colour (rows without their own)', 'travelstore-custom-widgets' ), '.ts-cal', '--ts-cal-bar-default', 'ts_terracotta' );
		$this->end_controls_section();

		/* ---------------- Style: box ---------------- */
		$this->start_controls_section(
			'ts_section_box',
			array(
				'label' => __( 'Box', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_color_control( 'box_bg', __( 'Box colour', 'travelstore-custom-widgets' ), '.ts-cal__box', 'background-color', 'ts_sand' );
		$this->add_color_control( 'box_border', __( 'Box border colour', 'travelstore-custom-widgets' ), '.ts-cal__box', 'border-color' );
		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-cal__box' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'box_padding',
			array(
				'label'          => __( 'Padding', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px', 'em' ),
				'default'        => array( 'top' => 26, 'right' => 26, 'bottom' => 26, 'left' => 26, 'unit' => 'px', 'isLinked' => true ),
				'mobile_default' => array( 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16, 'unit' => 'px', 'isLinked' => true ),
				'selectors'      => array( '{{WRAPPER}} .ts-cal__box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
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
		$this->add_typography_control( 'label_typography', __( 'Row name typography', 'travelstore-custom-widgets' ), '.ts-cal__label' );
		$this->add_color_control( 'label_color', __( 'Row name colour', 'travelstore-custom-widgets' ), '.ts-cal__label' );
		$this->add_typography_control( 'month_typography', __( 'Month typography', 'travelstore-custom-widgets' ), '.ts-cal__month' );
		$this->add_color_control( 'month_color', __( 'Month colour', 'travelstore-custom-widgets' ), '.ts-cal__month' );
		$this->add_typography_control( 'note_typography', __( 'Note typography', 'travelstore-custom-widgets' ), '.ts-cal__note' );
		$this->add_color_control( 'note_color', __( 'Note colour', 'travelstore-custom-widgets' ), '.ts-cal__note' );
		$this->add_responsive_control(
			'note_space',
			array(
				'label'      => __( 'Space above the note', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-cal__note' => 'margin-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	/**
	 * Month dropdown options, "1" => "January" … in the site language.
	 */
	private static function month_options() {
		global $wp_locale;
		$options = array();
		// Text values ("m3"): number-like keys would be listed before "No second period" in the editor.
		for ( $m = 1; $m <= 12; $m++ ) {
			$options[ 'm' . $m ] = $wp_locale ? $wp_locale->get_month( $m ) : gmdate( 'F', gmmktime( 0, 0, 0, $m, 1 ) );
		}

		return $options;
	}

	/**
	 * Short (Jan) and one-letter (J) month names in the site language, index 0–11.
	 */
	private function month_labels() {
		global $wp_locale;
		$long  = array();
		$short = array();
		for ( $m = 1; $m <= 12; $m++ ) {
			$name    = $wp_locale ? $wp_locale->get_month_abbrev( $wp_locale->get_month( $m ) ) : gmdate( 'M', gmmktime( 0, 0, 0, $m, 1 ) );
			$long[]  = $name;
			$short[] = function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 1 ) : substr( $name, 0, 1 );
		}

		return array( $long, $short );
	}

	/**
	 * The months of one row as "from-to, from2-to2" for parse_months().
	 * Rows saved before 1.6.1 kept a typed "months" text; it is still read.
	 */
	private function row_months( $row ) {
		$m = static function ( $key ) use ( $row ) {
			return isset( $row[ $key ] ) ? (int) ltrim( (string) $row[ $key ], 'm' ) : 0;
		};
		if ( $m( 'from' ) ) {
			$text = $m( 'from' ) . '-' . ( $m( 'to' ) ? $m( 'to' ) : $m( 'from' ) );
			if ( $m( 'from2' ) ) {
				$text .= ', ' . $m( 'from2' ) . '-' . ( $m( 'to2' ) ? $m( 'to2' ) : $m( 'from2' ) );
			}
			return $text;
		}

		return isset( $row['months'] ) ? (string) $row['months'] : '';
	}

	/**
	 * A month number (1–12) from "4", "04", "Apr" or "April"; 0 when not recognised.
	 */
	private function month_number( $token ) {
		$token = strtolower( trim( $token ) );
		if ( ctype_digit( $token ) ) {
			$n = (int) $token;
			return ( $n >= 1 && $n <= 12 ) ? $n : 0;
		}
		$i = array_search( substr( $token, 0, 3 ), self::EN_MONTHS, true );

		return false === $i ? 0 : $i + 1;
	}

	/**
	 * "Jan-Feb, 10-12" → sorted, non-overlapping array( array( start, end ), … ) with months 1–12.
	 */
	public function parse_months( $text ) {
		$ranges = array();
		foreach ( explode( ',', (string) $text ) as $part ) {
			$ends = preg_split( '/\s*[-–—]\s*/u', trim( $part ) );
			$a    = $this->month_number( $ends[0] );
			$b    = isset( $ends[1] ) ? $this->month_number( $ends[1] ) : $a;
			if ( ! $a || ! $b ) {
				continue;
			}
			if ( $b >= $a ) {
				$ranges[] = array( $a, $b );
			} else {
				// Across the new year: Nov-Feb becomes Nov-Dec and Jan-Feb.
				$ranges[] = array( $a, 12 );
				$ranges[] = array( 1, $b );
			}
		}
		usort(
			$ranges,
			static function ( $x, $y ) {
				return $x[0] - $y[0];
			}
		);
		$clean = array();
		$last  = 0;
		foreach ( $ranges as $range ) {
			$start = max( $range[0], $last + 1 );
			if ( $start <= $range[1] ) {
				$clean[] = array( $start, $range[1] );
				$last    = $range[1];
			}
		}

		return $clean;
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$rows = isset( $s['rows'] ) && is_array( $s['rows'] ) ? $s['rows'] : array();

		if ( empty( $rows ) ) {
			$this->render_empty( __( 'Add at least one row to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		list( $long, $short ) = $this->month_labels();
		$note = isset( $s['note'] ) ? trim( (string) $s['note'] ) : '';
		?>
		<div class="ts-w ts-cal">
			<div class="ts-cal__box">
				<table class="ts-cal__table">
					<?php if ( ! empty( $s['caption'] ) ) : ?>
						<caption class="ts-cal__sr"><?php echo esc_html( $s['caption'] ); ?></caption>
					<?php endif; ?>
					<colgroup>
						<col class="ts-cal__labelcol" />
						<col span="12" />
					</colgroup>
					<thead>
						<tr>
							<td class="ts-cal__corner"></td>
							<?php for ( $m = 0; $m < 12; $m++ ) : ?>
								<th scope="col" class="ts-cal__month"><span class="ts-cal__long"><?php echo esc_html( $long[ $m ] ); ?></span><span class="ts-cal__short" aria-hidden="true"><?php echo esc_html( $short[ $m ] ); ?></span></th>
							<?php endfor; ?>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $rows as $i => $row ) {
							$rid    = ! empty( $row['_id'] ) ? sanitize_html_class( $row['_id'] ) : 'i' . $i;
							$ranges = $this->parse_months( $this->row_months( $row ) );
							echo '<tr class="elementor-repeater-item-' . esc_attr( $rid ) . '">';
							echo '<th scope="row" class="ts-cal__label">' . esc_html( isset( $row['label'] ) ? $row['label'] : '' ) . '</th>';
							$m = 1;
							foreach ( $ranges as $range ) {
								for ( ; $m < $range[0]; $m++ ) {
									echo '<td class="ts-cal__cell"></td>';
								}
								$span = $range[1] - $range[0] + 1;
								$when = $long[ $range[0] - 1 ] . ( $span > 1 ? '–' . $long[ $range[1] - 1 ] : '' );
								printf(
									'<td class="ts-cal__cell" colspan="%1$d"><span class="ts-cal__bar"></span><span class="ts-cal__sr">%2$s</span></td>',
									(int) $span,
									esc_html( $when )
								);
								$m = $range[1] + 1;
							}
							for ( ; $m <= 12; $m++ ) {
								echo '<td class="ts-cal__cell"></td>';
							}
							echo '</tr>';
						}
						?>
					</tbody>
				</table>
			</div>
			<?php if ( '' !== $note ) : ?>
				<p class="ts-cal__note"><?php echo nl2br( esc_html( $note ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
}
