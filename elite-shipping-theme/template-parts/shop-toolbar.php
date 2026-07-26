<?php
/**
 * Shop archive toolbar — show count, grid view, sorting.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$per_page_options = array( 12, 24, 36, 48 );
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

		<span class="apex-shop-toolbar-sep" aria-hidden="true"></span>
		<span class="apex-shop-toolbar-label apex-shop-toolbar-label--layout"><?php esc_html_e( 'Layout', 'elite-shipping' ); ?> :</span>
		<div class="apex-shop-view-modes" role="group" aria-label="<?php esc_attr_e( 'Grid layout', 'elite-shipping' ); ?>">
			<?php
			$icon_layouts = array(
				2 => array(
					'cols' => 1,
					'rows' => 2,
				),
				4 => array(
					'cols' => 2,
					'rows' => 2,
				),
				6 => array(
					'cols' => 3,
					'rows' => 2,
				),
			);
			foreach ( array( 2, 4, 6 ) as $cols ) :
				?>
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
				$layout   = $icon_layouts[ $cols ];
				$cell_count = (int) $layout['cols'] * (int) $layout['rows'];
				?>
				<a
					class="apex-shop-view-mode<?php echo $current_cols === $cols ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( $url ); ?>"
					title="<?php echo esc_attr( sprintf( /* translators: %d: column count */ __( '%d column view', 'elite-shipping' ), $cols ) ); ?>"
					aria-label="<?php echo esc_attr( sprintf( /* translators: %d: column count */ __( '%d column grid', 'elite-shipping' ), $cols ) ); ?>"
					aria-pressed="<?php echo $current_cols === $cols ? 'true' : 'false'; ?>"
					data-cols="<?php echo esc_attr( (string) $cols ); ?>"
				>
					<span
						class="apex-shop-view-mode-icon apex-shop-view-mode-icon--<?php echo esc_attr( (string) $cols ); ?>"
						style="--icon-cols: <?php echo esc_attr( (string) $layout['cols'] ); ?>; --icon-rows: <?php echo esc_attr( (string) $layout['rows'] ); ?>;"
						aria-hidden="true"
					>
						<?php for ( $i = 0; $i < $cell_count; $i++ ) : ?>
							<i></i>
						<?php endfor; ?>
					</span>
					<span class="apex-shop-view-mode-text"><?php echo esc_html( (string) $cols ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="apex-shop-toolbar-right">
		<span class="apex-shop-toolbar-label"><?php esc_html_e( 'Sort', 'elite-shipping' ); ?> :</span>
		<div class="apex-shop-sort">
			<?php woocommerce_catalog_ordering(); ?>
		</div>
		<button
			type="button"
			class="apex-shop-filters-toggle"
			aria-expanded="false"
			aria-controls="apex-shop-sidebar"
			aria-label="<?php esc_attr_e( 'Open filters', 'elite-shipping' ); ?>"
		>
			<span class="apex-shop-filters-toggle-icon" aria-hidden="true">
				<i></i><i></i><i></i>
			</span>
		</button>
	</div>
</div>
