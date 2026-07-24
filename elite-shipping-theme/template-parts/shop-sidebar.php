<?php
/**
 * Shop archive sidebar — filters, categories, top rated.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_slug   = isset( $args['current_slug'] ) ? (string) $args['current_slug'] : '';
$min_price      = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : '';
$max_price      = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : '';
$categories     = elite_shipping_get_shop_sidebar_categories();
$top_rated      = elite_shipping_get_top_rated_products( 3 );
?>
<aside class="apex-shop-sidebar">
	<div class="apex-shop-sidebar-block">
		<h2 class="apex-shop-sidebar-title"><?php esc_html_e( 'Filter By Price', 'elite-shipping' ); ?></h2>
		<form class="apex-shop-price-filter" method="get" action="">
			<div class="apex-shop-price-fields">
				<input type="number" name="min_price" min="0" step="1" placeholder="<?php esc_attr_e( 'Min', 'elite-shipping' ); ?>" value="<?php echo esc_attr( $min_price ); ?>">
				<span class="apex-shop-price-sep">—</span>
				<input type="number" name="max_price" min="0" step="1" placeholder="<?php esc_attr_e( 'Max', 'elite-shipping' ); ?>" value="<?php echo esc_attr( $max_price ); ?>">
			</div>
			<?php if ( function_exists( 'wc_query_string_form_fields' ) ) : ?>
				<?php wc_query_string_form_fields( null, array( 'min_price', 'max_price', 'submit' ), true ); ?>
			<?php endif; ?>
			<button type="submit" class="apex-shop-filter-btn"><?php esc_html_e( 'Filter', 'elite-shipping' ); ?></button>
		</form>
	</div>

	<div class="apex-shop-sidebar-block">
		<h2 class="apex-shop-sidebar-title"><?php esc_html_e( 'Stock Status', 'elite-shipping' ); ?></h2>
		<ul class="apex-shop-sidebar-list">
			<li><a href="<?php echo esc_url( add_query_arg( 'onsale', '1' ) ); ?>"><?php esc_html_e( 'On sale', 'elite-shipping' ); ?></a></li>
			<li><a href="<?php echo esc_url( add_query_arg( 'stock_status', 'instock' ) ); ?>"><?php esc_html_e( 'In stock', 'elite-shipping' ); ?></a></li>
		</ul>
	</div>

	<div class="apex-shop-sidebar-block">
		<h2 class="apex-shop-sidebar-title"><?php esc_html_e( 'Product Categories', 'elite-shipping' ); ?></h2>
		<ul class="apex-shop-sidebar-list apex-shop-sidebar-cats">
			<?php foreach ( $categories as $category ) : ?>
				<?php $is_current = $current_slug && $current_slug === $category['slug']; ?>
				<li>
					<a class="<?php echo $is_current ? 'is-current' : ''; ?>" href="<?php echo esc_url( $category['url'] ); ?>">
						<?php echo esc_html( $category['name'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<?php if ( ! empty( $top_rated ) ) : ?>
		<div class="apex-shop-sidebar-block apex-shop-sidebar-block--top-rated">
			<h2 class="apex-shop-sidebar-title"><?php esc_html_e( 'Top Rated Products', 'elite-shipping' ); ?></h2>
			<ul class="apex-shop-top-rated-list">
				<?php foreach ( $top_rated as $product ) : ?>
					<li class="apex-shop-top-rated-item">
						<a class="apex-shop-top-rated-media" href="<?php echo esc_url( $product['url'] ); ?>">
							<?php echo $product['image'] ? $product['image'] : '<span class="apex-shop-top-rated-ph"></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
						<div class="apex-shop-top-rated-copy">
							<h3 class="apex-shop-top-rated-title">
								<a href="<?php echo esc_url( $product['url'] ); ?>"><?php echo esc_html( $product['title'] ); ?></a>
							</h3>
							<div class="apex-shop-top-rated-price"><?php echo wp_kses_post( $product['price_html'] ); ?></div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</aside>
