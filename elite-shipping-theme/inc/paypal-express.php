<?php
/**
 * Disable PayPal express/smart buttons on product and cart pages.
 *
 * PayPal can still be chosen as a payment method on the checkout page.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'woocommerce_init', 'elite_shipping_disable_paypal_express_buttons', 20 );

/**
 * Register PayPal express button disable hooks.
 */
function elite_shipping_disable_paypal_express_buttons() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	add_filter( 'woocommerce_paypal_payments_buttons_disabled', 'elite_shipping_paypal_express_disabled_outside_checkout', 100, 2 );
	add_filter( 'woocommerce_paypal_payments_product_buttons_enabled', '__return_false', 100 );
	add_filter( 'woocommerce_paypal_payments_product_buttons_disabled', '__return_true', 100 );
	add_filter( 'woocommerce_paypal_express_checkout_enabled_on_product_page', '__return_false', 100 );
	add_filter( 'woocommerce_paypal_payments_proceed_to_checkout_button_renderer_hook', 'elite_shipping_paypal_disabled_renderer_hook', 100 );
	add_filter( 'woocommerce_paypal_payments_single_product_renderer_hook', 'elite_shipping_paypal_disabled_renderer_hook', 100 );
	add_action( 'wp_head', 'elite_shipping_hide_paypal_express_buttons_css', 100 );
}

/**
 * Disable PayPal smart buttons on cart, product, and mini-cart contexts.
 *
 * @param bool|null $is_disabled Whether buttons are disabled.
 * @param string    $context     PayPal button context.
 * @return bool
 */
function elite_shipping_paypal_express_disabled_outside_checkout( $is_disabled, $context ) {
	if ( in_array( $context, array( 'cart', 'product', 'mini-cart' ), true ) ) {
		return true;
	}

	return (bool) $is_disabled;
}

/**
 * Render PayPal express buttons into a dead hook so they never output.
 *
 * @return string
 */
function elite_shipping_paypal_disabled_renderer_hook() {
	return 'elite_paypal_express_disabled';
}

/**
 * CSS fallback to hide PayPal express buttons on cart and product pages.
 */
function elite_shipping_hide_paypal_express_buttons_css() {
	if ( ! is_cart() && ! is_singular( 'product' ) ) {
		return;
	}
	?>
	<style id="elite-hide-paypal-express">
		body.elite-cart-page .ppc-button-wrapper,
		body.elite-cart-page .ppcp-button-wrapper,
		body.elite-cart-page [id^="ppc-button-"],
		body.elite-cart-page .paypal-buttons,
		body.elite-cart-page .woocommerce-paypal-buttons,
		body.elite-single-product-page .ppc-button-wrapper,
		body.elite-single-product-page .ppcp-button-wrapper,
		body.elite-single-product-page [id^="ppc-button-"],
		body.elite-single-product-page .paypal-buttons,
		body.elite-single-product-page .woocommerce-paypal-buttons,
		body.elite-single-product-page form.cart .ppc-button-wrapper,
		body.elite-single-product-page form.cart .ppcp-button-wrapper,
		body.elite-single-product-page form.cart [id^="ppc-button-"],
		body.elite-single-product-page form.cart .paypal-buttons,
		body.elite-single-product-page form.cart .woocommerce-paypal-buttons {
			display: none !important;
			visibility: hidden !important;
			height: 0 !important;
			margin: 0 !important;
			padding: 0 !important;
			overflow: hidden !important;
			pointer-events: none !important;
		}
		body.elite-single-product-page .apex-single-express-checkout:empty {
			display: none !important;
		}
	</style>
	<?php
}
