<?php
/**
 * Site header — Apex Containers exact layout.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$urls                 = elite_shipping_get_urls();
$contact              = elite_shipping_get_contact_details();
$categories           = elite_shipping_get_nav_categories();
$container_categories    = elite_shipping_get_containers_menu_categories();
$modification_menu_items = elite_shipping_get_modifications_menu_items();
$search_url              = home_url( '/' );
?>
<div class="elite-site">
	<div class="elite-site-header-bar">
	<div class="elite-topbar">
		<div class="elite-container elite-topbar-inner">
			<div class="elite-topbar-trust">
				<span class="elite-topbar-item elite-topbar-item--icon">
					<svg class="elite-topbar-ico elite-topbar-ico--accent" width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/><path d="M7 12.5l3 3 7-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					Nationwide delivery
				</span>
				<span class="elite-topbar-sep" aria-hidden="true">·</span>
				<span class="elite-topbar-item">Wind &amp; watertight quality</span>
				<span class="elite-topbar-sep" aria-hidden="true">·</span>
				<span class="elite-topbar-item">Fast quotes</span>
			</div>
			<div class="elite-topbar-links">
				<a href="#">
					<svg class="elite-topbar-ico" width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm1 7V3.5L19.5 9H15zM8 13h8v2H8v-2zm0 4h8v2H8v-2zm0-8h5v2H8V9z"/></svg>
					Brochure
				</a>
				<a href="<?php echo esc_url( $contact['phone_href'] ); ?>">
					<svg class="elite-topbar-ico" width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1.1-.2 1.2.4 2.5.6 3.8.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.6.6 3.8.1.4 0 .8-.3 1.1l-2.2 2.2z"/></svg>
					<?php echo esc_html( $contact['phone'] ); ?>
				</a>
				<a class="elite-topbar-quote" href="<?php echo esc_url( $urls['quote'] ); ?>">
					Get a Quote
				</a>
			</div>
		</div>
	</div>

	<header class="elite-header">
		<div class="elite-container elite-header-inner">
			<a class="elite-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php get_template_part( 'template-parts/logo', 'brand', array( 'variant' => 'light', 'height' => 42 ) ); ?>
			</a>
			<nav class="elite-nav" aria-label="Primary">
				<a class="<?php echo esc_attr( elite_shipping_nav_class( 'home' ) ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">HOME</a>
				<div class="elite-nav-dropdown">
					<button type="button" class="<?php echo esc_attr( elite_shipping_nav_class( 'containers', 'elite-nav-dropdown-toggle' ) ); ?>" aria-expanded="false" aria-haspopup="true" aria-controls="elite-containers-menu">
						CONTAINERS <span class="elite-caret">▾</span>
					</button>
					<div class="elite-nav-dropdown-menu" id="elite-containers-menu" role="menu">
						<?php foreach ( $container_categories as $category ) : ?>
							<a role="menuitem" href="<?php echo esc_url( $category['url'] ); ?>"><?php echo esc_html( $category['name'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="elite-nav-dropdown">
					<button type="button" class="<?php echo esc_attr( elite_shipping_nav_class( 'modifications', 'elite-nav-dropdown-toggle' ) ); ?>" aria-expanded="false" aria-haspopup="true" aria-controls="elite-modifications-menu">
						MODIFICATIONS <span class="elite-caret">▾</span>
					</button>
					<div class="elite-nav-dropdown-menu" id="elite-modifications-menu" role="menu">
						<?php foreach ( $modification_menu_items as $item ) : ?>
							<a role="menuitem" href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a>
						<?php endforeach; ?>
						<?php if ( ! empty( $modification_menu_items ) ) : ?>
							<span class="elite-nav-dropdown-divider" role="separator" aria-hidden="true"></span>
						<?php endif; ?>
						<a role="menuitem" class="elite-nav-dropdown-cta" href="<?php echo esc_url( $urls['quote'] ); ?>"><?php esc_html_e( 'Get a Quote', 'elite-shipping' ); ?></a>
						<a role="menuitem" href="<?php echo esc_url( $urls['contact'] ); ?>"><?php esc_html_e( 'Contact Us', 'elite-shipping' ); ?></a>
					</div>
				</div>
				<a class="<?php echo esc_attr( elite_shipping_nav_class( 'products' ) ); ?>" href="<?php echo esc_url( $urls['shop'] ); ?>">PRODUCTS <span class="elite-caret">▾</span></a>
				<a class="<?php echo esc_attr( elite_shipping_nav_class( 'about' ) ); ?>" href="<?php echo esc_url( $urls['about'] ); ?>">ABOUT US</a>
				<a class="<?php echo esc_attr( elite_shipping_nav_class( 'blog' ) ); ?>" href="<?php echo esc_url( $urls['blog'] ); ?>">BLOG</a>
				<a class="<?php echo esc_attr( elite_shipping_nav_class( 'contact' ) ); ?>" href="<?php echo esc_url( $urls['contact'] ); ?>">CONTACT</a>
			</nav>
			<div class="elite-header-actions">
				<button type="button" class="elite-search-btn" aria-label="Search">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3-3"/></svg>
				</button>
				<a class="elite-btn elite-btn-primary elite-header-quote" href="<?php echo esc_url( $urls['quote'] ); ?>">GET A QUOTE</a>
				<button type="button" class="elite-menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="elite-mobile-nav">
					<span class="elite-menu-toggle-bar" aria-hidden="true"></span>
					<span class="elite-menu-toggle-bar" aria-hidden="true"></span>
					<span class="elite-menu-toggle-bar" aria-hidden="true"></span>
				</button>
			</div>
		</div>
	</header>
	</div>

	<div class="elite-mobile-nav-overlay" id="elite-mobile-nav-overlay" hidden aria-hidden="true"></div>
	<aside class="elite-mobile-nav" id="elite-mobile-nav" aria-label="Mobile navigation" hidden>
		<div class="elite-mobile-nav-search">
			<form class="elite-mobile-nav-search-form" action="<?php echo esc_url( $search_url ); ?>" method="get" role="search">
				<input
					class="elite-mobile-nav-search-input"
					type="search"
					name="s"
					placeholder="Search for products"
					autocomplete="off"
					aria-label="Search for products"
				>
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<input type="hidden" name="post_type" value="product">
				<?php endif; ?>
				<button type="submit" class="elite-mobile-nav-search-submit" aria-label="Search">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3-3"/></svg>
				</button>
			</form>
			<button type="button" class="elite-mobile-nav-close" aria-label="Close menu">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
			</button>
		</div>

		<div class="elite-mobile-nav-tabs" role="tablist" aria-label="Mobile menu sections">
			<button type="button" class="elite-mobile-nav-tab is-active" role="tab" aria-selected="true" aria-controls="elite-mobile-nav-menu" data-tab="menu">MENU</button>
			<button type="button" class="elite-mobile-nav-tab" role="tab" aria-selected="false" aria-controls="elite-mobile-nav-categories" data-tab="categories">CATEGORIES</button>
		</div>

		<div class="elite-mobile-nav-panels">
			<nav class="elite-mobile-nav-panel is-active" id="elite-mobile-nav-menu" role="tabpanel" data-panel="menu" aria-label="Menu links">
				<a class="<?php echo esc_attr( elite_shipping_nav_class( 'home' ) ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">HOME</a>
				<div class="elite-mobile-nav-group">
					<button type="button" class="<?php echo esc_attr( elite_shipping_nav_class( 'containers', 'elite-mobile-nav-group-toggle' ) ); ?>" aria-expanded="false">
						<span>CONTAINERS</span>
						<span class="elite-mobile-nav-chevron" aria-hidden="true">&rsaquo;</span>
					</button>
					<div class="elite-mobile-nav-submenu" hidden>
						<?php foreach ( $container_categories as $category ) : ?>
							<a href="<?php echo esc_url( $category['url'] ); ?>"><?php echo esc_html( $category['name'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="elite-mobile-nav-group">
					<button type="button" class="<?php echo esc_attr( elite_shipping_nav_class( 'modifications', 'elite-mobile-nav-group-toggle' ) ); ?>" aria-expanded="false">
						<span>MODIFICATIONS</span>
						<span class="elite-mobile-nav-chevron" aria-hidden="true">&rsaquo;</span>
					</button>
					<div class="elite-mobile-nav-submenu" hidden>
						<?php foreach ( $modification_menu_items as $item ) : ?>
							<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a>
						<?php endforeach; ?>
						<a class="elite-mobile-nav-submenu-cta" href="<?php echo esc_url( $urls['quote'] ); ?>"><?php esc_html_e( 'Get a Quote', 'elite-shipping' ); ?></a>
						<a href="<?php echo esc_url( $urls['contact'] ); ?>"><?php esc_html_e( 'Contact Us', 'elite-shipping' ); ?></a>
					</div>
				</div>
				<a class="elite-mobile-nav-link--sub <?php echo esc_attr( elite_shipping_nav_class( 'products' ) ); ?>" href="<?php echo esc_url( $urls['shop'] ); ?>">
					<span>PRODUCTS</span>
					<span class="elite-mobile-nav-chevron" aria-hidden="true">&rsaquo;</span>
				</a>
				<a class="<?php echo esc_attr( elite_shipping_nav_class( 'about' ) ); ?>" href="<?php echo esc_url( $urls['about'] ); ?>">ABOUT US</a>
				<a class="<?php echo esc_attr( elite_shipping_nav_class( 'blog' ) ); ?>" href="<?php echo esc_url( $urls['blog'] ); ?>">BLOG</a>
				<a class="<?php echo esc_attr( elite_shipping_nav_class( 'contact' ) ); ?>" href="<?php echo esc_url( $urls['contact'] ); ?>">CONTACT</a>
			</nav>

			<nav class="elite-mobile-nav-panel" id="elite-mobile-nav-categories" role="tabpanel" data-panel="categories" aria-label="Category links" hidden>
				<?php foreach ( $categories as $category ) : ?>
					<?php
					$category_name = function_exists( 'elite_shipping_decode_term_name' )
						? elite_shipping_decode_term_name( $category['name'] )
						: $category['name'];
					$category_name = function_exists( 'mb_strtoupper' )
						? mb_strtoupper( $category_name, 'UTF-8' )
						: strtoupper( $category_name );
					?>
					<a href="<?php echo esc_url( $category['url'] ); ?>"><?php echo esc_html( $category_name ); ?></a>
				<?php endforeach; ?>
			</nav>
		</div>
	</aside>
