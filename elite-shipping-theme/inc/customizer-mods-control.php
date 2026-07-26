<?php
/**
 * Customizer control — Modifications (Built to Suit Your Needs) display list.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Dynamic list of modification carousel cards (title + image).
	 */
	class Elite_Mods_Display_List_Control extends WP_Customize_Control {

		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'elite_mods_display_list';

		/**
		 * Render control markup.
		 */
		public function render_content() {
			$items = elite_shipping_get_mods_display_list_rows();
			?>
			<div class="elite-mods-list-control">
				<div class="elite-mods-list-control__head">
					<?php if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif; ?>
					<button type="button" class="button button-secondary elite-mods-add">
						<?php esc_html_e( 'Add', 'elite-shipping' ); ?>
					</button>
				</div>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
				<ul class="elite-mods-list-items">
					<?php if ( empty( $items ) ) : ?>
						<li class="elite-mods-list-items__empty"><?php esc_html_e( 'No cards in the display list yet. Click Add to create modification cards for the homepage.', 'elite-shipping' ); ?></li>
					<?php else : ?>
						<?php foreach ( $items as $index => $item ) : ?>
							<?php
							echo elite_shipping_render_mods_list_item_row( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								(int) $index,
								(string) ( $item['title'] ?? '' ),
								(int) ( $item['image'] ?? 0 )
							);
							?>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
				<input
					id="<?php echo esc_attr( $this->id ); ?>"
					type="hidden"
					class="elite-mods-list-value"
					<?php $this->link(); ?>
					value="<?php echo esc_attr( wp_json_encode( array_values( $items ) ) ); ?>"
				>
			</div>
			<?php
		}
	}
}

/**
 * Dashicon markup for Customizer icon buttons.
 *
 * @param string $icon  Dashicon slug without prefix.
 * @param string $label Accessible label.
 * @return string
 */
function elite_shipping_customizer_dashicon_button_content( $icon, $label ) {
	return '<span class="dashicons dashicons-' . esc_attr( $icon ) . '" aria-hidden="true"></span>'
		. '<span class="screen-reader-text">' . esc_html( $label ) . '</span>';
}

/**
 * Render one modification display list row.
 *
 * @param int    $index Row index.
 * @param string $title Card title.
 * @param int    $image Attachment ID.
 * @return string
 */
function elite_shipping_render_mods_list_item_row( $index, $title, $image ) {
	$preview_url   = $image ? wp_get_attachment_image_url( $image, 'thumbnail' ) : '';
	$preview_style = $preview_url ? 'background-image:url(' . esc_url( $preview_url ) . ');' : '';
	$image_label   = $preview_url
		? __( 'Change image', 'elite-shipping' )
		: __( 'Select image', 'elite-shipping' );

	ob_start();
	?>
	<li class="elite-mods-list-item" data-index="<?php echo esc_attr( (string) $index ); ?>">
		<span class="elite-mods-list-item__num"><?php echo esc_html( (string) ( $index + 1 ) ); ?>.</span>
		<input
			type="text"
			class="elite-mods-list-item__title"
			value="<?php echo esc_attr( $title ); ?>"
			placeholder="<?php esc_attr_e( 'Card title', 'elite-shipping' ); ?>"
			aria-label="<?php esc_attr_e( 'Card title', 'elite-shipping' ); ?>"
		>
		<input type="hidden" class="elite-mods-list-item__image-id" value="<?php echo esc_attr( (string) $image ); ?>">
		<div class="elite-mods-list-item__actions">
			<button
				type="button"
				class="elite-mods-icon-btn elite-mods-select-image<?php echo $preview_url ? ' has-image' : ''; ?>"
				aria-label="<?php echo esc_attr( $image_label ); ?>"
				<?php echo $preview_url ? 'style="' . esc_attr( $preview_style ) . '"' : ''; ?>
			><?php echo elite_shipping_customizer_dashicon_button_content( 'format-image', $image_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			<button
				type="button"
				class="elite-mods-icon-btn elite-mods-icon-btn--remove elite-mods-remove"
				aria-label="<?php esc_attr_e( 'Remove', 'elite-shipping' ); ?>"
			><?php echo elite_shipping_customizer_dashicon_button_content( 'trash', __( 'Remove', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		</div>
	</li>
	<?php
	return ob_get_clean();
}

/**
 * Parse mods display list from theme mod.
 *
 * @return array<int, array{title: string, image: int}>
 */
function elite_shipping_parse_mods_display_list_theme_mod() {
	$stored = get_theme_mod( 'elite_mods_display_list', '' );

	if ( is_array( $stored ) ) {
		return elite_shipping_normalize_mods_display_list_items( $stored );
	}

	if ( is_string( $stored ) && '' !== $stored ) {
		$decoded = json_decode( $stored, true );
		if ( is_array( $decoded ) ) {
			return elite_shipping_normalize_mods_display_list_items( $decoded );
		}
	}

	return array();
}

/**
 * Normalize modification list items.
 *
 * @param array $items Raw items.
 * @return array<int, array{title: string, image: int}>
 */
function elite_shipping_normalize_mods_display_list_items( $items ) {
	$normalized = array();

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$title = sanitize_text_field( $item['title'] ?? '' );
		$image = absint( $item['image'] ?? 0 );

		if ( '' === $title && $image <= 0 ) {
			continue;
		}

		$normalized[] = array(
			'title' => $title,
			'image' => $image,
		);
	}

	return $normalized;
}

/**
 * Rows for the Customizer control (includes empty title rows while editing).
 *
 * @return array<int, array{title: string, image: int}>
 */
function elite_shipping_get_mods_display_list_rows() {
	$items = elite_shipping_parse_mods_display_list_theme_mod();
	if ( ! empty( $items ) ) {
		return $items;
	}

	return elite_shipping_migrate_legacy_mod_slots();
}

/**
 * Legacy slot settings → display list rows.
 *
 * @return array<int, array{title: string, image: int}>
 */
function elite_shipping_migrate_legacy_mod_slots() {
	$defaults = elite_shipping_get_default_mod_cards();
	$items    = array();

	for ( $slot = 1; $slot <= 5; $slot++ ) {
		$default = $defaults[ $slot - 1 ] ?? array(
			'title' => '',
			'image' => '',
		);
		$title   = get_theme_mod( 'elite_mod_title_' . $slot, $default['title'] );
		$image   = absint( get_theme_mod( 'elite_mod_image_' . $slot, 0 ) );

		if ( '' === trim( (string) $title ) && $image <= 0 ) {
			continue;
		}

		$items[] = array(
			'title' => (string) $title,
			'image' => $image,
		);
	}

	return $items;
}

/**
 * Whether a custom mods display list has been saved.
 *
 * @return bool
 */
function elite_shipping_mods_uses_display_list() {
	$stored = get_theme_mod( 'elite_mods_display_list', '' );

	if ( is_array( $stored ) ) {
		return ! empty( $stored );
	}

	return is_string( $stored ) && '' !== $stored && '[]' !== $stored;
}

/**
 * Modification cards for the homepage carousel.
 *
 * @return array<int, array{title: string, image_id: int, image_url: string}>
 */
function elite_shipping_get_mods_display_list_items() {
	$items          = elite_shipping_parse_mods_display_list_theme_mod();
	$from_migration = false;

	if ( empty( $items ) ) {
		$items          = elite_shipping_migrate_legacy_mod_slots();
		$from_migration = ! empty( $items );
	}

	$defaults = elite_shipping_get_default_mod_cards();
	$resolved = array();

	foreach ( $items as $index => $item ) {
		$title = trim( (string) ( $item['title'] ?? '' ) );
		if ( '' === $title ) {
			continue;
		}

		$image_id  = absint( $item['image'] ?? 0 );
		$image_url = $image_id ? (string) wp_get_attachment_image_url( $image_id, 'large' ) : '';

		if ( ! $image_url && $from_migration ) {
			$slot    = $index + 1;
			$default = $defaults[ $index ] ?? array( 'image' => '' );
			$image_url = elite_shipping_get_theme_mod_image_url(
				'elite_mod_image_' . $slot,
				(string) ( $default['image'] ?? '' )
			);
		}

		$resolved[] = array(
			'title'     => $title,
			'image_id'  => $image_id,
			'image_url' => $image_url,
		);
	}

	return $resolved;
}

/**
 * Sanitize mods display list theme mod.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function elite_shipping_sanitize_mods_display_list( $value ) {
	$items = array();

	if ( is_array( $value ) ) {
		$items = $value;
	} elseif ( is_string( $value ) && '' !== $value ) {
		$decoded = json_decode( wp_unslash( $value ), true );
		if ( is_array( $decoded ) ) {
			$items = $decoded;
		}
	}

	$clean = array();

	foreach ( elite_shipping_normalize_mods_display_list_items( $items ) as $item ) {
		if ( '' === trim( $item['title'] ) ) {
			continue;
		}

		$image = absint( $item['image'] );
		if ( $image > 0 && ! wp_get_attachment_url( $image ) ) {
			$image = 0;
		}

		$clean[] = array(
			'title' => $item['title'],
			'image' => $image,
		);
	}

	return wp_json_encode( array_values( $clean ) );
}
