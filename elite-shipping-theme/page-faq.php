<?php
/**
 * FAQ page template.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$urls    = elite_shipping_get_urls();
$contact = elite_shipping_get_contact_details();
$sections = function_exists( 'elite_shipping_get_faq_sections' ) ? elite_shipping_get_faq_sections() : array();

$hero_kicker = get_theme_mod( 'elite_faq_page_kicker', 'FAQ' );
$hero_title  = get_theme_mod( 'elite_faq_page_title', 'Frequently Asked Questions (FAQ)' );
$hero_desc   = get_theme_mod(
	'elite_faq_page_desc',
	__( 'Find answers about container types, ordering, delivery, modifications, storage use, and customer support.', 'elite-shipping' )
);
$hero_fallback = elite_shipping_migrate_media_url( 'https://firstchoiceshippingcontainers.com/wp-content/uploads/2025/07/510631176_23998021113198545_7887283231137689143_n.jpg' );
$hero_img      = elite_shipping_get_theme_mod_image_url( 'elite_faq_hero_image', $hero_fallback );

$intro_title = get_theme_mod( 'elite_faq_intro_title', '' );
$intro_text  = get_theme_mod(
	'elite_faq_intro_text',
	__( 'Browse the sections below for detailed answers. If you need a tailored quote or advice on delivery and modifications, our team is ready to help.', 'elite-shipping' )
);
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'elite-faq-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<main class="apex-faq-page">
	<?php
	get_template_part(
		'template-parts/page',
		'hero-bar',
		array(
			'kicker'   => $hero_kicker,
			'title'    => $hero_title,
			'desc'     => $hero_desc,
			'image'    => $hero_img,
			'modifier' => 'apex-page-hero--faq',
		)
	);
	?>

	<section class="apex-faq-main">
		<div class="elite-container">
			<div class="apex-faq-layout">
				<?php if ( $intro_title || $intro_text ) : ?>
					<header class="apex-faq-intro">
						<?php if ( $intro_title ) : ?>
							<h2 class="apex-faq-intro__title"><?php echo esc_html( $intro_title ); ?></h2>
						<?php endif; ?>
						<?php if ( $intro_text ) : ?>
							<p class="apex-faq-intro__text"><?php echo esc_html( $intro_text ); ?></p>
						<?php endif; ?>
					</header>
				<?php endif; ?>

				<?php if ( ! empty( $sections ) ) : ?>
					<div class="apex-faq-sections">
						<?php
						$item_index = 0;
						foreach ( $sections as $section ) :
							$section_title = trim( (string) ( $section['title'] ?? '' ) );
							$section_items = isset( $section['items'] ) && is_array( $section['items'] ) ? $section['items'] : array();
							if ( '' === $section_title || empty( $section_items ) ) {
								continue;
							}
							$section_id = sanitize_title( $section_title );
							?>
							<section class="apex-faq-section" aria-labelledby="<?php echo esc_attr( $section_id ); ?>">
								<h2 class="apex-faq-section__title" id="<?php echo esc_attr( $section_id ); ?>"><?php echo esc_html( $section_title ); ?></h2>
								<div class="apex-faq-list">
									<?php foreach ( $section_items as $item ) : ?>
										<?php
										$question = trim( (string) ( $item['question'] ?? '' ) );
										$answer   = trim( (string) ( $item['answer'] ?? '' ) );
										if ( '' === $question || '' === $answer ) {
											continue;
										}
										++$item_index;
										$faq_id = 'faq-item-' . $item_index;
										?>
										<details class="apex-faq-item" id="<?php echo esc_attr( $faq_id ); ?>"<?php echo 1 === $item_index ? ' open' : ''; ?>>
											<summary class="apex-faq-item__question">
												<span class="apex-faq-item__question-text"><?php echo esc_html( $question ); ?></span>
												<span class="apex-faq-item__icon" aria-hidden="true"></span>
											</summary>
											<div class="apex-faq-item__answer">
												<?php
												if ( function_exists( 'elite_shipping_render_faq_answer' ) ) {
													elite_shipping_render_faq_answer( $answer );
												} else {
													echo wp_kses_post( wpautop( $answer ) );
												}
												?>
											</div>
										</details>
									<?php endforeach; ?>
								</div>
							</section>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="apex-faq-empty"><?php esc_html_e( 'FAQ content will appear here soon.', 'elite-shipping' ); ?></p>
				<?php endif; ?>

				<aside class="apex-faq-cta">
					<h2 class="apex-faq-cta__title"><?php esc_html_e( 'Still have questions?', 'elite-shipping' ); ?></h2>
					<p class="apex-faq-cta__text"><?php esc_html_e( 'Speak with our team for pricing, delivery advice, or a custom container solution.', 'elite-shipping' ); ?></p>
					<div class="apex-faq-cta__actions">
						<a class="elite-btn elite-btn-primary" href="<?php echo esc_url( $urls['quote'] ); ?>"><?php esc_html_e( 'Get a Quote', 'elite-shipping' ); ?></a>
						<a class="elite-btn elite-btn-navy" href="<?php echo esc_url( $urls['contact'] ); ?>"><?php esc_html_e( 'Contact Us', 'elite-shipping' ); ?></a>
					</div>
					<p class="apex-faq-cta__meta">
						<a href="<?php echo esc_url( $contact['phone_href'] ); ?>"><?php echo esc_html( $contact['phone'] ); ?></a>
						<span aria-hidden="true">·</span>
						<a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>"><?php echo esc_html( $contact['email'] ); ?></a>
					</p>
				</aside>
			</div>
		</div>
	</section>
</main>

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
