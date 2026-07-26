<?php
/**
 * Cart page template.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero_img = ELITE_SHIPPING_URI . '/assets/images/image_c.jpg';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'elite-cart-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<main class="apex-cart-page">
	<section class="apex-page-hero apex-page-hero--cart" style="background-image: linear-gradient(rgba(0, 18, 40, 0.78), rgba(0, 18, 40, 0.78)), url('<?php echo esc_url( $hero_img ); ?>');">
		<div class="elite-container">
			<span class="apex-kicker"><?php esc_html_e( 'YOUR CART', 'elite-shipping' ); ?></span>
			<h1 class="apex-page-hero-title"><?php esc_html_e( 'Review Your Order', 'elite-shipping' ); ?></h1>
			<p class="apex-page-hero-desc"><?php esc_html_e( 'Check your selected containers, update quantities, then proceed to secure checkout.', 'elite-shipping' ); ?></p>
		</div>
	</section>

	<section class="apex-cart-main">
		<div class="elite-container">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</section>
</main>

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
