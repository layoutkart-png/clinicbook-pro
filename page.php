<?php
/**
 * ClinicBook Pro — page.
 *
 * @package ClinicBook_Pro
 */
get_header();
while ( have_posts() ) : the_post();
?>
<div class="page-hero"><div class="container"><h1><?php the_title(); ?></h1></div></div>
<section class="section"><div class="container" style="max-width:860px">
	<div><?php the_content(); ?></div>
</div></section>
<?php endwhile; ?>
<?php get_footer(); ?>
