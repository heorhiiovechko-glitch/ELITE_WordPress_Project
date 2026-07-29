<?php
/**
 * Native wishlist: storage, AJAX, page helpers.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELITE_WISHLIST_COOKIE', 'elite_wishlist' );
define( 'ELITE_WISHLIST_META', '_elite_wishlist_ids' );

/**
 * Normalize product ID list.
 *
 * @param mixed $ids Raw IDs.
 * @return int[]
 */
function elite_shipping_normalize_wishlist_ids( $ids ) {
	if ( is_string( $ids ) ) {
		$ids = explode( ',', $ids );
	}

	if ( ! is_array( $ids ) ) {
		return array();
	}

	$ids = array_map( 'absint', $ids );
	$ids = array_filter( $ids );
	$ids = array_values( array_unique( $ids ) );

	return $ids;
}

/**
 * Keep only published products.
 *
 * @param int[] $ids Product IDs.
 * @return int[]
 */
function elite_shipping_filter_wishlist_product_ids( $ids ) {
	$ids = elite_shipping_normalize_wishlist_ids( $ids );
	$out = array();

	foreach ( $ids as $id ) {
		if ( 'product' !== get_post_type( $id ) ) {
			continue;
		}
		if ( 'publish' !== get_post_status( $id ) ) {
			continue;
		}
		$out[] = $id;
	}

	return $out;
}

/**
 * Read wishlist IDs from cookie.
 *
 * @return int[]
 */
function elite_shipping_read_wishlist_cookie() {
	if ( empty( $_COOKIE[ ELITE_WISHLIST_COOKIE ] ) ) {
		return array();
	}

	$raw = sanitize_text_field( wp_unslash( (string) $_COOKIE[ ELITE_WISHLIST_COOKIE ] ) );
	if ( '' === $raw ) {
		return array();
	}

	// Prefer pipe separator (commas can truncate in browsers). Fall back to legacy commas.
	$parts = ( false !== strpos( $raw, '|' ) ) ? explode( '|', $raw ) : explode( ',', $raw );

	return elite_shipping_normalize_wishlist_ids( $parts );
}

/**
 * Persist wishlist IDs to cookie.
 *
 * @param int[] $ids Product IDs.
 */
function elite_shipping_write_wishlist_cookie( $ids ) {
	$ids    = elite_shipping_normalize_wishlist_ids( $ids );
	$value  = implode( '|', $ids );
	$expire = time() + ( 3 * MONTH_IN_SECONDS );

	if ( function_exists( 'wc_setcookie' ) ) {
		wc_setcookie( ELITE_WISHLIST_COOKIE, $value, $expire, false, false );
	} else {
		$path   = defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/';
		$domain = defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '';
		setcookie( ELITE_WISHLIST_COOKIE, $value, $expire, $path, $domain, is_ssl(), false );
	}

	$_COOKIE[ ELITE_WISHLIST_COOKIE ] = $value;
}

/**
 * Current wishlist product IDs.
 *
 * @return int[]
 */
function elite_shipping_get_wishlist_ids() {
	$cookie_ids = elite_shipping_read_wishlist_cookie();
	$meta_ids   = array();

	if ( is_user_logged_in() ) {
		$meta = get_user_meta( get_current_user_id(), ELITE_WISHLIST_META, true );
		if ( is_array( $meta ) || is_string( $meta ) ) {
			$meta_ids = elite_shipping_normalize_wishlist_ids( $meta );
		}
	}

	return elite_shipping_filter_wishlist_product_ids( array_merge( $meta_ids, $cookie_ids ) );
}

/**
 * Save wishlist IDs (user meta + cookie).
 *
 * @param int[] $ids Product IDs.
 * @return int[]
 */
function elite_shipping_save_wishlist_ids( $ids ) {
	$ids = elite_shipping_filter_wishlist_product_ids( $ids );

	if ( is_user_logged_in() ) {
		update_user_meta( get_current_user_id(), ELITE_WISHLIST_META, $ids );
	}

	elite_shipping_write_wishlist_cookie( $ids );

	return $ids;
}

/**
 * Whether a product is in the wishlist.
 *
 * @param int $product_id Product ID.
 * @return bool
 */
function elite_shipping_is_product_in_wishlist( $product_id ) {
	$product_id = absint( $product_id );
	if ( ! $product_id ) {
		return false;
	}

	return in_array( $product_id, elite_shipping_get_wishlist_ids(), true );
}

/**
 * Add a product to the wishlist.
 *
 * @param int $product_id Product ID.
 * @return int[]
 */
function elite_shipping_wishlist_add( $product_id ) {
	$product_id = absint( $product_id );
	$ids        = elite_shipping_get_wishlist_ids();

	if ( $product_id && ! in_array( $product_id, $ids, true ) ) {
		$ids[] = $product_id;
	}

	return elite_shipping_save_wishlist_ids( $ids );
}

/**
 * Remove a product from the wishlist.
 *
 * @param int $product_id Product ID.
 * @return int[]
 */
function elite_shipping_wishlist_remove( $product_id ) {
	$product_id = absint( $product_id );
	$ids        = array_values(
		array_filter(
			elite_shipping_get_wishlist_ids(),
			static function ( $id ) use ( $product_id ) {
				return (int) $id !== $product_id;
			}
		)
	);

	return elite_shipping_save_wishlist_ids( $ids );
}

/**
 * Toggle a product in the wishlist.
 *
 * @param int $product_id Product ID.
 * @return array{ids:int[],in_wishlist:bool}
 */
function elite_shipping_wishlist_toggle( $product_id ) {
	$product_id = absint( $product_id );

	if ( elite_shipping_is_product_in_wishlist( $product_id ) ) {
		$ids = elite_shipping_wishlist_remove( $product_id );
		return array(
			'ids'          => $ids,
			'in_wishlist'  => false,
		);
	}

	$ids = elite_shipping_wishlist_add( $product_id );

	return array(
		'ids'         => $ids,
		'in_wishlist' => in_array( $product_id, $ids, true ),
	);
}

/**
 * Wishlist page URL.
 *
 * @return string
 */
function elite_shipping_get_wishlist_url() {
	$page = get_page_by_path( 'wishlist' );
	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}

	return home_url( '/wishlist/' );
}

/**
 * Wishlist item count.
 *
 * @return int
 */
function elite_shipping_get_wishlist_count() {
	return count( elite_shipping_get_wishlist_ids() );
}

/**
 * Add-to-wishlist URL (links to wishlist page; JS handles toggle).
 *
 * @param int $product_id Product ID.
 * @return string
 */
function elite_shipping_get_add_to_wishlist_url( $product_id ) {
	$product_id = absint( $product_id );

	return add_query_arg(
		array(
			'add_to_wishlist' => $product_id,
		),
		elite_shipping_get_wishlist_url()
	);
}

/**
 * Ensure the Wishlist page exists.
 */
function elite_shipping_ensure_wishlist_page() {
	if ( ! function_exists( 'wp_insert_post' ) ) {
		return;
	}

	$existing = get_page_by_path( 'wishlist' );
	if ( $existing instanceof WP_Post ) {
		return;
	}

	$result = wp_insert_post(
		array(
			'post_title'   => __( 'Wishlist', 'elite-shipping' ),
			'post_name'    => 'wishlist',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		),
		true
	);

	if ( ! is_wp_error( $result ) && $result ) {
		flush_rewrite_rules( false );
	}
}
add_action( 'after_setup_theme', 'elite_shipping_ensure_wishlist_page', 26 );

/**
 * Merge cookie wishlist into user meta on login.
 *
 * @param string  $user_login Username.
 * @param WP_User $user       User object.
 */
function elite_shipping_wishlist_merge_on_login( $user_login, $user ) {
	if ( ! $user instanceof WP_User ) {
		return;
	}

	$cookie_ids = elite_shipping_read_wishlist_cookie();
	$meta_ids   = get_user_meta( $user->ID, ELITE_WISHLIST_META, true );
	$meta_ids   = elite_shipping_normalize_wishlist_ids( $meta_ids );
	$merged     = elite_shipping_filter_wishlist_product_ids( array_merge( $meta_ids, $cookie_ids ) );

	update_user_meta( $user->ID, ELITE_WISHLIST_META, $merged );
	elite_shipping_write_wishlist_cookie( $merged );
}
add_action( 'wp_login', 'elite_shipping_wishlist_merge_on_login', 20, 2 );

/**
 * Handle non-JS add_to_wishlist query on wishlist page.
 */
function elite_shipping_wishlist_handle_query_add() {
	if ( empty( $_GET['add_to_wishlist'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$product_id = absint( wp_unslash( $_GET['add_to_wishlist'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $product_id ) {
		return;
	}

	elite_shipping_wishlist_add( $product_id );

	$redirect = remove_query_arg( 'add_to_wishlist' );
	wp_safe_redirect( $redirect ? $redirect : elite_shipping_get_wishlist_url() );
	exit;
}
add_action( 'template_redirect', 'elite_shipping_wishlist_handle_query_add', 5 );

/**
 * AJAX: toggle wishlist item.
 */
function elite_shipping_ajax_wishlist_toggle() {
	check_ajax_referer( 'elite_wishlist', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;
	if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Invalid product.', 'elite-shipping' ),
			),
			400
		);
	}

	$result = elite_shipping_wishlist_toggle( $product_id );
	$count  = count( $result['ids'] );

	wp_send_json_success(
		array(
			'product_id'  => $product_id,
			'in_wishlist' => (bool) $result['in_wishlist'],
			'count'       => $count,
			'ids'         => $result['ids'],
			'message'     => $result['in_wishlist']
				? __( 'Added to wishlist.', 'elite-shipping' )
				: __( 'Removed from wishlist.', 'elite-shipping' ),
			'label'       => $result['in_wishlist']
				? __( 'Remove from wishlist', 'elite-shipping' )
				: __( 'Add to wishlist', 'elite-shipping' ),
			'wishlistUrl' => elite_shipping_get_wishlist_url(),
		)
	);
}
add_action( 'wp_ajax_elite_wishlist_toggle', 'elite_shipping_ajax_wishlist_toggle' );
add_action( 'wp_ajax_nopriv_elite_wishlist_toggle', 'elite_shipping_ajax_wishlist_toggle' );

/**
 * AJAX: clear wishlist.
 */
function elite_shipping_ajax_wishlist_clear() {
	check_ajax_referer( 'elite_wishlist', 'nonce' );

	elite_shipping_save_wishlist_ids( array() );

	wp_send_json_success(
		array(
			'count'   => 0,
			'ids'     => array(),
			'message' => __( 'Wishlist cleared.', 'elite-shipping' ),
		)
	);
}
add_action( 'wp_ajax_elite_wishlist_clear', 'elite_shipping_ajax_wishlist_clear' );
add_action( 'wp_ajax_nopriv_elite_wishlist_clear', 'elite_shipping_ajax_wishlist_clear' );

/**
 * When a product is added to cart, remove it from the wishlist (move to cart).
 *
 * @param string $cart_item_key  Cart item key.
 * @param int    $product_id     Product ID.
 * @param int    $quantity       Quantity.
 * @param int    $variation_id   Variation ID.
 * @param array  $variation      Variation data.
 * @param array  $cart_item_data Cart item data.
 */
function elite_shipping_wishlist_move_on_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id = 0, $variation = array(), $cart_item_data = array() ) {
	$candidates = array( absint( $product_id ), absint( $variation_id ) );
	$candidates = array_values( array_filter( array_unique( $candidates ) ) );

	foreach ( $candidates as $id ) {
		if ( elite_shipping_is_product_in_wishlist( $id ) ) {
			elite_shipping_wishlist_remove( $id );
		}
	}
}
add_action( 'woocommerce_add_to_cart', 'elite_shipping_wishlist_move_on_add_to_cart', 20, 6 );

/**
 * Wishlist sync fragment for AJAX add-to-cart responses.
 * Note: wishlist badge is NOT included here — WooCommerce session fragment
 * cache was overwriting the live badge with a stale count.
 *
 * @param array<string, string> $fragments Cart fragments.
 * @return array<string, string>
 */
function elite_shipping_wishlist_cart_fragments( $fragments ) {
	$ids   = elite_shipping_get_wishlist_ids();
	$count = count( $ids );

	$fragments['div.elite-wishlist-sync'] = sprintf(
		'<div class="elite-wishlist-sync" hidden data-count="%1$s" data-ids="%2$s" aria-hidden="true"></div>',
		esc_attr( (string) $count ),
		esc_attr( implode( '|', $ids ) )
	);

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'elite_shipping_wishlist_cart_fragments', 25 );

/**
 * Hidden sync node so AJAX fragments can update wishlist UI.
 */
function elite_shipping_render_wishlist_sync_node() {
	if ( ! class_exists( 'WooCommerce' ) || is_admin() ) {
		return;
	}

	$ids   = elite_shipping_get_wishlist_ids();
	$count = count( $ids );
	printf(
		'<div class="elite-wishlist-sync" hidden data-count="%1$s" data-ids="%2$s" aria-hidden="true"></div>',
		esc_attr( (string) $count ),
		esc_attr( implode( '|', $ids ) )
	);
}
add_action( 'wp_footer', 'elite_shipping_render_wishlist_sync_node', 20 );

/**
 * Persist merged cookie/meta wishlist when storage is out of sync or legacy.
 */
function elite_shipping_wishlist_repair_storage() {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	$raw           = isset( $_COOKIE[ ELITE_WISHLIST_COOKIE ] ) ? (string) wp_unslash( $_COOKIE[ ELITE_WISHLIST_COOKIE ] ) : '';
	$legacy_cookie = ( '' !== $raw && false === strpos( $raw, '|' ) && false !== strpos( $raw, ',' ) );
	$ids           = elite_shipping_get_wishlist_ids();
	$needs_save    = $legacy_cookie;

	if ( is_user_logged_in() ) {
		$current = elite_shipping_normalize_wishlist_ids( get_user_meta( get_current_user_id(), ELITE_WISHLIST_META, true ) );
		if ( $current !== $ids ) {
			$needs_save = true;
		}
	}

	$cookie_ids = elite_shipping_read_wishlist_cookie();
	if ( $cookie_ids !== $ids ) {
		$needs_save = true;
	}

	if ( $needs_save ) {
		elite_shipping_save_wishlist_ids( $ids );
	}
}
add_action( 'wp', 'elite_shipping_wishlist_repair_storage', 5 );

/**
 * Localize wishlist script data.
 */
function elite_shipping_enqueue_wishlist_assets() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	wp_localize_script(
		'elite-shipping-main',
		'eliteShippingWishlist',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'elite_wishlist' ),
			'wishlistUrl' => elite_shipping_get_wishlist_url(),
			'ids'         => elite_shipping_get_wishlist_ids(),
			'i18n'        => array(
				'add'     => __( 'Add to wishlist', 'elite-shipping' ),
				'remove'  => __( 'Remove from wishlist', 'elite-shipping' ),
				'empty'   => __( 'Your wishlist is empty.', 'elite-shipping' ),
				'shop'    => __( 'Browse products', 'elite-shipping' ),
				'added'   => __( 'Added to wishlist.', 'elite-shipping' ),
				'removed' => __( 'Removed from wishlist.', 'elite-shipping' ),
				'items'   => __( '%d items', 'elite-shipping' ),
				'item'    => __( '%d item', 'elite-shipping' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'elite_shipping_enqueue_wishlist_assets', 35 );

/**
 * Render wishlist page product rows.
 *
 * @param int[] $ids Product IDs.
 */
function elite_shipping_render_wishlist_items( $ids = null ) {
	if ( null === $ids ) {
		$ids = elite_shipping_get_wishlist_ids();
	}

	$ids = elite_shipping_filter_wishlist_product_ids( $ids );

	if ( empty( $ids ) ) {
		$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		echo '<div class="apex-wishlist-empty">';
		echo '<p>' . esc_html__( 'Your wishlist is empty.', 'elite-shipping' ) . '</p>';
		echo '<a class="apex-wishlist-empty-btn" href="' . esc_url( $shop_url ) . '">' . esc_html__( 'Browse products', 'elite-shipping' ) . '</a>';
		echo '</div>';
		return;
	}

	echo '<ul class="apex-wishlist-list" role="list">';

	foreach ( $ids as $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! $product instanceof WC_Product ) {
			continue;
		}

		$url       = get_permalink( $product_id );
		$title     = $product->get_name();
		$thumbnail = $product->get_image( 'woocommerce_thumbnail' );
		$price     = $product->get_price_html();
		$in_stock  = $product->is_in_stock();
		?>
		<li class="apex-wishlist-item" data-product_id="<?php echo esc_attr( (string) $product_id ); ?>">
			<div class="apex-wishlist-item-media">
				<a href="<?php echo esc_url( $url ); ?>">
					<?php echo $thumbnail ? $thumbnail : '<div class="apex-product-ph"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>
			<div class="apex-wishlist-item-body">
				<h2 class="apex-wishlist-item-title">
					<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
				</h2>
				<div class="apex-wishlist-item-price"><?php echo wp_kses_post( $price ); ?></div>
				<p class="apex-wishlist-item-stock <?php echo $in_stock ? 'is-instock' : 'is-outofstock'; ?>">
					<?php echo $in_stock ? esc_html__( 'In stock', 'elite-shipping' ) : esc_html__( 'Out of stock', 'elite-shipping' ); ?>
				</p>
			</div>
			<div class="apex-wishlist-item-actions">
				<a class="apex-wishlist-item-view" href="<?php echo esc_url( $url ); ?>">
					<?php esc_html_e( 'View details', 'elite-shipping' ); ?>
				</a>
				<?php if ( $product->is_purchasable() && $in_stock ) : ?>
					<?php
					$add_classes = array(
						'apex-wishlist-item-cart',
						'button',
						'product_type_' . $product->get_type(),
						'add_to_cart_button',
					);
					if ( $product->supports( 'ajax_add_to_cart' ) ) {
						$add_classes[] = 'ajax_add_to_cart';
					}
					?>
					<a
						class="<?php echo esc_attr( implode( ' ', $add_classes ) ); ?>"
						href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
						data-quantity="1"
						data-product_id="<?php echo esc_attr( (string) $product_id ); ?>"
						data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
						aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>"
						rel="nofollow"
					>
						<?php esc_html_e( 'Add to cart', 'elite-shipping' ); ?>
					</a>
				<?php endif; ?>
				<button
					type="button"
					class="apex-wishlist-item-remove js-elite-wishlist-toggle"
					data-product_id="<?php echo esc_attr( (string) $product_id ); ?>"
					aria-label="<?php esc_attr_e( 'Remove from wishlist', 'elite-shipping' ); ?>"
				>
					&times;
				</button>
			</div>
		</li>
		<?php
	}

	echo '</ul>';
}
