<?php
/**
 * ClinicBook Pro — single post.
 *
 * @package ClinicBook_Pro
 */
get_header();
while ( have_posts() ) : the_post();
?>
<section class="section"><div class="container" style="max-width:820px">
	<h1><?php the_title(); ?></h1>
	<p style="color:var(--cbp-muted)"><?php echo esc_html( get_the_date() ); ?> · <?php the_author(); ?></p>
	<?php if ( has_post_thumbnail() ) : ?><div style="margin:20px 0;border-radius:16px;overflow:hidden"><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?>
	<div><?php the_content(); ?></div>
	<?php comments_template(); ?>
</div></section>
<?php endwhile; ?>
<?php get_footer(); ?>
