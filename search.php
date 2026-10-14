<?php
/**
 * ClinicBook Pro — search results.
 *
 * @package ClinicBook_Pro
 */
get_header();
?>
<div class="page-hero"><div class="container">
	<h1><?php printf( esc_html__( 'Results for "%s"', 'clinicbook-pro' ), esc_html( get_search_query() ) ); ?></h1>
</div></div>
<section class="section"><div class="container">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article class="post-card">
			<h2><a href="<?php the_permalink(); ?>" style="color:var(--cbp-navy)"><?php the_title(); ?></a></h2>
			<div><?php the_excerpt(); ?></div>
		</article>
	<?php endwhile; the_posts_pagination(); else : ?>
		<p><?php esc_html_e( 'No results. Try another search.', 'clinicbook-pro' ); ?></p>
	<?php endif; ?>
</div></section>
<?php get_footer(); ?>
