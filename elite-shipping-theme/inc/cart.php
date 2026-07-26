<?php
/**
 * Cart page layout and hooks.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart progress steps.
 */
function elite_shipping_cart_progress_steps() {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() || WC()->cart->is_empty() ) {
		return;
	}

	$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' );
	?>
	<nav class="apex-checkout-steps" aria-label="<?php esc_attr_e( 'Checkout progress', 'elite-shipping' ); ?>">
		<span class="apex-checkout-step is-active" aria-current="step">
			<span class="apex-checkout-step-num">1</span>
			<span class="apex-checkout-step-label"><?php esc_html_e( 'Cart', 'elite-shipping' ); ?></span>
		</span>
		<span class="apex-checkout-step-divider" aria-hidden="true"></span>
		<a class="apex-checkout-step" href="<?php echo esc_url( $checkout_url ); ?>">
			<span class="apex-checkout-step-num">2</span>
			<span class="apex-checkout-step-label"><?php esc_html_e( 'Checkout', 'elite-shipping' ); ?></span>
		</a>
		<span class="apex-checkout-step-divider" aria-hidden="true"></span>
		<span class="apex-checkout-step">
			<span class="apex-checkout-step-num">3</span>
			<span class="apex-checkout-step-label"><?php esc_html_e( 'Confirmation', 'elite-shipping' ); ?></span>
		</span>
	</nav>
	<?php
}

/**
 * Shipment label for cart totals.
 *
 * @return string
 */
function elite_shipping_cart_shipment_label() {
	if ( function_exists( 'elite_shipping_cart_has_containers' ) && elite_shipping_cart_has_containers() ) {
		return elite_shipping_container_delivery_fee_label();
	}

	return __( 'Shipment', 'elite-shipping' );
}

/**
 * Open cart top bar wrapper.
 */
function elite_shipping_cart_top_open() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}
	echo '<div class="apex-checkout-top apex-cart-top">';
}

/**
 * Close cart top bar wrapper.
 */
function elite_shipping_cart_top_close() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}
	echo '</div>';
}

/**
 * Register cart hooks.
 */
function elite_shipping_cart_hooks() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	add_action( 'woocommerce_before_cart', 'elite_shipping_cart_top_open', 4 );
	add_action( 'woocommerce_before_cart', 'elite_shipping_cart_progress_steps', 5 );
	add_action( 'woocommerce_before_cart', 'elite_shipping_cart_top_close', 6 );
	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
}
add_action( 'after_setup_theme', 'elite_shipping_cart_hooks', 25 );

/**
 * Rename Shipping → Shipment on cart totals.
 *
 * @param string $translated Translated text.
 * @param string $text       Original text.
 * @param string $domain     Text domain.
 * @return string
 */
function elite_shipping_cart_shipping_label( $translated, $text, $domain ) {
	if ( 'woocommerce' === $domain && function_exists( 'is_cart' ) && is_cart() && 'Shipping' === $text ) {
		if ( function_exists( 'elite_shipping_cart_has_containers' ) && elite_shipping_cart_has_containers() ) {
			return elite_shipping_container_delivery_fee_label();
		}

		return __( 'Shipment', 'elite-shipping' );
	}

	return $translated;
}
add_filter( 'gettext', 'elite_shipping_cart_shipping_label', 10, 3 );
