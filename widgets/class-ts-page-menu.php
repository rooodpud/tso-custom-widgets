<?php
/**
 * Widget: TS Page Menu — an "On this page" menu for long pages (1.8.0).
 *
 * The menu builds itself from the headings of the section it is placed in (nothing to type and
 * nothing to keep in step when a heading changes). On wide screens it is a list that stays in
 * view while scrolling and highlights the part being read; on narrow screens it becomes a
 * "Jump to section" dropdown that stays at the top of the screen.
 *
 * Placement: directly inside the section (container) that holds the content, beside or above
 * it — not in a column of its own — so it has the whole height of the section to stay in view.
 *
 * The list is filled in the visitor's browser (assets/js/ts-page-menu.js). Without JavaScript
 * the menu is not shown; the page itself reads the same.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Page_Menu extends TS_Base_Widget {

	public function get_name() {
		return 'ts-page-menu';
	}

	public function get_title() {
		return __( 'TS Page Menu', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	public function get_keywords() {
		return array( 'menu', 'contents', 'on this page', 'jump', 'sections', 'anchor', 'toc', 'travel' );
	}

	public function get_script_depends() {
		return array( 'ts-page-menu' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Menu', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'how_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'The menu lists the headings by itself. Place this widget directly inside the section that holds the content (beside or above it, not in a column of its own), so it can stay in view while the visitor scrolls.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title above the list', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'On this page', 'travelstore-custom-widgets' ),
				'description' => __( 'Leave empty to hide it.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'levels',
			array(
				'label'   => __( 'Headings to list', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h2'   => __( 'Main section headings (H2)', 'travelstore-custom-widgets' ),
					'h2h3' => __( 'Main headings and the ones below them (H2 and H3)', 'travelstore-custom-widgets' ),
					'h3'   => __( 'H3 headings only', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'scope',
			array(
				'label'   => __( 'Look for headings in', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'section',
				'options' => array(
					'section' => __( 'The section this menu is placed in', 'travelstore-custom-widgets' ),
					'page'    => __( 'The whole page', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_control(
			'dropdown_on',
			array(
				'label'       => __( 'Show as a dropdown', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'tablet',
				'options'     => array(
					'tablet' => __( 'On tablets and phones', 'travelstore-custom-widgets' ),
					'mobile' => __( 'On phones only', 'travelstore-custom-widgets' ),
					'always' => __( 'On every screen', 'travelstore-custom-widgets' ),
					'never'  => __( 'Never (always a list)', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'A dropdown takes one line and stays at the top of the screen. Use it wherever the menu sits above the content and not beside it.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'dropdown_label',
			array(
				'label'     => __( 'Label above the dropdown', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Jump to section', 'travelstore-custom-widgets' ),
				'condition' => array( 'dropdown_on!' => 'never' ),
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
			'list_width',
			array(
				'label'       => __( 'Width of the list', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 140, 'max' => 420 ) ),
				'default'     => array( 'size' => 240, 'unit' => 'px' ),
				'description' => __( 'Used while the menu is a list. The dropdown is always as wide as its section.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}}' => '--ts-pm-w: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'stick_top',
			array(
				'label'       => __( 'Distance from the top of the screen', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'description' => __( 'Where the menu stays while the visitor scrolls. Make it larger when the site header also stays at the top.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}}' => '--ts-pm-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'jump_space',
			array(
				'label'       => __( 'Space above a section after jumping to it', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors'   => array( '{{WRAPPER}}' => '--ts-pm-space: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: list ---------------- */
		$this->start_controls_section(
			'ts_section_text_style',
			array(
				'label' => __( 'List', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'title_typography', __( 'Title typography', 'travelstore-custom-widgets' ), '.ts-pm__title' );
		$this->add_color_control( 'title_color', __( 'Title colour', 'travelstore-custom-widgets' ), '.ts-pm__title' );
		$this->add_typography_control( 'link_typography', __( 'Link typography', 'travelstore-custom-widgets' ), '.ts-pm__link' );
		$this->add_color_control( 'link_color', __( 'Link colour', 'travelstore-custom-widgets' ), '.ts-pm .ts-pm__link' );
		$this->add_color_control( 'line_color', __( 'Line colour', 'travelstore-custom-widgets' ), '.ts-pm .ts-pm__link', 'border-left-color' );
		$this->add_color_control( 'active_color', __( 'Current section – text colour', 'travelstore-custom-widgets' ), '.ts-pm .ts-pm__link.is-active', 'color', 'ts_terracotta_dark' );
		$this->add_color_control( 'active_line', __( 'Current section – line colour', 'travelstore-custom-widgets' ), '.ts-pm .ts-pm__link.is-active', 'border-left-color', 'ts_terracotta' );
		$this->add_color_control( 'active_bg', __( 'Current section – background', 'travelstore-custom-widgets' ), '.ts-pm .ts-pm__link.is-active', 'background-color' );
		$this->add_responsive_control(
			'link_padding',
			array(
				'label'      => __( 'Link padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-pm .ts-pm__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: dropdown ---------------- */
		$this->start_controls_section(
			'ts_section_dropdown_style',
			array(
				'label'     => __( 'Dropdown', 'travelstore-custom-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'dropdown_on!' => 'never' ),
			)
		);
		$this->add_color_control( 'bar_bg', __( 'Background behind the dropdown', 'travelstore-custom-widgets' ), '.ts-pm__jump', 'background-color', '', array( 'description' => __( 'Use the colour of the section, so the page does not show through while the dropdown stays at the top.', 'travelstore-custom-widgets' ) ) );
		$this->add_typography_control( 'label_typography', __( 'Label typography', 'travelstore-custom-widgets' ), '.ts-pm__jump-label' );
		$this->add_color_control( 'label_color', __( 'Label colour', 'travelstore-custom-widgets' ), '.ts-pm__jump-label' );
		$this->add_typography_control( 'select_typography', __( 'Dropdown typography', 'travelstore-custom-widgets' ), '.ts-pm .ts-pm__select' );
		$this->add_color_control( 'select_color', __( 'Dropdown text colour', 'travelstore-custom-widgets' ), '.ts-pm .ts-pm__select' );
		$this->add_color_control( 'select_bg', __( 'Dropdown colour', 'travelstore-custom-widgets' ), '.ts-pm .ts-pm__select', 'background-color' );
		$this->add_color_control( 'select_border', __( 'Dropdown border colour', 'travelstore-custom-widgets' ), '.ts-pm .ts-pm__select', 'border-color' );
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$title    = isset( $s['title'] ) ? trim( (string) $s['title'] ) : '';
		$label    = isset( $s['dropdown_label'] ) ? trim( (string) $s['dropdown_label'] ) : '';
		$levels   = isset( $s['levels'] ) && in_array( $s['levels'], array( 'h2', 'h2h3', 'h3' ), true ) ? $s['levels'] : 'h2';
		$scope    = isset( $s['scope'] ) && 'page' === $s['scope'] ? 'page' : 'section';
		$dropdown = isset( $s['dropdown_on'] ) && in_array( $s['dropdown_on'], array( 'tablet', 'mobile', 'always', 'never' ), true ) ? $s['dropdown_on'] : 'tablet';
		$name     = '' !== $title ? $title : __( 'On this page', 'travelstore-custom-widgets' );
		$field    = 'ts-pm-' . $this->get_id();
		$editing  = \Elementor\Plugin::$instance->editor->is_edit_mode();
		?>
		<nav class="ts-w ts-pm ts-pm--drop-<?php echo esc_attr( $dropdown ); ?>" aria-label="<?php echo esc_attr( $name ); ?>" data-levels="<?php echo esc_attr( $levels ); ?>" data-scope="<?php echo esc_attr( $scope ); ?>">
			<div class="ts-pm__side">
				<?php if ( '' !== $title ) : ?>
					<div class="ts-pm__title"><?php echo esc_html( $title ); ?></div>
				<?php endif; ?>
				<ul class="ts-pm__list"></ul>
			</div>
			<?php if ( 'never' !== $dropdown ) : ?>
				<div class="ts-pm__jump">
					<label class="ts-pm__jump-label" for="<?php echo esc_attr( $field ); ?>"><?php echo esc_html( '' !== $label ? $label : __( 'Jump to section', 'travelstore-custom-widgets' ) ); ?></label>
					<select class="ts-pm__select" id="<?php echo esc_attr( $field ); ?>"></select>
				</div>
			<?php endif; ?>
			<?php if ( $editing ) : ?>
				<div class="ts-empty ts-pm__empty"><?php esc_html_e( 'No headings found yet. The menu lists the headings of the section it is placed in.', 'travelstore-custom-widgets' ); ?></div>
			<?php endif; ?>
		</nav>
		<?php
	}
}
