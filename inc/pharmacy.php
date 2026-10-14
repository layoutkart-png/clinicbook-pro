<?php
/**
 * ClinicBook Pro — Pharmacy / online medical store features.
 *
 * Medicine meta boxes on WooCommerce products, Rx badges, medicine facts
 * cards, symptom-chip medicine search shortcode and WhatsApp ordering.
 *
 * @package ClinicBook_Pro
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ---------- medicine meta fields ---------- */
function cbp_medicine_fields() {
	return array(
		'generic'   => __( 'Generic Name', 'clinicbook-pro' ),
		'company'   => __( 'Company / Manufacturer', 'clinicbook-pro' ),
		'form'      => __( 'Form (Tablet, Syrup, Capsule…)', 'clinicbook-pro' ),
		'strength'  => __( 'Strength (e.g. 500mg)', 'clinicbook-pro' ),
		'pack_size' => __( 'Pack Size (e.g. 15 tablets)', 'clinicbook-pro' ),
	);
}

function cbp_add_medicine_metabox() {
	if ( ! post_type_exists( 'product' ) ) { return; }
	add_meta_box(
		'cbp_medicine',
		__( 'Medicine Details', 'clinicbook-pro' ),
		'cbp_medicine_metabox_html',
		'product',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'cbp_add_medicine_metabox' );

function cbp_medicine_metabox_html( $post ) {
	wp_nonce_field( 'cbp_medicine_save', 'cbp_medicine_nonce' );
	$rx = get_post_meta( $post->ID, '_cbp_rx', true );
	foreach ( cbp_medicine_fields() as $key => $label ) {
		$val = get_post_meta( $post->ID, '_cbp_med_' . $key, true );
		?>
		<p>
			<label><strong><?php echo esc_html( $label ); ?></strong></label><br>
			<input type="text" name="cbp_med_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $val ); ?>" style="width:100%;max-width:420px">
		</p>
		<?php
	}
	?>
	<p>
		<label>
			<input type="checkbox" name="cbp_med_rx" value="1" <?php checked( $rx, '1' ); ?>>
			<strong><?php esc_html_e( 'Prescription required (Rx)', 'clinicbook-pro' ); ?></strong>
		</label>
	</p>
	<?php
}

function cbp_save_medicine_meta( $post_id ) {
	if ( ! isset( $_POST['cbp_medicine_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['cbp_medicine_nonce'] ), 'cbp_medicine_save' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	foreach ( array_keys( cbp_medicine_fields() ) as $key ) {
		$field = 'cbp_med_' . $key;
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, '_cbp_med_' . $key, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
	update_post_meta( $post_id, '_cbp_rx', isset( $_POST['cbp_med_rx'] ) ? '1' : '0' );
}
add_action( 'save_post_product', 'cbp_save_medicine_meta' );

/* ---------- helpers ---------- */
function cbp_is_rx( $product_id ) {
	return get_post_meta( $product_id, '_cbp_rx', true ) === '1';
}

function cbp_med_meta( $product_id, $key, $default = '' ) {
	$val = get_post_meta( $product_id, '_cbp_med_' . $key, true );
	return $val !== '' ? $val : $default;
}

function cbp_pharmacy_whatsapp() {
	$num = get_theme_mod( 'cbp_pharmacy_whatsapp', '' );
	if ( $num === '' ) {
		$num = get_theme_mod( 'cbp_whatsapp', '' );
	}
	return preg_replace( '/\D/', '', $num );
}

function cbp_product_whatsapp_link( $product_id ) {
	$wa = cbp_pharmacy_whatsapp();
	if ( $wa === '' ) { return ''; }
	$title = get_the_title( $product_id );
	$price = '';
	if ( function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $product_id );
		if ( $product ) {
			$price = wp_strip_all_tags( $product->get_price_html() );
		}
	}
	$msg = sprintf(
		/* translators: %1$s product name, %2$s price */
		__( 'Namaste! I want to order this medicine: %1$s (%2$s). Please confirm availability.', 'clinicbook-pro' ),
		$title,
		$price
	);
	return 'https://wa.me/' . $wa . '?text=' . rawurlencode( $msg );
}

/* ---------- Rx badge ---------- */
function cbp_rx_badge_html( $product_id ) {
	if ( ! cbp_is_rx( $product_id ) ) { return ''; }
	return '<span class="cbp-rx-badge" title="' . esc_attr__( 'Prescription required', 'clinicbook-pro' ) . '">Rx</span>';
}

/* Rx badge on shop loop thumbnails */
function cbp_loop_rx_badge() {
	global $product;
	if ( ! $product ) { return; }
	echo cbp_rx_badge_html( $product->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'woocommerce_before_shop_loop_item_title', 'cbp_loop_rx_badge', 9 );

/* Rx badge + WhatsApp order button on single product */
function cbp_single_rx_badge() {
	global $product;
	if ( ! $product ) { return; }
	echo cbp_rx_badge_html( $product->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'woocommerce_single_product_summary', 'cbp_single_rx_badge', 4 );

function cbp_single_whatsapp_button() {
	global $product;
	if ( ! $product ) { return; }
	$link = cbp_product_whatsapp_link( $product->get_id() );
	if ( $link === '' ) { return; }
	?>
	<a class="cbp-wa-order" href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.2 14.2c-.2.6-1.2 1.2-1.7 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.6-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.9 2.1c.1.2.1.4 0 .6l-.4.6-.5.5c-.2.2-.3.4-.1.7.2.3.9 1.5 2 2.4 1.4 1.2 2.5 1.6 2.9 1.8.3.2.5.1.7-.1l1-1.2c.2-.3.4-.2.7-.1l2 1c.3.1.5.2.6.4 0 .1 0 .7-.2 1.4z"/></svg>
		<?php esc_html_e( 'Order on WhatsApp', 'clinicbook-pro' ); ?>
	</a>
	<?php
	$rx_info = get_theme_mod( 'cbp_rx_info', '' );
	if ( cbp_is_rx( $product->get_id() ) && $rx_info !== '' ) {
		echo '<p class="cbp-rx-note">' . esc_html( $rx_info ) . '</p>';
	}
}
add_action( 'woocommerce_single_product_summary', 'cbp_single_whatsapp_button', 35 );

/* ---------- medicine facts card on single product ---------- */
function cbp_medicine_facts_card() {
	global $product;
	if ( ! $product ) { return; }
	$pid     = $product->get_id();
	$generic = cbp_med_meta( $pid, 'generic' );
	$company = cbp_med_meta( $pid, 'company' );
	$form    = cbp_med_meta( $pid, 'form' );
	$str     = cbp_med_meta( $pid, 'strength' );
	$pack    = cbp_med_meta( $pid, 'pack_size' );
	if ( $generic === '' && $company === '' && $form === '' && $str === '' && $pack === '' ) { return; }
	$rows = array(
		__( 'Generic Name', 'clinicbook-pro' ) => $generic,
		__( 'Company', 'clinicbook-pro' )      => $company,
		__( 'Form', 'clinicbook-pro' )         => $form,
		__( 'Strength', 'clinicbook-pro' )     => $str,
		__( 'Pack Size', 'clinicbook-pro' )    => $pack,
	);
	?>
	<div class="cbp-facts-card">
		<h4><?php esc_html_e( 'Medicine Facts', 'clinicbook-pro' ); ?> <?php echo cbp_rx_badge_html( $pid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h4>
		<table>
			<?php foreach ( $rows as $label => $value ) : if ( $value === '' ) { continue; } ?>
				<tr><th><?php echo esc_html( $label ); ?></th><td><?php echo esc_html( $value ); ?></td></tr>
			<?php endforeach; ?>
		</table>
		<p class="cbp-facts-note"><?php esc_html_e( 'Take medicines only as directed by your doctor.', 'clinicbook-pro' ); ?></p>
	</div>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'cbp_medicine_facts_card', 25 );

/* ---------- [clinicbook_medicine_search] ---------- */
function cbp_symptom_chips() {
	return array(
		__( 'Fever', 'clinicbook-pro' ),
		__( 'Cold', 'clinicbook-pro' ),
		__( 'Pain', 'clinicbook-pro' ),
		__( 'Acidity', 'clinicbook-pro' ),
		__( 'Diabetes', 'clinicbook-pro' ),
		__( 'BP', 'clinicbook-pro' ),
		__( 'Vitamins', 'clinicbook-pro' ),
		__( 'Baby Care', 'clinicbook-pro' ),
	);
}

function cbp_sc_medicine_search( $atts ) {
	$atts = shortcode_atts( array( 'placeholder' => __( 'Search medicines by name…', 'clinicbook-pro' ) ), $atts, 'clinicbook_medicine_search' );
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	ob_start();
	?>
	<div class="cbp-med-search">
		<form role="search" method="get" action="<?php echo esc_url( $shop_url ); ?>">
			<input type="search" name="s" placeholder="<?php echo esc_attr( $atts['placeholder'] ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
			<?php if ( function_exists( 'is_woocommerce' ) ) : ?>
				<input type="hidden" name="post_type" value="product">
			<?php endif; ?>
			<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Search', 'clinicbook-pro' ); ?></button>
		</form>
		<div class="cbp-chips">
			<?php foreach ( cbp_symptom_chips() as $chip ) : ?>
				<a class="cbp-chip" href="<?php echo esc_url( add_query_arg( array( 's' => $chip, 'post_type' => 'product' ), $shop_url ) ); ?>"><?php echo esc_html( $chip ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'clinicbook_medicine_search', 'cbp_sc_medicine_search' );
