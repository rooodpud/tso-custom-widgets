<?php
/**
 * Plugin Name: Travel Store Custom Widgets
 * Description: Travel Store (TS) Elementor widgets for the Travel Store Oman site. Local development only.
 * Version:     1.13.1
 * Author:      Pradeep Hingorani
 * GitHub Plugin URI: https://github.com/rooodpud/tso-custom-widgets
 * Primary Branch: main
 * Text Domain: travelstore-custom-widgets
 * Requires Plugins: elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TSW_PATH', plugin_dir_path( __FILE__ ) );
define( 'TSW_URL', plugin_dir_url( __FILE__ ) );
define( 'TSW_VERSION', '1.13.1' );

// Reusable-widget support classes (no Elementor dependency at load time). DEC-015.
require_once TSW_PATH . 'includes/class-ts-art.php';
require_once TSW_PATH . 'includes/class-ts-routes.php';
require_once TSW_PATH . 'includes/class-ts-month-levels.php'; // 1.10.0: great/good/tough month ratings (Atlas + Season Matrix).
require_once TSW_PATH . 'includes/class-ts-extra-css.php';
require_once TSW_PATH . 'includes/class-ts-enquiry-fields.php'; // 1.7.0: TS Enquiry Form fields, shared with TS Enquiries.
\TravelStore\TS_Extra_CSS::init();
require_once TSW_PATH . 'includes/class-ts-link-search.php'; // 1.9.7: pages (Published and Draft) by name or slug in link boxes.
\TravelStore\TS_Link_Search::init();
require_once TSW_PATH . 'includes/class-ts-page-links.php'; // 1.12.0: page links stored by page ID, shown with the current address.
\TravelStore\TS_Page_Links::init();

/**
 * Register the reusable widgets' stylesheets. Elementor loads only the ones a page uses.
 */
add_action(
	'elementor/frontend/after_register_styles',
	static function () {
		/*
		 * The widgets load
		 * the two Travel Store typefaces (with optical sizes; same URL as the Home page) whenever one is on
		 * the page. Return false from this filter to self-host the fonts instead.
		 */
		$base_deps = array();
		if ( apply_filters( 'ts_widgets_load_google_fonts', true ) ) {
			wp_register_style(
				'ts-fonts',
				'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,700;12..96,800&family=Karla:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap',
				array(),
				null
			);
			$base_deps = array( 'ts-fonts' );
		}

		$sheets = array( 'ts-base', 'ts-section-header', 'ts-art', 'ts-cards', 'ts-media-cards', 'ts-faq-list', 'ts-panel', 'ts-stats', 'ts-steps', 'ts-info-list', 'ts-split-feature', 'ts-tabs', 'ts-testimonials', 'ts-route-story', 'ts-season-calendar', 'ts-trip-cards', 'ts-enquiry-form', 'ts-notice', 'ts-page-menu', 'ts-comparison', 'ts-atlas', 'ts-season-matrix', 'ts-time-bars', 'ts-trip-picker', 'ts-photo-gallery' );
		foreach ( $sheets as $handle ) {
			$file = 'assets/css/' . $handle . '.css';
			if ( is_readable( TSW_PATH . $file ) ) {
				// 1.9.4: the file date as version, so a changed stylesheet is never served from a browser's cache.
				wp_register_style( $handle, TSW_URL . $file, 'ts-base' === $handle ? $base_deps : array( 'ts-base' ), (string) filemtime( TSW_PATH . $file ) );
			}
		}
	}
);

/**
 * Register the widgets' scripts (TS Tabs, TS Route Story, TS Media Cards filter). Elementor loads it only where the widget is used.
 */
add_action(
	'elementor/frontend/after_register_scripts',
	static function () {
		wp_register_script( 'ts-tabs', TSW_URL . 'assets/js/ts-tabs.js', array(), (string) filemtime( TSW_PATH . 'assets/js/ts-tabs.js' ), true );
		wp_register_script( 'ts-route-story', TSW_URL . 'assets/js/ts-route-story.js', array(), (string) filemtime( TSW_PATH . 'assets/js/ts-route-story.js' ), true );
		wp_register_script( 'ts-media-cards', TSW_URL . 'assets/js/ts-media-cards.js', array(), (string) filemtime( TSW_PATH . 'assets/js/ts-media-cards.js' ), true );
		wp_register_script( 'ts-enquiry-form', TSW_URL . 'assets/js/ts-enquiry-form.js', array(), (string) filemtime( TSW_PATH . 'assets/js/ts-enquiry-form.js' ), true );
		// 1.8.0: live clock (TS Cards), tick-off checklist (TS Info List), "On this page" menu.
		wp_register_script( 'ts-clock', TSW_URL . 'assets/js/ts-clock.js', array(), (string) filemtime( TSW_PATH . 'assets/js/ts-clock.js' ), true );
		wp_register_script( 'ts-checklist', TSW_URL . 'assets/js/ts-checklist.js', array(), (string) filemtime( TSW_PATH . 'assets/js/ts-checklist.js' ), true );
		wp_register_script( 'ts-page-menu', TSW_URL . 'assets/js/ts-page-menu.js', array(), (string) filemtime( TSW_PATH . 'assets/js/ts-page-menu.js' ), true );
		// 1.10.0: Destination Atlas, Season Matrix, Travel Time Bars.
		// 1.11.0: Trip Style Picker (Experiences page).
		foreach ( array( 'ts-atlas', 'ts-season-matrix', 'ts-time-bars', 'ts-trip-picker', 'ts-photo-gallery' ) as $handle ) {
			wp_register_script( $handle, TSW_URL . 'assets/js/' . $handle . '.js', array(), (string) filemtime( TSW_PATH . 'assets/js/' . $handle . '.js' ), true );
		}
	}
);

/**
 * 1.9.2: one copy of each Travel Store typeface.
 *
 * Elementor also requests Bricolage Grotesque and Karla for the kit's Global Fonts, from
 * Google's older font address. That copy has no optical sizes and, printed after "ts-fonts",
 * it replaced it: large headings were drawn with the small-text letter shapes (wider, looser
 * than the design). Where "ts-fonts" is used, Elementor's copy of these two fonts is left out
 * (and "ts-fonts" is printed in its place if it has not been printed yet). Other fonts chosen
 * in Elementor still load as usual.
 */
add_filter(
	'style_loader_tag',
	static function ( $tag, $handle ) {
		static $duplicates = array(
			'elementor-gf-bricolagegrotesque',
			'elementor-gf-karla',
			'elementor-gf-local-bricolagegrotesque',
			'elementor-gf-local-karla',
		);
		if ( ! in_array( $handle, $duplicates, true ) || ! apply_filters( 'ts_widgets_load_google_fonts', true ) || ! wp_style_is( 'ts-fonts', 'registered' ) ) {
			return $tag;
		}
		$styles = wp_styles();
		if ( in_array( 'ts-fonts', $styles->done, true ) ) {
			return '';
		}
		ob_start();
		$styles->do_item( 'ts-fonts' );
		$styles->done[] = 'ts-fonts';
		return (string) ob_get_clean();
	},
	10,
	2
);

/**
 * Speed up the font request with connection hints.
 */
add_filter(
	'wp_resource_hints',
	static function ( $urls, $relation_type ) {
		if ( 'preconnect' === $relation_type && apply_filters( 'ts_widgets_load_google_fonts', true ) ) {
			$urls[] = 'https://fonts.googleapis.com';
			$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' );
		}
		return $urls;
	},
	10,
	2
);

/**
 * Register the "Travel Store" widget category.
 *
 * @param \Elementor\Elements_Manager $elements_manager
 */
add_action(
	'elementor/elements/categories_registered',
	static function ( $elements_manager ) {
		$elements_manager->add_category(
			'travel-store',
			array(
				'title' => 'Travel Store',
				'icon'  => 'eicon-gallery-grid',
			)
		);
	}
);

/**
 * Register Travel Store widgets.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager
 */
add_action(
	'elementor/widgets/register',
	static function ( $widgets_manager ) {
		if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
			return;
		}
		require_once TSW_PATH . 'widgets/class-ts-oman-frames.php';
		$widgets_manager->register( new \TravelStore\Widgets\TS_Oman_Frames() );

		// Reusable, page-independent widgets (DEC-015).
		require_once TSW_PATH . 'includes/class-ts-base-widget.php';
		foreach ( array( 'section-header', 'art', 'cards', 'media-cards', 'faq-list', 'panel', 'stats', 'steps', 'info-list', 'split-feature', 'tabs', 'testimonials', 'route-story', 'season-calendar', 'trip-cards', 'enquiry-form', 'notice', 'page-menu', 'comparison', 'atlas', 'season-matrix', 'time-bars', 'trip-picker', 'photo-gallery' ) as $slug ) {
			require_once TSW_PATH . 'widgets/class-ts-' . $slug . '.php';
		}
		$widgets_manager->register( new \TravelStore\Widgets\TS_Section_Header() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Art() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Cards() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Media_Cards() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_FAQ_List() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Panel() );
		// Phase 2 (FUT-005).
		$widgets_manager->register( new \TravelStore\Widgets\TS_Stats() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Steps() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Info_List() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Split_Feature() );
		// Home-page widgets (tabs, reviews).
		$widgets_manager->register( new \TravelStore\Widgets\TS_Tabs() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Testimonials() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Route_Story() );
		// 1.6.0: month calendar; WP Travel Engine trips as cards.
		$widgets_manager->register( new \TravelStore\Widgets\TS_Season_Calendar() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Trip_Cards() );
		// 1.7.0: enquiry form (saved and emailed by the TS Enquiries plugin).
		$widgets_manager->register( new \TravelStore\Widgets\TS_Enquiry_Form() );
		// 1.8.0: notice box with formatted text; "On this page" menu.
		$widgets_manager->register( new \TravelStore\Widgets\TS_Notice() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Page_Menu() );
		// 1.9.0: two columns compared topic by topic.
		$widgets_manager->register( new \TravelStore\Widgets\TS_Comparison() );
		// 1.10.0: month-picker map, year chart and travel-time bars (Destinations page).
		$widgets_manager->register( new \TravelStore\Widgets\TS_Atlas() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Season_Matrix() );
		$widgets_manager->register( new \TravelStore\Widgets\TS_Time_Bars() );
		// 1.11.0: "What sounds like your trip?" buttons that highlight parts of the page (Experiences page).
		$widgets_manager->register( new \TravelStore\Widgets\TS_Trip_Picker() );
		// 1.13.0: guest photo gallery with category buttons and an enlarged view.
		$widgets_manager->register( new \TravelStore\Widgets\TS_Photo_Gallery() );
	}
);
