<?php
/**
 * Shop archive toolbar — show count, grid view, sorting.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$per_page_options = array( 9, 12, 18, 24 );
$current_per_page = isset( $_GET['per_page'] ) ? absint( wp_unslash( $_GET['per_page'] ) ) : 12;
$current_cols     = isset( $_GET['cols'] ) ? absint( wp_unslash( $_GET['cols'] ) ) : 4;

if ( ! in_array( $current_per_page, $per_page_options, true ) ) {
	$current_per_page = 12;
}
if ( ! in_array( $current_cols, array( 2, 4, 6 ), true ) ) {
	$current_cols = 4;
}

$base_args = array();
foreach ( array( 'orderby', 'order', 'min_price', 'max_price', 'onsale', 'stock_status' ) as $key ) {
	if ( isset( $_GET[ $key ] ) && '' !== $_GET[ $key ] ) {
		$base_args[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
	}
}
?>
<div class="apex-shop-toolbar">
	<div class="apex-shop-toolbar-left">
		<span class="apex-shop-toolbar-label"><?php esc_html_e( 'Show', 'elite-shipping' ); ?> :</span>
		<div class="apex-shop-per-page">
			<?php foreach ( $per_page_options as $index => $count ) : ?>
				<?php if ( $index > 0 ) : ?>
					<span class="apex-shop-per-page-sep">/</span>
				<?php endif; ?>
				<?php
				$url = add_query_arg(
					array_merge(
						$base_args,
						array(
							'per_page' => $count,
							'cols'     => $current_cols,
						)
					)
				);
				?>
				<a
					class="apex-shop-per-page-link<?php echo $current_per_page === $count ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( $url ); ?>"
				><?php echo esc_html( (string) $count ); ?></a>
			<?php endforeach; ?>
		</div>

		<div class="apex-shop-view-modes" aria-label="<?php esc_attr_e( 'Grid layout', 'elite-shipping' ); ?>">
			<?php foreach ( array( 2, 4, 6 ) as $cols ) : ?>
				<?php
				$url = add_query_arg(
					array_merge(
						$base_args,
						array(
							'per_page' => $current_per_page,
							'cols'     => $cols,
						)
					)
				);
				?>
				<a
					class="apex-shop-view-mode<?php echo $current_cols === $cols ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( $url ); ?>"
					aria-label="<?php echo esc_attr( sprintf( /* translators: %d: column count */ __( '%d column grid', 'elite-shipping' ), $cols ) ); ?>"
					data-cols="<?php echo esc_attr( (string) $cols ); ?>"
				>
					<span class="apex-shop-view-mode-icon" aria-hidden="true">
						<?php for ( $i = 0; $i < $cols; $i++ ) : ?>
							<i></i>
						<?php endfor; ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="apex-shop-toolbar-right">
		<?php woocommerce_catalog_ordering(); ?>
	</div>
</div>
