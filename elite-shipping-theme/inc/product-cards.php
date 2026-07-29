<?php
/**
 * Product & category cards.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extract size badge (e.g. 20FT) from product title.
 *
 * @param WC_Product $product Product object.
 * @return string
 */
function elite_get_product_size_badge( $product ) {
	$title = $product->get_name();

	if ( preg_match( '/\b(\d+)\s*(?:FT|ft)\b/i', $title, $matches ) ) {
		return strtoupper( $matches[1] . 'FT' );
	}

	if ( preg_match( '/\b(\d+)FT\b/i', $title, $matches ) ) {
		return strtoupper( $matches[1] . 'FT' );
	}

	return '';
}

/**
 * Format product price for bestseller cards (no decimals).
 *
 * @param WC_Product $product Product object.
 * @return string
 */
function elite_format_bestseller_price( $product ) {
	$price = $product->get_price();

	if ( '' === $price || null === $price ) {
		return $product->get_price_html();
	}

	return wc_price(
		(float) $price,
		array(
			'decimals' => 0,
		)
	);
}

/**
 * Product condition label for card meta.
 *
 * @param WC_Product $product Product object.
 * @return string
 */
function elite_get_product_condition_label( $product ) {
	$title = strtolower( $product->get_name() );

	if ( false !== strpos( $title, 'used' ) ) {
		return 'Used';
	}
	if ( false !== strpos( $title, 'new' ) ) {
		return 'New';
	}

	$terms = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term_name ) {
			$name = strtolower( $term_name );
			if ( false !== strpos( $name, 'used' ) ) {
				return 'Used';
			}
			if ( false !== strpos( $name, 'new' ) ) {
				return 'New';
			}
		}
	}

	return 'New';
}

/**
 * Dimension label for popular product cards.
 *
 * @param WC_Product $product Product object.
 * @return string
 */
function elite_get_product_dims_label( $product ) {
	$title  = strtolower( $product->get_name() );
	$length = '20ft';

	if ( preg_match( '/\b(\d+)\s*(?:ft|foot)\b/i', $product->get_name(), $matches ) ) {
		$length = strtolower( $matches[1] . 'ft' );
	}

	$height = ( false !== strpos( $title, 'high cube' ) ) ? '9.6ft' : '8.6ft';

	return $length . ' / 8ft / ' . $height;
}

/**
 * Primary category label for product cards.
 *
 * @param WC_Product $product Product object.
 * @return string
 */
function elite_get_product_category_label( $product ) {
	$terms = wp_get_post_terms(
		$product->get_id(),
		'product_cat',
		array(
			'orderby' => 'parent',
			'order'   => 'ASC',
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return 'Containers';
	}

	foreach ( $terms as $term ) {
		if ( 'uncategorized' !== $term->slug ) {
			return $term->name;
		}
	}

	return $terms[0]->name;
}

/**
 * Comma-separated category list for shop archive cards.
 *
 * @param WC_Product $product Product object.
 * @return string
 */
function elite_get_product_shop_category_label( $product ) {
	$terms = wp_get_post_terms(
		$product->get_id(),
		'product_cat',
		array(
			'orderby' => 'parent',
			'order'   => 'ASC',
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return 'Containers';
	}

	$names = array();
	foreach ( $terms as $term ) {
		if ( 'uncategorized' !== $term->slug ) {
			$names[] = $term->name;
		}
	}

	return ! empty( $names ) ? implode( ', ', $names ) : 'Containers';
}

/**
 * Display review count for popular product star ratings.
 *
 * Uses a stable pool so each product keeps the same number on reload.
 *
 * @param int $product_id Product ID.
 * @param int $index      Optional card index fallback.
 * @return int
 */
function elite_get_product_star_review_count( $product_id, $index = 0 ) {
	$counts = array( 128, 112, 129, 87, 42, 156, 94, 203, 67, 118, 145, 76, 91, 134, 58, 171, 83, 102, 147, 63 );
	$slot   = ( absint( $product_id ) + absint( $index ) ) % count( $counts );

	return $counts[ $slot ];
}

/**
 * Render browse category cards.
 *
 * @param int $limit Maximum categories to show. 0 = no limit (uses full display list).
 */
function elite_render_category_grid( $limit = 0 ) {
	$shop_url  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$uses_list = function_exists( 'elite_shipping_top_picks_uses_display_list' ) && elite_shipping_top_picks_uses_display_list();
	$limit     = $uses_list ? 0 : max( 1, absint( $limit ) ?: 8 );
	$terms     = function_exists( 'elite_shipping_get_top_picks_categories' )
		? elite_shipping_get_top_picks_categories( $limit )
		: array();

	if ( empty( $terms ) && ! $uses_list && function_exists( 'elite_shipping_get_product_categories' ) ) {
		$terms = elite_shipping_get_product_categories(
			array(
				'parent' => 0,
				'number' => $limit,
			)
		);
	}

	if ( empty( $terms ) && $uses_list ) {
		echo '<div id="elite-top-picks-grid" class="apex-grid apex-product-grid">';
		echo '<p class="apex-empty">' . esc_html__( 'No categories selected. Choose categories in Appearance → Customize → Home → Top Picks for You.', 'elite-shipping' ) . '</p>';
		echo '</div>';
		return;
	}

	if ( empty( $terms ) ) {
		$fallback = array(
			array( 'name' => 'New Containers', 'link' => $shop_url, 'image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=600&q=80' ),
			array( 'name' => 'Used Containers', 'link' => $shop_url, 'image' => 'https://images.unsplash.com/photo-1605745341112-85968b19335b?w=600&q=80' ),
			array( 'name' => 'Office & Custom', 'link' => $shop_url, 'image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&q=80' ),
			array( 'name' => 'Refrigerated', 'link' => $shop_url, 'image' => 'https://images.unsplash.com/photo-1605884040212-49bb1c2a4d34?w=600&q=80' ),
			array( 'name' => 'Flat Pack', 'link' => $shop_url, 'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=600&q=80' ),
			array( 'name' => 'Accessories', 'link' => $shop_url, 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' ),
			array( 'name' => '20FT Containers', 'link' => $shop_url, 'image' => 'https://images.unsplash.com/photo-1605884040212-49bb1c2a4d34?w=600&q=80' ),
			array( 'name' => '40FT Containers', 'link' => $shop_url, 'image' => 'https://images.unsplash.com/photo-1605745341112-85968b19335b?w=600&q=80' ),
		);

		echo '<div id="elite-top-picks-grid" class="apex-grid apex-product-grid">';
		foreach ( array_slice( $fallback, 0, $limit ) as $item ) {
			?>
			<article class="apex-product-card apex-product-card--category">
				<a class="apex-product-media" href="<?php echo esc_url( $item['link'] ); ?>">
					<img class="apex-product-img" src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['name'] ); ?>" loading="lazy">
				</a>
				<div class="apex-product-body">
					<h3 class="apex-product-name"><a href="<?php echo esc_url( $item['link'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a></h3>
					<a class="apex-view-link" href="<?php echo esc_url( $item['link'] ); ?>">VIEW DETAILS →</a>
				</div>
			</article>
			<?php
		}
		echo '</div>';
		return;
	}

	echo '<div id="elite-top-picks-grid" class="apex-grid apex-product-grid">';
	$display_terms = $limit > 0 ? array_slice( $terms, 0, $limit ) : $terms;
	foreach ( $display_terms as $term ) {
		$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
		$image    = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : '';
		if ( ! $image ) {
			$image = 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=600&q=80';
		}
		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			$link = $shop_url;
		}
		?>
		<article class="apex-product-card apex-product-card--category">
			<a class="apex-product-media" href="<?php echo esc_url( $link ); ?>">
				<img class="apex-product-img" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" loading="lazy">
			</a>
			<div class="apex-product-body">
				<h3 class="apex-product-name"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $term->name ); ?></a></h3>
				<a class="apex-view-link" href="<?php echo esc_url( $link ); ?>">VIEW DETAILS →</a>
			</div>
		</article>
		<?php
	}
	echo '</div>';
}

/**
 * Render product grid.
 *
 * @param array $args WP_Query args.
 * @param array $opts Display mode: featured|popular|bestseller.
 */
function elite_render_product_grid( $args = array(), $opts = array() ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		echo '<p class="apex-empty">Products will appear here after WooCommerce import.</p>';
		return;
	}

	$defaults = array(
		'post_type'      => 'product',
		'posts_per_page' => 8,
		'post_status'    => 'publish',
	);

	$query = new WP_Query( array_merge( $defaults, $args ) );
	$mode  = isset( $opts['mode'] ) ? $opts['mode'] : 'featured';

	if ( ! $query->have_posts() ) {
		echo '<p class="apex-empty">No products found.</p>';
		return;
	}

	if ( 'bestseller' === $mode ) {
		echo '<div class="apex-grid apex-bestseller-grid">';
	} elseif ( 'popular' === $mode ) {
		echo '<div class="apex-grid apex-popular-track">';
	} else {
		echo '<div class="apex-grid apex-product-grid">';
	}

	$index = 0;
	while ( $query->have_posts() ) {
		$query->the_post();
		$product = wc_get_product( get_the_ID() );
		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			continue;
		}

		++$index;
		$url        = get_permalink();
		$title      = get_the_title();
		$img        = $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'apex-product-img' ) );
		$price      = $product->get_price_html();
		$condition      = elite_get_product_condition_label( $product );
		$size_badge     = elite_get_product_size_badge( $product );
		$category_label = elite_get_product_category_label( $product );
		$card_heading   = 'popular' === $mode ? $category_label : $title;
		?>
		<article class="apex-product-card <?php echo 'popular' === $mode ? 'apex-popular-card' : ''; ?><?php echo 'bestseller' === $mode ? ' apex-product-card--bestseller' : ''; ?>">
			<?php if ( 'popular' === $mode && 1 === $index ) : ?>
				<span class="apex-best-badge">Best Seller</span>
			<?php endif; ?>
			<a class="apex-product-media<?php echo 'bestseller' === $mode ? ' apex-product-media--cover' : ''; ?><?php echo 'popular' === $mode ? ' apex-product-media--popular' : ''; ?>" href="<?php echo esc_url( $url ); ?>">
				<?php echo $img ? $img : '<div class="apex-product-ph"></div>'; ?>
				<?php if ( 'bestseller' === $mode && $size_badge ) : ?>
					<span class="apex-size-badge"><?php echo esc_html( $size_badge ); ?></span>
				<?php endif; ?>
			</a>
			<div class="apex-product-body">
				<h3 class="apex-product-name"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $card_heading ); ?></a></h3>
				<?php if ( 'bestseller' === $mode ) : ?>
					<div class="apex-product-meta apex-product-meta--bestseller">
						<span class="apex-product-condition"><?php echo esc_html( $condition ); ?></span>
						<div class="apex-product-price apex-product-price--orange"><?php echo wp_kses_post( elite_format_bestseller_price( $product ) ); ?></div>
					</div>
				<?php elseif ( 'popular' === $mode ) : ?>
					<p class="apex-product-dims"><?php echo esc_html( elite_get_product_dims_label( $product ) ); ?></p>
					<div class="apex-product-price apex-popular-price"><?php echo wp_kses_post( elite_format_bestseller_price( $product ) ); ?></div>
					<div class="apex-stars" aria-hidden="true">★★★★★ <span>(<?php echo esc_html( (string) elite_get_product_star_review_count( $product->get_id(), $index ) ); ?>)</span></div>
				<?php else : ?>
					<a class="apex-view-link" href="<?php echo esc_url( $url ); ?>">VIEW DETAILS →</a>
				<?php endif; ?>
			</div>
		</article>
		<?php
	}

	echo '</div>';
	wp_reset_postdata();
}

/**
 * Render hover add-to-cart control for shop product cards.
 *
 * @param WC_Product $product Product object.
 */
function elite_render_shop_product_add_button( $product ) {
	if ( ! $product instanceof WC_Product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return;
	}

	$args = array(
		'quantity'   => 1,
		'class'      => implode(
			' ',
			array_filter(
				array(
					'apex-shop-product-add',
					'button',
					'product_type_' . $product->get_type(),
					$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
					$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
				)
			)
		),
		'attributes' => array(
			'data-product_id'  => $product->get_id(),
			'data-product_sku' => $product->get_sku(),
			'aria-label'       => $product->add_to_cart_description(),
			'rel'              => 'nofollow',
		),
	);

	$icon = '<svg class="apex-shop-product-add-icon" width="18" height="18" viewBox="0 0 640 640" fill="currentColor" aria-hidden="true"><path d="M0 72C0 58.7 10.7 48 24 48L69.3 48C96.4 48 119.6 67.4 124.4 94L124.8 96L537.5 96C557.5 96 572.6 114.2 568.9 133.9L537.8 299.8C532.1 330.1 505.7 352 474.9 352L171.3 352L176.4 380.3C178.5 391.7 188.4 400 200 400L456 400C469.3 400 480 410.7 480 424C480 437.3 469.3 448 456 448L200.1 448C165.3 448 135.5 423.1 129.3 388.9L77.2 102.6C76.5 98.8 73.2 96 69.3 96L24 96C10.7 96 0 85.3 0 72zM160 528C160 501.5 181.5 480 208 480C234.5 480 256 501.5 256 528C256 554.5 234.5 576 208 576C181.5 576 160 554.5 160 528zM384 528C384 501.5 405.5 480 432 480C458.5 480 480 501.5 480 528C480 554.5 458.5 576 432 576C405.5 576 384 554.5 384 528zM336 142.4C322.7 142.4 312 153.1 312 166.4L312 200L278.4 200C265.1 200 254.4 210.7 254.4 224C254.4 237.3 265.1 248 278.4 248L312 248L312 281.6C312 294.9 322.7 305.6 336 305.6C349.3 305.6 360 294.9 360 281.6L360 248L393.6 248C406.9 248 417.6 237.3 417.6 224C417.6 210.7 406.9 200 393.6 200L360 200L360 166.4C360 153.1 349.3 142.4 336 142.4z"/></svg>';

	echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'woocommerce_loop_add_to_cart_link',
		sprintf(
			'<a href="%s" data-quantity="%s" class="%s" %s>%s<span class="apex-shop-product-add-tooltip" role="tooltip">%s</span></a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( $args['quantity'] ),
			esc_attr( $args['class'] ),
			wc_implode_html_attributes( $args['attributes'] ),
			$icon,
			esc_html__( 'Add to cart', 'elite-shipping' )
		),
		$product,
		$args
	);
}

/**
 * Enqueue WooCommerce AJAX add-to-cart for shop cards.
 */
function elite_shipping_enqueue_shop_card_add_to_cart() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	if ( ! is_shop() && ! is_product_taxonomy() && ! is_product() ) {
		return;
	}

	wp_enqueue_script( 'wc-cart-fragments' );

	$ajax_url = class_exists( 'WC_AJAX' )
		? WC_AJAX::get_endpoint( 'add_to_cart' )
		: home_url( '/?wc-ajax=add_to_cart' );

	wp_localize_script(
		'elite-shipping-main',
		'eliteShippingAddToCart',
		array(
			'ajaxUrl' => $ajax_url,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'elite_shipping_enqueue_shop_card_add_to_cart', 25 );

/**
 * Render a single shop-style product card.
 *
 * @param WC_Product $product Product object.
 */
function elite_render_shop_product_card( $product ) {
	if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
		return;
	}

	$url            = get_permalink( $product->get_id() );
	$title          = $product->get_name();
	$category_label = elite_get_product_shop_category_label( $product );
	$is_on_sale     = $product->is_on_sale();
	$is_in_stock    = $product->is_in_stock();
	$img            = $product->get_image(
		'woocommerce_thumbnail',
		array(
			'class' => 'apex-shop-product-img',
		)
	);
	?>
	<article <?php wc_product_class( 'apex-shop-product-card', $product ); ?>>
		<div class="apex-shop-product-media">
			<a class="apex-shop-product-media-link" href="<?php echo esc_url( $url ); ?>">
				<?php if ( $is_on_sale ) : ?>
					<span class="apex-shop-product-badge apex-shop-product-badge--sale"><?php esc_html_e( 'Sale', 'elite-shipping' ); ?></span>
				<?php endif; ?>
				<?php echo $img ? $img : '<div class="apex-product-ph"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
			<?php elite_render_shop_product_add_button( $product ); ?>
		</div>
		<div class="apex-shop-product-body">
			<p class="apex-shop-product-cat"><?php echo esc_html( $category_label ); ?></p>
			<h3 class="apex-shop-product-title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></h3>
			<div class="apex-shop-product-meta">
				<div class="apex-shop-product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
				<?php if ( $is_in_stock ) : ?>
					<span class="apex-shop-product-stock"><?php esc_html_e( 'In stock', 'elite-shipping' ); ?></span>
				<?php endif; ?>
			</div>
			<a class="apex-shop-product-link" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'View details', 'elite-shipping' ); ?> &rarr;</a>
		</div>
	</article>
	<?php
}

/**
 * Render WooCommerce shop/category archive product cards.
 *
 * @param int $columns Grid column count.
 */
function elite_render_wc_shop_product_loop( $columns = 4 ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$columns = in_array( (int) $columns, array( 2, 4, 6 ), true ) ? (int) $columns : 4;

	echo '<div class="apex-grid apex-shop-grid apex-shop-grid--cols-' . esc_attr( (string) $columns ) . '">';

	while ( have_posts() ) {
		the_post();
		$product = wc_get_product( get_the_ID() );
		elite_render_shop_product_card( $product );
	}

	echo '</div>';
}

/** Add-on accessory cards. */
function elite_render_addon_cards() {
	$items = elite_shipping_get_default_addon_items();

	echo '<div class="apex-grid apex-addon-grid">';
	foreach ( $items as $item ) {
		elite_render_addon_card( $item );
	}
	echo '</div>';
}
