<?php
/**
 * Homepage — pixel-match Apex Containers (type1) for Elite Shipping Containers Ltd.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$urls        = elite_shipping_get_urls();
$hero_slides = elite_shipping_get_hero_slides();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header', 'site' ); ?>

<!-- HERO -->
<section class="apex-hero">
	<div class="apex-hero-bg" aria-hidden="true">
		<div class="apex-hero-bg-track">
			<?php foreach ( $hero_slides as $slide_url ) : ?>
				<div class="apex-hero-bg-slide" style="background-image:url('<?php echo esc_url( $slide_url ); ?>');"></div>
			<?php endforeach; ?>
		</div>
		<div class="apex-hero-bg-overlay"></div>
	</div>
	<div class="elite-container apex-hero-inner">
		<div class="apex-hero-left">
			<p class="apex-hero-eyebrow">RELIABLE SHIPPING CONTAINERS FOR EVERY INDUSTRY — DELIVERED ACROSS THE UK</p>
			<h1 class="apex-hero-title">
				<span class="line-white">PREMIUM SHIPPING CONTAINERS</span>
				<span class="line-orange">BUILT FOR EVERY NEED</span>
			</h1>
			<p class="apex-hero-desc">
				Elite Shipping Containers Ltd provides durable, secure, and affordable shipping containers nationwide.
				Whether you need storage, transport, or a custom-built solution — we deliver quality you can trust.
			</p>
			<div class="apex-hero-btns">
				<a class="elite-btn elite-btn-primary elite-btn-lg" href="<?php echo esc_url( $urls['shop'] ); ?>">SHOP CONTAINERS</a>
				<a class="elite-btn elite-btn-outline elite-btn-lg" href="<?php echo esc_url( $urls['quote'] ); ?>">EXPLORE SOLUTIONS</a>
			</div>
		</div>
		<div class="apex-hero-stats">
			<div class="apex-stat">
				<svg class="apex-stat-ico" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
					<path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z"/>
					<path d="M8.5 12.5l2.5 2.5 5-5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<div><strong>20+</strong><small>Years of Experience</small></div>
			</div>
			<div class="apex-stat">
				<svg class="apex-stat-ico" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
					<rect x="2" y="7" width="20" height="14" rx="1.5"/>
					<path d="M2 12h20"/>
					<path d="M7 7V5"/>
					<path d="M17 7V5"/>
				</svg>
				<div><strong>50K+</strong><small>Containers Delivered</small></div>
			</div>
			<div class="apex-stat">
				<svg class="apex-stat-ico" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
					<circle cx="12" cy="8" r="3.5"/>
					<path d="M5.5 20v-1a6.5 6.5 0 0 1 13 0v1"/>
				</svg>
				<div><strong>100+</strong><small>UK Locations Served</small></div>
			</div>
		</div>
	</div>
	<div class="apex-hero-trust">
		<div class="elite-container">
			<div class="apex-trust-bar">
				<div class="apex-trust-row">
			<div class="apex-trust-item">
				<svg class="apex-trust-ico" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
					<circle cx="12" cy="12" r="9"/>
					<path d="M12 3l6.5 3v5c0 3.8-2.6 7.2-6.5 8-3.9-.8-6.5-4.2-6.5-8V6L12 3z"/>
					<path d="M8.5 12.5l2.5 2.5 5-5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<div class="apex-trust-copy">
					<strong>ISO-Certified Quality</strong>
					<span>Built to international standards</span>
				</div>
			</div>
			<div class="apex-trust-item">
				<svg class="apex-trust-ico" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
					<rect x="2" y="7" width="20" height="14" rx="1.5"/>
					<path d="M8 21V11"/>
					<path d="M16 21V11"/>
					<path d="M2 11h20"/>
					<path d="M12 7V4"/>
					<path d="M8 4h8"/>
				</svg>
				<div class="apex-trust-copy">
					<strong>UK Nationwide Delivery</strong>
					<span>Fast &amp; reliable shipping</span>
				</div>
			</div>
			<div class="apex-trust-item">
				<svg class="apex-trust-ico" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
					<circle cx="8" cy="8" r="3"/>
					<circle cx="16" cy="8" r="3"/>
					<circle cx="12" cy="16" r="3"/>
					<path d="M10.2 9.8l1.8 3.4"/>
					<path d="M13.8 9.8l-1.8 3.4"/>
				</svg>
				<div class="apex-trust-copy">
					<strong>Custom Solutions</strong>
					<span>Tailored to your requirements</span>
				</div>
			</div>
			<div class="apex-trust-item">
				<svg class="apex-trust-ico" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
					<path d="M4 14v3a2 2 0 0 0 2 2h1"/>
					<path d="M20 14v3a2 2 0 0 1-2 2h-1"/>
					<path d="M4 14a8 8 0 0 1 16 0"/>
					<path d="M12 14v3"/>
				</svg>
				<div class="apex-trust-copy">
					<strong>24/7 Support</strong>
					<span>We're here to help</span>
				</div>
			</div>
				</div>
			</div>
		</div>
	</div>
	<div class="apex-hero-slider-nav" aria-label="Hero slideshow controls">
		<button type="button" class="apex-hero-slider-arrow apex-hero-slider-prev" aria-label="Previous slide">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
		</button>
		<div class="apex-hero-slider-dots" role="tablist" aria-label="Choose slide"></div>
		<button type="button" class="apex-hero-slider-arrow apex-hero-slider-next" aria-label="Next slide">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
		</button>
	</div>
</section>

<!-- FEATURED CATEGORIES -->
<?php elite_render_home_top_picks_section(); ?>

<!-- ABOUT -->
<?php elite_render_home_about_section(); ?>

<!-- MODIFICATIONS -->
<?php elite_render_home_mods_section(); ?>

<!-- ADD-ONS -->
<?php elite_render_home_addons_section(); ?>

<!-- POPULAR -->
<?php elite_render_home_popular_section(); ?>

<!-- TRUST BAR -->
<section class="apex-trustbar">
	<div class="elite-container">
		<div class="apex-trustbar-band">
		<div class="apex-trustbar-row">
			<div class="apex-trustbar-item">
				<svg class="apex-trustbar-ico" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
					<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
					<circle class="tb-accent" cx="7" cy="7" r="1.2" fill="currentColor" stroke="none"/>
				</svg>
				<div class="apex-trustbar-copy">
					<strong>Best Prices</strong>
					<span>Competitive pricing guaranteed</span>
				</div>
			</div>
			<div class="apex-trustbar-item">
				<svg class="apex-trustbar-ico" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
					<rect x="1" y="8" width="15" height="10" rx="1"/>
					<path d="M16 11h3l2 3v4h-5"/>
					<circle cx="6" cy="18" r="2"/>
					<circle cx="18" cy="18" r="2"/>
					<path class="tb-accent" d="M1 11h4" stroke-linecap="round"/>
					<path class="tb-accent" d="M0 9h3" stroke-linecap="round"/>
				</svg>
				<div class="apex-trustbar-copy">
					<strong>Fast Delivery</strong>
					<span>On-time delivery worldwide</span>
				</div>
			</div>
			<div class="apex-trustbar-item">
				<svg class="apex-trustbar-ico" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
					<rect x="4" y="10" width="16" height="11" rx="1.5"/>
					<path d="M8 10V7a4 4 0 0 1 8 0v3"/>
					<path class="tb-accent" d="M12 13v3" stroke-linecap="round"/>
					<circle class="tb-accent" cx="12" cy="13" r="1.2" fill="currentColor" stroke="none"/>
				</svg>
				<div class="apex-trustbar-copy">
					<strong>Secure Payment</strong>
					<span>Safe &amp; trusted transactions</span>
				</div>
			</div>
			<div class="apex-trustbar-item">
				<svg class="apex-trustbar-ico" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
					<circle cx="12" cy="12" r="9"/>
					<path d="M4 14v2a2 2 0 0 0 2 2h1"/>
					<path d="M20 14v2a2 2 0 0 1-2 2h-1"/>
					<path d="M4 14a8 8 0 0 1 16 0"/>
					<circle class="tb-accent" cx="12" cy="12" r="2.5" fill="currentColor" stroke="none"/>
				</svg>
				<div class="apex-trustbar-copy">
					<strong>Expert Support</strong>
					<span>24/7 customer assistance</span>
				</div>
			</div>
		</div>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
