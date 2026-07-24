<?php
/**
 * About Us page template.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$urls         = elite_shipping_get_urls();
$testimonials = elite_shipping_get_about_testimonials();
$about        = static function ( $filename ) {
	return elite_shipping_get_about_image_url( $filename );
};
$features     = array(
	array( 'Quality Assurance', 'Every container is rigorously inspected for long-lasting durability.' ),
	array( 'Vast Selection', 'From brand-new units to affordable used containers, we have options for every need.' ),
	array( 'Customer-Centric Service', 'Our expert team is with you at every step — from quote to delivery.' ),
	array( 'Transparent Pricing', 'No hidden fees — just honest, upfront costs every time.' ),
	array( 'Easy Online Experience', 'Shop confidently with a fast, secure, and intuitive digital process.' ),
	array( 'Secure Payments', 'Enjoy safe and seamless transactions through our trusted payment gateway.' ),
	array( 'UK Nationwide Delivery', 'We deliver across the United Kingdom — reliably and quickly.' ),
	array( '24/7 Support', 'Our friendly support team is ready around the clock to help you anytime.' ),
);
$why_cards    = array(
	array(
		'title' => 'Quality Products',
		'text'  => 'We take pride in offering best-in-class containers made from highly durable steel, suitable for industrial, commercial, and residential applications.',
		'image' => $about( 'about_.png' ),
	),
	array(
		'title' => 'Competitive Pricing',
		'text'  => 'With strong market insight and industry experience, we provide top-tier containers at unbeatable prices — delivering maximum value with no compromise on quality.',
		'image' => $about( 'about_.png' ),
	),
	array(
		'title' => 'Personalized Service',
		'text'  => 'We tailor every solution to your unique needs. From modified units to custom layouts, we deliver containers that match your specifications perfectly.',
		'image' => $about( 'about_.png' ),
	),
);
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'elite-about-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<main class="apex-about-page">
	<section class="apex-page-hero">
		<div class="elite-container">
			<span class="apex-kicker">ABOUT US</span>
			<h1 class="apex-page-hero-title">About Elite Shipping Containers</h1>
			<p class="apex-page-hero-desc">Your trusted UK partner for premium shipping containers, nationwide delivery, and expert support.</p>
		</div>
	</section>

	<section class="apex-about-split apex-about-split--white">
		<div class="elite-container apex-about-split-inner">
			<div class="apex-about-split-copy">
				<span class="apex-kicker">WHO WE ARE</span>
				<h2>Your Ultimate Partner in Premium Container Solutions</h2>
				<p>At Elite Shipping Containers Ltd, we lead the way in delivering high-quality shipping containers through a modern, customer-first platform. Whether for personal or commercial use, our goal is to make finding and buying containers seamless, transparent, and efficient.</p>
			</div>
			<div class="apex-about-split-media">
				<img class="apex-about-split-main" src="<?php echo esc_url( $about( 'about_1.webp' ) ); ?>" alt="Who We Are" loading="lazy">
				<img class="apex-about-split-sub" src="<?php echo esc_url( $about( 'about_2.jpg' ) ); ?>" alt="Container yard" loading="lazy">
			</div>
		</div>
	</section>

	<section class="apex-about-split apex-about-split--gray">
		<div class="elite-container apex-about-split-inner apex-about-split-inner--reverse">
			<div class="apex-about-split-copy">
				<span class="apex-kicker">OUR MISSION</span>
				<h2>Empowering You with Choice, Confidence &amp; Convenience</h2>
				<p>Our mission is simple yet powerful: to offer a wide selection of shipping containers, unmatched service, and competitive prices. We strive to make every customer interaction stress-free, ensuring you get exactly what you need, when you need it — with complete peace of mind.</p>
			</div>
			<div class="apex-about-split-media">
				<img class="apex-about-split-main" src="<?php echo esc_url( $about( 'about_3.webp' ) ); ?>" alt="Our Mission" loading="lazy">
				<img class="apex-about-split-sub" src="<?php echo esc_url( $about( 'about_4.jpeg' ) ); ?>" alt="Container office" loading="lazy">
			</div>
		</div>
	</section>

	<section class="apex-about-why">
		<div class="elite-container">
			<h2 class="apex-about-why-title">Why Choose Elite Shipping Containers</h2>
			<p class="apex-about-why-tagline">Trusted across the UK for quality, value, and service</p>
			<div class="apex-about-why-grid">
				<?php foreach ( $why_cards as $card ) : ?>
					<article class="apex-about-why-card">
						<img src="<?php echo esc_url( $card['image'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" loading="lazy">
						<div class="apex-about-why-card-body">
							<h3><?php echo esc_html( $card['title'] ); ?></h3>
							<p><?php echo esc_html( $card['text'] ); ?></p>
							<a class="apex-about-why-link" href="<?php echo esc_url( $urls['shop'] ); ?>">Shop Now →</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="apex-about-features">
		<div class="elite-container">
			<div class="apex-about-features-panel">
				<h2>What Sets Us Apart</h2>
				<p class="apex-about-features-sub">Quality, transparency &amp; customer satisfaction guaranteed</p>
				<div class="apex-about-features-grid">
					<?php foreach ( $features as $feature ) : ?>
						<div class="apex-about-feature">
							<strong><?php echo esc_html( $feature[0] ); ?></strong>
							<span><?php echo esc_html( $feature[1] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<section class="apex-about-split apex-about-split--panel">
		<div class="elite-container apex-about-panel">
			<div class="apex-about-panel-copy">
				<span class="apex-kicker">OUR EXPERTISE</span>
				<h2>Driven by Experience, Built on Trust</h2>
				<p>With years of experience in the shipping container industry, we understand what our customers need. Our carefully curated inventory spans various sizes, conditions, and applications — tailored for construction, retail, agriculture, events, and more.</p>
			</div>
			<div class="apex-about-panel-media">
				<img src="<?php echo esc_url( $about( 'about_5.png' ) ); ?>" alt="Our expertise" loading="lazy">
			</div>
		</div>
	</section>

	<section class="apex-about-split apex-about-split--panel">
		<div class="elite-container apex-about-panel apex-about-panel--reverse">
			<div class="apex-about-panel-copy">
				<span class="apex-kicker">SUSTAINABILITY</span>
				<h2>Supporting a Circular Economy with Smarter Choices</h2>
				<p>We are committed to sustainability through the promotion of high-quality used containers. By extending the lifecycle of these robust units, we help reduce waste and support environmentally responsible business practices — for a greener tomorrow.</p>
			</div>
			<div class="apex-about-panel-media">
				<img src="<?php echo esc_url( $about( 'about_6.png' ) ); ?>" alt="Sustainable container use" loading="lazy">
			</div>
		</div>
	</section>

	<section class="apex-about-testimonials">
		<div class="elite-container">
			<h2>What Our Clients Say About Us</h2>
			<div class="apex-about-testimonials-grid">
				<?php foreach ( $testimonials as $item ) : ?>
					<article class="apex-about-testimonial">
						<span class="apex-about-testimonial-service"><?php echo esc_html( $item['service'] ); ?></span>
						<h3><?php echo esc_html( $item['name'] ); ?> <span class="apex-about-verified">Verified</span></h3>
						<div class="apex-about-stars" aria-hidden="true">★★★★★</div>
						<p>&ldquo;<?php echo esc_html( $item['quote'] ); ?>&rdquo;</p>
						<span class="apex-about-country">United Kingdom</span>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="apex-about-cta">
				<a class="elite-btn elite-btn-primary elite-btn-lg" href="<?php echo esc_url( $urls['shop'] ); ?>">Browse Containers</a>
				<a class="elite-btn elite-btn-outline-orange elite-btn-lg" href="<?php echo esc_url( $urls['contact'] ); ?>">Contact Us</a>
			</div>
		</div>
	</section>
</main>

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
