<?php
/**
 * Thank you / order received page.
 *
 * @package Elite_Shipping
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-order apex-thankyou">

	<?php if ( $order ) : ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<div class="apex-thankyou-status apex-thankyou-status--failed">
				<span class="apex-thankyou-status-icon" aria-hidden="true">!</span>
				<div class="apex-thankyou-status-copy">
					<h2 class="apex-thankyou-status-title"><?php esc_html_e( 'Payment unsuccessful', 'elite-shipping' ); ?></h2>
					<p class="apex-thankyou-status-text">
						<?php esc_html_e( 'Unfortunately your order cannot be processed as the payment failed. Please try again or use another payment method.', 'elite-shipping' ); ?>
					</p>
					<p class="apex-thankyou-actions">
						<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button apex-thankyou-btn"><?php esc_html_e( 'Try payment again', 'elite-shipping' ); ?></a>
						<?php if ( is_user_logged_in() ) : ?>
							<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button apex-thankyou-btn apex-thankyou-btn--ghost"><?php esc_html_e( 'My account', 'elite-shipping' ); ?></a>
						<?php endif; ?>
					</p>
				</div>
			</div>

		<?php else : ?>

			<div class="apex-thankyou-status apex-thankyou-status--success">
				<span class="apex-thankyou-status-icon" aria-hidden="true">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</span>
				<div class="apex-thankyou-status-copy">
					<p class="apex-thankyou-status-kicker"><?php esc_html_e( 'Order confirmed', 'elite-shipping' ); ?></p>
					<h2 class="apex-thankyou-status-title"><?php echo esc_html__( 'Thank you. Your order has been received.', 'elite-shipping' ); ?></h2>
					<p class="apex-thankyou-status-text">
						<?php esc_html_e( 'A confirmation email is on its way. Our team will follow up with delivery details shortly.', 'elite-shipping' ); ?>
					</p>
				</div>
			</div>

			<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details apex-thankyou-overview">
				<li class="woocommerce-order-overview__order order">
					<span class="apex-thankyou-label"><?php esc_html_e( 'Order number', 'elite-shipping' ); ?></span>
					<strong><?php echo esc_html( $order->get_order_number() ); ?></strong>
				</li>
				<li class="woocommerce-order-overview__date date">
					<span class="apex-thankyou-label"><?php esc_html_e( 'Date', 'elite-shipping' ); ?></span>
					<strong><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></strong>
				</li>
				<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="woocommerce-order-overview__email email">
						<span class="apex-thankyou-label"><?php esc_html_e( 'Email', 'elite-shipping' ); ?></span>
						<strong><?php echo esc_html( $order->get_billing_email() ); ?></strong>
					</li>
				<?php endif; ?>
				<li class="woocommerce-order-overview__total total">
					<span class="apex-thankyou-label"><?php esc_html_e( 'Total', 'elite-shipping' ); ?></span>
					<strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong>
				</li>
				<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="woocommerce-order-overview__payment-method method">
						<span class="apex-thankyou-label"><?php esc_html_e( 'Payment method', 'elite-shipping' ); ?></span>
						<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
					</li>
				<?php endif; ?>
			</ul>

		<?php endif; ?>

		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<div class="apex-thankyou-status apex-thankyou-status--success">
			<span class="apex-thankyou-status-icon" aria-hidden="true">
				<svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</span>
			<div class="apex-thankyou-status-copy">
				<h2 class="apex-thankyou-status-title"><?php echo esc_html__( 'Thank you. Your order has been received.', 'elite-shipping' ); ?></h2>
			</div>
		</div>

	<?php endif; ?>

</div>
