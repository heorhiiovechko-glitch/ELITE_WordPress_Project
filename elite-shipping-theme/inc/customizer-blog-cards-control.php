<?php
/**
 * Customizer control — Blog card list (Customize → Blog → Post cards).
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Unlimited blog card list.
	 */
	class Elite_Blog_Cards_List_Control extends WP_Customize_Control {

		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'elite_blog_cards_list';

		/**
		 * Render control markup.
		 */
		public function render_content() {
			$items = elite_shipping_get_blog_cards_list_rows();
			?>
			<div class="elite-blog-cards-list-control">
				<div class="elite-blog-cards-list-control__head">
					<?php if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif; ?>
					<button type="button" class="button button-secondary elite-blog-cards-add" aria-label="<?php esc_attr_e( 'Add card', 'elite-shipping' ); ?>">
						<span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
						<span class="screen-reader-text"><?php esc_html_e( 'Add', 'elite-shipping' ); ?></span>
					</button>
				</div>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
				<ul class="elite-blog-cards-list-items">
					<?php if ( empty( $items ) ) : ?>
						<li class="elite-blog-cards-list-items__empty"><?php esc_html_e( 'No cards yet. Click + to add a blog card.', 'elite-shipping' ); ?></li>
					<?php else : ?>
						<?php foreach ( $items as $index => $item ) : ?>
							<?php
							echo elite_shipping_render_blog_card_list_item_row( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								(int) $index,
								$item
							);
							?>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
				<input
					id="<?php echo esc_attr( $this->id ); ?>"
					type="hidden"
					class="elite-blog-cards-list-value"
					<?php $this->link(); ?>
					value="<?php echo esc_attr( wp_json_encode( array_values( $items ) ) ); ?>"
				>
			</div>
			<?php
		}
	}
}

/**
 * Icon button helper for blog cards control.
 *
 * @param string $icon  Dashicon slug.
 * @param string $label Accessible label.
 * @return string
 */
function elite_shipping_blog_cards_icon_html( $icon, $label ) {
	if ( function_exists( 'elite_shipping_customizer_dashicon_button_content' ) ) {
		return elite_shipping_customizer_dashicon_button_content( $icon, $label );
	}

	return '<span class="dashicons dashicons-' . esc_attr( $icon ) . '" aria-hidden="true"></span>'
		. '<span class="screen-reader-text">' . esc_html( $label ) . '</span>';
}

/**
 * Normalize structured details payload.
 *
 * @param mixed $details Raw details.
 * @return array{paragraphs: array<int, array>, faqs: array<int, array>}
 */
function elite_shipping_normalize_blog_card_details( $details ) {
	$paragraphs = array();
	$faqs       = array();

	// Migrate legacy plain-text details.
	if ( is_string( $details ) && '' !== trim( $details ) ) {
		$paragraphs[] = array(
			'title'    => '',
			'sections' => array(
				array(
					'title'  => '',
					'blocks' => array(
						array(
							'type'    => 'text',
							'content' => $details,
						),
					),
				),
			),
		);
	} elseif ( is_array( $details ) ) {
		$raw_paragraphs = isset( $details['paragraphs'] ) && is_array( $details['paragraphs'] ) ? $details['paragraphs'] : array();
		foreach ( $raw_paragraphs as $paragraph ) {
			if ( ! is_array( $paragraph ) ) {
				continue;
			}
			$sections = array();
			$raw_sections = isset( $paragraph['sections'] ) && is_array( $paragraph['sections'] ) ? $paragraph['sections'] : array();
			foreach ( $raw_sections as $section ) {
				if ( ! is_array( $section ) ) {
					continue;
				}
				$blocks     = array();
				$raw_blocks = isset( $section['blocks'] ) && is_array( $section['blocks'] ) ? $section['blocks'] : array();
				foreach ( $raw_blocks as $block ) {
					if ( ! is_array( $block ) ) {
						continue;
					}
					$type = sanitize_key( $block['type'] ?? 'text' );
					if ( ! in_array( $type, array( 'text', 'table', 'list' ), true ) ) {
						$type = 'text';
					}
					$item = array(
						'type'    => $type,
						'content' => sanitize_textarea_field( $block['content'] ?? '' ),
					);
					if ( 'table' === $type ) {
						$item['cell'] = elite_shipping_sanitize_blog_table_cell_role( $block['cell'] ?? '' );
					}
					$blocks[] = $item;
				}
				$title     = sanitize_text_field( $section['title'] ?? '' );
				$has_title = array_key_exists( 'hasTitle', $section )
					? ! empty( $section['hasTitle'] )
					: ( '' !== $title || empty( $blocks ) );

				$sections[] = array(
					'title'    => $title,
					'hasTitle' => $has_title,
					'blocks'   => $blocks,
				);
			}
			$paragraphs[] = array(
				'title'    => sanitize_text_field( $paragraph['title'] ?? '' ),
				'sections' => $sections,
			);
		}

		$raw_faqs = isset( $details['faqs'] ) && is_array( $details['faqs'] ) ? $details['faqs'] : array();
		foreach ( $raw_faqs as $faq ) {
			if ( ! is_array( $faq ) ) {
				continue;
			}
			$faqs[] = array(
				'title' => sanitize_text_field( $faq['title'] ?? '' ),
				'text'  => sanitize_textarea_field( $faq['text'] ?? '' ),
			);
		}
	}

	return array(
		'paragraphs' => $paragraphs,
		'faqs'       => $faqs,
	);
}

/**
 * Sanitize table cell role (h1-h3 header, c1-c3 column).
 *
 * @param mixed $role Raw role.
 * @return string
 */
function elite_shipping_sanitize_blog_table_cell_role( $role ) {
	$role = strtolower( preg_replace( '/[^a-z0-9]/', '', (string) $role ) );
	if ( preg_match( '/^[hc][1-3]$/', $role ) ) {
		return $role;
	}
	return '';
}

/**
 * Render one content row for the Customizer editor.
 *
 * @param string $type    text|table|list.
 * @param string $content Content value.
 * @param string $cell    Table cell role (h1-h3, c1-c3).
 * @return string
 */
function elite_shipping_render_blog_card_content_row( $type, $content = '', $cell = '' ) {
	$type = sanitize_key( $type );
	if ( ! in_array( $type, array( 'text', 'table', 'list' ), true ) ) {
		$type = 'text';
	}

	if ( 'table' === $type ) {
		$mark        = '#';
		$placeholder = __( 'Table content', 'elite-shipping' );
	} elseif ( 'list' === $type ) {
		$mark        = '•';
		$placeholder = __( 'List content', 'elite-shipping' );
	} else {
		$mark        = '-';
		$placeholder = __( 'Small paragraph content', 'elite-shipping' );
	}

	$cell = elite_shipping_sanitize_blog_table_cell_role( $cell );

	ob_start();
	?>
	<div class="elite-blog-content-row" data-type="<?php echo esc_attr( $type ); ?>">
		<span class="elite-blog-field-mark elite-blog-field-mark--<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $mark ); ?></span>
		<input type="text" class="elite-blog-section__content" value="<?php echo esc_attr( (string) $content ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" aria-label="<?php echo esc_attr( $placeholder ); ?>">
		<?php if ( 'table' === $type ) : ?>
			<input
				type="text"
				class="elite-blog-section__cell"
				value="<?php echo esc_attr( $cell ); ?>"
				placeholder="<?php esc_attr_e( 'h1', 'elite-shipping' ); ?>"
				aria-label="<?php esc_attr_e( 'Table cell role (h1-h3, c1-c3)', 'elite-shipping' ); ?>"
				title="<?php esc_attr_e( 'h1-h3 = header, c1-c3 = column', 'elite-shipping' ); ?>"
				maxlength="2"
			>
		<?php endif; ?>
		<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-icon-btn--remove elite-blog-remove-content" aria-label="<?php esc_attr_e( 'Remove content', 'elite-shipping' ); ?>" title="<?php esc_attr_e( 'Remove content', 'elite-shipping' ); ?>">
			<?php echo elite_shipping_blog_cards_icon_html( 'trash', __( 'Remove content', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Render one small-title row for the Customizer editor.
 *
 * @param string $title Title value.
 * @param int    $number Small title number (1-based).
 * @return string
 */
function elite_shipping_render_blog_card_title_row( $title = '', $number = 1 ) {
	$number = max( 1, absint( $number ) );
	ob_start();
	?>
	<div class="elite-blog-section__title-row">
		<span class="elite-blog-field-mark elite-blog-field-mark--title"><?php echo esc_html( 's' . $number ); ?></span>
		<input type="text" class="elite-blog-section__title" value="<?php echo esc_attr( (string) $title ); ?>" placeholder="<?php esc_attr_e( 'Small paragraph title', 'elite-shipping' ); ?>" aria-label="<?php esc_attr_e( 'Small paragraph title', 'elite-shipping' ); ?>">
		<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-icon-btn--remove elite-blog-remove-section" aria-label="<?php esc_attr_e( 'Remove small title', 'elite-shipping' ); ?>" title="<?php esc_attr_e( 'Remove small title', 'elite-shipping' ); ?>">
			<?php echo elite_shipping_blog_cards_icon_html( 'trash', __( 'Remove small title', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Render small titles and content rows as separate sibling fields.
 *
 * @param array $sections Sections.
 * @return string
 */
function elite_shipping_render_blog_card_sections( $sections ) {
	ob_start();
	$title_number = 0;
	foreach ( $sections as $section ) {
		if ( ! is_array( $section ) ) {
			continue;
		}

		$title     = (string) ( $section['title'] ?? '' );
		$blocks    = isset( $section['blocks'] ) && is_array( $section['blocks'] ) ? $section['blocks'] : array();
		$has_title = array_key_exists( 'hasTitle', $section )
			? ! empty( $section['hasTitle'] )
			: ( '' !== trim( $title ) || empty( $blocks ) );

		// Title rows stay independent from content rows.
		if ( $has_title ) {
			++$title_number;
			echo elite_shipping_render_blog_card_title_row( $title, $title_number ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		foreach ( $blocks as $block ) {
			if ( ! is_array( $block ) ) {
				continue;
			}
			echo elite_shipping_render_blog_card_content_row( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				(string) ( $block['type'] ?? 'text' ),
				(string) ( $block['content'] ?? '' ),
				(string) ( $block['cell'] ?? '' )
			);
		}
	}
	return ob_get_clean();
}

/**
 * Toolbar under paragraph title: new small title + content type buttons.
 *
 * @return string
 */
function elite_shipping_render_blog_card_paragraph_toolbar() {
	ob_start();
	?>
	<div class="elite-blog-paragraph__toolbar">
		<button type="button" class="elite-blog-cards-icon-btn elite-blog-add-section" aria-label="<?php esc_attr_e( 'New small title', 'elite-shipping' ); ?>" title="<?php esc_attr_e( 'New small title', 'elite-shipping' ); ?>">
			<?php echo elite_shipping_blog_cards_icon_html( 'plus-alt', __( 'New small title', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
		<button type="button" class="elite-blog-cards-icon-btn elite-blog-add-content" data-type="text" aria-label="<?php esc_attr_e( 'Add text content', 'elite-shipping' ); ?>" title="<?php esc_attr_e( 'Add text content', 'elite-shipping' ); ?>">
			<?php echo elite_shipping_blog_cards_icon_html( 'text', __( 'Add text content', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
		<button type="button" class="elite-blog-cards-icon-btn elite-blog-add-content" data-type="table" aria-label="<?php esc_attr_e( 'Add table content', 'elite-shipping' ); ?>" title="<?php esc_attr_e( 'Add table content', 'elite-shipping' ); ?>">
			<?php echo elite_shipping_blog_cards_icon_html( 'editor-table', __( 'Add table content', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
		<button type="button" class="elite-blog-cards-icon-btn elite-blog-add-content" data-type="list" aria-label="<?php esc_attr_e( 'Add list content', 'elite-shipping' ); ?>" title="<?php esc_attr_e( 'Add list content', 'elite-shipping' ); ?>">
			<?php echo elite_shipping_blog_cards_icon_html( 'editor-ul', __( 'Add list content', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Render paragraphs under a card introduction.
 *
 * @param array $paragraphs Paragraphs.
 * @return string
 */
function elite_shipping_render_blog_card_paragraphs( $paragraphs ) {
	ob_start();
	foreach ( $paragraphs as $index => $paragraph ) {
		?>
		<div class="elite-blog-paragraph">
			<div class="elite-blog-paragraph__top">
				<span class="elite-blog-paragraph__num"><?php echo esc_html( sprintf( __( 'Paragraph %d', 'elite-shipping' ), (int) $index + 1 ) ); ?></span>
				<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-icon-btn--remove elite-blog-remove-paragraph" aria-label="<?php esc_attr_e( 'Remove paragraph', 'elite-shipping' ); ?>" title="<?php esc_attr_e( 'Remove paragraph', 'elite-shipping' ); ?>">
					<?php echo elite_shipping_blog_cards_icon_html( 'trash', __( 'Remove paragraph', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
			<input type="text" class="elite-blog-paragraph__title" value="<?php echo esc_attr( (string) ( $paragraph['title'] ?? '' ) ); ?>" placeholder="<?php esc_attr_e( 'Paragraph title', 'elite-shipping' ); ?>" aria-label="<?php esc_attr_e( 'Paragraph title', 'elite-shipping' ); ?>">
			<?php echo elite_shipping_render_blog_card_paragraph_toolbar(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="elite-blog-paragraph__sections">
				<?php echo elite_shipping_render_blog_card_sections( isset( $paragraph['sections'] ) && is_array( $paragraph['sections'] ) ? $paragraph['sections'] : array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}
	return ob_get_clean();
}

/**
 * Render FAQs inside details panel.
 *
 * @param array $faqs FAQs.
 * @return string
 */
function elite_shipping_render_blog_card_faqs( $faqs ) {
	ob_start();
	foreach ( $faqs as $faq ) {
		?>
		<div class="elite-blog-faq">
			<div class="elite-blog-faq__top">
				<input type="text" class="elite-blog-faq__title" value="<?php echo esc_attr( (string) ( $faq['title'] ?? '' ) ); ?>" placeholder="<?php esc_attr_e( 'FAQ title', 'elite-shipping' ); ?>" aria-label="<?php esc_attr_e( 'FAQ title', 'elite-shipping' ); ?>">
				<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-icon-btn--remove elite-blog-remove-faq" aria-label="<?php esc_attr_e( 'Remove FAQ', 'elite-shipping' ); ?>">
					<?php echo elite_shipping_blog_cards_icon_html( 'trash', __( 'Remove FAQ', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
			<textarea class="elite-blog-faq__text" rows="3" placeholder="<?php esc_attr_e( 'FAQ text', 'elite-shipping' ); ?>" aria-label="<?php esc_attr_e( 'FAQ text', 'elite-shipping' ); ?>"><?php echo esc_textarea( (string) ( $faq['text'] ?? '' ) ); ?></textarea>
		</div>
		<?php
	}
	return ob_get_clean();
}

/**
 * Render one blog card list row.
 *
 * @param int   $index Row index.
 * @param array $item  Card data.
 * @return string
 */
function elite_shipping_render_blog_card_list_item_row( $index, $item ) {
	$title   = (string) ( $item['title'] ?? '' );
	$date    = (string) ( $item['date'] ?? '' );
	$image   = absint( $item['image'] ?? 0 );
	$intro   = (string) ( $item['intro'] ?? '' );
	$details = elite_shipping_normalize_blog_card_details( $item['details'] ?? array() );

	$preview_url   = $image ? wp_get_attachment_image_url( $image, 'thumbnail' ) : '';
	$preview_style = $preview_url ? 'background-image:url(' . esc_url( $preview_url ) . ');' : '';
	$image_label   = $preview_url
		? __( 'Change image', 'elite-shipping' )
		: __( 'Select image', 'elite-shipping' );

	ob_start();
	?>
	<li class="elite-blog-cards-list-item" data-index="<?php echo esc_attr( (string) $index ); ?>">
		<div class="elite-blog-cards-list-item__top">
			<span class="elite-blog-cards-list-item__num"><?php echo esc_html( sprintf( __( 'Card %d', 'elite-shipping' ), $index + 1 ) ); ?></span>
			<div class="elite-blog-cards-list-item__actions">
				<input type="date" class="elite-blog-cards-list-item__date" value="<?php echo esc_attr( $date ); ?>" aria-label="<?php esc_attr_e( 'Date', 'elite-shipping' ); ?>">
				<input type="hidden" class="elite-blog-cards-list-item__image-id" value="<?php echo esc_attr( (string) $image ); ?>">
				<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-select-image<?php echo $preview_url ? ' has-image' : ''; ?>" aria-label="<?php echo esc_attr( $image_label ); ?>" <?php echo $preview_url ? 'style="' . esc_attr( $preview_style ) . '"' : ''; ?>>
					<?php echo elite_shipping_blog_cards_icon_html( 'format-image', $image_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
				<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-icon-btn--remove elite-blog-cards-remove" aria-label="<?php esc_attr_e( 'Remove', 'elite-shipping' ); ?>">
					<?php echo elite_shipping_blog_cards_icon_html( 'trash', __( 'Remove', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
		</div>
		<input type="text" class="elite-blog-cards-list-item__title" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php esc_attr_e( 'Title', 'elite-shipping' ); ?>" aria-label="<?php esc_attr_e( 'Title', 'elite-shipping' ); ?>">
		<textarea class="elite-blog-cards-list-item__intro" rows="3" placeholder="<?php esc_attr_e( 'Introduction', 'elite-shipping' ); ?>" aria-label="<?php esc_attr_e( 'Introduction', 'elite-shipping' ); ?>"><?php echo esc_textarea( $intro ); ?></textarea>

		<div class="elite-blog-paragraphs">
			<?php echo elite_shipping_render_blog_card_paragraphs( $details['paragraphs'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<button type="button" class="button button-secondary elite-blog-add-paragraph">
			<?php esc_html_e( 'Add new paragraph', 'elite-shipping' ); ?>
		</button>

		<div class="elite-blog-faqs-block">
			<div class="elite-blog-faqs-block__head">
				<span class="elite-blog-faqs-block__title"><?php esc_html_e( 'FAQs', 'elite-shipping' ); ?></span>
				<button type="button" class="button button-secondary elite-blog-add-faq">
					<?php esc_html_e( 'Add new FAQ', 'elite-shipping' ); ?>
				</button>
			</div>
			<div class="elite-blog-faqs">
				<?php echo elite_shipping_render_blog_card_faqs( $details['faqs'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</li>
	<?php
	return ob_get_clean();
}

/**
 * Normalize blog card list items.
 *
 * @param array $items      Raw items.
 * @param bool  $keep_empty Keep incomplete rows.
 * @return array<int, array>
 */
function elite_shipping_normalize_blog_cards_list_items( $items, $keep_empty = false ) {
	$normalized = array();

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$title   = sanitize_text_field( $item['title'] ?? '' );
		$date    = sanitize_text_field( $item['date'] ?? '' );
		$image   = absint( $item['image'] ?? 0 );
		$intro   = sanitize_textarea_field( $item['intro'] ?? '' );
		$details = elite_shipping_normalize_blog_card_details( $item['details'] ?? array() );

		$has_details = ! empty( $details['paragraphs'] ) || ! empty( $details['faqs'] );

		if ( '' === $title && $image <= 0 && '' === $intro && ! $has_details && ! $keep_empty ) {
			continue;
		}

		if ( ! $keep_empty && '' === $title ) {
			continue;
		}

		if ( $date && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			$timestamp = strtotime( $date );
			$date      = $timestamp ? gmdate( 'Y-m-d', $timestamp ) : '';
		}

		$normalized[] = array(
			'title'   => $title,
			'date'    => $date,
			'image'   => $image,
			'intro'   => $intro,
			'details' => $details,
		);
	}

	return $normalized;
}

/**
 * Parse blog cards list from theme mod.
 *
 * @return array<int, array>
 */
function elite_shipping_parse_blog_cards_list_theme_mod() {
	$stored = get_theme_mod( 'elite_blog_cards_list', '' );

	if ( is_array( $stored ) ) {
		return elite_shipping_normalize_blog_cards_list_items( $stored );
	}

	if ( is_string( $stored ) && '' !== $stored ) {
		$decoded = json_decode( $stored, true );
		if ( is_array( $decoded ) ) {
			return elite_shipping_normalize_blog_cards_list_items( $decoded );
		}
	}

	return array();
}

/**
 * Rows for the Customizer control.
 *
 * @return array<int, array>
 */
function elite_shipping_get_blog_cards_list_rows() {
	$stored = get_theme_mod( 'elite_blog_cards_list', null );

	if ( null === $stored || '' === $stored ) {
		return array();
	}

	if ( is_array( $stored ) ) {
		return elite_shipping_normalize_blog_cards_list_items( $stored, true );
	}

	if ( is_string( $stored ) ) {
		$decoded = json_decode( $stored, true );
		if ( ! is_array( $decoded ) ) {
			return array();
		}
		return elite_shipping_normalize_blog_cards_list_items( $decoded, true );
	}

	return array();
}

/**
 * Sanitize blog cards list theme mod.
 *
 * @param mixed $value Raw value.
 * @return string JSON
 */
function elite_shipping_sanitize_blog_cards_list( $value ) {
	if ( is_string( $value ) ) {
		$decoded = json_decode( wp_unslash( $value ), true );
		$value   = is_array( $decoded ) ? $decoded : array();
	}

	if ( ! is_array( $value ) ) {
		$value = array();
	}

	return wp_json_encode( elite_shipping_normalize_blog_cards_list_items( $value, true ) );
}

/**
 * Whether a custom blog card list is configured.
 *
 * @return bool
 */
function elite_shipping_blog_uses_cards_list() {
	return ! empty( elite_shipping_parse_blog_cards_list_theme_mod() );
}

/**
 * Build a table from cell roles (h1-h3 headers, c1-c3 columns).
 *
 * @param array $cells Table block items.
 * @return string
 */
function elite_shipping_render_blog_card_table_html( $cells ) {
	$headers     = array();
	$rows        = array();
	$current_row = array();
	$has_roles   = false;
	$legacy      = array();

	foreach ( $cells as $cell ) {
		if ( ! is_array( $cell ) ) {
			continue;
		}
		$content = trim( (string) ( $cell['content'] ?? '' ) );
		$role    = elite_shipping_sanitize_blog_table_cell_role( $cell['cell'] ?? '' );

		if ( '' === $role ) {
			if ( '' !== $content ) {
				$legacy[] = $content;
			}
			continue;
		}

		$has_roles = true;
		$col       = (int) substr( $role, 1, 1 );

		if ( 'h' === $role[0] ) {
			$headers[ $col ] = $content;
			continue;
		}

		// Start a new body row whenever c1 appears after values already exist.
		if ( 1 === $col && ! empty( $current_row ) ) {
			$rows[]      = $current_row;
			$current_row = array();
		}
		$current_row[ $col ] = $content;
	}

	if ( ! empty( $current_row ) ) {
		$rows[] = $current_row;
	}

	// Legacy pipe/newline table content (no cell roles).
	if ( ! $has_roles && ! empty( $legacy ) ) {
		ob_start();
		echo '<div class="apex-blog-detail-table-wrap"><table class="apex-blog-detail-table">';
		foreach ( $legacy as $legacy_i => $legacy_content ) {
			$legacy_rows = preg_split( '/\r\n|\r|\n/', $legacy_content );
			foreach ( $legacy_rows as $row_i => $row ) {
				$parts = array_map( 'trim', explode( '|', (string) $row ) );
				if ( '' === implode( '', $parts ) ) {
					continue;
				}
				echo '<tr>';
				foreach ( $parts as $part ) {
					$tag = ( 0 === $legacy_i && 0 === $row_i ) ? 'th' : 'td';
					echo '<' . $tag . '>' . esc_html( $part ) . '</' . $tag . '>';
				}
				echo '</tr>';
			}
		}
		echo '</table></div>';
		return ob_get_clean();
	}

	if ( empty( $headers ) && empty( $rows ) ) {
		return '';
	}

	$max_col = 0;
	foreach ( array_keys( $headers ) as $col ) {
		$max_col = max( $max_col, (int) $col );
	}
	foreach ( $rows as $row ) {
		foreach ( array_keys( $row ) as $col ) {
			$max_col = max( $max_col, (int) $col );
		}
	}
	$max_col = max( 1, $max_col );

	ob_start();
	echo '<div class="apex-blog-detail-table-wrap"><table class="apex-blog-detail-table">';
	if ( ! empty( $headers ) ) {
		echo '<tr>';
		for ( $col = 1; $col <= $max_col; $col++ ) {
			echo '<th>' . esc_html( (string) ( $headers[ $col ] ?? '' ) ) . '</th>';
		}
		echo '</tr>';
	}
	foreach ( $rows as $row ) {
		echo '<tr>';
		for ( $col = 1; $col <= $max_col; $col++ ) {
			echo '<td>' . esc_html( (string) ( $row[ $col ] ?? '' ) ) . '</td>';
		}
		echo '</tr>';
	}
	echo '</table></div>';
	return ob_get_clean();
}

/**
 * Render structured details HTML for the front-end.
 *
 * @param array|string $details Details payload.
 * @return string
 */
function elite_shipping_render_blog_card_details_html( $details ) {
	$data = elite_shipping_normalize_blog_card_details( $details );
	ob_start();

	foreach ( $data['paragraphs'] as $paragraph ) {
		$title = trim( (string) ( $paragraph['title'] ?? '' ) );
		echo '<section class="apex-blog-detail-paragraph">';
		if ( '' !== $title ) {
			echo '<h2 class="apex-blog-detail-paragraph__title">' . esc_html( $title ) . '</h2>';
		}
		$sections = isset( $paragraph['sections'] ) && is_array( $paragraph['sections'] ) ? $paragraph['sections'] : array();
		foreach ( $sections as $section ) {
			$section_title = trim( (string) ( $section['title'] ?? '' ) );
			echo '<div class="apex-blog-detail-section">';
			if ( '' !== $section_title ) {
				echo '<h3 class="apex-blog-detail-section__title">' . esc_html( $section_title ) . '</h3>';
			}
			$blocks = isset( $section['blocks'] ) && is_array( $section['blocks'] ) ? $section['blocks'] : array();
			$bi     = 0;
			$bcount = count( $blocks );
			while ( $bi < $bcount ) {
				$block = $blocks[ $bi ];
				if ( ! is_array( $block ) ) {
					++$bi;
					continue;
				}
				$type = (string) ( $block['type'] ?? 'text' );

				if ( 'table' === $type ) {
					$table_cells = array();
					while ( $bi < $bcount && is_array( $blocks[ $bi ] ) && 'table' === (string) ( $blocks[ $bi ]['type'] ?? '' ) ) {
						$table_cells[] = $blocks[ $bi ];
						++$bi;
					}
					echo elite_shipping_render_blog_card_table_html( $table_cells ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					continue;
				}

				$content = trim( (string) ( $block['content'] ?? '' ) );
				++$bi;
				if ( '' === $content ) {
					continue;
				}
				if ( 'list' === $type ) {
					$lines = preg_split( '/\r\n|\r|\n/', $content );
					echo '<ul class="apex-blog-detail-list">';
					foreach ( $lines as $line ) {
						$line = trim( (string) $line );
						if ( '' !== $line ) {
							echo '<li>' . esc_html( $line ) . '</li>';
						}
					}
					echo '</ul>';
				} else {
					echo '<div class="apex-blog-detail-text">' . wp_kses_post( wpautop( $content ) ) . '</div>';
				}
			}
			echo '</div>';
		}
		echo '</section>';
	}

	if ( ! empty( $data['faqs'] ) ) {
		echo '<section class="apex-blog-detail-faqs"><h2 class="apex-blog-detail-faqs__title">' . esc_html__( 'FAQs', 'elite-shipping' ) . '</h2>';
		foreach ( $data['faqs'] as $faq ) {
			$faq_title = trim( (string) ( $faq['title'] ?? '' ) );
			$faq_text  = trim( (string) ( $faq['text'] ?? '' ) );
			if ( '' === $faq_title && '' === $faq_text ) {
				continue;
			}
			echo '<div class="apex-blog-detail-faq">';
			if ( '' !== $faq_title ) {
				echo '<h3 class="apex-blog-detail-faq__title">' . esc_html( $faq_title ) . '</h3>';
			}
			if ( '' !== $faq_text ) {
				echo '<div class="apex-blog-detail-faq__text">' . wp_kses_post( wpautop( $faq_text ) ) . '</div>';
			}
			echo '</div>';
		}
		echo '</section>';
	}

	return ob_get_clean();
}

/**
 * Resolve customizer blog cards for the archive.
 *
 * @return array<int, array>
 */
function elite_shipping_get_customizer_blog_cards() {
	$items  = elite_shipping_parse_blog_cards_list_theme_mod();
	$author = get_theme_mod( 'elite_contact_company_name', ELITE_COMPANY_NAME );
	$blog   = elite_shipping_get_page_url( 'our-blog', '/our-blog/' );
	$ready  = array();

	foreach ( $items as $index => $item ) {
		$title = trim( (string) ( $item['title'] ?? '' ) );
		if ( '' === $title ) {
			continue;
		}

		$date_raw  = (string) ( $item['date'] ?? '' );
		$timestamp = $date_raw ? strtotime( $date_raw . ' 12:00:00' ) : false;
		if ( ! $timestamp ) {
			$timestamp = time();
		}

		$image_id  = absint( $item['image'] ?? 0 );
		$image_url = $image_id ? (string) wp_get_attachment_image_url( $image_id, 'large' ) : '';
		if ( ! $image_url ) {
			$image_url = elite_shipping_get_blog_image_url( 'blog_1.webp' );
		}

		$slug = sanitize_title( $title );
		if ( '' === $slug ) {
			$slug = 'card-' . ( $index + 1 );
		}

		$ready[] = array(
			'title'    => $title,
			'url'      => add_query_arg( 'card', $slug, $blog ),
			'image'    => $image_url,
			'excerpt'  => (string) ( $item['intro'] ?? '' ),
			'details'  => isset( $item['details'] ) ? $item['details'] : array(),
			'date'     => gmdate( 'F j, Y', $timestamp ),
			'datetime' => gmdate( 'Y-m-d', $timestamp ),
			'day'      => gmdate( 'j', $timestamp ),
			'month'    => gmdate( 'M', $timestamp ),
			'author'   => $author,
			'slug'     => $slug,
		);
	}

	return $ready;
}

/**
 * Find one customizer blog card by slug.
 *
 * @param string $slug Card slug.
 * @return array|null
 */
function elite_shipping_get_customizer_blog_card_by_slug( $slug ) {
	$slug = sanitize_title( $slug );
	if ( '' === $slug ) {
		return null;
	}

	foreach ( elite_shipping_get_customizer_blog_cards() as $card ) {
		if ( isset( $card['slug'] ) && $card['slug'] === $slug ) {
			return $card;
		}
	}

	return null;
}

/**
 * Next older customizer blog card (later in the Card list = older).
 *
 * @param string $slug Current card slug.
 * @return array|null
 */
function elite_shipping_get_customizer_blog_card_older( $slug ) {
	$slug  = sanitize_title( $slug );
	$cards = elite_shipping_get_customizer_blog_cards();
	$count = count( $cards );

	for ( $i = 0; $i < $count; $i++ ) {
		if ( empty( $cards[ $i ]['slug'] ) || $cards[ $i ]['slug'] !== $slug ) {
			continue;
		}
		if ( isset( $cards[ $i + 1 ] ) ) {
			return $cards[ $i + 1 ];
		}
		return null;
	}

	return null;
}
