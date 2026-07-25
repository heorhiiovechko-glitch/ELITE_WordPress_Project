<?php
/**
 * Footer payment/trust strip image.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$payments_image = function_exists( 'elite_shipping_get_footer_payments_image_url' )
	? elite_shipping_get_footer_payments_image_url()
	: ELITE_SHIPPING_URI . '/assets/images/payments.png';
?>
<img
	class="apex-pay-strip"
	src="<?php echo esc_url( $payments_image ); ?>"
	alt="<?php esc_attr_e( 'Accepted payment methods and trust badges', 'elite-shipping' ); ?>"
	width="512"
	height="64"
	loading="lazy"
	decoding="async"
>
