<?php
/**
 * Checkout page template.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_thankyou = function_exists( 'is_order_received_page' ) && is_order_received_page();
$hero_img    = ELITE_SHIPPING_URI . '/assets/images/image_c.jpg';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( $is_thankyou ? 'elite-checkout-page elite-thankyou-page' : 'elite-checkout-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<main class="apex-checkout-page">
	<section class="apex-page-hero apex-page-hero--checkout" style="background-image: linear-gradient(rgba(0, 18, 40, 0.78), rgba(0, 18, 40, 0.78)), url('<?php echo esc_url( $hero_img ); ?>');">
		<div class="elite-container">
			<span class="apex-kicker"><?php echo $is_thankyou ? esc_html__( 'ORDER CONFIRMED', 'elite-shipping' ) : esc_html__( 'SECURE CHECKOUT', 'elite-shipping' ); ?></span>
			<h1 class="apex-page-hero-title">
				<?php echo $is_thankyou ? esc_html__( 'Thank You for Your Order', 'elite-shipping' ) : esc_html__( 'Complete Your Purchase', 'elite-shipping' ); ?>
			</h1>
			<p class="apex-page-hero-desc">
				<?php
				echo $is_thankyou
					? esc_html__( 'Your order has been received. We will be in touch shortly with delivery details.', 'elite-shipping' )
					: esc_html__( 'Enter your details below to secure your container. Fast UK delivery and trusted payment options.', 'elite-shipping' );
				?>
			</p>
		</div>
	</section>

	<section class="apex-checkout-main">
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
