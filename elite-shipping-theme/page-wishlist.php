<?php
/**
 * Wishlist page template.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$urls  = elite_shipping_get_urls();
$count = function_exists( 'elite_shipping_get_wishlist_count' ) ? elite_shipping_get_wishlist_count() : 0;
$ids   = function_exists( 'elite_shipping_get_wishlist_ids' ) ? elite_shipping_get_wishlist_ids() : array();

$hero_kicker = __( 'Wishlist', 'elite-shipping' );
$hero_title  = __( 'Your Wishlist', 'elite-shipping' );
$hero_desc   = $count
	? sprintf(
		/* translators: %d: wishlist item count */
		_n( 'You have %d saved product.', 'You have %d saved products.', $count, 'elite-shipping' ),
		$count
	)
	: __( 'Save products you like and come back anytime.', 'elite-shipping' );

$hero_fallback = function_exists( 'elite_shipping_migrate_media_url' )
	? elite_shipping_migrate_media_url( 'https://firstchoiceshippingcontainers.com/wp-content/uploads/2025/07/510631176_23998021113198545_7887283231137689143_n.jpg' )
	: '';
$hero_img = function_exists( 'elite_shipping_get_theme_mod_image_url' )
	? elite_shipping_get_theme_mod_image_url( 'elite_wishlist_hero_image', $hero_fallback )
	: $hero_fallback;

$shop_url = ! empty( $urls['shop'] ) ? $urls['shop'] : home_url( '/shop/' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'elite-wishlist-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<main class="apex-wishlist-page">
	<?php
	get_template_part(
		'template-parts/page',
		'hero-bar',
		array(
			'kicker'   => $hero_kicker,
			'title'    => $hero_title,
			'desc'     => $hero_desc,
			'image'    => $hero_img,
			'modifier' => 'apex-page-hero--wishlist',
		)
	);
	?>

	<section class="apex-wishlist-main">
		<div class="elite-container">
			<div class="apex-wishlist-toolbar">
				<p class="apex-wishlist-count" data-elite-wishlist-count-label>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: wishlist item count */
							_n( '%d item', '%d items', $count, 'elite-shipping' ),
							$count
						)
					);
					?>
				</p>
				<div class="apex-wishlist-toolbar-actions">
					<?php if ( $count > 0 ) : ?>
						<button type="button" class="apex-wishlist-clear js-elite-wishlist-clear">
							<?php esc_html_e( 'Clear wishlist', 'elite-shipping' ); ?>
						</button>
					<?php endif; ?>
					<a class="apex-wishlist-continue" href="<?php echo esc_url( $shop_url ); ?>">
						<?php esc_html_e( 'Continue shopping', 'elite-shipping' ); ?>
					</a>
				</div>
			</div>

			<div class="apex-wishlist-content" data-elite-wishlist-content>
				<?php
				if ( function_exists( 'elite_shipping_render_wishlist_items' ) ) {
					elite_shipping_render_wishlist_items( $ids );
				}
				?>
			</div>
		</div>
	</section>
</main>

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
