<?php
/**
 * Widget: TS Oman Frames — "Oman, in a few frames." card grid.
 *
 * Travel Store-specific. Repeater of cards (label + art preset + optional photo)
 * rendered as the page's `.cfl-gstrip` responsive grid. Card visuals live in the
 * page's single support CSS block (`cflcss1`); a per-card photo overrides the
 * gradient art preset as the tile background.
 */

namespace TravelStore\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Oman_Frames extends \Elementor\Widget_Base {

	const ARTS = array( 't1', 't2', 't3', 't4', 't5', 't6' );

	public function get_name() {
		return 'ts-oman-frames';
	}

	public function get_title() {
		return __( 'Oman in a Few Frames', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( 'travel-store' );
	}

	public function get_keywords() {
		return array( 'oman', 'frames', 'gallery', 'cards', 'travel' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_content',
			array(
				'label' => __( 'Cards', 'travelstore-custom-widgets' ),
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title',
			array(
				'label'   => __( 'Label', 'travelstore-custom-widgets' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'art',
			array(
				'label'   => __( 'Card art', 'travelstore-custom-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 't1',
				'options' => array(
					't1' => __( 'Terracotta', 'travelstore-custom-widgets' ),
					't2' => __( 'Turquoise', 'travelstore-custom-widgets' ),
					't3' => __( 'Green', 'travelstore-custom-widgets' ),
					't4' => __( 'Stone / espresso', 'travelstore-custom-widgets' ),
					't5' => __( 'Cyan to blue', 'travelstore-custom-widgets' ),
					't6' => __( 'Gold / rust', 'travelstore-custom-widgets' ),
				),
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label'       => __( 'Background image', 'travelstore-custom-widgets' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => __( 'Optional photo shown as the card background. When empty, the Card art gradient below is used.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Cards', 'travelstore-custom-widgets' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'title' => 'Wahiba Sands', 'art' => 't1' ),
					array( 'title' => 'Musandam Fjords', 'art' => 't2' ),
					array( 'title' => 'Jabal Akhdar', 'art' => 't3' ),
					array( 'title' => 'Nizwa Fort', 'art' => 't4' ),
					array( 'title' => 'Wadi Shab', 'art' => 't5' ),
					array( 'title' => 'Khareef, Salalah', 'art' => 't6' ),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = isset( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();

		if ( empty( $items ) ) {
			return;
		}

		foreach ( $items as $item ) {
			$title   = isset( $item['title'] ) ? $item['title'] : '';
			$art     = isset( $item['art'] ) ? $item['art'] : 't1';
			$img_url = '';

			if ( ! in_array( $art, self::ARTS, true ) ) {
				$art = 't1';
			}

			$image = isset( $item['image'] ) ? $item['image'] : '';
			if ( is_array( $image ) ) {
				if ( ! empty( $image['id'] ) ) {
					$img_url = wp_get_attachment_image_url( $image['id'], 'full' );
				} elseif ( ! empty( $image['url'] ) ) {
					$img_url = $image['url'];
				}
			} elseif ( is_string( $image ) && '' !== $image ) {
				$img_url = $image;
			}

			$class = 'cfl-tile cfl-' . $art . ( $img_url ? ' cfl-photo' : '' );
			$style = $img_url ? ' style="' . esc_attr( 'background-image:url(' . $img_url . ');background-size:cover;background-position:center;' ) . '"' : '';

			printf(
				'<div class="%1$s"%2$s><span class="cfl-tlabel">%3$s</span></div>',
				esc_attr( $class ),
				$style,
				esc_html( $title )
			);
		}
	}

	protected function content_template() {
		?>
		<# if ( settings.items && settings.items.length ) { #>
			<# _.each( settings.items, function( item ) { #>
				<# var art = ( item.art && [ 't1','t2','t3','t4','t5','t6' ].includes( item.art ) ) ? item.art : 't1'; #>
				<# var img = ( item.image && item.image.url ) ? item.image.url : ''; #>
				<# var style = img ? ' style="background-image:url(' + img + ');background-size:cover;background-position:center;"' : ''; #>
				<div class="cfl-tile cfl-{{ art }}{{ img ? ' cfl-photo' : '' }}"{{{ style }}}><span class="cfl-tlabel">{{{ item.title }}}</span></div>
			<# } ); #>
		<# } #>
		<?php
	}
}
