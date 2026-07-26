<?php
/**
 * Empty cart page
 *
 * @package Elite_Shipping
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_cart_is_empty' );

if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
	<div class="apex-cart-empty">
		<div class="apex-cart-empty-icon" aria-hidden="true">
			<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
				<circle cx="9" cy="21" r="1"/>
				<circle cx="20" cy="21" r="1"/>
				<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
			</svg>
		</div>
		<p class="apex-cart-empty-text cart-empty woocommerce-info"><?php esc_html_e( 'Your cart is currently empty.', 'woocommerce' ); ?></p>
		<a class="button wc-backward apex-cart-empty-btn" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Return to shop', 'woocommerce' ); ?>
		</a>
	</div>
<?php endif; ?>
