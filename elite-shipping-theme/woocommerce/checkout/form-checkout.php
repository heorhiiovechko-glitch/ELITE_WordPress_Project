<?php
/**
 * Checkout Form
 *
 * @package Elite_Shipping
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout apex-checkout-form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<div class="apex-checkout-layout">
			<div class="apex-checkout-main-col">
				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<div class="apex-checkout-fields" id="customer_details">
					<div class="apex-checkout-panel apex-checkout-panel--billing">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
					</div>

					<div class="apex-checkout-panel apex-checkout-panel--shipping">
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
					</div>
				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
			</div>

			<aside class="apex-checkout-sidebar">
				<div class="apex-checkout-order-card">
					<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

					<h3 id="order_review_heading" class="apex-checkout-order-title apex-checkout-order-title--center"><?php esc_html_e( 'Your Order', 'elite-shipping' ); ?></h3>

					<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

					<div id="order_review" class="woocommerce-checkout-review-order">
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
					</div>
				</div>

				<?php do_action( 'elite_checkout_after_order_card' ); ?>
			</aside>
		</div>

	<?php endif; ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
