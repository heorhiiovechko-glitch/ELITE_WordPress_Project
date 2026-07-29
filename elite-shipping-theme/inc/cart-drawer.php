<?php
/**
 * Fixed cart button + slide-out mini cart drawer.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the cart drawer should render on this request.
 *
 * @return bool
 */
function elite_shipping_cart_drawer_enabled() {
	return class_exists( 'WooCommerce' ) && ! is_admin();
}

/**
 * Cart item count for the floating button badge.
 *
 * @return int
 */
function elite_shipping_get_cart_drawer_count() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return 0;
	}

	return (int) WC()->cart->get_cart_contents_count();
}

/**
 * Render mini cart line items (drawer body).
 */
function elite_shipping_render_cart_drawer_items() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	if ( WC()->cart->is_empty() ) {
		echo '<div class="elite-cart-drawer-empty">';
		echo '<p>' . esc_html__( 'Your cart is currently empty.', 'elite-shipping' ) . '</p>';
		if ( function_exists( 'wc_get_page_permalink' ) ) {
			echo '<a class="elite-cart-drawer-empty-link" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'Browse products', 'elite-shipping' ) . '</a>';
		}
		echo '</div>';
		return;
	}

	echo '<ul class="elite-cart-drawer-items" role="list">';

	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( ! $product || ! $product->exists() || $cart_item['quantity'] <= 0 ) {
			continue;
		}

		$product_name = apply_filters( 'woocommerce_cart_item_name', $product->get_name(), $cart_item, $cart_item_key );
		$permalink    = apply_filters( 'woocommerce_cart_item_permalink', $product->is_visible() ? $product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
		$thumbnail    = apply_filters( 'woocommerce_cart_item_thumbnail', $product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
		$price_html   = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
		?>
		<li class="elite-cart-drawer-item woocommerce-mini-cart-item">
			<?php
			echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'woocommerce_cart_item_remove_link',
				sprintf(
					'<a role="button" href="%s" class="remove remove_from_cart_button elite-cart-drawer-item-remove" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
					esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
					esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
					esc_attr( (string) $product->get_id() ),
					esc_attr( $cart_item_key ),
					esc_attr( $product->get_sku() )
				),
				$cart_item_key
			);
			?>
			<div class="elite-cart-drawer-item-media">
				<?php if ( $permalink ) : ?>
					<a href="<?php echo esc_url( $permalink ); ?>"><?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php else : ?>
					<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
			</div>
			<div class="elite-cart-drawer-item-body">
				<p class="elite-cart-drawer-item-name">
					<?php if ( $permalink ) : ?>
						<a href="<?php echo esc_url( $permalink ); ?>"><?php echo wp_kses_post( $product_name ); ?></a>
					<?php else : ?>
						<?php echo wp_kses_post( $product_name ); ?>
					<?php endif; ?>
				</p>
				<p class="elite-cart-drawer-item-meta">
					<span class="elite-cart-drawer-item-qty"><?php echo esc_html( (string) $cart_item['quantity'] ); ?></span>
					<span aria-hidden="true">&times;</span>
					<span class="elite-cart-drawer-item-price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				</p>
			</div>
		</li>
		<?php
	}

	echo '</ul>';
}

/**
 * Render drawer subtotal footer.
 */
function elite_shipping_render_cart_drawer_footer() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}

	$cart_url     = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
	$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' );
	?>
	<div class="elite-cart-drawer-footer">
		<div class="elite-cart-drawer-subtotal">
			<span class="elite-cart-drawer-subtotal-label"><?php esc_html_e( 'Subtotal:', 'elite-shipping' ); ?></span>
			<span class="elite-cart-drawer-subtotal-value"><?php wc_cart_totals_subtotal_html(); ?></span>
		</div>
		<div class="elite-cart-drawer-actions">
			<a class="elite-cart-drawer-btn elite-cart-drawer-btn--cart" href="<?php echo esc_url( $cart_url ); ?>">
				<?php esc_html_e( 'View cart', 'elite-shipping' ); ?>
			</a>
			<a class="elite-cart-drawer-btn elite-cart-drawer-btn--checkout" href="<?php echo esc_url( $checkout_url ); ?>">
				<?php esc_html_e( 'Checkout', 'elite-shipping' ); ?>
			</a>
		</div>
	</div>
	<?php
}

/**
 * Render replaceable drawer content wrapper.
 */
function elite_shipping_render_cart_drawer_content() {
	?>
	<div class="elite-cart-drawer-content">
		<div class="elite-cart-drawer-items-wrap">
			<?php elite_shipping_render_cart_drawer_items(); ?>
		</div>
		<?php elite_shipping_render_cart_drawer_footer(); ?>
	</div>
	<?php
}

/**
 * Floating cart button markup.
 */
function elite_shipping_render_cart_fab() {
	if ( ! elite_shipping_cart_drawer_enabled() ) {
		return;
	}

	$count = elite_shipping_get_cart_drawer_count();
	?>
	<button
		type="button"
		class="elite-cart-fab js-elite-cart-drawer-open"
		aria-label="<?php echo esc_attr( sprintf( _n( 'Open shopping cart, %d item', 'Open shopping cart, %d items', $count, 'elite-shipping' ), $count ) ); ?>"
		aria-controls="elite-cart-drawer"
		aria-expanded="false"
	>
		<span class="elite-cart-fab-shell" aria-hidden="true">
			<svg class="elite-cart-fab-icon" width="22" height="22" viewBox="0 0 640 640" fill="currentColor" aria-hidden="true">
				<path d="M0 72C0 58.7 10.7 48 24 48L69.3 48C96.4 48 119.6 67.4 124.4 94L124.8 96L537.5 96C557.5 96 572.6 114.2 568.9 133.9L537.8 299.8C532.1 330.1 505.7 352 474.9 352L171.3 352L176.4 380.3C178.5 391.7 188.4 400 200 400L456 400C469.3 400 480 410.7 480 424C480 437.3 469.3 448 456 448L200.1 448C165.3 448 135.5 423.1 129.3 388.9L77.2 102.6C76.5 98.8 73.2 96 69.3 96L24 96C10.7 96 0 85.3 0 72zM160 528C160 501.5 181.5 480 208 480C234.5 480 256 501.5 256 528C256 554.5 234.5 576 208 576C181.5 576 160 554.5 160 528zM384 528C384 501.5 405.5 480 432 480C458.5 480 480 501.5 480 528C480 554.5 458.5 576 432 576C405.5 576 384 554.5 384 528zM336 142.4C322.7 142.4 312 153.1 312 166.4L312 200L278.4 200C265.1 200 254.4 210.7 254.4 224C254.4 237.3 265.1 248 278.4 248L312 248L312 281.6C312 294.9 322.7 305.6 336 305.6C349.3 305.6 360 294.9 360 281.6L360 248L393.6 248C406.9 248 417.6 237.3 417.6 224C417.6 210.7 406.9 200 393.6 200L360 200L360 166.4C360 153.1 349.3 142.4 336 142.4z"/>
			</svg>
		</span>
		<span class="elite-cart-fab-count<?php echo $count ? '' : ' is-empty'; ?>" aria-hidden="true"><?php echo esc_html( (string) $count ); ?></span>
	</button>
	<?php
}

/**
 * Render cart drawer shell in the footer.
 */
function elite_shipping_render_cart_drawer() {
	if ( ! elite_shipping_cart_drawer_enabled() ) {
		return;
	}
	?>
	<div class="elite-cart-drawer-overlay" id="elite-cart-drawer-overlay" hidden aria-hidden="true"></div>
	<aside class="elite-cart-drawer" id="elite-cart-drawer" aria-label="<?php esc_attr_e( 'Shopping cart', 'elite-shipping' ); ?>" hidden aria-hidden="true">
		<div class="elite-cart-drawer-head">
			<h2 class="elite-cart-drawer-title"><?php esc_html_e( 'Shopping cart', 'elite-shipping' ); ?></h2>
			<button type="button" class="elite-cart-drawer-close" aria-label="<?php esc_attr_e( 'Close', 'elite-shipping' ); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
				<span><?php esc_html_e( 'Close', 'elite-shipping' ); ?></span>
			</button>
		</div>
		<?php elite_shipping_render_cart_drawer_content(); ?>
	</aside>
	<?php
	elite_shipping_render_cart_fab();
}
add_action( 'wp_footer', 'elite_shipping_render_cart_drawer', 25 );

/**
 * WooCommerce fragment refresh for drawer content and badge.
 *
 * @param array<string, string> $fragments Cart fragments.
 * @return array<string, string>
 */
function elite_shipping_cart_drawer_fragments( $fragments ) {
	if ( ! elite_shipping_cart_drawer_enabled() ) {
		return $fragments;
	}

	ob_start();
	elite_shipping_render_cart_drawer_content();
	$fragments['div.elite-cart-drawer-content'] = ob_get_clean();

	$count = elite_shipping_get_cart_drawer_count();
	$fragments['span.elite-cart-fab-count']     = '<span class="elite-cart-fab-count' . ( $count ? '' : ' is-empty' ) . '" aria-hidden="true">' . esc_html( (string) $count ) . '</span>';

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'elite_shipping_cart_drawer_fragments' );

/**
 * Enqueue WooCommerce cart fragments for AJAX mini cart updates.
 */
function elite_shipping_enqueue_cart_drawer_assets() {
	if ( ! elite_shipping_cart_drawer_enabled() ) {
		return;
	}

	wp_enqueue_script( 'wc-cart-fragments' );

	wp_localize_script(
		'elite-shipping-main',
		'eliteShippingCartDrawer',
		array(
			'cartUrl'     => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ),
			'checkoutUrl' => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'elite_shipping_enqueue_cart_drawer_assets', 30 );
