<?php
/**
 * Customizer blog card rendered in the default single-post layout.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$card = isset( $args['card'] ) && is_array( $args['card'] ) ? $args['card'] : array();
if ( empty( $card['title'] ) ) {
	return;
}

$urls       = elite_shipping_get_urls();
$blog_url   = isset( $urls['blog'] ) ? $urls['blog'] : home_url( '/our-blog/' );
$single_cat = get_theme_mod( 'elite_blog_single_cat', get_theme_mod( 'elite_blog_card_cat', __( 'Blog', 'elite-shipping' ) ) );
$thumb      = ! empty( $card['image'] ) ? $card['image'] : '';
$author     = ! empty( $card['author'] ) ? $card['author'] : get_theme_mod( 'elite_contact_company_name', ELITE_COMPANY_NAME );
$date_label = ! empty( $card['date'] ) ? $card['date'] : '';
$datetime   = ! empty( $card['datetime'] ) ? $card['datetime'] : '';
?>
<section class="apex-blog-single-wrap">
	<div class="elite-container apex-blog-single-layout">
		<article class="apex-blog-single-main">
			<header class="apex-blog-single-head">
				<a class="apex-blog-single-back" href="<?php echo esc_url( $blog_url ); ?>" aria-label="<?php esc_attr_e( 'Back to blog', 'elite-shipping' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
				</a>
				<a class="apex-blog-single-cat" href="<?php echo esc_url( $blog_url ); ?>"><?php echo esc_html( $single_cat ); ?></a>
				<h1 class="apex-blog-single-title"><?php echo esc_html( $card['title'] ); ?></h1>
				<div class="apex-blog-single-meta">
					<span class="apex-blog-single-meta-item">
						<svg class="apex-blog-single-meta-icon" width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
						<span><?php echo esc_html( $author ); ?></span>
					</span>
					<span class="apex-blog-single-meta-sep" aria-hidden="true"></span>
					<span class="apex-blog-single-meta-item">
						<svg class="apex-blog-single-meta-icon" width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 16H5V10h14v10z"/></svg>
						<time datetime="<?php echo esc_attr( $datetime ); ?>"><?php echo esc_html( $date_label ); ?></time>
					</span>
				</div>
			</header>

			<?php if ( $thumb ) : ?>
				<figure class="apex-blog-single-featured">
					<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" loading="lazy">
				</figure>
			<?php endif; ?>

			<div class="apex-blog-article entry-content">
				<?php if ( ! empty( $card['excerpt'] ) || ! empty( $card['short_text'] ) ) : ?>
					<section class="apex-blog-detail-paragraph apex-blog-detail-paragraph--intro">
						<h2 class="apex-blog-detail-paragraph__title"><?php esc_html_e( 'Introduction', 'elite-shipping' ); ?></h2>
						<?php if ( ! empty( $card['excerpt'] ) && function_exists( 'elite_shipping_format_blog_card_prose' ) ) : ?>
							<div class="apex-blog-card-lead"><?php echo elite_shipping_format_blog_card_prose( $card['excerpt'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<?php elseif ( ! empty( $card['excerpt'] ) ) : ?>
							<p class="apex-blog-card-lead"><?php echo esc_html( $card['excerpt'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $card['short_text'] ) && function_exists( 'elite_shipping_format_blog_card_prose' ) ) : ?>
							<div class="apex-blog-card-short-text"><?php echo elite_shipping_format_blog_card_prose( $card['short_text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<?php elseif ( ! empty( $card['short_text'] ) ) : ?>
							<p class="apex-blog-card-short-text"><?php echo esc_html( $card['short_text'] ); ?></p>
						<?php endif; ?>
					</section>
				<?php endif; ?>
				<?php if ( ! empty( $card['details'] ) && function_exists( 'elite_shipping_render_blog_card_details_html' ) ) : ?>
					<?php echo elite_shipping_render_blog_card_details_html( $card['details'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
			</div>

			<?php
			$older = ( ! empty( $card['slug'] ) && function_exists( 'elite_shipping_get_customizer_blog_card_older' ) )
				? elite_shipping_get_customizer_blog_card_older( $card['slug'] )
				: null;

			get_template_part(
				'template-parts/blog',
				'post-footer',
				array(
					'comments_mode' => 'preview',
					'share_url'     => ! empty( $card['url'] ) ? $card['url'] : '',
					'share_title'   => $card['title'],
					'share_image'   => $thumb,
					'older_url'     => ( $older && ! empty( $older['url'] ) ) ? $older['url'] : '',
					'older_title'   => ( $older && ! empty( $older['title'] ) ) ? $older['title'] : '',
				)
			);
			?>
		</article>

		<?php
		get_template_part(
			'template-parts/blog',
			'sidebar',
			array(
				'exclude_id' => ! empty( $card['slug'] ) ? $card['slug'] : 0,
			)
		);
		?>
	</div>
</section>
