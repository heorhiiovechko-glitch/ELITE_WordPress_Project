<?php
/**
 * Blog single sidebar — categories and recent posts.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$urls         = elite_shipping_get_urls();
$exclude_id   = isset( $args['exclude_id'] ) ? absint( $args['exclude_id'] ) : 0;
$recent_posts = elite_shipping_get_recent_blog_posts( $exclude_id, 3 );
?>
<aside class="apex-blog-sidebar">
	<div class="apex-blog-sidebar-block">
		<h2 class="apex-blog-sidebar-title"><?php echo esc_html( get_theme_mod( 'elite_blog_sidebar_categories_title', __( 'Categories', 'elite-shipping' ) ) ); ?></h2>
		<ul class="apex-blog-sidebar-list">
			<li><a href="<?php echo esc_url( $urls['blog'] ); ?>"><?php echo esc_html( get_theme_mod( 'elite_blog_card_cat', __( 'Blog', 'elite-shipping' ) ) ); ?></a></li>
		</ul>
	</div>

	<div class="apex-blog-sidebar-block">
		<h2 class="apex-blog-sidebar-title"><?php echo esc_html( get_theme_mod( 'elite_blog_sidebar_recent_title', __( 'Recent Posts', 'elite-shipping' ) ) ); ?></h2>
		<ul class="apex-blog-sidebar-recent">
			<?php foreach ( $recent_posts as $recent ) : ?>
				<li class="apex-blog-sidebar-recent-item">
					<a class="apex-blog-sidebar-recent-media" href="<?php echo esc_url( $recent['url'] ); ?>">
						<img src="<?php echo esc_url( $recent['image'] ); ?>" alt="<?php echo esc_attr( $recent['title'] ); ?>" loading="lazy">
					</a>
					<div class="apex-blog-sidebar-recent-copy">
						<a class="apex-blog-sidebar-recent-title" href="<?php echo esc_url( $recent['url'] ); ?>"><?php echo esc_html( $recent['title'] ); ?></a>
						<span class="apex-blog-sidebar-recent-meta">
							<?php echo esc_html( $recent['date'] ); ?>
							<?php
							if ( empty( $recent['comments'] ) ) {
								esc_html_e( 'No Comments', 'elite-shipping' );
							} else {
								printf(
									/* translators: %d: comment count */
									esc_html( _n( '%d Comment', '%d Comments', (int) $recent['comments'], 'elite-shipping' ) ),
									(int) $recent['comments']
								);
							}
							?>
						</span>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</aside>
