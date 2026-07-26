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
					$blocks[] = array(
						'type'    => $type,
						'content' => sanitize_textarea_field( $block['content'] ?? '' ),
					);
				}
				$sections[] = array(
					'title'  => sanitize_text_field( $section['title'] ?? '' ),
					'blocks' => $blocks,
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
 * Render content blocks inside a small paragraph.
 *
 * @param array $blocks Blocks.
 * @return string
 */
function elite_shipping_render_blog_card_blocks( $blocks ) {
	ob_start();
	foreach ( $blocks as $block ) {
		$type    = (string) ( $block['type'] ?? 'text' );
		$content = (string) ( $block['content'] ?? '' );
		$label   = 'text' === $type ? __( 'Normal text', 'elite-shipping' ) : ( 'table' === $type ? __( 'Table', 'elite-shipping' ) : __( 'List text', 'elite-shipping' ) );
		?>
		<div class="elite-blog-block" data-type="<?php echo esc_attr( $type ); ?>">
			<div class="elite-blog-block__head">
				<span class="elite-blog-block__type"><?php echo esc_html( $label ); ?></span>
				<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-icon-btn--remove elite-blog-remove-block" aria-label="<?php esc_attr_e( 'Remove', 'elite-shipping' ); ?>">
					<?php echo elite_shipping_blog_cards_icon_html( 'trash', __( 'Remove', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
			<textarea class="elite-blog-block__content" rows="3" placeholder="<?php echo esc_attr( $label ); ?>" aria-label="<?php echo esc_attr( $label ); ?>"><?php echo esc_textarea( $content ); ?></textarea>
		</div>
		<?php
	}
	return ob_get_clean();
}

/**
 * Render small paragraphs.
 *
 * @param array $sections Sections.
 * @return string
 */
function elite_shipping_render_blog_card_sections( $sections ) {
	ob_start();
	foreach ( $sections as $section ) {
		?>
		<div class="elite-blog-section">
			<div class="elite-blog-section__top">
				<input type="text" class="elite-blog-section__title" value="<?php echo esc_attr( (string) ( $section['title'] ?? '' ) ); ?>" placeholder="<?php esc_attr_e( 'Small paragraph title', 'elite-shipping' ); ?>" aria-label="<?php esc_attr_e( 'Small paragraph title', 'elite-shipping' ); ?>">
				<div class="elite-blog-section__actions">
					<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-icon-btn--remove elite-blog-remove-section" aria-label="<?php esc_attr_e( 'Remove', 'elite-shipping' ); ?>">
						<?php echo elite_shipping_blog_cards_icon_html( 'trash', __( 'Remove', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
					<button type="button" class="elite-blog-cards-icon-btn elite-blog-add-block-text" aria-label="<?php esc_attr_e( 'Add normal text', 'elite-shipping' ); ?>">
						<?php echo elite_shipping_blog_cards_icon_html( 'text', __( 'Add normal text', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
					<button type="button" class="elite-blog-cards-icon-btn elite-blog-add-block-table" aria-label="<?php esc_attr_e( 'Add table', 'elite-shipping' ); ?>">
						<?php echo elite_shipping_blog_cards_icon_html( 'editor-table', __( 'Add table', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
					<button type="button" class="elite-blog-cards-icon-btn elite-blog-add-block-list" aria-label="<?php esc_attr_e( 'Add list text', 'elite-shipping' ); ?>">
						<?php echo elite_shipping_blog_cards_icon_html( 'editor-ul', __( 'Add list text', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
			</div>
			<div class="elite-blog-section__blocks">
				<?php echo elite_shipping_render_blog_card_blocks( isset( $section['blocks'] ) && is_array( $section['blocks'] ) ? $section['blocks'] : array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}
	return ob_get_clean();
}

/**
 * Render paragraphs inside details panel.
 *
 * @param array $paragraphs Paragraphs.
 * @return string
 */
function elite_shipping_render_blog_card_paragraphs( $paragraphs ) {
	ob_start();
	foreach ( $paragraphs as $paragraph ) {
		?>
		<div class="elite-blog-paragraph">
			<div class="elite-blog-paragraph__top">
				<input type="text" class="elite-blog-paragraph__title" value="<?php echo esc_attr( (string) ( $paragraph['title'] ?? '' ) ); ?>" placeholder="<?php esc_attr_e( 'Paragraph title', 'elite-shipping' ); ?>" aria-label="<?php esc_attr_e( 'Paragraph title', 'elite-shipping' ); ?>">
				<div class="elite-blog-paragraph__actions">
					<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-icon-btn--remove elite-blog-remove-paragraph" aria-label="<?php esc_attr_e( 'Remove', 'elite-shipping' ); ?>">
						<?php echo elite_shipping_blog_cards_icon_html( 'trash', __( 'Remove', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
					<button type="button" class="elite-blog-cards-icon-btn elite-blog-add-section" aria-label="<?php esc_attr_e( 'Add small paragraph', 'elite-shipping' ); ?>">
						<?php echo elite_shipping_blog_cards_icon_html( 'plus-alt', __( 'Add small paragraph', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
			</div>
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

		<div class="elite-blog-cards-list-item__details-bar">
			<button type="button" class="elite-blog-cards-icon-btn elite-blog-cards-toggle-details" aria-expanded="false" aria-label="<?php esc_attr_e( 'Details', 'elite-shipping' ); ?>">
				<?php echo elite_shipping_blog_cards_icon_html( 'editor-paragraph', __( 'Details', 'elite-shipping' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
			<span class="elite-blog-cards-list-item__details-label"><?php esc_html_e( 'Details', 'elite-shipping' ); ?></span>
		</div>

		<div class="elite-blog-details-panel" hidden>
			<div class="elite-blog-details-panel__head">
				<span class="elite-blog-details-panel__title"><?php esc_html_e( 'Paragraphs', 'elite-shipping' ); ?></span>
				<button type="button" class="button button-secondary elite-blog-add-paragraph">
					<span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( 'Add new paragraph', 'elite-shipping' ); ?></span>
				</button>
			</div>
			<div class="elite-blog-paragraphs">
				<?php echo elite_shipping_render_blog_card_paragraphs( $details['paragraphs'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<div class="elite-blog-details-panel__head elite-blog-details-panel__head--faqs">
				<span class="elite-blog-details-panel__title"><?php esc_html_e( 'FAQs', 'elite-shipping' ); ?></span>
				<button type="button" class="button button-secondary elite-blog-add-faq">
					<span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( 'Add new FAQs', 'elite-shipping' ); ?></span>
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
			foreach ( $blocks as $block ) {
				$type    = (string) ( $block['type'] ?? 'text' );
				$content = trim( (string) ( $block['content'] ?? '' ) );
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
				} elseif ( 'table' === $type ) {
					$rows = preg_split( '/\r\n|\r|\n/', $content );
					echo '<div class="apex-blog-detail-table-wrap"><table class="apex-blog-detail-table">';
					foreach ( $rows as $row_i => $row ) {
						$cells = array_map( 'trim', explode( '|', (string) $row ) );
						if ( '' === implode( '', $cells ) ) {
							continue;
						}
						echo '<tr>';
						foreach ( $cells as $cell ) {
							$tag = 0 === $row_i ? 'th' : 'td';
							echo '<' . $tag . '>' . esc_html( $cell ) . '</' . $tag . '>';
						}
						echo '</tr>';
					}
					echo '</table></div>';
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
