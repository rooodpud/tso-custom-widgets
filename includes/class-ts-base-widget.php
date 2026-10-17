<?php
/**
 * Base class for the reusable Travel Store widgets (DEC-015).
 *
 * Shared contracts every TS widget follows:
 *  - every visual property a page needs is an editor control (no page CSS);
 *  - colours/fonts come from the Travel Store Global Colors/Fonts (DEC-016) through
 *    CSS variables that fall back to the draft values;
 *  - an editor-only empty state, a heading-tag control, and one shared button system;
 *  - a collapsed, admin-only "Extra CSS" escape hatch in the Advanced tab.
 *
 * Saved control IDs are frozen once a page uses them — never rename them.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class TS_Base_Widget extends \Elementor\Widget_Base {

	public function get_categories() {
		return array( 'travel-store' );
	}

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	public function get_style_depends() {
		return array( 'ts-base', $this->get_name() );
	}

	/* ------------------------------------------------------------------ */
	/* Option lists                                                        */
	/* ------------------------------------------------------------------ */

	/**
	 * Colour tones used by chips and icon tiles (all drawn from the palette).
	 */
	public static function tone_options( $with_none = false ) {
		$tones = array(
			'gold'       => __( 'Gold', 'travelstore-custom-widgets' ),
			'terracotta' => __( 'Terracotta', 'travelstore-custom-widgets' ),
			'oasis'      => __( 'Oasis (green)', 'travelstore-custom-widgets' ),
			'turquoise'  => __( 'Turquoise', 'travelstore-custom-widgets' ),
			'espresso'   => __( 'Espresso (brown)', 'travelstore-custom-widgets' ),
			'white'      => __( 'White, gold dot (for coloured backgrounds)', 'travelstore-custom-widgets' ),
		);

		return $with_none ? array( 'none' => __( 'None', 'travelstore-custom-widgets' ) ) + $tones : $tones;
	}

	protected function safe_tone( $tone, $default = 'gold' ) {
		return array_key_exists( $tone, self::tone_options( true ) ) ? $tone : $default;
	}

	protected function heading_tag_options() {
		return array(
			'h1'  => 'H1',
			'h2'  => 'H2',
			'h3'  => 'H3',
			'h4'  => 'H4',
			'h5'  => 'H5',
			'h6'  => 'H6',
			'p'   => 'p',
			'div' => 'div',
		);
	}

	protected function safe_tag( $tag, $default = 'h3' ) {
		return array_key_exists( $tag, $this->heading_tag_options() ) ? $tag : $default;
	}

	protected function button_style_options() {
		return array(
			'primary'   => __( 'Primary (orange)', 'travelstore-custom-widgets' ),
			'secondary' => __( 'Outline', 'travelstore-custom-widgets' ),
			'dark'      => __( 'Dark (brown)', 'travelstore-custom-widgets' ),
			'light'     => __( 'Light (white)', 'travelstore-custom-widgets' ),
			'cream'     => __( 'Cream', 'travelstore-custom-widgets' ),
			'gold'      => __( 'Gold', 'travelstore-custom-widgets' ),
		);
	}

	/**
	 * Split a textarea into trimmed, non-empty lines.
	 */
	protected function lines( $text ) {
		// Real line breaks only: without the u flag, \R also matches byte 0x85, which is inside UTF-8 characters such as ★ or Å.
		$lines = preg_split( '/\r\n|\r|\n/', (string) $text );

		return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
	}

	/* ------------------------------------------------------------------ */
	/* Shared controls                                                     */
	/* ------------------------------------------------------------------ */

	/**
	 * @param array $extra Optional extra control arguments, e.g. array( 'condition' => … ).
	 */
	protected function add_tag_control( $id, $label, $default, $extra = array() ) {
		$this->add_control(
			$id,
			array_merge(
				array(
					'label'   => $label,
					'type'    => Controls_Manager::SELECT,
					'default' => $default,
					'options' => $this->heading_tag_options(),
				),
				$extra
			)
		);
	}

	/**
	 * Limit every part of a selector list to this widget instance: ".a, .b" becomes
	 * "{{WRAPPER}} .a, {{WRAPPER}} .b" (a part that already starts with {{WRAPPER}} is kept).
	 * Without this, only the first part would be limited and a setting would also change every
	 * other widget of the same kind on the page (ISSUE-018). Commas inside brackets, as in
	 * :not(.a, .b), are not split.
	 */
	protected function scoped( $selector ) {
		$parts = array();
		$depth = 0;
		$buf   = '';
		foreach ( str_split( (string) $selector ) as $ch ) {
			if ( '(' === $ch ) {
				++$depth;
			} elseif ( ')' === $ch ) {
				--$depth;
			}
			if ( ',' === $ch && 0 === $depth ) {
				$parts[] = $buf;
				$buf     = '';
				continue;
			}
			$buf .= $ch;
		}
		$parts[] = $buf;
		foreach ( $parts as $i => $part ) {
			$part        = trim( $part );
			$parts[ $i ] = 0 === strpos( $part, '{{WRAPPER}}' ) ? $part : '{{WRAPPER}} ' . $part;
		}

		return implode( ', ', $parts );
	}

	/**
	 * Typography group; $global is the id of a Travel Store Global Font (e.g. ts_heading).
	 * With a global default, Elementor also loads the Google font for the page.
	 * $extra: optional extra group arguments, e.g. array( 'condition' => … ).
	 */
	protected function add_typography_control( $name, $label, $selector, $global = '', $extra = array() ) {
		$args = array(
			'name'     => $name,
			'label'    => $label,
			'selector' => $this->scoped( $selector ),
		);
		if ( $global ) {
			$args['global'] = array( 'default' => 'globals/typography?id=' . $global );
		}
		$this->add_group_control( Group_Control_Typography::get_type(), array_merge( $args, $extra ) );
	}

	/**
	 * @param string $global Optional Site Settings colour id (e.g. 'ts_espresso'). When set, the control
	 *                       starts linked to that Global Colour, so the editor shows where the colour comes from.
	 * @param array  $extra  Optional extra control arguments, e.g. array( 'condition' => … ).
	 */
	protected function add_color_control( $name, $label, $selector, $property = 'color', $global = '', $extra = array() ) {
		$args = array(
			'label'     => $label,
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( $this->scoped( $selector ) => $property . ': {{VALUE}};' ),
		);
		if ( $global ) {
			$args['global'] = array( 'default' => 'globals/colors?id=' . $global );
		}
		$this->add_control( $name, array_merge( $args, $extra ) );
	}

	/**
	 * Text + link + style controls for one button.
	 */
	protected function add_button_controls( $prefix, $label, $default_style, $condition = array(), $default_text = '' ) {
		$args_common = empty( $condition ) ? array() : array( 'condition' => $condition );

		$this->add_control(
			$prefix . '_heading',
			array(
				'label'     => $label,
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			) + $args_common
		);
		$this->add_control(
			$prefix . '_text',
			array(
				'label'       => __( 'Button text', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_text,
				'description' => __( 'Leave empty to hide this button.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			) + $args_common
		);
		$this->add_control(
			$prefix . '_link',
			array(
				'label'       => __( 'Button link', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'Type a page name or paste a link', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			) + $args_common
		);
		$this->add_control(
			$prefix . '_style',
			array(
				'label'   => __( 'Button look', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $default_style,
				'options' => $this->button_style_options(),
			) + $args_common
		);
	}

	/**
	 * Optional look-and-feel controls for the buttons inside a widget.
	 */
	protected function add_button_style_section( $id = 'ts_section_buttons_style' ) {
		$this->start_controls_section(
			$id,
			array(
				'label' => __( 'Buttons', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_typography_control( 'button_typography', __( 'Typography', 'travelstore-custom-widgets' ), '.ts-btn' );
		$this->add_control(
			'button_radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'button_primary_bg', __( 'Primary button colour', 'travelstore-custom-widgets' ), '.ts-btn--primary', 'background-color' );
		$this->add_color_control( 'button_primary_text', __( 'Primary button text', 'travelstore-custom-widgets' ), '.ts-btn--primary' );
		$this->add_color_control( 'button_secondary_border', __( 'Outline button border', 'travelstore-custom-widgets' ), '.ts-btn--secondary', 'border-color' );
		// 1.9.2: a faint fill for outline buttons on coloured panels (drafts' .btn-light).
		$this->add_color_control( 'button_secondary_bg', __( 'Outline button fill', 'travelstore-custom-widgets' ), '.ts-btn--secondary', 'background-color' );
		$this->add_color_control( 'button_secondary_text', __( 'Outline button text', 'travelstore-custom-widgets' ), '.ts-btn--secondary' );
		$this->end_controls_section();
	}

	/**
	 * Look of the rating bars (call inside a style section).
	 */
	protected function add_rating_style_controls( $condition = array() ) {
		$common = empty( $condition ) ? array() : array( 'condition' => $condition );
		$this->add_control(
			'rating_heading',
			array(
				'label'     => __( 'Rating bars', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			) + $common
		);
		$this->add_control(
			'rating_on',
			array(
				'label'     => __( 'Filled bar colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => 'globals/colors?id=ts_terracotta' ),
				'selectors' => array( '{{WRAPPER}}' => '--ts-rating-on: {{VALUE}};' ),
			) + $common
		);
		$this->add_control(
			'rating_off',
			array(
				'label'     => __( 'Empty bar colour', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}}' => '--ts-rating-off: {{VALUE}};' ),
			) + $common
		);
		$this->add_responsive_control(
			'rating_w',
			array(
				'label'      => __( 'Bar width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}}' => '--ts-rating-w: {{SIZE}}{{UNIT}};' ),
			) + $common
		);
		$this->add_responsive_control(
			'rating_h',
			array(
				'label'      => __( 'Bar height', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 2, 'max' => 24 ) ),
				'selectors'  => array( '{{WRAPPER}}' => '--ts-rating-h: {{SIZE}}{{UNIT}};' ),
			) + $common
		);
	}

	/**
	 * Collapsed, admin-only escape hatch. Call last in register_controls().
	 */
	/* ------------------------------------------------------------------ */
	/* Native Elementor image options (same controls as the Image widget)  */
	/* ------------------------------------------------------------------ */

	/**
	 * The Image widget's content options for one picture, placed right after its media field:
	 * Image Resolution, Caption (None / Attachment Caption / Custom Caption) and Link (None /
	 * Media File with Lightbox / Custom URL). Shown only once a picture is chosen.
	 *
	 * @param \Elementor\Controls_Stack $target A Repeater (one picture per item) or the widget itself.
	 * @param string                    $media  The media control's id ('image', 'photo').
	 * @param array                     $args   size: default resolution (the size used before 1.6.3);
	 *                                          caption / link: false to leave them out; link_note: extra help text.
	 */
	protected function add_image_options( $target, $media = 'image', $args = array() ) {
		$args = array_merge( array( 'size' => 'large', 'caption' => true, 'link' => true, 'link_note' => '' ), $args );
		$has  = array( $media . '[url]!' => '' );

		// Usage: {$media}_size and {$media}_custom_dimension, as in Elementor's Image widget.
		$target->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => $media,
				'default'   => $args['size'],
				'condition' => $has,
			)
		);
		if ( $args['caption'] ) {
			$target->add_control(
				$media . '_caption_source',
				array(
					'label'     => __( 'Caption', 'travelstore-custom-widgets' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'none',
					'options'   => array(
						'none'       => __( 'None', 'travelstore-custom-widgets' ),
						'attachment' => __( 'Attachment Caption', 'travelstore-custom-widgets' ),
						'custom'     => __( 'Custom Caption', 'travelstore-custom-widgets' ),
					),
					'description' => __( 'Shown under the picture. "Attachment Caption" is the caption saved with the image in the Media Library.', 'travelstore-custom-widgets' ),
					'condition' => $has,
				)
			);
			$target->add_control(
				$media . '_caption',
				array(
					'label'       => __( 'Custom Caption', 'travelstore-custom-widgets' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => __( 'Enter your image caption', 'travelstore-custom-widgets' ),
					'dynamic'     => array( 'active' => true ),
					'condition'   => array_merge( $has, array( $media . '_caption_source' => 'custom' ) ),
				)
			);
		}
		if ( $args['link'] ) {
			$target->add_control(
				$media . '_link_to',
				array(
					'label'       => __( 'Link', 'travelstore-custom-widgets' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'none',
					'options'     => array(
						'none'   => __( 'None', 'travelstore-custom-widgets' ),
						'file'   => __( 'Media File', 'travelstore-custom-widgets' ),
						'custom' => __( 'Custom URL', 'travelstore-custom-widgets' ),
					),
					'description' => trim( __( '"Media File" opens the full-size picture.', 'travelstore-custom-widgets' ) . ' ' . $args['link_note'] ),
					'condition'   => $has,
				)
			);
			$target->add_control(
				$media . '_link',
				array(
					'label'      => __( 'Link', 'travelstore-custom-widgets' ),
					'type'       => Controls_Manager::URL,
					'dynamic'    => array( 'active' => true ),
					'show_label' => false,
					'condition'  => array_merge( $has, array( $media . '_link_to' => 'custom' ) ),
				)
			);
			$target->add_control(
				$media . '_lightbox',
				array(
					'label'       => __( 'Lightbox', 'travelstore-custom-widgets' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'default',
					'options'     => array(
						'default' => __( 'Default', 'travelstore-custom-widgets' ),
						'yes'     => __( 'Yes', 'travelstore-custom-widgets' ),
						'no'      => __( 'No', 'travelstore-custom-widgets' ),
					),
					'description' => __( 'Yes = the picture opens enlarged over the page. Default follows Site Settings > Lightbox.', 'travelstore-custom-widgets' ),
					'condition'   => array_merge( $has, array( $media . '_link_to' => 'file' ) ),
				)
			);
		}
	}

	/**
	 * The Image widget's style options for the pictures of this widget: Object Fit, Object Position,
	 * Opacity and CSS Filters (normal / hover), Transition Duration, Hover Animation, Border and Box Shadow.
	 * Call inside a Style section. Control ids start with image_ (frozen once a page uses them).
	 *
	 * @param string $img   Selector of the picture (img).
	 * @param string $frame Selector of the box around the picture (border and shadow go here, so rounded corners stay).
	 * @param string $hover Selector of the picture while hovered, e.g. ".ts-tabs__frame:hover .ts-tabs__img".
	 * @param array  $args  fit: false when the widget already has its own fit control; border / shadow: false to leave out.
	 */
	protected function add_image_style_controls( $img, $frame, $hover, $args = array() ) {
		$args = array_merge( array( 'fit' => true, 'border' => true, 'radius' => false, 'shadow' => true ), $args );
		$img  = $this->scoped( $img );

		if ( $args['fit'] ) {
			$this->add_responsive_control(
				'image_object_fit',
				array(
					'label'     => __( 'Object Fit', 'travelstore-custom-widgets' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '',
					'options'   => array(
						''           => __( 'Default', 'travelstore-custom-widgets' ),
						'fill'       => __( 'Fill (stretch, may distort)', 'travelstore-custom-widgets' ),
						'cover'      => __( 'Cover (fill the area, edges may be cropped)', 'travelstore-custom-widgets' ),
						'contain'    => __( 'Contain (show the whole picture)', 'travelstore-custom-widgets' ),
						'scale-down' => __( 'Scale Down (never larger than the photo itself)', 'travelstore-custom-widgets' ),
					),
					'selectors' => array( $img => 'object-fit: {{VALUE}};' ),
				)
			);
		}
		$this->add_responsive_control(
			'image_object_position',
			array(
				'label'       => __( 'Object Position', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''              => __( 'Default', 'travelstore-custom-widgets' ),
					'center center' => __( 'Center Center', 'travelstore-custom-widgets' ),
					'center left'   => __( 'Center Left', 'travelstore-custom-widgets' ),
					'center right'  => __( 'Center Right', 'travelstore-custom-widgets' ),
					'top center'    => __( 'Top Center', 'travelstore-custom-widgets' ),
					'top left'      => __( 'Top Left', 'travelstore-custom-widgets' ),
					'top right'     => __( 'Top Right', 'travelstore-custom-widgets' ),
					'bottom center' => __( 'Bottom Center', 'travelstore-custom-widgets' ),
					'bottom left'   => __( 'Bottom Left', 'travelstore-custom-widgets' ),
					'bottom right'  => __( 'Bottom Right', 'travelstore-custom-widgets' ),
					'custom'        => __( 'Custom (X and Y below)', 'travelstore-custom-widgets' ),
				),
				'description' => __( 'Which part of the photo stays in view when it is cropped.', 'travelstore-custom-widgets' ),
				// 1.9.6: "Custom" reads the X / Y sliders below.
				'selectors_dictionary' => array( 'custom' => 'var(--ts-obj-x, 50%) var(--ts-obj-y, 50%)' ),
				'selectors'   => array( $img => 'object-position: {{VALUE}};' ),
			)
		);
		foreach ( array( 'x' => __( 'X Position (left to right)', 'travelstore-custom-widgets' ), 'y' => __( 'Y Position (top to bottom)', 'travelstore-custom-widgets' ) ) as $axis => $label ) {
			$this->add_responsive_control(
				'image_object_position_' . $axis,
				array(
					'label'      => $label,
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'range'      => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
					'default'    => array( 'size' => 50, 'unit' => '%' ),
					'selectors'  => array( $img => '--ts-obj-' . $axis . ': {{SIZE}}{{UNIT}};' ),
					'condition'  => array( 'image_object_position' => 'custom' ),
				)
			);
		}
		$this->add_control(
			'image_effects_divider',
			array(
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->start_controls_tabs( 'image_effects' );

		$this->start_controls_tab( 'image_normal', array( 'label' => __( 'Normal', 'travelstore-custom-widgets' ) ) );
		$this->add_control(
			'image_opacity',
			array(
				'label'     => __( 'Opacity', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0.1, 'max' => 1, 'step' => 0.01 ) ),
				'selectors' => array( $img => 'opacity: {{SIZE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'image_css_filters',
				'selector' => $img,
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'image_hover', array( 'label' => __( 'Hover', 'travelstore-custom-widgets' ) ) );
		$this->add_control(
			'image_opacity_hover',
			array(
				'label'     => __( 'Opacity', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0.1, 'max' => 1, 'step' => 0.01 ) ),
				'selectors' => array( $this->scoped( $hover ) => 'opacity: {{SIZE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'image_css_filters_hover',
				'selector' => $this->scoped( $hover ),
			)
		);
		$this->add_control(
			'image_transition',
			array(
				'label'     => __( 'Transition Duration', 'travelstore-custom-widgets' ) . ' (s)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ) ),
				'selectors' => array( $img => 'transition-duration: {{SIZE}}s;' ),
			)
		);
		$this->add_control(
			'image_hover_animation',
			array(
				'label' => __( 'Hover Animation', 'travelstore-custom-widgets' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		if ( $args['border'] ) {
			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'image_border',
					'selector'  => $this->scoped( $frame ),
					'separator' => 'before',
				)
			);
		}
		if ( $args['radius'] ) {
			$this->add_responsive_control(
				'image_border_radius',
				array(
					'label'      => __( 'Border Radius', 'travelstore-custom-widgets' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em', 'rem' ),
					'selectors'  => array( $this->scoped( $frame ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
				)
			);
		}
		if ( $args['shadow'] ) {
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'image_box_shadow',
					'exclude'  => array( 'box_shadow_position' ),
					'selector' => $this->scoped( $frame ),
				)
			);
		}
	}

	/**
	 * The Image widget's Caption style section (alignment, text colour, background, typography,
	 * text shadow, spacing) for the captions under this widget's pictures.
	 */
	protected function add_image_caption_style_section() {
		$sel = $this->scoped( '.ts-pic__caption' );

		$this->start_controls_section(
			'ts_section_image_caption_style',
			array(
				'label' => __( 'Picture caption', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'image_caption_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Used when a picture has a caption (Content tab, under the picture: Caption).', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_responsive_control(
			'image_caption_align',
			array(
				'label'     => __( 'Alignment', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'start'   => array( 'title' => __( 'Start', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-left' ),
					'center'  => array( 'title' => __( 'Center', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-center' ),
					'end'     => array( 'title' => __( 'End', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-right' ),
					'justify' => array( 'title' => __( 'Justified', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-justify' ),
				),
				'selectors' => array( $sel => 'text-align: {{VALUE}};' ),
			)
		);
		$this->add_color_control( 'image_caption_color', __( 'Text Color', 'travelstore-custom-widgets' ), '.ts-pic__caption' );
		$this->add_color_control( 'image_caption_bg', __( 'Background Color', 'travelstore-custom-widgets' ), '.ts-pic__caption', 'background-color' );
		$this->add_typography_control( 'image_caption_typography', __( 'Typography', 'travelstore-custom-widgets' ), '.ts-pic__caption' );
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'image_caption_text_shadow',
				'selector' => $sel,
			)
		);
		$this->add_responsive_control(
			'image_caption_space',
			array(
				'label'      => __( 'Spacing', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'max' => 100 ) ),
				'selectors'  => array( $sel => 'margin-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * <img> for a chosen picture at its Image Resolution (or '' when none is chosen).
	 * With the default resolution and no hover animation the markup is the same as before 1.6.3.
	 *
	 * @param array       $item  Repeater item or widget settings holding the media field.
	 * @param string|null $alt   Alt text; null = the Media Library's alt text.
	 * @param array       $attrs Extra attributes (class, loading, width, height).
	 */
	protected function image_tag( $item, $media, $default_size, $alt, $attrs ) {
		$m   = isset( $item[ $media ] ) && is_array( $item[ $media ] ) ? $item[ $media ] : array();
		$id  = ! empty( $m['id'] ) ? (int) $m['id'] : 0;
		$url = ! empty( $m['url'] ) && false === strpos( (string) $m['url'], 'placeholder' ) ? (string) $m['url'] : '';
		if ( ! $id && '' === $url ) {
			return '';
		}

		$attrs['class'] = trim( ( isset( $attrs['class'] ) ? $attrs['class'] : '' ) . $this->image_anim_class() );
		if ( null !== $alt ) {
			$attrs['alt'] = $alt;
		}

		$size = ! empty( $item[ $media . '_size' ] ) ? (string) $item[ $media . '_size' ] : $default_size;
		if ( $id && 'custom' !== $size ) {
			return (string) wp_get_attachment_image( $id, $size, false, $attrs );
		}
		if ( $id ) {
			$url = (string) Group_Control_Image_Size::get_attachment_image_src( $id, $media, $item );
			if ( ! isset( $attrs['alt'] ) ) {
				$attrs['alt'] = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
			}
		}
		if ( '' === $url ) {
			return '';
		}
		$html = '<img src="' . esc_url( $url ) . '"';
		foreach ( array_merge( array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ), $attrs ) as $k => $v ) {
			$html .= ' ' . $k . '="' . esc_attr( $v ) . '"';
		}
		return $html . ' />';
	}

	/**
	 * " elementor-animation-…" for the widget's Hover Animation ('' when none), with its stylesheet loaded.
	 * Added to every picture of the widget (photos and art scenes alike).
	 */
	protected function image_anim_class() {
		$anim = (string) $this->get_settings_for_display( 'image_hover_animation' );
		if ( '' === $anim || 'none' === $anim ) {
			return '';
		}
		\Elementor\Plugin::$instance->assets_loader->enable_assets( array( 'styles' => array( 'e-animation-' . $anim ) ) );
		return ' elementor-animation-' . sanitize_html_class( $anim );
	}

	/**
	 * URL of a chosen picture at its Image Resolution (for pictures shown as a background).
	 */
	protected function image_url( $item, $media, $default_size ) {
		$m = isset( $item[ $media ] ) && is_array( $item[ $media ] ) ? $item[ $media ] : array();
		if ( ! empty( $m['id'] ) ) {
			$size = ! empty( $item[ $media . '_size' ] ) ? (string) $item[ $media . '_size' ] : $default_size;
			return 'custom' === $size
				? (string) Group_Control_Image_Size::get_attachment_image_src( (int) $m['id'], $media, $item )
				: (string) wp_get_attachment_image_url( (int) $m['id'], $size );
		}
		return ! empty( $m['url'] ) && false === strpos( (string) $m['url'], 'placeholder' ) ? (string) $m['url'] : '';
	}

	/**
	 * Wraps a picture in its Link (Media File with lightbox, or Custom URL) when one is set.
	 */
	protected function image_linked( $html, $item, $media, $key ) {
		$m  = isset( $item[ $media ] ) && is_array( $item[ $media ] ) ? $item[ $media ] : array();
		$to = isset( $item[ $media . '_link_to' ] ) ? (string) $item[ $media . '_link_to' ] : 'none';
		if ( '' === $html || ( empty( $m['id'] ) && empty( $m['url'] ) ) ) {
			return $html;
		}
		if ( 'file' === $to ) {
			$href = ! empty( $m['id'] ) ? (string) wp_get_attachment_url( (int) $m['id'] ) : (string) $m['url'];
			$this->add_render_attribute( $key, 'href', esc_url( $href ) );
			$this->add_lightbox_data_attributes( $key, ! empty( $m['id'] ) ? (int) $m['id'] : null, isset( $item[ $media . '_lightbox' ] ) ? $item[ $media . '_lightbox' ] : 'default', $this->get_id() );
		} elseif ( 'custom' === $to && ! empty( $item[ $media . '_link' ]['url'] ) ) {
			$this->add_link_attributes( $key, $item[ $media . '_link' ] );
		} else {
			return $html;
		}
		$this->add_render_attribute( $key, 'class', 'ts-pic__link' );
		return '<a ' . $this->get_render_attribute_string( $key ) . '>' . $html . '</a>';
	}

	/**
	 * The picture's caption as a line under it ('' when Caption is None or empty).
	 */
	protected function image_caption( $item, $media, $tag = 'div' ) {
		$m      = isset( $item[ $media ] ) && is_array( $item[ $media ] ) ? $item[ $media ] : array();
		$source = isset( $item[ $media . '_caption_source' ] ) ? (string) $item[ $media . '_caption_source' ] : 'none';
		$text   = '';
		if ( 'attachment' === $source && ! empty( $m['id'] ) ) {
			$text = (string) wp_get_attachment_caption( (int) $m['id'] );
		} elseif ( 'custom' === $source && ( ! empty( $m['id'] ) || ! empty( $m['url'] ) ) ) {
			$text = isset( $item[ $media . '_caption' ] ) ? (string) $item[ $media . '_caption' ] : '';
		}
		$tag = 'figcaption' === $tag ? 'figcaption' : 'div';
		return '' !== trim( $text ) ? '<' . $tag . ' class="ts-pic__caption">' . esc_html( $text ) . '</' . $tag . '>' : '';
	}

	protected function register_extra_css_controls() {
		$editing = \Elementor\Plugin::$instance->editor->is_edit_mode() || is_admin();
		if ( $editing && ! current_user_can( 'unfiltered_html' ) ) {
			return;
		}

		$this->start_controls_section(
			'ts_section_extra_css',
			array(
				'label' => __( 'Extra CSS (developer)', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);
		$this->add_control(
			'ts_extra_css_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Optional safety valve for developers. Pages must not depend on it: everything a page needs is a normal control. Use the word "selector" for this widget, for example: selector .ts-card__title { letter-spacing: .02em; }. Applied after Update; reload the preview.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_control(
			\TravelStore\TS_Extra_CSS::SETTING,
			array(
				'label'      => __( 'Extra CSS', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::CODE,
				'language'   => 'css',
				'rows'       => 10,
				'render_type' => 'none',
			)
		);
		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ */
	/* Contact links (1.7.0): phone, WhatsApp and email without typing     */
	/* "tel:" or "mailto:" (REQ-015)                                       */
	/* ------------------------------------------------------------------ */

	/**
	 * "Link goes to" (Web page / Phone call / WhatsApp chat / Email) and the number or address,
	 * placed before an existing URL control $prefix (which stays the "Web page" link).
	 *
	 * @param \Elementor\Controls_Stack $target   A Repeater or the widget itself.
	 * @param string                    $prefix   The URL control's id, e.g. 'link'. New ids: {prefix}_kind, {prefix}_contact.
	 * @param string                    $fallback Help text naming the field used when the number or address is left empty ('' = none).
	 * @param array                     $extra    Optional extra arguments for both controls, e.g. array( 'condition' => … ).
	 */
	protected function add_contact_link_controls( $target, $prefix = 'link', $fallback = '', $extra = array() ) {
		$target->add_control(
			$prefix . '_kind',
			array_merge(
				array(
					'label'   => __( 'Link goes to', 'travelstore-custom-widgets' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'url',
					'options' => array(
						'url'      => __( 'A web page', 'travelstore-custom-widgets' ),
						'phone'    => __( 'A phone call', 'travelstore-custom-widgets' ),
						'whatsapp' => __( 'A WhatsApp chat', 'travelstore-custom-widgets' ),
						'email'    => __( 'An email', 'travelstore-custom-widgets' ),
					),
				),
				$extra
			)
		);
		$condition = isset( $extra['condition'] ) ? $extra['condition'] : array();
		$target->add_control(
			$prefix . '_contact',
			array_merge(
				$extra,
				array(
					'label'       => __( 'Phone number or email address', 'travelstore-custom-widgets' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'placeholder' => __( 'For example: +968 7410 0010', 'travelstore-custom-widgets' ),
					'description' => trim( __( 'Phone and WhatsApp: include the country code; spaces are fine.', 'travelstore-custom-widgets' ) . ' ' . $fallback ),
					'dynamic'     => array( 'active' => true ),
					'condition'   => array_merge( $condition, array( $prefix . '_kind!' => 'url' ) ),
				)
			)
		);
	}

	/**
	 * Link settings (for add_link_attributes) from the controls above; empty url when nothing is set.
	 *
	 * @param array  $item     Settings or repeater row.
	 * @param string $prefix   The URL control's id.
	 * @param string $fallback Number or address used when the contact field is empty.
	 */
	protected function contact_link( $item, $prefix = 'link', $fallback = '' ) {
		$kind = isset( $item[ $prefix . '_kind' ] ) ? (string) $item[ $prefix . '_kind' ] : 'url';
		if ( ! in_array( $kind, array( 'phone', 'whatsapp', 'email' ), true ) ) {
			$link = isset( $item[ $prefix ] ) && is_array( $item[ $prefix ] ) ? $item[ $prefix ] : array();
			return empty( $link['url'] ) ? array( 'url' => '' ) : $link;
		}
		$contact = isset( $item[ $prefix . '_contact' ] ) ? trim( (string) $item[ $prefix . '_contact' ] ) : '';
		if ( '' === $contact ) {
			$contact = trim( (string) $fallback );
		}

		return array(
			'url'         => self::contact_href( $kind, $contact ),
			'is_external' => 'whatsapp' === $kind ? 'on' : '',
			'nofollow'    => '',
		);
	}

	/**
	 * tel: / wa.me / mailto: address for a typed number or email ('' when it cannot be read).
	 */
	public static function contact_href( $kind, $contact ) {
		if ( 'email' === $kind ) {
			$email = sanitize_email( $contact );
			return is_email( $email ) ? 'mailto:' . $email : '';
		}
		$digits = preg_replace( '/\D+/', '', (string) $contact );
		if ( 'whatsapp' === $kind ) {
			$digits = preg_replace( '/^00/', '', $digits );
			return '' !== $digits ? 'https://wa.me/' . $digits : '';
		}
		if ( '' === $digits ) {
			return '';
		}
		$plus = 0 === strpos( ltrim( (string) $contact ), '+' ) ? '+' : '';

		return 'tel:' . $plus . $digits;
	}

	/* ------------------------------------------------------------------ */
	/* Links to other websites (1.8.0): marked with an arrow, new tab      */
	/* ------------------------------------------------------------------ */

	/**
	 * True when a web address points to another website (not this site; not tel:, mailto: or #section).
	 */
	public static function is_other_site( $url ) {
		$parts = wp_parse_url( trim( (string) $url ) );
		if ( empty( $parts['host'] ) || ( isset( $parts['scheme'] ) && ! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) ) ) {
			return false;
		}
		$strip = static function ( $host ) {
			return preg_replace( '/^www\./', '', strtolower( (string) $host ) );
		};

		return $strip( $parts['host'] ) !== $strip( wp_parse_url( home_url(), PHP_URL_HOST ) );
	}

	/**
	 * Words read out by screen readers after a link that opens another website.
	 */
	protected static function external_note() {
		return '<span class="ts-sr"> ' . esc_html__( '(opens another website in a new tab)', 'travelstore-custom-widgets' ) . '</span>';
	}

	/**
	 * In formatted text, every link to another website gets the arrow (class ts-ext), opens in a
	 * new tab and is announced as such. Links within this site are left as they are.
	 */
	protected function mark_external_links( $html ) {
		$html = (string) $html;
		if ( false === stripos( $html, '<a' ) || ! class_exists( '\WP_HTML_Tag_Processor' ) ) {
			return $html;
		}
		$tags  = new \WP_HTML_Tag_Processor( $html );
		$found = false;
		while ( $tags->next_tag( 'a' ) ) {
			$href = $tags->get_attribute( 'href' );
			if ( ! is_string( $href ) || ! self::is_other_site( $href ) ) {
				continue;
			}
			$rel = trim( (string) $tags->get_attribute( 'rel' ) );
			$tags->add_class( 'ts-ext' );
			$tags->set_attribute( 'target', '_blank' );
			$tags->set_attribute( 'rel', false === stripos( $rel, 'noopener' ) ? trim( $rel . ' noopener' ) : $rel );
			$found = true;
		}
		if ( ! $found ) {
			return $html;
		}
		$note = self::external_note();

		return (string) preg_replace_callback(
			'~(<a\b[^>]*\bclass="[^"]*\bts-ext\b[^"]*"[^>]*>)(.*?)</a>~is',
			static function ( $m ) use ( $note ) {
				return $m[1] . $m[2] . $note . '</a>';
			},
			$tags->get_updated_html()
		);
	}

	/* ------------------------------------------------------------------ */
	/* Rendering helpers                                                   */
	/* ------------------------------------------------------------------ */

	/**
	 * Editor-only placeholder; prints nothing on the public site.
	 */
	protected function render_empty( $message ) {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			echo '<div class="ts-w ts-empty">' . esc_html( $message ) . '</div>';
		}
	}

	/**
	 * Render one button defined by add_button_controls( $prefix ).
	 * $key_suffix keeps render attributes separate when the same prefix is used in several
	 * repeater rows.
	 */
	protected function render_button( $settings, $prefix, $key_suffix = '' ) {
		$text = isset( $settings[ $prefix . '_text' ] ) ? trim( (string) $settings[ $prefix . '_text' ] ) : '';
		if ( '' === $text ) {
			return;
		}

		$styles = $this->button_style_options();
		$style  = isset( $settings[ $prefix . '_style' ], $styles[ $settings[ $prefix . '_style' ] ] ) ? $settings[ $prefix . '_style' ] : 'primary';
		$key    = 'ts_btn_' . $prefix . $key_suffix;
		$link   = isset( $settings[ $prefix . '_link' ] ) && is_array( $settings[ $prefix . '_link' ] ) ? $settings[ $prefix . '_link' ] : array();

		if ( ! empty( $link['url'] ) ) {
			$this->add_link_attributes( $key, $link );
		} else {
			$this->add_render_attribute( $key, 'href', '#' );
		}
		$this->add_render_attribute( $key, 'class', array( 'ts-btn', 'ts-btn--' . $style ) );

		printf( '<a %s>%s</a>', $this->get_render_attribute_string( $key ), esc_html( $text ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/* ------------------------------------------------------------------ */
	/* Rating bars (e.g. an effort level)                                  */
	/* ------------------------------------------------------------------ */

	/**
	 * Default names for the levels 1–5.
	 */
	protected static function rating_default_names() {
		return array(
			1 => __( 'Easy', 'travelstore-custom-widgets' ),
			2 => __( 'Moderate', 'travelstore-custom-widgets' ),
			3 => __( 'Challenging', 'travelstore-custom-widgets' ),
			4 => __( 'Hard', 'travelstore-custom-widgets' ),
			5 => __( 'Extreme', 'travelstore-custom-widgets' ),
		);
	}

	/**
	 * Options for the per-card "Rating" dropdown.
	 */
	protected static function rating_options() {
		$options = array( '' => __( 'No rating bars', 'travelstore-custom-widgets' ) );
		for ( $n = 1; $n <= 5; $n++ ) {
			/* translators: %d: level number. */
			// Text values ("level-2"): number-like keys would be listed before "No rating bars" in the editor.
			$options[ 'level-' . $n ] = sprintf( _n( 'Level %1$d – %1$d bar filled', 'Level %1$d – %1$d bars filled', $n, 'travelstore-custom-widgets' ), $n );
		}

		return $options;
	}

	/**
	 * Widget-wide rating settings: what the bars mean, how many bars, and a name per level.
	 * Call inside a content section. Card rows add a "Rating" dropdown with rating_options().
	 */
	protected function add_rating_content_controls() {
		$this->add_control(
			'rating_label',
			array(
				'label'       => __( 'What the bars show', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Effort', 'travelstore-custom-widgets' ),
				'description' => __( 'For example: Effort. Shown above the bars in each card and read out by screen readers.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'rating_scale',
			array(
				'label'       => __( 'Number of bars', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '3',
				'options'     => array( '2' => '2', '3' => '3', '4' => '4', '5' => '5' ),
				'description' => __( 'A card set to a higher level than this shows all bars filled.', 'travelstore-custom-widgets' ),
			)
		);
		$names = self::rating_default_names();
		for ( $n = 1; $n <= 5; $n++ ) {
			$args = array(
				/* translators: %d: level number. */
				'label'   => sprintf( __( 'Name of level %d', 'travelstore-custom-widgets' ), $n ),
				'type'    => Controls_Manager::TEXT,
				'default' => $names[ $n ],
			);
			if ( $n > 2 ) {
				$args['condition'] = array( 'rating_scale' => array_map( 'strval', range( $n, 5 ) ) );
			}
			if ( 1 === $n ) {
				$args['description'] = __( 'Level 1 is the lowest (one bar filled).', 'travelstore-custom-widgets' );
			}
			$this->add_control( 'rating_name_' . $n, $args );
		}
	}

	protected function rating_scale( $s ) {
		$scale = isset( $s['rating_scale'] ) ? (int) $s['rating_scale'] : 3;

		return max( 2, min( 5, $scale ? $scale : 3 ) );
	}

	protected function rating_name( $s, $level ) {
		$names = self::rating_default_names();
		$name  = isset( $s[ 'rating_name_' . $level ] ) ? trim( (string) $s[ 'rating_name_' . $level ] ) : '';

		return '' !== $name ? $name : ( isset( $names[ $level ] ) ? $names[ $level ] : (string) $level );
	}

	/**
	 * A card's level (1…scale) from its dropdown value ("level-2"), or 0 for none.
	 * Older typed values such as "2/3" are still read (the first number).
	 */
	protected function rating_level( $value, $scale ) {
		if ( ! preg_match( '#(\d{1,2})#', (string) $value, $m ) ) {
			return 0;
		}

		return max( 0, min( $scale, (int) $m[1] ) );
	}

	/**
	 * Rating bars for a level, e.g. "Effort: Moderate, 2 of 3" for screen readers.
	 */
	protected function rating_html( $level, $scale, $label = '', $name = '' ) {
		if ( $level < 1 ) {
			return '';
		}
		/* translators: 1: filled bars, 2: total bars. */
		$aria = sprintf( __( '%1$d of %2$d', 'travelstore-custom-widgets' ), $level, $scale );
		if ( '' !== $name ) {
			$aria = $name . ', ' . $aria;
		}
		if ( '' !== $label ) {
			$aria = $label . ': ' . $aria;
		}
		$bars = '';
		for ( $i = 1; $i <= $scale; $i++ ) {
			$bars .= '<i' . ( $i <= $level ? ' class="is-on"' : '' ) . '></i>';
		}

		return '<span class="ts-rating" role="img" aria-label="' . esc_attr( $aria ) . '">' . $bars . '</span>';
	}

	/**
	 * A colour chosen in a repeater row, safe to print inside a style attribute:
	 * a Site Settings colour becomes its CSS variable; anything that is not a plain colour is dropped.
	 */
	protected function item_color( $item, $key ) {
		if ( ! empty( $item['__globals__'][ $key ] ) && preg_match( '/id=([A-Za-z0-9_-]+)/', (string) $item['__globals__'][ $key ], $m ) ) {
			return 'var(--e-global-color-' . $m[1] . ')';
		}
		$value = isset( $item[ $key ] ) ? trim( (string) $item[ $key ] ) : '';

		return preg_match( '/^(#[0-9a-fA-F]{3,8}|rgba?\([0-9\s.,%]+\)|hsla?\([0-9\s.,%deg]+\))$/', $value ) ? $value : '';
	}

	/**
	 * Eyebrow chip markup (tone + optional dot).
	 */
	protected function chip_html( $text, $tone, $show_dot = true, $extra_class = '' ) {
		$text = trim( (string) $text );
		if ( '' === $text ) {
			return '';
		}

		return sprintf(
			'<span class="ts-chip ts-tone--%1$s %2$s">%3$s%4$s</span>',
			esc_attr( $this->safe_tone( $tone ) ),
			esc_attr( $extra_class ),
			$show_dot ? '<span class="ts-chip__dot" aria-hidden="true"></span>' : '',
			esc_html( $text )
		);
	}
}
