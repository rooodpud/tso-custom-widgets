<?php
/**
 * TS Enquiry Form: the fields, their default labels and choices, and the server-side check of a
 * submission (1.7.0, DEC-020).
 *
 * One source for both sides:
 *  - the TS Enquiry Form widget uses defaults() for its control defaults and choices() to print the form;
 *  - the TS Enquiries plugin (travelstore-enquiries) uses clean() to check and label a submission.
 * Elementor does not save a value equal to its default, so settings read from a page are always merged
 * with defaults() first (settings()).
 *
 * No Elementor dependency: loaded when the plugin loads.
 */

namespace TravelStore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Enquiry_Fields {

	const WIDGET = 'ts-enquiry-form';

	const ADULTS   = array( '1', '2', '3', '4', '5', '6', '7–10', '11+' );
	const CHILDREN = array( '0', '1', '2', '3', '4+' );
	const DRIVERS  = array( '1', '2', '3+' );

	/** Longest accepted text, in characters. */
	const MAX_LINE    = 200;
	const MAX_MESSAGE = 5000;

	/**
	 * Every setting the form reads, with its default.
	 */
	public static function defaults() {
		return array(
			'form_title'          => __( 'Send us an enquiry', 'travelstore-custom-widgets' ),
			'form_intro'          => __( 'The more you tell us, the better our first reply will be — but only the fields marked * are required.', 'travelstore-custom-widgets' ),
			'types_label'         => __( 'What can we help with?', 'travelstore-custom-widgets' ),
			'types'               => self::default_types(),
			'name_label'          => __( 'Your name', 'travelstore-custom-widgets' ),
			'email_label'         => __( 'Email', 'travelstore-custom-widgets' ),
			'show_phone'          => 'yes',
			'phone_label'         => __( 'Phone / WhatsApp', 'travelstore-custom-widgets' ),
			'phone_note'          => __( '(optional)', 'travelstore-custom-widgets' ),
			'phone_placeholder'   => __( '+44 7700 900123', 'travelstore-custom-widgets' ),
			'phone_hint'          => __( 'Include your country code. We\'ll only message about your enquiry.', 'travelstore-custom-widgets' ),
			'show_country'        => 'yes',
			'country_label'       => __( 'Country you live in', 'travelstore-custom-widgets' ),
			'country_note'        => __( '(optional)', 'travelstore-custom-widgets' ),
			'show_dates'          => 'yes',
			'from_label'          => __( 'Arriving', 'travelstore-custom-widgets' ),
			'from_note'           => __( '(approx.)', 'travelstore-custom-widgets' ),
			'to_label'            => __( 'Leaving', 'travelstore-custom-widgets' ),
			'to_note'             => __( '(approx.)', 'travelstore-custom-widgets' ),
			'flexible_label'      => __( 'My dates are flexible', 'travelstore-custom-widgets' ),
			'show_travellers'     => 'yes',
			'adults_label'        => __( 'Adults', 'travelstore-custom-widgets' ),
			'adults_default'      => '2',
			'children_label'      => __( 'Children', 'travelstore-custom-widgets' ),
			'children_note'       => __( '(under 12)', 'travelstore-custom-widgets' ),
			'interests_label'     => __( 'What are you interested in?', 'travelstore-custom-widgets' ),
			'interests_note'      => __( '(pick any)', 'travelstore-custom-widgets' ),
			'interests'           => implode( "\n", array( 'Culture & forts', 'Desert', 'Mountains', 'Coast & sea', 'Adventure', 'Golf', 'Salalah & Khareef', 'Self-drive' ) ),
			'rental_title'        => __( 'About your rental', 'travelstore-custom-widgets' ),
			'vehicle_label'       => __( 'Vehicle', 'travelstore-custom-widgets' ),
			'vehicles'            => implode( "\n", array( 'Rooftop tent 4WD', 'Standard — 4WD', 'Standard — AWD', 'Standard — Sedan', 'Not sure yet — please advise' ) ),
			'pickup_label'        => __( 'Pick-up', 'travelstore-custom-widgets' ),
			'pickups'             => implode( "\n", array( 'Muscat Airport', 'Our Muscat office', 'Hotel delivery in Muscat', 'Somewhere else — I\'ll explain' ) ),
			'drivers_label'       => __( 'Number of drivers', 'travelstore-custom-widgets' ),
			'route_label'         => __( 'Would you like route notes?', 'travelstore-custom-widgets' ),
			'route_options'       => implode( "\n", array( 'Yes, suggest a route', 'No, I\'ll plan my own' ) ),
			'message_label'       => __( 'Tell us about your trip', 'travelstore-custom-widgets' ),
			'message_placeholder' => __( 'Places you\'d like to see, the pace you enjoy, anything we should know…', 'travelstore-custom-widgets' ),
			'show_heard'          => 'yes',
			'heard_label'         => __( 'How did you hear about us?', 'travelstore-custom-widgets' ),
			'heard_note'          => __( '(optional)', 'travelstore-custom-widgets' ),
			'heard_placeholder'   => __( 'Choose one', 'travelstore-custom-widgets' ),
			'heard_options'       => implode( "\n", array( 'Search engine', 'Social media', 'Friend or family', 'Travel agent', 'Returning guest', 'Other' ) ),
			'consent_text'        => __( 'I agree that Travel Store Oman may use these details to reply to my enquiry.', 'travelstore-custom-widgets' ),
			'submit_text'         => __( 'Send my enquiry', 'travelstore-custom-widgets' ),
			'submit_note'         => __( 'We reply within one working day.', 'travelstore-custom-widgets' ),
			'sending_text'        => __( 'Sending…', 'travelstore-custom-widgets' ),
			'success_title'       => __( 'Thank you — we\'ve got it.', 'travelstore-custom-widgets' ),
			'success_text'        => __( 'Someone from our Muscat team will reply within one working day. If it\'s urgent, message us on WhatsApp.', 'travelstore-custom-widgets' ),
			'success_first_name'  => 'yes',
			'success_button_text' => __( 'Message us on WhatsApp', 'travelstore-custom-widgets' ),
			'success_whatsapp'    => '+968 7410 0010',
			'err_name'            => __( 'Please tell us your name.', 'travelstore-custom-widgets' ),
			'err_email'           => __( 'Please enter a valid email address.', 'travelstore-custom-widgets' ),
			'err_message'         => __( 'A few words about your trip helps us reply properly.', 'travelstore-custom-widgets' ),
			'err_dates'           => __( 'Leaving date should be after your arrival date.', 'travelstore-custom-widgets' ),
			'err_consent'         => __( 'Please tick this so we can reply to you.', 'travelstore-custom-widgets' ),
			'err_summary'         => __( 'A few fields need your attention — they\'re highlighted above.', 'travelstore-custom-widgets' ),
			'err_wait'            => __( 'Please wait a moment before sending another enquiry.', 'travelstore-custom-widgets' ),
			'err_send'            => __( 'Sorry, your enquiry could not be sent. Please try again, or message us on WhatsApp.', 'travelstore-custom-widgets' ),
		);
	}

	/**
	 * The four enquiry types of the design. "extra": which extra questions the type shows.
	 */
	public static function default_types() {
		return array(
			array(
				'title' => __( 'Plan a trip', 'travelstore-custom-widgets' ),
				'text'  => __( 'A tailor-made tour of Oman', 'travelstore-custom-widgets' ),
				'extra' => 'interests',
			),
			array(
				'title' => __( 'Self-drive rental', 'travelstore-custom-widgets' ),
				'text'  => __( 'Standard or rooftop-tent vehicle', 'travelstore-custom-widgets' ),
				'extra' => 'rental',
			),
			array(
				'title' => __( 'Activity or day trip', 'travelstore-custom-widgets' ),
				'text'  => __( 'Adventure, sea trips, golf', 'travelstore-custom-widgets' ),
				'extra' => 'interests',
			),
			array(
				'title' => __( 'Something else', 'travelstore-custom-widgets' ),
				'text'  => __( 'Groups, partners, anything', 'travelstore-custom-widgets' ),
				'extra' => 'none',
			),
		);
	}

	public static function extra_options() {
		return array(
			'interests' => __( 'The interests (pick any)', 'travelstore-custom-widgets' ),
			'rental'    => __( 'The rental questions', 'travelstore-custom-widgets' ),
			'none'      => __( 'No extra questions', 'travelstore-custom-widgets' ),
		);
	}

	/**
	 * Saved widget settings merged with the defaults (Elementor leaves out values equal to a default).
	 */
	public static function settings( $raw ) {
		$raw = is_array( $raw ) ? $raw : array();
		$s   = array_merge( self::defaults(), $raw );
		if ( ! is_array( $s['types'] ) || empty( $s['types'] ) ) {
			$s['types'] = self::default_types();
		}

		return $s;
	}

	public static function text( $s, $key ) {
		$d = self::defaults();

		return isset( $s[ $key ] ) && is_scalar( $s[ $key ] ) ? trim( (string) $s[ $key ] ) : ( isset( $d[ $key ] ) && is_string( $d[ $key ] ) ? $d[ $key ] : '' );
	}

	public static function on( $s, $key ) {
		return isset( $s[ $key ] ) ? 'yes' === $s[ $key ] : 'yes' === ( self::defaults()[ $key ] ?? '' );
	}

	/**
	 * Trimmed, non-empty lines of a "one per line" setting.
	 */
	public static function lines( $s, $key ) {
		$lines = preg_split( '/\r\n|\r|\n/', self::text( $s, $key ) );

		return array_values( array_unique( array_filter( array_map( 'trim', $lines ), 'strlen' ) ) );
	}

	/**
	 * The enquiry types as array( title, text, extra ), without empty rows.
	 */
	public static function types( $s ) {
		$types = array();
		foreach ( (array) $s['types'] as $row ) {
			$title = isset( $row['title'] ) ? trim( (string) $row['title'] ) : '';
			if ( '' === $title ) {
				continue;
			}
			$extra   = isset( $row['extra'] ) && array_key_exists( $row['extra'], self::extra_options() ) ? $row['extra'] : 'interests';
			$types[] = array(
				'title' => $title,
				'text'  => isset( $row['text'] ) ? trim( (string) $row['text'] ) : '',
				'extra' => $extra,
				'_id'   => isset( $row['_id'] ) ? (string) $row['_id'] : '',
			);
		}

		return $types;
	}

	/**
	 * Check and label one submission.
	 *
	 * @param array $raw_settings The widget's saved settings (from the page).
	 * @param array $input        The posted fields (unslashed).
	 * @return array ok (bool), errors (field => message), data (field => value), rows (list of label/value), name, email, type.
	 */
	public static function clean( $raw_settings, $input ) {
		$s      = self::settings( $raw_settings );
		$errors = array();
		$line   = static function ( $key ) use ( $input ) {
			$value = isset( $input[ $key ] ) && is_scalar( $input[ $key ] ) ? sanitize_text_field( (string) $input[ $key ] ) : '';
			return mb_substr( $value, 0, self::MAX_LINE );
		};
		$pick   = static function ( $key, $choices, $fallback = '' ) use ( $line ) {
			$value = $line( $key );
			return in_array( $value, $choices, true ) ? $value : $fallback;
		};

		$types  = self::types( $s );
		$titles = wp_list_pluck( $types, 'title' );
		$type   = $pick( 'enquiry_type', $titles, isset( $titles[0] ) ? $titles[0] : '' );
		$extra  = 'none';
		foreach ( $types as $t ) {
			if ( $t['title'] === $type ) {
				$extra = $t['extra'];
			}
		}

		$data = array(
			'enquiry_type' => $type,
			'full_name'    => $line( 'full_name' ),
			'email'        => sanitize_email( $line( 'email' ) ),
		);
		if ( '' === $data['full_name'] ) {
			$errors['full_name'] = self::text( $s, 'err_name' );
		}
		if ( ! is_email( $data['email'] ) ) {
			$errors['email'] = self::text( $s, 'err_email' );
		}
		if ( self::on( $s, 'show_phone' ) ) {
			$data['phone'] = $line( 'phone' );
		}
		if ( self::on( $s, 'show_country' ) ) {
			$data['country'] = $line( 'country' );
		}
		if ( self::on( $s, 'show_dates' ) ) {
			$from = $line( 'date_from' );
			$to   = $line( 'date_to' );
			$data['date_from']      = self::valid_date( $from ) ? $from : '';
			$data['date_to']        = self::valid_date( $to ) ? $to : '';
			$data['dates_flexible'] = ! empty( $input['dates_flexible'] ) ? 'yes' : '';
			if ( '' !== $data['date_from'] && '' !== $data['date_to'] && $data['date_to'] < $data['date_from'] ) {
				$errors['date_to'] = self::text( $s, 'err_dates' );
			}
		}
		if ( self::on( $s, 'show_travellers' ) ) {
			$data['adults']   = $pick( 'adults', self::ADULTS );
			$data['children'] = $pick( 'children', self::CHILDREN );
		}
		if ( 'interests' === $extra ) {
			$chosen            = isset( $input['interests'] ) && is_array( $input['interests'] ) ? array_map( 'sanitize_text_field', array_map( 'strval', $input['interests'] ) ) : array();
			$data['interests'] = array_values( array_intersect( self::lines( $s, 'interests' ), $chosen ) );
		}
		if ( 'rental' === $extra ) {
			$data['vehicle']     = $pick( 'vehicle', self::lines( $s, 'vehicles' ) );
			$data['pickup']      = $pick( 'pickup', self::lines( $s, 'pickups' ) );
			$data['drivers']     = $pick( 'drivers', self::DRIVERS );
			$data['route_notes'] = $pick( 'route_notes', self::lines( $s, 'route_options' ) );
		}
		$message         = isset( $input['message'] ) && is_scalar( $input['message'] ) ? sanitize_textarea_field( (string) $input['message'] ) : '';
		$data['message'] = mb_substr( trim( $message ), 0, self::MAX_MESSAGE );
		if ( '' === $data['message'] ) {
			$errors['message'] = self::text( $s, 'err_message' );
		}
		if ( self::on( $s, 'show_heard' ) ) {
			$data['heard_about'] = $pick( 'heard_about', self::lines( $s, 'heard_options' ) );
		}
		$data['consent'] = ! empty( $input['consent'] ) ? 'yes' : '';
		if ( 'yes' !== $data['consent'] ) {
			$errors['consent'] = self::text( $s, 'err_consent' );
		}

		return array(
			'ok'     => empty( $errors ),
			'errors' => $errors,
			'data'   => $data,
			'rows'   => self::rows( $s, $data ),
			'name'   => $data['full_name'],
			'email'  => $data['email'],
			'type'   => $type,
		);
	}

	/**
	 * The submission as label / value rows, in form order, with the labels the form showed.
	 */
	public static function rows( $s, $data ) {
		$labels = array(
			'enquiry_type'   => self::text( $s, 'types_label' ),
			'full_name'      => self::text( $s, 'name_label' ),
			'email'          => self::text( $s, 'email_label' ),
			'phone'          => self::text( $s, 'phone_label' ),
			'country'        => self::text( $s, 'country_label' ),
			'date_from'      => self::text( $s, 'from_label' ),
			'date_to'        => self::text( $s, 'to_label' ),
			'dates_flexible' => self::text( $s, 'flexible_label' ),
			'adults'         => self::text( $s, 'adults_label' ),
			'children'       => self::text( $s, 'children_label' ),
			'interests'      => self::text( $s, 'interests_label' ),
			'vehicle'        => self::text( $s, 'vehicle_label' ),
			'pickup'         => self::text( $s, 'pickup_label' ),
			'drivers'        => self::text( $s, 'drivers_label' ),
			'route_notes'    => self::text( $s, 'route_label' ),
			'message'        => self::text( $s, 'message_label' ),
			'heard_about'    => self::text( $s, 'heard_label' ),
			'consent'        => __( 'Agreed to be contacted', 'travelstore-custom-widgets' ),
		);
		$rows = array();
		foreach ( $labels as $key => $label ) {
			if ( ! array_key_exists( $key, $data ) ) {
				continue;
			}
			$value = $data[ $key ];
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			} elseif ( in_array( $key, array( 'dates_flexible', 'consent' ), true ) ) {
				$value = 'yes' === $value ? __( 'Yes', 'travelstore-custom-widgets' ) : __( 'No', 'travelstore-custom-widgets' );
			} elseif ( in_array( $key, array( 'date_from', 'date_to' ), true ) && '' !== $value ) {
				$value = wp_date( 'j M Y', strtotime( $value . ' 12:00:00 UTC' ), new \DateTimeZone( 'UTC' ) );
			}
			$rows[] = array(
				'key'   => $key,
				'label' => '' !== $label ? $label : $key,
				'value' => (string) $value,
			);
		}

		return $rows;
	}

	private static function valid_date( $value ) {
		if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m ) ) {
			return false;
		}

		return checkdate( (int) $m[2], (int) $m[3], (int) $m[1] );
	}
}
