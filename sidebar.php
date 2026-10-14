<?php
/**
 * ClinicBook Pro — sidebar.
 *
 * @package ClinicBook_Pro
 */
?>
<aside class="filters">
	<h3><?php esc_html_e( 'Search', 'clinicbook-pro' ); ?></h3>
	<?php get_search_form(); ?>
	<h3 style="margin-top:22px"><?php esc_html_e( 'Specialities', 'clinicbook-pro' ); ?></h3>
	<ul>
		<?php
		$terms = get_terms( array( 'taxonomy' => 'specialization', 'hide_empty' => false ) );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $t ) {
				echo '<li><a href="' . esc_url( get_term_link( $t ) ) . '">' . esc_html( $t->name ) . '</a></li>';
			}
		}
		?>
	</ul>
</aside>
