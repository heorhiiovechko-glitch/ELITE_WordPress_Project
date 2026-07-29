<?php
/**
 * Elite Shipping Containers theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELITE_SHIPPING_VERSION', '1.9.143' );
define( 'ELITE_SHIPPING_URI', get_template_directory_uri() );
define( 'ELITE_SHIPPING_DIR', get_template_directory() );
define( 'ELITE_COMPANY_NAME', 'Elite Shipping Containers' );
define( 'ELITE_COMPANY_LEGAL_NAME', 'Elite Shipping Containers Ltd' );
define( 'ELITE_SITE_URL', 'https://eliteshippingcontainers.co.uk' );
define( 'ELITE_CONTACT_PHONE', '+44 7462 284270' );
define( 'ELITE_CONTACT_EMAIL', 'sales@eliteshippingcontainers.co.uk' );
define( 'ELITE_CONTACT_ADDRESS', 'Rainham House, Manor Way, Rainham RM13 8RH' );

$elite_product_cards = ELITE_SHIPPING_DIR . '/inc/product-cards.php';
if ( file_exists( $elite_product_cards ) ) {
	require_once $elite_product_cards;
}
$elite_single_summary = ELITE_SHIPPING_DIR . '/inc/single-product-summary.php';
if ( file_exists( $elite_single_summary ) ) {
	require_once $elite_single_summary;
}
$elite_single_product = ELITE_SHIPPING_DIR . '/inc/single-product-hooks.php';
if ( file_exists( $elite_single_product ) ) {
	require_once $elite_single_product;
}
$elite_checkout = ELITE_SHIPPING_DIR . '/inc/checkout.php';
if ( file_exists( $elite_checkout ) ) {
	require_once $elite_checkout;
}
$elite_gateway_bacs = ELITE_SHIPPING_DIR . '/inc/gateway-bacs.php';
if ( file_exists( $elite_gateway_bacs ) ) {
	require_once $elite_gateway_bacs;
}
$elite_payment_gateways = ELITE_SHIPPING_DIR . '/inc/payment-gateways.php';
if ( file_exists( $elite_payment_gateways ) ) {
	require_once $elite_payment_gateways;
}
$elite_cart = ELITE_SHIPPING_DIR . '/inc/cart.php';
if ( file_exists( $elite_cart ) ) {
	require_once $elite_cart;
}
$elite_paypal_express = ELITE_SHIPPING_DIR . '/inc/paypal-express.php';
if ( file_exists( $elite_paypal_express ) ) {
	require_once $elite_paypal_express;
}
$elite_container_checkout = ELITE_SHIPPING_DIR . '/inc/container-checkout.php';
if ( file_exists( $elite_container_checkout ) ) {
	require_once $elite_container_checkout;
}
$elite_live_search = ELITE_SHIPPING_DIR . '/inc/live-search.php';
if ( file_exists( $elite_live_search ) ) {
	require_once $elite_live_search;
}
$elite_quote_drawer = ELITE_SHIPPING_DIR . '/inc/quote-drawer.php';
if ( file_exists( $elite_quote_drawer ) ) {
	require_once $elite_quote_drawer;
}
$elite_wishlist = ELITE_SHIPPING_DIR . '/inc/wishlist.php';
if ( file_exists( $elite_wishlist ) ) {
	require_once $elite_wishlist;
}
$elite_cart_drawer = ELITE_SHIPPING_DIR . '/inc/cart-drawer.php';
if ( file_exists( $elite_cart_drawer ) ) {
	require_once $elite_cart_drawer;
}
$elite_policy_pages = ELITE_SHIPPING_DIR . '/inc/policy-pages.php';
if ( file_exists( $elite_policy_pages ) ) {
	require_once $elite_policy_pages;
}
$elite_faq_page = ELITE_SHIPPING_DIR . '/inc/faq-page.php';
if ( file_exists( $elite_faq_page ) ) {
	require_once $elite_faq_page;
}
$elite_live_chat = ELITE_SHIPPING_DIR . '/inc/live-chat.php';
if ( file_exists( $elite_live_chat ) ) {
	require_once $elite_live_chat;
}
$elite_home_sections = ELITE_SHIPPING_DIR . '/inc/home-sections.php';
if ( file_exists( $elite_home_sections ) ) {
	require_once $elite_home_sections;
}
$elite_customizer_top_picks = ELITE_SHIPPING_DIR . '/inc/customizer-top-picks-control.php';
if ( file_exists( $elite_customizer_top_picks ) ) {
	require_once $elite_customizer_top_picks;
}
$elite_customizer_mods = ELITE_SHIPPING_DIR . '/inc/customizer-mods-control.php';
if ( file_exists( $elite_customizer_mods ) ) {
	require_once $elite_customizer_mods;
}
$elite_customizer_hero_slides = ELITE_SHIPPING_DIR . '/inc/customizer-hero-slides-control.php';
if ( file_exists( $elite_customizer_hero_slides ) ) {
	require_once $elite_customizer_hero_slides;
}
$elite_customizer_blog_cards = ELITE_SHIPPING_DIR . '/inc/customizer-blog-cards-control.php';
if ( file_exists( $elite_customizer_blog_cards ) ) {
	require_once $elite_customizer_blog_cards;
}
$elite_customizer = ELITE_SHIPPING_DIR . '/inc/customizer.php';
if ( file_exists( $elite_customizer ) ) {
	require_once $elite_customizer;
}
$elite_customizer_pages = ELITE_SHIPPING_DIR . '/inc/customizer-pages.php';
if ( file_exists( $elite_customizer_pages ) ) {
	require_once $elite_customizer_pages;
}
if ( ! function_exists( 'elite_render_product_grid' ) ) {
	// Fallback if inc file missing on server — prevents fatal error.
	function elite_render_product_grid( $args = array(), $opts = array() ) {
		echo '<p class="elite-empty">Products will appear here after theme files are fully uploaded.</p>';
	}
	function elite_render_addon_cards() {}
	function elite_render_category_grid() {
		echo '<p class="elite-empty">Categories will appear here after theme files are fully uploaded.</p>';
	}
}

add_action( 'wp_enqueue_scripts', 'elite_shipping_enqueue_assets', 20 );
function elite_shipping_enqueue_assets() {
	wp_enqueue_style(
		'elite-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700;800&display=swap',
		array(),
		null
	);

	$main_css   = ELITE_SHIPPING_DIR . '/assets/css/main.css';
	$main_js    = ELITE_SHIPPING_DIR . '/assets/js/main.js';
	$css_ver    = file_exists( $main_css ) ? (string) filemtime( $main_css ) : ELITE_SHIPPING_VERSION;
	$js_ver     = file_exists( $main_js ) ? (string) filemtime( $main_js ) : ELITE_SHIPPING_VERSION;
	$wc_archive = elite_shipping_is_shop_archive();
	$style_deps = array( 'elite-google-fonts' );

	$is_account = function_exists( 'is_account_page' ) && is_account_page();

	if ( class_exists( 'WooCommerce' ) && ! $wc_archive ) {
		wp_enqueue_style( 'woocommerce-general' );
		// Account page uses a custom grid; WooCommerce layout floats break Login/Register.
		if ( ! $is_account ) {
			wp_enqueue_style( 'woocommerce-layout' );
			$style_deps[] = 'woocommerce-layout';
		}
		wp_enqueue_style( 'woocommerce-smallscreen' );
		$style_deps[] = 'woocommerce-general';
	}

	wp_enqueue_style(
		'elite-shipping-main',
		ELITE_SHIPPING_URI . '/assets/css/main.css',
		$style_deps,
		$css_ver
	);

	if ( $is_account ) {
		wp_add_inline_style( 'elite-shipping-main', elite_shipping_get_account_layout_css() );
	}

	wp_enqueue_script(
		'elite-shipping-main',
		ELITE_SHIPPING_URI . '/assets/js/main.js',
		array(),
		$js_ver,
		true
	);

	wp_localize_script(
		'elite-shipping-main',
		'eliteShippingLiveSearch',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'elite_live_search' ),
			'action'  => 'elite_live_product_search',
			'minChars' => 2,
		)
	);
}

/**
 * Hard override CSS for My Account Login/Register column layout.
 *
 * @return string
 */
function elite_shipping_get_account_layout_css() {
	return <<<'CSS'
body.woocommerce-account #customer_login,
body.elite-account-page #customer_login,
body.woocommerce-account .apex-account-auth,
body.elite-account-page .apex-account-auth{
	display:block!important;
	width:100%!important;
	max-width:460px!important;
	margin:0 auto!important;
	clear:both!important;
}
body.woocommerce-account #customer_login::before,
body.woocommerce-account #customer_login::after,
body.elite-account-page #customer_login::before,
body.elite-account-page #customer_login::after{
	display:none!important;
	content:none!important;
}
body.woocommerce-account #customer_login .col-1,
body.woocommerce-account #customer_login .col-2,
body.woocommerce-account #customer_login .u-column1,
body.woocommerce-account #customer_login .u-column2,
body.elite-account-page #customer_login .col-1,
body.elite-account-page #customer_login .col-2{
	float:none!important;
	clear:none!important;
	width:100%!important;
	max-width:none!important;
	margin:0!important;
}
body.woocommerce-account .apex-account-auth-card,
body.elite-account-page .apex-account-auth-card{
	background:#fff;
	border:1px solid #dde3ec;
	border-radius:14px;
	padding:28px 26px;
	box-shadow:0 12px 30px rgba(15,23,42,.06);
}
body.woocommerce-account .apex-account-auth-toggle,
body.elite-account-page .apex-account-auth-toggle{
	display:grid;
	grid-template-columns:1fr 1fr;
	gap:6px;
	margin:0 0 22px;
	padding:6px;
	border-radius:10px;
	background:#eef2f7;
}
body.woocommerce-account .apex-account-auth-toggle-btn,
body.elite-account-page .apex-account-auth-toggle-btn{
	appearance:none;
	border:0;
	border-radius:8px;
	background:transparent;
	color:#334155;
	font:inherit;
	font-size:14px;
	font-weight:750;
	letter-spacing:.04em;
	text-transform:uppercase;
	padding:12px 10px;
	cursor:pointer;
	transition:background .18s,color .18s,box-shadow .18s;
}
body.woocommerce-account .apex-account-auth-toggle-btn.is-active,
body.elite-account-page .apex-account-auth-toggle-btn.is-active{
	background:#001529;
	color:#fff;
	box-shadow:0 6px 14px rgba(0,21,41,.18);
}
body.woocommerce-account .apex-account-auth-panel[hidden],
body.elite-account-page .apex-account-auth-panel[hidden]{
	display:none!important;
}
body.woocommerce-account #customer_login .woocommerce-form-login,
body.woocommerce-account #customer_login .woocommerce-form-register,
body.elite-account-page #customer_login .woocommerce-form-login,
body.elite-account-page #customer_login .woocommerce-form-register{
	background:transparent!important;
	border:0!important;
	box-shadow:none!important;
	padding:0!important;
}
body.woocommerce-account #customer_login .lost_password a,
body.elite-account-page #customer_login .lost_password a{
	color:#ff6600;
	font-weight:650;
	text-decoration:none;
}
body.woocommerce-account .woocommerce form .password-input,
body.elite-account-page .woocommerce form .password-input{
	display:block!important;
	position:relative!important;
}
body.woocommerce-account .woocommerce form .password-input input,
body.elite-account-page .woocommerce form .password-input input{
	padding-right:48px!important;
	width:100%!important;
}
body.woocommerce-account .woocommerce form .show-password-input,
body.elite-account-page .woocommerce form .show-password-input,
body.woocommerce-account button.show-password-input,
body.elite-account-page button.show-password-input{
	position:absolute!important;
	top:50%!important;
	right:10px!important;
	left:auto!important;
	transform:translateY(-50%);
	width:34px!important;
	height:34px!important;
	margin:0!important;
	padding:0!important;
	border:0!important;
	border-radius:8px;
	background-color:transparent!important;
	background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' viewBox='0 0 24 24'%3E%3Cpath d='M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z'/%3E%3Ccircle cx='12' cy='12' r='3'/%3E%3C/svg%3E")!important;
	background-repeat:no-repeat!important;
	background-position:center!important;
	background-size:20px 20px!important;
	box-shadow:none!important;
	cursor:pointer;
	color:transparent!important;
	font-size:0!important;
	line-height:0!important;
	text-indent:-9999px;
	overflow:hidden;
	z-index:2;
}
body.woocommerce-account .woocommerce form .show-password-input::before,
body.woocommerce-account .woocommerce form .show-password-input::after,
body.elite-account-page .woocommerce form .show-password-input::before,
body.elite-account-page .woocommerce form .show-password-input::after{
	display:none!important;
	content:none!important;
}
body.woocommerce-account .woocommerce form .show-password-input.display-password,
body.elite-account-page .woocommerce form .show-password-input.display-password{
	background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%23ff6600' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' viewBox='0 0 24 24'%3E%3Cpath d='M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.8 21.8 0 0 1 5.06-5.94'/%3E%3Cpath d='M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 7 11 7a21.8 21.8 0 0 1-2.16 3.19'/%3E%3Cpath d='M14.12 14.12a3 3 0 0 1-4.24-4.24'/%3E%3Cpath d='M1 1l22 22'/%3E%3C/svg%3E")!important;
}
CSS;
}

/**
 * Whether the current request is a WooCommerce shop or taxonomy archive.
 *
 * @return bool
 */
function elite_shipping_is_shop_archive() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	return is_shop() || is_product_category() || is_product_tag();
}

add_action( 'after_setup_theme', 'elite_shipping_setup' );
function elite_shipping_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'woocommerce' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'elite-shipping' ),
		)
	);
}

add_filter( 'body_class', 'elite_shipping_body_class' );
function elite_shipping_body_class( $classes ) {
	$classes[] = 'elite-shipping-theme';
	return $classes;
}

/**
 * Always render the custom homepage template on the front page.
 * Prevents imported page shortcodes/Elementor from overriding front-page.php.
 */
add_filter( 'template_include', 'elite_shipping_force_theme_templates', 9999 );
function elite_shipping_force_theme_templates( $template ) {
	if ( is_front_page() ) {
		$front_page = ELITE_SHIPPING_DIR . '/front-page.php';
		if ( file_exists( $front_page ) ) {
			return $front_page;
		}
	}

	if ( is_page( 'about-us' ) ) {
		$about_page = ELITE_SHIPPING_DIR . '/page-about-us.php';
		if ( file_exists( $about_page ) ) {
			return $about_page;
		}
	}

	if ( is_page( array( 'contact-us', 'contact' ) ) ) {
		$contact_page = ELITE_SHIPPING_DIR . '/page-contact-us.php';
		if ( file_exists( $contact_page ) ) {
			return $contact_page;
		}
	}

	if ( is_page( array( 'our-blog', 'blog' ) ) ) {
		$blog_page = ELITE_SHIPPING_DIR . '/page-our-blog.php';
		if ( file_exists( $blog_page ) ) {
			return $blog_page;
		}
	}

	if ( function_exists( 'elite_shipping_get_policy_page_slugs' ) && is_page( elite_shipping_get_policy_page_slugs() ) ) {
		$policy_page = ELITE_SHIPPING_DIR . '/page-policy.php';
		if ( file_exists( $policy_page ) ) {
			return $policy_page;
		}
	}

	if ( is_single() && 'post' === get_post_type() ) {
		$single_post = ELITE_SHIPPING_DIR . '/single.php';
		if ( file_exists( $single_post ) ) {
			return $single_post;
		}
	}

	if ( is_product_category() ) {
		$category_archive = ELITE_SHIPPING_DIR . '/taxonomy-product_cat.php';
		if ( file_exists( $category_archive ) ) {
			return $category_archive;
		}
	}

	if ( function_exists( 'is_shop' ) && is_shop() ) {
		$shop_archive = ELITE_SHIPPING_DIR . '/woocommerce/archive-product.php';
		if ( file_exists( $shop_archive ) ) {
			return $shop_archive;
		}
	}

	if ( is_singular( 'product' ) ) {
		$single_product = ELITE_SHIPPING_DIR . '/woocommerce/single-product.php';
		if ( file_exists( $single_product ) ) {
			return $single_product;
		}
	}

	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		$checkout_page = ELITE_SHIPPING_DIR . '/woocommerce/checkout-page.php';
		if ( file_exists( $checkout_page ) ) {
			return $checkout_page;
		}
	}

	if ( function_exists( 'is_cart' ) && is_cart() ) {
		$cart_page = ELITE_SHIPPING_DIR . '/woocommerce/cart-page.php';
		if ( file_exists( $cart_page ) ) {
			return $cart_page;
		}
	}

	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		$account_page = ELITE_SHIPPING_DIR . '/woocommerce/my-account-page.php';
		if ( file_exists( $account_page ) ) {
			return $account_page;
		}
	}

	return $template;
}

/** WooCommerce wrappers — only hook after WooCommerce loads. */
add_action( 'after_setup_theme', 'elite_shipping_wc_hooks', 20 );
function elite_shipping_wc_hooks() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_before_main_content', 'elite_shipping_wc_wrapper_start', 10 );
	add_action( 'woocommerce_after_main_content', 'elite_shipping_wc_wrapper_end', 10 );
	add_filter( 'loop_shop_columns', 'elite_shipping_loop_columns' );
	add_filter( 'loop_shop_per_page', 'elite_shipping_loop_per_page', 20 );
	add_filter( 'woocommerce_enqueue_styles', 'elite_shipping_disable_wc_styles_on_archives' );
}

/**
 * Do not load WooCommerce CSS on custom shop/category archives.
 *
 * @param array<string, string> $styles Registered WooCommerce stylesheets.
 * @return array<string, string>
 */
function elite_shipping_disable_wc_styles_on_archives( $styles ) {
	if ( elite_shipping_is_shop_archive() ) {
		return array();
	}

	return $styles;
}

/**
 * Block WooCommerce general/layout CSS on custom shop archives.
 * Removes: .woocommerce img, .woocommerce-page img { height: auto; max-width: 100%; }
 */
add_action( 'wp_enqueue_scripts', 'elite_shipping_dequeue_wc_archive_styles', 100 );
function elite_shipping_dequeue_wc_archive_styles() {
	if ( elite_shipping_is_shop_archive() ) {
		wp_dequeue_style( 'woocommerce-general' );
		wp_dequeue_style( 'woocommerce-layout' );
		wp_dequeue_style( 'woocommerce-smallscreen' );
		return;
	}

	// Prevent WooCommerce float columns from breaking account Login/Register.
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		wp_dequeue_style( 'woocommerce-layout' );
		wp_deregister_style( 'woocommerce-layout' );
	}
}

/**
 * Products per page on shop/category archives.
 *
 * @param int $per_page Default per page.
 * @return int
 */
function elite_shipping_loop_per_page( $per_page ) {
	if ( is_admin() || ! ( is_shop() || is_product_category() || is_product_tag() ) ) {
		return $per_page;
	}

	$allowed = array( 12, 24, 36, 48 );
	if ( isset( $_GET['per_page'] ) ) {
		$requested = absint( wp_unslash( $_GET['per_page'] ) );
		if ( in_array( $requested, $allowed, true ) ) {
			return $requested;
		}
	}

	return 12;
}

function elite_shipping_wc_wrapper_start() {
	echo '<main class="elite-wc-main"><div class="elite-container">';
}

function elite_shipping_wc_wrapper_end() {
	echo '</div></main>';
}

function elite_shipping_loop_columns() {
	return 4;
}

function elite_shipping_get_page_url( $slug, $fallback_path ) {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return get_permalink( $page );
	}
	return home_url( $fallback_path );
}

/**
 * Swap old-site media host to the current WordPress site URL.
 *
 * @param string $url Media URL or path from the old server export.
 * @return string
 */
function elite_shipping_migrate_media_url( $url ) {
	$site = home_url();

	return str_replace(
		array(
			'https://www.firstchoiceshippingcontainers.com',
			'http://www.firstchoiceshippingcontainers.com',
			'https://firstchoiceshippingcontainers.com',
			'http://firstchoiceshippingcontainers.com',
		),
		$site,
		$url
	);
}

function elite_shipping_get_urls() {
	return array(
		'shop'    => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
		'quote'   => elite_shipping_get_page_url( 'get-a-quote', '/get-a-quote/' ),
		'about'   => elite_shipping_get_page_url( 'about-us', '/about-us/' ),
		'contact' => elite_shipping_get_page_url( 'contact-us', '/contact-us/' ),
		'blog'    => elite_shipping_get_page_url( 'our-blog', '/our-blog/' ),
		'faq'     => elite_shipping_get_page_url( 'faq', '/faq/' ),
		'wishlist'=> elite_shipping_get_page_url( 'wishlist', '/wishlist/' ),
		'account' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' ),
		'policies' => function_exists( 'elite_shipping_get_policy_urls' ) ? elite_shipping_get_policy_urls() : array(),
	);
}

/**
 * Core marketing pages required by theme templates and nav links.
 *
 * @return array<string, string> slug => title
 */
function elite_shipping_get_core_pages_config() {
	return array(
		'about-us'    => __( 'About Us', 'elite-shipping' ),
		'contact-us'  => __( 'Contact Us', 'elite-shipping' ),
		'our-blog'    => __( 'Our Blog', 'elite-shipping' ),
		'get-a-quote' => __( 'Get a Quote', 'elite-shipping' ),
		'faq'         => __( 'FAQ', 'elite-shipping' ),
		'wishlist'    => __( 'Wishlist', 'elite-shipping' ),
	);
}

/**
 * Create core pages (About, Contact, Blog, Quote) if missing.
 */
function elite_shipping_ensure_core_pages() {
	if ( ! function_exists( 'wp_insert_post' ) ) {
		return;
	}

	$created = false;

	foreach ( elite_shipping_get_core_pages_config() as $slug => $title ) {
		$existing = get_page_by_path( $slug );
		if ( $existing instanceof WP_Post ) {
			continue;
		}

		$result = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);

		if ( ! is_wp_error( $result ) && $result ) {
			$created = true;
		}
	}

	if ( $created ) {
		flush_rewrite_rules( false );
	}
}
add_action( 'after_setup_theme', 'elite_shipping_ensure_core_pages', 25 );

/**
 * WooCommerce store pages (Cart, Checkout, My account, Shop).
 *
 * @return array<string, array{title:string,slug:string,option:string,content:string}>
 */
function elite_shipping_get_woocommerce_pages_config() {
	return array(
		'cart'      => array(
			'title'   => __( 'Cart', 'elite-shipping' ),
			'slug'    => 'cart',
			'option'  => 'woocommerce_cart_page_id',
			'content' => '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->',
		),
		'checkout'  => array(
			'title'   => __( 'Checkout', 'elite-shipping' ),
			'slug'    => 'checkout',
			'option'  => 'woocommerce_checkout_page_id',
			'content' => '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->',
		),
		'myaccount' => array(
			'title'   => __( 'My account', 'elite-shipping' ),
			'slug'    => 'my-account',
			'option'  => 'woocommerce_myaccount_page_id',
			'content' => '<!-- wp:shortcode -->[woocommerce_my_account]<!-- /wp:shortcode -->',
		),
		'shop'      => array(
			'title'   => __( 'Shop', 'elite-shipping' ),
			'slug'    => 'shop',
			'option'  => 'woocommerce_shop_page_id',
			'content' => '',
		),
	);
}

/**
 * Whether a page already contains the expected WooCommerce shortcode/block.
 *
 * @param string $content Page content.
 * @param string $key     cart|checkout|myaccount|shop.
 * @return bool
 */
function elite_shipping_page_has_woocommerce_content( $content, $key ) {
	$content = (string) $content;
	if ( 'shop' === $key ) {
		return true;
	}

	$needles = array(
		'cart'      => array( '[woocommerce_cart]', 'woocommerce/cart' ),
		'checkout'  => array( '[woocommerce_checkout]', 'woocommerce/checkout' ),
		'myaccount' => array( '[woocommerce_my_account]', 'woocommerce/my-account' ),
	);

	if ( empty( $needles[ $key ] ) ) {
		return true;
	}

	foreach ( $needles[ $key ] as $needle ) {
		if ( false !== strpos( $content, $needle ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Create and assign WooCommerce pages if missing (common after migration).
 */
function elite_shipping_ensure_woocommerce_pages() {
	if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wp_insert_post' ) ) {
		return;
	}

	$changed = false;

	foreach ( elite_shipping_get_woocommerce_pages_config() as $key => $config ) {
		$page_id = absint( get_option( $config['option'], 0 ) );
		$page    = $page_id ? get_post( $page_id ) : null;

		if ( ! ( $page instanceof WP_Post ) || 'page' !== $page->post_type || 'publish' !== $page->post_status ) {
			$by_slug = get_page_by_path( $config['slug'] );
			if ( $by_slug instanceof WP_Post && 'publish' === $by_slug->post_status ) {
				$page    = $by_slug;
				$page_id = (int) $by_slug->ID;
				update_option( $config['option'], $page_id );
				$changed = true;
			} else {
				$page_id = wp_insert_post(
					array(
						'post_title'   => $config['title'],
						'post_name'    => $config['slug'],
						'post_status'  => 'publish',
						'post_type'    => 'page',
						'post_content' => $config['content'],
					),
					true
				);

				if ( is_wp_error( $page_id ) || ! $page_id ) {
					continue;
				}

				update_option( $config['option'], (int) $page_id );
				$changed = true;
				$page    = get_post( $page_id );
			}
		}

		if ( $page instanceof WP_Post && '' !== $config['content'] && ! elite_shipping_page_has_woocommerce_content( $page->post_content, $key ) ) {
			// Only fill empty pages so we do not overwrite custom checkout layouts.
			if ( '' === trim( (string) $page->post_content ) ) {
				wp_update_post(
					array(
						'ID'           => $page->ID,
						'post_content' => $config['content'],
					)
				);
				$changed = true;
			}
		}
	}

	if ( $changed ) {
		if ( class_exists( 'WC_Cache_Helper' ) && method_exists( 'WC_Cache_Helper', 'invalidate_cache_group' ) ) {
			WC_Cache_Helper::invalidate_cache_group( 'shipping' );
		}
		delete_transient( 'woocommerce_cache_excluded_uris' );
		flush_rewrite_rules( false );
	}
}
add_action( 'init', 'elite_shipping_ensure_woocommerce_pages', 20 );

/**
 * Map blog post slugs to image filenames in assets/images/blog or uploads.
 *
 * @return array<string, string>
 */
function elite_shipping_get_blog_image_map() {
	return array(
		'everything-you-need-to-know-about-high-cube-vs-standard-containers' => 'blog_1.webp',
		'the-growing-market-of-shipping-container-homes-in-the-us'           => 'blog_2.webp',
		'why-small-businesses-are-turning-to-shipping-containers-in-2025'    => 'blog_3.webp',
		'shipping-container-price-comparison-whats-trending-in-the-market'   => 'blog_4.webp',
		'5-things-to-know-before-buying-a-shipping-container'               => 'blog_5.webp',
		'20ft-or-40ft-container-choosing-the-best-fit-for-your-needs'       => 'blog_6.webp',
		'new-vs-used-shipping-containers-which-one-is-right-for-you'         => 'blog_7.jpg',
	);
}

/**
 * Resolve a blog image from theme assets/images/blog.
 *
 * @param string $filename Image filename.
 * @return string
 */
function elite_shipping_get_blog_image_url( $filename ) {
	$filename = ltrim( (string) $filename, '/' );

	return ELITE_SHIPPING_URI . '/assets/images/blog/' . $filename;
}

/**
 * Blog image filenames used as fallbacks.
 *
 * @return string[]
 */
function elite_shipping_get_blog_fallback_images() {
	return array(
		'blog_1.webp',
		'blog_2.webp',
		'blog_3.webp',
		'blog_4.webp',
		'blog_5.webp',
		'blog_6.webp',
		'blog_7.jpg',
	);
}

/**
 * Resolve the featured image for a blog post from assets/images/blog.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function elite_shipping_get_blog_post_image( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$map     = elite_shipping_get_blog_image_map();

	if ( $post_id ) {
		$slug = get_post_field( 'post_name', $post_id );
		if ( $slug && isset( $map[ $slug ] ) ) {
			return elite_shipping_get_blog_image_url( $map[ $slug ] );
		}
	}

	$fallbacks = array_values( $map );
	if ( empty( $fallbacks ) ) {
		$fallbacks = elite_shipping_get_blog_fallback_images();
	}

	$index = $post_id ? ( $post_id % count( $fallbacks ) ) : 0;

	return elite_shipping_get_blog_image_url( $fallbacks[ $index ] );
}

/**
 * Current primary nav item for active styling.
 *
 * @return string
 */
function elite_shipping_get_active_nav() {
	if ( is_front_page() ) {
		return 'home';
	}

	if ( is_page( array( 'about-us', 'about' ) ) ) {
		return 'about';
	}

	if ( is_page( array( 'contact-us', 'contact' ) ) ) {
		return 'contact';
	}

	if ( is_page( array( 'our-blog', 'blog' ) ) || ( is_single() && 'post' === get_post_type() ) ) {
		return 'blog';
	}

	if ( is_product_category() || is_product_tag() ) {
		return 'containers';
	}

	if ( function_exists( 'is_shop' ) && is_shop() ) {
		return 'products';
	}

	if ( is_singular( 'product' ) ) {
		return 'products';
	}

	return '';
}

/**
 * Build nav link/button classes with optional active state.
 *
 * @param string $item Nav item key.
 * @param string $extra Extra classes.
 * @return string
 */
function elite_shipping_nav_class( $item, $extra = '' ) {
	$classes = trim( $extra );

	if ( elite_shipping_get_active_nav() === $item ) {
		$classes .= ' is-active';
	}

	return trim( $classes );
}

/**
 * Hero background for shop/category archives.
 *
 * @return string
 */
function elite_shipping_get_shop_hero_image() {
	$slides = elite_shipping_get_hero_slides();

	if ( ! empty( $slides[0] ) ) {
		return $slides[0];
	}

	return elite_shipping_get_blog_image_url( 'blog_1.webp' );
}

/**
 * Replace legacy inline blog images with theme blog assets.
 *
 * @param string $content Post content.
 * @return string
 */
function elite_shipping_replace_blog_content_images( $content ) {
	if ( ! is_singular( 'post' ) || ! is_string( $content ) || '' === $content ) {
		return $content;
	}

	$content   = elite_shipping_migrate_media_url( $content );
	$post_image = elite_shipping_get_blog_post_image();

	return preg_replace_callback(
		'/<img\b([^>]*\bsrc=["\'])([^"\']+)(["\'][^>]*)>/i',
		static function ( $matches ) use ( $post_image ) {
			$src = $matches[2];
			if ( false !== strpos( $src, 'firstchoiceshippingcontainers.com' ) || false !== strpos( $src, '/wp-content/uploads/' ) ) {
				return '<img' . $matches[1] . esc_url( $post_image ) . $matches[3] . '>';
			}

			return $matches[0];
		},
		$content
	);
}
add_filter( 'the_content', 'elite_shipping_replace_blog_content_images', 25 );

/**
 * Resolve an About page image from theme assets/images/about.
 *
 * @param string $filename Image filename.
 * @return string
 */
function elite_shipping_get_about_image_url( $filename ) {
	$filename = ltrim( (string) $filename, '/' );

	return ELITE_SHIPPING_URI . '/assets/images/about/' . $filename;
}

/**
 * Legacy placeholder — blog listings use real WordPress posts only.
 *
 * @return array<int, array<string, string>>
 */
function elite_shipping_get_default_blog_posts() {
	return array();
}

/**
 * Post statuses used for front-end blog listings and single views.
 * Includes scheduled ("future") posts so intentionally dated posts still appear.
 *
 * @return string[]
 */
function elite_shipping_get_blog_post_statuses() {
	return array( 'publish', 'future' );
}

/**
 * Blog posts for the Our Blog page (WordPress posts only).
 *
 * @param int $limit Number of posts.
 * @return array<int, array<string, string>>
 */
function elite_shipping_get_blog_posts( $limit = 12 ) {
	$limit = max( 1, absint( $limit ) );

	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => $limit,
			'post_status'         => elite_shipping_get_blog_post_statuses(),
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	$posts = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$thumb = elite_shipping_get_blog_post_image( get_the_ID() );

			$posts[] = array(
				'title'    => get_the_title(),
				'url'      => get_permalink(),
				'image'    => $thumb,
				'excerpt'  => wp_trim_words( get_the_excerpt(), 28, '...' ),
				'date'     => get_the_date( 'F j, Y' ),
				'datetime' => get_the_date( 'Y-m-d' ),
				'day'      => get_the_date( 'j' ),
				'month'    => get_the_date( 'M' ),
				'author'   => get_the_author(),
			);
		}
		wp_reset_postdata();
	}

	if ( empty( $posts ) ) {
		return array();
	}

	return $posts;
}

/**
 * Allow scheduled (future) blog posts to resolve on the front end.
 *
 * @param WP_Query $query Main query.
 */
function elite_shipping_include_future_posts_on_front( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_single() || $query->is_singular( 'post' ) ) {
		$query->set( 'post_status', elite_shipping_get_blog_post_statuses() );
	}
}
add_action( 'pre_get_posts', 'elite_shipping_include_future_posts_on_front' );

/**
 * Include scheduled posts in previous/next post navigation.
 *
 * @param string $where Adjacent post WHERE clause.
 * @return string
 */
function elite_shipping_adjacent_post_where_include_future( $where ) {
	return str_replace(
		"p.post_status = 'publish'",
		"p.post_status IN ('publish','future')",
		(string) $where
	);
}
add_filter( 'get_previous_post_where', 'elite_shipping_adjacent_post_where_include_future' );
add_filter( 'get_next_post_where', 'elite_shipping_adjacent_post_where_include_future' );

/**
 * Social share links for a blog post.
/**
 * Social share links for a post or custom URL.
 *
 * @param int|array $post_id   Post ID, or override array with url/title/image.
 * @param array     $overrides Optional overrides: url, title, image.
 * @return array<int, array<string, string>>
 */
function elite_shipping_get_post_share_links( $post_id = 0, $overrides = array() ) {
	if ( is_array( $post_id ) ) {
		$overrides = $post_id;
		$post_id   = 0;
	}

	$post_id = absint( $post_id );
	$page_url = ! empty( $overrides['url'] ) ? (string) $overrides['url'] : ( $post_id ? get_permalink( $post_id ) : home_url( '/' ) );
	$title_raw = ! empty( $overrides['title'] ) ? (string) $overrides['title'] : ( $post_id ? get_the_title( $post_id ) : get_bloginfo( 'name' ) );
	$thumb     = ! empty( $overrides['image'] ) ? (string) $overrides['image'] : ( $post_id ? (string) get_the_post_thumbnail_url( $post_id, 'large' ) : '' );

	$url   = rawurlencode( $page_url );
	$title = rawurlencode( $title_raw );
	$media = $thumb ? rawurlencode( $thumb ) : '';

	return array(
		array(
			'id'    => 'facebook',
			'label' => 'Facebook',
			'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
			'icon'  => '<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 6.019 4.388 11.013 10.125 11.878v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
		),
		array(
			'id'    => 'x',
			'label' => 'X',
			'url'   => 'https://x.com/share?url=' . $url,
			'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.45-6.231zm-1.161 17.52h1.833L7.084 4.126H5.117l11.966 15.644z"/></svg>',
		),
		array(
			'id'    => 'pinterest',
			'label' => 'Pinterest',
			'url'   => 'https://pinterest.com/pin/create/button/?url=' . $url . '&media=' . $media . '&description=' . $title,
			'icon'  => '<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>',
		),
		array(
			'id'    => 'linkedin',
			'label' => 'LinkedIn',
			'url'   => 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url,
			'icon'  => '<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
		),
		array(
			'id'    => 'telegram',
			'label' => 'Telegram',
			'url'   => 'https://telegram.me/share/url?url=' . $url,
			'icon'  => '<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0h-.056zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',
		),
	);
}

add_filter( 'comment_form_defaults', 'elite_shipping_comment_form_defaults' );
function elite_shipping_comment_form_defaults( $defaults ) {
	$defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . __( 'Comment', 'elite-shipping' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea></p>';

	return $defaults;
}

/**
 * Recent blog posts for the single post sidebar.
 *
 * @param int $exclude_id Post ID to exclude.
 * @param int $limit        Number of posts.
 * @return array<int, array<string, mixed>>
 */
function elite_shipping_get_recent_blog_posts( $exclude_id = 0, $limit = 3 ) {
	$limit = max( 1, absint( $limit ) );
	$posts = array();

	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => $limit,
			'post_status'         => elite_shipping_get_blog_post_statuses(),
			'post__not_in'        => ( is_numeric( $exclude_id ) && $exclude_id ) ? array( absint( $exclude_id ) ) : array(),
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$thumb = elite_shipping_get_blog_post_image( get_the_ID() );

			$posts[] = array(
				'title'    => get_the_title(),
				'url'      => get_permalink(),
				'image'    => $thumb,
				'date'     => get_the_date( 'F j, Y' ),
				'comments' => get_comments_number(),
			);
		}
		wp_reset_postdata();
	}

	return $posts;
}

/**
 * Business identity and contact details used across the theme.
 *
 * @return array{
 *   company_name: string,
 *   company_legal_name: string,
 *   website: string,
 *   website_url: string,
 *   phone: string,
 *   phone_href: string,
 *   whatsapp_href: string,
 *   email: string,
 *   address: string,
 *   address_url: string,
 *   map_embed: string
 * }
 */
function elite_shipping_get_contact_details() {
	$address = get_theme_mod( 'elite_contact_address', ELITE_CONTACT_ADDRESS );
	$phone   = get_theme_mod( 'elite_contact_phone', ELITE_CONTACT_PHONE );
	$email   = get_theme_mod( 'elite_contact_email', ELITE_CONTACT_EMAIL );
	$website = get_theme_mod( 'elite_contact_website', 'eliteshippingcontainers.co.uk' );
	$web_url = get_theme_mod( 'elite_contact_website_url', ELITE_SITE_URL );
	$company = get_theme_mod( 'elite_contact_company_name', ELITE_COMPANY_NAME );

	$map_query = rawurlencode( $address );
	$digits    = preg_replace( '/\D+/', '', $phone );
	$map_embed = get_theme_mod( 'elite_contact_map_embed', '' );
	if ( ! $map_embed ) {
		$map_embed = 'https://maps.google.com/maps?q=' . $map_query . '&t=m&z=14&output=embed&iwloc=near';
	}

	return array(
		'company_name'       => $company,
		'company_legal_name' => ELITE_COMPANY_LEGAL_NAME,
		'website'            => $website,
		'website_url'        => $web_url,
		'phone'              => $phone,
		'phone_href'         => 'tel:+' . ltrim( (string) $digits, '+' ),
		'whatsapp_href'      => elite_shipping_get_whatsapp_href( $phone ),
		'email'              => $email,
		'address'            => $address,
		'address_url'        => 'https://www.google.com/maps/search/?api=1&query=' . $map_query,
		'map_embed'          => $map_embed,
	);
}

/**
 * Default Contact page intro copy.
 *
 * @return string
 */
function elite_shipping_get_contact_info_intro_default() {
	return __( 'Contact %s for container quotes, delivery questions, modifications, and order support across the UK. You can also speak with us anytime using the live chat widget on this page.', 'elite-shipping' );
}

/**
 * Format Contact page intro, replacing %s and stripping accidental theme-path URLs.
 *
 * @param string $intro   Raw intro from theme_mod.
 * @param string $company Company display name.
 * @return string
 */
function elite_shipping_format_contact_info_intro( $intro, $company = '' ) {
	$company = trim( (string) $company );
	if ( '' === $company ) {
		$company = ELITE_COMPANY_NAME;
	}

	$intro = trim( (string) $intro );
	if ( '' === $intro ) {
		$intro = elite_shipping_get_contact_info_intro_default();
	}

	// Remove accidental theme directory URLs / paths from saved Customizer text.
	$intro = preg_replace(
		'#https?://[^\s]+/wp-content/themes/[^\s]+#i',
		$company,
		$intro
	);
	$intro = preg_replace(
		'#/wp-content/themes/[^\s]+#i',
		$company,
		$intro
	);

	if ( false !== strpos( $intro, '%s' ) ) {
		$intro = sprintf( $intro, $company );
	}

	// Collapse leftover double spaces after replacements.
	$intro = preg_replace( '/\s{2,}/', ' ', $intro );

	return trim( (string) $intro );
}

/**
 * Repair a corrupted Contact intro theme_mod once (theme path saved as company).
 */
function elite_shipping_repair_contact_info_intro_theme_mod() {
	$stored = get_theme_mod( 'elite_contact_info_intro', null );
	if ( null === $stored || '' === $stored ) {
		return;
	}

	if ( false === stripos( (string) $stored, '/wp-content/themes/' ) ) {
		return;
	}

	$company = get_theme_mod( 'elite_contact_company_name', ELITE_COMPANY_NAME );
	$fixed   = elite_shipping_format_contact_info_intro( $stored, $company );
	set_theme_mod( 'elite_contact_info_intro', $fixed );
}
add_action( 'init', 'elite_shipping_repair_contact_info_intro_theme_mod', 30 );

/**
 * Build a WhatsApp chat link from a phone number.
 *
 * @param string $phone   E.164-style phone number.
 * @param string $message Optional pre-filled message.
 * @return string
 */
function elite_shipping_get_whatsapp_href( $phone = '', $message = '' ) {
	if ( '' === $phone ) {
		$phone = ELITE_CONTACT_PHONE;
	}

	$digits = preg_replace( '/\D+/', '', $phone );
	$url    = 'https://wa.me/' . $digits;

	if ( '' === $message ) {
		$message = __( 'Hi, I would like to enquire about a shipping container.', 'elite-shipping' );
	}

	if ( '' !== trim( (string) $message ) ) {
		$url .= '?text=' . rawurlencode( $message );
	}

	return $url;
}

/**
 * Container type options for the contact form (WooCommerce product categories).
 *
 * @return string[]
 */
function elite_shipping_get_contact_container_types() {
	$types = array();

	if ( taxonomy_exists( 'product_cat' ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( ! $term instanceof WP_Term ) {
					continue;
				}
				// Skip WooCommerce default uncategorized bucket.
				if ( 'uncategorized' === $term->slug ) {
					continue;
				}
				$name = trim( elite_shipping_decode_term_name( $term->name ) );
				if ( '' !== $name ) {
					$types[] = $name;
				}
			}
		}
	}

	if ( empty( $types ) ) {
		$types = array(
			'20ft Shipping Container',
			'40ft High Cube Container',
			'Office / Workshop Container',
			'Used Container',
			'Refrigerated Container',
			'Custom Modified Unit',
		);
	}

	$types[] = __( 'Other / Not sure', 'elite-shipping' );

	return array_values( array_unique( $types ) );
}

/**
 * Fill Contact Form 7 "Container Type" selects with product categories.
 *
 * @param array $tag CF7 form-tag.
 * @return array
 */
function elite_shipping_cf7_fill_container_categories( $tag ) {
	if ( ! is_array( $tag ) ) {
		return $tag;
	}

	$type = isset( $tag['type'] ) ? (string) $tag['type'] : '';
	$name = isset( $tag['name'] ) ? (string) $tag['name'] : '';

	if ( 0 !== strpos( $type, 'select' ) ) {
		return $tag;
	}

	$known_names = array(
		'container',
		'container-type',
		'container_type',
		'your-container',
		'menu-container',
		'container-type*',
	);

	if ( ! in_array( $name, $known_names, true ) && false === stripos( $name, 'container' ) ) {
		return $tag;
	}

	$types = elite_shipping_get_contact_container_types();
	if ( empty( $types ) ) {
		return $tag;
	}

	$tag['raw_values'] = $types;
	$tag['values']     = $types;
	$tag['labels']     = $types;

	return $tag;
}
add_filter( 'wpcf7_form_tag', 'elite_shipping_cf7_fill_container_categories', 20 );

/**
 * Output the Contact Us page form.
 */
function elite_shipping_render_contact_form() {
	$contact = elite_shipping_get_contact_details();
	$status  = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';

	if ( 'sent' === $status ) {
		echo '<p class="apex-contact-form-notice apex-contact-form-notice--success">' . esc_html__( 'Thank you — your message has been sent. We will get back to you shortly.', 'elite-shipping' ) . '</p>';
	} elseif ( 'error' === $status ) {
		echo '<p class="apex-contact-form-notice apex-contact-form-notice--error">' . esc_html__( 'Sorry, something went wrong. Please try again or email us directly.', 'elite-shipping' ) . '</p>';
	}

	if ( function_exists( 'wpcf7_contact_form' ) ) {
		$shortcodes = array(
			'[contact-form-7 id="1572" html_class="apex-cf7-form"]',
			'[contact-form-7 title="Contact form" html_class="apex-cf7-form"]',
			'[contact-form-7 title="Contact Form" html_class="apex-cf7-form"]',
		);

		foreach ( $shortcodes as $shortcode ) {
			$html = do_shortcode( $shortcode );
			if ( ! empty( $html ) && false === stripos( $html, 'not found' ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CF7 markup.
				echo $html;
				return;
			}
		}
	}

	$container_types = elite_shipping_get_contact_container_types();
	$values          = array(
		'name'         => '',
		'email'        => '',
		'phone'        => '',
		'location'     => '',
		'container'    => '',
		'quantity'     => '',
		'delivery'     => '',
		'requirements' => '',
	);

	if ( 'error' === $status && ! empty( $_GET['contact_data'] ) ) {
		$stored = get_transient( 'elite_contact_form_' . sanitize_key( wp_unslash( $_GET['contact_data'] ) ) );
		if ( is_array( $stored ) ) {
			$values = array_merge( $values, $stored );
		}
	}
	?>
	<form class="apex-contact-native-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="elite_contact_form">
		<?php wp_nonce_field( 'elite_contact_form', 'elite_contact_nonce' ); ?>
		<input type="text" name="elite_contact_company" value="" tabindex="-1" autocomplete="off" class="apex-contact-honeypot" aria-hidden="true">

		<p>
			<label for="elite-contact-name"><?php esc_html_e( 'Full Name', 'elite-shipping' ); ?> *</label>
			<input id="elite-contact-name" type="text" name="elite_contact_name" required maxlength="400" placeholder="<?php esc_attr_e( 'Jane Smith', 'elite-shipping' ); ?>" value="<?php echo esc_attr( $values['name'] ); ?>">
		</p>
		<p>
			<label for="elite-contact-email"><?php esc_html_e( 'Email Address', 'elite-shipping' ); ?> *</label>
			<input id="elite-contact-email" type="email" name="elite_contact_email" required maxlength="400" placeholder="<?php esc_attr_e( 'you@example.com', 'elite-shipping' ); ?>" value="<?php echo esc_attr( $values['email'] ); ?>">
		</p>
		<p>
			<label for="elite-contact-phone"><?php esc_html_e( 'Phone Number', 'elite-shipping' ); ?> *</label>
			<input id="elite-contact-phone" type="tel" name="elite_contact_phone" required maxlength="400" placeholder="<?php echo esc_attr( ELITE_CONTACT_PHONE ); ?>" value="<?php echo esc_attr( $values['phone'] ); ?>">
		</p>
		<p>
			<label for="elite-contact-location"><?php esc_html_e( 'Delivery Location', 'elite-shipping' ); ?> *</label>
			<input id="elite-contact-location" type="text" name="elite_contact_location" required maxlength="400" placeholder="<?php esc_attr_e( 'City, County or Postcode', 'elite-shipping' ); ?>" value="<?php echo esc_attr( $values['location'] ); ?>">
		</p>
		<p>
			<label for="elite-contact-container"><?php esc_html_e( 'Container Type', 'elite-shipping' ); ?> *</label>
			<select id="elite-contact-container" name="elite_contact_container" required>
				<option value=""><?php esc_html_e( 'Select a category…', 'elite-shipping' ); ?></option>
				<?php foreach ( $container_types as $type ) : ?>
					<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $values['container'], $type ); ?>><?php echo esc_html( $type ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="elite-contact-quantity"><?php esc_html_e( 'Quantity', 'elite-shipping' ); ?> *</label>
			<input id="elite-contact-quantity" type="number" name="elite_contact_quantity" required min="1" max="100" placeholder="<?php esc_attr_e( 'e.g. 2', 'elite-shipping' ); ?>" value="<?php echo esc_attr( $values['quantity'] ); ?>">
		</p>
		<p>
			<label for="elite-contact-delivery"><?php esc_html_e( 'Preferred Delivery Date', 'elite-shipping' ); ?></label>
			<input id="elite-contact-delivery" type="date" name="elite_contact_delivery" value="<?php echo esc_attr( $values['delivery'] ); ?>">
		</p>
		<p>
			<label for="elite-contact-requirements"><?php esc_html_e( 'Additional Requirements', 'elite-shipping' ); ?></label>
			<textarea id="elite-contact-requirements" name="elite_contact_requirements" rows="6" maxlength="2000" placeholder="<?php esc_attr_e( 'Special instructions, modifications, delivery access etc.', 'elite-shipping' ); ?>"><?php echo esc_textarea( $values['requirements'] ); ?></textarea>
		</p>
		<p>
			<input type="submit" value="<?php esc_attr_e( 'Request Quote →', 'elite-shipping' ); ?>">
		</p>
	</form>
	<?php
}

add_action( 'admin_post_elite_contact_form', 'elite_shipping_handle_contact_form' );
add_action( 'admin_post_nopriv_elite_contact_form', 'elite_shipping_handle_contact_form' );
function elite_shipping_handle_contact_form() {
	$from_drawer = isset( $_POST['elite_contact_source'] ) && 'drawer' === sanitize_key( wp_unslash( $_POST['elite_contact_source'] ) );
	$referer     = wp_get_referer();
	$redirect    = ( $from_drawer && $referer ) ? remove_query_arg( array( 'quote', 'contact', 'contact_data' ), $referer ) : elite_shipping_get_page_url( 'contact-us', '/contact-us/' );
	$error_key   = $from_drawer ? 'quote' : 'contact';
	$success_key = $from_drawer ? 'quote' : 'contact';

	if ( ! isset( $_POST['elite_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['elite_contact_nonce'] ) ), 'elite_contact_form' ) ) {
		wp_safe_redirect( add_query_arg( $error_key, 'error', $redirect ) );
		exit;
	}

	if ( ! empty( $_POST['elite_contact_company'] ) ) {
		wp_safe_redirect( add_query_arg( $success_key, 'sent', $redirect ) );
		exit;
	}

	$name         = isset( $_POST['elite_contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['elite_contact_name'] ) ) : '';
	$email        = isset( $_POST['elite_contact_email'] ) ? sanitize_email( wp_unslash( $_POST['elite_contact_email'] ) ) : '';
	$phone        = isset( $_POST['elite_contact_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['elite_contact_phone'] ) ) : '';
	$location     = isset( $_POST['elite_contact_location'] ) ? sanitize_text_field( wp_unslash( $_POST['elite_contact_location'] ) ) : '';
	$container    = isset( $_POST['elite_contact_container'] ) ? sanitize_text_field( wp_unslash( $_POST['elite_contact_container'] ) ) : '';
	$quantity     = isset( $_POST['elite_contact_quantity'] ) ? absint( wp_unslash( $_POST['elite_contact_quantity'] ) ) : 0;
	$delivery     = isset( $_POST['elite_contact_delivery'] ) ? sanitize_text_field( wp_unslash( $_POST['elite_contact_delivery'] ) ) : '';
	$requirements = isset( $_POST['elite_contact_requirements'] ) ? sanitize_textarea_field( wp_unslash( $_POST['elite_contact_requirements'] ) ) : '';

	$stored_values = array(
		'name'         => $name,
		'email'        => $email,
		'phone'        => $phone,
		'location'     => $location,
		'container'    => $container,
		'quantity'     => $quantity ? (string) $quantity : '',
		'delivery'     => $delivery,
		'requirements' => $requirements,
	);

	$allowed_types = elite_shipping_get_contact_container_types();
	if ( ! in_array( $container, $allowed_types, true ) ) {
		$container = $allowed_types[0];
	}

	if ( '' === $name || ! is_email( $email ) || '' === $phone || '' === $location || $quantity < 1 ) {
		$key = wp_generate_password( 12, false, false );
		set_transient( 'elite_contact_form_' . $key, $stored_values, MINUTE_IN_SECONDS * 10 );
		wp_safe_redirect(
			add_query_arg(
				array(
					$error_key     => 'error',
					'contact_data' => $key,
				),
				$redirect
			)
		);
		exit;
	}

	$contact = elite_shipping_get_contact_details();
	$to      = $contact['email'];
	$subject = sprintf(
		/* translators: %s: customer name */
		__( 'Contact form: quote request from %s', 'elite-shipping' ),
		$name
	);
	$body    = "Full Name: {$name}\n";
	$body   .= "Email: {$email}\n";
	$body   .= "Phone: {$phone}\n";
	$body   .= "Delivery Location: {$location}\n";
	$body   .= "Container Type: {$container}\n";
	$body   .= "Quantity: {$quantity}\n";
	$body   .= 'Preferred Delivery Date: ' . ( $delivery ? $delivery : 'Not specified' ) . "\n";
	$body   .= "Additional Requirements:\n{$requirements}\n";
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, $subject, $body, $headers );

	if ( ! $sent ) {
		$key = wp_generate_password( 12, false, false );
		set_transient( 'elite_contact_form_' . $key, $stored_values, MINUTE_IN_SECONDS * 10 );
		wp_safe_redirect(
			add_query_arg(
				array(
					$error_key     => 'error',
					'contact_data' => $key,
				),
				$redirect
			)
		);
		exit;
	}

	wp_safe_redirect( add_query_arg( $success_key, 'sent', $redirect ) );
	exit;
}

/**
 * Testimonials for About Us page.
 *
 * @return array<int, array{service: string, name: string, quote: string}>
 */
function elite_shipping_get_about_testimonials() {
	return array(
		array(
			'service' => '20ft Containers',
			'name'    => 'John D.',
			'quote'   => 'Our 20ft container arrived on time and was in perfect condition. We highly recommend Elite Shipping Containers for reliability and build quality.',
		),
		array(
			'service' => '40ft Containers',
			'name'    => 'Emily R.',
			'quote'   => 'Spacious and solid containers that met our business storage needs. Excellent service from Elite Shipping Containers from start to finish.',
		),
		array(
			'service' => 'Refrigerated Containers',
			'name'    => 'Sarah P.',
			'quote'   => 'The refrigerated unit worked flawlessly. Very pleased with Elite Shipping Containers\' performance and pricing.',
		),
		array(
			'service' => 'Flat Pack Units',
			'name'    => 'David T.',
			'quote'   => 'We assembled the flat pack unit within hours. It was simple, strong, and stylish, thank you Elite Shipping Containers!',
		),
		array(
			'service' => 'Cabins for Sale',
			'name'    => 'Jessica B.',
			'quote'   => 'The site cabin we received was both functional and cozy. Elite Shipping Containers exceeded our expectations in all ways.',
		),
		array(
			'service' => 'Used Containers',
			'name'    => 'Kevin F.',
			'quote'   => 'Used container was in near-new condition and affordable. Truly a great find from Elite Shipping Containers.',
		),
	);
}

/**
 * Fetch WooCommerce product categories.
 *
 * @param array $args Optional get_terms arguments.
 * @return WP_Term[]
 */
function elite_shipping_get_product_categories( $args = array() ) {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}

	$defaults = array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
		'exclude'    => array( get_option( 'default_product_cat', 0 ) ),
	);

	$terms = get_terms( wp_parse_args( $args, $defaults ) );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	return $terms;
}

/**
 * Footer "Our Products" category links (fixed list, ordered).
 *
 * @return array<int, array{name: string, url: string}>
 */
function elite_shipping_get_footer_product_categories() {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$labels   = array(
		'10Ft Containers',
		'16ft Storage Containers',
		'20ft Containers',
		'30ft Containers',
		'40ft Containers',
		'45Ft Containers',
	);
	$terms   = elite_shipping_get_product_categories();
	$by_name = array();

	foreach ( $terms as $term ) {
		$by_name[ strtolower( $term->name ) ] = $term;
	}

	$items = array();
	foreach ( $labels as $label ) {
		if ( isset( $by_name[ strtolower( $label ) ] ) ) {
			$term = $by_name[ strtolower( $label ) ];
			$link = get_term_link( $term );
			if ( is_wp_error( $link ) ) {
				$link = $shop_url;
			}
			$items[] = array(
				'name' => elite_shipping_decode_term_name( $term->name ),
				'url'  => $link,
			);
			continue;
		}

		$items[] = array(
			'name' => $label,
			'url'  => $shop_url,
		);
	}

	return $items;
}

/**
 * Decode HTML entities in term/category names (fixes &amp; → &).
 *
 * @param string $name Raw term name.
 * @return string
 */
function elite_shipping_decode_term_name( $name ) {
	$name = html_entity_decode( (string) $name, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	// Second pass covers double-encoded values like &amp;amp;.
	return html_entity_decode( $name, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
}

/**
 * Product categories for navigation lists.
 *
 * @return array<int, array{name: string, url: string}>
 */
function elite_shipping_get_nav_categories() {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$items    = array();
	$terms    = elite_shipping_get_product_categories();

	foreach ( $terms as $term ) {
		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			$link = $shop_url;
		}
		$items[] = array(
			'name' => elite_shipping_decode_term_name( $term->name ),
			'url'  => $link,
		);
	}

	if ( empty( $items ) ) {
		$fallback = array(
			'8ft x 10ft Containers',
			'16ft Storage Containers',
			'20ft Containers',
			'30ft Containers',
			'40ft Containers',
			'45Ft Containers',
			'Used Shipping Containers',
			'Refrigerated Containers',
			'Flat Pack Containers',
			'Cabins for Sale',
			'Modified Containers',
			'Shipping Container Pool',
		);
		foreach ( $fallback as $name ) {
			$items[] = array(
				'name' => $name,
				'url'  => $shop_url,
			);
		}
	}

	return $items;
}

/**
 * Container sizes for the header dropdown menu.
 *
 * @return array<int, array{name: string, url: string}>
 */
function elite_shipping_get_containers_menu_categories() {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$labels   = array(
		'8ft x 10ft Containers',
		'16ft Storage Containers',
		'20ft Containers',
		'30ft Containers',
		'40ft Containers',
		'45Ft Containers',
	);
	$terms = elite_shipping_get_product_categories();
	$by_name = array();

	foreach ( $terms as $term ) {
		$by_name[ strtolower( $term->name ) ] = $term;
		$by_name[ strtolower( str_replace( 'ft', 'Ft', $term->name ) ) ] = $term;
	}

	$items = array();
	foreach ( $labels as $label ) {
		$key = strtolower( $label );
		if ( isset( $by_name[ $key ] ) ) {
			$term = $by_name[ $key ];
			$link = get_term_link( $term );
			if ( is_wp_error( $link ) ) {
				$link = $shop_url;
			}
			$items[] = array(
				'name' => elite_shipping_decode_term_name( $term->name ),
				'url'  => $link,
				'slug' => $term->slug,
			);
			continue;
		}

		$items[] = array(
			'name' => $label,
			'url'  => $shop_url,
			'slug' => sanitize_title( $label ),
		);
	}

	return $items;
}

/**
 * Min/max catalog prices for the shop price slider.
 *
 * @return array{min: float, max: float, step: int}
 */
function elite_shipping_get_shop_price_bounds() {
	global $wpdb;

	$defaults = array(
		'min'  => 0,
		'max'  => 50000,
		'step' => 50,
	);

	if ( ! class_exists( 'WooCommerce' ) ) {
		return $defaults;
	}

	$join  = '';
	$where = " WHERE posts.post_type = 'product' AND posts.post_status = 'publish' AND lookup.min_price > 0 ";

	if ( is_product_category() || is_product_tag() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$join  .= " INNER JOIN {$wpdb->term_relationships} tr ON posts.ID = tr.object_id ";
			$join  .= " INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id ";
			$where .= $wpdb->prepare(
				' AND tt.taxonomy = %s AND tt.term_id = %d ',
				$term->taxonomy,
				$term->term_id
			);
		}
	}

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$row = $wpdb->get_row(
		"
		SELECT MIN(lookup.min_price) AS min_price, MAX(lookup.max_price) AS max_price
		FROM {$wpdb->wc_product_meta_lookup} lookup
		INNER JOIN {$wpdb->posts} posts ON lookup.product_id = posts.ID
		{$join}
		{$where}
		"
	);

	if ( ! $row || null === $row->min_price || null === $row->max_price ) {
		return $defaults;
	}

	$min = floor( (float) $row->min_price );
	$max = ceil( (float) $row->max_price );

	if ( $max <= $min ) {
		$max = $min + 100;
	}

	return array(
		'min'  => $min,
		'max'  => $max,
		'step' => max( 1, (int) round( ( $max - $min ) / 200 ) ),
	);
}

/**
 * Format a plain price amount for the shop slider label.
 *
 * @param float $amount Price amount.
 * @return string
 */
function elite_shipping_format_shop_price_amount( $amount ) {
	if ( function_exists( 'wc_price' ) ) {
		return html_entity_decode( wp_strip_all_tags( wc_price( $amount, array( 'decimals' => 0 ) ) ) );
	}

	return '£' . number_format_i18n( $amount, 0 );
}

/**
 * All WooCommerce categories for the shop sidebar.
 *
 * @return array<int, array{name: string, url: string, slug: string}>
 */
function elite_shipping_get_shop_sidebar_categories() {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$terms    = elite_shipping_get_product_categories();
	$items    = array();

	foreach ( $terms as $term ) {
		if ( 'uncategorized' === $term->slug ) {
			continue;
		}

		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			$link = $shop_url;
		}

		$items[] = array(
			'name'  => elite_shipping_decode_term_name( $term->name ),
			'url'   => $link,
			'slug'  => $term->slug,
			'count' => (int) $term->count,
		);
	}

	return $items;
}

/**
 * Top rated products for the shop sidebar widget.
 *
 * @param int $limit Number of products.
 * @return array<int, array{id: int, title: string, url: string, price_html: string, image: string}>
 */
function elite_shipping_get_top_rated_products( $limit = 3 ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return array();
	}

	$limit = max( 1, absint( $limit ) );
	$query   = new WC_Product_Query(
		array(
			'limit'    => $limit,
			'orderby'  => 'rating',
			'order'    => 'DESC',
			'status'   => 'publish',
			'return'   => 'ids',
			'paginate' => false,
		)
	);
	$ids     = $query->get_products();

	if ( empty( $ids ) ) {
		$query = new WC_Product_Query(
			array(
				'limit'    => $limit,
				'orderby'  => 'popularity',
				'order'    => 'DESC',
				'status'   => 'publish',
				'return'   => 'ids',
				'paginate' => false,
			)
		);
		$ids = $query->get_products();
	}

	if ( empty( $ids ) ) {
		$query = new WC_Product_Query(
			array(
				'limit'    => $limit,
				'orderby'  => 'date',
				'order'    => 'DESC',
				'status'   => 'publish',
				'return'   => 'ids',
				'paginate' => false,
			)
		);
		$ids = $query->get_products();
	}

	$items = array();

	foreach ( $ids as $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! $product || ! $product->is_visible() ) {
			continue;
		}

		$image = $product->get_image(
			'woocommerce_thumbnail',
			array(
				'class' => 'apex-shop-top-rated-img',
			)
		);

		$items[] = array(
			'id'          => $product_id,
			'title'       => $product->get_name(),
			'url'         => get_permalink( $product_id ),
			'price_html'  => $product->get_price_html(),
			'image'       => $image ? $image : '',
		);
	}

	return $items;
}

/**
 * Hero background slide image URLs.
 *
 * @return string[]
 */
function elite_shipping_get_hero_slides() {
	$base     = ELITE_SHIPPING_URI . '/assets/images/';
	$fallback = array(
		$base . 'image_a.jpg',
		$base . 'image_b.jpg',
		$base . 'image_c.jpg',
		$base . 'image_d.jpg',
	);
	$slides   = array();

	if ( function_exists( 'elite_shipping_get_hero_slide_attachment_ids' ) ) {
		foreach ( elite_shipping_get_hero_slide_attachment_ids() as $attachment_id ) {
			$url = wp_get_attachment_image_url( $attachment_id, 'full' );
			if ( ! $url ) {
				$url = wp_get_attachment_image_url( $attachment_id, 'large' );
			}
			if ( $url ) {
				$slides[] = $url;
			}
		}
	}

	return ! empty( $slides ) ? $slides : $fallback;
}

function elite_shipping_logo_url() {
	return ELITE_SHIPPING_URI . '/assets/images/elite-logo.png';
}

/** Fallback critical CSS if main.css blocked by cache/CDN. */
add_action( 'wp_head', 'elite_shipping_critical_css', 5 );
function elite_shipping_critical_css() {
	?>
	<style id="elite-critical-css">
		body.elite-shipping-theme{margin:0;font-family:Inter,sans-serif}
		.elite-topbar{background:#0f172a;color:#fff;font-size:14px}
		.elite-header{background:#fff;border-bottom:1px solid #e2e8f0;position:sticky;top:0;z-index:100}
		.elite-header-inner,.elite-topbar-inner,.elite-container{max-width:1770px;margin:0 auto;padding:0 20px}
		.elite-topbar-trust,.elite-topbar-links{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
		.elite-nav{display:flex;gap:18px;flex-wrap:wrap}
		.elite-nav a{color:#0f172a;text-decoration:none;font-size:15px;font-weight:600}
		.elite-topbar a{color:#fff;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
		.elite-btn-primary{background:#f97316;color:#fff;padding:12px 22px;border-radius:8px;text-decoration:none;font-weight:700}
		.elite-hero{background:#0f172a;color:#fff;padding:48px 0 0}
		.elite-footer{background:#0f172a;color:#fff;padding:40px 0}
	</style>
	<?php
}
