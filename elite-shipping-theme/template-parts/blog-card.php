<?php
/**
 * Blog post card for archive listings.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post = isset( $args['post'] ) && is_array( $args['post'] ) ? $args['post'] : array();
if ( empty( $post['title'] ) || empty( $post['url'] ) ) {
	return;
}
?>
<article class="apex-blog-card">
	<a class="apex-blog-card-media" href="<?php echo esc_url( $post['url'] ); ?>">
		<img src="<?php echo esc_url( $post['image'] ); ?>" alt="<?php echo esc_attr( $post['title'] ); ?>" loading="lazy">
		<span class="apex-blog-card-media-shade" aria-hidden="true"></span>
		<div class="apex-blog-card-date">
			<span class="apex-blog-card-day"><?php echo esc_html( $post['day'] ); ?></span>
			<span class="apex-blog-card-month"><?php echo esc_html( $post['month'] ); ?></span>
		</div>
	</a>
	<div class="apex-blog-card-body">
		<span class="apex-blog-card-cat"><?php echo esc_html( get_theme_mod( 'elite_blog_card_cat', __( 'Blog', 'elite-shipping' ) ) ); ?></span>
		<h2 class="apex-blog-card-title">
			<a href="<?php echo esc_url( $post['url'] ); ?>"><?php echo esc_html( $post['title'] ); ?></a>
		</h2>
		<div class="apex-blog-card-meta">
			<span><?php esc_html_e( 'Posted by', 'elite-shipping' ); ?> <strong><?php echo esc_html( $post['author'] ); ?></strong></span>
			<span class="apex-blog-card-meta-dot" aria-hidden="true"></span>
			<time datetime="<?php echo esc_attr( $post['datetime'] ); ?>"><?php echo esc_html( $post['date'] ); ?></time>
		</div>
		<?php if ( ! empty( $post['excerpt'] ) ) : ?>
			<p class="apex-blog-card-excerpt"><?php echo esc_html( $post['excerpt'] ); ?></p>
		<?php endif; ?>
		<a class="apex-blog-card-more" href="<?php echo esc_url( $post['url'] ); ?>">
			<span><?php echo esc_html( get_theme_mod( 'elite_blog_card_more', __( 'Continue reading', 'elite-shipping' ) ) ); ?></span>
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
		</a>
	</div>
</article>
