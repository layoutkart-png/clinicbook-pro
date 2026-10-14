<?php
/**
 * ClinicBook Pro — comments (used for doctor reviews/feedback).
 *
 * @package ClinicBook_Pro
 */
if ( post_password_required() ) { return; }
?>
<div class="comments-area" id="comments">
	<h3><?php printf( esc_html( _n( '%d Review', '%d Reviews', get_comments_number(), 'clinicbook-pro' ) ), esc_html( number_format_i18n( get_comments_number() ) ) ); ?></h3>
	<?php if ( have_comments() ) : ?>
		<ul class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ul',
				'short_ping'  => true,
				'avatar_size' => 48,
			) );
			?>
		</ul>
	<?php endif; ?>

	<?php
	comment_form( array(
		'title_reply'         => __( 'Write a Review', 'clinicbook-pro' ),
		'label_submit'        => __( 'Submit Review', 'clinicbook-pro' ),
		'comment_field'       => '<div class="field"><label for="comment">' . esc_html__( 'Your review', 'clinicbook-pro' ) . '</label><textarea id="comment" name="comment" rows="4" required></textarea></div>',
		'submit_button'      => '<button class="btn btn-primary" type="submit">%4$s</button>',
		'class_submit'        => '',
	) );
	?>
</div>
