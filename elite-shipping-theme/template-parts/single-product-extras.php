<?php
/**
 * Single product summary extras.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

$urls = elite_shipping_get_urls();
?>
<div class="apex-single-social-proof">
	<p><?php esc_html_e( '149 people are viewing this right now', 'elite-shipping' ); ?></p>
	<p><?php esc_html_e( '11 sold in last 15 hours', 'elite-shipping' ); ?></p>
</div>

<div class="apex-single-trust-box">
	<strong class="apex-single-trust-title"><?php esc_html_e( 'Guaranteed Safe Checkout', 'elite-shipping' ); ?></strong>
	<div class="apex-single-trust-body">
		<div class="apex-single-trust-copy">
			<?php get_template_part( 'template-parts/payment', 'icons' ); ?>
		</div>
		<a class="apex-single-trust-btn" href="<?php echo esc_url( $urls['contact'] ); ?>">
			<?php esc_html_e( 'For Any Query Get In Touch', 'elite-shipping' ); ?>
		</a>
	</div>
</div>
