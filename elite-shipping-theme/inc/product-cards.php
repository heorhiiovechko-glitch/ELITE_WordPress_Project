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
 * Render browse category cards.
 */
function elite_render_category_grid() {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$terms    = function_exists( 'elite_shipping_get_product_categories' )
		? elite_shipping_get_product_categories( array( 'parent' => 0 ) )
		: array();

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

		echo '<div class="apex-grid apex-product-grid">';
		foreach ( $fallback as $item ) {
			?>
			<article class="apex-product-card">
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

	echo '<div class="apex-grid apex-product-grid">';
	foreach ( $terms as $term ) {
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
		<article class="apex-product-card">
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
					<div class="apex-stars" aria-hidden="true">★★★★★ <span>(128)</span></div>
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
		if ( ! $product || ! $product->is_visible() ) {
			continue;
		}

		$url            = get_permalink();
		$title          = $product->get_name();
		$category_label = elite_get_product_shop_category_label( $product );
		$img            = $product->get_image(
			'woocommerce_thumbnail',
			array(
				'class' => 'apex-shop-product-img',
			)
		);
		$badge          = '';
		$badge_class    = '';

		if ( $product->is_on_sale() ) {
			$regular = (float) $product->get_regular_price();
			$sale    = (float) $product->get_sale_price();
			$badge_class = 'apex-shop-product-badge--sale';
			if ( $regular > 0 && $sale > 0 ) {
				$badge = '-' . round( ( ( $regular - $sale ) / $regular ) * 100 ) . '%';
			} else {
				$badge = 'SALE';
			}
		} elseif ( $product->is_featured() ) {
			$badge       = 'HOT';
			$badge_class = 'apex-shop-product-badge--hot';
		}
		?>
		<article <?php wc_product_class( 'apex-shop-product-card', $product ); ?>>
			<a class="apex-shop-product-media" href="<?php echo esc_url( $url ); ?>">
				<?php echo $img ? $img : '<div class="apex-product-ph"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( $badge ) : ?>
					<span class="apex-shop-product-badge <?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>
			</a>
			<div class="apex-shop-product-body">
				<h3 class="apex-shop-product-title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></h3>
				<p class="apex-shop-product-cat"><?php echo esc_html( $category_label ); ?></p>
				<div class="apex-shop-product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			</div>
		</article>
		<?php
	}

	echo '</div>';
}

/** Add-on accessory cards. */
function elite_render_addon_cards() {
	$items = array(
		array( 'Lock Box', '£150.00', 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&q=80' ),
		array( 'Container Vent', '£45.00', 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=400&q=80' ),
		array( 'Container Corner Castings', '£45.00', 'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=400&q=80' ),
	);

	echo '<div class="apex-grid apex-addon-grid">';
	foreach ( $items as $item ) {
		list( $title, $price, $image ) = $item;
		?>
		<article class="apex-addon-card">
			<a class="apex-addon-media" href="#">
				<img class="apex-addon-img" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
			</a>
			<div class="apex-addon-body">
				<h3 class="apex-addon-name"><?php echo esc_html( $title ); ?></h3>
				<div class="apex-addon-price"><?php echo esc_html( $price ); ?></div>
				<a class="apex-addon-link" href="#">VIEW DETAILS →</a>
			</div>
		</article>
		<?php
	}
	echo '</div>';
}
