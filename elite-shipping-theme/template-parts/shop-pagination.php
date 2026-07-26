<?php
/**
 * Shop archive pagination with result summary.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$position = isset( $args['position'] ) ? (string) $args['position'] : 'bottom';
$total    = function_exists( 'wc_get_loop_prop' ) ? (int) wc_get_loop_prop( 'total', 0 ) : 0;
$per_page = function_exists( 'wc_get_loop_prop' ) ? (int) wc_get_loop_prop( 'per_page', 12 ) : 12;
$current  = function_exists( 'wc_get_loop_prop' ) ? max( 1, (int) wc_get_loop_prop( 'current_page', 1 ) ) : 1;
$pages    = function_exists( 'wc_get_loop_prop' ) ? max( 1, (int) wc_get_loop_prop( 'total_pages', 1 ) ) : 1;

if ( $total > 0 ) {
	$from = ( ( $current - 1 ) * $per_page ) + 1;
	$to   = min( $total, $current * $per_page );
} else {
	$from = 0;
	$to   = 0;
}
?>
<div class="apex-shop-pagination-wrap apex-shop-pagination-wrap--<?php echo esc_attr( $position ); ?>">
	<p class="apex-shop-results-meta">
		<span class="apex-shop-results-page">
			<?php
			printf(
				/* translators: 1: current page number, 2: total pages */
				esc_html__( 'Page %1$d of %2$d', 'elite-shipping' ),
				$current,
				$pages
			);
			?>
		</span>
		<span class="apex-shop-results-sep" aria-hidden="true">·</span>
		<span class="apex-shop-results-count">
			<?php
			if ( $total > 0 ) {
				printf(
					/* translators: 1: first product number on page, 2: last product number on page, 3: total filtered products */
					esc_html__( 'Showing %1$d–%2$d of %3$d products', 'elite-shipping' ),
					$from,
					$to,
					$total
				);
			} else {
				esc_html_e( '0 products', 'elite-shipping' );
			}
			?>
		</span>
	</p>

	<div class="apex-shop-pagination apex-shop-pagination--<?php echo esc_attr( $position ); ?>">
		<?php woocommerce_pagination(); ?>
	</div>
</div>
