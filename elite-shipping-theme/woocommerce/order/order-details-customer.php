<?php
/**
 * Order customer details — billing / shipping cards.
 *
 * @package Elite_Shipping
 * @see     woocommerce/templates/order/order-details-customer.php
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>

<section class="woocommerce-customer-details apex-order-addresses">

	<?php if ( $show_shipping ) : ?>
	<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses apex-order-addresses-grid">
		<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">
	<?php endif; ?>

	<div class="apex-order-address-card">
		<header class="apex-order-address-card__head">
			<span class="apex-order-address-card__icon" aria-hidden="true">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
			</span>
			<h2 class="woocommerce-column__title"><?php esc_html_e( 'Billing address', 'woocommerce' ); ?></h2>
		</header>
		<address class="apex-order-address-card__body">
			<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>

			<?php if ( $order->get_billing_phone() ) : ?>
				<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
			<?php endif; ?>

			<?php if ( $order->get_billing_email() ) : ?>
				<p class="woocommerce-customer-details--email"><?php echo esc_html( $order->get_billing_email() ); ?></p>
			<?php endif; ?>

			<?php
			/**
			 * Action hook fired after an address in order details.
			 *
			 * @since 8.7.0
			 * @param string   $address_type Type of address (billing or shipping).
			 * @param WC_Order $order Order object.
			 */
			do_action( 'woocommerce_order_details_after_customer_address', 'billing', $order );
			?>
		</address>
	</div>

	<?php if ( $show_shipping ) : ?>
		</div>

		<div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2">
			<div class="apex-order-address-card">
				<header class="apex-order-address-card__head">
					<span class="apex-order-address-card__icon" aria-hidden="true">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-5.3 7-12a7 7 0 1 0-14 0c0 6.7 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/></svg>
					</span>
					<h2 class="woocommerce-column__title"><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></h2>
				</header>
				<address class="apex-order-address-card__body">
					<?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>

					<?php if ( $order->get_shipping_phone() ) : ?>
						<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_shipping_phone() ); ?></p>
					<?php endif; ?>

					<?php
					/**
					 * Action hook fired after an address in order details.
					 *
					 * @since 8.7.0
					 * @param string   $address_type Type of address (billing or shipping).
					 * @param WC_Order $order Order object.
					 */
					do_action( 'woocommerce_order_details_after_customer_address', 'shipping', $order );
					?>
				</address>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

</section>
