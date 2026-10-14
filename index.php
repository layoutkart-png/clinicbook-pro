<?php
/**
 * ClinicBook Pro — main loop template.
 *
 * @package ClinicBook_Pro
 */
get_header();
?>
<div class="page-hero"><div class="container"><h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1></div></div>
<section class="section"><div class="container">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article class="post-card">
			<h2><a href="<?php the_permalink(); ?>" style="color:var(--cbp-navy)"><?php the_title(); ?></a></h2>
			<p style="color:var(--cbp-muted);font-size:14px"><?php echo esc_html( get_the_date() ); ?></p>
			<div><?php the_excerpt(); ?></div>
			<a class="btn btn-outline btn-sm" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'clinicbook-pro' ); ?></a>
		</article>
	<?php endwhile; the_posts_pagination(); else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'clinicbook-pro' ); ?></p>
	<?php endif; ?>
</div></section>
<?php get_footer(); ?>
