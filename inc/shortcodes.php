<?php
/**
 * ClinicBook Pro — shortcodes.
 *
 * @package ClinicBook_Pro
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ---------- shared doctor card ---------- */
function cbp_doctor_card_html( $post_id ) {
	$specs     = cbp_doctor_specializations( $post_id );
	$spec      = $specs ? $specs[0] : __( 'General', 'clinicbook-pro' );
	$available = cbp_is_available_today( $post_id );
	$fees      = cbp_doc_meta( $post_id, 'fees', '500' );
	$rating    = (float) cbp_doc_meta( $post_id, 'rating', '4.5' );
	$photo     = get_the_post_thumbnail( $post_id, 'cbp-doctor', array( 'alt' => get_the_title( $post_id ) ) );
	if ( ! $photo ) {
		$photo = '<div class="doc-photo" style="display:flex;align-items:center;justify-content:center;font-size:64px;background:#dfe9f5">🩺</div>';
	} else {
		$photo = '<div class="doc-photo">' . $photo . '</div>';
	}
	ob_start();
	?>
	<div class="doc-card">
		<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span class="rating-badge">★ <?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></span>
		<div class="doc-body">
			<div class="doc-spec-row">
				<span class="doc-spec"><?php echo esc_html( $spec ); ?></span>
				<span class="avail <?php echo $available ? 'yes' : 'no'; ?>">● <?php echo $available ? esc_html__( 'Available', 'clinicbook-pro' ) : esc_html__( 'On Leave', 'clinicbook-pro' ); ?></span>
			</div>
			<h3><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
			<p class="doc-meta"><?php echo esc_html( cbp_doc_meta( $post_id, 'credentials' ) ); ?> · <?php echo esc_html( cbp_doc_meta( $post_id, 'experience', '5' ) ); ?> <?php esc_html_e( 'yrs exp.', 'clinicbook-pro' ); ?></p>
			<p class="doc-meta">📍 <?php echo esc_html( cbp_doc_meta( $post_id, 'clinic' ) ); ?></p>
			<div class="doc-fees">
				<div><b><?php echo esc_html( cbp_format_inr( $fees ) ); ?></b><small><?php esc_html_e( 'Consultation', 'clinicbook-pro' ); ?></small></div>
				<a class="btn btn-primary btn-sm" href="<?php echo esc_url( cbp_booking_url( $post_id ) ); ?>"><?php esc_html_e( 'Book', 'clinicbook-pro' ); ?></a>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/* ---------- [clinicbook_doctors count="4" specialization="" ] ---------- */
function cbp_sc_doctors( $atts ) {
	$atts = shortcode_atts( array( 'count' => 4, 'specialization' => '', 'featured' => '' ), $atts, 'clinicbook_doctors' );
	$args = array(
		'post_type'      => 'doctor',
		'posts_per_page' => absint( $atts['count'] ),
		'post_status'    => 'publish',
	);
	if ( $atts['specialization'] !== '' ) {
		$args['tax_query'] = array( array(
			'taxonomy' => 'specialization',
			'field'    => 'slug',
			'terms'    => sanitize_title( $atts['specialization'] ),
		) );
	}
	if ( $atts['featured'] !== '' ) {
		$args['meta_query'] = array( array( 'key' => '_cbp_featured', 'value' => '1' ) );
	}
	$q = new WP_Query( $args );
	if ( ! $q->have_posts() ) {
		return '<p>' . esc_html__( 'No doctors found.', 'clinicbook-pro' ) . '</p>';
	}
	$out = '<div class="doc-grid">';
	while ( $q->have_posts() ) { $q->the_post(); $out .= cbp_doctor_card_html( get_the_ID() ); }
	$out .= '</div>';
	wp_reset_postdata();
	return $out;
}
add_shortcode( 'clinicbook_doctors', 'cbp_sc_doctors' );

/* ---------- [clinicbook_my_appointments] — phone lookup ---------- */
function cbp_sc_my_appointments() {
	ob_start();
	$phone   = isset( $_GET['phone'] ) ? preg_replace( '/\D/', '', wp_unslash( $_GET['phone'] ) ) : '';
	$results = array();
	if ( $phone !== '' && isset( $_GET['cbp_lookup'] ) && wp_verify_nonce( sanitize_key( $_GET['cbp_lookup'] ), 'cbp_lookup' ) ) {
		$q = new WP_Query( array(
			'post_type'      => 'appointment',
			'posts_per_page' => 20,
			'orderby'        => 'meta_value',
			'meta_key'       => '_cbp_date',
			'order'          => 'DESC',
			'meta_query'     => array( array( 'key' => '_cbp_patient_phone', 'value' => $phone, 'compare' => '=' ) ),
		) );
		while ( $q->have_posts() ) { $q->the_post(); $results[] = cbp_get_appointment( get_the_ID() ); }
		wp_reset_postdata();
	}
	?>
	<div class="cbp-lookup">
		<form method="get" class="search-card" style="margin:0 0 24px">
			<?php wp_nonce_field( 'cbp_lookup', 'cbp_lookup', false ); ?>
			<div class="search-row" style="grid-template-columns:1fr auto">
				<input type="tel" name="phone" required pattern="[0-9+ ]{10,15}" placeholder="<?php esc_attr_e( 'Enter your 10-digit mobile number', 'clinicbook-pro' ); ?>" value="<?php echo esc_attr( $phone ); ?>" />
				<button class="btn btn-primary" type="submit"><?php esc_html_e( 'My Appointments', 'clinicbook-pro' ); ?></button>
			</div>
		</form>
		<?php if ( $phone !== '' ) : ?>
			<?php if ( $results ) : ?>
				<table class="cbp-table">
					<thead><tr>
						<th><?php esc_html_e( 'ID', 'clinicbook-pro' ); ?></th>
						<th><?php esc_html_e( 'Doctor', 'clinicbook-pro' ); ?></th>
						<th><?php esc_html_e( 'Date', 'clinicbook-pro' ); ?></th>
						<th><?php esc_html_e( 'Time', 'clinicbook-pro' ); ?></th>
						<th><?php esc_html_e( 'Status', 'clinicbook-pro' ); ?></th>
					</tr></thead>
					<tbody>
					<?php foreach ( $results as $a ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $a['appt_id'] ); ?></strong></td>
							<td><?php echo esc_html( get_the_title( $a['doctor_id'] ) ); ?></td>
							<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $a['date'] ) ) ); ?></td>
							<td><?php echo esc_html( date_i18n( 'g:i A', strtotime( $a['slot'] ) ) ); ?></td>
							<td><span class="status <?php echo esc_attr( $a['status'] ); ?>"><?php echo esc_html( ucfirst( $a['status'] ) ); ?></span></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<p class="notice error"><?php esc_html_e( 'No appointments found for this number.', 'clinicbook-pro' ); ?></p>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'clinicbook_my_appointments', 'cbp_sc_my_appointments' );

/* ---------- [clinicbook_doctor_dashboard] ---------- */
function cbp_sc_doctor_dashboard() {
	if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
		return '<p class="notice error">' . esc_html__( 'Please log in with a doctor account to view the dashboard.', 'clinicbook-pro' ) . '</p>';
	}
	$today = date( 'Y-m-d' );
	$q = new WP_Query( array(
		'post_type'      => 'appointment',
		'posts_per_page' => 50,
		'meta_query'     => array( array( 'key' => '_cbp_date', 'value' => $today, 'compare' => '>=' ) ),
		'orderby'        => 'meta_value',
		'meta_key'       => '_cbp_date',
		'order'          => 'ASC',
	) );
	ob_start();
	?>
	<div class="cbp-dashboard">
		<h3><?php esc_html_e( "Today's Appointments", 'clinicbook-pro' ); ?></h3>
		<?php if ( $q->have_posts() ) : ?>
			<table class="cbp-table" id="cbpDashTable">
				<thead><tr>
					<th><?php esc_html_e( 'ID', 'clinicbook-pro' ); ?></th>
					<th><?php esc_html_e( 'Patient', 'clinicbook-pro' ); ?></th>
					<th><?php esc_html_e( 'Doctor', 'clinicbook-pro' ); ?></th>
					<th><?php esc_html_e( 'Date', 'clinicbook-pro' ); ?></th>
					<th><?php esc_html_e( 'Time', 'clinicbook-pro' ); ?></th>
					<th><?php esc_html_e( 'Status', 'clinicbook-pro' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'clinicbook-pro' ); ?></th>
				</tr></thead>
				<tbody>
				<?php while ( $q->have_posts() ) : $q->the_post(); $a = cbp_get_appointment( get_the_ID() ); ?>
					<tr data-appt="<?php echo esc_attr( get_the_ID() ); ?>">
						<td><strong><?php echo esc_html( $a['appt_id'] ); ?></strong></td>
						<td><?php echo esc_html( $a['patient_name'] ); ?><br><small><?php echo esc_html( $a['patient_phone'] ); ?></small></td>
						<td><?php echo esc_html( get_the_title( $a['doctor_id'] ) ); ?></td>
						<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $a['date'] ) ) ); ?></td>
						<td><?php echo esc_html( date_i18n( 'g:i A', strtotime( $a['slot'] ) ) ); ?></td>
						<td><span class="status <?php echo esc_attr( $a['status'] ); ?>"><?php echo esc_html( ucfirst( $a['status'] ) ); ?></span></td>
						<td>
							<?php if ( $a['status'] === 'pending' ) : ?>
								<button class="btn btn-primary btn-sm cbp-appt-act" data-act="confirmed"><?php esc_html_e( 'Confirm', 'clinicbook-pro' ); ?></button>
								<button class="btn btn-outline btn-sm cbp-appt-act" data-act="cancelled"><?php esc_html_e( 'Cancel', 'clinicbook-pro' ); ?></button>
							<?php else : ?>—<?php endif; ?>
						</td>
					</tr>
				<?php endwhile; wp_reset_postdata(); ?>
				</tbody>
			</table>
		<?php else : ?>
			<p><?php esc_html_e( 'No upcoming appointments.', 'clinicbook-pro' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'clinicbook_doctor_dashboard', 'cbp_sc_doctor_dashboard' );
