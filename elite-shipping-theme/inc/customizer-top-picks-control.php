<?php
/**
 * Customizer control — Top Picks dynamic display list.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Add / remove category rows for the Top Picks homepage display list.
	 */
	class Elite_Top_Picks_Display_List_Control extends WP_Customize_Control {

		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'elite_top_picks_display_list';

		/**
		 * Category dropdown choices.
		 *
		 * @var array<int, string>
		 */
		public $choices = array();

		/**
		 * Pass choices to the Customizer JS template.
		 */
		public function to_json() {
			parent::to_json();
			$this->json['choices'] = $this->choices;
		}

		/**
		 * Render control markup.
		 */
		public function render_content() {
			$ids     = elite_shipping_get_top_picks_display_list_rows();
			$choices = $this->choices;
			?>
			<div class="elite-top-picks-list-control">
				<div class="elite-top-picks-list-control__head">
					<?php if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif; ?>
					<button type="button" class="button button-secondary elite-top-picks-add">
						<?php esc_html_e( 'Add', 'elite-shipping' ); ?>
					</button>
				</div>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
				<ul class="elite-top-picks-list-items">
					<?php if ( empty( $ids ) ) : ?>
						<li class="elite-top-picks-list-items__empty"><?php esc_html_e( 'No categories in the display list yet. Click Add to choose categories for the homepage.', 'elite-shipping' ); ?></li>
					<?php else : ?>
						<?php foreach ( $ids as $index => $term_id ) : ?>
							<?php echo elite_shipping_render_top_picks_list_item_row( (int) $index, (int) $term_id, $choices ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
				<input
					id="<?php echo esc_attr( $this->id ); ?>"
					type="hidden"
					class="elite-top-picks-list-value"
					<?php $this->link(); ?>
					value="<?php echo esc_attr( wp_json_encode( array_values( array_map( 'absint', $ids ) ) ) ); ?>"
				>
			</div>
			<?php
		}
	}
}

/**
 * Render one display list row.
 *
 * @param int   $index   Row index.
 * @param int   $term_id Selected category ID.
 * @param array $choices Category choices.
 * @return string
 */
function elite_shipping_render_top_picks_list_item_row( $index, $term_id, $choices ) {
	natcasesort( $choices );

	$select = '<select class="elite-top-picks-list-item__select" aria-label="' . esc_attr__( 'Category', 'elite-shipping' ) . '">';
	$select .= '<option value="0">' . esc_html__( '— Select category —', 'elite-shipping' ) . '</option>';

	foreach ( $choices as $choice_id => $label ) {
		if ( $choice_id <= 0 ) {
			continue;
		}
		$select .= '<option value="' . esc_attr( (string) $choice_id ) . '"' . selected( (int) $term_id, (int) $choice_id, false ) . '>' . esc_html( $label ) . '</option>';
	}

	$select .= '</select>';

	ob_start();
	?>
	<li class="elite-top-picks-list-item" data-index="<?php echo esc_attr( (string) $index ); ?>">
		<span class="elite-top-picks-list-item__num"><?php echo esc_html( (string) ( $index + 1 ) ); ?>.</span>
		<?php echo $select; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<button type="button" class="button-link elite-top-picks-remove"><?php esc_html_e( 'Remove', 'elite-shipping' ); ?></button>
	</li>
	<?php
	return ob_get_clean();
}

/**
 * Read display list rows for the Customizer control (includes empty rows).
 *
 * @return int[]
 */
function elite_shipping_get_top_picks_display_list_rows() {
	$stored = get_theme_mod( 'elite_top_picks_category_ids', '' );

	if ( is_array( $stored ) ) {
		return array_values( array_map( 'absint', $stored ) );
	}

	if ( is_string( $stored ) && '' !== $stored ) {
		$decoded = json_decode( $stored, true );
		if ( is_array( $decoded ) ) {
			return array_values( array_map( 'absint', $decoded ) );
		}
	}

	return elite_shipping_get_top_picks_category_ids();
}

/**
 * Read Top Picks category IDs from theme mod (with legacy slot migration).
 *
 * @return int[]
 */
function elite_shipping_get_top_picks_category_ids() {
	$stored = get_theme_mod( 'elite_top_picks_category_ids', '' );

	if ( is_array( $stored ) ) {
		return array_values( array_filter( array_map( 'absint', $stored ) ) );
	}

	if ( is_string( $stored ) && '' !== $stored ) {
		$decoded = json_decode( $stored, true );
		if ( is_array( $decoded ) ) {
			return array_values(
				array_filter(
					array_map( 'absint', $decoded ),
					static function ( $id ) {
						return $id > 0;
					}
				)
			);
		}
	}

	$legacy_ids = array();
	for ( $slot = 1; $slot <= 8; $slot++ ) {
		$term_id = absint( get_theme_mod( 'elite_top_picks_cat_' . $slot, 0 ) );
		if ( $term_id > 0 && ! in_array( $term_id, $legacy_ids, true ) ) {
			$legacy_ids[] = $term_id;
		}
	}

	return $legacy_ids;
}

/**
 * Whether the Top Picks display list has any categories configured.
 *
 * @return bool
 */
function elite_shipping_top_picks_uses_display_list() {
	return ! empty( elite_shipping_get_top_picks_category_ids() );
}

/**
 * Sanitize the Top Picks display list theme mod.
 *
 * @param mixed $value Raw value.
 * @return string JSON array of term IDs.
 */
function elite_shipping_sanitize_top_picks_category_ids( $value ) {
	$ids = array();

	if ( is_array( $value ) ) {
		$ids = $value;
	} elseif ( is_string( $value ) && '' !== $value ) {
		$decoded = json_decode( wp_unslash( $value ), true );
		if ( is_array( $decoded ) ) {
			$ids = $decoded;
		}
	}

	$clean = array();
	$seen  = array();

	foreach ( $ids as $id ) {
		$term_id = absint( $id );

		if ( 0 === $term_id ) {
			$clean[] = 0;
			continue;
		}

		if ( in_array( $term_id, $seen, true ) ) {
			continue;
		}

		$term = get_term( $term_id, 'product_cat' );
		if ( $term instanceof WP_Term && ! is_wp_error( $term ) ) {
			$clean[] = $term_id;
			$seen[]  = $term_id;
		}
	}

	while ( count( $clean ) > 1 && 0 === end( $clean ) ) {
		array_pop( $clean );
	}

	return wp_json_encode( array_values( $clean ) );
}
