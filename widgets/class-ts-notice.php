<?php
/**
 * Widget: TS Notice — a box of formatted text with an optional icon and title (1.8.0).
 *
 * For notes beside the main content: "good to know", a warning, a tip, or a short block of
 * rules with a bulleted list. The text is formatted text, so it can hold paragraphs, a list and
 * links inside a sentence. Links to other websites are marked with an arrow and open in a new
 * tab, so visitors can see they lead away from this site.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Notice extends TS_Base_Widget {

	const LOOKS = array( 'info', 'warning', 'good', 'plain' );

	public function get_name() {
		return 'ts-notice';
	}

	public function get_title() {
		return __( 'TS Notice', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-info-box';
	}

	public function get_keywords() {
		return array( 'notice', 'note', 'callout', 'tip', 'warning', 'info', 'text', 'links', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Content', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'look',
			array(
				'label'   => __( 'Look', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'info',
				'options' => array(
					'info'    => __( 'Information (blue)', 'travelstore-custom-widgets' ),
					'warning' => __( 'Warning (orange)', 'travelstore-custom-widgets' ),
					'good'    => __( 'Good news (green)', 'travelstore-custom-widgets' ),
					'plain'   => __( 'Plain (sand)', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'icon',
			array(
				'label'       => __( 'Icon', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'default'     => array( 'value' => 'fas fa-info-circle', 'library' => 'fa-solid' ),
				'description' => __( 'Optional. Remove it to show the text only.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Optional.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_tag_control( 'title_tag', __( 'Title level', 'travelstore-custom-widgets' ), 'h3', array( 'condition' => array( 'title!' => '' ) ) );
		$this->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => __( 'A short note for your visitors.', 'travelstore-custom-widgets' ),
				'description' => __( 'To add a link, select the words and use the link button above the text.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_control(
			'mark_external',
			array(
				'label'        => __( 'Mark links to other websites', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Adds a small arrow after each link that leads to another website and opens it in a new tab, so visitors keep this page open.', 'travelstore-custom-widgets' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: box ---------------- */
		$this->start_controls_section(
			'ts_section_layout',
			array(
				'label' => __( 'Box', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'colors_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Leave the colours empty to follow the "Look" (Content).', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_color_control( 'box_bg', __( 'Box colour', 'travelstore-custom-widgets' ), '.ts-notice', 'background-color' );
		$this->add_color_control( 'box_border', __( 'Border colour', 'travelstore-custom-widgets' ), '.ts-notice', 'border-color' );
		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-notice' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-notice' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'max_width',
			array(
				'label'      => __( 'Width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 240, 'max' => 1200 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'selectors'  => array( '{{WRAPPER}} .ts-notice' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'icon_color', __( 'Icon colour', 'travelstore-custom-widgets' ), '.ts-notice__icon', 'color', '', array( 'separator' => 'before' ) );
		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon size', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 48 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-notice__icon' => 'font-size: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'icon_gap',
			array(
				'label'      => __( 'Space beside the icon', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-notice' => 'column-gap: {{SIZE}}{{UNIT}};' ),
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
		$this->add_typography_control( 'title_typography', __( 'Title typography', 'travelstore-custom-widgets' ), '.ts-notice__title' );
		$this->add_color_control( 'title_color', __( 'Title colour', 'travelstore-custom-widgets' ), '.ts-notice .ts-notice__title' );
		$this->add_typography_control( 'text_typography', __( 'Text typography', 'travelstore-custom-widgets' ), '.ts-notice__text' );
		$this->add_color_control( 'text_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-notice__text' );
		$this->add_color_control( 'link_color', __( 'Link colour', 'travelstore-custom-widgets' ), '.ts-notice .ts-notice__text a' );
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$title = isset( $s['title'] ) ? trim( (string) $s['title'] ) : '';
		$text  = isset( $s['text'] ) ? trim( (string) $s['text'] ) : '';

		if ( '' === $title && '' === $text ) {
			$this->render_empty( __( 'Add a title or some text to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$look     = isset( $s['look'] ) && in_array( $s['look'], self::LOOKS, true ) ? $s['look'] : 'info';
		$has_icon = ! empty( $s['icon']['value'] );
		$tag      = $this->safe_tag( isset( $s['title_tag'] ) ? $s['title_tag'] : 'h3', 'h3' );

		if ( '' !== $text ) {
			$text = wp_kses_post( $this->parse_text_editor( $text ) );
			if ( ! isset( $s['mark_external'] ) || 'yes' === $s['mark_external'] ) {
				$text = $this->mark_external_links( $text );
			}
		}
		?>
		<div class="ts-w ts-notice ts-notice--<?php echo esc_attr( $look ); ?>">
			<?php if ( $has_icon ) : ?>
				<div class="ts-notice__icon"><?php \Elementor\Icons_Manager::render_icon( $s['icon'], array( 'aria-hidden' => 'true' ) ); ?></div>
			<?php endif; ?>
			<div class="ts-notice__body">
				<?php
				if ( '' !== $title ) {
					printf( '<%1$s class="ts-notice__title">%2$s</%1$s>', esc_attr( $tag ), esc_html( $title ) );
				}
				if ( '' !== $text ) {
					echo '<div class="ts-notice__text">' . $text . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_kses_post above.
				}
				?>
			</div>
		</div>
		<?php
	}
}
