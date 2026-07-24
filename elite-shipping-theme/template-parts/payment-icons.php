<?php
/**
 * Footer payment method icons.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$payments_img = ELITE_SHIPPING_URI . '/assets/images/payments.png';
?>
<div class="apex-pay-icons" aria-label="<?php echo esc_attr__( 'Accepted payment methods', 'elite-shipping' ); ?>">
	<img
		class="apex-pay-strip"
		src="<?php echo esc_url( $payments_img ); ?>"
		alt="<?php echo esc_attr__( 'Visa, Mastercard, PayPal, American Express, Visa Electron, Maestro', 'elite-shipping' ); ?>"
		width="512"
		height="51"
		loading="lazy"
		decoding="async"
	/>
</div>
