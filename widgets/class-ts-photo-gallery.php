<?php
/**
 * Widget: TS Photo Gallery — a browsable wall of pictures with category buttons and an enlarged view (1.13.0).
 *
 * For guest photos and similar. Each picture gets a category name typed as plain words ("Desert");
 * the buttons above the wall are made from those names, in the order they first appear. Clicking a
 * picture opens it enlarged over the page, with previous / next, the keyboard arrows and Esc.
 * Pictures use the same native image options as every other TS widget.
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Photo_Gallery extends TS_Base_Widget {

	public function get_name() {
		return 'ts-photo-gallery';
	}

	public function get_title() {
		return __( 'TS Photo Gallery', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_keywords() {
		return array( 'gallery', 'photos', 'pictures', 'images', 'lightbox', 'guests', 'masonry', 'travel' );
	}

	public function get_script_depends() {
		return array( 'ts-photo-gallery' );
	}

	protected function register_controls() {
		/* ---------------- Content: photos ---------------- */
		$this->start_controls_section(
			'ts_photos',
			array( 'label' => __( 'Photos', 'travelstore-custom-widgets' ) )
		);

		$r = new Repeater();
		$r->add_control(
			'image',
			array(
				'label'   => __( 'Picture', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => \Elementor\Utils::get_placeholder_image_src() ),
			)
		);
		$this->add_image_options( $r, 'image', array( 'size' => 'large', 'link' => false ) );
		$r->add_control(
			'group',
			array(
				'label'       => __( 'Category', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'For example: Desert', 'travelstore-custom-widgets' ),
				'description' => __( 'Type the same words for pictures that belong together. The buttons above the gallery are made from these.', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'photos',
			array(
				'label'       => __( 'Pictures', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ group || "Picture" }}}',
				'default'     => array(),
			)
		);
		$this->end_controls_section();

		/* ---------------- Content: buttons and enlarged view ---------------- */
		$this->start_controls_section(
			'ts_options',
			array( 'label' => __( 'Buttons and enlarged view', 'travelstore-custom-widgets' ) )
		);
		$this->add_control(
			'show_filter',
			array(
				'label'        => __( 'Show category buttons', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'all_label',
			array(
				'label'     => __( 'First button', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'All photos', 'travelstore-custom-widgets' ),
				'description' => __( 'The button that shows every picture.', 'travelstore-custom-widgets' ),
				'condition' => array( 'show_filter' => 'yes' ),
			)
		);
		$this->add_control(
			'lightbox',
			array(
				'label'        => __( 'Open pictures enlarged', 'travelstore-custom-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Clicking a picture shows it large over the page, with previous and next buttons.', 'travelstore-custom-widgets' ),
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
		$this->add_control(
			'tile_shape',
			array(
				'label'   => __( 'Picture shape', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'natural',
				'options' => array(
					'natural'   => __( 'Natural (each picture keeps its own shape)', 'travelstore-custom-widgets' ),
					'landscape' => __( 'Equal tiles, wide (cropped)', 'travelstore-custom-widgets' ),
					'square'    => __( 'Equal tiles, square (cropped)', 'travelstore-custom-widgets' ),
					'portrait'  => __( 'Equal tiles, tall (cropped)', 'travelstore-custom-widgets' ),
				),
			)
		);
		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'travelstore-custom-widgets' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '2',
				'options'        => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5' ),
				'selectors'      => array( '{{WRAPPER}} .ts-pgal__grid' => '--ts-pgal-cols: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Space between pictures', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-pgal__grid' => '--ts-pgal-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'filters_gap',
			array(
				'label'      => __( 'Space under the buttons', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-pgal__filters' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'show_filter' => 'yes' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Style: pictures ---------------- */
		$this->start_controls_section(
			'ts_section_image_style',
			array(
				'label' => __( 'Pictures', 'travelstore-custom-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'tile_radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-pgal__frame' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_image_style_controls( '.ts-pgal__img', '.ts-pgal__frame', '.ts-pgal__item:hover .ts-pgal__img', array( 'fit' => false ) );
		$this->end_controls_section();

		$this->add_image_caption_style_section();

		/* ---------------- Style: buttons ---------------- */
		$this->start_controls_section(
			'ts_section_filter_style',
			array(
				'label'     => __( 'Category buttons', 'travelstore-custom-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_filter' => 'yes' ),
			)
		);
		$this->add_responsive_control(
			'filters_align',
			array(
				'label'     => __( 'Alignment', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array( 'title' => __( 'Start', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-left' ),
					'center'     => array( 'title' => __( 'Center', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-center' ),
					'flex-end'   => array( 'title' => __( 'End', 'travelstore-custom-widgets' ), 'icon' => 'eicon-text-align-right' ),
				),
				'selectors' => array( '{{WRAPPER}} .ts-pgal__filters' => 'justify-content: {{VALUE}};' ),
			)
		);
		$this->add_typography_control( 'filter_typography', __( 'Typography', 'travelstore-custom-widgets' ), '.ts-pgal__filter' );
		$this->add_color_control( 'filter_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-w .ts-pgal__filter' );
		$this->add_color_control( 'filter_border', __( 'Border colour', 'travelstore-custom-widgets' ), '.ts-w .ts-pgal__filter', 'border-color' );
		$this->add_color_control( 'filter_on_bg', __( 'Chosen button colour', 'travelstore-custom-widgets' ), '.ts-w .ts-pgal__filter[aria-pressed="true"]', 'background-color' );
		$this->add_color_control( 'filter_on_text', __( 'Chosen button text', 'travelstore-custom-widgets' ), '.ts-w .ts-pgal__filter[aria-pressed="true"]' );
		$this->end_controls_section();

		$this->register_extra_css_controls();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$photos = isset( $s['photos'] ) && is_array( $s['photos'] ) ? $s['photos'] : array();
		$photos = array_values(
			array_filter(
				$photos,
				static function ( $p ) {
					return ! empty( $p['image']['id'] ) || ( ! empty( $p['image']['url'] ) && false === strpos( (string) $p['image']['url'], 'placeholder' ) );
				}
			)
		);

		if ( ! $photos ) {
			$this->render_empty( __( 'Add pictures to preview this gallery.', 'travelstore-custom-widgets' ) );
			return;
		}

		$shape    = isset( $s['tile_shape'] ) && in_array( $s['tile_shape'], array( 'natural', 'landscape', 'square', 'portrait' ), true ) ? $s['tile_shape'] : 'natural';
		$zoom     = ! isset( $s['lightbox'] ) || 'yes' === $s['lightbox'];
		$filters  = ! isset( $s['show_filter'] ) || 'yes' === $s['show_filter'];
		$groups   = array();
		foreach ( $photos as $p ) {
			$name = isset( $p['group'] ) ? trim( (string) $p['group'] ) : '';
			if ( '' !== $name && ! isset( $groups[ sanitize_title( $name ) ] ) ) {
				$groups[ sanitize_title( $name ) ] = $name;
			}
		}
		$count = count( $photos );
		?>
		<div class="ts-w ts-pgal ts-pgal--<?php echo esc_attr( $shape ); ?>" data-zoom="<?php echo $zoom ? '1' : '0'; ?>">
			<?php if ( $filters && count( $groups ) > 1 ) : ?>
				<div class="ts-pgal__filters" role="group" aria-label="<?php esc_attr_e( 'Filter photos', 'travelstore-custom-widgets' ); ?>">
					<button type="button" class="ts-pgal__filter" data-filter="all" aria-pressed="true"><?php echo esc_html( '' !== trim( (string) ( isset( $s['all_label'] ) ? $s['all_label'] : '' ) ) ? $s['all_label'] : __( 'All photos', 'travelstore-custom-widgets' ) ); ?></button>
					<?php foreach ( $groups as $slug => $name ) : ?>
						<button type="button" class="ts-pgal__filter" data-filter="<?php echo esc_attr( $slug ); ?>" aria-pressed="false"><?php echo esc_html( $name ); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="ts-pgal__grid">
				<?php
				foreach ( $photos as $i => $p ) {
					$name    = isset( $p['group'] ) ? trim( (string) $p['group'] ) : '';
					$id      = ! empty( $p['image']['id'] ) ? (int) $p['image']['id'] : 0;
					$full    = $id ? (string) wp_get_attachment_image_url( $id, 'full' ) : (string) $p['image']['url'];
					$alt     = $id ? (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) : '';
					$img     = $this->image_tag( $p, 'image', 'large', null, array( 'class' => 'ts-pgal__img', 'loading' => 'lazy', 'sizes' => '(max-width: 767px) 50vw, 33vw' ) );
					$caption = $this->image_caption( $p, 'image', 'figcaption' );
					$text    = '' !== $caption ? trim( wp_strip_all_tags( $caption ) ) : '';
					?>
					<figure class="ts-pgal__item" data-group="<?php echo esc_attr( '' !== $name ? sanitize_title( $name ) : '' ); ?>">
						<?php if ( $zoom ) : ?>
							<button type="button" class="ts-pgal__frame ts-pgal__open" data-full="<?php echo esc_url( $full ); ?>" data-alt="<?php echo esc_attr( $alt ); ?>" data-caption="<?php echo esc_attr( $text ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: picture description */ __( 'Enlarge photo: %s', 'travelstore-custom-widgets' ), '' !== $alt ? $alt : sprintf( __( 'photo %d', 'travelstore-custom-widgets' ), $i + 1 ) ) ); ?>">
								<?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput -- built by wp_get_attachment_image / escaped helper. ?>
							</button>
						<?php else : ?>
							<div class="ts-pgal__frame"><?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<?php endif; ?>
						<?php echo $caption; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in image_caption(). ?>
					</figure>
					<?php
				}
				?>
			</div>
			<p class="ts-pgal__count screen-reader-text" aria-live="polite" data-total="<?php echo (int) $count; ?>"></p>
		</div>
		<?php
	}
}
