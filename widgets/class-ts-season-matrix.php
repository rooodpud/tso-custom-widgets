<?php
/**
 * Widget: TS Season Matrix — every place, every month, on one chart (1.10.0).
 *
 * One row per place and twelve coloured cells (great / good with a catch / tough), as a real
 * table so screen readers can read it. One month column can be outlined: the current month,
 * a month the editor picks, or none. When the page also has a TS Destination Atlas, the
 * outline follows the month chosen there (the two widgets keep their own data).
 * Built for the Destinations page "All seven, every month, on one chart".
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use TravelStore\TS_Month_Levels;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Season_Matrix extends TS_Base_Widget {

	public function get_name() {
		return 'ts-season-matrix';
	}

	public function get_title() {
		return __( 'TS Season Matrix', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_keywords() {
		return array( 'season', 'months', 'matrix', 'year', 'best time', 'weather', 'chart', 'travel' );
	}

	public function get_script_depends() {
		return array( 'ts-season-matrix' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Chart', 'travelstore-custom-widgets' ) )
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
			'link',
			array(
				'label'       => __( 'Link (optional)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'Type a page name or paste a link', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		TS_Month_Levels::add_controls( $r );

		$this->add_control(
			'rows',
			array(
				'label'       => __( 'Places', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array( 'label' => __( 'Place one', 'travelstore-custom-widgets' ) ),
					array( 'label' => __( 'Place two', 'travelstore-custom-widgets' ) ),
				),
			)
		);

		$this->add_control(
			'highlight',
			array(
				'label'       => __( 'Outlined month', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'current',
				'options'     => array(
					'current' => __( 'This month (follows the month picker if the page has a Destination Atlas)', 'travelstore-custom-widgets' ),
					'none'    => __( 'None', 'travelstore-custom-widgets' ),
				) + $this->month_dropdown(),
				'separator'   => 'before',
			)
		);
		$this->add_control(
			'caption',
			array(
				'label'       => __( 'Table title (screen readers)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Best months for each destination', 'travelstore-custom-widgets' ),
				'description' => __( 'Not shown on the page; read out before the table.', 'travelstore-custom-widgets' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Content: words ---------------- */
		$this->start_controls_section(
			'ts_words',
			array( 'label' => __( 'Words', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'legend_show',
			array(
				'label'        => __( 'Show the key under the chart', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'legend_great',
			array(
				'label'     => __( 'Key: great', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Great', 'travelstore-custom-widgets' ),
				'condition' => array( 'legend_show' => 'yes' ),
			)
		);
		$this->add_control(
			'legend_good',
			array(
				'label'     => __( 'Key: good', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Good, with a catch', 'travelstore-custom-widgets' ),
				'condition' => array( 'legend_show' => 'yes' ),
			)
		);
		$this->add_control(
			'legend_tough',
			array(
				'label'     => __( 'Key: tough', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Tough: very hot, or closed', 'travelstore-custom-widgets' ),
				'condition' => array( 'legend_show' => 'yes' ),
			)
		);
		$this->add_control(
			'names_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Month names come from the site language (Settings > General). Phones show the first letter (Style > Layout > Month names).', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
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
					'long'  => __( 'Full (Jan)', 'travelstore-custom-widgets' ),
					'short' => __( 'Short (J)', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					'long'  => '--ts-ym-long:inline;--ts-ym-short:none;',
					'short' => '--ts-ym-long:none;--ts-ym-short:inline;',
				),
				'selectors'            => array( '{{WRAPPER}} .ts-ym' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'name_width',
			array(
				'label'          => __( 'Place name column width', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array( 'px' => array( 'min' => 50, 'max' => 320 ) ),
				'default'        => array( 'size' => 150, 'unit' => 'px' ),
				'mobile_default' => array( 'size' => 84, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .ts-ym__namecol' => 'width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'cell_gap',
			array(
				'label'          => __( 'Space between cells', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array( 'px' => array( 'min' => 0, 'max' => 12 ) ),
				'default'        => array( 'size' => 4, 'unit' => 'px' ),
				'mobile_default' => array( 'size' => 2, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .ts-ym' => '--ts-ym-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'cell_height',
			array(
				'label'          => __( 'Cell height', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array( 'px' => array( 'min' => 12, 'max' => 70 ) ),
				'default'        => array( 'size' => 34, 'unit' => 'px' ),
				'mobile_default' => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .ts-ym__cell' => 'height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'cell_radius',
			array(
				'label'          => __( 'Cell corner roundness', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array( 'px' => array( 'min' => 0, 'max' => 24 ) ),
				'default'        => array( 'size' => 8, 'unit' => 'px' ),
				'mobile_default' => array( 'size' => 5, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .ts-ym__cell' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'great_color', __( 'Great colour', 'travelstore-custom-widgets' ), '.ts-ym', '--ts-ym-great', 'ts_oasis' );
		$this->add_color_control( 'good_color', __( 'Good colour', 'travelstore-custom-widgets' ), '.ts-ym', '--ts-ym-good', 'ts_gold' );
		$this->add_color_control( 'tough_color', __( 'Tough colour', 'travelstore-custom-widgets' ), '.ts-ym', '--ts-ym-tough' );
		$this->add_color_control( 'outline_color', __( 'Outlined month colour', 'travelstore-custom-widgets' ), '.ts-ym', '--ts-ym-outline', 'ts_espresso' );
		$this->end_controls_section();

		/* ---------------- Style: text ---------------- */
		$this->start_controls_section(
			'ts_section_text_style',
			array(
				'label' => __( 'Text', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'name_typography', __( 'Place name typography', 'travelstore-custom-widgets' ), '.ts-ym__name' );
		$this->add_color_control( 'name_color', __( 'Place name colour', 'travelstore-custom-widgets' ), '.ts-ym__name, .ts-ym__name a' );
		$this->add_color_control( 'name_hover', __( 'Place name underline on hover', 'travelstore-custom-widgets' ), '.ts-ym', '--ts-ym-hover', 'ts_terracotta' );
		$this->add_typography_control( 'month_typography', __( 'Month typography', 'travelstore-custom-widgets' ), '.ts-ym__month' );
		$this->add_color_control( 'month_color', __( 'Month colour', 'travelstore-custom-widgets' ), '.ts-ym__month' );
		$this->add_typography_control( 'legend_typography', __( 'Key typography', 'travelstore-custom-widgets' ), '.ts-ym__legend' );
		$this->add_color_control( 'legend_color', __( 'Key colour', 'travelstore-custom-widgets' ), '.ts-ym__legend' );
		$this->add_responsive_control(
			'legend_space',
			array(
				'label'      => __( 'Space above the key', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-ym__legend' => 'margin-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	/**
	 * "m1" => "January" … in the site language.
	 */
	private function month_dropdown() {
		$names = TS_Month_Levels::month_names();
		$out   = array();
		foreach ( $names[2] as $i => $name ) {
			$out[ 'm' . ( $i + 1 ) ] = $name;
		}

		return $out;
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$rows = isset( $s['rows'] ) && is_array( $s['rows'] ) ? $s['rows'] : array();

		if ( empty( $rows ) ) {
			$this->render_empty( __( 'Add at least one place to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		list( $long, $short, $full ) = TS_Month_Levels::month_names();
		$level_names                 = array(
			'great' => isset( $s['legend_great'] ) && '' !== trim( (string) $s['legend_great'] ) ? $s['legend_great'] : __( 'Great', 'travelstore-custom-widgets' ),
			'good'  => isset( $s['legend_good'] ) && '' !== trim( (string) $s['legend_good'] ) ? $s['legend_good'] : __( 'Good, with a catch', 'travelstore-custom-widgets' ),
			'tough' => isset( $s['legend_tough'] ) && '' !== trim( (string) $s['legend_tough'] ) ? $s['legend_tough'] : __( 'Tough', 'travelstore-custom-widgets' ),
		);

		$mode = isset( $s['highlight'] ) ? (string) $s['highlight'] : 'current';
		if ( 'current' === $mode ) {
			$now_attr = 'current';
			$now      = (int) wp_date( 'n' ) - 1; // fallback until the script sets the visitor's month
		} elseif ( 'none' === $mode ) {
			$now_attr = 'none';
			$now      = -1;
		} else {
			$n        = TS_Month_Levels::month_value( $mode );
			$now      = $n ? $n - 1 : -1;
			$now_attr = $n ? (string) $n : 'none';
		}
		?>
		<div class="ts-w ts-ym" data-ts-ym="<?php echo esc_attr( $now_attr ); ?>">
			<table class="ts-ym__table">
				<?php if ( ! empty( $s['caption'] ) ) : ?>
					<caption class="ts-ym__sr"><?php echo esc_html( $s['caption'] ); ?></caption>
				<?php endif; ?>
				<colgroup>
					<col class="ts-ym__namecol" />
					<col span="12" />
				</colgroup>
				<thead>
					<tr>
						<td class="ts-ym__corner"></td>
						<?php for ( $m = 0; $m < 12; $m++ ) : ?>
							<th scope="col" class="ts-ym__month<?php echo $m === $now ? ' is-now' : ''; ?>" data-m="<?php echo (int) $m; ?>"><span class="ts-ym__long" title="<?php echo esc_attr( $full[ $m ] ); ?>"><?php echo esc_html( $long[ $m ] ); ?></span><span class="ts-ym__short" aria-hidden="true"><?php echo esc_html( $short[ $m ] ); ?></span></th>
						<?php endfor; ?>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $rows as $i => $row ) {
						$rid    = ! empty( $row['_id'] ) ? sanitize_html_class( $row['_id'] ) : 'i' . $i;
						$label  = isset( $row['label'] ) ? (string) $row['label'] : '';
						$levels = TS_Month_Levels::read( $row );
						echo '<tr class="elementor-repeater-item-' . esc_attr( $rid ) . '">';
						echo '<th scope="row" class="ts-ym__name">';
						if ( ! empty( $row['link']['url'] ) ) {
							$key = 'ts_ym_link_' . $rid;
							$this->add_link_attributes( $key, $row['link'] );
							printf( '<a %s>%s</a>', $this->get_render_attribute_string( $key ), esc_html( $label ) ); // phpcs:ignore WordPress.Security.EscapeOutput
						} else {
							echo esc_html( $label );
						}
						echo '</th>';
						foreach ( $levels as $m => $level ) {
							printf(
								'<td class="ts-ym__cell is-%1$s%2$s" data-m="%3$d" title="%4$s"><span class="ts-ym__sr">%5$s</span></td>',
								esc_attr( $level ),
								$m === $now ? ' is-now' : '',
								(int) $m,
								esc_attr( $label . ', ' . $long[ $m ] . ': ' . $level_names[ $level ] ),
								esc_html( $full[ $m ] . ': ' . $level_names[ $level ] )
							);
						}
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
			<?php if ( ! empty( $s['legend_show'] ) ) : ?>
				<ul class="ts-ym__legend">
					<?php foreach ( array( 'great', 'good', 'tough' ) as $k ) : ?>
						<li><i class="ts-ym__swatch is-<?php echo esc_attr( $k ); ?>" aria-hidden="true"></i><?php echo esc_html( $level_names[ $k ] ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	}
}
