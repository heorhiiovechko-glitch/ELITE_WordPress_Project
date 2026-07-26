<?php
/**
 * WooCommerce checkout layout and trust elements.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checkout progress steps.
 */
function elite_shipping_checkout_progress_steps() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return;
	}

	$is_thankyou = function_exists( 'is_order_received_page' ) && is_order_received_page();
	?>
	<nav class="apex-checkout-steps" aria-label="<?php esc_attr_e( 'Checkout progress', 'elite-shipping' ); ?>">
		<a class="apex-checkout-step<?php echo $is_thankyou ? ' is-complete' : ''; ?>" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
			<span class="apex-checkout-step-num">1</span>
			<span class="apex-checkout-step-label"><?php esc_html_e( 'Cart', 'elite-shipping' ); ?></span>
		</a>
		<span class="apex-checkout-step-divider" aria-hidden="true"></span>
		<?php if ( $is_thankyou ) : ?>
			<span class="apex-checkout-step is-complete">
				<span class="apex-checkout-step-num">2</span>
				<span class="apex-checkout-step-label"><?php esc_html_e( 'Checkout', 'elite-shipping' ); ?></span>
			</span>
		<?php else : ?>
			<span class="apex-checkout-step is-active" aria-current="step">
				<span class="apex-checkout-step-num">2</span>
				<span class="apex-checkout-step-label"><?php esc_html_e( 'Checkout', 'elite-shipping' ); ?></span>
			</span>
		<?php endif; ?>
		<span class="apex-checkout-step-divider" aria-hidden="true"></span>
		<span class="apex-checkout-step<?php echo $is_thankyou ? ' is-active' : ''; ?>"<?php echo $is_thankyou ? ' aria-current="step"' : ''; ?>>
			<span class="apex-checkout-step-num">3</span>
			<span class="apex-checkout-step-label"><?php esc_html_e( 'Confirmation', 'elite-shipping' ); ?></span>
		</span>
	</nav>
	<?php
}

/**
 * Trust panel for checkout and cart.
 *
 * @param string $placement Optional placement modifier for responsive positioning.
 */
function elite_shipping_checkout_trust_sidebar( $placement = '' ) {
	if ( ! function_exists( 'elite_shipping_get_payments_trust_image_url' ) ) {
		return;
	}

	$contact = function_exists( 'elite_shipping_get_contact_details' ) ? elite_shipping_get_contact_details() : array();
	$phone   = isset( $contact['phone'] ) ? $contact['phone'] : '';
	$email   = isset( $contact['email'] ) ? $contact['email'] : '';
	$classes = 'apex-checkout-trust';

	if ( $placement ) {
		$classes .= ' apex-checkout-trust--' . sanitize_html_class( $placement );
	}
	?>
	<div class="<?php echo esc_attr( $classes ); ?>">
		<div class="apex-checkout-trust-item">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
				<path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z"/>
				<path d="M8.5 12.5l2.5 2.5 5-5" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
			<div>
				<strong><?php esc_html_e( 'Secure Checkout', 'elite-shipping' ); ?></strong>
				<span><?php esc_html_e( '256-bit SSL encryption', 'elite-shipping' ); ?></span>
			</div>
		</div>
		<div class="apex-checkout-trust-item">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
				<rect x="2" y="7" width="20" height="14" rx="1.5"/>
				<path d="M2 12h20"/>
			</svg>
			<div>
				<strong><?php esc_html_e( 'UK Nationwide Delivery', 'elite-shipping' ); ?></strong>
				<span><?php esc_html_e( 'Fast & reliable shipping', 'elite-shipping' ); ?></span>
			</div>
		</div>
		<div class="apex-checkout-trust-item">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
				<path d="M4 14v3a2 2 0 0 0 2 2h1"/>
				<path d="M20 14v3a2 2 0 0 1-2 2h-1"/>
				<path d="M4 14a8 8 0 0 1 16 0"/>
			</svg>
			<div>
				<strong><?php esc_html_e( 'Expert Support', 'elite-shipping' ); ?></strong>
				<span><?php esc_html_e( 'We are here to help', 'elite-shipping' ); ?></span>
			</div>
		</div>
		<div class="apex-checkout-trust-payments">
			<img
				src="<?php echo esc_url( elite_shipping_get_payments_trust_image_url() ); ?>"
				alt="<?php esc_attr_e( 'Accepted payment methods', 'elite-shipping' ); ?>"
				width="420"
				height="105"
				loading="lazy"
				decoding="async"
			>
		</div>
		<?php if ( $phone || $email ) : ?>
			<p class="apex-checkout-trust-help">
				<?php esc_html_e( 'Need help?', 'elite-shipping' ); ?>
				<?php if ( $phone ) : ?>
					<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Trust panel below billing/shipping on desktop checkout.
 */
function elite_shipping_checkout_trust_after_details() {
	elite_shipping_checkout_trust_sidebar( 'after-details' );
}

/**
 * Trust panel below the order card on mobile checkout.
 */
function elite_shipping_checkout_trust_after_order() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_order_received_page() ) {
		return;
	}

	elite_shipping_checkout_trust_sidebar( 'after-order' );
}

/**
 * Custom coupon toggle + form (replaces default WooCommerce markup).
 */
function elite_shipping_checkout_coupon_form() {
	if ( ! wc_coupons_enabled() ) {
		return;
	}
	?>
	<div class="apex-checkout-coupon-wrap">
		<div class="apex-checkout-coupon-toggle woocommerce-form-coupon-toggle">
			<svg class="apex-checkout-coupon-ico" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
				<path d="M20 12v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6"/>
				<path d="M4 8h16"/>
				<path d="M9 12h6"/>
				<path d="M7 4v4"/>
				<path d="M17 4v4"/>
			</svg>
			<p class="apex-checkout-coupon-text">
				<?php esc_html_e( 'Have a coupon?', 'elite-shipping' ); ?>
				<a href="#" class="showcoupon" role="button"><?php esc_html_e( 'Click here to enter your code', 'elite-shipping' ); ?></a>
			</p>
		</div>
		<form class="checkout_coupon woocommerce-form-coupon apex-checkout-coupon-form" method="post" style="display:none">
			<p class="apex-checkout-coupon-field">
				<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
				<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Enter coupon code', 'elite-shipping' ); ?>" id="coupon_code" value="" autocomplete="off" />
				<button type="submit" class="button apex-checkout-coupon-apply" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>">
					<?php esc_html_e( 'Apply', 'elite-shipping' ); ?>
				</button>
			</p>
		</form>
	</div>
	<?php
}

/**
 * Open checkout top bar (steps + coupon).
 */
function elite_shipping_checkout_top_open() {
	echo '<div class="apex-checkout-top">';
}

/**
 * Close checkout top bar.
 */
function elite_shipping_checkout_top_close() {
	echo '</div>';
}

/**
 * Register checkout hooks.
 */
function elite_shipping_checkout_hooks() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	add_action( 'woocommerce_before_checkout_form', 'elite_shipping_checkout_top_open', 4 );
	add_action( 'woocommerce_before_checkout_form', 'elite_shipping_checkout_progress_steps', 5 );
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
	add_action( 'woocommerce_before_checkout_form', 'elite_shipping_checkout_coupon_form', 15 );
	add_action( 'woocommerce_before_checkout_form', 'elite_shipping_checkout_top_close', 20 );
	add_action( 'woocommerce_checkout_after_customer_details', 'elite_shipping_checkout_trust_after_details', 10 );
	add_action( 'elite_checkout_after_order_card', 'elite_shipping_checkout_trust_after_order', 10 );
	add_action( 'woocommerce_review_order_after_submit', 'elite_shipping_checkout_support_button', 10 );
}
add_action( 'after_setup_theme', 'elite_shipping_checkout_hooks', 25 );

/**
 * Rename Shipping → Shipment on checkout totals.
 *
 * @param string $translated Translated text.
 * @param string $text       Original text.
 * @param string $domain     Text domain.
 * @return string
 */
function elite_shipping_checkout_shipping_label( $translated, $text, $domain ) {
	if ( 'woocommerce' === $domain && function_exists( 'is_checkout' ) && is_checkout() && 'Shipping' === $text ) {
		if ( function_exists( 'elite_shipping_cart_has_containers' ) && elite_shipping_cart_has_containers() ) {
			return elite_shipping_container_delivery_fee_label();
		}

		return __( 'Shipment', 'elite-shipping' );
	}

	return $translated;
}
add_filter( 'gettext', 'elite_shipping_checkout_shipping_label', 10, 3 );

/**
 * Support button below Place order.
 */
function elite_shipping_checkout_support_button() {
	$urls = elite_shipping_get_urls();
	?>
	<a class="apex-checkout-support-btn" href="<?php echo esc_url( $urls['contact'] ); ?>">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
			<path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
		</svg>
		<?php esc_html_e( 'For any payment issue or problem, get in touch', 'elite-shipping' ); ?>
	</a>
	<?php
}

/**
 * AJAX: update cart quantity from checkout sidebar.
 */
function elite_shipping_checkout_update_qty() {
	check_ajax_referer( 'elite_checkout_qty', 'nonce' );

	$cart_key = isset( $_POST['cart_key'] ) ? wc_clean( wp_unslash( $_POST['cart_key'] ) ) : '';
	$quantity = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 0;

	if ( ! $cart_key || ! WC()->cart ) {
		wp_send_json_error();
	}

	if ( $quantity <= 0 ) {
		WC()->cart->remove_cart_item( $cart_key );
	} else {
		WC()->cart->set_quantity( $cart_key, $quantity, true );
	}

	WC()->cart->calculate_totals();
	wp_send_json_success();
}
add_action( 'wp_ajax_elite_checkout_update_qty', 'elite_shipping_checkout_update_qty' );
add_action( 'wp_ajax_nopriv_elite_checkout_update_qty', 'elite_shipping_checkout_update_qty' );

/**
 * Localize checkout AJAX vars.
 */
function elite_shipping_checkout_scripts() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return;
	}

	wp_localize_script(
		'elite-shipping-main',
		'eliteCheckout',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'elite_checkout_qty' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'elite_shipping_checkout_scripts', 30 );

/**
 * Custom add-to-cart success notice markup.
 *
 * @param string       $message  Default message HTML.
 * @param array<int,int> $products Product ID => quantity added.
 * @return string
 */
function elite_shipping_add_to_cart_message_html( $message, $products ) {
	if ( empty( $products ) || ! function_exists( 'wc_get_cart_url' ) ) {
		return $message;
	}

	$titles = array();

	foreach ( $products as $product_id => $qty ) {
		$product_id = absint( $product_id );
		if ( ! $product_id ) {
			continue;
		}

		$title = get_the_title( $product_id );
		if ( $title ) {
			$titles[] = $title;
		}
	}

	if ( empty( $titles ) ) {
		return $message;
	}

	$label = implode( ', ', $titles );
	ob_start();
	?>
	<div class="apex-cart-notice">
		<div class="apex-cart-notice-icon" aria-hidden="true">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M5 12l4 4L19 6"/>
			</svg>
		</div>
		<div class="apex-cart-notice-body">
			<p class="apex-cart-notice-message">
				<strong class="apex-cart-notice-product"><?php echo esc_html( $label ); ?></strong>
				<span class="apex-cart-notice-text"><?php esc_html_e( 'has been added to your cart.', 'elite-shipping' ); ?></span>
			</p>
		</div>
		<a class="button wc-forward apex-cart-notice-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
			<?php esc_html_e( 'View cart', 'elite-shipping' ); ?>
		</a>
	</div>
	<?php
	return ob_get_clean();
}
add_filter( 'wc_add_to_cart_message_html', 'elite_shipping_add_to_cart_message_html', 10, 2 );

/**
 * Parse WooCommerce removed-item notice HTML.
 *
 * @param string $html Default notice HTML.
 * @return array{name: string, undo_url: string}
 */
function elite_shipping_parse_removed_notice( $html ) {
	$parsed = array(
		'name'     => '',
		'undo_url' => '',
	);

	if ( preg_match( '/<a[^>]*class=["\'][^"\']*restore-item[^"\']*["\'][^>]*href=["\']([^"\']+)["\']/i', $html, $undo_match )
		|| preg_match( '/<a[^>]*href=["\']([^"\']+)["\'][^>]*class=["\'][^"\']*restore-item/i', $html, $undo_match ) ) {
		$parsed['undo_url'] = html_entity_decode( $undo_match[1] );
	}

	$plain = html_entity_decode( wp_strip_all_tags( $html ) );
	if ( preg_match( '/["“](.+?)["”]\s*removed\.?/iu', $plain, $name_match ) ) {
		$parsed['name'] = trim( $name_match[1] );
	}

	return $parsed;
}

/**
 * Custom removed-from-cart notice markup.
 *
 * @param string $default_html Default WooCommerce notice HTML.
 * @return string
 */
function elite_shipping_build_removed_notice_html( $default_html ) {
	$parsed = elite_shipping_parse_removed_notice( $default_html );

	if ( '' === $parsed['name'] ) {
		return $default_html;
	}

	ob_start();
	?>
	<div class="apex-cart-notice apex-cart-notice--removed">
		<div class="apex-cart-notice-icon apex-cart-notice-icon--removed" aria-hidden="true">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M5 12h14"/>
			</svg>
		</div>
		<div class="apex-cart-notice-body">
			<p class="apex-cart-notice-message">
				<strong class="apex-cart-notice-product"><?php echo esc_html( $parsed['name'] ); ?></strong>
				<span class="apex-cart-notice-text"><?php esc_html_e( 'removed.', 'elite-shipping' ); ?></span>
			</p>
		</div>
		<?php if ( $parsed['undo_url'] ) : ?>
			<a class="apex-cart-notice-undo restore-item" href="<?php echo esc_url( $parsed['undo_url'] ); ?>">
				<?php esc_html_e( 'Undo?', 'elite-shipping' ); ?>
			</a>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Replace default removed-item notices with custom markup.
 */
function elite_shipping_transform_cart_notices() {
	if ( ! function_exists( 'wc_get_notices' ) ) {
		return;
	}

	foreach ( array( 'success', 'notice', 'error' ) as $type ) {
		$notices = wc_get_notices( $type );
		if ( empty( $notices ) ) {
			continue;
		}

		wc_clear_notices( $type );

		foreach ( $notices as $notice ) {
			$html = isset( $notice['notice'] ) ? $notice['notice'] : '';
			$data = isset( $notice['data'] ) ? $notice['data'] : array();

			if ( false !== strpos( $html, 'restore-item' ) && false === strpos( $html, 'apex-cart-notice--removed' ) ) {
				$html = elite_shipping_build_removed_notice_html( $html );
			}

			wc_add_notice( $html, $type, $data );
		}
	}
}
add_action( 'woocommerce_before_checkout_form', 'elite_shipping_transform_cart_notices', 1 );
add_action( 'woocommerce_before_cart', 'elite_shipping_transform_cart_notices', 1 );

/**
 * Keep checkout AJAX notice fragment styled after cart updates.
 *
 * @param array<string, string> $fragments Checkout fragments.
 * @return array<string, string>
 */
function elite_shipping_checkout_notice_fragments( $fragments ) {
	elite_shipping_transform_cart_notices();

	ob_start();
	woocommerce_output_all_notices();
	$fragments['.woocommerce-notices-wrapper'] = ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_update_order_review_fragments', 'elite_shipping_checkout_notice_fragments' );
