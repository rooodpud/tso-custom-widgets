<?php
/**
 * Widget: TS Testimonials — an optional rating summary and a grid of review cards.
 *
 * Reviews are typed in the widget for now. A future reviews feed (for example Google reviews)
 * can supply them through the filters `ts_testimonials_items` and `ts_testimonials_summary`
 * without changing pages: the widget keeps rendering whatever list it is given.
 * No review schema is printed (search engines ignore self-published reviews of a business).
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Testimonials extends TS_Base_Widget {

	public function get_name() {
		return 'ts-testimonials';
	}

	public function get_title() {
		return __( 'TS Testimonials', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	public function get_keywords() {
		return array( 'testimonials', 'reviews', 'quotes', 'rating', 'stars', 'google', 'travel' );
	}

	protected function register_controls() {
		/* ---------------- Content: summary ---------------- */
		$this->start_controls_section(
			'ts_summary',
			array( 'label' => __( 'Rating summary', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'show_summary',
			array(
				'label'        => __( 'Show the rating summary', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'rating',
			array(
				'label'       => __( 'Average rating (0–5)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 5,
				'step'        => 0.1,
				'default'     => 4.8,
				'description' => __( 'The stars fill to this value, for example 4.8.', 'travelstore-custom-widgets' ),
				'condition'   => array( 'show_summary' => 'yes' ),
			)
		);
		$this->add_control(
			'summary_title',
			array(
				'label'     => __( 'Main line', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( '4.8 out of 5', 'travelstore-custom-widgets' ),
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'show_summary' => 'yes' ),
			)
		);
		$this->add_control(
			'summary_text',
			array(
				'label'     => __( 'Small line', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Based on 214 Google reviews', 'travelstore-custom-widgets' ),
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'show_summary' => 'yes' ),
			)
		);
		$this->add_control(
			'summary_link',
			array(
				'label'       => __( 'Link (optional)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'Type a page name or paste a link', 'travelstore-custom-widgets' ),
				'description' => __( 'For example your Google reviews page. The whole summary becomes the link.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
				'condition'   => array( 'show_summary' => 'yes' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Content: reviews ---------------- */
		$this->start_controls_section(
			'ts_content',
			array( 'label' => __( 'Reviews', 'travelstore-custom-widgets' ) )
		);
		$r = new Repeater();
		$r->add_control(
			'quote',
			array(
				'label'   => __( 'Review', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 5,
				'dynamic' => array( 'active' => true ),
			)
		);
		$r->add_control(
			'name',
			array(
				'label'   => __( 'Name', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);
		$r->add_control(
			'details',
			array(
				'label'       => __( 'Details', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Shown after the name, for example: Self-drive, Germany.', 'travelstore-custom-widgets' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$r->add_control(
			'stars',
			array(
				'label'   => __( 'Stars (1–5)', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 5,
				'step'    => 1,
				'default' => 5,
			)
		);
		$r->add_control(
			'photo',
			array(
				'label'       => __( 'Photo (optional)', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'A small round picture next to the name.', 'travelstore-custom-widgets' ),
			)
		);
		// The name line is already the photo's caption, so no separate caption here.
		$this->add_image_options( $r, 'photo', array( 'size' => 'thumbnail', 'caption' => false ) );
		$this->add_control(
			'items',
			array(
				'label'       => __( 'Reviews', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array(
						'quote'   => __( 'One or two sentences from a traveller about their trip.', 'travelstore-custom-widgets' ),
						'name'    => __( 'Traveller name', 'travelstore-custom-widgets' ),
						'details' => __( 'Route, country', 'travelstore-custom-widgets' ),
					),
					array(
						'quote'   => __( 'One or two sentences from a traveller about their trip.', 'travelstore-custom-widgets' ),
						'name'    => __( 'Traveller name', 'travelstore-custom-widgets' ),
						'details' => __( 'Route, country', 'travelstore-custom-widgets' ),
					),
					array(
						'quote'   => __( 'One or two sentences from a traveller about their trip.', 'travelstore-custom-widgets' ),
						'name'    => __( 'Traveller name', 'travelstore-custom-widgets' ),
						'details' => __( 'Route, country', 'travelstore-custom-widgets' ),
					),
				),
			)
		);
		$this->add_control(
			'show_stars',
			array(
				'label'        => __( 'Show stars on each review', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: layout ---------------- */
		$this->start_controls_section(
			'ts_section_layout_style',
			array(
				'label' => __( 'Layout', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'text_scheme',
			array(
				'label'   => __( 'Text colours', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dark',
				'options' => array(
					'dark'  => __( 'Dark text (for light backgrounds)', 'travelstore-custom-widgets' ),
					'light' => __( 'Light text (for dark backgrounds)', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Reviews per row', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '1',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors'      => array( '{{WRAPPER}} .ts-testi' => '--ts-testi-cols: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Space between reviews', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-testi' => '--ts-testi-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'summary_spacing',
			array(
				'label'      => __( 'Space below the summary', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 40, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-testi' => '--ts-testi-summary-space: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'star_color', __( 'Star colour', 'travelstore-custom-widgets' ), '.ts-testi', '--ts-testi-star-on', 'ts_gold' );
		$this->add_color_control( 'star_empty_color', __( 'Empty star colour', 'travelstore-custom-widgets' ), '.ts-testi', '--ts-testi-star-off' );
		$this->end_controls_section();

		/* ---------------- Style: summary ---------------- */
		$this->start_controls_section(
			'ts_section_summary_style',
			array(
				'label'     => __( 'Rating summary', 'travelstore-custom-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_summary' => 'yes' ),
			)
		);
		$this->add_control(
			'summary_star_size',
			array(
				'label'      => __( 'Star size', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 40 ) ),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-testi__summary .ts-stars' => 'font-size: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_typography_control( 'summary_title_typography', __( 'Main line typography', 'travelstore-custom-widgets' ), '.ts-testi__score', 'ts_heading' );
		$this->add_color_control( 'summary_title_color', __( 'Main line colour', 'travelstore-custom-widgets' ), '.ts-testi__score' );
		$this->add_typography_control( 'summary_text_typography', __( 'Small line typography', 'travelstore-custom-widgets' ), '.ts-testi__count' );
		$this->add_color_control( 'summary_text_color', __( 'Small line colour', 'travelstore-custom-widgets' ), '.ts-testi__count' );
		$this->add_color_control( 'summary_bg', __( 'Box colour', 'travelstore-custom-widgets' ), '.ts-testi__summary', 'background-color' );
		$this->add_color_control( 'summary_border', __( 'Box border colour', 'travelstore-custom-widgets' ), '.ts-testi__summary', 'border-color' );
		$this->add_control(
			'summary_radius',
			array(
				'label'      => __( 'Box corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 16, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-testi__summary' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'summary_padding',
			array(
				'label'      => __( 'Box padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-testi__summary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: cards ---------------- */
		$this->start_controls_section(
			'ts_section_card_style',
			array(
				'label' => __( 'Review cards', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_color_control( 'card_bg', __( 'Card colour', 'travelstore-custom-widgets' ), '.ts-testi__card', 'background-color' );
		$this->add_color_control( 'card_border', __( 'Card border colour', 'travelstore-custom-widgets' ), '.ts-testi__card', 'border-color' );
		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Card corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-testi__card' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Card padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-testi__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'card_star_size',
			array(
				'label'      => __( 'Star size', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 32 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ts-testi__card .ts-stars' => 'font-size: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_typography_control( 'quote_typography', __( 'Review typography', 'travelstore-custom-widgets' ), '.ts-testi__quote', 'ts_body' );
		$this->add_color_control( 'quote_color', __( 'Review colour', 'travelstore-custom-widgets' ), '.ts-testi__quote' );
		$this->add_typography_control( 'attrib_typography', __( 'Name typography', 'travelstore-custom-widgets' ), '.ts-testi__attrib' );
		$this->add_color_control( 'attrib_color', __( 'Name colour', 'travelstore-custom-widgets' ), '.ts-testi__attrib' );
		$this->add_control(
			'colors_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Leave the colours empty to follow "Text colours" (Layout). The defaults come from Site Settings.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: reviewer photos (Elementor's Image options) ---------------- */
		$this->start_controls_section(
			'ts_section_photo_style',
			array(
				'label' => __( 'Reviewer photos', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'photo_width',
			array(
				'label'      => __( 'Size', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 160 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-testi .ts-testi__photo' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ),
			)
		);
		// Selectors one step stronger than ".ts-w .ts-testi__photo" in ts-testimonials.css.
		$this->add_image_style_controls( '.ts-testi .ts-testi__photo', '.ts-testi .ts-testi__photo', '.ts-testi .ts-testi__photo:hover', array( 'radius' => true ) );
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = isset( $s['items'] ) && is_array( $s['items'] ) ? $s['items'] : array();

		/**
		 * Lets a reviews feed supply the reviews. Each item: quote, name, details, stars (1–5),
		 * optional photo ( array( 'id' => attachment ID ) or array( 'url' => … ) ).
		 */
		$items = apply_filters( 'ts_testimonials_items', $items, $s, $this );

		$summary = array(
			'show'   => ! empty( $s['show_summary'] ),
			'rating' => isset( $s['rating'] ) ? (float) $s['rating'] : 0,
			'title'  => isset( $s['summary_title'] ) ? (string) $s['summary_title'] : '',
			'text'   => isset( $s['summary_text'] ) ? (string) $s['summary_text'] : '',
			'link'   => isset( $s['summary_link'] ) && is_array( $s['summary_link'] ) ? $s['summary_link'] : array(),
		);
		/** Lets a reviews feed supply the average and the count line. */
		$summary = apply_filters( 'ts_testimonials_summary', $summary, $s, $this );

		if ( empty( $items ) && empty( $summary['show'] ) ) {
			$this->render_empty( __( 'Add at least one review to preview this widget.', 'travelstore-custom-widgets' ) );
			return;
		}

		$classes = array( 'ts-w', 'ts-testi' );
		if ( isset( $s['text_scheme'] ) && 'light' === $s['text_scheme'] ) {
			$classes[] = 'ts-testi--light';
			$classes[] = 'ts-on-dark';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php
			if ( ! empty( $summary['show'] ) ) {
				$this->render_summary( $summary );
			}
			if ( $items ) {
				echo '<div class="ts-testi__grid">';
				foreach ( $items as $i => $item ) {
					$this->render_card( $item, $i, ! empty( $s['show_stars'] ) );
				}
				echo '</div>';
			}
			?>
		</div>
		<?php
	}

	private function render_summary( $summary ) {
		$has_link = ! empty( $summary['link']['url'] );
		if ( $has_link ) {
			$this->add_link_attributes( 'ts_summary', $summary['link'] );
		}
		$this->add_render_attribute( 'ts_summary', 'class', 'ts-testi__summary' );
		$tag = $has_link ? 'a' : 'div';

		printf( '<%1$s %2$s>', esc_attr( $tag ), $this->get_render_attribute_string( 'ts_summary' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo $this->stars_html( $summary['rating'] ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<span class="ts-testi__summary-text">';
		if ( '' !== trim( $summary['title'] ) ) {
			echo '<span class="ts-testi__score">' . esc_html( $summary['title'] ) . '</span>';
		}
		if ( '' !== trim( $summary['text'] ) ) {
			echo '<span class="ts-testi__count">' . esc_html( $summary['text'] ) . '</span>';
		}
		echo '</span>';
		printf( '</%s>', esc_attr( $tag ) );
	}

	private function render_card( $item, $i, $show_stars ) {
		$quote   = isset( $item['quote'] ) ? trim( (string) $item['quote'] ) : '';
		$name    = isset( $item['name'] ) ? trim( (string) $item['name'] ) : '';
		$details = isset( $item['details'] ) ? trim( (string) $item['details'] ) : '';
		$rid     = ! empty( $item['_id'] ) ? sanitize_html_class( $item['_id'] ) : 'i' . $i;
		$attrib  = implode( ' · ', array_filter( array( $name, $details ), 'strlen' ) );

		echo '<figure class="ts-testi__card elementor-repeater-item-' . esc_attr( $rid ) . '">';
		if ( $show_stars ) {
			$stars = isset( $item['stars'] ) && '' !== $item['stars'] ? (int) $item['stars'] : 5;
			echo $this->stars_html( max( 1, min( 5, $stars ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( '' !== $quote ) {
			echo '<blockquote class="ts-testi__quote"><p>' . nl2br( esc_html( $quote ) ) . '</p></blockquote>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( '' !== $attrib ) {
			echo '<figcaption class="ts-testi__attrib">';
			$this->render_photo( $item, $rid );
			echo '<span>' . esc_html( $attrib ) . '</span></figcaption>';
		}
		echo '</figure>';
	}

	private function render_photo( $item, $rid ) {
		$photo = isset( $item['photo'] ) && is_array( $item['photo'] ) ? $item['photo'] : array();
		$img   = '';
		if ( ! empty( $photo['id'] ) ) {
			$img = $this->image_tag( $item, 'photo', 'thumbnail', '', array( 'class' => 'ts-testi__photo', 'loading' => 'lazy' ) );
		} elseif ( ! empty( $photo['url'] ) && false === strpos( (string) $photo['url'], 'placeholder' ) ) {
			$img = sprintf( '<img class="ts-testi__photo%2$s" src="%1$s" alt="" width="40" height="40" loading="lazy" decoding="async" />', esc_url( $photo['url'] ), esc_attr( $this->image_anim_class() ) );
		}
		echo $this->image_linked( $img, $item, 'photo', 'ts_testi_photo_' . $rid ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Five stars filled to $rating (0–5); read out as "Rated 4.8 out of 5".
	 */
	private function stars_html( $rating ) {
		$rating = max( 0, min( 5, (float) $rating ) );
		$label  = sprintf( /* translators: %s: rating */ __( 'Rated %s out of 5', 'travelstore-custom-widgets' ), rtrim( rtrim( number_format( $rating, 1, '.', '' ), '0' ), '.' ) );

		return sprintf(
			'<span class="ts-stars" role="img" aria-label="%1$s" style="--ts-rate:%2$s%%"><span class="ts-stars__row" aria-hidden="true">★★★★★</span><span class="ts-stars__fill" aria-hidden="true">★★★★★</span></span>',
			esc_attr( $label ),
			esc_attr( round( $rating / 5 * 100, 2 ) )
		);
	}
}
