<?php
/**
 * Contact Us page template.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$urls     = elite_shipping_get_urls();
$contact  = elite_shipping_get_contact_details();
$media    = static function ( $path ) {
	return elite_shipping_migrate_media_url( 'https://firstchoiceshippingcontainers.com/wp-content/uploads/' . ltrim( $path, '/' ) );
};
$hero_img = $media( '2025/07/510631176_23998021113198545_7887283231137689143_n.jpg' );
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
			<span class="apex-kicker">CONTACT US</span>
			<h1 class="apex-page-hero-title">Get In Touch</h1>
			<p class="apex-page-hero-desc">Speak with our team about container quotes, delivery, modifications, and support — we are here to help across the UK.</p>
		</div>
	</section>

	<section class="apex-contact-main">
		<div class="elite-container apex-contact-grid">
			<div class="apex-contact-info">
				<div class="apex-contact-block">
					<h2 class="apex-contact-heading">Get in touch</h2>
					<div class="apex-contact-copy">
						<p>Feel free to talk to our online representative at any time using our Live Chat system on our website.</p>
						<p>Please be patient while waiting for a response. (24/7 Support!)</p>
						<p>
							Phone General Inquiries:
							<a href="<?php echo esc_url( $contact['phone_href'] ); ?>"><?php echo esc_html( $contact['phone'] ); ?></a>
						</p>
					</div>
				</div>

				<div class="apex-contact-block">
					<h2 class="apex-contact-heading">Company Address</h2>
					<div class="apex-contact-copy">
						<p>
							Address (UK):
							<a href="<?php echo esc_url( $contact['address_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $contact['address'] ); ?></a>
						</p>
						<p>
							Telephone / WhatsApp:
							<a href="<?php echo esc_url( $contact['phone_href'] ); ?>"><?php echo esc_html( $contact['phone'] ); ?></a>
						</p>
						<p>
							Email:
							<a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>"><?php echo esc_html( $contact['email'] ); ?></a>
						</p>
					</div>
				</div>
			</div>

			<div class="apex-contact-form-wrap">
				<span class="apex-kicker">INFORMATION ABOUT US</span>
				<h2 class="apex-contact-form-title">Contact Us For Any Questions</h2>
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
