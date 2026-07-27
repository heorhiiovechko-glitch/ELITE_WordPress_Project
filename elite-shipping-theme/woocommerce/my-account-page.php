<?php
/**
 * My Account page template.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero_img  = ELITE_SHIPPING_URI . '/assets/images/image_c.jpg';
$is_logged = is_user_logged_in();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'elite-account-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<main class="apex-account-page">
	<section class="apex-page-hero apex-page-hero--account" style="background-image: linear-gradient(rgba(0, 18, 40, 0.78), rgba(0, 18, 40, 0.78)), url('<?php echo esc_url( $hero_img ); ?>');">
		<div class="elite-container">
			<span class="apex-kicker"><?php esc_html_e( 'MY ACCOUNT', 'elite-shipping' ); ?></span>
			<h1 class="apex-page-hero-title">
				<?php
				echo $is_logged
					? esc_html__( 'Your Account Dashboard', 'elite-shipping' )
					: esc_html__( 'Sign In to Your Account', 'elite-shipping' );
				?>
			</h1>
			<p class="apex-page-hero-desc">
				<?php
				echo $is_logged
					? esc_html__( 'View orders, update addresses, and manage your account details in one place.', 'elite-shipping' )
					: esc_html__( 'Log in to track orders, manage delivery details, and update your account.', 'elite-shipping' );
				?>
			</p>
		</div>
	</section>

	<section class="apex-account-main">
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
