<?php
/**
 * One-time product stock seeding — assign stock quantities from 1–5.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stable stock qty (1–5) derived from a product/variation ID.
 *
 * @param int $product_id Product or variation ID.
 * @return int
 */
function elite_shipping_stock_qty_for_id( $product_id ) {
	return ( ( absint( $product_id ) % 5 ) + 1 );
}

/**
 * Apply manage-stock + qty 1–5 to a single product or variation.
 *
 * @param WC_Product $product Product object.
 * @return void
 */
function elite_shipping_apply_ranged_stock_to_product( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	// Variable parents keep stock on variations.
	if ( $product->is_type( 'variable' ) ) {
		$product->set_manage_stock( false );
		$product->set_stock_status( 'instock' );
		$product->save();

		foreach ( $product->get_children() as $child_id ) {
			$variation = wc_get_product( (int) $child_id );
			if ( $variation instanceof WC_Product ) {
				elite_shipping_apply_ranged_stock_to_product( $variation );
			}
		}
		return;
	}

	$qty = elite_shipping_stock_qty_for_id( $product->get_id() );

	$product->set_manage_stock( true );
	$product->set_stock_quantity( $qty );
	$product->set_stock_status( 'instock' );
	$product->set_backorders( 'no' );
	$product->save();
}

/**
 * Seed all published products with stock quantities between 1 and 5.
 * Runs in batches so large catalogs do not time out.
 *
 * @return void
 */
function elite_shipping_seed_product_stock_ranges() {
	if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_product' ) ) {
		return;
	}

	if ( 'yes' === get_option( 'elite_shipping_product_stock_ranged', '' ) ) {
		return;
	}

	// Avoid front-end visitors kicking off a long write job when possible.
	if ( ! is_admin() && ! wp_doing_cron() ) {
		return;
	}

	update_option( 'woocommerce_manage_stock', 'yes' );
	update_option( 'woocommerce_stock_format', '' ); // Show "X in stock".

	$offset = absint( get_option( 'elite_shipping_product_stock_seed_offset', 0 ) );
	$batch  = 40;

	$product_ids = get_posts(
		array(
			'post_type'              => array( 'product' ),
			'post_status'            => array( 'publish', 'private' ),
			'fields'                 => 'ids',
			'posts_per_page'         => $batch,
			'offset'                 => $offset,
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( empty( $product_ids ) ) {
		update_option( 'elite_shipping_product_stock_ranged', 'yes' );
		delete_option( 'elite_shipping_product_stock_seed_offset' );
		return;
	}

	foreach ( $product_ids as $product_id ) {
		$product = wc_get_product( (int) $product_id );
		if ( $product instanceof WC_Product ) {
			elite_shipping_apply_ranged_stock_to_product( $product );
		}
	}

	update_option( 'elite_shipping_product_stock_seed_offset', $offset + count( $product_ids ) );
}
add_action( 'admin_init', 'elite_shipping_seed_product_stock_ranges', 40 );

/**
 * Admin notice while stock seeding is in progress / completed once.
 *
 * @return void
 */
function elite_shipping_product_stock_seed_notice() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	if ( 'yes' === get_option( 'elite_shipping_product_stock_ranged', '' ) ) {
		return;
	}

	$offset = absint( get_option( 'elite_shipping_product_stock_seed_offset', 0 ) );
	?>
	<div class="notice notice-info">
		<p>
			<?php
			echo esc_html(
				sprintf(
					/* translators: %d: number of products processed so far */
					__( 'Elite Shipping: assigning product stock quantities (1–5). Processed %d so far — keep browsing the admin until this notice disappears.', 'elite-shipping' ),
					$offset
				)
			);
			?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'elite_shipping_product_stock_seed_notice' );
