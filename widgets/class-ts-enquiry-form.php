<?php
/**
 * Widget: TS Enquiry Form — the contact page's enquiry form (1.7.0, DEC-020).
 *
 * Enquiry types as choice cards; each type shows the interests, the rental questions or nothing extra.
 * Contact details, dates, travellers, message, "how did you hear about us" and consent. Checked in the
 * browser and again on the server; a thank-you message replaces the form.
 *
 * Saving, the office email and the acknowledgement are done by the TS Enquiries plugin
 * (travelstore-enquiries). Fields, defaults and the server check: TravelStore\TS_Enquiry_Fields.
 * Works without JavaScript too (normal form post, the page reloads with the thank-you message).
 */

namespace TravelStore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use TravelStore\TS_Enquiry_Fields as F;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TS_Enquiry_Form extends TS_Base_Widget {

	public function get_name() {
		return F::WIDGET;
	}

	public function get_title() {
		return __( 'TS Enquiry Form', 'travelstore-custom-widgets' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_keywords() {
		return array( 'form', 'enquiry', 'contact', 'inquiry', 'email', 'travel' );
	}

	public function get_script_depends() {
		return array( 'ts-enquiry-form' );
	}

	/**
	 * Rendered on every request (not cached by Elementor): the form carries a fresh security code.
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * A text control whose default comes from TS_Enquiry_Fields.
	 */
	private function field_text( $id, $label, $args = array() ) {
		$d = F::defaults();
		$this->add_control(
			$id,
			array_merge(
				array(
					'label'       => $label,
					'type'        => Controls_Manager::TEXT,
					'default'     => isset( $d[ $id ] ) ? $d[ $id ] : '',
					'label_block' => true,
				),
				$args
			)
		);
	}

	private function field_lines( $id, $label, $description = '', $args = array() ) {
		$this->field_text(
			$id,
			$label,
			array_merge(
				array(
					'type'        => Controls_Manager::TEXTAREA,
					'rows'        => 5,
					'description' => trim( __( 'One choice per line.', 'travelstore-custom-widgets' ) . ' ' . $description ),
				),
				$args
			)
		);
	}

	private function field_switch( $id, $label, $description = '' ) {
		$d = F::defaults();
		$this->add_control(
			$id,
			array(
				'label'        => $label,
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => isset( $d[ $id ] ) ? $d[ $id ] : '',
				'description'  => $description,
			)
		);
	}

	protected function register_controls() {
		$note = __( 'Small grey text after the label, for example (optional). Leave empty for none.', 'travelstore-custom-widgets' );

		/* ---------------- Heading ---------------- */
		$this->start_controls_section( 'ts_heading', array( 'label' => __( 'Heading', 'travelstore-custom-widgets' ) ) );
		if ( ! class_exists( '\TravelStore\Enquiries\Plugin' ) ) {
			$this->add_control(
				'plugin_note',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => esc_html__( 'The TS Enquiries plugin is not active, so enquiries sent with this form are not saved or emailed. Activate it under Plugins.', 'travelstore-custom-widgets' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				)
			);
		}
		$this->field_text( 'form_title', __( 'Title', 'travelstore-custom-widgets' ), array( 'description' => __( 'Leave empty to hide.', 'travelstore-custom-widgets' ) ) );
		$this->add_tag_control( 'title_tag', __( 'Title level', 'travelstore-custom-widgets' ), 'h2' );
		$this->field_text( 'form_intro', __( 'Intro text', 'travelstore-custom-widgets' ), array( 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'description' => __( 'A * in this text is shown in the "required" colour.', 'travelstore-custom-widgets' ) ) );
		$this->add_control(
			'office_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Where enquiries are emailed, and the acknowledgement email, are set in WordPress under Enquiries > Settings.', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->end_controls_section();

		/* ---------------- Enquiry types ---------------- */
		$this->start_controls_section( 'ts_types', array( 'label' => __( 'What can we help with?', 'travelstore-custom-widgets' ) ) );
		$this->field_text( 'types_label', __( 'Question', 'travelstore-custom-widgets' ) );
		$r = new Repeater();
		$r->add_control(
			'title',
			array(
				'label'       => __( 'Choice', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$r->add_control(
			'text',
			array(
				'label'       => __( 'Short description', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$r->add_control(
			'extra',
			array(
				'label'   => __( 'When chosen, also ask', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'interests',
				'options' => F::extra_options(),
			)
		);
		$this->add_control(
			'types',
			array(
				'label'       => __( 'Choices', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => F::default_types(),
				'description' => __( 'The first choice is selected when the page opens.', 'travelstore-custom-widgets' ),
			)
		);
		$this->end_controls_section();

		/* ---------------- Contact details ---------------- */
		$this->start_controls_section( 'ts_contact', array( 'label' => __( 'Contact details', 'travelstore-custom-widgets' ) ) );
		$this->field_text( 'name_label', __( 'Name – label', 'travelstore-custom-widgets' ), array( 'description' => __( 'Name, email, message and the consent tick are always required.', 'travelstore-custom-widgets' ) ) );
		$this->field_text( 'email_label', __( 'Email – label', 'travelstore-custom-widgets' ) );
		$this->field_switch( 'show_phone', __( 'Ask for a phone number', 'travelstore-custom-widgets' ) );
		$phone = array( 'condition' => array( 'show_phone' => 'yes' ) );
		$this->field_text( 'phone_label', __( 'Phone – label', 'travelstore-custom-widgets' ), $phone );
		$this->field_text( 'phone_note', __( 'Phone – note', 'travelstore-custom-widgets' ), $phone + array( 'description' => $note ) );
		$this->field_text( 'phone_placeholder', __( 'Phone – example shown in the empty box', 'travelstore-custom-widgets' ), $phone );
		$this->field_text( 'phone_hint', __( 'Phone – help text under the box', 'travelstore-custom-widgets' ), $phone );
		$this->field_switch( 'show_country', __( 'Ask which country they live in', 'travelstore-custom-widgets' ) );
		$country = array( 'condition' => array( 'show_country' => 'yes' ) );
		$this->field_text( 'country_label', __( 'Country – label', 'travelstore-custom-widgets' ), $country );
		$this->field_text( 'country_note', __( 'Country – note', 'travelstore-custom-widgets' ), $country + array( 'description' => $note ) );
		$this->end_controls_section();

		/* ---------------- Dates and travellers ---------------- */
		$this->start_controls_section( 'ts_dates', array( 'label' => __( 'Dates and travellers', 'travelstore-custom-widgets' ) ) );
		$this->field_switch( 'show_dates', __( 'Ask for travel dates', 'travelstore-custom-widgets' ) );
		$dates = array( 'condition' => array( 'show_dates' => 'yes' ) );
		$this->field_text( 'from_label', __( 'Arrival date – label', 'travelstore-custom-widgets' ), $dates );
		$this->field_text( 'from_note', __( 'Arrival date – note', 'travelstore-custom-widgets' ), $dates + array( 'description' => $note ) );
		$this->field_text( 'to_label', __( 'Departure date – label', 'travelstore-custom-widgets' ), $dates );
		$this->field_text( 'to_note', __( 'Departure date – note', 'travelstore-custom-widgets' ), $dates + array( 'description' => $note ) );
		$this->field_text( 'flexible_label', __( '"Flexible dates" tick box', 'travelstore-custom-widgets' ), $dates );
		$this->field_switch( 'show_travellers', __( 'Ask how many adults and children', 'travelstore-custom-widgets' ) );
		$trav = array( 'condition' => array( 'show_travellers' => 'yes' ) );
		$this->field_text( 'adults_label', __( 'Adults – label', 'travelstore-custom-widgets' ), $trav );
		$this->add_control(
			'adults_default',
			array(
				'label'     => __( 'Adults – selected when the page opens', 'travelstore-custom-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '2',
				'options'   => array_combine( F::ADULTS, F::ADULTS ),
				'condition' => $trav['condition'],
			)
		);
		$this->field_text( 'children_label', __( 'Children – label', 'travelstore-custom-widgets' ), $trav );
		$this->field_text( 'children_note', __( 'Children – note', 'travelstore-custom-widgets' ), $trav + array( 'description' => $note ) );
		$this->end_controls_section();

		/* ---------------- Interests ---------------- */
		$this->start_controls_section(
			'ts_interests',
			array(
				'label' => __( 'Interests', 'travelstore-custom-widgets' ),
			)
		);
		$this->add_control(
			'interests_note_help',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Shown for the choices set to "When chosen, also ask: The interests".', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->field_text( 'interests_label', __( 'Question', 'travelstore-custom-widgets' ) );
		$this->field_text( 'interests_note', __( 'Note', 'travelstore-custom-widgets' ), array( 'description' => $note ) );
		$this->field_lines( 'interests', __( 'Interests', 'travelstore-custom-widgets' ), __( 'Visitors can pick any number.', 'travelstore-custom-widgets' ), array( 'rows' => 8 ) );
		$this->end_controls_section();

		/* ---------------- Rental questions ---------------- */
		$this->start_controls_section( 'ts_rental', array( 'label' => __( 'Rental questions', 'travelstore-custom-widgets' ) ) );
		$this->add_control(
			'rental_help',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Shown for the choices set to "When chosen, also ask: The rental questions".', 'travelstore-custom-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->field_text( 'rental_title', __( 'Box title', 'travelstore-custom-widgets' ) );
		$this->field_text( 'vehicle_label', __( 'Vehicle – label', 'travelstore-custom-widgets' ) );
		$this->field_lines( 'vehicles', __( 'Vehicles', 'travelstore-custom-widgets' ) );
		$this->field_text( 'pickup_label', __( 'Pick-up – label', 'travelstore-custom-widgets' ) );
		$this->field_lines( 'pickups', __( 'Pick-up places', 'travelstore-custom-widgets' ) );
		$this->field_text( 'drivers_label', __( 'Number of drivers – label', 'travelstore-custom-widgets' ), array( 'description' => __( 'Choices: 1, 2, 3+.', 'travelstore-custom-widgets' ) ) );
		$this->field_text( 'route_label', __( 'Route notes – label', 'travelstore-custom-widgets' ) );
		$this->field_lines( 'route_options', __( 'Route notes – choices', 'travelstore-custom-widgets' ), '', array( 'rows' => 3 ) );
		$this->end_controls_section();

		/* ---------------- Message and sending ---------------- */
		$this->start_controls_section( 'ts_message', array( 'label' => __( 'Message and sending', 'travelstore-custom-widgets' ) ) );
		$this->field_text( 'message_label', __( 'Message – label', 'travelstore-custom-widgets' ) );
		$this->field_text( 'message_placeholder', __( 'Message – example shown in the empty box', 'travelstore-custom-widgets' ), array( 'type' => Controls_Manager::TEXTAREA, 'rows' => 2 ) );
		$this->field_switch( 'show_heard', __( 'Ask how they heard about us', 'travelstore-custom-widgets' ) );
		$heard = array( 'condition' => array( 'show_heard' => 'yes' ) );
		$this->field_text( 'heard_label', __( 'How they heard – label', 'travelstore-custom-widgets' ), $heard );
		$this->field_text( 'heard_note', __( 'How they heard – note', 'travelstore-custom-widgets' ), $heard + array( 'description' => $note ) );
		$this->field_text( 'heard_placeholder', __( 'How they heard – first line of the list', 'travelstore-custom-widgets' ), $heard );
		$this->field_lines( 'heard_options', __( 'How they heard – choices', 'travelstore-custom-widgets' ), '', $heard );
		$this->field_text( 'consent_text', __( 'Consent tick box', 'travelstore-custom-widgets' ), array( 'type' => Controls_Manager::TEXTAREA, 'rows' => 2 ) );
		$this->field_text( 'submit_text', __( 'Button text', 'travelstore-custom-widgets' ) );
		$this->add_control(
			'submit_style',
			array(
				'label'   => __( 'Button look', 'travelstore-custom-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'primary',
				'options' => $this->button_style_options(),
			)
		);
		$this->field_text( 'sending_text', __( 'Button text while sending', 'travelstore-custom-widgets' ) );
		$this->field_text( 'submit_note', __( 'Note beside the button', 'travelstore-custom-widgets' ) );
		$this->end_controls_section();

		/* ---------------- After sending ---------------- */
		$this->start_controls_section( 'ts_success', array( 'label' => __( 'Thank-you message', 'travelstore-custom-widgets' ) ) );
		$this->field_text( 'success_title', __( 'Title', 'travelstore-custom-widgets' ) );
		$this->field_text( 'success_text', __( 'Text', 'travelstore-custom-widgets' ), array( 'type' => Controls_Manager::TEXTAREA, 'rows' => 3 ) );
		$this->field_switch( 'success_first_name', __( 'Start the text with "Thanks, [first name]."', 'travelstore-custom-widgets' ) );
		$this->field_text( 'success_button_text', __( 'WhatsApp button text', 'travelstore-custom-widgets' ), array( 'description' => __( 'Leave empty to hide the button.', 'travelstore-custom-widgets' ) ) );
		$this->field_text( 'success_whatsapp', __( 'WhatsApp number', 'travelstore-custom-widgets' ), array( 'description' => __( 'With the country code; spaces are fine.', 'travelstore-custom-widgets' ) ) );
		$this->end_controls_section();

		/* ---------------- Messages ---------------- */
		$this->start_controls_section( 'ts_errors', array( 'label' => __( 'Error messages', 'travelstore-custom-widgets' ) ) );
		$this->field_text( 'err_name', __( 'Name missing', 'travelstore-custom-widgets' ) );
		$this->field_text( 'err_email', __( 'Email not valid', 'travelstore-custom-widgets' ) );
		$this->field_text( 'err_message', __( 'Message missing', 'travelstore-custom-widgets' ) );
		$this->field_text( 'err_dates', __( 'Leaving before arriving', 'travelstore-custom-widgets' ) );
		$this->field_text( 'err_consent', __( 'Consent not ticked', 'travelstore-custom-widgets' ) );
		$this->field_text( 'err_summary', __( 'Under the form, when something is missing', 'travelstore-custom-widgets' ) );
		$this->field_text( 'err_wait', __( 'Sent twice within 30 seconds', 'travelstore-custom-widgets' ) );
		$this->field_text( 'err_send', __( 'Could not be sent', 'travelstore-custom-widgets' ) );
		$this->end_controls_section();

		$this->register_style_controls();
		$this->register_extra_css_controls();
	}

	private function register_style_controls() {
		/* Form box */
		$this->start_controls_section( 'ts_section_box', array( 'label' => __( 'Form box', 'travelstore-custom-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_color_control( 'box_bg', __( 'Background colour', 'travelstore-custom-widgets' ), '.ts-enq', 'background-color' );
		$this->add_color_control( 'box_border', __( 'Border colour', 'travelstore-custom-widgets' ), '.ts-enq', 'border-color' );
		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-enq' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Padding', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ts-enq' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		/* Heading */
		$this->start_controls_section( 'ts_section_heading_style', array( 'label' => __( 'Heading', 'travelstore-custom-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_typography_control( 'title_typography', __( 'Title typography', 'travelstore-custom-widgets' ), '.ts-enq__title', 'ts_heading' );
		$this->add_color_control( 'title_color', __( 'Title colour', 'travelstore-custom-widgets' ), '.ts-enq__title' );
		$this->add_typography_control( 'intro_typography', __( 'Intro typography', 'travelstore-custom-widgets' ), '.ts-enq__intro', 'ts_body' );
		$this->add_color_control( 'intro_color', __( 'Intro colour', 'travelstore-custom-widgets' ), '.ts-enq__intro' );
		$this->end_controls_section();

		/* Labels and boxes */
		$this->start_controls_section( 'ts_section_fields', array( 'label' => __( 'Labels and boxes', 'travelstore-custom-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control(
			'accent',
			array(
				'label'       => __( 'Highlight colour', 'travelstore-custom-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'The chosen enquiry type, the box being typed in, and the tick boxes.', 'travelstore-custom-widgets' ),
				'global'      => array( 'default' => 'globals/colors?id=ts_terracotta' ),
				'selectors'   => array( '{{WRAPPER}} .ts-enq' => '--ts-enq-accent: {{VALUE}};' ),
			)
		);
		$this->add_typography_control( 'label_typography', __( 'Label typography', 'travelstore-custom-widgets' ), '.ts-enq__label' );
		$this->add_color_control( 'label_color', __( 'Label colour', 'travelstore-custom-widgets' ), '.ts-enq__label' );
		$this->add_color_control( 'req_color', __( '"Required" star colour', 'travelstore-custom-widgets' ), '.ts-enq__req' );
		$this->add_color_control( 'field_bg', __( 'Box colour', 'travelstore-custom-widgets' ), '.ts-enq__input', 'background-color' );
		$this->add_color_control( 'field_border', __( 'Box border colour', 'travelstore-custom-widgets' ), '.ts-enq__input', 'border-color' );
		$this->add_color_control( 'field_text', __( 'Typed text colour', 'travelstore-custom-widgets' ), '.ts-enq__input' );
		$this->add_responsive_control(
			'field_radius',
			array(
				'label'      => __( 'Box corner roundness', 'travelstore-custom-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'selectors'  => array( '{{WRAPPER}} .ts-enq__input' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_color_control( 'type_bg', __( 'Enquiry type cards – colour', 'travelstore-custom-widgets' ), '.ts-enq__type-label', 'background-color' );
		$this->add_color_control( 'type_on_bg', __( 'Enquiry type cards – colour when chosen', 'travelstore-custom-widgets' ), '.ts-enq__type input:checked + .ts-enq__type-label', 'background-color' );
		$this->add_color_control( 'pill_on_bg', __( 'Interest pills – colour when picked', 'travelstore-custom-widgets' ), '.ts-enq__pill input:checked + label', 'background-color' );
		$this->add_color_control( 'error_color', __( 'Error colour', 'travelstore-custom-widgets' ), '.ts-enq', '--ts-enq-error' );
		$this->end_controls_section();

		/* Thank-you message */
		$this->start_controls_section( 'ts_section_success_style', array( 'label' => __( 'Thank-you message', 'travelstore-custom-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_typography_control( 'success_title_typography', __( 'Title typography', 'travelstore-custom-widgets' ), '.ts-enq__success-title', 'ts_heading' );
		$this->add_color_control( 'success_title_color', __( 'Title colour', 'travelstore-custom-widgets' ), '.ts-enq__success-title' );
		$this->add_typography_control( 'success_text_typography', __( 'Text typography', 'travelstore-custom-widgets' ), '.ts-enq__success-text', 'ts_body' );
		$this->add_color_control( 'success_text_color', __( 'Text colour', 'travelstore-custom-widgets' ), '.ts-enq__success-text' );
		$this->add_color_control( 'success_icon_bg', __( 'Tick circle colour', 'travelstore-custom-widgets' ), '.ts-enq__success-icon', 'background-color' );
		$this->add_color_control( 'success_icon', __( 'Tick colour', 'travelstore-custom-widgets' ), '.ts-enq__success-icon', 'color' );
		$this->add_color_control( 'whatsapp_bg', __( 'WhatsApp button colour', 'travelstore-custom-widgets' ), '.ts-btn.ts-enq__wa', 'background-color' );
		$this->end_controls_section();

		$this->add_button_style_section();
	}

	/* ------------------------------------------------------------------ */
	/* Render                                                              */
	/* ------------------------------------------------------------------ */

	protected function render() {
		$s     = F::settings( $this->get_settings_for_display() );
		$types = F::types( $s );
		$uid   = 'ts-enq-' . $this->get_id();
		$t     = static function ( $key ) use ( $s ) {
			return F::text( $s, $key );
		};

		if ( empty( $types ) ) {
			$this->render_empty( __( 'Add at least one choice under "What can we help with?".', 'travelstore-custom-widgets' ) );
			return;
		}

		// Result of a form sent without JavaScript (the page reloads with ?ts_enquiry=…).
		$result = '';
		if ( isset( $_GET['ts_enquiry'], $_GET['ts_enquiry_w'] ) && sanitize_key( wp_unslash( $_GET['ts_enquiry_w'] ) ) === $this->get_id() ) { // phpcs:ignore WordPress.Security.NonceVerification
			$result = sanitize_key( wp_unslash( $_GET['ts_enquiry'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}
		$done = 'received' === $result;

		$config = array(
			'ajax'      => admin_url( 'admin-ajax.php' ),
			'firstName' => F::on( $s, 'success_first_name' ),
			'sending'   => $t( 'sending_text' ),
			'errWait'   => $t( 'err_wait' ),
			'errSend'   => $t( 'err_send' ),
			/* translators: %s: the visitor's first name. */
			'thanks'    => __( 'Thanks, %s.', 'travelstore-custom-widgets' ),
		);
		$styles = $this->button_style_options();
		$bstyle = isset( $s['submit_style'], $styles[ $s['submit_style'] ] ) ? $s['submit_style'] : 'primary';
		$tag    = $this->safe_tag( isset( $s['title_tag'] ) ? $s['title_tag'] : 'h2', 'h2' );
		$req    = '<span class="ts-enq__req" aria-hidden="true">*</span>';
		$post   = (int) get_the_ID();
		?>
		<div class="ts-w ts-enq" id="<?php echo esc_attr( $uid ); ?>" data-ts-enq="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
			<div class="ts-enq__main"<?php echo $done ? ' hidden' : ''; ?>>
				<?php if ( '' !== $t( 'form_title' ) ) : ?>
					<<?php echo esc_attr( $tag ); ?> class="ts-enq__title"><?php echo esc_html( $t( 'form_title' ) ); ?></<?php echo esc_attr( $tag ); ?>>
				<?php endif; ?>
				<?php if ( '' !== $t( 'form_intro' ) ) : ?>
					<p class="ts-enq__intro"><?php echo str_replace( '*', $req, esc_html( $t( 'form_intro' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				<?php endif; ?>

				<form class="ts-enq__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
					<input type="hidden" name="action" value="ts_enquiry_submit">
					<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'ts_enquiry_submit' ) ); ?>">
					<input type="hidden" name="post_id" value="<?php echo esc_attr( $post ); ?>">
					<input type="hidden" name="widget_id" value="<?php echo esc_attr( $this->get_id() ); ?>">
					<div class="ts-enq__hp" aria-hidden="true"><label for="<?php echo esc_attr( $uid ); ?>-website">Website</label><input id="<?php echo esc_attr( $uid ); ?>-website" type="text" name="website" tabindex="-1" autocomplete="off"></div>

					<fieldset class="ts-enq__fs">
						<legend class="ts-enq__label"><?php echo esc_html( $t( 'types_label' ) ); ?> <?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput ?></legend>
						<div class="ts-enq__types">
							<?php foreach ( $types as $i => $type ) : ?>
								<div class="ts-enq__type">
									<input type="radio" name="enquiry_type" id="<?php echo esc_attr( $uid . '-t' . $i ); ?>" value="<?php echo esc_attr( $type['title'] ); ?>" data-extra="<?php echo esc_attr( $type['extra'] ); ?>"<?php checked( 0, $i ); ?>>
									<label class="ts-enq__type-label" for="<?php echo esc_attr( $uid . '-t' . $i ); ?>">
										<span class="ts-enq__tick" aria-hidden="true"></span>
										<span><b class="ts-enq__type-title"><?php echo esc_html( $type['title'] ); ?></b><?php if ( '' !== $type['text'] ) : ?><span class="ts-enq__type-text"><?php echo esc_html( $type['text'] ); ?></span><?php endif; ?></span>
									</label>
								</div>
							<?php endforeach; ?>
						</div>
					</fieldset>

					<div class="ts-enq__row">
						<?php
						$this->input( $uid, 'full_name', $t( 'name_label' ), '', 'text', array( 'required' => true, 'autocomplete' => 'name', 'error' => $t( 'err_name' ) ) );
						$this->input( $uid, 'email', $t( 'email_label' ), '', 'email', array( 'required' => true, 'autocomplete' => 'email', 'error' => $t( 'err_email' ) ) );
						?>
					</div>
					<?php if ( F::on( $s, 'show_phone' ) || F::on( $s, 'show_country' ) ) : ?>
						<div class="ts-enq__row">
							<?php
							if ( F::on( $s, 'show_phone' ) ) {
								$this->input( $uid, 'phone', $t( 'phone_label' ), $t( 'phone_note' ), 'tel', array( 'autocomplete' => 'tel', 'placeholder' => $t( 'phone_placeholder' ), 'hint' => $t( 'phone_hint' ) ) );
							}
							if ( F::on( $s, 'show_country' ) ) {
								$this->input( $uid, 'country', $t( 'country_label' ), $t( 'country_note' ), 'text', array( 'autocomplete' => 'country-name' ) );
							}
							?>
						</div>
					<?php endif; ?>

					<?php if ( F::on( $s, 'show_dates' ) ) : ?>
						<div class="ts-enq__row">
							<?php
							$this->input( $uid, 'date_from', $t( 'from_label' ), $t( 'from_note' ), 'date' );
							$this->input( $uid, 'date_to', $t( 'to_label' ), $t( 'to_note' ), 'date', array( 'error' => $t( 'err_dates' ) ) );
							?>
						</div>
						<label class="ts-enq__check"><input type="checkbox" name="dates_flexible" value="yes"> <span><?php echo esc_html( $t( 'flexible_label' ) ); ?></span></label>
					<?php endif; ?>

					<?php if ( F::on( $s, 'show_travellers' ) ) : ?>
						<div class="ts-enq__row">
							<?php
							$this->select( $uid, 'adults', $t( 'adults_label' ), '', F::ADULTS, isset( $s['adults_default'] ) ? (string) $s['adults_default'] : '2' );
							$this->select( $uid, 'children', $t( 'children_label' ), $t( 'children_note' ), F::CHILDREN, '0' );
							?>
						</div>
					<?php endif; ?>

					<?php $interests = F::lines( $s, 'interests' ); ?>
					<?php if ( ! empty( $interests ) ) : ?>
						<div class="ts-enq__extra" data-extra="interests">
							<fieldset class="ts-enq__fs">
								<legend class="ts-enq__label"><?php echo esc_html( $t( 'interests_label' ) ); ?><?php echo $this->note( $t( 'interests_note' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></legend>
								<div class="ts-enq__pills">
									<?php foreach ( $interests as $i => $interest ) : ?>
										<div class="ts-enq__pill"><input type="checkbox" id="<?php echo esc_attr( $uid . '-i' . $i ); ?>" name="interests[]" value="<?php echo esc_attr( $interest ); ?>"><label for="<?php echo esc_attr( $uid . '-i' . $i ); ?>"><?php echo esc_html( $interest ); ?></label></div>
									<?php endforeach; ?>
								</div>
							</fieldset>
						</div>
					<?php endif; ?>

					<div class="ts-enq__extra" data-extra="rental">
						<div class="ts-enq__box">
							<?php if ( '' !== $t( 'rental_title' ) ) : ?>
								<div class="ts-enq__box-title"><?php echo esc_html( $t( 'rental_title' ) ); ?></div>
							<?php endif; ?>
							<div class="ts-enq__row">
								<?php
								$this->select( $uid, 'vehicle', $t( 'vehicle_label' ), '', F::lines( $s, 'vehicles' ) );
								$this->select( $uid, 'pickup', $t( 'pickup_label' ), '', F::lines( $s, 'pickups' ) );
								?>
							</div>
							<div class="ts-enq__row">
								<?php
								$this->select( $uid, 'drivers', $t( 'drivers_label' ), '', F::DRIVERS );
								$this->select( $uid, 'route_notes', $t( 'route_label' ), '', F::lines( $s, 'route_options' ) );
								?>
							</div>
						</div>
					</div>

					<?php $this->input( $uid, 'message', $t( 'message_label' ), '', 'textarea', array( 'required' => true, 'placeholder' => $t( 'message_placeholder' ), 'error' => $t( 'err_message' ) ) ); ?>

					<?php
					if ( F::on( $s, 'show_heard' ) ) {
						$this->select( $uid, 'heard_about', $t( 'heard_label' ), $t( 'heard_note' ), F::lines( $s, 'heard_options' ), '', $t( 'heard_placeholder' ) );
					}
					?>

					<div class="ts-enq__field ts-enq__field--consent" data-field="consent">
						<label class="ts-enq__check"><input type="checkbox" name="consent" value="yes" required aria-describedby="<?php echo esc_attr( $uid ); ?>-consent-err"> <span><?php echo esc_html( $t( 'consent_text' ) ); ?> <?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput ?></span></label>
						<div class="ts-enq__err" id="<?php echo esc_attr( $uid ); ?>-consent-err"><?php echo esc_html( $t( 'err_consent' ) ); ?></div>
					</div>

					<div class="ts-enq__summary" role="alert" data-default="<?php echo esc_attr( $t( 'err_summary' ) ); ?>"<?php echo in_array( $result, array( 'invalid', 'wait', 'error' ), true ) ? '' : ' hidden'; ?>>
						<?php
						$messages = array(
							'invalid' => $t( 'err_summary' ),
							'wait'    => $t( 'err_wait' ),
							'error'   => $t( 'err_send' ),
						);
						echo esc_html( isset( $messages[ $result ] ) ? $messages[ $result ] : $t( 'err_summary' ) );
						?>
					</div>
					<div class="ts-enq__submit-row">
						<button type="submit" class="ts-btn ts-btn--<?php echo esc_attr( $bstyle ); ?> ts-enq__submit"><?php echo esc_html( $t( 'submit_text' ) ); ?></button>
						<?php if ( '' !== $t( 'submit_note' ) ) : ?>
							<span class="ts-enq__note"><?php echo esc_html( $t( 'submit_note' ) ); ?></span>
						<?php endif; ?>
					</div>
				</form>
			</div>

			<div class="ts-enq__success" role="status" aria-live="polite"<?php echo $done ? '' : ' hidden'; ?>>
				<div class="ts-enq__success-icon" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
				<?php if ( '' !== $t( 'success_title' ) ) : ?>
					<div class="ts-enq__success-title"><?php echo esc_html( $t( 'success_title' ) ); ?></div>
				<?php endif; ?>
				<p class="ts-enq__success-text" data-text="<?php echo esc_attr( $t( 'success_text' ) ); ?>"><?php echo esc_html( $t( 'success_text' ) ); ?></p>
				<?php
				$wa = self::contact_href( 'whatsapp', $t( 'success_whatsapp' ) );
				if ( '' !== $t( 'success_button_text' ) && '' !== $wa ) :
					?>
					<a class="ts-btn ts-enq__wa" href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $t( 'success_button_text' ) ); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	private function note( $text ) {
		return '' !== $text ? ' <span class="ts-enq__opt">' . esc_html( $text ) . '</span>' : '';
	}

	/**
	 * One labelled box (text, email, tel, date or textarea) with its error message.
	 */
	private function input( $uid, $name, $label, $note, $type, $args = array() ) {
		$id       = $uid . '-' . $name;
		$required = ! empty( $args['required'] );
		$describe = array();
		if ( ! empty( $args['hint'] ) ) {
			$describe[] = $id . '-hint';
		}
		if ( ! empty( $args['error'] ) ) {
			$describe[] = $id . '-err';
		}
		$attrs = sprintf(
			'id="%1$s" name="%2$s" class="ts-enq__input"%3$s%4$s%5$s%6$s',
			esc_attr( $id ),
			esc_attr( $name ),
			$required ? ' required' : '',
			! empty( $args['autocomplete'] ) ? ' autocomplete="' . esc_attr( $args['autocomplete'] ) . '"' : '',
			! empty( $args['placeholder'] ) ? ' placeholder="' . esc_attr( $args['placeholder'] ) . '"' : '',
			$describe ? ' aria-describedby="' . esc_attr( implode( ' ', $describe ) ) . '"' : ''
		);
		echo '<div class="ts-enq__field" data-field="' . esc_attr( $name ) . '">';
		echo '<label class="ts-enq__label" for="' . esc_attr( $id ) . '">' . esc_html( $label ) . ( $required ? ' <span class="ts-enq__req" aria-hidden="true">*</span>' : '' ) . $this->note( $note ) . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( 'textarea' === $type ) {
			echo '<textarea ' . $attrs . ' maxlength="' . (int) F::MAX_MESSAGE . '"></textarea>'; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			echo '<input type="' . esc_attr( $type ) . '" ' . $attrs . ( 'date' === $type ? '' : ' maxlength="' . (int) F::MAX_LINE . '"' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( ! empty( $args['hint'] ) ) {
			echo '<div class="ts-enq__hint" id="' . esc_attr( $id . '-hint' ) . '">' . esc_html( $args['hint'] ) . '</div>';
		}
		if ( ! empty( $args['error'] ) ) {
			echo '<div class="ts-enq__err" id="' . esc_attr( $id . '-err' ) . '">' . esc_html( $args['error'] ) . '</div>';
		}
		echo '</div>';
	}

	private function select( $uid, $name, $label, $note, $choices, $selected = '', $placeholder = '' ) {
		if ( empty( $choices ) ) {
			return;
		}
		$id = $uid . '-' . $name;
		echo '<div class="ts-enq__field" data-field="' . esc_attr( $name ) . '">';
		echo '<label class="ts-enq__label" for="' . esc_attr( $id ) . '">' . esc_html( $label ) . $this->note( $note ) . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" class="ts-enq__input">';
		if ( '' !== $placeholder ) {
			echo '<option value="">' . esc_html( $placeholder ) . '</option>';
		}
		foreach ( $choices as $choice ) {
			echo '<option value="' . esc_attr( $choice ) . '"' . selected( $selected, $choice, false ) . '>' . esc_html( $choice ) . '</option>';
		}
		echo '</select></div>';
	}
}
