<?php
/**
 * ClinicBook Pro — single doctor profile.
 *
 * @package ClinicBook_Pro
 */
get_header();

while ( have_posts() ) : the_post();
	$pid       = get_the_ID();
	$specs     = cbp_doctor_specializations( $pid );
	$spec_name = $specs ? implode( ', ', $specs ) : __( 'General', 'clinicbook-pro' );
	$rating    = (float) cbp_doc_meta( $pid, 'rating', '4.5' );
	$fees      = cbp_doc_meta( $pid, 'fees', '500' );
	$types     = (array) cbp_doc_meta( $pid, 'consult_types', array( 'in-person' ) );
	$services  = array_filter( array_map( 'trim', explode( "\n", cbp_doc_meta( $pid, 'services' ) ) ) );
	$tags      = array_filter( array_map( 'trim', explode( ',', cbp_doc_meta( $pid, 'languages' ) . ',' . $spec_name ) ) );
	?>

	<section class="section" style="padding-top:44px">
		<div class="container">
			<div class="profile-card">
				<div class="profile-photo">
					<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'cbp-doctor' ); endif; ?>
				</div>
				<div class="profile-main">
					<h1><?php the_title(); ?> <span class="verified" title="<?php esc_attr_e( 'Verified', 'clinicbook-pro' ); ?>">✔</span></h1>
					<p style="color:var(--cbp-muted);margin:4px 0"><?php echo esc_html( cbp_doc_meta( $pid, 'credentials' ) ); ?></p>
					<?php echo cbp_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div style="margin:10px 0">
						<span class="tag"><?php echo esc_html( $spec_name ); ?></span>
						<?php if ( in_array( 'video', $types, true ) ) : ?><span class="tag">📹 <?php esc_html_e( 'Video Consult', 'clinicbook-pro' ); ?></span><?php endif; ?>
						<?php if ( in_array( 'in-person', $types, true ) ) : ?><span class="tag">🏥 <?php esc_html_e( 'In-Person', 'clinicbook-pro' ); ?></span><?php endif; ?>
					</div>
					<p class="doc-meta">🏥 <?php echo esc_html( cbp_doc_meta( $pid, 'clinic' ) ); ?></p>
					<p class="doc-meta">📍 <?php echo esc_html( cbp_doc_meta( $pid, 'location' ) ); ?></p>
					<div style="margin:12px 0">
						<?php foreach ( array_slice( $tags, 0, 6 ) as $tg ) : ?>
							<span class="tag"><?php echo esc_html( $tg ); ?></span>
						<?php endforeach; ?>
					</div>
					<a class="btn btn-outline btn-sm" href="#reviews"><?php esc_html_e( 'Add Feedback', 'clinicbook-pro' ); ?></a>
				</div>
				<div class="profile-side">
					<div class="stat-row">👍 <b><?php echo esc_html( number_format_i18n( (int) cbp_doc_meta( $pid, 'patients', 0 ) / 40 + 1 ) ); ?></b>&nbsp;<?php esc_html_e( 'Votes', 'clinicbook-pro' ); ?></div>
					<div class="stat-row">💬 <b><?php echo esc_html( number_format_i18n( get_comments_number() ) ); ?></b>&nbsp;<?php esc_html_e( 'Feedback', 'clinicbook-pro' ); ?></div>
					<div class="stat-row">🧑‍⚕️ <b><?php echo esc_html( number_format_i18n( (int) cbp_doc_meta( $pid, 'patients', '100' ) ) ); ?></b>&nbsp;<?php esc_html_e( 'Patients Treated', 'clinicbook-pro' ); ?></div>
					<div class="stat-row">💰 <b><?php echo esc_html( cbp_format_inr( $fees ) ); ?></b></div>
					<div class="stat-row">📞 <b><?php echo esc_html( cbp_doc_meta( $pid, 'phone' ) ); ?></b></div>
					<div style="margin-top:18px">
						<a class="btn btn-primary" style="width:100%" href="<?php echo esc_url( cbp_booking_url( $pid ) ); ?>"><?php esc_html_e( 'Book Appointment', 'clinicbook-pro' ); ?></a>
					</div>
				</div>
			</div>

			<div class="tabs">
				<button class="tab-btn active" data-tab="overview"><?php esc_html_e( 'Overview', 'clinicbook-pro' ); ?></button>
				<button class="tab-btn" data-tab="reviews" id="reviews"><?php esc_html_e( 'Reviews', 'clinicbook-pro' ); ?></button>
			</div>

			<div class="tab-panel active" id="tab-overview">
				<h3><?php printf( esc_html__( 'About "%s"', 'clinicbook-pro' ), esc_html( get_the_title() ) ); ?></h3>
				<div><?php the_content(); ?></div>

				<?php if ( $services ) : ?>
					<h3 style="margin-top:28px"><?php esc_html_e( 'Offered Services', 'clinicbook-pro' ); ?></h3>
					<?php foreach ( $services as $srv ) : ?>
						<div class="accordion">
							<button class="accordion-head" type="button"><?php echo esc_html( $srv ); ?><span>＋</span></button>
							<div class="accordion-body"><p><?php printf( esc_html__( '%s is available at %s. Book a slot to consult.', 'clinicbook-pro' ), esc_html( $srv ), esc_html( cbp_doc_meta( $pid, 'clinic' ) ) ); ?></p></div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>

				<h3 style="margin-top:28px"><?php esc_html_e( 'Education', 'clinicbook-pro' ); ?></h3>
				<p><?php echo esc_html( cbp_doc_meta( $pid, 'education' ) ); ?></p>

				<h3 style="margin-top:20px"><?php esc_html_e( 'Languages', 'clinicbook-pro' ); ?></h3>
				<p><?php echo esc_html( cbp_doc_meta( $pid, 'languages' ) ); ?></p>
			</div>

			<div class="tab-panel" id="tab-reviews">
				<?php comments_template(); ?>
			</div>
		</div>
	</section>

<?php endwhile; ?>
<?php get_footer(); ?>
