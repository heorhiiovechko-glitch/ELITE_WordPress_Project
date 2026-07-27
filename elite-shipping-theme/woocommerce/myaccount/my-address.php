<?php
/**
 * My Addresses — card layout.
 *
 * @package Elite_Shipping
 * @see     woocommerce/templates/myaccount/my-address.php
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Billing address', 'woocommerce' ),
			'shipping' => __( 'Shipping address', 'woocommerce' ),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __( 'Billing address', 'woocommerce' ),
		),
		$customer_id
	);
}

$address_icons = array(
	'billing'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>',
	'shipping' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-5.3 7-12a7 7 0 1 0-14 0c0 6.7 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/></svg>',
);
?>

<div class="apex-account-addresses">
	<p class="apex-account-addresses-intro">
		<?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</p>

	<div class="apex-account-addresses-grid woocommerce-Addresses addresses col2-set">
		<?php foreach ( $get_addresses as $name => $address_title ) : ?>
			<?php
			$address = wc_get_account_formatted_address( $name );
			$col     = ( 'billing' === $name ) ? 1 : 2;
			?>
			<div class="apex-account-address-card woocommerce-Address col-<?php echo (int) $col; ?> address">
				<header class="woocommerce-Address-title title apex-account-address-card__head">
					<div class="apex-account-address-card__title">
						<span class="apex-account-address-card__icon" aria-hidden="true">
							<?php echo isset( $address_icons[ $name ] ) ? $address_icons[ $name ] : $address_icons['billing']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<h3><?php echo esc_html( $address_title ); ?></h3>
					</div>
					<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="apex-account-address-card__action edit">
						<?php
						printf(
							/* translators: %s: Address title */
							$address ? esc_html__( 'Edit %s', 'woocommerce' ) : esc_html__( 'Add %s', 'woocommerce' ),
							esc_html( $address_title )
						);
						?>
					</a>
				</header>
				<address class="apex-account-address-card__body">
					<?php
					echo $address
						? wp_kses_post( $address )
						: esc_html__( 'You have not set up this type of address yet.', 'woocommerce' );
					?>
				</address>
			</div>
		<?php endforeach; ?>
	</div>
</div>
