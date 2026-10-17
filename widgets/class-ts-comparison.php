<?php
/**
 * Widget: TS Comparison — two columns compared topic by topic (1.9.0).
 *
 * For example "A traditional travel agency" against "Travel Store Oman", or a sedan against a 4WD.
 * One column can be highlighted. It is a real table, so screen readers read it as one. On small
 * screens each topic stacks: topic, then each column's text with the column name above it.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Comparison extends TS_Base_Widget {

	public function get_name() {
		return 'ts-comparison';
	}

	public function get_title() {
		return __( 'TS Comparison', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_keywords() {
		return array( 'comparison', 'compare', 'table', 'versus', 'difference', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Comparison', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'col_a',
			array(
				'label'       => __( 'First column name', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'A traditional travel agency', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'col_b',
			array(
				'label'       => __( 'Second column name', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Travel Store Oman', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'highlight',
			array(
				'label'   => __( 'Highlighted column', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'b',
				'options' => array(
					'b'    => __( 'Second column', 'travelstore-custom-widgets' ),
					'a'    => __( 'First column', 'travelstore-custom-widgets' ),
					'none' => __( 'None', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'topic_heading',
			array(
				'label'       => __( 'Heading above the topics', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Optional, for example: What you get. Leave empty for a blank corner.', 'travelstore-custom-widgets' ),
			)
		);

		$r = new Repeater();
		$r->add_control(
			'topic',
			array(
				'label'       => __( 'Topic', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$r->add_control(
			'text_a',
			array(
				'label' => __( 'First column text', 'travelstore-custom-widgets' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 2,
			)
		);
		$r->add_control(
			'text_b',
			array(
				'label' => __( 'Second column text', 'travelstore-custom-widgets' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 2,
			)
		);
		$this->add_control(
			'rows',
			array(
				'label'       => __( 'Topics', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ topic }}}',
				'separator'   => 'before',
				'default'     => array(
					array(
						'topic'  => __( 'Your itinerary', 'travelstore-custom-widgets' ),
						'text_a' => __( 'Fixed packages from a brochure', 'travelstore-custom-widgets' ),
						'text_b' => __( 'Built from scratch around how you like to travel', 'travelstore-custom-widgets' ),
					),
					array(
						'topic'  => __( 'Who’s on the ground', 'travelstore-custom-widgets' ),
						'text_a' => __( 'Often resold to third parties overseas', 'travelstore-custom-widgets' ),
						'text_b' => __( 'Our own team, based in Muscat', 'travelstore-custom-widgets' ),
					),
				),
			)
		);
		$this->add_control(
			'caption',
			array(
				'label'       => __( 'Name of the table (for screen readers)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Comparison', 'travelstore-custom-widgets' ),
				'separator'   => 'before',
				'description' => __( 'Not shown on the page. For example: Comparison between a traditional travel agency and Travel Store Oman.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'stack_on',
			array(
				'label'       => __( 'Stack the topics on', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'mobile',
				'options'     => array(
					'mobile' => __( 'Phones', 'travelstore-custom-widgets' ),
					'tablet' => __( 'Tablets and phones', 'travelstore-custom-widgets' ),
					'never'  => __( 'Never (always a table)', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'Stacked: each topic shows its texts one under the other, with the column name above each text.', 'travelstore-custom-widgets' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: table ---------------- */
		$this->start_controls_section(
			'ts_section_table',
			array(
				'label' => __( 'Table', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'topic_width',
			array(
				'label'      => __( 'Topic column width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 80, 'max' => 400 ),
					'%'  => array( 'min' => 10, 'max' => 50 ),
				),
				'default'    => array( 'size' => 200, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-cmp' => '--ts-cmp-topic: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'table_bg', __( 'Table background', 'travelstore-custom-widgets' ), '.ts-cmp', 'background-color', 'ts_cream' );
		$this->add_color_control( 'line_color', __( 'Lines and border', 'travelstore-custom-widgets' ), '.ts-cmp', '--ts-cmp-line' );
		$this->add_responsive_control(
			'table_radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-cmp' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'cell_padding',
			array(
				'label'      => __( 'Space inside each box', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-cmp' => '--ts-cmp-pad: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: column names ---------------- */
		$this->start_controls_section(
			'ts_section_head',
			array(
				'label' => __( 'Column names', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'head_typography', __( 'Typography', 'travelstore-custom-widgets' ), '.ts-cmp__head', 'ts_heading' );
		$this->add_color_control( 'head_plain_bg', __( 'Plain column – background', 'travelstore-custom-widgets' ), '.ts-cmp__head--plain', 'background-color' );
		$this->add_color_control( 'head_plain_color', __( 'Plain column – text', 'travelstore-custom-widgets' ), '.ts-cmp__head--plain' );
		$this->add_color_control( 'head_hl_bg', __( 'Highlighted column – background', 'travelstore-custom-widgets' ), '.ts-cmp__head--hl', 'background-color', 'ts_terracotta' );
		$this->add_color_control( 'head_hl_color', __( 'Highlighted column – text', 'travelstore-custom-widgets' ), '.ts-cmp__head--hl' );
		$this->end_controls_section();

		/* ---------------- Style: topics and texts ---------------- */
		$this->start_controls_section(
			'ts_section_cells',
			array(
				'label' => __( 'Topics and texts', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'topic_typography', __( 'Topic typography', 'travelstore-custom-widgets' ), '.ts-cmp__topic' );
		$this->add_color_control( 'topic_color', __( 'Topic colour', 'travelstore-custom-widgets' ), '.ts-cmp__topic', 'color', 'ts_espresso' );
		$this->add_typography_control( 'cell_typography', __( 'Text typography', 'travelstore-custom-widgets' ), '.ts-cmp__cell' );
		$this->add_color_control( 'cell_plain_bg', __( 'Plain column – background', 'travelstore-custom-widgets' ), '.ts-cmp__cell--plain', 'background-color' );
		$this->add_color_control( 'cell_plain_color', __( 'Plain column – text', 'travelstore-custom-widgets' ), '.ts-cmp__cell--plain' );
		$this->add_typography_control( 'cell_hl_typography', __( 'Highlighted column – typography', 'travelstore-custom-widgets' ), '.ts-cmp__cell--hl' );
		$this->add_color_control( 'cell_hl_bg', __( 'Highlighted column – background', 'travelstore-custom-widgets' ), '.ts-cmp__cell--hl', 'background-color' );
		$this->add_color_control( 'cell_hl_color', __( 'Highlighted column – text', 'travelstore-custom-widgets' ), '.ts-cmp__cell--hl', 'color', 'ts_espresso' );
		$this->add_control(
			'label_heading',
			array(
				'label'     => __( 'Column names above the texts (stacked)', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_typography_control( 'label_typography', __( 'Typography', 'travelstore-custom-widgets' ), '.ts-cmp__lbl' );
		$this->add_color_control( 'label_plain_color', __( 'Plain column', 'travelstore-custom-widgets' ), '.ts-cmp__cell--plain .ts-cmp__lbl' );
		$this->add_color_control( 'label_hl_color', __( 'Highlighted column', 'travelstore-custom-widgets' ), '.ts-cmp__cell--hl .ts-cmp__lbl', 'color', 'ts_terracotta_dark' );
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$rows = isset( $s['rows'] ) && is_array( $s['rows'] ) ? $s['rows'] : array();

		if ( empty( $rows ) ) {
			$this->render_empty( __( 'Add at least one topic to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$hl    = isset( $s['highlight'] ) && in_array( $s['highlight'], array( 'a', 'b', 'none' ), true ) ? $s['highlight'] : 'b';
		$stack = isset( $s['stack_on'] ) && in_array( $s['stack_on'], array( 'mobile', 'tablet', 'never' ), true ) ? $s['stack_on'] : 'mobile';
		$cols  = array(
			'a' => isset( $s['col_a'] ) ? trim( (string) $s['col_a'] ) : '',
			'b' => isset( $s['col_b'] ) ? trim( (string) $s['col_b'] ) : '',
		);
		$look  = function ( $col ) use ( $hl ) {
			return $col === $hl ? 'hl' : 'plain';
		};
		$topic_head = isset( $s['topic_heading'] ) ? trim( (string) $s['topic_heading'] ) : '';
		$caption    = isset( $s['caption'] ) ? trim( (string) $s['caption'] ) : '';
		?>
		<div class="ts-w ts-cmp ts-cmp--stack-<?php echo esc_attr( $stack ); ?>">
			<table class="ts-cmp__table">
				<?php if ( '' !== $caption ) : ?>
					<caption class="ts-sr"><?php echo esc_html( $caption ); ?></caption>
				<?php endif; ?>
				<colgroup><col class="ts-cmp__col-topic"><col><col></colgroup>
				<thead>
					<tr>
						<?php if ( '' !== $topic_head ) : ?>
							<th scope="col" class="ts-cmp__corner"><?php echo esc_html( $topic_head ); ?></th>
						<?php else : ?>
							<td class="ts-cmp__corner"></td>
						<?php endif; ?>
						<?php foreach ( $cols as $key => $name ) : ?>
							<th scope="col" class="ts-cmp__head ts-cmp__head--<?php echo esc_attr( $look( $key ) ); ?>"><?php echo esc_html( $name ); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rows as $i => $row ) : ?>
						<?php $rid = ! empty( $row['_id'] ) ? sanitize_html_class( $row['_id'] ) : 'r' . $i; ?>
						<tr class="ts-cmp__row elementor-repeater-item-<?php echo esc_attr( $rid ); ?>">
							<th scope="row" class="ts-cmp__topic"><?php echo esc_html( isset( $row['topic'] ) ? $row['topic'] : '' ); ?></th>
							<?php foreach ( $cols as $key => $name ) : ?>
								<td class="ts-cmp__cell ts-cmp__cell--<?php echo esc_attr( $look( $key ) ); ?>">
									<?php if ( '' !== $name ) : ?>
										<span class="ts-cmp__lbl" aria-hidden="true"><?php echo esc_html( $name ); ?></span>
									<?php endif; ?>
									<?php echo nl2br( esc_html( isset( $row[ 'text_' . $key ] ) ? $row[ 'text_' . $key ] : '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
