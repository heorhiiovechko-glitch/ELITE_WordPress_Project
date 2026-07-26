<?php
/**
 * Blog archive page template.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts    = elite_shipping_get_blog_posts();
$hero_img = elite_shipping_get_theme_mod_image_url(
	'elite_blog_hero_image',
	elite_shipping_get_blog_image_url( 'blog_1.webp' )
);
$hero_kicker = get_theme_mod( 'elite_blog_kicker', 'OUR BLOG' );
$hero_title  = get_theme_mod( 'elite_blog_title', 'Our Blog' );
$hero_desc   = get_theme_mod(
	'elite_blog_desc',
	'Expert guides, market insights, and practical advice on buying, using, and modifying shipping containers across the UK.'
);

$active_card = null;
$card_slug   = isset( $_GET['card'] ) ? sanitize_title( wp_unslash( $_GET['card'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( $card_slug && function_exists( 'elite_shipping_get_customizer_blog_card_by_slug' ) ) {
	$active_card = elite_shipping_get_customizer_blog_card_by_slug( $card_slug );
}

$single_kicker = get_theme_mod( 'elite_blog_single_kicker', $hero_kicker );
$single_title  = get_theme_mod( 'elite_blog_single_title', $hero_title );
$single_desc   = get_theme_mod( 'elite_blog_single_desc', $hero_desc );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( $active_card ? 'elite-blog-single elite-blog-page--card' : 'elite-blog-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<?php if ( $active_card ) : ?>
	<main class="apex-blog-single">
		<?php
		get_template_part(
			'template-parts/page',
			'hero-bar',
			array(
				'kicker'   => $single_kicker,
				'title'    => $single_title,
				'desc'     => $single_desc,
				'image'    => ! empty( $active_card['image'] ) ? $active_card['image'] : $hero_img,
				'modifier' => 'apex-page-hero--blog',
			)
		);
		get_template_part( 'template-parts/blog', 'card-single', array( 'card' => $active_card ) );
		?>
	</main>
<?php else : ?>
	<main class="apex-blog-page">
		<?php
		get_template_part(
			'template-parts/page',
			'hero-bar',
			array(
				'kicker'   => $hero_kicker,
				'title'    => $hero_title,
				'desc'     => $hero_desc,
				'image'    => $hero_img,
				'modifier' => 'apex-page-hero--blog',
			)
		);
		?>
		<section class="apex-blog-list">
			<div class="elite-container">
				<div class="apex-blog-grid">
					<?php foreach ( $posts as $post ) : ?>
						<?php get_template_part( 'template-parts/blog', 'card', array( 'post' => $post ) ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	</main>
<?php endif; ?>

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
