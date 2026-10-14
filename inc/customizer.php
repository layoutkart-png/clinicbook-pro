<?php
/**
 * ClinicBook Pro — Customizer settings.
 *
 * @package ClinicBook_Pro
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function cbp_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'cbp_clinic', array(
		'title'    => __( 'Clinic Settings', 'clinicbook-pro' ),
		'priority' => 30,
	) );

	$settings = array(
		'cbp_phone' => array(
			'label'   => __( 'Clinic Phone', 'clinicbook-pro' ),
			'default' => '+91 98765 43210',
			'type'    => 'text',
		),
		'cbp_timing' => array(
			'label'   => __( 'Clinic Timing', 'clinicbook-pro' ),
			'default' => __( 'Mon–Sat: 10 AM – 8 PM', 'clinicbook-pro' ),
			'type'    => 'text',
		),
		'cbp_address' => array(
			'label'   => __( 'Clinic Address', 'clinicbook-pro' ),
			'default' => __( '123 Health Street, Prayagraj, UP', 'clinicbook-pro' ),
			'type'    => 'textarea',
		),
		'cbp_whatsapp' => array(
			'label'       => __( 'WhatsApp Number (with country code, digits only)', 'clinicbook-pro' ),
			'default'     => '919876543210',
			'type'        => 'text',
			'description' => __( 'Used for the appointment confirmation button.', 'clinicbook-pro' ),
		),
		'cbp_hero_title' => array(
			'label'   => __( 'Hero Headline', 'clinicbook-pro' ),
			'default' => __( 'Find the Right Doctor, Book in Seconds', 'clinicbook-pro' ),
			'type'    => 'text',
		),
		'cbp_hero_sub' => array(
			'label'   => __( 'Hero Subtext', 'clinicbook-pro' ),
			'default' => __( 'Verified doctors, instant slot booking and WhatsApp confirmations — healthcare made simple.', 'clinicbook-pro' ),
			'type'    => 'textarea',
		),
		'cbp_accent' => array(
			'label'   => __( 'Primary Color', 'clinicbook-pro' ),
			'default' => '#0E8AFD',
			'type'    => 'color',
		),
		'cbp_pharmacy_whatsapp' => array(
			'label'       => __( 'Pharmacy WhatsApp Number (digits only)', 'clinicbook-pro' ),
			'default'     => '',
			'type'        => 'text',
			'description' => __( 'Used for medicine ordering buttons. Leave empty to reuse the clinic WhatsApp number.', 'clinicbook-pro' ),
		),
		'cbp_rx_info' => array(
			'label'       => __( 'Prescription (Rx) Info Text', 'clinicbook-pro' ),
			'default'     => __( 'This is a prescription medicine. Please share a valid prescription on WhatsApp while ordering.', 'clinicbook-pro' ),
			'type'        => 'textarea',
			'description' => __( 'Shown under the WhatsApp order button on prescription products.', 'clinicbook-pro' ),
		),
	);

	foreach ( $settings as $id => $args ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $args['default'],
			'sanitize_callback' => $args['type'] === 'color' ? 'sanitize_hex_color' : 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$control_args = array(
			'label'   => $args['label'],
			'section' => 'cbp_clinic',
		);
		if ( ! empty( $args['description'] ) ) { $control_args['description'] = $args['description']; }
		if ( $args['type'] === 'color' ) {
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, $control_args ) );
		} elseif ( $args['type'] === 'textarea' ) {
			$control_args['type'] = 'textarea';
			$wp_customize->add_control( $id, $control_args );
		} else {
			$control_args['type'] = 'text';
			$wp_customize->add_control( $id, $control_args );
		}
	}
}
add_action( 'customize_register', 'cbp_customize_register' );

/* ---------- accent color CSS ---------- */
function cbp_accent_css() {
	$accent = get_theme_mod( 'cbp_accent', '#0E8AFD' );
	if ( strtolower( $accent ) === '#0e8afd' ) { return; }
	?>
	<style>:root{--cbp-blue:<?php echo esc_html( $accent ); ?>}</style>
	<?php
}
add_action( 'wp_head', 'cbp_accent_css' );
