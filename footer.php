<?php
/**
 * ClinicBook Pro — footer.
 *
 * @package ClinicBook_Pro
 */
?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="foot-grid">
			<div>
				<div class="foot-logo">Clinic<span>Book</span> Pro</div>
				<p><?php esc_html_e( 'Book verified doctors in seconds. Instant slots, WhatsApp confirmations, zero waiting-room chaos.', 'clinicbook-pro' ); ?></p>
				<form class="news-row" id="cbpNews" onsubmit="return false;">
					<input type="email" id="cbpNewsEmail" placeholder="<?php esc_attr_e( 'Email for health tips', 'clinicbook-pro' ); ?>" aria-label="<?php esc_attr_e( 'Email', 'clinicbook-pro' ); ?>" />
					<button type="submit"><?php esc_html_e( 'Join', 'clinicbook-pro' ); ?></button>
				</form>
			</div>
			<div>
				<h4><?php esc_html_e( 'Quick Links', 'clinicbook-pro' ); ?></h4>
				<?php
				wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'fallback_cb' => 'cbp_fallback_footer_menu' ) );
				?>
			</div>
			<div>
				<h4><?php esc_html_e( 'Specialities', 'clinicbook-pro' ); ?></h4>
				<ul>
					<?php
					$terms = get_terms( array( 'taxonomy' => 'specialization', 'number' => 6, 'hide_empty' => false ) );
					if ( $terms && ! is_wp_error( $terms ) ) :
						foreach ( $terms as $t ) :
							?>
							<li><a href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a></li>
							<?php
						endforeach;
					else :
						foreach ( array( 'Cardiology', 'Dermatology', 'Orthopedic', 'Pediatrics', 'Neurology', 'Dental' ) as $s ) :
							?>
							<li><a href="<?php echo esc_url( get_post_type_archive_link( 'doctor' ) ); ?>"><?php echo esc_html( $s ); ?></a></li>
							<?php
						endforeach;
					endif;
					?>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Contact', 'clinicbook-pro' ); ?></h4>
				<ul>
					<li>📞 <?php echo esc_html( get_theme_mod( 'cbp_phone', '+91 98765 43210' ) ); ?></li>
					<li>🕙 <?php echo esc_html( get_theme_mod( 'cbp_timing', '' ) ); ?></li>
					<li>📍 <?php echo esc_html( get_theme_mod( 'cbp_address', '' ) ); ?></li>
				</ul>
			</div>
		</div>
		<div class="foot-bottom">
			© <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'clinicbook-pro' ); ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
<?php
function cbp_fallback_footer_menu() {
	echo '<ul>';
	echo '<li><a href="' . esc_url( get_post_type_archive_link( 'doctor' ) ) . '">' . esc_html__( 'Find Doctors', 'clinicbook-pro' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/book-appointment/' ) ) . '">' . esc_html__( 'Book Appointment', 'clinicbook-pro' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/my-appointments/' ) ) . '">' . esc_html__( 'My Appointments', 'clinicbook-pro' ) . '</a></li>';
	echo '</ul>';
}
