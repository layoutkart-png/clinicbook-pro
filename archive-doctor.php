<?php
/**
 * ClinicBook Pro — "Find Doctors" archive.
 *
 * @package ClinicBook_Pro
 */
get_header();

$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$spec   = isset( $_GET['spec'] ) ? sanitize_title( wp_unslash( $_GET['spec'] ) ) : '';

$args = array(
	'post_type'      => 'doctor',
	'posts_per_page' => 12,
	'post_status'    => 'publish',
	'paged'          => max( 1, get_query_var( 'paged' ) ),
);
if ( $search !== '' ) { $args['s'] = $search; }
if ( $spec !== '' ) {
	$args['tax_query'] = array( array( 'taxonomy' => 'specialization', 'field' => 'slug', 'terms' => $spec ) );
}
$q = new WP_Query( $args );
?>

<div class="page-hero">
	<div class="container">
		<h1><?php esc_html_e( 'Find Doctors', 'clinicbook-pro' ); ?></h1>
		<p style="color:var(--cbp-muted)"><?php printf( esc_html( _n( '%d doctor available', '%d doctors available', $q->found_posts, 'clinicbook-pro' ) ), esc_html( number_format_i18n( $q->found_posts ) ) ); ?></p>
	</div>
</div>

<section class="section">
	<div class="container listing-layout">
		<aside class="filters">
			<h3><?php esc_html_e( 'Filter Doctors', 'clinicbook-pro' ); ?></h3>
			<form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'doctor' ) ); ?>">
				<div class="field">
					<label for="f_s"><?php esc_html_e( 'Search', 'clinicbook-pro' ); ?></label>
					<input type="search" id="f_s" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Name or clinic…', 'clinicbook-pro' ); ?>" />
				</div>
				<div class="field">
					<label for="f_spec"><?php esc_html_e( 'Specialization', 'clinicbook-pro' ); ?></label>
					<select id="f_spec" name="spec">
						<option value=""><?php esc_html_e( 'All Specialities', 'clinicbook-pro' ); ?></option>
						<?php
						$terms = get_terms( array( 'taxonomy' => 'specialization', 'hide_empty' => false ) );
						if ( $terms && ! is_wp_error( $terms ) ) {
							foreach ( $terms as $t ) {
								echo '<option value="' . esc_attr( $t->slug ) . '"' . selected( $spec, $t->slug, false ) . '>' . esc_html( $t->name ) . '</option>';
							}
						}
						?>
					</select>
				</div>
				<button class="btn btn-primary" type="submit" style="width:100%"><?php esc_html_e( 'Search', 'clinicbook-pro' ); ?></button>
			</form>
		</aside>

		<div>
			<?php if ( $q->have_posts() ) : ?>
				<div class="listing-grid">
					<?php
					while ( $q->have_posts() ) : $q->the_post();
						$pid       = get_the_ID();
						$specs     = cbp_doctor_specializations( $pid );
						$spec_name = $specs ? $specs[0] : __( 'General', 'clinicbook-pro' );
						$available = cbp_is_available_today( $pid );
						$fees      = cbp_doc_meta( $pid, 'fees', '500' );
						$rating    = (float) cbp_doc_meta( $pid, 'rating', '4.5' );
						$days      = array();
						$sched     = get_post_meta( $pid, '_cbp_schedule', true );
						$day_names = array( 'mon' => __( 'Mon', 'clinicbook-pro' ), 'tue' => __( 'Tue', 'clinicbook-pro' ), 'wed' => __( 'Wed', 'clinicbook-pro' ), 'thu' => __( 'Thu', 'clinicbook-pro' ), 'fri' => __( 'Fri', 'clinicbook-pro' ), 'sat' => __( 'Sat', 'clinicbook-pro' ), 'sun' => __( 'Sun', 'clinicbook-pro' ) );
						if ( is_array( $sched ) ) {
							foreach ( $day_names as $k => $lbl ) {
								if ( ! empty( $sched[ $k ]['m_from'] ) || ! empty( $sched[ $k ]['e_from'] ) ) { $days[] = $lbl; }
							}
						}
						?>
						<article class="list-card">
							<div class="doc-photo">
								<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'cbp-doctor' ); endif; ?>
								<span class="rating-badge">★ <?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></span>
							</div>
							<div style="flex:1;min-width:0">
								<div class="doc-spec-row">
									<span class="doc-spec"><?php echo esc_html( $spec_name ); ?></span>
									<span class="avail <?php echo $available ? 'yes' : 'no'; ?>">● <?php echo $available ? esc_html__( 'Available Today', 'clinicbook-pro' ) : esc_html__( 'On Leave', 'clinicbook-pro' ); ?></span>
								</div>
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p class="doc-meta"><?php echo esc_html( cbp_doc_meta( $pid, 'credentials' ) ); ?> · <?php echo esc_html( cbp_doc_meta( $pid, 'experience', '5' ) ); ?> <?php esc_html_e( 'yrs', 'clinicbook-pro' ); ?></p>
								<?php if ( $days ) : ?><p class="doc-meta">🗓 <?php echo esc_html( implode( ', ', $days ) ); ?></p><?php endif; ?>
								<p class="doc-meta">🏥 <?php echo esc_html( cbp_doc_meta( $pid, 'clinic' ) ); ?></p>
								<p class="doc-meta"><b style="color:var(--cbp-navy)"><?php echo esc_html( cbp_format_inr( $fees ) ); ?></b> · 📞 <?php echo esc_html( cbp_doc_meta( $pid, 'phone' ) ); ?></p>
								<div class="list-actions">
									<a class="btn btn-outline btn-sm" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View Profile', 'clinicbook-pro' ); ?></a>
									<a class="btn btn-primary btn-sm" href="<?php echo esc_url( cbp_booking_url( $pid ) ); ?>"><?php esc_html_e( 'Book Appointment', 'clinicbook-pro' ); ?></a>
								</div>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
				<div style="margin-top:28px">
					<?php
					echo paginate_links( array(
						'total'   => $q->max_num_pages,
						'current' => max( 1, get_query_var( 'paged' ) ),
					) );
					?>
				</div>
			<?php else : ?>
				<div class="post-card" style="text-align:center">
					<h3><?php esc_html_e( 'No doctors found', 'clinicbook-pro' ); ?></h3>
					<p><?php esc_html_e( 'Try a different search or specialization.', 'clinicbook-pro' ); ?></p>
				</div>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
