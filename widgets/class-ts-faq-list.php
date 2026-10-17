<?php
/**
 * Widget: TS FAQ List — questions and answers.
 *
 * "Always open" shows every answer as a divided list (like the drafts). "Click to
 * open" turns each item into a native, keyboard-friendly disclosure (details /
 * summary) with no JavaScript. Also suits any question/answer or title/text list.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_FAQ_List extends TS_Base_Widget {

	public function get_name() {
		return 'ts-faq-list';
	}

	public function get_title() {
		return __( 'TS FAQ List', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-help-o';
	}

	public function get_keywords() {
		return array( 'faq', 'questions', 'answers', 'accordion', 'list', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Questions', 'travelstore-custom-widgets' ) )
		);

		$r = new Repeater();
		$r->add_control(
			'question',
			array(
				'label'       => __( 'Question', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'answer',
			array(
				'label'   => __( 'Answer', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Questions and answers', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ question }}}',
				'default'     => array(
					array(
						'question' => __( 'What is the first question people ask?', 'travelstore-custom-widgets' ),
						'answer'   => __( 'A clear, friendly answer in a sentence or two.', 'travelstore-custom-widgets' ),
					),
					array(
						'question' => __( 'What is the second question?', 'travelstore-custom-widgets' ),
						'answer'   => __( 'Another short, helpful answer.', 'travelstore-custom-widgets' ),
					),
				),
			)
		);
		$this->add_tag_control( 'question_tag', __( 'Question level', 'travelstore-custom-widgets' ), 'h3' );
		$this->add_control(
			'mode',
			array(
				'label'   => __( 'Behaviour', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'list',
				'options' => array(
					'list'      => __( 'Always open (a simple list)', 'travelstore-custom-widgets' ),
					'accordion' => __( 'Click to open', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'one_open',
			array(
				'label'        => __( 'Only one open at a time', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'mode' => 'accordion' ),
			)
		);
		$this->add_control(
			'first_open',
			array(
				'label'        => __( 'First question open', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'mode' => 'accordion' ),
			)
		);
		$this->add_control(
			'schema',
			array(
				'label'        => __( 'Add FAQ search markup (SEO)', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Helps search engines understand the questions. Leave off unless asked.', 'travelstore-custom-widgets' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style ---------------- */
		$this->start_controls_section(
			'ts_section_layout',
			array(
				'label' => __( 'Layout', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'max_width',
			array(
				'label'      => __( 'Width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 280, 'max' => 1400 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'size' => 760, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-faq' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => __( 'Space above and below each item', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 60 ) ),
				'default'    => array( 'size' => 22, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-faq__item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'line_color', __( 'Divider colour', 'travelstore-custom-widgets' ), '.ts-faq__item', 'border-color' );
		$this->end_controls_section();

		$this->start_controls_section(
			'ts_section_text_style',
			array(
				'label' => __( 'Text', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'question_typography', __( 'Question typography', 'travelstore-custom-widgets' ), '.ts-faq__q', 'ts_heading' );
		$this->add_color_control( 'question_color', __( 'Question colour', 'travelstore-custom-widgets' ), '.ts-faq__q' );
		$this->add_typography_control( 'answer_typography', __( 'Answer typography', 'travelstore-custom-widgets' ), '.ts-faq__a', 'ts_body' );
		$this->add_color_control( 'answer_color', __( 'Answer colour', 'travelstore-custom-widgets' ), '.ts-faq__a' );
		// 1.9.3: links inside the answers (empty = dark orange, as in the drafts).
		$this->add_color_control( 'answer_link_color', __( 'Link colour (in answers)', 'travelstore-custom-widgets' ), '.ts-faq', '--ts-faq-link' );
		$this->add_color_control( 'icon_color', __( 'Open/close icon colour', 'travelstore-custom-widgets' ), '.ts-faq__icon', 'color' );
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = isset( $s['items'] ) && is_array( $s['items'] ) ? $s['items'] : array();
		$items = array_values(
			array_filter(
				$items,
				static function ( $item ) {
					return ! empty( $item['question'] );
				}
			)
		);

		if ( empty( $items ) ) {
			$this->render_empty( __( 'Add at least one question to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$accordion = isset( $s['mode'] ) && 'accordion' === $s['mode'];
		$tag       = $this->safe_tag( isset( $s['question_tag'] ) ? $s['question_tag'] : 'h3', 'h3' );
		$group     = 'ts-faq-' . $this->get_id();
		?>
		<div class="ts-w ts-faq ts-faq--<?php echo $accordion ? 'accordion' : 'list'; ?>">
			<?php foreach ( $items as $i => $item ) : ?>
				<?php $answer = isset( $item['answer'] ) ? (string) $item['answer'] : ''; ?>
				<?php if ( $accordion ) : ?>
					<details class="ts-faq__item"
						<?php echo ! empty( $s['one_open'] ) ? 'name="' . esc_attr( $group ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo ( 0 === $i && ! empty( $s['first_open'] ) ) ? 'open' : ''; ?>>
						<summary class="ts-faq__summary">
							<<?php echo esc_attr( $tag ); ?> class="ts-faq__q"><?php echo esc_html( $item['question'] ); ?></<?php echo esc_attr( $tag ); ?>>
							<span class="ts-faq__icon" aria-hidden="true"></span>
						</summary>
						<div class="ts-faq__a"><?php echo wp_kses_post( $answer ); ?></div>
					</details>
				<?php else : ?>
					<div class="ts-faq__item">
						<<?php echo esc_attr( $tag ); ?> class="ts-faq__q"><?php echo esc_html( $item['question'] ); ?></<?php echo esc_attr( $tag ); ?>>
						<div class="ts-faq__a"><?php echo wp_kses_post( $answer ); ?></div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php
			if ( ! empty( $s['schema'] ) ) {
				$entities = array();
				foreach ( $items as $item ) {
					$entities[] = array(
						'@type'          => 'Question',
						'name'           => wp_strip_all_tags( $item['question'] ),
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => wp_kses_post( isset( $item['answer'] ) ? $item['answer'] : '' ),
						),
					);
				}
				echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $entities ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			?>
		</div>
		<?php
	}
}
