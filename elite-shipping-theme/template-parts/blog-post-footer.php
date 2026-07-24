<?php
/**
 * Blog post footer — share, navigation, and comments.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id   = get_the_ID();
$share     = elite_shipping_get_post_share_links( $post_id );
$urls      = elite_shipping_get_urls();
$prev_post = get_previous_post();
?>
<div class="apex-blog-post-footer">
	<div class="apex-blog-share">
		<?php foreach ( $share as $item ) : ?>
			<a
				class="apex-blog-share-link apex-blog-share-link--<?php echo esc_attr( $item['id'] ); ?>"
				href="<?php echo esc_url( $item['url'] ); ?>"
				target="_blank"
				rel="noopener noreferrer"
				aria-label="<?php echo esc_attr( $item['label'] ); ?>"
			>
				<?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe SVG markup. ?>
			</a>
		<?php endforeach; ?>
	</div>

	<nav class="apex-blog-post-nav" aria-label="<?php esc_attr_e( 'Post navigation', 'elite-shipping' ); ?>">
		<a class="apex-blog-post-nav-grid" href="<?php echo esc_url( $urls['blog'] ); ?>" aria-label="<?php esc_attr_e( 'Back to blog', 'elite-shipping' ); ?>">
			<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 0h7v7h-7v-7z"/></svg>
		</a>

		<?php if ( $prev_post ) : ?>
			<a class="apex-blog-post-nav-older" href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>">
				<span class="apex-blog-post-nav-label"><?php esc_html_e( 'Older', 'elite-shipping' ); ?></span>
				<span class="apex-blog-post-nav-title"><?php echo esc_html( get_the_title( $prev_post ) ); ?></span>
				<span class="apex-blog-post-nav-arrow" aria-hidden="true">&rsaquo;</span>
			</a>
		<?php endif; ?>
	</nav>

	<div class="apex-blog-comments" id="respond">
		<?php
		if ( comments_open( $post_id ) ) {
			comment_form(
				array(
					'title_reply'          => __( 'Leave a Reply', 'elite-shipping' ),
					'title_reply_to'       => __( 'Leave a Reply to %s', 'elite-shipping' ),
					'cancel_reply_link'    => __( 'Cancel reply', 'elite-shipping' ),
					'label_submit'         => __( 'Post Comment', 'elite-shipping' ),
					'comment_notes_before' => '',
					'comment_notes_after'  => '',
					'class_form'           => 'apex-blog-comment-form',
					'class_submit'         => 'apex-blog-comment-submit',
					'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
					'fields'               => array(
						'author'  => '',
						'email'   => '',
						'url'     => '',
						'cookies' => '',
					),
				),
				$post_id
			);
		} else {
			?>
			<h2 class="apex-blog-comments-title"><?php esc_html_e( 'Leave a Reply', 'elite-shipping' ); ?></h2>
			<p class="apex-blog-comments-closed"><?php esc_html_e( 'Comments are closed for this post.', 'elite-shipping' ); ?></p>
			<?php
		}
		?>
	</div>
</div>
