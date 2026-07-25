<?php
/**
 * PayPal Pay Monthly financing modal.
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

$price_raw   = (float) $product->get_price();
$price_label = function_exists( 'wc_price' )
	? wp_strip_all_tags( wc_price( $price_raw, array( 'decimals' => 2 ) ) )
	: '$' . number_format( $price_raw, 2 );
$paypal_logo = ELITE_SHIPPING_URI . '/assets/images/paypal.svg';
?>
<div
	id="apex-paypal-monthly-modal"
	class="apex-paypal-modal"
	hidden
	aria-hidden="true"
>
	<div class="apex-paypal-modal-backdrop" data-paypal-modal-close></div>
	<div
		class="apex-paypal-modal-dialog"
		role="dialog"
		aria-modal="true"
		aria-labelledby="apex-paypal-modal-title"
		tabindex="-1"
	>
		<button type="button" class="apex-paypal-modal-close" data-paypal-modal-close aria-label="<?php esc_attr_e( 'Close', 'elite-shipping' ); ?>">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
		</button>

		<div class="apex-paypal-modal-header">
			<img class="apex-paypal-modal-logo" src="<?php echo esc_url( $paypal_logo ); ?>" alt="PayPal" width="101" height="32">
			<h2 id="apex-paypal-modal-title"><?php esc_html_e( 'Pay Monthly', 'elite-shipping' ); ?></h2>
			<p><?php esc_html_e( 'As low as 0% APR for up to 6 months. No down payment, no late fees.', 'elite-shipping' ); ?></p>
		</div>

		<div class="apex-paypal-modal-body">
			<label class="apex-paypal-modal-label" for="apex-paypal-purchase-amount"><?php esc_html_e( 'How much is your purchase?', 'elite-shipping' ); ?></label>
			<div class="apex-paypal-modal-amount">
				<span class="apex-paypal-modal-amount-label"><?php esc_html_e( 'Purchase amount', 'elite-shipping' ); ?></span>
				<input
					id="apex-paypal-purchase-amount"
					class="apex-paypal-modal-amount-input"
					type="text"
					inputmode="decimal"
					value="<?php echo esc_attr( $price_label ); ?>"
					data-paypal-amount-input
					data-paypal-amount-raw="<?php echo esc_attr( (string) $price_raw ); ?>"
				>
			</div>

			<div class="apex-paypal-modal-plans" data-paypal-plans></div>

			<p class="apex-paypal-modal-disclaimer">
				<?php esc_html_e( '*Fixed APR is 0% to 35.99% based on the cardholder\'s creditworthiness, determined when the application is submitted. Subject to credit approval. Terms and conditions apply.', 'elite-shipping' ); ?>
			</p>

			<ol class="apex-paypal-modal-steps">
				<li><?php echo wp_kses_post( __( 'Choose PayPal at checkout to pay later with <strong>Pay Monthly</strong>.', 'elite-shipping' ) ); ?></li>
				<li><?php esc_html_e( 'Get a decision in seconds and complete your purchase.', 'elite-shipping' ); ?></li>
				<li><?php esc_html_e( 'Use autopay for your payments. It\'s easy!', 'elite-shipping' ); ?></li>
			</ol>

			<p class="apex-paypal-modal-legal">
				<?php
				echo wp_kses_post(
					__(
						'Pay Monthly is subject to consumer credit approval and eligibility. Payments may change based on shipping, taxes, updates to your purchase, or missed payments. Availability depends on the merchant and may not be available for subscriptions or recurring payments. Pay Monthly is currently not available to residents of AK, CT, HI, or WA. You must be 18 years old or older to apply. Missed payments may have an impact on your credit score. The lender for Pay Monthly is WebBank. PayPal, Inc. (NMLS #910457): RI Loan Broker Licensee. VT Loan Solicitation Licensee. VT residents: <a href="https://www.paypal.com/uk/webapps/mpp/paypal-payin4" target="_blank" rel="noopener noreferrer">Find more disclosures</a> by going to PayPal\'s page on Pay Later.',
						'elite-shipping'
					)
				);
				?>
			</p>

			<p class="apex-paypal-modal-more">
				<a href="https://www.paypal.com/uk/webapps/mpp/paypal-payin4" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'See other ways to pay over time', 'elite-shipping' ); ?>
				</a>
			</p>
		</div>
	</div>
</div>
