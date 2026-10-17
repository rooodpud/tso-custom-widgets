<?php
/**
 * Widget: TS Travel Time Bars — how long each place takes, as horizontal bars (1.10.0).
 *
 * One row per place with one or two bars: each bar has a kind (road, flight, long road with a
 * striped fill), a text ("Road · ~1.5–2 hrs") and the hours it is drawn for. The bars share one
 * scale (the longest the chart goes up to) and grow when they scroll into view. Built for the
 * Destinations page "Drive or fly?" chart; suits any "how long does it take" comparison.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Time_Bars extends TS_Base_Widget {

	public function get_name() {
		return 'ts-time-bars';
	}

	public function get_title() {
		return __( 'TS Travel Time Bars', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-progress-tracker';
	}

	public function get_keywords() {
		return array( 'travel time', 'bars', 'drive', 'fly', 'distance', 'hours', 'chart', 'travel' );
	}

	public function get_script_depends() {
		return array( 'ts-time-bars' );
	}

	private function kind_options( $with_none = false ) {
		$kinds = array(
			'road'   => __( 'Road (orange)', 'travelstore-custom-widgets' ),
			'flight' => __( 'Flight (blue)', 'travelstore-custom-widgets' ),
			'long'   => __( 'Long road (orange, striped)', 'travelstore-custom-widgets' ),
		);

		return $with_none ? array( '' => __( 'No second bar', 'travelstore-custom-widgets' ) ) + $kinds : $kinds;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Rows', 'travelstore-custom-widgets' ) )
		);

		$r = new Repeater();
		$r->add_control(
			'label',
			array(
				'label'       => __( 'Place', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'b1_heading',
			array(
				'label'     => __( 'First bar', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$r->add_control(
			'b1_kind',
			array(
				'label'   => __( 'Kind', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'road',
				'options' => $this->kind_options(),
			)
		);
		$r->add_control(
			'b1_text',
			array(
				'label'       => __( 'Words beside the bar', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'For example: Road · ~2 hrs', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'b1_hours',
			array(
				'label'       => __( 'Bar length in hours', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 100,
				'step'        => 0.05,
				'description' => __( 'How long the bar is drawn. Use the middle of a range, for example 1.75 for "1.5–2 hrs".', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'b2_heading',
			array(
				'label'     => __( 'Second bar (optional)', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$r->add_control(
			'b2_kind',
			array(
				'label'   => __( 'Kind', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->kind_options( true ),
			)
		);
		$r->add_control(
			'b2_text',
			array(
				'label'       => __( 'Words beside the bar', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'For example: Flight · ~1 hr', 'travelstore-custom-widgets' ),
				'condition'   => array( 'b2_kind!' => '' ),
			)
		);
		$r->add_control(
			'b2_hours',
			array(
				'label'     => __( 'Bar length in hours', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'step'      => 0.05,
				'condition' => array( 'b2_kind!' => '' ),
			)
		);

		$this->add_control(
			'rows',
			array(
				'label'       => __( 'Places', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array(
						'label'    => __( 'Place one', 'travelstore-custom-widgets' ),
						'b1_kind'  => 'road',
						'b1_text'  => __( 'Road · ~2 hrs', 'travelstore-custom-widgets' ),
						'b1_hours' => 2,
					),
					array(
						'label'    => __( 'Place two', 'travelstore-custom-widgets' ),
						'b1_kind'  => 'flight',
						'b1_text'  => __( 'Flight · ~1 hr', 'travelstore-custom-widgets' ),
						'b1_hours' => 1,
						'b2_kind'  => 'long',
						'b2_text'  => __( 'Road · 6+ hrs', 'travelstore-custom-widgets' ),
						'b2_hours' => 6.5,
					),
				),
			)
		);
		$this->end_controls_section();

		/* ---------------- Content: scale and note ---------------- */
		$this->start_controls_section(
			'ts_scale',
			array( 'label' => __( 'Scale and note', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'max_hours',
			array(
				'label'       => __( 'Full width of a bar = hours', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 100,
				'default'     => 12,
				'description' => __( 'A bar of this many hours fills the whole track. Longer bars are cut at the end.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'scale_show',
			array(
				'label'        => __( 'Show the hours scale under the bars', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Hidden on phones.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'scale_step',
			array(
				'label'     => __( 'Scale mark every', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3',
				'options'   => array(
					'1' => __( '1 hour', 'travelstore-custom-widgets' ),
					'2' => __( '2 hours', 'travelstore-custom-widgets' ),
					'3' => __( '3 hours', 'travelstore-custom-widgets' ),
					'4' => __( '4 hours', 'travelstore-custom-widgets' ),
					'6' => __( '6 hours', 'travelstore-custom-widgets' ),
				),
				'condition' => array( 'scale_show' => 'yes' ),
			)
		);
		$this->add_control(
			'unit_word',
			array(
				'label'       => __( 'Word for hours on the scale', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'hrs', 'travelstore-custom-widgets' ),
				'description' => __( 'For example: 3 hrs, 6 hrs.', 'travelstore-custom-widgets' ),
				'condition'   => array( 'scale_show' => 'yes' ),
			)
		);
		$this->add_control(
			'grow',
			array(
				'label'        => __( 'Bars grow when they scroll into view', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'note',
			array(
				'label'       => __( 'Note under the chart', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::WYSIWYG,
				'description' => __( 'Optional. Use the link button for a link.', 'travelstore-custom-widgets' ),
				'separator'   => 'before',
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: box ---------------- */
		$this->start_controls_section(
			'ts_section_box',
			array(
				'label' => __( 'Box', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_color_control( 'box_bg', __( 'Box colour', 'travelstore-custom-widgets' ), '.ts-tb__box', 'background-color', 'ts_cream' );
		$this->add_color_control( 'box_border', __( 'Box border colour', 'travelstore-custom-widgets' ), '.ts-tb__box', 'border-color' );
		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-tb__box' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'box_padding',
			array(
				'label'          => __( 'Padding', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px', 'em' ),
				'default'        => array( 'top' => 30, 'right' => 30, 'bottom' => 30, 'left' => 30, 'unit' => 'px', 'isLinked' => true ),
				'mobile_default' => array( 'top' => 20, 'right' => 16, 'bottom' => 20, 'left' => 16, 'unit' => 'px', 'isLinked' => false ),
				'selectors'      => array( '{{WRAPPER}} .ts-tb__box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: bars ---------------- */
		$this->start_controls_section(
			'ts_section_bars',
			array(
				'label' => __( 'Bars', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'name_width',
			array(
				'label'      => __( 'Place name column width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 60, 'max' => 320 ) ),
				'default'    => array( 'size' => 150, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-tb' => '--ts-tb-name-w: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'label_width',
			array(
				'label'          => __( 'Words beside the bar: width', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array( 'px' => array( 'min' => 60, 'max' => 320 ) ),
				'default'        => array( 'size' => 150, 'unit' => 'px' ),
				'mobile_default' => array( 'size' => 118, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .ts-tb' => '--ts-tb-label-w: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'track_height',
			array(
				'label'      => __( 'Bar thickness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 40 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-tb__track' => 'height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'track_color', __( 'Empty track colour', 'travelstore-custom-widgets' ), '.ts-tb', '--ts-tb-track' );
		$this->add_color_control( 'road_color', __( 'Road colour', 'travelstore-custom-widgets' ), '.ts-tb', '--ts-tb-road', 'ts_terracotta' );
		$this->add_color_control( 'flight_color', __( 'Flight colour', 'travelstore-custom-widgets' ), '.ts-tb', '--ts-tb-flight', 'ts_turquoise' );
		$this->add_color_control( 'row_line', __( 'Line between rows (phones)', 'travelstore-custom-widgets' ), '.ts-tb', '--ts-tb-line' );
		$this->end_controls_section();

		/* ---------------- Style: text ---------------- */
		$this->start_controls_section(
			'ts_section_text_style',
			array(
				'label' => __( 'Text', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'name_typography', __( 'Place name typography', 'travelstore-custom-widgets' ), '.ts-tb__name' );
		$this->add_color_control( 'name_color', __( 'Place name colour', 'travelstore-custom-widgets' ), '.ts-tb__name' );
		$this->add_typography_control( 'text_typography', __( 'Words beside the bar: typography', 'travelstore-custom-widgets' ), '.ts-tb__t' );
		$this->add_color_control( 'text_color', __( 'Words beside the bar: colour', 'travelstore-custom-widgets' ), '.ts-tb__t' );
		$this->add_typography_control( 'scale_typography', __( 'Scale typography', 'travelstore-custom-widgets' ), '.ts-tb__scale' );
		$this->add_color_control( 'scale_color', __( 'Scale colour', 'travelstore-custom-widgets' ), '.ts-tb__scale' );
		$this->add_typography_control( 'note_typography', __( 'Note typography', 'travelstore-custom-widgets' ), '.ts-tb__note' );
		$this->add_color_control( 'note_color', __( 'Note colour', 'travelstore-custom-widgets' ), '.ts-tb__note' );
		$this->add_color_control( 'note_link', __( 'Note link colour', 'travelstore-custom-widgets' ), '.ts-tb__note a' );
		$this->add_color_control( 'note_link_line', __( 'Note link underline', 'travelstore-custom-widgets' ), '.ts-tb__note a', 'border-color' );
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$rows = isset( $s['rows'] ) && is_array( $s['rows'] ) ? $s['rows'] : array();

		if ( empty( $rows ) ) {
			$this->render_empty( __( 'Add at least one place to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$max   = isset( $s['max_hours'] ) && (float) $s['max_hours'] > 0 ? (float) $s['max_hours'] : 12.0;
		$kinds = array( 'road', 'flight', 'long' );
		$step  = isset( $s['scale_step'] ) && (int) $s['scale_step'] > 0 ? (int) $s['scale_step'] : 3;
		$unit  = isset( $s['unit_word'] ) ? trim( (string) $s['unit_word'] ) : '';
		$note  = isset( $s['note'] ) ? trim( (string) $s['note'] ) : '';
		$class = 'ts-w ts-tb' . ( ! empty( $s['grow'] ) ? ' ts-tb--grow' : '' );
		?>
		<div class="<?php echo esc_attr( $class ); ?>" data-ts-tb>
			<div class="ts-tb__box">
				<?php
				foreach ( $rows as $i => $row ) {
					$rid  = ! empty( $row['_id'] ) ? sanitize_html_class( $row['_id'] ) : 'i' . $i;
					$bars = array();
					foreach ( array( 'b1', 'b2' ) as $b ) {
						$kind = isset( $row[ $b . '_kind' ] ) ? (string) $row[ $b . '_kind' ] : '';
						if ( ! in_array( $kind, $kinds, true ) ) {
							continue;
						}
						$hours  = isset( $row[ $b . '_hours' ] ) ? (float) $row[ $b . '_hours' ] : 0.0;
						$bars[] = array(
							'kind' => $kind,
							'text' => isset( $row[ $b . '_text' ] ) ? (string) $row[ $b . '_text' ] : '',
							'pct'  => max( 0, min( 100, $hours / $max * 100 ) ),
						);
					}
					?>
					<div class="ts-tb__row elementor-repeater-item-<?php echo esc_attr( $rid ); ?>">
						<div class="ts-tb__name"><?php echo esc_html( isset( $row['label'] ) ? $row['label'] : '' ); ?></div>
						<div class="ts-tb__bars">
							<?php foreach ( $bars as $bar ) : ?>
								<div class="ts-tb__bar">
									<span class="ts-tb__t"><?php echo esc_html( $bar['text'] ); ?></span>
									<div class="ts-tb__track" aria-hidden="true"><div class="ts-tb__fill is-<?php echo esc_attr( $bar['kind'] ); ?>" style="--ts-tb-w:<?php echo esc_attr( round( $bar['pct'], 2 ) ); ?>%"></div></div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<?php
				}
				?>
				<?php if ( ! empty( $s['scale_show'] ) ) : ?>
					<div class="ts-tb__scale" aria-hidden="true">
						<span></span>
						<div class="ts-tb__ticks">
							<?php
							for ( $h = 0; $h <= $max + 0.0001; $h += $step ) {
								echo '<span>' . esc_html( 0 === (int) $h ? '0' : rtrim( $h . ' ' . $unit ) ) . '</span>';
							}
							?>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( '' !== $note ) : ?>
					<div class="ts-tb__note"><?php echo $this->mark_external_links( wp_kses_post( $note ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
