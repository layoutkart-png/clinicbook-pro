<?php
/**
 * ClinicBook Pro — header.
 *
 * @package ClinicBook_Pro
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="topbar">
	<div class="container">
		<span>📞 <?php echo esc_html( get_theme_mod( 'cbp_phone', '+91 98765 43210' ) ); ?></span>
		<span>🕙 <?php echo esc_html( get_theme_mod( 'cbp_timing', __( 'Mon–Sat: 10 AM – 8 PM', 'clinicbook-pro' ) ) ); ?></span>
	</div>
</div>

<header class="site-header">
	<div class="container">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">Clinic<span>Book</span> Pro</a>
		<?php endif; ?>

		<button class="hamburger" id="cbpMenuBtn" aria-label="<?php esc_attr_e( 'Menu', 'clinicbook-pro' ); ?>">☰</button>

		<nav class="main-nav" id="cbpNav" aria-label="<?php esc_attr_e( 'Primary', 'clinicbook-pro' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => 'cbp_fallback_menu',
			) );
			?>
		</nav>

		<div class="header-actions">
			<?php if ( is_user_logged_in() ) : ?>
				<a class="btn btn-dark btn-sm" href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>"><?php esc_html_e( 'Logout', 'clinicbook-pro' ); ?></a>
			<?php else : ?>
				<a class="btn btn-outline btn-sm" href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'Login', 'clinicbook-pro' ); ?></a>
				<a class="btn btn-primary btn-sm" href="<?php echo esc_url( wp_registration_url() ); ?>"><?php esc_html_e( 'Register', 'clinicbook-pro' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</header>

<main id="content">
<?php
function cbp_fallback_menu() {
	echo '<ul>';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'clinicbook-pro' ) . '</a></li>';
	echo '<li><a href="' . esc_url( get_post_type_archive_link( 'doctor' ) ) . '">' . esc_html__( 'Doctors', 'clinicbook-pro' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/book-appointment/' ) ) . '">' . esc_html__( 'Book Appointment', 'clinicbook-pro' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/blog/' ) ) . '">' . esc_html__( 'Blog', 'clinicbook-pro' ) . '</a></li>';
	echo '</ul>';
}
