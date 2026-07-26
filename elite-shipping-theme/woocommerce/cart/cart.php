<?php
/**
 * Cart Page
 *
 * @package Elite_Shipping
 * @version 7.9.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="apex-cart-layout">
	<div class="apex-cart-main-col">
		<form class="woocommerce-cart-form apex-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<?php do_action( 'woocommerce_before_cart_table' ); ?>

			<div class="apex-cart-card">
				<h2 class="apex-cart-title apex-cart-title--center"><?php esc_html_e( 'Your Cart', 'elite-shipping' ); ?></h2>

				<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents apex-cart-table apex-checkout-order-table" cellspacing="0">
					<thead>
						<tr>
							<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
							<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php do_action( 'woocommerce_before_cart_contents' ); ?>

						<?php
						foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
							$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

							if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
								$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
								$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
								$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
								$sku               = $_product->get_sku();
								$max_qty           = $_product->get_max_purchase_quantity();
								?>
								<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item apex-cart-item', $cart_item, $cart_item_key ) ); ?>">
									<td class="apex-checkout-item-cell" colspan="2">
										<div class="apex-checkout-item-row">
											<a
												class="apex-checkout-item-remove"
												href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>"
												aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ); ?>"
											>&times;</a>

											<div class="apex-checkout-item-thumb">
												<?php
												if ( $product_permalink ) {
													printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												} else {
													echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												}
												?>
											</div>

											<div class="apex-checkout-item-info">
												<div class="apex-checkout-item-name">
													<?php
													if ( $product_permalink ) {
														printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $product_name ) );
													} else {
														echo wp_kses_post( $product_name );
													}
													?>
												</div>
												<div class="apex-checkout-item-sku">
													<?php
													printf(
														/* translators: %s: product SKU */
														esc_html__( 'SKU: %s', 'elite-shipping' ),
														$sku ? esc_html( $sku ) : esc_html__( 'N/A', 'elite-shipping' )
													);
													?>
												</div>

												<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

												<?php if ( $_product->is_sold_individually() ) : ?>
													<input type="hidden" name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]" value="1" />
													<div class="apex-cart-qty-note"><?php esc_html_e( 'Qty: 1', 'elite-shipping' ); ?></div>
												<?php else : ?>
													<div class="apex-checkout-qty-wrap">
														<button type="button" class="apex-checkout-qty-btn apex-cart-qty-btn" data-action="minus" aria-label="<?php esc_attr_e( 'Decrease quantity', 'elite-shipping' ); ?>">−</button>
														<input
															type="number"
															class="apex-checkout-qty apex-cart-qty"
															name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]"
															value="<?php echo esc_attr( $cart_item['quantity'] ); ?>"
															min="0"
															<?php echo $max_qty > 0 ? 'max="' . esc_attr( $max_qty ) . '"' : ''; ?>
															aria-label="<?php esc_attr_e( 'Quantity', 'elite-shipping' ); ?>"
														>
														<button type="button" class="apex-checkout-qty-btn apex-cart-qty-btn" data-action="plus" aria-label="<?php esc_attr_e( 'Increase quantity', 'elite-shipping' ); ?>">+</button>
													</div>
												<?php endif; ?>
											</div>

											<div class="apex-checkout-item-price">
												<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											</div>
										</div>
									</td>
								</tr>
								<?php
							}
						}
						?>

						<?php do_action( 'woocommerce_cart_contents' ); ?>
						<?php do_action( 'woocommerce_after_cart_contents' ); ?>
					</tbody>
				</table>

				<div class="apex-cart-footer">
					<?php if ( wc_coupons_enabled() ) : ?>
						<div class="coupon apex-cart-coupon">
							<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
							<div class="apex-cart-coupon-field">
								<input type="text" name="coupon_code" class="input-text apex-cart-coupon-input" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Enter coupon code', 'elite-shipping' ); ?>" />
								<button type="submit" class="button apex-cart-coupon-btn" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>">
									<?php esc_html_e( 'Apply', 'elite-shipping' ); ?>
								</button>
							</div>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php endif; ?>

					<div class="apex-cart-footer-actions">
						<button type="submit" class="button apex-cart-update-btn" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>">
							<svg class="apex-cart-update-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
								<path d="M21 12a9 9 0 1 1-2.64-6.36"/>
								<polyline points="21 3 21 9 15 9"/>
							</svg>
							<?php esc_html_e( 'Update cart', 'elite-shipping' ); ?>
						</button>
						<?php do_action( 'woocommerce_cart_actions' ); ?>
					</div>

					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</div>

				<?php do_action( 'woocommerce_after_cart_table' ); ?>
			</div>
		</form>
	</div>

	<aside class="apex-cart-sidebar">
		<div class="cart-collaterals apex-cart-collaterals">
			<?php do_action( 'woocommerce_cart_collaterals' ); ?>
		</div>

		<?php
		if ( function_exists( 'elite_shipping_checkout_trust_sidebar' ) ) {
			elite_shipping_checkout_trust_sidebar();
		}
		?>
	</aside>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
