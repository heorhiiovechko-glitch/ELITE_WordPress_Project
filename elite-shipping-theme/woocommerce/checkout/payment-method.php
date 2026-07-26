<?php
/**
 * Output a single payment method.
 *
 * @package Elite_Shipping
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> apex-payment-method">
	<div class="apex-payment-method-head">
		<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />

		<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
			<?php echo $gateway->get_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $gateway->get_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</label>
	</div>
	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
			<?php
			if ( 'bacs' === $gateway->id && function_exists( 'elite_shipping_render_bacs_payment_fields' ) ) {
				elite_shipping_render_bacs_payment_fields( $gateway );
			} else {
				$gateway->payment_fields();
			}
			?>
		</div>
	<?php endif; ?>
</li>
