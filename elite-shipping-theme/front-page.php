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

$hero_eyebrow     = get_theme_mod( 'elite_hero_eyebrow', 'RELIABLE SHIPPING CONTAINERS FOR EVERY INDUSTRY — DELIVERED ACROSS THE UK' );
$hero_title_white = get_theme_mod( 'elite_hero_title_white', 'PREMIUM SHIPPING CONTAINERS' );
$hero_title_orange = get_theme_mod( 'elite_hero_title_orange', 'BUILT FOR EVERY NEED' );
$hero_desc        = get_theme_mod(
	'elite_hero_desc',
	'Elite Shipping Containers Ltd provides durable, secure, and affordable shipping containers nationwide. Whether you need storage, transport, or a custom-built solution — we deliver quality you can trust.'
);
$hero_btn_primary_text = get_theme_mod( 'elite_hero_btn_primary_text', 'SHOP CONTAINERS' );
$hero_btn_primary_url  = get_theme_mod( 'elite_hero_btn_primary_url', $urls['shop'] );
$hero_btn_secondary_text = get_theme_mod( 'elite_hero_btn_secondary_text', 'EXPLORE SOLUTIONS' );
$hero_btn_secondary_url  = get_theme_mod( 'elite_hero_btn_secondary_url', $urls['quote'] );

$hero_stats = array(
	array(
		'value' => get_theme_mod( 'elite_hero_stat_1_value', '20+' ),
		'label' => get_theme_mod( 'elite_hero_stat_1_label', 'Years of Experience' ),
		'icon'  => 'shield',
	),
	array(
		'value' => get_theme_mod( 'elite_hero_stat_2_value', '50K+' ),
		'label' => get_theme_mod( 'elite_hero_stat_2_label', 'Containers Delivered' ),
		'icon'  => 'box',
	),
	array(
		'value' => get_theme_mod( 'elite_hero_stat_3_value', '100+' ),
		'label' => get_theme_mod( 'elite_hero_stat_3_label', 'UK Locations Served' ),
		'icon'  => 'user',
	),
);

$hero_trust = array(
	array(
		'title' => get_theme_mod( 'elite_hero_trust_1_title', 'ISO-Certified Quality' ),
		'text'  => get_theme_mod( 'elite_hero_trust_1_text', 'Built to international standards' ),
		'icon'  => 'iso',
	),
	array(
		'title' => get_theme_mod( 'elite_hero_trust_2_title', 'UK Nationwide Delivery' ),
		'text'  => get_theme_mod( 'elite_hero_trust_2_text', 'Fast & reliable shipping' ),
		'icon'  => 'delivery',
	),
	array(
		'title' => get_theme_mod( 'elite_hero_trust_3_title', 'Custom Solutions' ),
		'text'  => get_theme_mod( 'elite_hero_trust_3_text', 'Tailored to your requirements' ),
		'icon'  => 'network',
	),
	array(
		'title' => get_theme_mod( 'elite_hero_trust_4_title', '24/7 Support' ),
		'text'  => get_theme_mod( 'elite_hero_trust_4_text', "We're here to help" ),
		'icon'  => 'support',
	),
);
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
<section class="apex-hero" id="elite-home-hero">
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
			<p class="apex-hero-eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
			<h1 class="apex-hero-title">
				<span class="line-white"><?php echo esc_html( $hero_title_white ); ?></span>
				<span class="line-orange"><?php echo esc_html( $hero_title_orange ); ?></span>
			</h1>
			<p class="apex-hero-desc"><?php echo wp_kses_post( $hero_desc ); ?></p>
			<div class="apex-hero-btns">
				<a class="elite-btn elite-btn-primary elite-btn-lg" href="<?php echo esc_url( $hero_btn_primary_url ); ?>"><?php echo esc_html( $hero_btn_primary_text ); ?></a>
				<a class="elite-btn elite-btn-outline elite-btn-lg" href="<?php echo esc_url( $hero_btn_secondary_url ); ?>"><?php echo esc_html( $hero_btn_secondary_text ); ?></a>
			</div>
		</div>
		<div class="apex-hero-stats">
			<?php foreach ( $hero_stats as $stat ) : ?>
				<div class="apex-stat">
					<?php if ( 'shield' === $stat['icon'] ) : ?>
						<svg class="apex-stat-ico" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
							<path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z"/>
							<path d="M8.5 12.5l2.5 2.5 5-5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					<?php elseif ( 'box' === $stat['icon'] ) : ?>
						<svg class="apex-stat-ico" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
							<rect x="2" y="7" width="20" height="14" rx="1.5"/>
							<path d="M2 12h20"/>
							<path d="M7 7V5"/>
							<path d="M17 7V5"/>
						</svg>
					<?php else : ?>
						<svg class="apex-stat-ico" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
							<circle cx="12" cy="8" r="3.5"/>
							<path d="M5.5 20v-1a6.5 6.5 0 0 1 13 0v1"/>
						</svg>
					<?php endif; ?>
					<div>
						<strong><?php echo esc_html( $stat['value'] ); ?></strong>
						<small><?php echo esc_html( $stat['label'] ); ?></small>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="apex-hero-trust">
		<div class="elite-container">
			<div class="apex-trust-bar">
				<div class="apex-trust-row">
					<?php foreach ( $hero_trust as $trust ) : ?>
						<div class="apex-trust-item">
							<?php if ( 'iso' === $trust['icon'] ) : ?>
								<svg class="apex-trust-ico" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
									<circle cx="12" cy="12" r="9"/>
									<path d="M12 3l6.5 3v5c0 3.8-2.6 7.2-6.5 8-3.9-.8-6.5-4.2-6.5-8V6L12 3z"/>
									<path d="M8.5 12.5l2.5 2.5 5-5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							<?php elseif ( 'delivery' === $trust['icon'] ) : ?>
								<svg class="apex-trust-ico" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
									<rect x="2" y="7" width="20" height="14" rx="1.5"/>
									<path d="M8 21V11"/>
									<path d="M16 21V11"/>
									<path d="M2 11h20"/>
									<path d="M12 7V4"/>
									<path d="M8 4h8"/>
								</svg>
							<?php elseif ( 'network' === $trust['icon'] ) : ?>
								<svg class="apex-trust-ico" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
									<circle cx="8" cy="8" r="3"/>
									<circle cx="16" cy="8" r="3"/>
									<circle cx="12" cy="16" r="3"/>
									<path d="M10.2 9.8l1.8 3.4"/>
									<path d="M13.8 9.8l-1.8 3.4"/>
								</svg>
							<?php else : ?>
								<svg class="apex-trust-ico" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
									<path d="M4 14v3a2 2 0 0 0 2 2h1"/>
									<path d="M20 14v3a2 2 0 0 1-2 2h-1"/>
									<path d="M4 14a8 8 0 0 1 16 0"/>
									<path d="M12 14v3"/>
								</svg>
							<?php endif; ?>
							<div class="apex-trust-copy">
								<strong><?php echo esc_html( $trust['title'] ); ?></strong>
								<span><?php echo esc_html( $trust['text'] ); ?></span>
							</div>
						</div>
					<?php endforeach; ?>
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

<?php get_template_part( 'template-parts/footer', 'site' ); ?>
<?php wp_footer(); ?>
</body>
</html>
