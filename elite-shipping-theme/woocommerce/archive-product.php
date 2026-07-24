<?php
/**
 * WooCommerce shop archive template.
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
<body <?php body_class( 'elite-shop-archive elite-shop-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<main class="elite-wc-main">
	<?php get_template_part( 'template-parts/shop', 'archive-body' ); ?>
</main>

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
