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
$price_bounds   = elite_shipping_get_shop_price_bounds();
$price_floor    = (float) $price_bounds['min'];
$price_ceiling  = (float) $price_bounds['max'];
$price_step     = (int) $price_bounds['step'];
$slider_min     = '' !== $min_price ? (float) $min_price : $price_floor;
$slider_max     = '' !== $max_price ? (float) $max_price : $price_ceiling;
$slider_min     = max( $price_floor, min( $slider_min, $price_ceiling ) );
$slider_max     = max( $price_floor, min( $slider_max, $price_ceiling ) );
if ( $slider_min > $slider_max ) {
	$slider_min = $price_floor;
	$slider_max = $price_ceiling;
}
$currency_symbol = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '£';
$categories      = elite_shipping_get_shop_sidebar_categories();
$top_rated       = elite_shipping_get_top_rated_products( 3 );
$is_on_sale      = isset( $_GET['onsale'] ) && '1' === (string) wp_unslash( $_GET['onsale'] );
$is_in_stock     = isset( $_GET['stock_status'] ) && 'instock' === (string) wp_unslash( $_GET['stock_status'] );
$onsale_url      = $is_on_sale ? remove_query_arg( 'onsale' ) : add_query_arg( 'onsale', '1' );
$instock_url     = $is_in_stock ? remove_query_arg( 'stock_status' ) : add_query_arg( 'stock_status', 'instock' );
?>
<aside class="apex-shop-sidebar" id="apex-shop-sidebar">
	<div class="apex-shop-filters-drawer-head">
		<button
			type="button"
			class="apex-shop-filters-close"
			aria-label="<?php esc_attr_e( 'Close filters', 'elite-shipping' ); ?>"
		>
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
			<span><?php esc_html_e( 'Close', 'elite-shipping' ); ?></span>
		</button>
	</div>
	<div class="apex-shop-sidebar-block">
		<h2 class="apex-shop-sidebar-title"><?php esc_html_e( 'Filter By Price', 'elite-shipping' ); ?></h2>
		<form
			class="apex-shop-price-filter"
			method="get"
			action=""
			data-price-floor="<?php echo esc_attr( (string) $price_floor ); ?>"
			data-price-ceiling="<?php echo esc_attr( (string) $price_ceiling ); ?>"
			data-price-step="<?php echo esc_attr( (string) $price_step ); ?>"
			data-currency-symbol="<?php echo esc_attr( $currency_symbol ); ?>"
		>
			<div class="apex-shop-price-slider" aria-hidden="false">
				<div class="apex-shop-price-slider-track">
					<div class="apex-shop-price-slider-range"></div>
				</div>
				<input
					type="range"
					class="apex-shop-price-slider-min"
					min="<?php echo esc_attr( (string) $price_floor ); ?>"
					max="<?php echo esc_attr( (string) $price_ceiling ); ?>"
					step="<?php echo esc_attr( (string) $price_step ); ?>"
					value="<?php echo esc_attr( (string) $slider_min ); ?>"
					aria-label="<?php esc_attr_e( 'Minimum price', 'elite-shipping' ); ?>"
				>
				<input
					type="range"
					class="apex-shop-price-slider-max"
					min="<?php echo esc_attr( (string) $price_floor ); ?>"
					max="<?php echo esc_attr( (string) $price_ceiling ); ?>"
					step="<?php echo esc_attr( (string) $price_step ); ?>"
					value="<?php echo esc_attr( (string) $slider_max ); ?>"
					aria-label="<?php esc_attr_e( 'Maximum price', 'elite-shipping' ); ?>"
				>
			</div>

			<p class="apex-shop-price-slider-label">
				<span class="apex-shop-price-slider-label-text"><?php esc_html_e( 'Price:', 'elite-shipping' ); ?></span>
				<strong data-price-min-display><?php echo esc_html( elite_shipping_format_shop_price_amount( $slider_min ) ); ?></strong>
				<span class="apex-shop-price-slider-label-sep">—</span>
				<strong data-price-max-display><?php echo esc_html( elite_shipping_format_shop_price_amount( $slider_max ) ); ?></strong>
			</p>

			<div class="apex-shop-price-fields apex-shop-price-fields--visually-hidden">
				<label class="screen-reader-text" for="apex-shop-min-price"><?php esc_html_e( 'Minimum price', 'elite-shipping' ); ?></label>
				<input id="apex-shop-min-price" type="number" name="min_price" min="0" step="1" placeholder="<?php esc_attr_e( 'Min', 'elite-shipping' ); ?>" value="<?php echo esc_attr( $min_price ); ?>">
				<span class="apex-shop-price-sep">—</span>
				<label class="screen-reader-text" for="apex-shop-max-price"><?php esc_html_e( 'Maximum price', 'elite-shipping' ); ?></label>
				<input id="apex-shop-max-price" type="number" name="max_price" min="0" step="1" placeholder="<?php esc_attr_e( 'Max', 'elite-shipping' ); ?>" value="<?php echo esc_attr( $max_price ); ?>">
			</div>
			<?php if ( function_exists( 'wc_query_string_form_fields' ) ) : ?>
				<?php wc_query_string_form_fields( null, array( 'min_price', 'max_price', 'submit' ), true ); ?>
			<?php endif; ?>
			<button type="submit" class="apex-shop-filter-btn"><?php esc_html_e( 'Filter', 'elite-shipping' ); ?></button>
		</form>
	</div>

	<div class="apex-shop-sidebar-block apex-shop-sidebar-block--stock">
		<h2 class="apex-shop-sidebar-title"><?php esc_html_e( 'Stock Status', 'elite-shipping' ); ?></h2>
		<ul class="apex-shop-stock-list">
			<li>
				<a
					class="apex-shop-stock-option<?php echo $is_on_sale ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( $onsale_url ); ?>"
					<?php echo $is_on_sale ? ' aria-current="true"' : ''; ?>
				>
					<span class="apex-shop-stock-option-icon apex-shop-stock-option-icon--sale" aria-hidden="true">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><circle cx="7" cy="7" r="1.2" fill="currentColor" stroke="none"/></svg>
					</span>
					<span class="apex-shop-stock-option-copy">
						<span class="apex-shop-stock-option-label"><?php esc_html_e( 'On sale', 'elite-shipping' ); ?></span>
						<span class="apex-shop-stock-option-desc"><?php esc_html_e( 'Special offers', 'elite-shipping' ); ?></span>
					</span>
					<span class="apex-shop-stock-option-check" aria-hidden="true">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12l5 5L20 7"/></svg>
					</span>
				</a>
			</li>
			<li>
				<a
					class="apex-shop-stock-option<?php echo $is_in_stock ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( $instock_url ); ?>"
					<?php echo $is_in_stock ? ' aria-current="true"' : ''; ?>
				>
					<span class="apex-shop-stock-option-icon apex-shop-stock-option-icon--stock" aria-hidden="true">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
					</span>
					<span class="apex-shop-stock-option-copy">
						<span class="apex-shop-stock-option-label"><?php esc_html_e( 'In stock', 'elite-shipping' ); ?></span>
						<span class="apex-shop-stock-option-desc"><?php esc_html_e( 'Ready to ship', 'elite-shipping' ); ?></span>
					</span>
					<span class="apex-shop-stock-option-check" aria-hidden="true">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12l5 5L20 7"/></svg>
					</span>
				</a>
			</li>
		</ul>
	</div>

	<div class="apex-shop-sidebar-block apex-shop-sidebar-block--categories">
		<h2 class="apex-shop-sidebar-title"><?php esc_html_e( 'Product Categories', 'elite-shipping' ); ?></h2>
		<div class="apex-shop-category-panel">
			<ul class="apex-shop-category-list">
				<?php foreach ( $categories as $category ) : ?>
					<?php $is_current = $current_slug && $current_slug === $category['slug']; ?>
					<li>
						<a
							class="apex-shop-category-link<?php echo $is_current ? ' is-current' : ''; ?>"
							href="<?php echo esc_url( $category['url'] ); ?>"
							<?php echo $is_current ? ' aria-current="page"' : ''; ?>
						>
							<span class="apex-shop-category-label"><?php echo esc_html( $category['name'] ); ?></span>
							<?php if ( ! empty( $category['count'] ) ) : ?>
								<span class="apex-shop-category-count"><?php echo esc_html( (string) $category['count'] ); ?></span>
							<?php endif; ?>
							<span class="apex-shop-category-chevron" aria-hidden="true">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
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
