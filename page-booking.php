<?php
/**
 * Template Name: Booking
 * ClinicBook Pro — 4-step appointment booking wizard.
 *
 * @package ClinicBook_Pro
 */
get_header();

$doctor_id = isset( $_GET['doctor'] ) ? absint( $_GET['doctor'] ) : 0;
if ( ! $doctor_id || get_post_type( $doctor_id ) !== 'doctor' ) {
	// Let the visitor pick a doctor first.
	$doctors = get_posts( array( 'post_type' => 'doctor', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ) );
	?>
	<div class="page-hero"><div class="container"><h1><?php esc_html_e( 'Book Appointment', 'clinicbook-pro' ); ?></h1></div></div>
	<section class="section"><div class="container" style="max-width:640px">
		<div class="post-card">
			<h3><?php esc_html_e( 'Choose a Doctor', 'clinicbook-pro' ); ?></h3>
			<?php if ( $doctors ) : ?>
				<div class="field">
					<select id="cbpPickDoctor">
						<option value=""><?php esc_html_e( '— Select —', 'clinicbook-pro' ); ?></option>
						<?php foreach ( $doctors as $d ) : ?>
							<option value="<?php echo esc_attr( $d->ID ); ?>"><?php echo esc_html( get_the_title( $d ) ); ?> (<?php echo esc_html( cbp_format_inr( cbp_doc_meta( $d->ID, 'fees', '500' ) ) ); ?>)</option>
						<?php endforeach; ?>
					</select>
				</div>
				<button class="btn btn-primary" id="cbpGoDoctor"><?php esc_html_e( 'Continue', 'clinicbook-pro' ); ?></button>
			<?php else : ?>
				<p><?php esc_html_e( 'No doctors available yet.', 'clinicbook-pro' ); ?></p>
			<?php endif; ?>
		</div>
	</div></section>
	<?php
	get_footer();
	return;
}

$specs     = cbp_doctor_specializations( $doctor_id );
$fees      = cbp_doc_meta( $doctor_id, 'fees', '500' );
$types     = (array) cbp_doc_meta( $doctor_id, 'consult_types', array( 'in-person' ) );
$dates     = array();
for ( $i = 0; $i < 14; $i++ ) {
	$ts      = strtotime( "+$i days" );
	$dates[] = array( 'y' => date( 'Y-m-d', $ts ), 'd' => date_i18n( 'd', $ts ), 'm' => date_i18n( 'M', $ts ), 'w' => date_i18n( 'D', $ts ) );
}
?>

<div class="page-hero"><div class="container"><h1><?php esc_html_e( 'Book Appointment', 'clinicbook-pro' ); ?></h1></div></div>

<section class="section">
	<div class="container">
		<div class="wizard" id="cbpWizard" data-doctor="<?php echo esc_attr( $doctor_id ); ?>">
			<div class="wizard-steps">
				<div class="wstep active" data-step="1"><span class="n">1</span><?php esc_html_e( 'Doctor', 'clinicbook-pro' ); ?></div>
				<div class="wstep" data-step="2"><span class="n">2</span><?php esc_html_e( 'Date & Time', 'clinicbook-pro' ); ?></div>
				<div class="wstep" data-step="3"><span class="n">3</span><?php esc_html_e( 'Your Details', 'clinicbook-pro' ); ?></div>
				<div class="wstep" data-step="4"><span class="n">4</span><?php esc_html_e( 'Confirmed', 'clinicbook-pro' ); ?></div>
			</div>
			<div class="wizard-body">

				<!-- STEP 1 -->
				<div class="wpanel active" data-panel="1">
					<div class="appt-summary">
						<?php echo get_the_post_thumbnail( $doctor_id, 'thumbnail' ); ?>
						<div>
							<h3 style="margin:0"><?php echo esc_html( get_the_title( $doctor_id ) ); ?></h3>
							<p class="doc-meta"><?php echo esc_html( $specs ? $specs[0] : '' ); ?> · <?php echo esc_html( cbp_format_inr( $fees ) ); ?></p>
							<p class="doc-meta">🏥 <?php echo esc_html( cbp_doc_meta( $doctor_id, 'clinic' ) ); ?></p>
						</div>
					</div>
					<div class="field">
						<label><?php esc_html_e( 'Consultation Type', 'clinicbook-pro' ); ?></label>
						<select id="cbpCtype">
							<?php if ( in_array( 'in-person', $types, true ) ) : ?><option value="in-person"><?php esc_html_e( 'In-Person at Clinic', 'clinicbook-pro' ); ?></option><?php endif; ?>
							<?php if ( in_array( 'video', $types, true ) ) : ?><option value="video"><?php esc_html_e( 'Video Consultation', 'clinicbook-pro' ); ?></option><?php endif; ?>
						</select>
					</div>
					<button class="btn btn-primary cbp-next" data-goto="2"><?php esc_html_e( 'Continue', 'clinicbook-pro' ); ?></button>
				</div>

				<!-- STEP 2 -->
				<div class="wpanel" data-panel="2">
					<h3><?php esc_html_e( 'Select Date', 'clinicbook-pro' ); ?></h3>
					<div class="date-row" id="cbpDates">
						<?php foreach ( $dates as $dt ) : ?>
							<button type="button" class="date-chip" data-date="<?php echo esc_attr( $dt['y'] ); ?>">
								<b><?php echo esc_html( $dt['d'] ); ?></b><span><?php echo esc_html( $dt['m'] . ' · ' . $dt['w'] ); ?></span>
							</button>
						<?php endforeach; ?>
					</div>
					<h3 style="margin-top:22px"><?php esc_html_e( 'Available Slots', 'clinicbook-pro' ); ?></h3>
					<div class="slot-grid" id="cbpSlots"><p style="color:var(--cbp-muted)"><?php esc_html_e( 'Pick a date to see slots.', 'clinicbook-pro' ); ?></p></div>
					<div style="margin-top:22px;display:flex;gap:12px">
						<button class="btn btn-outline cbp-next" data-goto="1"><?php esc_html_e( 'Back', 'clinicbook-pro' ); ?></button>
						<button class="btn btn-primary cbp-next" data-goto="3" id="cbpToStep3"><?php esc_html_e( 'Continue', 'clinicbook-pro' ); ?></button>
					</div>
				</div>

				<!-- STEP 3 -->
				<div class="wpanel" data-panel="3">
					<h3><?php esc_html_e( 'Your Details', 'clinicbook-pro' ); ?></h3>
					<div class="notice error" id="cbpBookError" style="display:none"></div>
					<div class="field">
						<label for="cbpName"><?php esc_html_e( 'Full Name *', 'clinicbook-pro' ); ?></label>
						<input type="text" id="cbpName" autocomplete="name" placeholder="<?php esc_attr_e( 'e.g. Rahul Sharma', 'clinicbook-pro' ); ?>" />
					</div>
					<div class="field">
						<label for="cbpPhone"><?php esc_html_e( 'Mobile Number *', 'clinicbook-pro' ); ?></label>
						<input type="tel" id="cbpPhone" autocomplete="tel" pattern="[0-9+ ]{10,15}" placeholder="<?php esc_attr_e( '10-digit mobile number', 'clinicbook-pro' ); ?>" />
					</div>
					<div class="field">
						<label for="cbpNotes"><?php esc_html_e( 'Notes (optional)', 'clinicbook-pro' ); ?></label>
						<textarea id="cbpNotes" rows="3" placeholder="<?php esc_attr_e( 'Symptoms, questions…', 'clinicbook-pro' ); ?>"></textarea>
					</div>
					<div style="display:flex;gap:12px">
						<button class="btn btn-outline cbp-next" data-goto="2"><?php esc_html_e( 'Back', 'clinicbook-pro' ); ?></button>
						<button class="btn btn-primary" id="cbpConfirmBook"><?php esc_html_e( 'Confirm Appointment', 'clinicbook-pro' ); ?></button>
					</div>
				</div>

				<!-- STEP 4 -->
				<div class="wpanel" data-panel="4">
					<div class="confirm-box">
						<div style="font-size:56px">✅</div>
						<h3><?php esc_html_e( 'Appointment Requested!', 'clinicbook-pro' ); ?></h3>
						<p style="color:var(--cbp-muted)"><?php esc_html_e( 'The clinic will confirm shortly. Save your appointment ID:', 'clinicbook-pro' ); ?></p>
						<div class="confirm-id" id="cbpApptId">—</div>
						<p id="cbpApptMeta" style="font-weight:600;color:var(--cbp-navy)"></p>
						<a class="btn wa-btn" id="cbpWaBtn" href="#" target="_blank" rel="noopener">💬 <?php esc_html_e( 'Confirm on WhatsApp', 'clinicbook-pro' ); ?></a>
						<div style="margin-top:18px"><a class="btn btn-outline btn-sm" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'clinicbook-pro' ); ?></a></div>
					</div>
				</div>

			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
