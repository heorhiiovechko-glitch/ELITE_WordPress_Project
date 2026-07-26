<?php
/**
 * Customizer control — Hero background slides list (Paragraph 1).
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Unlimited hero background image list.
	 */
	class Elite_Hero_Slides_List_Control extends WP_Customize_Control {

		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'elite_hero_slides_list';

		/**
		 * Render control markup.
		 */
		public function render_content() {
			$items = elite_shipping_get_hero_slides_list_rows();
			?>
			<div class="elite-hero-slides-list-control">
				<div class="elite-hero-slides-list-control__head">
					<?php if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif; ?>
					<button type="button" class="button button-secondary elite-hero-slides-add" aria-label="<?php esc_attr_e( 'Add background', 'elite-shipping' ); ?>">
						<span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
						<span class="screen-reader-text"><?php esc_html_e( 'Add', 'elite-shipping' ); ?></span>
					</button>
				</div>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
				<ul class="elite-hero-slides-list-items">
					<?php if ( empty( $items ) ) : ?>
						<li class="elite-hero-slides-list-items__empty"><?php esc_html_e( 'No backgrounds yet. Click + to add a slide image.', 'elite-shipping' ); ?></li>
					<?php else : ?>
						<?php foreach ( $items as $index => $item ) : ?>
							<?php
							echo elite_shipping_render_hero_slide_list_item_row( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								(int) $index,
								(int) ( $item['image'] ?? 0 )
							);
							?>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
				<input
					id="<?php echo esc_attr( $this->id ); ?>"
					type="hidden"
					class="elite-hero-slides-list-value"
					<?php $this->link(); ?>
					value="<?php echo esc_attr( wp_json_encode( array_values( $items ) ) ); ?>"
				>
			</div>
			<?php
		}
	}
}

/**
 * Render one hero background list row.
 *
 * @param int $index Row index.
 * @param int $image Attachment ID.
 * @return string
 */
function elite_shipping_render_hero_slide_list_item_row( $index, $image ) {
	$preview_url   = $image ? wp_get_attachment_image_url( $image, 'thumbnail' ) : '';
	$preview_style = $preview_url ? 'background-image:url(' . esc_url( $preview_url ) . ');' : '';
	$image_label   = $preview_url
		? __( 'Change image', 'elite-shipping' )
		: __( 'Select image', 'elite-shipping' );

	$icon_fn = function_exists( 'elite_shipping_customizer_dashicon_button_content' )
		? 'elite_shipping_customizer_dashicon_button_content'
		: null;

	ob_start();
	?>
	<li class="elite-hero-slides-list-item" data-index="<?php echo esc_attr( (string) $index ); ?>">
		<span class="elite-hero-slides-list-item__num"><?php echo esc_html( sprintf( __( 'Image %d', 'elite-shipping' ), $index + 1 ) ); ?></span>
		<input type="hidden" class="elite-hero-slides-list-item__image-id" value="<?php echo esc_attr( (string) $image ); ?>">
		<div class="elite-hero-slides-list-item__actions">
			<button
				type="button"
				class="elite-hero-slides-icon-btn elite-hero-slides-select-image<?php echo $preview_url ? ' has-image' : ''; ?>"
				aria-label="<?php echo esc_attr( $image_label ); ?>"
				<?php echo $preview_url ? 'style="' . esc_attr( $preview_style ) . '"' : ''; ?>
			>
				<?php
				if ( $icon_fn ) {
					echo $icon_fn( 'format-image', $image_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo '<span class="dashicons dashicons-format-image" aria-hidden="true"></span>';
				}
				?>
			</button>
			<button
				type="button"
				class="elite-hero-slides-icon-btn elite-hero-slides-icon-btn--remove elite-hero-slides-remove"
				aria-label="<?php esc_attr_e( 'Remove', 'elite-shipping' ); ?>"
			>
				<?php
				if ( $icon_fn ) {
					echo $icon_fn( 'trash', __( 'Remove', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo '<span class="dashicons dashicons-trash" aria-hidden="true"></span>';
				}
				?>
			</button>
		</div>
	</li>
	<?php
	return ob_get_clean();
}

/**
 * Normalize hero slides list items.
 *
 * @param array $items Raw items.
 * @return array<int, array{image: int}>
 */
function elite_shipping_normalize_hero_slides_list_items( $items, $keep_empty = false ) {
	$normalized = array();

	foreach ( $items as $item ) {
		if ( is_numeric( $item ) ) {
			$image = absint( $item );
		} elseif ( is_array( $item ) ) {
			$image = absint( $item['image'] ?? 0 );
		} else {
			continue;
		}

		if ( $image <= 0 && ! $keep_empty ) {
			continue;
		}

		$normalized[] = array(
			'image' => $image,
		);
	}

	return $normalized;
}

/**
 * Parse hero slides list from theme mod.
 *
 * @return array<int, array{image: int}>
 */
function elite_shipping_parse_hero_slides_list_theme_mod() {
	$stored = get_theme_mod( 'elite_hero_slides_list', '' );

	if ( is_array( $stored ) ) {
		return elite_shipping_normalize_hero_slides_list_items( $stored );
	}

	if ( is_string( $stored ) && '' !== $stored ) {
		$decoded = json_decode( $stored, true );
		if ( is_array( $decoded ) ) {
			return elite_shipping_normalize_hero_slides_list_items( $decoded );
		}
	}

	return array();
}

/**
 * Migrate legacy fixed slide settings into list rows.
 *
 * @return array<int, array{image: int}>
 */
function elite_shipping_migrate_legacy_hero_slides() {
	$items = array();

	for ( $i = 1; $i <= 4; $i++ ) {
		$image = absint( get_theme_mod( 'elite_hero_slide_' . $i, 0 ) );
		if ( $image > 0 ) {
			$items[] = array( 'image' => $image );
		}
	}

	return $items;
}

/**
 * Rows for the Customizer control (keeps empty image rows while editing).
 *
 * @return array<int, array{image: int}>
 */
function elite_shipping_get_hero_slides_list_rows() {
	$stored = get_theme_mod( 'elite_hero_slides_list', null );

	if ( null === $stored || '' === $stored ) {
		$legacy = elite_shipping_migrate_legacy_hero_slides();
		if ( ! empty( $legacy ) ) {
			return $legacy;
		}
		return array();
	}

	if ( is_array( $stored ) ) {
		$items = elite_shipping_normalize_hero_slides_list_items( $stored );
	} elseif ( is_string( $stored ) ) {
		$decoded = json_decode( $stored, true );
		$items   = is_array( $decoded ) ? $decoded : array();
		// Keep rows with image 0 while editing in Customizer.
		$rows = array();
		foreach ( $items as $item ) {
			if ( is_numeric( $item ) ) {
				$rows[] = array( 'image' => absint( $item ) );
			} elseif ( is_array( $item ) ) {
				$rows[] = array( 'image' => absint( $item['image'] ?? 0 ) );
			}
		}
		return $rows;
	} else {
		$items = array();
	}

	return $items;
}

/**
 * Sanitize hero slides list theme mod.
 *
 * @param mixed $value Raw value.
 * @return string JSON
 */
function elite_shipping_sanitize_hero_slides_list( $value ) {
	if ( is_string( $value ) ) {
		$decoded = json_decode( wp_unslash( $value ), true );
		$value   = is_array( $decoded ) ? $decoded : array();
	}

	if ( ! is_array( $value ) ) {
		$value = array();
	}

	// Keep empty rows while editing in Customizer; front-end ignores image 0.
	return wp_json_encode( elite_shipping_normalize_hero_slides_list_items( $value, true ) );
}

/**
 * Attachment IDs from the hero slides list (front-end).
 *
 * @return int[]
 */
function elite_shipping_get_hero_slide_attachment_ids() {
	$items = elite_shipping_parse_hero_slides_list_theme_mod();
	if ( empty( $items ) ) {
		$items = elite_shipping_migrate_legacy_hero_slides();
	}

	$ids = array();
	foreach ( $items as $item ) {
		$image = absint( $item['image'] ?? 0 );
		if ( $image > 0 ) {
			$ids[] = $image;
		}
	}

	return $ids;
}
