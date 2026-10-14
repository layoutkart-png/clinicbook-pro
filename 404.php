<?php
/**
 * ClinicBook Pro — 404.
 *
 * @package ClinicBook_Pro
 */
get_header();
?>
<section class="section"><div class="container" style="text-align:center;max-width:560px">
	<h1 style="font-size:72px">404</h1>
	<h2><?php esc_html_e( 'Page not found', 'clinicbook-pro' ); ?></h2>
	<p style="color:var(--cbp-muted)"><?php esc_html_e( 'The page you are looking for may have been moved or deleted.', 'clinicbook-pro' ); ?></p>
	<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'clinicbook-pro' ); ?></a>
</div></section>
<?php get_footer(); ?>
