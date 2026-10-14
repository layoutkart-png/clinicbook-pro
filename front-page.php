<?php
/**
 * ClinicBook Pro — homepage.
 *
 * @package ClinicBook_Pro
 */
get_header();
?>

<!-- HERO -->
<section class="hero">
	<div class="container hero-grid">
		<div>
			<h1><?php echo esc_html( get_theme_mod( 'cbp_hero_title', __( 'Find the Right Doctor, Book in Seconds', 'clinicbook-pro' ) ) ); ?></h1>
			<p><?php echo esc_html( get_theme_mod( 'cbp_hero_sub', __( 'Verified doctors, instant slot booking and WhatsApp confirmations — healthcare made simple.', 'clinicbook-pro' ) ) ); ?></p>
			<form class="search-card" action="<?php echo esc_url( get_post_type_archive_link( 'doctor' ) ); ?>" method="get">
				<div class="search-row">
					<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search doctors, clinics…', 'clinicbook-pro' ); ?>" />
					<select name="spec">
						<option value=""><?php esc_html_e( 'All Specialities', 'clinicbook-pro' ); ?></option>
						<?php
						$terms = get_terms( array( 'taxonomy' => 'specialization', 'hide_empty' => false ) );
						if ( $terms && ! is_wp_error( $terms ) ) {
							foreach ( $terms as $t ) {
								echo '<option value="' . esc_attr( $t->slug ) . '">' . esc_html( $t->name ) . '</option>';
							}
						}
						?>
					</select>
					<button class="btn btn-primary" type="submit"><?php esc_html_e( 'Search', 'clinicbook-pro' ); ?></button>
				</div>
			</form>
			<div class="hero-stats">
				<div><b>500+</b><span><?php esc_html_e( 'Verified Doctors', 'clinicbook-pro' ); ?></span></div>
				<div><b>50k+</b><span><?php esc_html_e( 'Appointments', 'clinicbook-pro' ); ?></span></div>
				<div><b>4.8★</b><span><?php esc_html_e( 'Patient Rating', 'clinicbook-pro' ); ?></span></div>
			</div>
		</div>
		<div class="hero-art">
			<svg width="380" height="380" viewBox="0 0 380 380" role="img" aria-label="<?php esc_attr_e( 'Doctor illustration', 'clinicbook-pro' ); ?>">
				<circle cx="190" cy="190" r="170" fill="#dbeeff"/>
				<circle cx="190" cy="190" r="130" fill="#c2e2ff"/>
				<rect x="150" y="120" width="80" height="150" rx="36" fill="#ffffff"/>
				<circle cx="190" cy="105" r="34" fill="#ffd9b8"/>
				<path d="M158 100 q32 -34 64 0 q-6 -30 -32 -30 q-26 0 -32 30" fill="#3b2f2f"/>
				<rect x="172" y="140" width="36" height="60" rx="8" fill="#0E8AFD" opacity="0.9"/>
				<circle cx="150" cy="200" r="10" fill="#0DCEA9"/><circle cx="230" cy="200" r="10" fill="#0DCEA9"/>
				<rect x="120" y="270" width="140" height="14" rx="7" fill="#0E8AFD" opacity="0.25"/>
				<rect x="140" y="292" width="100" height="14" rx="7" fill="#0DCEA9" opacity="0.35"/>
			</svg>
		</div>
	</div>
</section>

<!-- SERVICE TILES -->
<section class="section">
	<div class="container">
		<div class="tiles">
			<?php
			$tiles = array(
				array( '📅', __( 'Book Appointment', 'clinicbook-pro' ), __( 'Pick a slot that suits you.', 'clinicbook-pro' ) ),
				array( '🩺', __( 'Find Doctors', 'clinicbook-pro' ), __( 'Verified specialists near you.', 'clinicbook-pro' ) ),
				array( '🧪', __( 'Lab Tests', 'clinicbook-pro' ), __( 'Home sample collection.', 'clinicbook-pro' ) ),
				array( '💊', __( 'Medicines', 'clinicbook-pro' ), __( 'Order with prescription.', 'clinicbook-pro' ) ),
			);
			foreach ( $tiles as $t ) :
				?>
				<div class="tile">
					<div class="icon"><?php echo esc_html( $t[0] ); ?></div>
					<h3><?php echo esc_html( $t[1] ); ?></h3>
					<p><?php echo esc_html( $t[2] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- SPECIALITIES -->
<section class="section section-alt">
	<div class="container">
		<span class="eyebrow" style="display:table;margin:0 auto 14px"><?php esc_html_e( 'Specialities', 'clinicbook-pro' ); ?></span>
		<h2 class="section-title"><?php esc_html_e( 'Browse by Speciality', 'clinicbook-pro' ); ?></h2>
		<p class="section-sub"><?php esc_html_e( 'From heart to skin — find the right specialist for every concern.', 'clinicbook-pro' ); ?></p>
		<div class="spec-grid">
			<?php
			$spec_data = array(
				array( 'Cardiology', '❤️' ), array( 'Dermatology', '✨' ), array( 'Orthopedic', '🦴' ),
				array( 'Pediatrics', '👶' ), array( 'Neurology', '🧠' ), array( 'Dental', '🦷' ),
				array( 'Gynecology', '🤰' ), array( 'Ophthalmology', '👁️' ),
			);
			foreach ( $spec_data as $sd ) :
				$term = get_term_by( 'name', $sd[0], 'specialization' );
				$count = $term ? absint( $term->count ) : 0;
				$link = $term ? get_term_link( $term ) : get_post_type_archive_link( 'doctor' );
				?>
				<a class="spec-card" href="<?php echo esc_url( $link ); ?>">
					<div class="spec-ico"><?php echo esc_html( $sd[1] ); ?></div>
					<h3><?php echo esc_html( $sd[0] ); ?></h3>
					<span class="count"><?php printf( esc_html( _n( '%d Doctor', '%d Doctors', $count, 'clinicbook-pro' ) ), esc_html( number_format_i18n( $count ) ) ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- FEATURED DOCTORS -->
<section class="section">
	<div class="container">
		<span class="eyebrow" style="display:table;margin:0 auto 14px"><?php esc_html_e( 'Featured Doctors', 'clinicbook-pro' ); ?></span>
		<h2 class="section-title"><?php esc_html_e( 'Our Highlighted Doctors', 'clinicbook-pro' ); ?></h2>
		<p class="section-sub"><?php esc_html_e( 'Top-rated, verified doctors trusted by thousands of patients.', 'clinicbook-pro' ); ?></p>
		<?php echo do_shortcode( '[clinicbook_doctors count="4" featured="1"]' ); ?>
		<div style="text-align:center;margin-top:34px">
			<a class="btn btn-outline" href="<?php echo esc_url( get_post_type_archive_link( 'doctor' ) ); ?>"><?php esc_html_e( 'View All Doctors', 'clinicbook-pro' ); ?></a>
		</div>
	</div>
</section>

<!-- ORDER MEDICINES ONLINE -->
<section class="section">
	<div class="container">
		<div class="cbp-pharma-band">
			<h2><?php esc_html_e( 'Order Medicines Online', 'clinicbook-pro' ); ?></h2>
			<p><?php esc_html_e( 'Genuine medicines, vitamins & healthcare products delivered to your doorstep. Upload your prescription on WhatsApp.', 'clinicbook-pro' ); ?></p>
			<?php echo do_shortcode( '[clinicbook_medicine_search]' ); ?>
			<?php
			$wa = cbp_pharmacy_whatsapp();
			if ( $wa !== '' ) :
				$rx_msg = __( 'Namaste! I want to order medicines with my prescription. Please share the upload link.', 'clinicbook-pro' );
			?>
				<a class="cbp-wa-order" href="<?php echo esc_url( 'https://wa.me/' . $wa . '?text=' . rawurlencode( $rx_msg ) ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'Send Prescription on WhatsApp', 'clinicbook-pro' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>

<!-- HOW IT WORKS -->
<section class="section section-alt">
	<div class="container">
		<h2 class="section-title"><?php esc_html_e( 'How It Works', 'clinicbook-pro' ); ?></h2>
		<p class="section-sub"><?php esc_html_e( 'Four simple steps to your appointment.', 'clinicbook-pro' ); ?></p>
		<div class="steps">
			<?php
			$steps = array(
				array( __( 'Search', 'clinicbook-pro' ), __( 'Find doctors by speciality or name.', 'clinicbook-pro' ) ),
				array( __( 'Pick a Slot', 'clinicbook-pro' ), __( 'Choose date & time that suits you.', 'clinicbook-pro' ) ),
				array( __( 'Confirm', 'clinicbook-pro' ), __( 'Enter details, get instant confirmation.', 'clinicbook-pro' ) ),
				array( __( 'Visit / Video Call', 'clinicbook-pro' ), __( 'Meet your doctor, in-person or online.', 'clinicbook-pro' ) ),
			);
			$i = 1;
			foreach ( $steps as $s ) :
				?>
				<div class="step-card">
					<div class="step-num"><?php echo esc_html( $i++ ); ?></div>
					<h3><?php echo esc_html( $s[0] ); ?></h3>
					<p><?php echo esc_html( $s[1] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- WHY CHOOSE US -->
<section class="section">
	<div class="container">
		<h2 class="section-title"><?php esc_html_e( 'Why Choose ClinicBook Pro', 'clinicbook-pro' ); ?></h2>
		<p class="section-sub"><?php esc_html_e( 'Healthcare booking without the headache.', 'clinicbook-pro' ); ?></p>
		<div class="why-grid">
			<div class="why-card">
				<h3>✅ <?php esc_html_e( 'Verified Doctors', 'clinicbook-pro' ); ?></h3>
				<p><?php esc_html_e( 'Every doctor is credential-checked before listing. No fakes, no surprises.', 'clinicbook-pro' ); ?></p>
			</div>
			<div class="why-card">
				<h3>⚡ <?php esc_html_e( 'Instant Booking', 'clinicbook-pro' ); ?></h3>
				<p><?php esc_html_e( 'Real-time slots, instant confirmation and WhatsApp reminders. No phone-tag.', 'clinicbook-pro' ); ?></p>
			</div>
			<div class="why-card">
				<h3>💰 <?php esc_html_e( 'Transparent Pricing', 'clinicbook-pro' ); ?></h3>
				<p><?php esc_html_e( 'Fees in ₹ shown upfront. No hidden charges, ever.', 'clinicbook-pro' ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- STATS -->
<section class="section section-alt">
	<div class="container">
		<div class="counters">
			<div><b data-count="500">0</b><span><?php esc_html_e( 'Doctors', 'clinicbook-pro' ); ?></span></div>
			<div><b data-count="50000">0</b><span><?php esc_html_e( 'Appointments', 'clinicbook-pro' ); ?></span></div>
			<div><b data-count="120">0</b><span><?php esc_html_e( 'Clinics', 'clinicbook-pro' ); ?></span></div>
			<div><b data-count="15">0</b><span><?php esc_html_e( 'Cities', 'clinicbook-pro' ); ?></span></div>
		</div>
	</div>
</section>

<!-- TESTIMONIALS -->
<section class="section">
	<div class="container">
		<h2 class="section-title"><?php esc_html_e( 'What Patients Say', 'clinicbook-pro' ); ?></h2>
		<p class="section-sub"><?php esc_html_e( 'Real stories from real patients.', 'clinicbook-pro' ); ?></p>
		<div class="testi-grid">
			<div class="testi">
				<div class="stars">★★★★★</div>
				<p><?php esc_html_e( '“Booked a cardiologist at 9 AM, confirmed on WhatsApp by 9:05. Zero waiting at the clinic.”', 'clinicbook-pro' ); ?></p>
				<div class="who">— Ramesh Gupta, Prayagraj</div>
			</div>
			<div class="testi">
				<div class="stars">★★★★★</div>
				<p><?php esc_html_e( '“Video consultation for my mother saved us a 40 km trip. Doctor was excellent.”', 'clinicbook-pro' ); ?></p>
				<div class="who">— Sunita Devi, Kaushambi</div>
			</div>
			<div class="testi">
				<div class="stars">★★★★☆</div>
				<p><?php esc_html_e( '“Fees shown upfront, reminder on WhatsApp, smooth experience overall.”', 'clinicbook-pro' ); ?></p>
				<div class="who">— Arjun Singh, Lucknow</div>
			</div>
		</div>
	</div>
</section>

<!-- FAQ -->
<section class="section section-alt">
	<div class="container">
		<h2 class="section-title"><?php esc_html_e( 'Frequently Asked Questions', 'clinicbook-pro' ); ?></h2>
		<div class="faq" style="margin-top:34px">
			<?php
			$faqs = array(
				array( __( 'Do I need to create an account to book?', 'clinicbook-pro' ), __( 'No. Just enter your name and mobile number — booking takes under a minute, no login needed.', 'clinicbook-pro' ) ),
				array( __( 'How do I confirm my appointment?', 'clinicbook-pro' ), __( 'After booking you get an appointment ID instantly, plus a WhatsApp button to confirm directly with the clinic.', 'clinicbook-pro' ) ),
				array( __( 'Can I reschedule or cancel?', 'clinicbook-pro' ), __( 'Yes — call the clinic on the number shown in your confirmation, or message them on WhatsApp.', 'clinicbook-pro' ) ),
				array( __( 'Are video consultations available?', 'clinicbook-pro' ), __( 'Doctors marked with the Video tag offer online consultations. Choose the Video option while booking.', 'clinicbook-pro' ) ),
				array( __( 'Is there any booking fee?', 'clinicbook-pro' ), __( 'No. You only pay the doctor’s consultation fee, shown in ₹ before you book.', 'clinicbook-pro' ) ),
			);
			foreach ( $faqs as $f ) :
				?>
				<div class="accordion">
					<button class="accordion-head" type="button"><?php echo esc_html( $f[0] ); ?><span>＋</span></button>
					<div class="accordion-body"><p><?php echo esc_html( $f[1] ); ?></p></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- BLOG -->
<section class="section">
	<div class="container">
		<h2 class="section-title"><?php esc_html_e( 'Health Blog', 'clinicbook-pro' ); ?></h2>
		<p class="section-sub"><?php esc_html_e( 'Tips from our doctors.', 'clinicbook-pro' ); ?></p>
		<div class="blog-grid">
			<?php
			$blog = new WP_Query( array( 'posts_per_page' => 3, 'post_status' => 'publish' ) );
			if ( $blog->have_posts() ) :
				while ( $blog->have_posts() ) : $blog->the_post();
					?>
					<article class="blog-card">
						<div class="thumb"><?php the_post_thumbnail( 'cbp-thumb' ); ?></div>
						<div class="bbody">
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p style="color:var(--cbp-muted);font-size:14px"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 16 ) ); ?></p>
						</div>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
			else :
				?>
				<p style="grid-column:1/-1;text-align:center;color:var(--cbp-muted)"><?php esc_html_e( 'Blog posts coming soon.', 'clinicbook-pro' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
