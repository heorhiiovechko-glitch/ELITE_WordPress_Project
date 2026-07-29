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
 * Collapse/expand toggle for blog card and paragraph panels.
 *
 * @param string $label Accessible label.
 * @return string
 */
function elite_shipping_render_blog_card_toggle_button( $label = '' ) {
	$label = $label ? $label : __( 'Toggle section', 'elite-shipping' );

	return '<button type="button" class="elite-blog-cards-icon-btn elite-blog-toggle" aria-expanded="true" aria-label="' . esc_attr( $label ) . '" title="' . esc_attr( $label ) . '">'
		. elite_shipping_blog_cards_icon_html( 'arrow-down-alt2', $label )
		. '</button>';
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
						$pos          = elite_shipping_sanitize_blog_table_position( $block );
						$item['row']  = $pos['row'];
						$item['col']  = $pos['col'];
						// Keep legacy role briefly for ordered migration of c1/c2 groups.
						if ( ! empty( $block['cell'] ) && ( empty( $block['row'] ) || empty( $block['col'] ) ) ) {
							$item['_cell'] = elite_shipping_sanitize_blog_table_cell_role( $block['cell'] );
						}
					}
					$blocks[] = $item;
				}
				$blocks = elite_shipping_migrate_blog_table_block_positions( $blocks );
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

			// Merge consecutive untitled sections (fixes older saves that
			// split each table cell into its own section).
			$merged_sections = array();
			foreach ( $sections as $section ) {
				$sec_title = trim( (string) ( $section['title'] ?? '' ) );
				$last      = ! empty( $merged_sections ) ? $merged_sections[ count( $merged_sections ) - 1 ] : null;
				if ( $last && '' === $sec_title && '' === trim( (string) ( $last['title'] ?? '' ) ) ) {
					$merged_sections[ count( $merged_sections ) - 1 ]['blocks'] = array_merge(
						isset( $last['blocks'] ) && is_array( $last['blocks'] ) ? $last['blocks'] : array(),
						isset( $section['blocks'] ) && is_array( $section['blocks'] ) ? $section['blocks'] : array()
					);
					continue;
				}
				$merged_sections[] = $section;
			}

			$paragraphs[] = array(
				'title'    => sanitize_text_field( $paragraph['title'] ?? '' ),
				'sections' => $merged_sections,
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
 * Sanitize legacy table cell role (h1-h3 header, c1-c3 column).
 *
 * @param mixed $role Raw role.
 * @return string
 */
function elite_shipping_sanitize_blog_table_cell_role( $role ) {
	$role = strtolower( preg_replace( '/[^a-z0-9]/', '', (string) $role ) );
	if ( preg_match( '/^[hc][1-9]$/', $role ) ) {
		return $role;
	}
	return '';
}

/**
 * Sanitize table cell row/column position.
 *
 * @param array $block Raw block.
 * @return array{row:int,col:int}
 */
function elite_shipping_sanitize_blog_table_position( $block ) {
	$block = is_array( $block ) ? $block : array();
	$row   = absint( $block['row'] ?? 0 );
	$col   = absint( $block['col'] ?? 0 );

	if ( $row < 1 || $col < 1 ) {
		$role = elite_shipping_sanitize_blog_table_cell_role( $block['cell'] ?? '' );
		if ( $role ) {
			$col = max( 1, (int) substr( $role, 1, 1 ) );
			$row = ( 'h' === $role[0] ) ? 1 : 2;
		} else {
			$row = 1;
			$col = 1;
		}
	}

	return array(
		'row' => max( 1, min( 50, $row ) ),
		'col' => max( 1, min( 20, $col ) ),
	);
}

/**
 * Convert legacy h/c cell roles into explicit row/col for a block list.
 *
 * @param array $blocks Content blocks.
 * @return array
 */
function elite_shipping_migrate_blog_table_block_positions( $blocks ) {
	if ( ! is_array( $blocks ) ) {
		return array();
	}

	$body_row = 2;
	$has_body = false;

	foreach ( $blocks as &$block ) {
		if ( ! is_array( $block ) || 'table' !== ( $block['type'] ?? '' ) ) {
			continue;
		}

		$legacy = elite_shipping_sanitize_blog_table_cell_role( $block['_cell'] ?? ( $block['cell'] ?? '' ) );
		unset( $block['_cell'], $block['cell'] );

		// Prefer explicit row/col (1:1 = row 1, col 1).
		$has_explicit = ! empty( $block['row'] ) && ! empty( $block['col'] );
		if ( $has_explicit ) {
			$pos          = elite_shipping_sanitize_blog_table_position( $block );
			$block['row'] = $pos['row'];
			$block['col'] = $pos['col'];
			continue;
		}

		if ( $legacy ) {
			$col = max( 1, (int) substr( $legacy, 1, 1 ) );
			if ( 'h' === $legacy[0] ) {
				$block['row'] = 1;
				$block['col'] = $col;
			} else {
				if ( 1 === $col && $has_body ) {
					++$body_row;
				}
				$has_body     = true;
				$block['row'] = $body_row;
				$block['col'] = $col;
			}
			continue;
		}

		$pos          = elite_shipping_sanitize_blog_table_position( $block );
		$block['row'] = $pos['row'];
		$block['col'] = $pos['col'];
	}
	unset( $block );

	return $blocks;
}

/**
 * Render one content row for the Customizer editor.
 *
 * @param string   $type    text|table|list.
 * @param string   $content Content value.
 * @param int|array $row_or_cell Row number, or legacy cell role string.
 * @param int      $col     Column number.
 * @return string
 */
function elite_shipping_render_blog_card_content_row( $type, $content = '', $row_or_cell = 1, $col = 1 ) {
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

	if ( is_string( $row_or_cell ) && preg_match( '/^[hc][1-9]$/', strtolower( $row_or_cell ) ) ) {
		$pos = elite_shipping_sanitize_blog_table_position(
			array(
				'cell' => $row_or_cell,
			)
		);
	} else {
		$pos = elite_shipping_sanitize_blog_table_position(
			array(
				'row' => $row_or_cell,
				'col' => $col,
			)
		);
	}

	ob_start();
	?>
	<div class="elite-blog-content-row" data-type="<?php echo esc_attr( $type ); ?>">
		<span class="elite-blog-field-mark elite-blog-field-mark--<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $mark ); ?></span>
		<input type="text" class="elite-blog-section__content" value="<?php echo esc_attr( (string) $content ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" aria-label="<?php echo esc_attr( $placeholder ); ?>">
		<?php if ( 'table' === $type ) : ?>
			<span class="elite-blog-section__pos">
				<span class="elite-blog-spin" title="<?php esc_attr_e( 'Row number (1 = header)', 'elite-shipping' ); ?>">
					<button type="button" class="elite-blog-spin__btn elite-blog-spin__up" data-spin="up" aria-label="<?php esc_attr_e( 'Increase row', 'elite-shipping' ); ?>">▲</button>
					<input type="number" class="elite-blog-section__row" value="<?php echo esc_attr( (string) $pos['row'] ); ?>" min="1" max="50" step="1" aria-label="<?php esc_attr_e( 'Row number', 'elite-shipping' ); ?>">
					<button type="button" class="elite-blog-spin__btn elite-blog-spin__down" data-spin="down" aria-label="<?php esc_attr_e( 'Decrease row', 'elite-shipping' ); ?>">▼</button>
				</span>
				<span class="elite-blog-spin" title="<?php esc_attr_e( 'Column number', 'elite-shipping' ); ?>">
					<button type="button" class="elite-blog-spin__btn elite-blog-spin__up" data-spin="up" aria-label="<?php esc_attr_e( 'Increase column', 'elite-shipping' ); ?>">▲</button>
					<input type="number" class="elite-blog-section__col" value="<?php echo esc_attr( (string) $pos['col'] ); ?>" min="1" max="20" step="1" aria-label="<?php esc_attr_e( 'Column number', 'elite-shipping' ); ?>">
					<button type="button" class="elite-blog-spin__btn elite-blog-spin__down" data-spin="down" aria-label="<?php esc_attr_e( 'Decrease column', 'elite-shipping' ); ?>">▼</button>
				</span>
			</span>
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
		$blocks    = elite_shipping_migrate_blog_table_block_positions( $blocks );
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
			$pos = elite_shipping_sanitize_blog_table_position( $block );
			echo elite_shipping_render_blog_card_content_row( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				(string) ( $block['type'] ?? 'text' ),
				(string) ( $block['content'] ?? '' ),
				$pos['row'],
				$pos['col']
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
				<div class="elite-blog-paragraph__actions">
					<?php echo elite_shipping_render_blog_card_toggle_button( __( 'Toggle paragraph', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-icon-btn--remove elite-blog-remove-paragraph" aria-label="<?php esc_attr_e( 'Remove paragraph', 'elite-shipping' ); ?>" title="<?php esc_attr_e( 'Remove paragraph', 'elite-shipping' ); ?>">
						<?php echo elite_shipping_blog_cards_icon_html( 'trash', __( 'Remove paragraph', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
			</div>
			<div class="elite-blog-paragraph__body">
				<input type="text" class="elite-blog-paragraph__title" value="<?php echo esc_attr( (string) ( $paragraph['title'] ?? '' ) ); ?>" placeholder="<?php esc_attr_e( 'Paragraph title', 'elite-shipping' ); ?>" aria-label="<?php esc_attr_e( 'Paragraph title', 'elite-shipping' ); ?>">
				<?php echo elite_shipping_render_blog_card_paragraph_toolbar(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="elite-blog-paragraph__sections">
					<?php echo elite_shipping_render_blog_card_sections( isset( $paragraph['sections'] ) && is_array( $paragraph['sections'] ) ? $paragraph['sections'] : array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
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
	$title      = (string) ( $item['title'] ?? '' );
	$date       = (string) ( $item['date'] ?? '' );
	$image      = absint( $item['image'] ?? 0 );
	$image_file = sanitize_file_name( (string) ( $item['image_file'] ?? '' ) );
	$slug       = sanitize_title( (string) ( $item['slug'] ?? '' ) );
	$intro      = (string) ( $item['intro'] ?? '' );
	$short_text = (string) ( $item['short_text'] ?? '' );
	$details    = elite_shipping_normalize_blog_card_details( $item['details'] ?? array() );

	$preview_url = $image ? wp_get_attachment_image_url( $image, 'thumbnail' ) : '';
	if ( ! $preview_url && $image_file && function_exists( 'elite_shipping_get_blog_image_url' ) ) {
		$preview_url = elite_shipping_get_blog_image_url( $image_file );
	}
	$preview_style = $preview_url ? 'background-image:url(' . esc_url( $preview_url ) . ');' : '';
	$image_label   = $preview_url
		? __( 'Change image', 'elite-shipping' )
		: __( 'Select image', 'elite-shipping' );

	ob_start();
	?>
	<li class="elite-blog-cards-list-item" data-index="<?php echo esc_attr( (string) $index ); ?>"<?php echo $slug ? ' data-slug="' . esc_attr( $slug ) . '"' : ''; ?>>
		<div class="elite-blog-cards-list-item__top">
			<span class="elite-blog-cards-list-item__num"><?php echo esc_html( 'Card' . ( $index + 1 ) ); ?></span>
			<div class="elite-blog-cards-list-item__actions">
				<input type="date" class="elite-blog-cards-list-item__date" value="<?php echo esc_attr( $date ); ?>" aria-label="<?php esc_attr_e( 'Date', 'elite-shipping' ); ?>">
				<input type="hidden" class="elite-blog-cards-list-item__image-id" value="<?php echo esc_attr( (string) $image ); ?>">
				<input type="hidden" class="elite-blog-cards-list-item__image-file" value="<?php echo esc_attr( $image_file ); ?>">
				<input type="hidden" class="elite-blog-cards-list-item__slug" value="<?php echo esc_attr( $slug ); ?>">
				<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-select-image<?php echo $preview_url ? ' has-image' : ''; ?>" aria-label="<?php echo esc_attr( $image_label ); ?>" <?php echo $preview_url ? 'style="' . esc_attr( $preview_style ) . '"' : ''; ?>>
					<?php echo elite_shipping_blog_cards_icon_html( 'format-image', $image_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
				<?php echo elite_shipping_render_blog_card_toggle_button( __( 'Toggle card', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-icon-btn--remove elite-blog-cards-remove" aria-label="<?php esc_attr_e( 'Remove', 'elite-shipping' ); ?>">
					<?php echo elite_shipping_blog_cards_icon_html( 'trash', __( 'Remove', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
		</div>
		<div class="elite-blog-cards-list-item__body">
		<input type="text" class="elite-blog-cards-list-item__title" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php esc_attr_e( 'Title', 'elite-shipping' ); ?>" aria-label="<?php esc_attr_e( 'Title', 'elite-shipping' ); ?>">
		<textarea class="elite-blog-cards-list-item__intro" rows="3" placeholder="<?php esc_attr_e( 'Introduction', 'elite-shipping' ); ?>" aria-label="<?php esc_attr_e( 'Introduction', 'elite-shipping' ); ?>"><?php echo esc_textarea( $intro ); ?></textarea>
		<input type="text" class="elite-blog-cards-list-item__short-text" value="<?php echo esc_attr( $short_text ); ?>" placeholder="<?php esc_attr_e( 'Short text', 'elite-shipping' ); ?>" aria-label="<?php esc_attr_e( 'Short text', 'elite-shipping' ); ?>">

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
		</div>
	</li>
	<?php
	return ob_get_clean();
}

/**
 * Clean blog card prose input (literal \\n, broken ".nNext" joins).
 *
 * @param string $text       Raw text.
 * @param bool   $allow_nl   Keep real newlines.
 * @return string
 */
function elite_shipping_clean_blog_card_prose_input( $text, $allow_nl = true ) {
	$text = (string) $text;
	if ( '' === $text ) {
		return '';
	}

	// Literal escape sequences from pasted JSON / bad exports.
	$text = str_replace( array( '\\r\\n', '\\n', '\\r' ), "\n", $text );
	$text = str_replace( array( "\r\n", "\r" ), "\n", $text );

	// Broken newline that became a lone "n" between sentences: "...home.nThis..."
	$text = preg_replace( '/\.(\s*)n(?=[A-Z])/', ".$1\n\n", $text );

	if ( ! $allow_nl ) {
		// Short text is single-line in the UI; keep one paragraph.
		$text = preg_replace( '/^n(?=[A-Z])/', '', $text );
		$text = preg_replace( '/\s+/', ' ', str_replace( "\n", ' ', $text ) );
	}

	return trim( (string) $text );
}

/**
 * Format blog card intro/short text for the front-end (paragraphs + **bold**).
 *
 * @param string $text Raw text.
 * @return string Safe HTML.
 */
function elite_shipping_format_blog_card_prose( $text ) {
	$text = elite_shipping_clean_blog_card_prose_input( $text, true );
	if ( '' === $text ) {
		return '';
	}

	$html = esc_html( $text );
	$html = wpautop( $html );
	$html = preg_replace( '/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html );

	return wp_kses_post( (string) $html );
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

		$title      = sanitize_text_field( $item['title'] ?? '' );
		$date       = sanitize_text_field( $item['date'] ?? '' );
		$image      = absint( $item['image'] ?? 0 );
		$image_file = sanitize_file_name( (string) ( $item['image_file'] ?? '' ) );
		$slug       = sanitize_title( (string) ( $item['slug'] ?? '' ) );
		$intro      = elite_shipping_clean_blog_card_prose_input( sanitize_textarea_field( $item['intro'] ?? '' ), true );
		$short_text = elite_shipping_clean_blog_card_prose_input( sanitize_text_field( $item['short_text'] ?? '' ), false );
		$details    = elite_shipping_normalize_blog_card_details( $item['details'] ?? array() );

		$has_details = ! empty( $details['paragraphs'] ) || ! empty( $details['faqs'] );

		if ( '' === $title && $image <= 0 && '' === $image_file && '' === $intro && '' === $short_text && ! $has_details && ! $keep_empty ) {
			continue;
		}

		if ( ! $keep_empty && '' === $title ) {
			continue;
		}

		if ( $date && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			$timestamp = strtotime( $date );
			$date      = $timestamp ? gmdate( 'Y-m-d', $timestamp ) : '';
		}

		if ( '' === $slug && '' !== $title ) {
			$slug = sanitize_title( $title );
		}

		$row = array(
			'title'      => $title,
			'date'       => $date,
			'image'      => $image,
			'intro'      => $intro,
			'short_text' => $short_text,
			'details'    => $details,
		);

		if ( '' !== $slug ) {
			$row['slug'] = $slug;
		}
		if ( '' !== $image_file ) {
			$row['image_file'] = $image_file;
		}

		$normalized[] = $row;
	}

	return $normalized;
}

/**
 * Build Card list rows from the blogs currently shown on the Blog page.
 *
 * Prefer published WordPress posts; fall back to theme default blog posts.
 *
 * @return array<int, array>
 */
function elite_shipping_get_seed_blog_cards_list_items() {
	$items = array();

	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'posts_per_page'         => 12,
			'post_status'            => 'publish',
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( $query->have_posts() ) {
		$image_map = function_exists( 'elite_shipping_get_blog_image_map' )
			? elite_shipping_get_blog_image_map()
			: array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();
			$slug    = (string) get_post_field( 'post_name', $post_id );
			$image   = (int) get_post_thumbnail_id( $post_id );
			$excerpt = trim( (string) get_the_excerpt() );
			if ( '' === $excerpt ) {
				$excerpt = wp_trim_words( wp_strip_all_tags( (string) get_the_content( null, false, $post_id ) ), 40, '...' );
			}

			$content = trim( wp_strip_all_tags( (string) get_the_content( null, false, $post_id ) ) );
			$details = array(
				'paragraphs' => array(),
				'faqs'       => array(),
			);
			if ( '' !== $content ) {
				$details['paragraphs'][] = array(
					'title'    => '',
					'sections' => array(
						array(
							'title'    => '',
							'hasTitle' => false,
							'blocks'   => array(
								array(
									'type'    => 'text',
									'content' => $content,
								),
							),
						),
					),
				);
			}

			$item = array(
				'title'      => get_the_title(),
				'date'       => get_the_date( 'Y-m-d' ),
				'image'      => $image,
				'intro'      => $excerpt,
				'short_text' => '',
				'details'    => $details,
				'slug'       => $slug,
			);

			if ( $image <= 0 && $slug && isset( $image_map[ $slug ] ) ) {
				$item['image_file'] = $image_map[ $slug ];
			}

			$items[] = $item;
		}
		wp_reset_postdata();
	}

	if ( empty( $items ) && function_exists( 'elite_shipping_get_default_blog_posts' ) ) {
		$image_map = function_exists( 'elite_shipping_get_blog_image_map' )
			? elite_shipping_get_blog_image_map()
			: array();

		foreach ( elite_shipping_get_default_blog_posts() as $post ) {
			$slug = sanitize_title( (string) ( $post['slug'] ?? $post['title'] ?? '' ) );
			$date = (string) ( $post['datetime'] ?? '' );
			if ( $date && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
				$timestamp = strtotime( $date );
				$date      = $timestamp ? gmdate( 'Y-m-d', $timestamp ) : '';
			}

			$image_file = '';
			if ( $slug && isset( $image_map[ $slug ] ) ) {
				$image_file = $image_map[ $slug ];
			} elseif ( ! empty( $post['image'] ) && false === strpos( (string) $post['image'], '://' ) ) {
				$image_file = sanitize_file_name( (string) $post['image'] );
			}

			$items[] = array(
				'title'      => (string) ( $post['title'] ?? '' ),
				'date'       => $date,
				'image'      => 0,
				'image_file' => $image_file,
				'intro'      => (string) ( $post['excerpt'] ?? '' ),
				'short_text' => '',
				'details'    => array(
					'paragraphs' => array(),
					'faqs'       => array(),
				),
				'slug'       => $slug,
			);
		}
	}

	return elite_shipping_normalize_blog_cards_list_items( $items, true );
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
 * When no Card list has been saved yet, seed from the blogs shown on the Blog page
 * so they can be edited immediately in Customize → Blog → Post cards.
 *
 * @return array<int, array>
 */
function elite_shipping_get_blog_cards_list_rows() {
	$stored = get_theme_mod( 'elite_blog_cards_list', null );

	if ( null === $stored || '' === $stored ) {
		return elite_shipping_get_seed_blog_cards_list_items();
	}

	if ( is_array( $stored ) ) {
		$rows = elite_shipping_normalize_blog_cards_list_items( $stored, true );
		return ! empty( $rows ) ? $rows : elite_shipping_get_seed_blog_cards_list_items();
	}

	if ( is_string( $stored ) ) {
		if ( '[]' === trim( $stored ) ) {
			return elite_shipping_get_seed_blog_cards_list_items();
		}
		$decoded = json_decode( $stored, true );
		if ( ! is_array( $decoded ) ) {
			return elite_shipping_get_seed_blog_cards_list_items();
		}
		$rows = elite_shipping_normalize_blog_cards_list_items( $decoded, true );
		return ! empty( $rows ) ? $rows : elite_shipping_get_seed_blog_cards_list_items();
	}

	return elite_shipping_get_seed_blog_cards_list_items();
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
 * Build a table from row/column cell positions (row 1 = header).
 *
 * @param array $cells Table block items.
 * @return string
 */
function elite_shipping_render_blog_card_table_html( $cells ) {
	$raw     = is_array( $cells ) ? $cells : array();
	$has_pos = false;
	$legacy  = array();

	foreach ( $raw as $cell ) {
		if ( ! is_array( $cell ) ) {
			continue;
		}
		$content = trim( (string) ( $cell['content'] ?? '' ) );
		if ( ! empty( $cell['row'] ) || ! empty( $cell['col'] ) || ! empty( $cell['cell'] ) ) {
			$has_pos = true;
		} elseif ( '' !== $content ) {
			$legacy[] = $content;
		}
	}

	// Legacy pipe/newline table content (no positions).
	if ( ! $has_pos && ! empty( $legacy ) ) {
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

	$cells = elite_shipping_migrate_blog_table_block_positions( $raw );
	$grid  = array();

	foreach ( $cells as $cell ) {
		if ( ! is_array( $cell ) ) {
			continue;
		}
		$content = trim( (string) ( $cell['content'] ?? '' ) );
		if ( '' === $content ) {
			continue;
		}
		$pos = elite_shipping_sanitize_blog_table_position( $cell );
		$row = $pos['row'];
		$col = $pos['col'];
		if ( ! isset( $grid[ $row ] ) ) {
			$grid[ $row ] = array();
		}
		$grid[ $row ][ $col ] = $content;
	}

	if ( empty( $grid ) ) {
		return '';
	}

	ksort( $grid, SORT_NUMERIC );
	$max_col = 1;
	$max_row = 1;
	foreach ( $grid as $row_num => $cols ) {
		$max_row = max( $max_row, (int) $row_num );
		foreach ( array_keys( $cols ) as $col_num ) {
			$max_col = max( $max_col, (int) $col_num );
		}
	}

	ob_start();
	echo '<div class="apex-blog-detail-table-wrap"><table class="apex-blog-detail-table">';
	if ( isset( $grid[1] ) ) {
		echo '<thead><tr>';
		for ( $col = 1; $col <= $max_col; $col++ ) {
			echo '<th scope="col">' . esc_html( (string) ( $grid[1][ $col ] ?? '' ) ) . '</th>';
		}
		echo '</tr></thead>';
	}
	echo '<tbody>';
	for ( $row = ( isset( $grid[1] ) ? 2 : 1 ); $row <= $max_row; $row++ ) {
		echo '<tr>';
		for ( $col = 1; $col <= $max_col; $col++ ) {
			echo '<td>' . esc_html( (string) ( $grid[ $row ][ $col ] ?? '' ) ) . '</td>';
		}
		echo '</tr>';
	}
	echo '</tbody></table></div>';
	return ob_get_clean();
}

/**
 * Flatten paragraph sections into a render stream (titles + blocks).
 * Consecutive table cells keep their row/col so one grid is built.
 *
 * @param array $sections Sections.
 * @return array<int, array{kind:string,title?:string,block?:array}>
 */
function elite_shipping_blog_card_paragraph_stream( $sections ) {
	$stream   = array();
	$sections = is_array( $sections ) ? $sections : array();

	foreach ( $sections as $section ) {
		if ( ! is_array( $section ) ) {
			continue;
		}
		$section_title = trim( (string) ( $section['title'] ?? '' ) );
		$blocks        = isset( $section['blocks'] ) && is_array( $section['blocks'] ) ? $section['blocks'] : array();

		if ( '' !== $section_title ) {
			$stream[] = array(
				'kind'  => 'title',
				'title' => $section_title,
			);
		}

		foreach ( $blocks as $block ) {
			if ( ! is_array( $block ) ) {
				continue;
			}
			$stream[] = array(
				'kind'  => 'block',
				'block' => $block,
			);
		}
	}

	return $stream;
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
		$stream   = elite_shipping_blog_card_paragraph_stream( $sections );
		$count    = count( $stream );
		$i        = 0;
		$open_sec = false;

		while ( $i < $count ) {
			$item = $stream[ $i ];
			$kind = (string) ( $item['kind'] ?? '' );

			if ( 'title' === $kind ) {
				if ( $open_sec ) {
					echo '</div>';
					$open_sec = false;
				}
				echo '<div class="apex-blog-detail-section">';
				echo '<h3 class="apex-blog-detail-section__title">' . esc_html( (string) ( $item['title'] ?? '' ) ) . '</h3>';
				$open_sec = true;
				++$i;
				continue;
			}

			$block = isset( $item['block'] ) && is_array( $item['block'] ) ? $item['block'] : null;
			if ( ! $block ) {
				++$i;
				continue;
			}

			$type = (string) ( $block['type'] ?? 'text' );

			if ( 'table' === $type ) {
				$table_cells = array();
				while ( $i < $count ) {
					$next = $stream[ $i ];
					if ( 'block' !== (string) ( $next['kind'] ?? '' ) ) {
						break;
					}
					$next_block = isset( $next['block'] ) && is_array( $next['block'] ) ? $next['block'] : null;
					if ( ! $next_block || 'table' !== (string) ( $next_block['type'] ?? '' ) ) {
						break;
					}
					$table_cells[] = $next_block;
					++$i;
				}
				if ( ! $open_sec ) {
					echo '<div class="apex-blog-detail-section">';
					$open_sec = true;
				}
				echo elite_shipping_render_blog_card_table_html( $table_cells ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				continue;
			}

			$content = trim( (string) ( $block['content'] ?? '' ) );
			++$i;
			if ( '' === $content ) {
				continue;
			}
			if ( ! $open_sec ) {
				echo '<div class="apex-blog-detail-section">';
				$open_sec = true;
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

		if ( $open_sec ) {
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

		$image_id   = absint( $item['image'] ?? 0 );
		$image_file = sanitize_file_name( (string) ( $item['image_file'] ?? '' ) );
		$image_url  = $image_id ? (string) wp_get_attachment_image_url( $image_id, 'large' ) : '';
		if ( ! $image_url && $image_file ) {
			$image_url = elite_shipping_get_blog_image_url( $image_file );
		}

		$slug = sanitize_title( (string) ( $item['slug'] ?? '' ) );
		if ( '' === $slug ) {
			$slug = sanitize_title( $title );
		}
		if ( '' === $slug ) {
			$slug = 'card-' . ( $index + 1 );
		}

		if ( ! $image_url && function_exists( 'elite_shipping_get_blog_image_map' ) ) {
			$map = elite_shipping_get_blog_image_map();
			if ( isset( $map[ $slug ] ) ) {
				$image_url = elite_shipping_get_blog_image_url( $map[ $slug ] );
			}
		}
		if ( ! $image_url ) {
			$image_url = elite_shipping_get_blog_image_url( 'blog_1.webp' );
		}

		$intro      = (string) ( $item['intro'] ?? '' );
		$short_text = (string) ( $item['short_text'] ?? '' );

		$ready[] = array(
			'title'      => $title,
			'url'        => add_query_arg( 'card', $slug, $blog ),
			'image'      => $image_url,
			'excerpt'    => $intro,
			'short_text' => $short_text,
			'details'    => isset( $item['details'] ) ? $item['details'] : array(),
			'date'       => gmdate( 'F j, Y', $timestamp ),
			'datetime'   => gmdate( 'Y-m-d', $timestamp ),
			'day'        => gmdate( 'j', $timestamp ),
			'month'      => gmdate( 'M', $timestamp ),
			'author'     => $author,
			'slug'       => $slug,
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
