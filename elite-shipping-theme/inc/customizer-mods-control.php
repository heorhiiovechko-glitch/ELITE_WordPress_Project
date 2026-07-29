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
		 * Category dropdown choices.
		 *
		 * @var array<int, string>
		 */
		public $choices = array();

		/**
		 * Pass choices and category thumbnails to the Customizer JS template.
		 */
		public function to_json() {
			parent::to_json();
			$this->json['choices']        = $this->choices;
			$this->json['categoryImages'] = elite_shipping_get_category_picker_image_map();
		}

		/**
		 * Render control markup.
		 */
		public function render_content() {
			$items   = elite_shipping_get_mods_display_list_rows();
			$choices = $this->choices;
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
								(int) ( $item['image'] ?? 0 ),
								(int) ( $item['category_id'] ?? 0 ),
								$choices
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
 * Category thumbnail attachment IDs for Customizer pickers.
 *
 * @return array<int, int>
 */
function elite_shipping_get_category_picker_image_map() {
	$map = array();

	if ( ! function_exists( 'elite_shipping_get_category_picker_choices' ) ) {
		return $map;
	}

	foreach ( elite_shipping_get_category_picker_choices() as $term_id => $label ) {
		$term_id = absint( $term_id );
		if ( $term_id <= 0 ) {
			continue;
		}

		$map[ $term_id ] = absint( get_term_meta( $term_id, 'thumbnail_id', true ) );
	}

	return $map;
}

/**
 * Resolve a mods list row category ID from stored data.
 *
 * @param int    $category_id Stored category ID.
 * @param string $title       Stored title.
 * @param array  $choices     Category choices.
 * @return int
 */
function elite_shipping_resolve_mods_list_category_id( $category_id, $title, $choices ) {
	$category_id = absint( $category_id );
	if ( $category_id > 0 ) {
		return $category_id;
	}

	$title = trim( (string) $title );
	if ( '' === $title ) {
		return 0;
	}

	foreach ( $choices as $choice_id => $label ) {
		if ( 0 === strcasecmp( (string) $label, $title ) ) {
			return absint( $choice_id );
		}
	}

	return 0;
}

/**
 * Render one modification display list row.
 *
 * @param int    $index       Row index.
 * @param string $title       Card title.
 * @param int    $image       Attachment ID.
 * @param int    $category_id Selected category ID.
 * @param array  $choices     Category choices.
 * @return string
 */
function elite_shipping_render_mods_list_item_row( $index, $title, $image, $category_id = 0, $choices = array() ) {
	$category_id = elite_shipping_resolve_mods_list_category_id( $category_id, $title, $choices );
	$preview_url   = $image ? wp_get_attachment_image_url( $image, 'thumbnail' ) : '';
	$preview_style = $preview_url ? 'background-image:url(' . esc_url( $preview_url ) . ');' : '';
	$image_label   = $preview_url
		? __( 'Change image', 'elite-shipping' )
		: __( 'Select image', 'elite-shipping' );

	natcasesort( $choices );

	$select = '<select class="elite-mods-list-item__select" aria-label="' . esc_attr__( 'Category', 'elite-shipping' ) . '">';
	$select .= '<option value="0">' . esc_html__( '— Select category —', 'elite-shipping' ) . '</option>';

	foreach ( $choices as $choice_id => $label ) {
		if ( $choice_id <= 0 ) {
			continue;
		}
		$select .= '<option value="' . esc_attr( (string) $choice_id ) . '"' . selected( $category_id, (int) $choice_id, false ) . '>' . esc_html( $label ) . '</option>';
	}

	$select .= '</select>';

	ob_start();
	?>
	<li class="elite-mods-list-item" data-index="<?php echo esc_attr( (string) $index ); ?>">
		<span class="elite-mods-list-item__num"><?php echo esc_html( (string) ( $index + 1 ) ); ?>.</span>
		<?php echo $select; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<input type="hidden" class="elite-mods-list-item__title" value="<?php echo esc_attr( $title ); ?>">
		<input type="hidden" class="elite-mods-list-item__category-id" value="<?php echo esc_attr( (string) $category_id ); ?>">
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
 * @return array<int, array{title: string, image: int, category_id: int}>
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
 * @return array<int, array{title: string, image: int, category_id: int}>
 */
function elite_shipping_normalize_mods_display_list_items( $items ) {
	$normalized = array();

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$category_id = absint( $item['category_id'] ?? 0 );
		$title       = sanitize_text_field( $item['title'] ?? '' );
		$image       = absint( $item['image'] ?? 0 );

		if ( $category_id > 0 ) {
			$term = get_term( $category_id, 'product_cat' );
			if ( $term instanceof WP_Term && ! is_wp_error( $term ) ) {
				$title = $term->name;
				if ( $image <= 0 ) {
					$image = absint( get_term_meta( $category_id, 'thumbnail_id', true ) );
				}
			} else {
				$category_id = 0;
			}
		}

		if ( '' === $title && $image <= 0 && $category_id <= 0 ) {
			continue;
		}

		$normalized[] = array(
			'title'       => $title,
			'image'       => $image,
			'category_id' => $category_id,
		);
	}

	return $normalized;
}

/**
 * Rows for the Customizer control (includes empty title rows while editing).
 *
 * @return array<int, array{title: string, image: int, category_id: int}>
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
 * @return array<int, array{title: string, image: int, category_id: int}>
 */
function elite_shipping_migrate_legacy_mod_slots() {
	$defaults = elite_shipping_get_default_mod_cards();
	$choices  = function_exists( 'elite_shipping_get_category_picker_choices' )
		? elite_shipping_get_category_picker_choices()
		: array();
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
			'title'       => (string) $title,
			'image'       => $image,
			'category_id' => elite_shipping_resolve_mods_list_category_id( 0, (string) $title, $choices ),
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
 * Resolve a modification card link from category data.
 *
 * @param int    $category_id Category term ID.
 * @param string $title       Card title fallback for legacy rows.
 * @return string
 */
function elite_shipping_get_mod_card_url( $category_id = 0, $title = '' ) {
	$category_id = absint( $category_id );

	if ( $category_id > 0 ) {
		$link = get_term_link( $category_id, 'product_cat' );
		if ( ! is_wp_error( $link ) ) {
			return (string) $link;
		}
	}

	$choices = function_exists( 'elite_shipping_get_category_picker_choices' )
		? elite_shipping_get_category_picker_choices()
		: array();
	$matched = elite_shipping_resolve_mods_list_category_id( 0, $title, $choices );

	if ( $matched > 0 ) {
		$link = get_term_link( $matched, 'product_cat' );
		if ( ! is_wp_error( $link ) ) {
			return (string) $link;
		}
	}

	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$shop_url = wc_get_page_permalink( 'shop' );
		if ( $shop_url ) {
			return (string) $shop_url;
		}
	}

	return home_url( '/shop/' );
}

/**
 * Modification cards for the homepage carousel.
 *
 * @return array<int, array{title: string, image_id: int, image_url: string, category_id: int, url: string}>
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
		$category_id = absint( $item['category_id'] ?? 0 );
		$title       = trim( (string) ( $item['title'] ?? '' ));

		if ( $category_id > 0 ) {
			$term = get_term( $category_id, 'product_cat' );
			if ( $term instanceof WP_Term && ! is_wp_error( $term ) ) {
				$title = $term->name;
			} else {
				continue;
			}
		} elseif ( '' === $title ) {
			continue;
		}

		$image_id  = absint( $item['image'] ?? 0 );
		$image_url = $image_id ? (string) wp_get_attachment_image_url( $image_id, 'large' ) : '';

		if ( ! $image_url && $category_id > 0 ) {
			$thumb_id = absint( get_term_meta( $category_id, 'thumbnail_id', true ) );
			if ( $thumb_id > 0 ) {
				$image_id  = $thumb_id;
				$image_url = (string) wp_get_attachment_image_url( $thumb_id, 'large' );
			}
		}

		if ( ! $image_url && $from_migration ) {
			$slot    = $index + 1;
			$default = $defaults[ $index ] ?? array( 'image' => '' );
			$image_url = elite_shipping_get_theme_mod_image_url(
				'elite_mod_image_' . $slot,
				(string) ( $default['image'] ?? '' )
			);
		}

		$url = '';
		if ( $category_id > 0 ) {
			$term_link = get_term_link( $category_id, 'product_cat' );
			if ( ! is_wp_error( $term_link ) ) {
				$url = (string) $term_link;
			}
		}

		if ( '' === $url ) {
			$url = elite_shipping_get_mod_card_url( 0, $title );
		}

		$resolved[] = array(
			'title'       => $title,
			'image_id'    => $image_id,
			'image_url'   => $image_url,
			'category_id' => $category_id,
			'url'         => $url,
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
		$category_id = absint( $item['category_id'] ?? 0 );
		$title       = trim( (string) ( $item['title'] ?? '' ) );

		if ( $category_id <= 0 && '' === $title ) {
			continue;
		}

		$image = absint( $item['image'] );
		if ( $image > 0 && ! wp_get_attachment_url( $image ) ) {
			$image = 0;
		}

		$clean[] = array(
			'title'       => $title,
			'image'       => $image,
			'category_id' => $category_id,
		);
	}

	return wp_json_encode( array_values( $clean ) );
}
