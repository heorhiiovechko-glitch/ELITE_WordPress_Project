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
$about_fallback = static function ( $filename ) {
	return elite_shipping_get_about_image_url( $filename );
};

$hero_kicker = get_theme_mod( 'elite_about_page_kicker', 'ABOUT US' );
$hero_title  = get_theme_mod( 'elite_about_page_title', 'About Elite Shipping Containers' );
$hero_desc   = get_theme_mod( 'elite_about_page_desc', 'Your trusted UK partner for premium shipping containers, nationwide delivery, and expert support.' );

$who_kicker = get_theme_mod( 'elite_about_who_kicker', 'WHO WE ARE' );
$who_title  = get_theme_mod( 'elite_about_who_title', 'Your Ultimate Partner in Premium Container Solutions' );
$who_text   = get_theme_mod( 'elite_about_who_text', 'At Elite Shipping Containers Ltd, we lead the way in delivering high-quality shipping containers through a modern, customer-first platform. Whether for personal or commercial use, our goal is to make finding and buying containers seamless, transparent, and efficient.' );
$who_main   = elite_shipping_get_theme_mod_image_url( 'elite_about_who_image_main', $about_fallback( 'about_1.webp' ) );
$who_sub    = elite_shipping_get_theme_mod_image_url( 'elite_about_who_image_sub', $about_fallback( 'about_2.jpg' ) );

$mission_kicker = get_theme_mod( 'elite_about_mission_kicker', 'OUR MISSION' );
$mission_title  = get_theme_mod( 'elite_about_mission_title', 'Empowering You with Choice, Confidence & Convenience' );
$mission_text   = get_theme_mod( 'elite_about_mission_text', 'Our mission is simple yet powerful: to offer a wide selection of shipping containers, unmatched service, and competitive prices. We strive to make every customer interaction stress-free, ensuring you get exactly what you need, when you need it — with complete peace of mind.' );
$mission_main   = elite_shipping_get_theme_mod_image_url( 'elite_about_mission_image_main', $about_fallback( 'about_3.webp' ) );
$mission_sub    = elite_shipping_get_theme_mod_image_url( 'elite_about_mission_image_sub', $about_fallback( 'about_4.jpeg' ) );

$why_title   = get_theme_mod( 'elite_about_why_title', 'Why Choose Elite Shipping Containers' );
$why_tagline = get_theme_mod( 'elite_about_why_tagline', 'Trusted across the UK for quality, value, and service' );
$why_btn     = get_theme_mod( 'elite_about_why_btn_text', 'Shop Now →' );
$why_btn_url = get_theme_mod( 'elite_about_why_btn_url', $urls['shop'] );
$why_defaults = array(
	1 => array(
		'Quality Products',
		'We take pride in offering best-in-class containers made from highly durable steel, suitable for industrial, commercial, and residential applications.',
	),
	2 => array(
		'Competitive Pricing',
		'With strong market insight and industry experience, we provide top-tier containers at unbeatable prices — delivering maximum value with no compromise on quality.',
	),
	3 => array(
		'Personalized Service',
		'We tailor every solution to your unique needs. From modified units to custom layouts, we deliver containers that match your specifications perfectly.',
	),
);
$why_cards = array();
foreach ( $why_defaults as $i => $defaults ) {
	$why_cards[] = array(
		'title' => get_theme_mod( 'elite_about_why_' . $i . '_title', $defaults[0] ),
		'text'  => get_theme_mod( 'elite_about_why_' . $i . '_text', $defaults[1] ),
		'image' => elite_shipping_get_theme_mod_image_url( 'elite_about_why_' . $i . '_image', $about_fallback( 'about_.png' ) ),
	);
}

$features_title = get_theme_mod( 'elite_about_features_title', 'What Sets Us Apart' );
$features_sub   = get_theme_mod( 'elite_about_features_sub', 'Quality, transparency & customer satisfaction guaranteed' );
$feature_defaults = array(
	1 => array( 'Quality Assurance', 'Every container is rigorously inspected for long-lasting durability.' ),
	2 => array( 'Vast Selection', 'From brand-new units to affordable used containers, we have options for every need.' ),
	3 => array( 'Customer-Centric Service', 'Our expert team is with you at every step — from quote to delivery.' ),
	4 => array( 'Transparent Pricing', 'No hidden fees — just honest, upfront costs every time.' ),
	5 => array( 'Easy Online Experience', 'Shop confidently with a fast, secure, and intuitive digital process.' ),
	6 => array( 'Secure Payments', 'Enjoy safe and seamless transactions through our trusted payment gateway.' ),
	7 => array( 'UK Nationwide Delivery', 'We deliver across the United Kingdom — reliably and quickly.' ),
	8 => array( '24/7 Support', 'Our friendly support team is ready around the clock to help you anytime.' ),
);
$features = array();
foreach ( $feature_defaults as $i => $defaults ) {
	$features[] = array(
		get_theme_mod( 'elite_about_feature_' . $i . '_title', $defaults[0] ),
		get_theme_mod( 'elite_about_feature_' . $i . '_text', $defaults[1] ),
	);
}

$expertise_kicker = get_theme_mod( 'elite_about_expertise_kicker', 'OUR EXPERTISE' );
$expertise_title  = get_theme_mod( 'elite_about_expertise_title', 'Driven by Experience, Built on Trust' );
$expertise_text   = get_theme_mod( 'elite_about_expertise_text', 'With years of experience in the shipping container industry, we understand what our customers need. Our carefully curated inventory spans various sizes, conditions, and applications — tailored for construction, retail, agriculture, events, and more.' );
$expertise_image  = elite_shipping_get_theme_mod_image_url( 'elite_about_expertise_image', $about_fallback( 'about_5.png' ) );

$sustain_kicker = get_theme_mod( 'elite_about_sustain_kicker', 'SUSTAINABILITY' );
$sustain_title  = get_theme_mod( 'elite_about_sustain_title', 'Supporting a Circular Economy with Smarter Choices' );
$sustain_text   = get_theme_mod( 'elite_about_sustain_text', 'We are committed to sustainability through the promotion of high-quality used containers. By extending the lifecycle of these robust units, we help reduce waste and support environmentally responsible business practices — for a greener tomorrow.' );
$sustain_image  = elite_shipping_get_theme_mod_image_url( 'elite_about_sustain_image', $about_fallback( 'about_6.png' ) );

$testimonials_title = get_theme_mod( 'elite_about_testimonials_title', 'What Our Clients Say About Us' );
$cta_primary_text   = get_theme_mod( 'elite_about_cta_primary_text', 'Browse Containers' );
$cta_primary_url    = get_theme_mod( 'elite_about_cta_primary_url', $urls['shop'] );
$cta_secondary_text = get_theme_mod( 'elite_about_cta_secondary_text', 'Contact Us' );
$cta_secondary_url  = get_theme_mod( 'elite_about_cta_secondary_url', $urls['contact'] );
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
			<span class="apex-kicker"><?php echo esc_html( $hero_kicker ); ?></span>
			<h1 class="apex-page-hero-title"><?php echo esc_html( $hero_title ); ?></h1>
			<p class="apex-page-hero-desc"><?php echo wp_kses_post( $hero_desc ); ?></p>
		</div>
	</section>

	<section class="apex-about-split apex-about-split--white">
		<div class="elite-container apex-about-split-inner">
			<div class="apex-about-split-copy">
				<span class="apex-kicker"><?php echo esc_html( $who_kicker ); ?></span>
				<h2><?php echo esc_html( $who_title ); ?></h2>
				<p><?php echo wp_kses_post( $who_text ); ?></p>
			</div>
			<div class="apex-about-split-media">
				<img class="apex-about-split-main" src="<?php echo esc_url( $who_main ); ?>" alt="<?php echo esc_attr( $who_title ); ?>" loading="lazy">
				<img class="apex-about-split-sub" src="<?php echo esc_url( $who_sub ); ?>" alt="" loading="lazy">
			</div>
		</div>
	</section>

	<section class="apex-about-split apex-about-split--gray">
		<div class="elite-container apex-about-split-inner apex-about-split-inner--reverse">
			<div class="apex-about-split-copy">
				<span class="apex-kicker"><?php echo esc_html( $mission_kicker ); ?></span>
				<h2><?php echo esc_html( $mission_title ); ?></h2>
				<p><?php echo wp_kses_post( $mission_text ); ?></p>
			</div>
			<div class="apex-about-split-media">
				<img class="apex-about-split-main" src="<?php echo esc_url( $mission_main ); ?>" alt="<?php echo esc_attr( $mission_title ); ?>" loading="lazy">
				<img class="apex-about-split-sub" src="<?php echo esc_url( $mission_sub ); ?>" alt="" loading="lazy">
			</div>
		</div>
	</section>

	<section class="apex-about-why">
		<div class="elite-container">
			<h2 class="apex-about-why-title"><?php echo esc_html( $why_title ); ?></h2>
			<p class="apex-about-why-tagline"><?php echo esc_html( $why_tagline ); ?></p>
			<div class="apex-about-why-grid">
				<?php foreach ( $why_cards as $card ) : ?>
					<article class="apex-about-why-card">
						<img src="<?php echo esc_url( $card['image'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" loading="lazy">
						<div class="apex-about-why-card-body">
							<h3><?php echo esc_html( $card['title'] ); ?></h3>
							<p><?php echo wp_kses_post( $card['text'] ); ?></p>
							<a class="apex-about-why-link" href="<?php echo esc_url( $why_btn_url ); ?>"><?php echo esc_html( $why_btn ); ?></a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="apex-about-features">
		<div class="elite-container">
			<div class="apex-about-features-panel">
				<h2><?php echo esc_html( $features_title ); ?></h2>
				<p class="apex-about-features-sub"><?php echo esc_html( $features_sub ); ?></p>
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
				<span class="apex-kicker"><?php echo esc_html( $expertise_kicker ); ?></span>
				<h2><?php echo esc_html( $expertise_title ); ?></h2>
				<p><?php echo wp_kses_post( $expertise_text ); ?></p>
			</div>
			<div class="apex-about-panel-media">
				<img src="<?php echo esc_url( $expertise_image ); ?>" alt="<?php echo esc_attr( $expertise_title ); ?>" loading="lazy">
			</div>
		</div>
	</section>

	<section class="apex-about-split apex-about-split--panel">
		<div class="elite-container apex-about-panel apex-about-panel--reverse">
			<div class="apex-about-panel-copy">
				<span class="apex-kicker"><?php echo esc_html( $sustain_kicker ); ?></span>
				<h2><?php echo esc_html( $sustain_title ); ?></h2>
				<p><?php echo wp_kses_post( $sustain_text ); ?></p>
			</div>
			<div class="apex-about-panel-media">
				<img src="<?php echo esc_url( $sustain_image ); ?>" alt="<?php echo esc_attr( $sustain_title ); ?>" loading="lazy">
			</div>
		</div>
	</section>

	<section class="apex-about-testimonials">
		<div class="elite-container">
			<h2><?php echo esc_html( $testimonials_title ); ?></h2>
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
				<a class="elite-btn elite-btn-primary elite-btn-lg" href="<?php echo esc_url( $cta_primary_url ); ?>"><?php echo esc_html( $cta_primary_text ); ?></a>
				<a class="elite-btn elite-btn-outline-orange elite-btn-lg" href="<?php echo esc_url( $cta_secondary_url ); ?>"><?php echo esc_html( $cta_secondary_text ); ?></a>
			</div>
		</div>
	</section>
</main>

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
