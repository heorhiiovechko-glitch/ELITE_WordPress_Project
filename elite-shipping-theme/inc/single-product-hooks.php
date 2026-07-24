<?php
/**
 * Single product page hooks and helpers.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'elite_shipping_single_product_setup', 25 );
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
 * Configure single product layout hooks.
 */
function elite_shipping_single_product_setup() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

	add_action( 'woocommerce_single_product_summary', 'elite_shipping_single_product_breadcrumbs', 4 );
	add_action( 'woocommerce_before_single_product_summary', 'elite_shipping_single_product_sale_badge', 9 );
	add_action( 'woocommerce_single_product_summary', 'elite_shipping_single_product_actions', 36 );
	add_action( 'woocommerce_single_product_summary', 'elite_shipping_single_product_category_share', 45 );

	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 32 );
}

/**
 * Breadcrumbs above product title.
 */
function elite_shipping_single_product_breadcrumbs() {
	if ( ! function_exists( 'woocommerce_breadcrumb' ) ) {
		return;
	}

	woocommerce_breadcrumb(
		array(
			'wrap_before' => '<nav class="apex-single-breadcrumbs woocommerce-breadcrumb" aria-label="Breadcrumb">',
			'wrap_after'  => '</nav>',
			'delimiter'   => ' / ',
		)
	);
}

/**
 * Circular sale badge on gallery.
 */
function elite_shipping_single_product_sale_badge() {
	global $product;

	if ( ! $product instanceof WC_Product || ! $product->is_on_sale() ) {
		return;
	}

	$badge = 'SALE';
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();

	if ( $regular > 0 && $sale > 0 ) {
		$badge = '-' . round( ( ( $regular - $sale ) / $regular ) * 100 ) . '%';
	}

	echo '<span class="apex-single-sale-badge">' . esc_html( $badge ) . '</span>';
}

/**
 * Compare / wishlist links and PayPal note (after add to cart).
 */
function elite_shipping_single_product_actions() {
	?>
	<p class="apex-single-finance"><?php esc_html_e( 'Flexible payment options available including PayPal checkout on eligible orders.', 'elite-shipping' ); ?></p>
	<div class="apex-single-secondary-actions">
		<a class="apex-single-secondary-link" href="#"><?php esc_html_e( 'Add to compare', 'elite-shipping' ); ?></a>
		<a class="apex-single-secondary-link" href="#"><?php esc_html_e( 'Add to wishlist', 'elite-shipping' ); ?></a>
	</div>
	<?php
}

/**
 * Category line and social share icons.
 */
function elite_shipping_single_product_category_share() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$terms = wp_get_post_terms( $product->get_id(), 'product_cat' );
	$share = elite_shipping_get_post_share_links( $product->get_id() );
	?>
	<div class="apex-single-meta-share">
		<?php if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) : ?>
			<p class="apex-single-category">
				<?php esc_html_e( 'Category:', 'elite-shipping' ); ?>
				<?php
				$links = array();
				foreach ( $terms as $term ) {
					if ( 'uncategorized' === $term->slug ) {
						continue;
					}
					$link = get_term_link( $term );
					if ( ! is_wp_error( $link ) ) {
						$links[] = '<a href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a>';
					}
				}
				echo wp_kses_post( implode( ', ', $links ) );
				?>
			</p>
		<?php endif; ?>

		<div class="apex-single-share">
			<span class="apex-single-share-label"><?php esc_html_e( 'Share:', 'elite-shipping' ); ?></span>
			<?php foreach ( $share as $item ) : ?>
				<a class="apex-single-share-link apex-single-share-link--<?php echo esc_attr( $item['id'] ); ?>" href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $item['label'] ); ?>">
					<?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}
