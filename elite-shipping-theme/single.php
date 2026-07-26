<?php
/**
 * Single blog post template.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$urls = elite_shipping_get_urls();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'elite-blog-single' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<main class="apex-blog-single">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();

			$thumb = elite_shipping_get_blog_post_image( get_the_ID() );
			$single_kicker = get_theme_mod( 'elite_blog_single_kicker', 'OUR BLOG' );
			$single_title  = get_theme_mod( 'elite_blog_single_title', 'Our Blog' );
			$single_desc   = get_theme_mod(
				'elite_blog_single_desc',
				'Expert guides, market insights, and practical advice on buying, using, and modifying shipping containers across the UK.'
			);
			$single_cat    = get_theme_mod( 'elite_blog_single_cat', __( 'Blog', 'elite-shipping' ) );
			?>
			<?php
			get_template_part(
				'template-parts/page',
				'hero-bar',
				array(
					'kicker'   => $single_kicker,
					'title'    => $single_title,
					'desc'     => $single_desc,
					'image'    => $thumb,
					'modifier' => 'apex-page-hero--blog',
				)
			);
			?>
			<section class="apex-blog-single-wrap">
				<div class="elite-container apex-blog-single-layout">
					<article class="apex-blog-single-main">
						<header class="apex-blog-single-head">
							<a class="apex-blog-single-cat" href="<?php echo esc_url( $urls['blog'] ); ?>"><?php echo esc_html( $single_cat ); ?></a>
							<h1 class="apex-blog-single-title"><?php the_title(); ?></h1>
							<div class="apex-blog-single-meta">
								<span class="apex-blog-single-meta-item">
									<?php esc_html_e( 'Posted by', 'elite-shipping' ); ?>
									<svg class="apex-blog-single-meta-icon" width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
									<span><?php the_author(); ?></span>
								</span>
								<span class="apex-blog-single-meta-item">
									<?php esc_html_e( 'On', 'elite-shipping' ); ?>
									<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></time>
								</span>
								<span class="apex-blog-single-meta-item">
									<svg class="apex-blog-single-meta-icon" width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20 2H4a2 2 0 0 0-2 2v14l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/></svg>
									<span><?php echo esc_html( (string) get_comments_number() ); ?></span>
								</span>
							</div>
						</header>

						<?php if ( $thumb ) : ?>
							<div class="apex-blog-single-featured">
								<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
							</div>
						<?php endif; ?>

						<div class="apex-blog-article entry-content">
							<?php the_content(); ?>
						</div>

						<?php get_template_part( 'template-parts/blog', 'post-footer' ); ?>
					</article>

					<?php
					get_template_part(
						'template-parts/blog',
						'sidebar',
						array(
							'exclude_id' => get_the_ID(),
						)
					);
					?>
				</div>
			</section>
		<?php endwhile; ?>
	<?php endif; ?>
</main>

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
