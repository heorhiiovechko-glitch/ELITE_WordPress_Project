<?php
/**
 * Single product summary column — self-contained render + hook takeover.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'woocommerce_init', 'elite_shipping_register_single_product_summary', 20 );

/**
 * Replace all WooCommerce/default summary hooks with one explicit renderer.
 */
function elite_shipping_register_single_product_summary() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$summary_hooks = array(
		'woocommerce_template_single_title'       => 5,
		'woocommerce_template_single_rating'      => 10,
		'woocommerce_template_single_price'       => 10,
		'woocommerce_template_single_excerpt'     => 20,
		'woocommerce_template_single_add_to_cart' => 30,
		'woocommerce_template_single_meta'        => 40,
		'woocommerce_template_single_sharing'     => 50,
	);

	foreach ( $summary_hooks as $callback => $priority ) {
		remove_action( 'woocommerce_single_product_summary', $callback, $priority );
	}

	$legacy_callbacks = array(
		'elite_shipping_single_product_breadcrumbs'    => array( 4 ),
		'elite_shipping_single_product_summary_head'   => array( 4 ),
		'elite_shipping_single_product_title'          => array( 5 ),
		'elite_shipping_single_product_extras'         => array( 24, 31 ),
		'elite_shipping_single_product_actions'        => array( 36 ),
		'elite_shipping_single_product_category_share' => array( 45 ),
		'elite_shipping_render_single_product_summary' => array( 5, 10 ),
	);

	foreach ( $legacy_callbacks as $callback => $priorities ) {
		foreach ( $priorities as $priority ) {
			remove_action( 'woocommerce_single_product_summary', $callback, $priority );
		}
	}

	remove_action( 'woocommerce_single_product_summary', 'elite_shipping_render_single_product_summary', 5 );
	add_action( 'woocommerce_single_product_summary', 'elite_shipping_render_single_product_summary', 5 );
}

/**
 * Render the full custom product summary column in reference order.
 */
function elite_shipping_render_single_product_summary() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		$product = wc_get_product( get_the_ID() );
	}

	if ( $product instanceof WC_Product ) {
		$GLOBALS['product'] = $product;
	}

	echo '<!-- elite-product-summary ' . esc_html( ELITE_SHIPPING_VERSION ) . ' -->';

	elite_shipping_single_product_summary_head();
	elite_shipping_single_product_title();

	if ( function_exists( 'woocommerce_template_single_price' ) ) {
		woocommerce_template_single_price();
	}

	if ( function_exists( 'woocommerce_template_single_excerpt' ) ) {
		woocommerce_template_single_excerpt();
	}

	elite_shipping_single_product_extras();

	if ( function_exists( 'woocommerce_template_single_add_to_cart' ) ) {
		woocommerce_template_single_add_to_cart();
	}

	elite_shipping_single_product_actions();
	elite_shipping_single_product_category_share();
}

/**
 * Trust/payment strip image used in the checkout box.
 *
 * @return string
 */
function elite_shipping_get_payments_trust_image_url() {
	return ELITE_SHIPPING_URI . '/assets/images/payments_2.webp';
}

/**
 * Footer payment strip image.
 *
 * @return string
 */
function elite_shipping_get_footer_payments_image_url() {
	return ELITE_SHIPPING_URI . '/assets/images/payments.png';
}

/**
 * Adjacent product preview data for nav tooltips.
 *
 * @param WP_Post|null $post Product post.
 * @return array{url: string, title: string, price_html: string, thumb: string}|null
 */
function elite_shipping_get_product_nav_preview( $post ) {
	if ( ! $post instanceof WP_Post || ! function_exists( 'wc_get_product' ) ) {
		return null;
	}

	$product = wc_get_product( $post->ID );
	if ( ! $product instanceof WC_Product ) {
		return null;
	}

	$image_id = $product->get_image_id();
	$thumb    = $image_id
		? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' )
		: wc_placeholder_img_src( 'woocommerce_thumbnail' );

	return array(
		'url'        => get_permalink( $post ),
		'title'      => get_the_title( $post ),
		'price_html' => $product->get_price_html(),
		'thumb'      => $thumb ? $thumb : '',
	);
}

/**
 * Hover preview card for previous/next product nav buttons.
 *
 * @param array{url: string, title: string, price_html: string, thumb: string}|null $preview Preview data.
 */
function elite_shipping_render_product_nav_tooltip( $preview, $modifier = '' ) {
	if ( empty( $preview ) || empty( $preview['title'] ) ) {
		return;
	}

	$classes = 'apex-single-nav-tooltip';
	if ( $modifier ) {
		$classes .= ' apex-single-nav-tooltip--' . sanitize_html_class( $modifier );
	}
	?>
	<div class="<?php echo esc_attr( $classes ); ?>" role="tooltip">
		<span class="apex-single-nav-tooltip-thumb">
			<?php if ( ! empty( $preview['thumb'] ) ) : ?>
				<img src="<?php echo esc_url( $preview['thumb'] ); ?>" alt="" loading="lazy" decoding="async">
			<?php endif; ?>
		</span>
		<span class="apex-single-nav-tooltip-body">
			<span class="apex-single-nav-tooltip-title"><?php echo esc_html( $preview['title'] ); ?></span>
			<?php if ( ! empty( $preview['price_html'] ) ) : ?>
				<span class="apex-single-nav-tooltip-price price"><?php echo wp_kses_post( $preview['price_html'] ); ?></span>
			<?php endif; ?>
		</span>
	</div>
	<?php
}

/**
 * Breadcrumbs and product navigation row.
 */
function elite_shipping_single_product_summary_head() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$urls        = elite_shipping_get_urls();
	$terms       = wp_get_post_terms( $product->get_id(), 'product_cat' );
	$grid        = $urls['shop'];
	$prev        = get_previous_post( true, '', 'product_cat' );
	$next        = get_next_post( true, '', 'product_cat' );
	$prev_preview = $prev instanceof WP_Post ? elite_shipping_get_product_nav_preview( $prev ) : null;
	$next_preview = $next instanceof WP_Post ? elite_shipping_get_product_nav_preview( $next ) : null;

	if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
		$term_link = get_term_link( $terms[0] );
		if ( ! is_wp_error( $term_link ) ) {
			$grid = $term_link;
		}
	}
	?>
	<div class="apex-single-summary-head">
		<?php elite_shipping_single_product_breadcrumbs(); ?>
		<div class="apex-single-title-nav" aria-label="<?php esc_attr_e( 'Product navigation', 'elite-shipping' ); ?>">
			<?php if ( $prev instanceof WP_Post ) : ?>
				<a class="apex-single-title-nav-btn apex-single-title-nav-btn--prev" href="<?php echo esc_url( get_permalink( $prev ) ); ?>" aria-label="<?php esc_attr_e( 'Previous product', 'elite-shipping' ); ?>">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
				</a>
			<?php else : ?>
				<span class="apex-single-title-nav-btn is-disabled" aria-hidden="true">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
				</span>
			<?php endif; ?>
			<a class="apex-single-title-nav-btn" href="<?php echo esc_url( $grid ); ?>" aria-label="<?php esc_attr_e( 'View category products', 'elite-shipping' ); ?>">
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
			</a>
			<?php if ( $next instanceof WP_Post ) : ?>
				<a class="apex-single-title-nav-btn apex-single-title-nav-btn--next" href="<?php echo esc_url( get_permalink( $next ) ); ?>" aria-label="<?php esc_attr_e( 'Next product', 'elite-shipping' ); ?>">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
				</a>
			<?php else : ?>
				<span class="apex-single-title-nav-btn is-disabled" aria-hidden="true">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
				</span>
			<?php endif; ?>
			<?php
			if ( $prev instanceof WP_Post ) {
				elite_shipping_render_product_nav_tooltip( $prev_preview, 'prev' );
			}
			if ( $next instanceof WP_Post ) {
				elite_shipping_render_product_nav_tooltip( $next_preview, 'next' );
			}
			?>
		</div>
	</div>
	<?php
}

/**
 * Product title.
 */
function elite_shipping_single_product_title() {
	?>
	<h1 class="product_title entry-title"><?php the_title(); ?></h1>
	<?php
}

/**
 * Stable social proof lines per product.
 *
 * @param int $product_id Product ID.
 * @return array{viewing: string, sold: string}
 */
function elite_shipping_get_single_product_social_proof( $product_id ) {
	$product_id = absint( $product_id );
	$viewing    = 120 + ( $product_id % 80 );
	$sold       = 8 + ( ( $product_id * 3 ) % 20 );
	$hours      = 12 + ( $product_id % 10 );

	return array(
		'viewing' => sprintf(
			/* translators: %d: viewer count */
			__( '%d people are viewing this right now', 'elite-shipping' ),
			$viewing
		),
		'sold'    => sprintf(
			/* translators: 1: sold count, 2: hour count */
			__( '%1$d sold in last %2$d hours', 'elite-shipping' ),
			$sold,
			$hours
		),
	);
}

/**
 * Estimated monthly financing amount from product price.
 *
 * @param WC_Product $product Product object.
 * @return float
 */
function elite_shipping_get_product_financing_monthly( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return 0.0;
	}

	$price = (float) $product->get_price();
	if ( $price <= 0 ) {
		return 0.0;
	}

	return round( $price / 18.57, 2 );
}

/**
 * PayPal logo asset URL.
 *
 * @return string
 */
function elite_shipping_get_paypal_logo_url() {
	return ELITE_SHIPPING_URI . '/assets/images/paypal.svg';
}

/**
 * Social proof and trust checkout box.
 */
function elite_shipping_single_product_extras() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$urls  = elite_shipping_get_urls();
	$proof = elite_shipping_get_single_product_social_proof( $product->get_id() );
	?>
	<div class="apex-single-social-proof">
		<p><?php echo esc_html( $proof['viewing'] ); ?></p>
		<p><?php echo esc_html( $proof['sold'] ); ?></p>
	</div>

	<div class="apex-single-trust-box">
		<strong class="apex-single-trust-title"><?php esc_html_e( 'Guaranteed Safe Checkout', 'elite-shipping' ); ?></strong>
		<hr class="apex-single-trust-divider">
		<div class="apex-single-trust-body">
			<img
				class="apex-single-trust-payments-img"
				src="<?php echo esc_url( elite_shipping_get_payments_trust_image_url() ); ?>"
				alt="<?php esc_attr_e( 'Guaranteed safe checkout and accepted payment methods', 'elite-shipping' ); ?>"
				width="420"
				height="105"
				loading="lazy"
				decoding="async"
			>
			<a class="apex-single-trust-btn" href="<?php echo esc_url( $urls['contact'] ); ?>">
				<?php esc_html_e( 'For Any Query Get in Touch', 'elite-shipping' ); ?>
			</a>
		</div>
	</div>
	<?php
}

/**
 * Breadcrumbs above product title.
 */
function elite_shipping_single_product_breadcrumbs() {
	if ( ! function_exists( 'woocommerce_breadcrumb' ) ) {
		return;
	}

	woocommerce_breadcrumb(
		array(
			'wrap_before' => '<nav class="apex-single-breadcrumbs woocommerce-breadcrumb" aria-label="Breadcrumb">',
			'wrap_after'  => '</nav>',
			'delimiter'   => ' / ',
		)
	);
}

/**
 * Compare / wishlist links and PayPal note (after add to cart).
 */
function elite_shipping_single_product_actions() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '#';
	$paypal_url   = add_query_arg(
		array(
			'add-to-cart' => $product->get_id(),
		),
		$checkout_url
	);
	$monthly      = elite_shipping_get_product_financing_monthly( $product );
	$monthly_html = function_exists( 'wc_price' )
		? wc_price( $monthly, array( 'decimals' => 2 ) )
		: '$' . number_format( $monthly, 2 );
	$paypal_logo  = elite_shipping_get_paypal_logo_url();
	?>
	<button
		type="button"
		class="apex-single-paypal-note"
		data-paypal-monthly-open
		aria-haspopup="dialog"
		aria-controls="apex-paypal-monthly-modal"
	>
		<span class="apex-single-paypal-note-text">
			<?php
			echo wp_kses_post(
				sprintf(
					/* translators: %s: monthly payment amount */
					__( 'Starting at %s/mo or as low as 0%% APR with', 'elite-shipping' ),
					$monthly_html
				)
			);
			?>
			<img class="apex-single-paypal-inline-logo" src="<?php echo esc_url( $paypal_logo ); ?>" alt="PayPal" width="58" height="15">
			<span class="apex-single-paypal-note-dot">.</span>
		</span>
		<span class="apex-single-paypal-note-more"><?php esc_html_e( 'Learn more', 'elite-shipping' ); ?></span>
	</button>
	<a class="apex-single-paypal-btn" href="<?php echo esc_url( $paypal_url ); ?>">
		<?php esc_html_e( 'Pay with', 'elite-shipping' ); ?>
		<img class="apex-single-paypal-btn-logo" src="<?php echo esc_url( $paypal_logo ); ?>" alt="PayPal" width="85" height="22">
	</a>
	<?php get_template_part( 'template-parts/paypal-monthly', 'modal' ); ?>
	<div class="apex-single-secondary-actions">
		<a class="apex-single-secondary-link" href="#">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M16 3h5v5"/><path d="M8 3H3v5"/><path d="M8 21H3v-5"/><path d="M16 21h5v-5"/><path d="M3 10h18"/></svg>
			<?php esc_html_e( 'Add to compare', 'elite-shipping' ); ?>
		</a>
		<a class="apex-single-secondary-link" href="#">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
			<?php esc_html_e( 'Add to wishlist', 'elite-shipping' ); ?>
		</a>
	</div>
	<?php
}

/**
 * Category line and social share icons.
 */
function elite_shipping_single_product_category_share() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$terms = wp_get_post_terms( $product->get_id(), 'product_cat' );
	$share = elite_shipping_get_post_share_links( $product->get_id() );
	?>
	<div class="apex-single-meta-share">
		<?php if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) : ?>
			<p class="apex-single-category">
				<?php esc_html_e( 'Category:', 'elite-shipping' ); ?>
				<?php
				$links = array();
				foreach ( $terms as $term ) {
					if ( 'uncategorized' === $term->slug ) {
						continue;
					}
					$link = get_term_link( $term );
					if ( ! is_wp_error( $link ) ) {
						$links[] = '<a href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a>';
					}
				}
				echo wp_kses_post( implode( ', ', $links ) );
				?>
			</p>
		<?php endif; ?>

		<div class="apex-single-share">
			<span class="apex-single-share-label"><?php esc_html_e( 'Share:', 'elite-shipping' ); ?></span>
			<?php foreach ( $share as $item ) : ?>
				<a class="apex-single-share-link apex-single-share-link--<?php echo esc_attr( $item['id'] ); ?>" href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $item['label'] ); ?>">
					<?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}
