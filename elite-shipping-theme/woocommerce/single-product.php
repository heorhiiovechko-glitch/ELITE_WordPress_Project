<?php
/**
 * Single product page template.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'elite-single-product-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<main class="elite-wc-main elite-single-product">
	<div class="elite-container">
		<?php
		while ( have_posts() ) :
			the_post();
			wc_get_template_part( 'content', 'single-product' );
		endwhile;
		?>
	</div>
</main>

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
