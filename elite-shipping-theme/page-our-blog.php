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
$hero_img = elite_shipping_get_blog_image_url( 'blog_1.webp' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'elite-blog-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<main class="apex-blog-page">
	<?php
	get_template_part(
		'template-parts/page',
		'hero-bar',
		array(
			'kicker'   => 'OUR BLOG',
			'title'    => 'Our Blog',
			'desc'     => 'Expert guides, market insights, and practical advice on buying, using, and modifying shipping containers across the UK.',
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

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
