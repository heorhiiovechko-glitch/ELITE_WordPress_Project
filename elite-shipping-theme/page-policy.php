<?php
/**
 * Shared policy page template.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slug       = get_post_field( 'post_name', get_queried_object_id() );
$config     = elite_shipping_get_policy_pages_config();
$policy     = isset( $config[ $slug ] ) ? $config[ $slug ] : null;
$page_title = $policy ? $policy['title'] : get_the_title();
$kicker     = $policy ? $policy['kicker'] : __( 'POLICY', 'elite-shipping' );
$intro      = $policy ? $policy['intro'] : '';
$sections   = $policy ? $policy['sections'] : array();
$content    = trim( (string) get_post_field( 'post_content', get_queried_object_id() ) );
$hero_img   = elite_shipping_migrate_media_url( 'https://firstchoiceshippingcontainers.com/wp-content/uploads/2025/07/510631176_23998021113198545_7887283231137689143_n.jpg' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'elite-policy-page elite-policy-page--' . sanitize_html_class( $slug ) ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<main class="apex-policy-page">
	<?php
	get_template_part(
		'template-parts/page-hero',
		'bar',
		array(
			'kicker'   => $kicker,
			'title'    => $page_title,
			'desc'     => $intro,
			'image'    => $hero_img,
			'modifier' => 'apex-page-hero--policy',
		)
	);
	?>

	<section class="apex-policy-content">
		<div class="elite-container">
			<div class="apex-policy-layout">
				<article class="apex-policy-panel">
					<header class="apex-policy-panel__head">
						<p class="apex-policy-panel__eyebrow"><?php esc_html_e( 'Official policy', 'elite-shipping' ); ?></p>
						<h2 class="apex-policy-panel__title"><?php echo esc_html( $page_title ); ?></h2>
						<?php if ( $intro ) : ?>
							<p class="apex-policy-panel__lead"><?php echo esc_html( $intro ); ?></p>
						<?php endif; ?>
					</header>

					<?php if ( ! empty( $sections ) ) : ?>
						<div class="apex-policy-sections">
							<?php foreach ( $sections as $index => $section ) : ?>
								<?php
								$section_id = 'policy-section-' . ( $index + 1 );
								$heading    = (string) $section['heading'];
								$number     = (string) ( $index + 1 );
								if ( preg_match( '/^(\d+)\.\s*(.+)$/', $heading, $matches ) ) {
									$number  = $matches[1];
									$heading = $matches[2];
								}
								?>
								<section class="apex-policy-section" id="<?php echo esc_attr( $section_id ); ?>">
									<div class="apex-policy-section__head">
										<span class="apex-policy-section__num" aria-hidden="true"><?php echo esc_html( str_pad( $number, 2, '0', STR_PAD_LEFT ) ); ?></span>
										<h3 class="apex-policy-section__title"><?php echo esc_html( $heading ); ?></h3>
									</div>
									<div class="apex-policy-section__body">
										<?php foreach ( $section['paragraphs'] as $paragraph ) : ?>
											<p><?php echo esc_html( $paragraph ); ?></p>
										<?php endforeach; ?>
									</div>
								</section>
							<?php endforeach; ?>
						</div>
					<?php elseif ( $content ) : ?>
						<div class="apex-policy-custom-content">
							<?php the_content(); ?>
						</div>
					<?php else : ?>
						<p class="apex-policy-empty"><?php esc_html_e( 'Policy content will be published here shortly.', 'elite-shipping' ); ?></p>
					<?php endif; ?>
				</article>

				<aside class="apex-policy-aside">
					<?php if ( ! empty( $sections ) ) : ?>
						<nav class="apex-policy-toc" aria-label="<?php esc_attr_e( 'On this page', 'elite-shipping' ); ?>">
							<p class="apex-policy-toc__title"><?php esc_html_e( 'On this page', 'elite-shipping' ); ?></p>
							<ol class="apex-policy-toc__list">
								<?php foreach ( $sections as $index => $section ) : ?>
									<?php
									$section_id = 'policy-section-' . ( $index + 1 );
									$heading    = (string) $section['heading'];
									if ( preg_match( '/^\d+\.\s*(.+)$/', $heading, $matches ) ) {
										$heading = $matches[1];
									}
									?>
									<li>
										<a href="#<?php echo esc_attr( $section_id ); ?>"><?php echo esc_html( $heading ); ?></a>
									</li>
								<?php endforeach; ?>
							</ol>
						</nav>
					<?php endif; ?>

					<?php elite_shipping_render_policy_contact_block(); ?>
				</aside>
			</div>
		</div>
	</section>
</main>

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
