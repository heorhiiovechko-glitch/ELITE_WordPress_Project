<?php
/**
 * Homepage section renderers (Customizer-driven).
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default modification carousel cards.
 *
 * @return array<int, array{title: string, image: string}>
 */
function elite_shipping_get_default_mod_cards() {
	return array(
		array(
			'title' => 'Office Containers',
			'image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=600&q=80',
		),
		array(
			'title' => 'Storage Containers',
			'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=600&q=80',
		),
		array(
			'title' => 'Custom Containers',
			'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=600&q=80',
		),
		array(
			'title' => 'Refrigerated Units',
			'image' => 'https://images.unsplash.com/photo-1605745341112-85968b19335b?auto=format&fit=crop&w=600&q=80',
		),
		array(
			'title' => 'Side Open Containers',
			'image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=600&q=80',
		),
	);
}

/**
 * Modification types for header/footer navigation (Customizer-driven).
 *
 * @return array<int, array{name: string, url: string}>
 */
function elite_shipping_get_modifications_menu_items() {
	$items = function_exists( 'elite_shipping_get_mods_display_list_items' )
		? elite_shipping_get_mods_display_list_items()
		: array();

	if ( empty( $items ) ) {
		$defaults = elite_shipping_get_default_mod_cards();
		foreach ( $defaults as $default ) {
			if ( '' === trim( (string) ( $default['title'] ?? '' ) ) ) {
				continue;
			}
			$items[] = array(
				'title'     => $default['title'],
				'image_id'  => 0,
				'image_url' => $default['image'] ?? '',
				'url'       => function_exists( 'elite_shipping_get_mod_card_url' )
					? elite_shipping_get_mod_card_url( 0, (string) $default['title'] )
					: home_url( '/shop/' ),
			);
		}
	}

	$menu_items = array();
	foreach ( $items as $item ) {
		$url = ! empty( $item['url'] )
			? (string) $item['url']
			: ( function_exists( 'elite_shipping_get_mod_card_url' )
				? elite_shipping_get_mod_card_url( (int) ( $item['category_id'] ?? 0 ), (string) ( $item['title'] ?? '' ) )
				: home_url( '/shop/' ) );

		$menu_items[] = array(
			'name' => $item['title'],
			'url'  => $url,
		);
	}

	return $menu_items;
}

/**
 * Resolve a product URL for add-on cards by title or slug.
 *
 * @param string $title    Product title to match.
 * @param string $fallback Optional fallback URL.
 * @return string
 */
function elite_shipping_resolve_addon_product_url( $title, $fallback = '' ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return $fallback ? $fallback : home_url( '/shop/' );
	}

	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

	$page = get_page_by_title( $title, OBJECT, 'product' );
	if ( $page instanceof WP_Post ) {
		return get_permalink( $page->ID );
	}

	$slug    = sanitize_title( $title );
	$by_slug = get_page_by_path( $slug, OBJECT, 'product' );
	if ( $by_slug instanceof WP_Post ) {
		return get_permalink( $by_slug->ID );
	}

	$products = wc_get_products(
		array(
			'limit'   => 1,
			'status'  => 'publish',
			'search'  => $title,
			'orderby' => 'relevance',
		)
	);

	if ( ! empty( $products ) ) {
		$product = $products[0];
		if ( $product instanceof WC_Product && $product->is_visible() ) {
			return get_permalink( $product->get_id() );
		}
	}

	return $fallback ? $fallback : $shop_url;
}

/**
 * Default add-on fallback items.
 *
 * @return array<int, array{title: string, price: string, image: string, url: string}>
 */
function elite_shipping_get_default_addon_items() {
	$items = array(
		array(
			'title' => 'Lock Box',
			'price' => '£150.00',
			'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&q=80',
			'url'   => '',
		),
		array(
			'title' => 'Container Vent',
			'price' => '£45.00',
			'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=400&q=80',
			'url'   => '',
		),
		array(
			'title' => 'Container Corner Castings',
			'price' => '£45.00',
			'image' => 'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=400&q=80',
			'url'   => '',
		),
	);

	foreach ( $items as $index => $item ) {
		$items[ $index ]['url'] = elite_shipping_resolve_addon_product_url( $item['title'] );
	}

	return $items;
}

/**
 * Resolve an attachment ID from theme mod with URL fallback.
 *
 * @param string $setting   Theme mod key.
 * @param string $fallback  Default image URL.
 * @return string
 */
function elite_shipping_get_theme_mod_image_url( $setting, $fallback = '' ) {
	$attachment_id = absint( get_theme_mod( $setting, 0 ) );
	if ( $attachment_id ) {
		$url = wp_get_attachment_image_url( $attachment_id, 'large' );
		if ( $url ) {
			return $url;
		}
	}

	return $fallback;
}

/**
 * Product IDs picked for Popular Products.
 *
 * @param int $limit Max products.
 * @return int[]
 */
/**
 * Product IDs picked for Popular Products.
 *
 * @param int $limit Max products.
 * @return int[]
 */
function elite_shipping_get_popular_product_ids( $limit = 10 ) {
	$limit  = max( 1, absint( $limit ) );
	$picked = array();

	for ( $slot = 1; $slot <= 10; $slot++ ) {
		$product_id = absint( get_theme_mod( 'elite_popular_product_' . $slot, 0 ) );
		if ( $product_id > 0 && ! in_array( $product_id, $picked, true ) ) {
			$picked[] = $product_id;
		}
		if ( count( $picked ) >= $limit ) {
			break;
		}
	}

	if ( count( $picked ) < $limit && class_exists( 'WooCommerce' ) ) {
		$query = new WP_Query(
			array(
				'post_type'              => 'product',
				'posts_per_page'         => $limit - count( $picked ),
				'post_status'            => 'publish',
				'orderby'                => 'date',
				'order'                  => 'DESC',
				'post__not_in'           => $picked,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		if ( ! empty( $query->posts ) ) {
			foreach ( $query->posts as $product_id ) {
				$product_id = absint( $product_id );
				if ( $product_id > 0 && ! in_array( $product_id, $picked, true ) ) {
					$picked[] = $product_id;
				}
				if ( count( $picked ) >= $limit ) {
					break;
				}
			}
		}
	}

	return array_slice( $picked, 0, $limit );
}

/**
 * Product IDs picked for Essential Add-Ons.
 *
 * @param int $limit Max products.
 * @return int[]
 */
function elite_shipping_get_addon_product_ids( $limit = 3 ) {
	$limit  = max( 1, absint( $limit ) );
	$picked = array();

	for ( $slot = 1; $slot <= 3; $slot++ ) {
		$product_id = absint( get_theme_mod( 'elite_addon_product_' . $slot, 0 ) );
		if ( $product_id > 0 && ! in_array( $product_id, $picked, true ) ) {
			$picked[] = $product_id;
		}
	}

	return array_slice( $picked, 0, $limit );
}

/**
 * Render Top Picks section.
 */
function elite_render_home_top_picks_section() {
	$urls = elite_shipping_get_urls();
	?>
	<section class="apex-section apex-featured" id="elite-home-top-picks">
		<div class="elite-container">
			<div class="apex-section-top apex-section-top--linked">
				<div class="apex-section-head-copy">
					<span class="apex-kicker"><?php echo esc_html( get_theme_mod( 'elite_top_picks_kicker', 'FEATURED CONTAINERS' ) ); ?></span>
					<h2><?php echo esc_html( get_theme_mod( 'elite_top_picks_title', 'Top Picks for You' ) ); ?></h2>
					<p class="apex-section-desc"><?php echo esc_html( get_theme_mod( 'elite_top_picks_desc', 'High-quality containers in stock and ready to ship.' ) ); ?></p>
				</div>
				<a class="elite-btn elite-btn-navy" href="<?php echo esc_url( get_theme_mod( 'elite_top_picks_btn_url', $urls['shop'] ) ); ?>">
					<?php echo esc_html( get_theme_mod( 'elite_top_picks_btn_text', 'VIEW ALL PRODUCTS' ) ); ?>
				</a>
			</div>
			<?php elite_render_category_grid(); ?>
		</div>
	</section>
	<?php
}

/**
 * Render About section.
 */
function elite_render_home_about_section() {
	$urls = elite_shipping_get_urls();
	$about_images = array(
		elite_shipping_get_theme_mod_image_url( 'elite_about_image_1', ELITE_SHIPPING_URI . '/assets/images/image_a.jpg' ),
		elite_shipping_get_theme_mod_image_url( 'elite_about_image_2', ELITE_SHIPPING_URI . '/assets/images/image_b.jpg' ),
		elite_shipping_get_theme_mod_image_url( 'elite_about_image_3', ELITE_SHIPPING_URI . '/assets/images/image_c.jpg' ),
		elite_shipping_get_theme_mod_image_url( 'elite_about_image_4', ELITE_SHIPPING_URI . '/assets/images/image_d.jpg' ),
	);
	$photo_classes = array( 'a', 'b', 'c', 'd' );
	?>
	<section class="apex-section apex-about" id="elite-home-about">
		<div class="elite-container apex-about-grid">
			<div class="apex-about-copy">
				<span class="apex-kicker"><?php echo esc_html( get_theme_mod( 'elite_about_kicker', 'ABOUT ELITE SHIPPING CONTAINERS' ) ); ?></span>
				<h2><?php echo esc_html( get_theme_mod( 'elite_about_title', 'Your Trusted Container Partner' ) ); ?></h2>
				<p><?php echo wp_kses_post( get_theme_mod( 'elite_about_text', 'Elite Shipping Containers Ltd provides durable, secure, and affordable shipping containers for storage, transport, and special projects. With competitive pricing and exceptional customer service, we deliver quality you can trust across the United Kingdom.' ) ); ?></p>
				<ul class="apex-checklist apex-checklist--about">
					<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
						<?php
						$defaults = array(
							1 => 'Wide Range of New & Used Containers',
							2 => 'Custom Modifications Available',
							3 => 'UK Nationwide Delivery & Support',
						);
						$item = get_theme_mod( 'elite_about_check_' . $i, $defaults[ $i ] );
						if ( '' === trim( (string) $item ) ) {
							continue;
						}
						?>
						<li><?php echo esc_html( $item ); ?></li>
					<?php endfor; ?>
				</ul>
				<a class="elite-btn elite-btn-outline-orange apex-about-btn" href="<?php echo esc_url( get_theme_mod( 'elite_about_btn_url', $urls['about'] ) ); ?>">
					<?php echo esc_html( get_theme_mod( 'elite_about_btn_text', 'LEARN MORE ABOUT US →' ) ); ?>
				</a>
			</div>
			<div class="apex-about-gallery apex-about-gallery--grid" aria-hidden="true">
				<?php foreach ( $photo_classes as $index => $class_suffix ) : ?>
					<div
						class="apex-about-photo apex-about-photo--<?php echo esc_attr( $class_suffix ); ?>"
						style="background-image:url('<?php echo esc_url( $about_images[ $index ] ); ?>');"
					></div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Render Modifications carousel section.
 */
function elite_render_home_mods_section() {
	$uses_list = function_exists( 'elite_shipping_mods_uses_display_list' ) && elite_shipping_mods_uses_display_list();
	$items     = function_exists( 'elite_shipping_get_mods_display_list_items' )
		? elite_shipping_get_mods_display_list_items()
		: array();

	if ( empty( $items ) && ! $uses_list ) {
		$defaults = elite_shipping_get_default_mod_cards();
		foreach ( $defaults as $default ) {
			if ( '' === trim( (string) ( $default['title'] ?? '' ) ) ) {
				continue;
			}
			$items[] = array(
				'title'     => $default['title'],
				'image_id'  => 0,
				'image_url' => $default['image'] ?? '',
				'url'       => function_exists( 'elite_shipping_get_mod_card_url' )
					? elite_shipping_get_mod_card_url( 0, (string) $default['title'] )
					: home_url( '/shop/' ),
			);
		}
	}
	?>
	<section id="modifications" class="apex-section apex-mods">
		<div class="elite-container">
			<div class="apex-mods-band">
				<div class="apex-mods-head">
					<span class="apex-kicker apex-kicker-light"><?php echo esc_html( get_theme_mod( 'elite_mods_kicker', 'CONTAINER MODIFICATIONS' ) ); ?></span>
					<h2><?php echo esc_html( get_theme_mod( 'elite_mods_title', 'Built to Suit Your Needs' ) ); ?></h2>
				</div>
				<div class="apex-carousel-wrap" data-carousel="mods">
					<button type="button" class="apex-arrow apex-arrow-prev" aria-label="Previous">‹</button>
					<div class="apex-mod-track" id="elite-home-mods-track">
						<?php if ( empty( $items ) && $uses_list ) : ?>
							<p class="apex-empty"><?php esc_html_e( 'No modification cards selected. Add cards in Appearance → Customize → Home → Built to Suit Your Needs.', 'elite-shipping' ); ?></p>
						<?php else : ?>
							<?php foreach ( $items as $item ) : ?>
								<?php
								$image_url = $item['image_url'] ?? '';
								if ( ! $image_url && ! empty( $item['image_id'] ) ) {
									$image_url = (string) wp_get_attachment_image_url( (int) $item['image_id'], 'large' );
								}
								$card_url = ! empty( $item['url'] )
									? (string) $item['url']
									: ( function_exists( 'elite_shipping_get_mod_card_url' )
										? elite_shipping_get_mod_card_url( (int) ( $item['category_id'] ?? 0 ), (string) ( $item['title'] ?? '' ) )
										: home_url( '/shop/' ) );
								?>
								<a class="apex-mod-card" href="<?php echo esc_url( $card_url ); ?>">
									<div class="apex-mod-card__media">
										<?php if ( $image_url ) : ?>
											<img
												class="apex-mod-card__img"
												src="<?php echo esc_url( $image_url ); ?>"
												alt="<?php echo esc_attr( $item['title'] ); ?>"
												loading="lazy"
												decoding="async"
											>
										<?php else : ?>
											<div class="apex-mod-card__placeholder" aria-hidden="true"></div>
										<?php endif; ?>
									</div>
									<div class="apex-mod-card__body">
										<h3 class="apex-mod-card__title"><?php echo esc_html( $item['title'] ); ?></h3>
									</div>
								</a>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<button type="button" class="apex-arrow apex-arrow-next" aria-label="Next">›</button>
				</div>
				<div class="apex-dots" aria-hidden="true"></div>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Render a single add-on card.
 *
 * @param array{title: string, price: string, image: string, url: string} $item Card data.
 */
function elite_render_addon_card( $item ) {
	$url = ! empty( $item['url'] ) ? $item['url'] : elite_shipping_resolve_addon_product_url( $item['title'] ?? '' );
	?>
	<article class="apex-addon-card">
		<a class="apex-addon-media" href="<?php echo esc_url( $url ); ?>" tabindex="-1" aria-hidden="true">
			<img class="apex-addon-img" src="<?php echo esc_url( $item['image'] ); ?>" alt="" loading="lazy">
		</a>
		<div class="apex-addon-body">
			<h3 class="apex-addon-name">
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
			</h3>
			<div class="apex-addon-price"><?php echo wp_kses_post( $item['price'] ); ?></div>
			<a class="apex-addon-link" href="<?php echo esc_url( $url ); ?>">
				<span><?php esc_html_e( 'View details', 'elite-shipping' ); ?></span>
				<svg class="apex-addon-link-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
					<path d="M5 12h12M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</a>
		</div>
	</article>
	<?php
}

/**
 * Build add-on items from Customizer or fallback data.
 *
 * @return array<int, array{title: string, price: string, image: string, url: string}>
 */
function elite_shipping_get_addon_items() {
	$product_ids = elite_shipping_get_addon_product_ids( 3 );
	$items         = array();

	if ( ! empty( $product_ids ) && class_exists( 'WooCommerce' ) ) {
		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( ! $product || ! $product->is_visible() ) {
				continue;
			}

			$image_id = $product->get_image_id();
			$image    = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : wc_placeholder_img_src();

			$items[] = array(
				'title' => $product->get_name(),
				'price' => $product->get_price_html(),
				'image' => $image ? $image : wc_placeholder_img_src(),
				'url'   => get_permalink( $product_id ),
			);
		}
	}

	if ( ! empty( $items ) ) {
		return $items;
	}

	return elite_shipping_get_default_addon_items();
}

/**
 * Render Essential Add-Ons section.
 */
function elite_render_home_addons_section() {
	$items = elite_shipping_get_addon_items();
	?>
	<section class="apex-section apex-addons" id="elite-home-addons">
		<div class="elite-container">
			<div class="apex-addons-head">
				<span class="apex-kicker"><?php echo esc_html( get_theme_mod( 'elite_addons_kicker', 'CONTAINER ACCESSORIES' ) ); ?></span>
				<h2><?php echo esc_html( get_theme_mod( 'elite_addons_title', 'Essential Add-Ons' ) ); ?></h2>
				<p class="apex-addons-desc"><?php echo esc_html( get_theme_mod( 'elite_addons_desc', 'Enhance the functionality and security of your container.' ) ); ?></p>
			</div>
			<div class="apex-grid apex-addon-grid" id="elite-home-addons-grid">
				<?php foreach ( $items as $item ) : ?>
					<?php elite_render_addon_card( $item ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Render a single popular product card.
 *
 * @param WC_Product $product Product.
 * @param int          $index 1-based index in the row.
 */
function elite_render_popular_product_card( $product, $index = 1 ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$url            = get_permalink( $product->get_id() );
	$img            = $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'apex-product-img' ) );
	$category_label = elite_get_product_category_label( $product );
	?>
	<article class="apex-product-card apex-popular-card">
		<?php if ( 1 === $index ) : ?>
			<span class="apex-best-badge"><?php esc_html_e( 'Best Seller', 'elite-shipping' ); ?></span>
		<?php endif; ?>
		<a class="apex-product-media apex-product-media--popular" href="<?php echo esc_url( $url ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo $img ? $img : '<div class="apex-product-ph"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
		<div class="apex-product-body">
			<h3 class="apex-product-name"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $category_label ); ?></a></h3>
			<p class="apex-product-dims"><?php echo esc_html( elite_get_product_dims_label( $product ) ); ?></p>
			<div class="apex-popular-card-foot">
				<div class="apex-product-price apex-popular-price"><?php echo wp_kses_post( elite_format_bestseller_price( $product ) ); ?></div>
				<?php elite_render_popular_star_rating( $product->get_id(), $index ); ?>
			</div>
		</div>
	</article>
	<?php
}

/**
 * Render Popular Products track (inner carousel content).
 */
function elite_render_home_popular_track() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		echo '<p class="apex-empty">Products will appear here after WooCommerce import.</p>';
		return;
	}

	$product_ids = elite_shipping_get_popular_product_ids( 10 );
	echo '<div class="apex-grid apex-popular-track" id="elite-home-popular-track">';

	if ( empty( $product_ids ) ) {
		echo '<p class="apex-empty">' . esc_html__( 'No products found.', 'elite-shipping' ) . '</p>';
		echo '</div>';
		return;
	}

	$index = 0;
	foreach ( $product_ids as $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! $product || ! $product->is_visible() ) {
			continue;
		}
		++$index;
		elite_render_popular_product_card( $product, $index );
	}

	echo '</div>';
}

/**
 * Render Popular Products section.
 */
function elite_render_home_popular_section() {
	$urls = elite_shipping_get_urls();
	?>
	<section class="apex-section apex-popular" id="elite-home-popular">
		<div class="elite-container">
			<div class="apex-section-top apex-section-top--linked">
				<h2 class="apex-popular-title"><?php echo esc_html( get_theme_mod( 'elite_popular_title', 'POPULAR PRODUCTS' ) ); ?></h2>
				<a class="elite-btn elite-btn-navy" href="<?php echo esc_url( get_theme_mod( 'elite_popular_btn_url', $urls['shop'] ) ); ?>">
					<?php echo esc_html( get_theme_mod( 'elite_popular_btn_text', 'VIEW ALL PRODUCTS' ) ); ?>
				</a>
			</div>
			<div class="apex-carousel-wrap" data-carousel="popular">
				<button type="button" class="apex-arrow apex-arrow-prev" aria-label="Previous">‹</button>
				<?php elite_render_home_popular_track(); ?>
				<button type="button" class="apex-arrow apex-arrow-next" aria-label="Next">›</button>
			</div>
			<?php elite_render_home_popular_trustbar(); ?>
		</div>
	</section>
	<?php
}

/**
 * Trust features bar under Popular Products.
 */
function elite_render_home_popular_trustbar() {
	$items = array(
		array(
			'icon'  => 'price',
			'title' => __( 'Best Prices', 'elite-shipping' ),
			'text'  => __( 'Competitive pricing guaranteed', 'elite-shipping' ),
		),
		array(
			'icon'  => 'delivery',
			'title' => __( 'Fast Delivery', 'elite-shipping' ),
			'text'  => __( 'On-time delivery worldwide', 'elite-shipping' ),
		),
		array(
			'icon'  => 'secure',
			'title' => __( 'Secure Payment', 'elite-shipping' ),
			'text'  => __( 'Safe & trusted transactions', 'elite-shipping' ),
		),
		array(
			'icon'  => 'support',
			'title' => __( 'Expert Support', 'elite-shipping' ),
			'text'  => __( '24/7 customer assistance', 'elite-shipping' ),
		),
	);
	?>
	<div class="apex-trustbar apex-trustbar--popular" aria-label="<?php esc_attr_e( 'Shopping benefits', 'elite-shipping' ); ?>">
		<div class="apex-trustbar-band">
			<div class="apex-trustbar-row">
				<?php foreach ( $items as $item ) : ?>
					<div class="apex-trustbar-item">
						<?php if ( 'price' === $item['icon'] ) : ?>
							<svg class="apex-trustbar-ico" width="36" height="36" viewBox="0 0 48 48" fill="none" aria-hidden="true">
								<path d="M18 14h12l4 6v14a4 4 0 0 1-4 4H18a4 4 0 0 1-4-4V20l4-6z" stroke="currentColor" stroke-width="2.2"/>
								<path d="M14 20h20" stroke="currentColor" stroke-width="2.2"/>
								<circle class="tb-accent" cx="24" cy="30" r="5" stroke="currentColor" stroke-width="2.2" fill="none"/>
								<path class="tb-accent" d="M22 30l1.6 1.6L27 28" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						<?php elseif ( 'delivery' === $item['icon'] ) : ?>
							<svg class="apex-trustbar-ico" width="36" height="36" viewBox="0 0 48 48" fill="none" aria-hidden="true">
								<path d="M6 30h22V16H6v14z" stroke="currentColor" stroke-width="2.2"/>
								<path d="M28 22h8l4 5v3h-12v-8z" stroke="currentColor" stroke-width="2.2"/>
								<circle cx="14" cy="34" r="3.5" stroke="currentColor" stroke-width="2.2"/>
								<circle class="tb-accent" cx="34" cy="34" r="3.5" stroke="currentColor" stroke-width="2.2"/>
								<path d="M4 18h-2M6 14H3M9 11H5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
							</svg>
						<?php elseif ( 'secure' === $item['icon'] ) : ?>
							<svg class="apex-trustbar-ico" width="36" height="36" viewBox="0 0 48 48" fill="none" aria-hidden="true">
								<rect x="10" y="16" width="28" height="20" rx="3" stroke="currentColor" stroke-width="2.2"/>
								<path d="M10 24h28" stroke="currentColor" stroke-width="2.2"/>
								<path class="tb-accent" d="M16 32h8" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
								<path class="tb-accent" d="M34 10v6M31 13h6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
							</svg>
						<?php else : ?>
							<svg class="apex-trustbar-ico" width="36" height="36" viewBox="0 0 48 48" fill="none" aria-hidden="true">
								<circle cx="24" cy="24" r="14" stroke="currentColor" stroke-width="2.2"/>
								<circle class="tb-accent" cx="24" cy="20" r="4" stroke="currentColor" stroke-width="2.2"/>
								<path class="tb-accent" d="M16 34c1.8-5 5-7.5 8-7.5s6.2 2.5 8 7.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
							</svg>
						<?php endif; ?>
						<div class="apex-trustbar-copy">
							<strong><?php echo esc_html( $item['title'] ); ?></strong>
							<span><?php echo esc_html( $item['text'] ); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}
