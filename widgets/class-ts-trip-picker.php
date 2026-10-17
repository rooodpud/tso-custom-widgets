<?php
/**
 * Widget: TS Trip Style Picker — "What sounds like your trip?" (1.11.0, Experiences page).
 *
 * A question and a row of buttons. Choosing a button highlights the parts of the page that
 * match it (blocks found by their CSS ID, set in Advanced › CSS ID), fades the other parts
 * this picker knows about, scrolls to the first match and shows a short tip under the box.
 * Choosing the same button again clears it. Without JavaScript the buttons do nothing and
 * the page reads as usual.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Trip_Picker extends TS_Base_Widget {

	const PARTS = array( 'part1', 'part2', 'part3' );

	public function get_name() {
		return 'ts-trip-picker';
	}

	public function get_title() {
		return __( 'TS Trip Style Picker', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-click';
	}

	public function get_keywords() {
		return array( 'picker', 'finder', 'choose', 'style', 'quiz', 'highlight', 'buttons', 'travel' );
	}

	public function get_script_depends() {
		return array( 'ts-trip-picker' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Question', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'question',
			array(
				'label'       => __( 'Question', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'What sounds like your trip?', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'subline',
			array(
				'label'       => __( 'Small line under the question', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Tap one to see where to start', 'travelstore-custom-widgets' ),
				'description' => __( 'Optional.', 'travelstore-custom-widgets' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'ts_choices',
			array( 'label' => __( 'Buttons', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'choices_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Give each part of the page a CSS ID first (select the block, then Advanced › CSS ID, for example: golf). Then type that ID in the buttons below. Parts named in any button are faded while a button that does not name them is chosen.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$r = new Repeater();
		$r->add_control(
			'label',
			array(
				'label'       => __( 'Button text', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'I want to be active outdoors', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'tip',
			array(
				'label'       => __( 'Tip shown under the box', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => __( 'Optional. One or two sentences: where to start.', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'part1',
			array(
				'label'       => __( 'Part to highlight (its CSS ID)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'For example: adventure', 'travelstore-custom-widgets' ),
				'description' => __( 'The page scrolls to this part.', 'travelstore-custom-widgets' ),
				'separator'   => 'before',
			)
		);
		$r->add_control(
			'part2',
			array(
				'label'       => __( 'Second part (optional)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'For example: self-drive', 'travelstore-custom-widgets' ),
			)
		);
		$r->add_control(
			'part3',
			array(
				'label'       => __( 'Third part (optional)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'choices',
			array(
				'label'       => __( 'Buttons', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array(
						'label' => __( 'I want to be active outdoors', 'travelstore-custom-widgets' ),
						'part1' => 'adventure',
					),
					array(
						'label' => __( 'I want to go at my own pace', 'travelstore-custom-widgets' ),
						'part1' => 'self-drive',
					),
				),
			)
		);
		$this->add_control(
			'scroll',
			array(
				'label'        => __( 'Scroll to the highlighted part', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);
		$this->add_control(
			'fade_others',
			array(
				'label'        => __( 'Fade the other parts', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
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
		$this->add_control(
			'arrangement',
			array(
				'label'   => __( 'Question and buttons', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'beside',
				'options' => array(
					'beside'  => __( 'Side by side (stacked on tablets and phones)', 'travelstore-custom-widgets' ),
					'stacked' => __( 'Question above the buttons', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_color_control( 'box_bg', __( 'Box colour', 'travelstore-custom-widgets' ), '.ts-pick__box', 'background-color', 'ts_cream' );
		$this->add_color_control( 'box_border', __( 'Border colour', 'travelstore-custom-widgets' ), '.ts-pick__box', 'border-color' );
		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-pick__box' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-pick__box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'box_gap',
			array(
				'label'      => __( 'Space between the question and the buttons', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-pick__box' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: question ---------------- */
		$this->start_controls_section(
			'ts_section_question',
			array(
				'label' => __( 'Question', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'question_typography', __( 'Question typography', 'travelstore-custom-widgets' ), '.ts-pick__q', 'ts_heading' );
		$this->add_color_control( 'question_color', __( 'Question colour', 'travelstore-custom-widgets' ), '.ts-pick__q' );
		$this->add_typography_control( 'subline_typography', __( 'Small line typography', 'travelstore-custom-widgets' ), '.ts-pick__sub' );
		$this->add_color_control( 'subline_color', __( 'Small line colour', 'travelstore-custom-widgets' ), '.ts-pick__sub' );
		$this->end_controls_section();

		/* ---------------- Style: buttons ---------------- */
		$this->start_controls_section(
			'ts_section_buttons',
			array(
				'label' => __( 'Buttons', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'button_typography', __( 'Typography', 'travelstore-custom-widgets' ), '.ts-pick__btn' );
		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-pick__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'button_gap',
			array(
				'label'      => __( 'Space between buttons', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-pick__btns' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->start_controls_tabs( 'button_tabs' );
		$this->start_controls_tab( 'button_tab_normal', array( 'label' => __( 'Normal', 'travelstore-custom-widgets' ) ) );
		$this->add_color_control( 'button_bg', __( 'Button colour', 'travelstore-custom-widgets' ), '.ts-pick__btn', 'background-color' );
		$this->add_color_control( 'button_text', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-pick__btn' );
		$this->add_color_control( 'button_border', __( 'Border colour', 'travelstore-custom-widgets' ), '.ts-pick__btn', 'border-color' );
		$this->end_controls_tab();
		$this->start_controls_tab( 'button_tab_hover', array( 'label' => __( 'Hover', 'travelstore-custom-widgets' ) ) );
		$this->add_color_control( 'button_border_hover', __( 'Border colour', 'travelstore-custom-widgets' ), '.ts-pick__btn:hover', 'border-color' );
		$this->end_controls_tab();
		$this->start_controls_tab( 'button_tab_on', array( 'label' => __( 'Chosen', 'travelstore-custom-widgets' ) ) );
		$this->add_color_control( 'button_on_bg', __( 'Button colour', 'travelstore-custom-widgets' ), '.ts-pick__btn[aria-pressed="true"]', 'background-color' );
		$this->add_color_control( 'button_on_text', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-pick__btn[aria-pressed="true"]' );
		$this->add_color_control( 'button_on_border', __( 'Border colour', 'travelstore-custom-widgets' ), '.ts-pick__btn[aria-pressed="true"]', 'border-color' );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		/* ---------------- Style: tip and highlight ---------------- */
		$this->start_controls_section(
			'ts_section_tip',
			array(
				'label' => __( 'Tip and highlight', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'tip_typography', __( 'Tip typography', 'travelstore-custom-widgets' ), '.ts-pick__tip' );
		$this->add_color_control( 'tip_color', __( 'Tip colour', 'travelstore-custom-widgets' ), '.ts-pick__tip' );
		$this->add_responsive_control(
			'tip_space',
			array(
				'label'      => __( 'Space above the tip', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-pick__tip' => 'margin-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'ring_color',
			array(
				'label'       => __( 'Highlight line colour', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'The line drawn around a highlighted part.', 'travelstore-custom-widgets' ),
				'separator'   => 'before',
				'selectors'   => array( '{{WRAPPER}} .ts-pick' => '--ts-pick-ring: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'fade_opacity',
			array(
				'label'      => __( 'Faded parts – opacity', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array( 'px' => array( 'min' => 0.1, 'max' => 1, 'step' => 0.05 ) ),
				'condition'  => array( 'fade_others' => 'yes' ),
				'selectors'  => array( '{{WRAPPER}} .ts-pick' => '--ts-pick-fade: {{SIZE}};' ),
			)
		);
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	/**
	 * A CSS ID as typed (with or without "#"), reduced to the characters an ID can safely hold here.
	 */
	private function clean_id( $id ) {
		return preg_replace( '/[^A-Za-z0-9_-]/', '', ltrim( trim( (string) $id ), '#' ) );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$choices = array();
		foreach ( isset( $s['choices'] ) && is_array( $s['choices'] ) ? $s['choices'] : array() as $c ) {
			$label = isset( $c['label'] ) ? trim( (string) $c['label'] ) : '';
			if ( '' === $label ) {
				continue;
			}
			$ids = array();
			foreach ( self::PARTS as $p ) {
				$id = $this->clean_id( isset( $c[ $p ] ) ? $c[ $p ] : '' );
				if ( '' !== $id && ! in_array( $id, $ids, true ) ) {
					$ids[] = $id;
				}
			}
			$choices[] = array(
				'label' => $label,
				'tip'   => isset( $c['tip'] ) ? trim( (string) $c['tip'] ) : '',
				'ids'   => $ids,
			);
		}

		if ( empty( $choices ) ) {
			$this->render_empty( __( 'Add at least one button to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$question = isset( $s['question'] ) ? trim( (string) $s['question'] ) : '';
		$subline  = isset( $s['subline'] ) ? trim( (string) $s['subline'] ) : '';
		$stacked  = isset( $s['arrangement'] ) && 'stacked' === $s['arrangement'];
		$tip_id   = 'ts-pick-tip-' . $this->get_id();
		$q_id     = 'ts-pick-q-' . $this->get_id();
		?>
		<div class="ts-w ts-pick<?php echo $stacked ? ' ts-pick--stacked' : ''; ?>" data-ts-pick data-scroll="<?php echo ! isset( $s['scroll'] ) || 'yes' === $s['scroll'] ? '1' : '0'; ?>" data-fade="<?php echo ! isset( $s['fade_others'] ) || 'yes' === $s['fade_others'] ? '1' : '0'; ?>">
			<div class="ts-pick__box">
				<?php if ( '' !== $question || '' !== $subline ) : ?>
					<div class="ts-pick__label">
						<?php if ( '' !== $question ) : ?>
							<div class="ts-pick__q" id="<?php echo esc_attr( $q_id ); ?>"><?php echo esc_html( $question ); ?></div>
						<?php endif; ?>
						<?php if ( '' !== $subline ) : ?>
							<div class="ts-pick__sub"><?php echo esc_html( $subline ); ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="ts-pick__btns" role="group"<?php echo '' !== $question ? ' aria-labelledby="' . esc_attr( $q_id ) . '"' : ''; ?>>
					<?php foreach ( $choices as $c ) : ?>
						<button type="button" class="ts-pick__btn" aria-pressed="false" aria-controls="<?php echo esc_attr( $tip_id ); ?>" data-parts="<?php echo esc_attr( implode( ' ', $c['ids'] ) ); ?>" data-tip="<?php echo esc_attr( $c['tip'] ); ?>"><?php echo esc_html( $c['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
			</div>
			<p class="ts-pick__tip" id="<?php echo esc_attr( $tip_id ); ?>" aria-live="polite"></p>
		</div>
		<?php
	}
}
