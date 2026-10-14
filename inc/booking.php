<?php
/**
 * ClinicBook Pro — booking engine: slot generation, appointment creation, WhatsApp links.
 *
 * @package ClinicBook_Pro
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Generate available time slots for a doctor on a given date (Y-m-d).
 *
 * @param int    $doctor_id Doctor post ID.
 * @param string $date      Date in Y-m-d.
 * @return array Array of ['start' => 'H:i', 'end' => 'H:i', 'booked' => bool].
 */
function cbp_get_available_slots( $doctor_id, $date ) {
	$doctor_id = absint( $doctor_id );
	$ts        = strtotime( $date );
	if ( ! $doctor_id || ! $ts || $ts < strtotime( 'today' ) ) { return array(); }

	$day_key = strtolower( date( 'D', $ts ) );
	$map     = array( 'mon' => 'mon', 'tue' => 'tue', 'wed' => 'wed', 'thu' => 'thu', 'fri' => 'fri', 'sat' => 'sat', 'sun' => 'sun' );
	$sched   = get_post_meta( $doctor_id, '_cbp_schedule', true );
	if ( ! is_array( $sched ) || empty( $sched[ $map[ $day_key ] ] ) ) { return array(); }
	$d = $sched[ $map[ $day_key ] ];

	$slot_min = max( 5, absint( $d['slot'] ?? 15 ) );
	$slots    = array();

	$ranges = array();
	if ( ! empty( $d['m_from'] ) && ! empty( $d['m_to'] ) ) { $ranges[] = array( $d['m_from'], $d['m_to'] ); }
	if ( ! empty( $d['e_from'] ) && ! empty( $d['e_to'] ) ) { $ranges[] = array( $d['e_from'], $d['e_to'] ); }

	foreach ( $ranges as $range ) {
		$start = strtotime( $date . ' ' . $range[0] );
		$end   = strtotime( $date . ' ' . $range[1] );
		if ( ! $start || ! $end || $end <= $start ) { continue; }
		for ( $t = $start; $t + $slot_min * 60 <= $end; $t += $slot_min * 60 ) {
			$slots[] = array(
				'start'  => date( 'H:i', $t ),
				'end'    => date( 'H:i', $t + $slot_min * 60 ),
				'booked' => false,
			);
		}
	}

	// Mark already-booked slots (skip cancelled).
	$booked = get_posts( array(
		'post_type'      => 'appointment',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => array(
			'relation' => 'AND',
			array( 'key' => '_cbp_doctor_id', 'value' => $doctor_id, 'compare' => '=' ),
			array( 'key' => '_cbp_date', 'value' => $date, 'compare' => '=' ),
			array( 'key' => '_cbp_status', 'value' => 'cancelled', 'compare' => '!=' ),
		),
	) );
	$taken = array();
	foreach ( $booked as $aid ) {
		$s = get_post_meta( $aid, '_cbp_slot', true );
		if ( $s ) { $taken[ $s ] = true; }
	}
	foreach ( $slots as &$s ) {
		if ( isset( $taken[ $s['start'] ] ) ) { $s['booked'] = true; }
	}
	unset( $s );

	// Hide past slots when booking for today.
	if ( $date === date( 'Y-m-d' ) ) {
		$now = current_time( 'timestamp' );
		$slots = array_values( array_filter( $slots, function( $s ) use ( $date, $now ) {
			return strtotime( $date . ' ' . $s['start'] ) > $now;
		} ) );
	}

	return $slots;
}

/**
 * Create an appointment (pending). Returns appointment ID or WP_Error.
 */
function cbp_create_appointment( $args ) {
	$doctor_id = absint( $args['doctor_id'] ?? 0 );
	$date      = sanitize_text_field( $args['date'] ?? '' );
	$slot      = sanitize_text_field( $args['slot'] ?? '' );
	$name      = sanitize_text_field( $args['name'] ?? '' );
	$phone     = sanitize_text_field( $args['phone'] ?? '' );
	$notes     = sanitize_textarea_field( $args['notes'] ?? '' );

	if ( ! $doctor_id || get_post_type( $doctor_id ) !== 'doctor' ) {
		return new WP_Error( 'bad_doctor', __( 'Invalid doctor selected.', 'clinicbook-pro' ) );
	}
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) || strtotime( $date ) < strtotime( 'today' ) ) {
		return new WP_Error( 'bad_date', __( 'Please choose a valid date.', 'clinicbook-pro' ) );
	}
	if ( $name === '' || mb_strlen( $name ) < 2 ) {
		return new WP_Error( 'bad_name', __( 'Please enter your full name.', 'clinicbook-pro' ) );
	}
	$digits = preg_replace( '/\D/', '', $phone );
	if ( strlen( $digits ) < 10 ) {
		return new WP_Error( 'bad_phone', __( 'Please enter a valid 10-digit mobile number.', 'clinicbook-pro' ) );
	}

	// Verify the slot is genuinely free.
	$free = false;
	foreach ( cbp_get_available_slots( $doctor_id, $date ) as $s ) {
		if ( $s['start'] === $slot && ! $s['booked'] ) { $free = true; break; }
	}
	if ( ! $free ) {
		return new WP_Error( 'slot_taken', __( 'This slot was just booked. Please choose another.', 'clinicbook-pro' ) );
	}

	$appt_id = 'CBP-' . strtoupper( wp_generate_password( 8, false, false ) );
	$post_id = wp_insert_post( array(
		'post_type'   => 'appointment',
		'post_title'  => $appt_id,
		'post_status' => 'publish',
	) );
	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return new WP_Error( 'db', __( 'Could not save your appointment. Please try again.', 'clinicbook-pro' ) );
	}

	update_post_meta( $post_id, '_cbp_appt_id', $appt_id );
	update_post_meta( $post_id, '_cbp_doctor_id', $doctor_id );
	update_post_meta( $post_id, '_cbp_date', $date );
	update_post_meta( $post_id, '_cbp_slot', $slot );
	update_post_meta( $post_id, '_cbp_patient_name', $name );
	update_post_meta( $post_id, '_cbp_patient_phone', $digits );
	update_post_meta( $post_id, '_cbp_notes', $notes );
	update_post_meta( $post_id, '_cbp_status', 'pending' );
	update_post_meta( $post_id, '_cbp_created', current_time( 'mysql' ) );

	return $post_id;
}

function cbp_get_appointment( $post_id ) {
	$post_id = absint( $post_id );
	if ( get_post_type( $post_id ) !== 'appointment' ) { return null; }
	return array(
		'id'           => $post_id,
		'appt_id'      => get_post_meta( $post_id, '_cbp_appt_id', true ),
		'doctor_id'    => absint( get_post_meta( $post_id, '_cbp_doctor_id', true ) ),
		'date'         => get_post_meta( $post_id, '_cbp_date', true ),
		'slot'         => get_post_meta( $post_id, '_cbp_slot', true ),
		'patient_name' => get_post_meta( $post_id, '_cbp_patient_name', true ),
		'patient_phone'=> get_post_meta( $post_id, '_cbp_patient_phone', true ),
		'notes'        => get_post_meta( $post_id, '_cbp_notes', true ),
		'status'       => get_post_meta( $post_id, '_cbp_status', true ) ?: 'pending',
	);
}

/**
 * Build a WhatsApp confirmation link (wa.me) with a prefilled bilingual message.
 */
function cbp_whatsapp_link( $appointment ) {
	$wa_number = get_theme_mod( 'cbp_whatsapp', '' );
	$wa_number = preg_replace( '/\D/', '', $wa_number );
	if ( $wa_number === '' ) { return ''; }

	$doctor_name = get_the_title( $appointment['doctor_id'] );
	$date_fmt    = date_i18n( get_option( 'date_format' ), strtotime( $appointment['date'] ) );
	$msg = sprintf(
		/* translators: 1: appointment ID, 2: doctor name, 3: date, 4: slot, 5: patient name */
		__( 'Namaste! Appointment %1$s confirm karna hai — Dr. %2$s, %3$s ko %4$s baje. Patient: %5$s', 'clinicbook-pro' ),
		$appointment['appt_id'], $doctor_name, $date_fmt, $appointment['slot'], $appointment['patient_name']
	);
	return 'https://wa.me/' . $wa_number . '?text=' . rawurlencode( $msg );
}

/* ---------- AJAX: fetch slots ---------- */
function cbp_ajax_slots() {
	check_ajax_referer( 'cbp_nonce', 'nonce' );
	$doctor_id = absint( $_POST['doctor_id'] ?? 0 );
	$date      = sanitize_text_field( $_POST['date'] ?? '' );
	$slots     = cbp_get_available_slots( $doctor_id, $date );
	$out       = array();
	foreach ( $slots as $s ) {
		$out[] = array(
			'start' => $s['start'],
			'end'   => $s['end'],
			'label' => date_i18n( 'g:i A', strtotime( $s['start'] ) ),
			'booked'=> $s['booked'],
		);
	}
	wp_send_json_success( $out );
}
add_action( 'wp_ajax_cbp_slots', 'cbp_ajax_slots' );
add_action( 'wp_ajax_nopriv_cbp_slots', 'cbp_ajax_slots' );

/* ---------- AJAX: create appointment ---------- */
function cbp_ajax_book() {
	check_ajax_referer( 'cbp_nonce', 'nonce' );
	$result = cbp_create_appointment( array(
		'doctor_id' => $_POST['doctor_id'] ?? 0,
		'date'      => $_POST['date'] ?? '',
		'slot'      => $_POST['slot'] ?? '',
		'name'      => $_POST['name'] ?? '',
		'phone'     => $_POST['phone'] ?? '',
		'notes'     => $_POST['notes'] ?? '',
	) );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}
	$appt = cbp_get_appointment( $result );
	wp_send_json_success( array(
		'appt_id'   => $appt['appt_id'],
		'doctor'    => get_the_title( $appt['doctor_id'] ),
		'date'      => date_i18n( get_option( 'date_format' ), strtotime( $appt['date'] ) ),
		'slot'      => date_i18n( 'g:i A', strtotime( $appt['slot'] ) ),
		'whatsapp'  => cbp_whatsapp_link( $appt ),
	) );
}
add_action( 'wp_ajax_cbp_book', 'cbp_ajax_book' );
add_action( 'wp_ajax_nopriv_cbp_book', 'cbp_ajax_book' );

/* ---------- AJAX: doctor confirm/cancel ---------- */
function cbp_ajax_appt_status() {
	check_ajax_referer( 'cbp_nonce', 'nonce' );
	if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => __( 'Not allowed.', 'clinicbook-pro' ) ) );
	}
	$post_id = absint( $_POST['appt_id'] ?? 0 );
	$status  = sanitize_key( $_POST['status'] ?? '' );
	if ( get_post_type( $post_id ) !== 'appointment' || ! in_array( $status, array( 'confirmed', 'cancelled' ), true ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid request.', 'clinicbook-pro' ) ) );
	}
	update_post_meta( $post_id, '_cbp_status', $status );
	wp_send_json_success( array( 'status' => $status ) );
}
add_action( 'wp_ajax_cbp_appt_status', 'cbp_ajax_appt_status' );
