<?php
/**
 * Single product gallery hooks and helpers.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'woocommerce_init', 'elite_shipping_single_product_setup' );
add_filter( 'body_class', 'elite_shipping_single_product_body_class' );

/**
 * @param string[] $classes Body classes.
 * @return string[]
 */
function elite_shipping_single_product_body_class( $classes ) {
	if ( is_singular( 'product' ) ) {
		$classes[] = 'elite-single-product-page';
	}

	return $classes;
}

/**
 * Configure single product gallery hooks.
 */
function elite_shipping_single_product_setup() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	add_action( 'woocommerce_before_single_product_summary', 'elite_shipping_single_product_sale_badge', 9 );

	add_filter( 'woocommerce_single_product_carousel_options', 'elite_shipping_disable_gallery_direction_nav', 999 );
	add_filter( 'woocommerce_get_script_data', 'elite_shipping_disable_gallery_direction_nav_script', 999, 2 );
	add_action( 'wp_head', 'elite_shipping_gallery_hide_direction_nav_css', 999 );

	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	add_action( 'woocommerce_after_single_product_summary', 'elite_shipping_output_related_products', 20 );
}

/**
 * Disable WooCommerce Flexslider arrow nav (theme provides its own gallery arrows).
 *
 * @param array<string, mixed> $options Flexslider options.
 * @return array<string, mixed>
 */
function elite_shipping_disable_gallery_direction_nav( $options ) {
	$options['directionNav'] = false;

	return $options;
}

/**
 * @param array<string, mixed> $params  Script params.
 * @param string               $handle Script handle.
 * @return array<string, mixed>
 */
function elite_shipping_disable_gallery_direction_nav_script( $params, $handle ) {
	if ( 'wc-single-product' === $handle && isset( $params['flexslider'] ) && is_array( $params['flexslider'] ) ) {
		$params['flexslider']['directionNav'] = false;
	}

	return $params;
}

/**
 * Early inline CSS so default Flexslider arrows never show on product pages.
 */
function elite_shipping_gallery_hide_direction_nav_css() {
	if ( ! is_singular( 'product' ) ) {
		return;
	}
	?>
	<style id="elite-gallery-hide-flex-nav">
		body.elite-single-product-page .woocommerce-product-gallery .flex-direction-nav,
		body.elite-single-product-page div.product div.images .flex-direction-nav,
		body.elite-single-product-page div.product div.images a.flex-prev,
		body.elite-single-product-page div.product div.images a.flex-next,
		body.elite-single-product-page div.product div.images .flex-nav-prev,
		body.elite-single-product-page div.product div.images .flex-nav-next {
			display: none !important;
			visibility: hidden !important;
			opacity: 0 !important;
			width: 0 !important;
			height: 0 !important;
			overflow: hidden !important;
			pointer-events: none !important;
			position: absolute !important;
			left: -9999px !important;
			top: -9999px !important;
		}
	</style>
	<?php
}

/**
 * Circular sale badge on gallery.
 */
function elite_shipping_single_product_sale_badge() {
	global $product;

	if ( ! $product instanceof WC_Product || ! $product->is_on_sale() ) {
		return;
	}

	$badge   = 'SALE';
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();

	if ( $regular > 0 && $sale > 0 ) {
		$badge = '-' . round( ( ( $regular - $sale ) / $regular ) * 100 ) . '%';
	}

	echo '<span class="apex-single-sale-badge">' . esc_html( $badge ) . '</span>';
}

/**
 * Related products grid using shop card layout.
 */
function elite_shipping_output_related_products() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$args = apply_filters(
		'woocommerce_output_related_products_args',
		array(
			'posts_per_page' => 4,
			'columns'        => 4,
			'orderby'        => 'rand',
		)
	);

	$related_ids = wc_get_related_products(
		$product->get_id(),
		(int) $args['posts_per_page'],
		array( $product->get_id() )
	);

	if ( empty( $related_ids ) ) {
		return;
	}

	$columns = in_array( (int) $args['columns'], array( 2, 4, 6 ), true ) ? (int) $args['columns'] : 4;

	echo '<section class="related products apex-related-products">';
	echo '<h2>' . esc_html__( 'Related products', 'elite-shipping' ) . '</h2>';
	echo '<div class="apex-grid apex-shop-grid apex-shop-grid--cols-' . esc_attr( (string) $columns ) . '">';

	foreach ( $related_ids as $related_id ) {
		$related_product = wc_get_product( $related_id );
		if ( function_exists( 'elite_render_shop_product_card' ) ) {
			elite_render_shop_product_card( $related_product );
		}
	}

	echo '</div></section>';
}
