<?php
/**
 * ClinicBook Pro — theme setup, post types, meta boxes, assets.
 *
 * @package ClinicBook_Pro
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'CBP_VERSION', '1.0.0' );
define( 'CBP_DIR', get_template_directory() );
define( 'CBP_URI', get_template_directory_uri() );

require_once CBP_DIR . '/inc/booking.php';
require_once CBP_DIR . '/inc/shortcodes.php';
require_once CBP_DIR . '/inc/customizer.php';
require_once CBP_DIR . '/inc/pharmacy.php';

/* ---------- text domain ---------- */
function cbp_load_textdomain() {
	load_theme_textdomain( 'clinicbook-pro', CBP_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'cbp_load_textdomain' );

/* ---------- theme setup ---------- */
function cbp_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'clinicbook-pro' ),
		'footer'  => __( 'Footer Menu', 'clinicbook-pro' ),
	) );
	add_image_size( 'cbp-doctor', 480, 520, true );
	add_image_size( 'cbp-thumb', 400, 260, true );
}
add_action( 'after_setup_theme', 'cbp_setup' );

/* ---------- assets ---------- */
function cbp_assets() {
	wp_enqueue_style( 'cbp-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap', array(), null );
	wp_enqueue_style( 'cbp-style', get_stylesheet_uri(), array( 'cbp-fonts' ), CBP_VERSION );
	wp_enqueue_script( 'cbp-main', CBP_URI . '/assets/js/main.js', array(), CBP_VERSION, true );
	wp_localize_script( 'cbp-main', 'CBP_DATA', array(
		'ajax'   => admin_url( 'admin-ajax.php' ),
		'nonce'  => wp_create_nonce( 'cbp_nonce' ),
		'i18n'   => array(
			'select_slot'  => __( 'Please select a time slot.', 'clinicbook-pro' ),
			'loading'      => __( 'Loading…', 'clinicbook-pro' ),
			'no_slots'     => __( 'No slots available on this date. Please try another day.', 'clinicbook-pro' ),
			'error'        => __( 'Something went wrong. Please try again.', 'clinicbook-pro' ),
		),
	) );
}
add_action( 'wp_enqueue_scripts', 'cbp_assets' );

/* ---------- Doctors CPT ---------- */
function cbp_register_doctor_cpt() {
	$labels = array(
		'name'          => __( 'Doctors', 'clinicbook-pro' ),
		'singular_name' => __( 'Doctor', 'clinicbook-pro' ),
		'add_new_item'  => __( 'Add New Doctor', 'clinicbook-pro' ),
		'edit_item'     => __( 'Edit Doctor', 'clinicbook-pro' ),
	);
	register_post_type( 'doctor', array(
		'labels'       => $labels,
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => array( 'slug' => 'doctors' ),
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-businessperson',
	) );

	register_taxonomy( 'specialization', 'doctor', array(
		'labels' => array(
			'name'          => __( 'Specializations', 'clinicbook-pro' ),
			'singular_name' => __( 'Specialization', 'clinicbook-pro' ),
		),
		'hierarchical'      => true,
		'public'            => true,
		'rewrite'           => array( 'slug' => 'speciality' ),
		'show_admin_column' => true,
	) );
}
add_action( 'init', 'cbp_register_doctor_cpt' );

/* ---------- Appointments CPT (private) ---------- */
function cbp_register_appointment_cpt() {
	register_post_type( 'appointment', array(
		'labels' => array(
			'name'          => __( 'Appointments', 'clinicbook-pro' ),
			'singular_name' => __( 'Appointment', 'clinicbook-pro' ),
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => 'edit.php?post_type=doctor',
		'supports'     => array( 'title' ),
		'menu_icon'    => 'dashicons-calendar-alt',
		'capabilities' => array(
			'create_posts' => 'do_not_allow',
		),
		'map_meta_cap' => true,
	) );
}
add_action( 'init', 'cbp_register_appointment_cpt' );

/* ---------- Doctor meta boxes ---------- */
function cbp_doctor_meta_boxes() {
	add_meta_box( 'cbp_doctor_details', __( 'Doctor Details', 'clinicbook-pro' ), 'cbp_doctor_meta_html', 'doctor', 'normal', 'high' );
	add_meta_box( 'cbp_doctor_schedule', __( 'Weekly Schedule', 'clinicbook-pro' ), 'cbp_doctor_schedule_html', 'doctor', 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'cbp_doctor_meta_boxes' );

function cbp_doctor_meta_html( $post ) {
	wp_nonce_field( 'cbp_doctor_meta', 'cbp_doctor_meta_nonce' );
	$get = function( $k, $d = '' ) use ( $post ) { return get_post_meta( $post->ID, '_cbp_' . $k, true ) ?: $d; };
	?>
	<table class="form-table">
		<tr><th><label for="cbp_fees"><?php esc_html_e( 'Consultation Fees (₹)', 'clinicbook-pro' ); ?></label></th>
			<td><input type="number" id="cbp_fees" name="cbp_fees" value="<?php echo esc_attr( $get( 'fees', '500' ) ); ?>" min="0" step="1" /></td></tr>
		<tr><th><label for="cbp_exp"><?php esc_html_e( 'Experience (years)', 'clinicbook-pro' ); ?></label></th>
			<td><input type="number" id="cbp_exp" name="cbp_exp" value="<?php echo esc_attr( $get( 'experience', '5' ) ); ?>" min="0" /></td></tr>
		<tr><th><label for="cbp_cred"><?php esc_html_e( 'Credentials (e.g. MBBS, MD)', 'clinicbook-pro' ); ?></label></th>
			<td><input type="text" id="cbp_cred" name="cbp_credentials" value="<?php echo esc_attr( $get( 'credentials' ) ); ?>" class="regular-text" /></td></tr>
		<tr><th><label for="cbp_clinic"><?php esc_html_e( 'Clinic Name', 'clinicbook-pro' ); ?></label></th>
			<td><input type="text" id="cbp_clinic" name="cbp_clinic" value="<?php echo esc_attr( $get( 'clinic' ) ); ?>" class="regular-text" /></td></tr>
		<tr><th><label for="cbp_loc"><?php esc_html_e( 'Location / City', 'clinicbook-pro' ); ?></label></th>
			<td><input type="text" id="cbp_loc" name="cbp_location" value="<?php echo esc_attr( $get( 'location' ) ); ?>" class="regular-text" /></td></tr>
		<tr><th><label for="cbp_phone"><?php esc_html_e( 'Phone', 'clinicbook-pro' ); ?></label></th>
			<td><input type="text" id="cbp_phone" name="cbp_phone" value="<?php echo esc_attr( $get( 'phone' ) ); ?>" class="regular-text" /></td></tr>
		<tr><th><label for="cbp_lang"><?php esc_html_e( 'Languages (comma separated)', 'clinicbook-pro' ); ?></label></th>
			<td><input type="text" id="cbp_lang" name="cbp_languages" value="<?php echo esc_attr( $get( 'languages', 'Hindi, English' ) ); ?>" class="regular-text" /></td></tr>
		<tr><th><label for="cbp_edu"><?php esc_html_e( 'Education', 'clinicbook-pro' ); ?></label></th>
			<td><textarea id="cbp_edu" name="cbp_education" rows="3" class="large-text"><?php echo esc_textarea( $get( 'education' ) ); ?></textarea></td></tr>
		<tr><th><label for="cbp_services"><?php esc_html_e( 'Services (one per line)', 'clinicbook-pro' ); ?></label></th>
			<td><textarea id="cbp_services" name="cbp_services" rows="4" class="large-text"><?php echo esc_textarea( $get( 'services' ) ); ?></textarea></td></tr>
		<tr><th><?php esc_html_e( 'Consultation Types', 'clinicbook-pro' ); ?></th>
			<td>
				<label><input type="checkbox" name="cbp_consult_types[]" value="in-person" <?php checked( in_array( 'in-person', (array) $get( 'consult_types', array( 'in-person' ) ), true ) ); ?> /> <?php esc_html_e( 'In-Person', 'clinicbook-pro' ); ?></label>&nbsp;&nbsp;
				<label><input type="checkbox" name="cbp_consult_types[]" value="video" <?php checked( in_array( 'video', (array) $get( 'consult_types', array() ), true ) ); ?> /> <?php esc_html_e( 'Video', 'clinicbook-pro' ); ?></label>
			</td></tr>
		<tr><th><label for="cbp_rating"><?php esc_html_e( 'Rating (0–5)', 'clinicbook-pro' ); ?></label></th>
			<td><input type="number" id="cbp_rating" name="cbp_rating" value="<?php echo esc_attr( $get( 'rating', '4.5' ) ); ?>" min="0" max="5" step="0.1" /></td></tr>
		<tr><th><label for="cbp_patients"><?php esc_html_e( 'Patients Treated', 'clinicbook-pro' ); ?></label></th>
			<td><input type="number" id="cbp_patients" name="cbp_patients" value="<?php echo esc_attr( $get( 'patients', '100' ) ); ?>" min="0" /></td></tr>
		<tr><th><label for="cbp_featured"><?php esc_html_e( 'Featured on Homepage', 'clinicbook-pro' ); ?></label></th>
			<td><label><input type="checkbox" id="cbp_featured" name="cbp_featured" value="1" <?php checked( $get( 'featured' ), '1' ); ?> /> <?php esc_html_e( 'Show in Featured Doctors', 'clinicbook-pro' ); ?></label></td></tr>
	</table>
	<?php
}

function cbp_doctor_schedule_html( $post ) {
	wp_nonce_field( 'cbp_doctor_meta', 'cbp_doctor_meta_nonce' );
	$schedule = get_post_meta( $post->ID, '_cbp_schedule', true );
	if ( ! is_array( $schedule ) ) { $schedule = array(); }
	$days = array( 'mon' => __( 'Monday', 'clinicbook-pro' ), 'tue' => __( 'Tuesday', 'clinicbook-pro' ), 'wed' => __( 'Wednesday', 'clinicbook-pro' ), 'thu' => __( 'Thursday', 'clinicbook-pro' ), 'fri' => __( 'Friday', 'clinicbook-pro' ), 'sat' => __( 'Saturday', 'clinicbook-pro' ), 'sun' => __( 'Sunday', 'clinicbook-pro' ) );
	?>
	<p class="description"><?php esc_html_e( 'Set available time ranges per day (24h format, e.g. 10:00–14:00). Leave blank for a day off. Slot duration in minutes.', 'clinicbook-pro' ); ?></p>
	<table class="widefat striped">
		<thead><tr><th><?php esc_html_e( 'Day', 'clinicbook-pro' ); ?></th><th><?php esc_html_e( 'Morning (from–to)', 'clinicbook-pro' ); ?></th><th><?php esc_html_e( 'Evening (from–to)', 'clinicbook-pro' ); ?></th><th><?php esc_html_e( 'Slot (min)', 'clinicbook-pro' ); ?></th></tr></thead>
		<tbody>
		<?php foreach ( $days as $key => $label ) :
			$d = isset( $schedule[ $key ] ) ? $schedule[ $key ] : array( 'm_from' => '10:00', 'm_to' => '13:00', 'e_from' => '17:00', 'e_to' => '20:00', 'slot' => 15 );
		?>
			<tr>
				<td><strong><?php echo esc_html( $label ); ?></strong></td>
				<td><input type="time" name="cbp_sched[<?php echo esc_attr( $key ); ?>][m_from]" value="<?php echo esc_attr( $d['m_from'] ); ?>" /> –
					<input type="time" name="cbp_sched[<?php echo esc_attr( $key ); ?>][m_to]" value="<?php echo esc_attr( $d['m_to'] ); ?>" /></td>
				<td><input type="time" name="cbp_sched[<?php echo esc_attr( $key ); ?>][e_from]" value="<?php echo esc_attr( $d['e_from'] ); ?>" /> –
					<input type="time" name="cbp_sched[<?php echo esc_attr( $key ); ?>][e_to]" value="<?php echo esc_attr( $d['e_to'] ); ?>" /></td>
				<td><input type="number" name="cbp_sched[<?php echo esc_attr( $key ); ?>][slot]" value="<?php echo esc_attr( $d['slot'] ); ?>" min="5" max="120" step="5" style="width:70px" /></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}

function cbp_save_doctor_meta( $post_id ) {
	if ( ! isset( $_POST['cbp_doctor_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['cbp_doctor_meta_nonce'] ), 'cbp_doctor_meta' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

	$text_fields = array( 'fees', 'experience', 'credentials', 'clinic', 'location', 'phone', 'languages', 'education', 'services', 'rating', 'patients' );
	foreach ( $text_fields as $f ) {
		if ( isset( $_POST[ 'cbp_' . $f ] ) ) {
			update_post_meta( $post_id, '_cbp_' . $f, sanitize_text_field( wp_unslash( $_POST[ 'cbp_' . $f ] ) ) );
		}
	}
	$types = isset( $_POST['cbp_consult_types'] ) ? array_map( 'sanitize_text_field', wp_unslash( (array) $_POST['cbp_consult_types'] ) ) : array();
	update_post_meta( $post_id, '_cbp_consult_types', $types );
	update_post_meta( $post_id, '_cbp_featured', isset( $_POST['cbp_featured'] ) ? '1' : '0' );

	if ( isset( $_POST['cbp_sched'] ) && is_array( $_POST['cbp_sched'] ) ) {
		$sched = array();
		foreach ( wp_unslash( $_POST['cbp_sched'] ) as $day => $row ) {
			$day = sanitize_key( $day );
			$sched[ $day ] = array(
				'm_from' => sanitize_text_field( $row['m_from'] ?? '' ),
				'm_to'   => sanitize_text_field( $row['m_to'] ?? '' ),
				'e_from' => sanitize_text_field( $row['e_from'] ?? '' ),
				'e_to'   => sanitize_text_field( $row['e_to'] ?? '' ),
				'slot'   => absint( $row['slot'] ?? 15 ),
			);
		}
		update_post_meta( $post_id, '_cbp_schedule', $sched );
	}
}
add_action( 'save_post_doctor', 'cbp_save_doctor_meta' );

/* ---------- flush rewrites on activation ---------- */
function cbp_activate() {
	cbp_register_doctor_cpt();
	cbp_register_appointment_cpt();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'cbp_activate' );

/* ---------- helpers ---------- */
function cbp_doc_meta( $post_id, $key, $default = '' ) {
	$v = get_post_meta( $post_id, '_cbp_' . $key, true );
	return ( $v === '' || $v === false ) ? $default : $v;
}

function cbp_doctor_specializations( $post_id ) {
	$terms = get_the_terms( $post_id, 'specialization' );
	if ( ! $terms || is_wp_error( $terms ) ) { return array(); }
	return wp_list_pluck( $terms, 'name' );
}

function cbp_format_inr( $amount ) {
	return '₹' . number_format_i18n( (float) $amount );
}

function cbp_stars( $rating ) {
	$rating = max( 0, min( 5, (float) $rating ) );
	$full   = (int) floor( $rating );
	$half   = ( $rating - $full ) >= 0.5 ? 1 : 0;
	$out    = '<span class="stars" aria-label="' . esc_attr( sprintf( __( 'Rated %s out of 5', 'clinicbook-pro' ), $rating ) ) . '">';
	for ( $i = 0; $i < $full; $i++ ) { $out .= '★'; }
	if ( $half ) { $out .= '★'; }
	for ( $i = $full + $half; $i < 5; $i++ ) { $out .= '☆'; }
	$out .= '</span>';
	return $out;
}

function cbp_is_available_today( $post_id ) {
	$day_key = strtolower( date( 'D' ) );
	$map     = array( 'mon' => 'mon', 'tue' => 'tue', 'wed' => 'wed', 'thu' => 'thu', 'fri' => 'fri', 'sat' => 'sat', 'sun' => 'sun' );
	$sched   = get_post_meta( $post_id, '_cbp_schedule', true );
	if ( ! is_array( $sched ) || empty( $sched[ $map[ $day_key ] ] ) ) { return false; }
	$d = $sched[ $map[ $day_key ] ];
	return ! empty( $d['m_from'] ) || ! empty( $d['e_from'] );
}

function cbp_booking_url( $doctor_id = 0 ) {
	$url = home_url( '/book-appointment/' );
	if ( $doctor_id ) { $url = add_query_arg( 'doctor', absint( $doctor_id ), $url ); }
	return $url;
}
