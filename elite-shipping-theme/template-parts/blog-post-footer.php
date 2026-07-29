<?php
/**
 * Blog post footer — share, navigation, and comments.
 *
 * Optional $args for Customizer card detail pages:
 * - share_url, share_title, share_image
 * - newer_url, newer_title
 * - older_url, older_title
 * - comments_mode: 'post' (default) | 'preview'
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = isset( $args ) && is_array( $args ) ? $args : array();

$post_id       = ! empty( $args['post_id'] ) ? absint( $args['post_id'] ) : (int) get_the_ID();
$comments_mode = ! empty( $args['comments_mode'] ) ? (string) $args['comments_mode'] : 'post';
$share_url     = ! empty( $args['share_url'] ) ? (string) $args['share_url'] : '';
$share_title   = ! empty( $args['share_title'] ) ? (string) $args['share_title'] : '';
$share_image   = ! empty( $args['share_image'] ) ? (string) $args['share_image'] : '';
$newer_url     = ! empty( $args['newer_url'] ) ? (string) $args['newer_url'] : '';
$newer_title   = ! empty( $args['newer_title'] ) ? (string) $args['newer_title'] : '';
$older_url     = ! empty( $args['older_url'] ) ? (string) $args['older_url'] : '';
$older_title   = ! empty( $args['older_title'] ) ? (string) $args['older_title'] : '';

$share_overrides = array();
if ( $share_url || $share_title || $share_image ) {
	$share_overrides = array(
		'url'   => $share_url ? $share_url : ( $post_id ? get_permalink( $post_id ) : '' ),
		'title' => $share_title ? $share_title : ( $post_id ? get_the_title( $post_id ) : '' ),
		'image' => $share_image,
	);
}

$share = $share_overrides
	? elite_shipping_get_post_share_links( $share_overrides )
	: elite_shipping_get_post_share_links( $post_id );

$urls = elite_shipping_get_urls();

if ( 'preview' !== $comments_mode ) {
	if ( ! $newer_url ) {
		$next_post = get_next_post();
		if ( $next_post ) {
			$newer_url   = get_permalink( $next_post );
			$newer_title = get_the_title( $next_post );
		}
	}
	if ( ! $older_url ) {
		$prev_post = get_previous_post();
		if ( $prev_post ) {
			$older_url   = get_permalink( $prev_post );
			$older_title = get_the_title( $prev_post );
		}
	}
}
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
		<?php if ( $older_url && $older_title ) : ?>
			<a class="apex-blog-post-nav-older" href="<?php echo esc_url( $older_url ); ?>">
				<span class="apex-blog-post-nav-arrow" aria-hidden="true">&lsaquo;</span>
				<span class="apex-blog-post-nav-text">
					<span class="apex-blog-post-nav-label"><?php esc_html_e( 'Older', 'elite-shipping' ); ?></span>
					<span class="apex-blog-post-nav-title"><?php echo esc_html( $older_title ); ?></span>
				</span>
			</a>
		<?php else : ?>
			<span class="apex-blog-post-nav-spacer" aria-hidden="true"></span>
		<?php endif; ?>

		<a class="apex-blog-post-nav-grid" href="<?php echo esc_url( $urls['blog'] ); ?>" aria-label="<?php esc_attr_e( 'Back to blog', 'elite-shipping' ); ?>">
			<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 0h7v7h-7v-7z"/></svg>
		</a>

		<?php if ( $newer_url && $newer_title ) : ?>
			<a class="apex-blog-post-nav-newer" href="<?php echo esc_url( $newer_url ); ?>">
				<span class="apex-blog-post-nav-text">
					<span class="apex-blog-post-nav-label"><?php esc_html_e( 'Newer', 'elite-shipping' ); ?></span>
					<span class="apex-blog-post-nav-title"><?php echo esc_html( $newer_title ); ?></span>
				</span>
				<span class="apex-blog-post-nav-arrow" aria-hidden="true">&rsaquo;</span>
			</a>
		<?php else : ?>
			<span class="apex-blog-post-nav-spacer" aria-hidden="true"></span>
		<?php endif; ?>
	</nav>

	<div class="apex-blog-comments" id="respond">
		<?php if ( 'preview' === $comments_mode ) : ?>
			<?php
			$current_user = wp_get_current_user();
			$is_logged_in = is_user_logged_in() && $current_user instanceof WP_User && $current_user->exists();
			?>
			<h3 class="comment-reply-title apex-blog-comments-title"><?php esc_html_e( 'Leave a Reply', 'elite-shipping' ); ?></h3>
			<?php if ( $is_logged_in ) : ?>
				<p class="logged-in-as">
					<?php
					printf(
						/* translators: 1: user display name, 2: edit profile URL, 3: logout URL */
						wp_kses(
							__( 'Logged in as %1$s. <a href="%2$s">Edit your profile</a>. <a href="%3$s">Log out?</a> Required fields are marked <span class="required">*</span>', 'elite-shipping' ),
							array(
								'a'    => array( 'href' => array() ),
								'span' => array( 'class' => array() ),
							)
						),
						esc_html( $current_user->display_name ),
						esc_url( get_edit_profile_url() ),
						esc_url( wp_logout_url( get_permalink() ) )
					);
					?>
				</p>
			<?php else : ?>
				<p class="comment-notes">
					<span class="required-field-message">
						<?php esc_html_e( 'Required fields are marked', 'elite-shipping' ); ?>
						<span class="required">*</span>
					</span>
				</p>
			<?php endif; ?>
			<form class="apex-blog-comment-form comment-form" action="<?php echo esc_url( $share_url ? $share_url : '#' ); ?>" method="post">
				<p class="comment-form-comment">
					<label for="comment"><?php esc_html_e( 'Comment', 'elite-shipping' ); ?> <span class="required">*</span></label>
					<textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea>
				</p>
				<?php if ( ! $is_logged_in ) : ?>
					<p class="comment-form-author">
						<label for="author"><?php esc_html_e( 'Name', 'elite-shipping' ); ?> <span class="required">*</span></label>
						<input id="author" name="author" type="text" value="" size="30" maxlength="245" required="required">
					</p>
					<p class="comment-form-email">
						<label for="email"><?php esc_html_e( 'Email', 'elite-shipping' ); ?> <span class="required">*</span></label>
						<input id="email" name="email" type="email" value="" size="30" maxlength="100" required="required">
					</p>
				<?php endif; ?>
				<p class="form-submit">
					<input name="submit" type="submit" id="submit" class="apex-blog-comment-submit submit" value="<?php esc_attr_e( 'Post Comment', 'elite-shipping' ); ?>">
				</p>
			</form>
		<?php elseif ( $post_id && comments_open( $post_id ) ) : ?>
			<?php
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
			?>
		<?php else : ?>
			<h2 class="apex-blog-comments-title"><?php esc_html_e( 'Leave a Reply', 'elite-shipping' ); ?></h2>
			<p class="apex-blog-comments-closed"><?php esc_html_e( 'Comments are closed for this post.', 'elite-shipping' ); ?></p>
		<?php endif; ?>
	</div>
</div>
