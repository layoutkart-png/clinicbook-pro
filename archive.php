<?php
/**
 * ClinicBook Pro — generic archive.
 *
 * @package ClinicBook_Pro
 */
get_header();
?>
<div class="page-hero"><div class="container"><h1><?php the_archive_title(); ?></h1></div></div>
<section class="section"><div class="container">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article class="post-card">
			<h2><a href="<?php the_permalink(); ?>" style="color:var(--cbp-navy)"><?php the_title(); ?></a></h2>
			<div><?php the_excerpt(); ?></div>
		</article>
	<?php endwhile; the_posts_pagination(); else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'clinicbook-pro' ); ?></p>
	<?php endif; ?>
</div></section>
<?php get_footer(); ?>
