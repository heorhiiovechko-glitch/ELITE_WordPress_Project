<?php
/**
 * Contact Us page template.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$urls    = elite_shipping_get_urls();
$contact = elite_shipping_get_contact_details();

$hero_kicker = get_theme_mod( 'elite_contact_page_kicker', 'CONTACT US' );
$hero_title  = get_theme_mod( 'elite_contact_page_title', 'Get In Touch' );
$hero_desc   = get_theme_mod( 'elite_contact_page_desc', 'Speak with our team about container quotes, delivery, modifications, and support — we are here to help across the UK.' );
$hero_fallback = elite_shipping_migrate_media_url( 'https://firstchoiceshippingcontainers.com/wp-content/uploads/2025/07/510631176_23998021113198545_7887283231137689143_n.jpg' );
$hero_img      = elite_shipping_get_theme_mod_image_url( 'elite_contact_hero_image', $hero_fallback );

$info_heading = get_theme_mod( 'elite_contact_info_heading', 'Get in touch' );
$info_intro   = elite_shipping_format_contact_info_intro(
	get_theme_mod( 'elite_contact_info_intro', elite_shipping_get_contact_info_intro_default() ),
	$contact['company_name']
);
$form_kicker  = get_theme_mod( 'elite_contact_form_kicker', 'INFORMATION ABOUT US' );
$form_title   = get_theme_mod( 'elite_contact_form_title', 'Contact Us For Any Questions' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'elite-contact-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<main class="apex-contact-page">
	<section class="apex-page-hero apex-page-hero--contact" style="background-image: linear-gradient(rgba(0, 18, 40, 0.72), rgba(0, 18, 40, 0.72)), url('<?php echo esc_url( $hero_img ); ?>');">
		<div class="elite-container">
			<span class="apex-kicker"><?php echo esc_html( $hero_kicker ); ?></span>
			<h1 class="apex-page-hero-title"><?php echo esc_html( $hero_title ); ?></h1>
			<p class="apex-page-hero-desc"><?php echo wp_kses_post( $hero_desc ); ?></p>
		</div>
	</section>

	<section class="apex-contact-main">
		<div class="elite-container apex-contact-grid">
			<div class="apex-contact-info">
				<div class="apex-contact-block">
					<h2 class="apex-contact-heading"><?php echo esc_html( $info_heading ); ?></h2>
					<div class="apex-contact-copy">
						<p><?php echo esc_html( $info_intro ); ?></p>
						<p>
							<?php esc_html_e( 'Phone:', 'elite-shipping' ); ?>
							<a href="<?php echo esc_url( $contact['phone_href'] ); ?>"><?php echo esc_html( $contact['phone'] ); ?></a>
						</p>
						<p>
							<?php esc_html_e( 'Email:', 'elite-shipping' ); ?>
							<a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>"><?php echo esc_html( $contact['email'] ); ?></a>
						</p>
					</div>
				</div>

				<div class="apex-contact-block">
					<h2 class="apex-contact-heading"><?php echo esc_html( $contact['company_name'] ); ?></h2>
					<div class="apex-contact-copy">
						<p>
							<?php esc_html_e( 'Address (UK):', 'elite-shipping' ); ?>
							<a href="<?php echo esc_url( $contact['address_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $contact['address'] ); ?></a>
						</p>
						<p>
							<?php esc_html_e( 'Website:', 'elite-shipping' ); ?>
							<a href="<?php echo esc_url( $contact['website_url'] ); ?>"><?php echo esc_html( $contact['website'] ); ?></a>
						</p>
						<p>
							<?php esc_html_e( 'WhatsApp:', 'elite-shipping' ); ?>
							<a href="<?php echo esc_url( $contact['whatsapp_href'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $contact['phone'] ); ?></a>
						</p>
						<p>
							<?php esc_html_e( 'Email:', 'elite-shipping' ); ?>
							<a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>"><?php echo esc_html( $contact['email'] ); ?></a>
						</p>
					</div>
				</div>
			</div>

			<div class="apex-contact-form-wrap">
				<span class="apex-kicker"><?php echo esc_html( $form_kicker ); ?></span>
				<h2 class="apex-contact-form-title"><?php echo esc_html( $form_title ); ?></h2>
				<div class="apex-contact-form">
					<?php elite_shipping_render_contact_form(); ?>
				</div>
			</div>
		</div>
	</section>

	<section class="apex-contact-map">
		<div class="apex-contact-map-embed">
			<iframe
				loading="lazy"
				src="<?php echo esc_url( $contact['map_embed'] ); ?>"
				title="<?php echo esc_attr( $contact['address'] ); ?>"
				aria-label="<?php echo esc_attr( $contact['address'] ); ?>"
				allowfullscreen
			></iframe>
		</div>
	</section>
</main>

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
