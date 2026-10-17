<?php
/**
 * Widget: TS Art — a picture from the bundled art library, or any image.
 *
 * Pick a scene from the dropdown (SVG files shipped with the plugin) or choose an
 * image from the media library, which overrides the scene. Generic and reusable:
 * page heroes, side panels, dividers.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Art extends TS_Base_Widget {

	public function get_name() {
		return 'ts-art';
	}

	public function get_title() {
		return __( 'TS Art', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-image';
	}

	public function get_keywords() {
		return array( 'art', 'image', 'illustration', 'hero', 'scene', 'picture', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Picture', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'art',
			array(
				'label'       => __( 'Art scene', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => \TravelStore\TS_Art::options(),
				'description' => __( 'Illustrations that come with the site.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'inline',
			array(
				'label'        => __( 'Use the site fonts for text in the art', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'For art with words in it, such as a map with place names. The drawing is placed in the page instead of as a picture file.', 'travelstore-custom-widgets' ),
				'condition'    => array( 'art!' => '' ),
			)
		);
		// 1.9.4: a fixed height, for drawings that stretch to fill (e.g. the Salalah trail line, 90px in the draft).
		$this->add_responsive_control(
			'height',
			array(
				// Owner 2026-10-14: new Art widgets start at 450px desktop / 350px tablet / 250px mobile.
				'default'        => array( 'size' => 450, 'unit' => 'px' ),
				'tablet_default' => array( 'size' => 350, 'unit' => 'px' ),
				'mobile_default' => array( 'size' => 250, 'unit' => 'px' ),
				'label'       => __( 'Height (empty = the picture\'s own height)', 'travelstore-custom-widgets' ),
				// 1.9.6: the picture is cropped to the height, never squashed; Style > Object Position picks the part in view.
				'description' => __( 'The picture keeps its proportions and is cropped to this height. Choose which part stays in view under Style > Object Position.', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 800 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-art__img, {{WRAPPER}} .ts-art__svg svg' => 'height: {{SIZE}}{{UNIT}}; aspect-ratio: auto;' ),
			)
		);
		$this->add_control(
			'image',
			array(
				'label'       => __( 'Or choose your own image', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'A photo (JPG, PNG or WebP). It replaces the art scene above.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		// Before 1.6.3 a chosen image was always shown at full size, so that stays the default.
		$this->add_image_options( $this, 'image', array( 'size' => 'full' ) );
		$this->add_control(
			'alt',
			array(
				'label'       => __( 'Description for screen readers', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Leave empty if the picture is purely decorative.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'ts_section_style',
			array(
				'label' => __( 'Picture style', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'shape',
			array(
				'label'     => __( 'Shape', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'natural',
				'options'   => array(
					'natural' => __( 'Natural (keep the picture\'s proportions)', 'travelstore-custom-widgets' ),
					'wide'    => __( 'Wide (21:9, cropped)', 'travelstore-custom-widgets' ),
					'landscape' => __( 'Landscape (3:2, cropped)', 'travelstore-custom-widgets' ),
					'square'  => __( 'Square (cropped)', 'travelstore-custom-widgets' ),
				),
				'selectors_dictionary' => array(
					// 1.9.4: a bundled drawing's own proportions (its viewBox), so a drawing that stretches
					// (preserveAspectRatio="none", e.g. the Salalah trail line) is not drawn 150px high.
					'natural'   => 'aspect-ratio:var(--ts-art-ratio, auto);',
					'wide'      => 'aspect-ratio:21/9;object-fit:cover;',
					'landscape' => 'aspect-ratio:3/2;object-fit:cover;',
					'square'    => 'aspect-ratio:1/1;object-fit:cover;',
				),
				'selectors' => array( '{{WRAPPER}} .ts-art__img' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'width',
			array(
				'label'      => __( 'Width', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array( 'min' => 10, 'max' => 100 ),
					'px' => array( 'min' => 80, 'max' => 1400 ),
				),
				'selectors'  => array( '{{WRAPPER}} .ts-art' => 'width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Position', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array( 'title' => __( 'Left', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-left' ),
					'center' => array( 'title' => __( 'Center', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-center' ),
					'right'  => array( 'title' => __( 'Right', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-right' ),
				),
				'selectors_dictionary' => array(
					'left'   => 'margin-left:0;margin-right:auto;',
					'center' => 'margin-left:auto;margin-right:auto;',
					'right'  => 'margin-left:auto;margin-right:0;',
				),
				'selectors' => array( '{{WRAPPER}} .ts-art' => '{{VALUE}}' ),
			)
		);
		$this->add_responsive_control(
			'radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-art__img' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'border_color',
			array(
				'label'       => __( 'Thin border colour', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'Leave empty for no border.', 'travelstore-custom-widgets' ),
				'selectors'   => array( '{{WRAPPER}} .ts-art__img' => 'border: 1px solid {{VALUE}};' ),
			)
		);
		// Elementor's Image options; the border stays "Thin border colour" above.
		$this->add_image_style_controls( '.ts-art__img', '.ts-art__img', '.ts-art__img:hover', array( 'border' => false ) );
		$this->end_controls_section();

		$this->add_image_caption_style_section();

		$this->register_extra_css_controls();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$src = '';
		$w   = 0;
		$h   = 0;
		$alt = isset( $s['alt'] ) ? trim( (string) $s['alt'] ) : '';

		$photo = false;
		if ( ! empty( $s['image']['id'] ) ) {
			$photo = true;
			$size  = ! empty( $s['image_size'] ) ? (string) $s['image_size'] : 'full';
			if ( 'custom' === $size ) {
				$src = $this->image_url( $s, 'image', 'full' );
			} else {
				$img = wp_get_attachment_image_src( (int) $s['image']['id'], $size );
				if ( $img ) {
					$src = (string) $img[0];
					$w   = (int) $img[1];
					$h   = (int) $img[2];
				}
			}
			if ( '' === $alt ) {
				$alt = (string) get_post_meta( (int) $s['image']['id'], '_wp_attachment_image_alt', true );
			}
		} elseif ( ! empty( $s['image']['url'] ) && false === strpos( (string) $s['image']['url'], 'placeholder' ) ) {
			$src   = (string) $s['image']['url'];
			$photo = true;
		}

		if ( '' === $src && ! empty( $s['art'] ) && ! empty( $s['inline'] ) ) {
			$svg = \TravelStore\TS_Art::inline_svg( $s['art'], $this->get_id(), $alt );
			if ( '' !== $svg ) {
				echo '<figure class="ts-w ts-art"><span class="ts-art__img ts-art__svg' . esc_attr( $this->image_anim_class() ) . '">' . $svg . '</span></figure>'; // phpcs:ignore WordPress.Security.EscapeOutput -- bundled plugin file, cleaned in inline_svg().
				return;
			}
		}

		$stretch = false;
		if ( '' === $src && ! empty( $s['art'] ) ) {
			$stretch = \TravelStore\TS_Art::stretches( $s['art'] );
			$src  = \TravelStore\TS_Art::url( $s['art'] );
			$size = \TravelStore\TS_Art::size( $s['art'] );
			if ( $size ) {
				list( $w, $h ) = $size;
			}
		}

		if ( '' === $src ) {
			$this->render_empty( __( 'Choose an art scene or an image to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$img = sprintf(
			'<img class="ts-art__img%4$s" src="%1$s" alt="%2$s"%3$s loading="lazy" decoding="async" />',
			esc_url( $src ),
			esc_attr( $alt ),
			( $w && $h ) ? ' width="' . (int) $w . '" height="' . (int) $h . '"' . ( $photo ? '' : ' style="--ts-art-ratio:' . (int) $w . ' / ' . (int) $h . '"' ) : '',
			esc_attr( ( $stretch ? ' ts-art__img--stretch' : '' ) . $this->image_anim_class() )
		);
		if ( $photo ) {
			$img  = $this->image_linked( $img, $s, 'image', 'ts_art_link' );
			$img .= $this->image_caption( $s, 'image', 'figcaption' );
		}
		echo '<figure class="ts-w ts-art">' . $img . '</figure>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
