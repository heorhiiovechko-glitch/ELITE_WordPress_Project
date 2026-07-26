<?php
/**
 * Theme Customizer — Home page sections.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product category choices for Customizer dropdowns.
 *
 * @return array<int, string>
 */
function elite_shipping_get_category_customizer_choices() {
	return elite_shipping_get_category_slot_choices();
}

/**
 * Category dropdown choices for Top Picks display list slots.
 *
 * @return array<int, string>
 */
function elite_shipping_get_category_slot_choices() {
	$choices = array(
		0 => __( '— Not included —', 'elite-shipping' ),
	);

	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return $choices;
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return $choices;
	}

	$default_cat = absint( get_option( 'default_product_cat', 0 ) );

	foreach ( $terms as $term ) {
		if ( $default_cat && (int) $term->term_id === $default_cat ) {
			continue;
		}
		if ( 'uncategorized' === $term->slug ) {
			continue;
		}

		$choices[ (int) $term->term_id ] = $term->name;
	}

	$labels = $choices;
	unset( $labels[0] );
	natcasesort( $labels );
	$choices = array( 0 => $choices[0] ) + $labels;

	return $choices;
}

/**
 * WooCommerce product choices for Customizer dropdowns.
 *
 * @return array<int, string>
 */
function elite_shipping_get_product_customizer_choices() {
	$choices = array(
		0 => __( '— Automatic —', 'elite-shipping' ),
	);

	if ( ! class_exists( 'WooCommerce' ) ) {
		return $choices;
	}

	$product_ids = wc_get_products(
		array(
			'limit'   => 500,
			'status'  => 'publish',
			'orderby' => 'title',
			'order'   => 'ASC',
			'return'  => 'ids',
		)
	);

	foreach ( $product_ids as $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product ) {
			$choices[ (int) $product_id ] = $product->get_name();
		}
	}

	return $choices;
}

/**
 * Category choices for Top Picks list row selects (A–Z, no empty option).
 *
 * @return array<int, string>
 */
function elite_shipping_get_category_picker_choices() {
	$choices = elite_shipping_get_category_slot_choices();
	unset( $choices[0] );
	natcasesort( $choices );

	return $choices;
}

/**
 * Categories in the Customizer Top Picks display list.
 *
 * @param int $limit Maximum categories to return. 0 = no limit.
 * @return WP_Term[]
 */
function elite_shipping_get_top_picks_categories( $limit = 0 ) {
	$picked_ids = elite_shipping_get_top_picks_category_ids();

	if ( empty( $picked_ids ) ) {
		return array();
	}

	if ( $limit > 0 ) {
		$picked_ids = array_slice( $picked_ids, 0, absint( $limit ) );
	}

	$terms = array();

	foreach ( $picked_ids as $term_id ) {
		$term = get_term( $term_id, 'product_cat' );
		if ( $term instanceof WP_Term && ! is_wp_error( $term ) ) {
			$terms[] = $term;
		}
	}

	return $terms;
}

/**
 * Register a text theme setting + control.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 * @param string               $id           Setting ID.
 * @param array                $args         Control args.
 */
function elite_shipping_add_text_setting( $wp_customize, $id, $args ) {
	$wp_customize->add_setting(
		$id,
		array(
			'default'           => $args['default'] ?? '',
			'sanitize_callback' => $args['sanitize'] ?? 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		$id,
		array(
			'label'   => $args['label'],
			'section' => $args['section'],
			'type'    => $args['type'] ?? 'text',
		)
	);
}

/**
 * Register an image theme setting + control.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 * @param string               $id           Setting ID.
 * @param array                $args         Control args.
 */
function elite_shipping_add_image_setting( $wp_customize, $id, $args ) {
	$wp_customize->add_setting(
		$id,
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			$id,
			array(
				'label'     => $args['label'],
				'section'   => $args['section'],
				'mime_type' => 'image',
			)
		)
	);
}

/**
 * Register Customizer settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function elite_shipping_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'elite_home',
		array(
			'title'       => __( 'Home', 'elite-shipping' ),
			'description' => __( 'Customize homepage sections.', 'elite-shipping' ),
			'priority'    => 120,
		)
	);

	$product_choices = elite_shipping_get_product_customizer_choices();

	/* Top Picks for You */
	$wp_customize->add_section(
		'elite_home_top_picks',
		array(
			'title'       => __( 'Top Picks for You', 'elite-shipping' ),
			'description' => __( 'Build the homepage display list. Click Add to insert a category row, then choose a category. Only items in this list appear on the homepage, in order.', 'elite-shipping' ),
			'panel'       => 'elite_home',
			'priority'    => 10,
		)
	);

	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_top_picks_kicker',
		array(
			'label'   => __( 'Kicker', 'elite-shipping' ),
			'section' => 'elite_home_top_picks',
			'default' => 'FEATURED CONTAINERS',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_top_picks_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_home_top_picks',
			'default' => 'Top Picks for You',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_top_picks_desc',
		array(
			'label'   => __( 'Description', 'elite-shipping' ),
			'section' => 'elite_home_top_picks',
			'default' => 'High-quality containers in stock and ready to ship.',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_top_picks_btn_text',
		array(
			'label'   => __( 'Button text', 'elite-shipping' ),
			'section' => 'elite_home_top_picks',
			'default' => 'VIEW ALL PRODUCTS',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_top_picks_btn_url',
		array(
			'label'   => __( 'Button URL', 'elite-shipping' ),
			'section' => 'elite_home_top_picks',
			'default' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
			'sanitize' => 'esc_url_raw',
		)
	);

	if ( class_exists( 'Elite_Top_Picks_Display_List_Control' ) ) {
		$picker_choices = elite_shipping_get_category_picker_choices();

		$wp_customize->add_setting(
			'elite_top_picks_category_ids',
			array(
				'default'           => '[]',
				'sanitize_callback' => 'elite_shipping_sanitize_top_picks_category_ids',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new Elite_Top_Picks_Display_List_Control(
				$wp_customize,
				'elite_top_picks_category_ids',
				array(
					'label'       => __( 'Display list', 'elite-shipping' ),
					'description' => __( 'These categories will appear on the homepage.', 'elite-shipping' ),
					'section'     => 'elite_home_top_picks',
					'priority'    => 24,
					'choices'     => $picker_choices,
				)
			)
		);
	}

	/* Your Trusted Container Partner */
	$wp_customize->add_section(
		'elite_home_about',
		array(
			'title'       => __( 'Your Trusted Container Partner', 'elite-shipping' ),
			'description' => __( 'About section text and gallery images.', 'elite-shipping' ),
			'panel'       => 'elite_home',
			'priority'    => 20,
		)
	);

	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_kicker',
		array(
			'label'   => __( 'Kicker', 'elite-shipping' ),
			'section' => 'elite_home_about',
			'default' => 'ABOUT ELITE SHIPPING CONTAINERS',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_home_about',
			'default' => 'Your Trusted Container Partner',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_text',
		array(
			'label'    => __( 'Description', 'elite-shipping' ),
			'section'  => 'elite_home_about',
			'type'     => 'textarea',
			'default'  => 'Elite Shipping Containers Ltd provides durable, secure, and affordable shipping containers for storage, transport, and special projects. With competitive pricing and exceptional customer service, we deliver quality you can trust across the United Kingdom.',
			'sanitize' => 'wp_kses_post',
		)
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		$defaults = array(
			1 => 'Wide Range of New & Used Containers',
			2 => 'Custom Modifications Available',
			3 => 'UK Nationwide Delivery & Support',
		);
		elite_shipping_add_text_setting(
			$wp_customize,
			'elite_about_check_' . $i,
			array(
				'label'   => sprintf( __( 'Checklist item %d', 'elite-shipping' ), $i ),
				'section' => 'elite_home_about',
				'default' => $defaults[ $i ],
			)
		);
	}

	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_btn_text',
		array(
			'label'   => __( 'Button text', 'elite-shipping' ),
			'section' => 'elite_home_about',
			'default' => 'LEARN MORE ABOUT US →',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_btn_url',
		array(
			'label'    => __( 'Button URL', 'elite-shipping' ),
			'section'  => 'elite_home_about',
			'default'  => home_url( '/about/' ),
			'sanitize' => 'esc_url_raw',
		)
	);

	for ( $i = 1; $i <= 4; $i++ ) {
		elite_shipping_add_image_setting(
			$wp_customize,
			'elite_about_image_' . $i,
			array(
				'label'   => sprintf( __( 'Gallery image %d', 'elite-shipping' ), $i ),
				'section' => 'elite_home_about',
			)
		);
	}

	/* Built to Suit Your Needs */
	$wp_customize->add_section(
		'elite_home_mods',
		array(
			'title'       => __( 'Built to Suit Your Needs', 'elite-shipping' ),
			'description' => __( 'Build the modifications carousel display list. Click Add for each card, enter a title, and select an image.', 'elite-shipping' ),
			'panel'       => 'elite_home',
			'priority'    => 30,
		)
	);

	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_mods_kicker',
		array(
			'label'   => __( 'Kicker', 'elite-shipping' ),
			'section' => 'elite_home_mods',
			'default' => 'CONTAINER MODIFICATIONS',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_mods_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_home_mods',
			'default' => 'Built to Suit Your Needs',
		)
	);

	if ( class_exists( 'Elite_Mods_Display_List_Control' ) ) {
		$wp_customize->add_setting(
			'elite_mods_display_list',
			array(
				'default'           => '[]',
				'sanitize_callback' => 'elite_shipping_sanitize_mods_display_list',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new Elite_Mods_Display_List_Control(
				$wp_customize,
				'elite_mods_display_list',
				array(
					'label'       => __( 'Display list', 'elite-shipping' ),
					'description' => __( 'These cards will appear in the homepage modifications carousel.', 'elite-shipping' ),
					'section'     => 'elite_home_mods',
					'priority'    => 24,
				)
			)
		);
	}

	/* Essential Add-Ons */
	$wp_customize->add_section(
		'elite_home_addons',
		array(
			'title'       => __( 'Essential Add-Ons', 'elite-shipping' ),
			'description' => __( 'Choose up to 3 WooCommerce products. Leave on Automatic to use default accessory cards.', 'elite-shipping' ),
			'panel'       => 'elite_home',
			'priority'    => 40,
		)
	);

	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_addons_kicker',
		array(
			'label'   => __( 'Kicker', 'elite-shipping' ),
			'section' => 'elite_home_addons',
			'default' => 'CONTAINER ACCESSORIES',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_addons_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_home_addons',
			'default' => 'Essential Add-Ons',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_addons_desc',
		array(
			'label'   => __( 'Description', 'elite-shipping' ),
			'section' => 'elite_home_addons',
			'default' => 'Enhance the functionality and security of your container.',
		)
	);

	for ( $slot = 1; $slot <= 3; $slot++ ) {
		$setting_id = 'elite_addon_product_' . $slot;
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => 0,
				'sanitize_callback' => 'elite_shipping_sanitize_product_choice',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => sprintf( __( 'Card %d product', 'elite-shipping' ), $slot ),
				'section' => 'elite_home_addons',
				'type'    => 'select',
				'choices' => $product_choices,
			)
		);
	}

	/* Popular Products */
	$wp_customize->add_section(
		'elite_home_popular',
		array(
			'title'       => __( 'Popular Products', 'elite-shipping' ),
			'description' => __( 'Choose up to 5 WooCommerce products. Leave on Automatic to show the 5 most recent products.', 'elite-shipping' ),
			'panel'       => 'elite_home',
			'priority'    => 50,
		)
	);

	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_popular_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_home_popular',
			'default' => 'POPULAR PRODUCTS',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_popular_btn_text',
		array(
			'label'   => __( 'Button text', 'elite-shipping' ),
			'section' => 'elite_home_popular',
			'default' => 'VIEW ALL PRODUCTS',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_popular_btn_url',
		array(
			'label'    => __( 'Button URL', 'elite-shipping' ),
			'section'  => 'elite_home_popular',
			'default'  => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
			'sanitize' => 'esc_url_raw',
		)
	);

	for ( $slot = 1; $slot <= 5; $slot++ ) {
		$setting_id = 'elite_popular_product_' . $slot;
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => 0,
				'sanitize_callback' => 'elite_shipping_sanitize_product_choice',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => sprintf( __( 'Card %d product', 'elite-shipping' ), $slot ),
				'section' => 'elite_home_popular',
				'type'    => 'select',
				'choices' => $product_choices,
			)
		);
	}

	if ( isset( $wp_customize->selective_refresh ) ) {
		$partials = array(
			array(
				'id'       => 'elite_home_top_picks',
				'selector' => '#elite-home-top-picks',
				'callback' => 'elite_render_home_top_picks_section',
			),
			array(
				'id'       => 'elite_home_about',
				'selector' => '#elite-home-about',
				'callback' => 'elite_render_home_about_section',
			),
			array(
				'id'       => 'elite_home_mods',
				'selector' => '#modifications',
				'callback' => 'elite_render_home_mods_section',
			),
			array(
				'id'       => 'elite_home_addons',
				'selector' => '#elite-home-addons',
				'callback' => 'elite_render_home_addons_section',
			),
			array(
				'id'       => 'elite_home_popular',
				'selector' => '#elite-home-popular',
				'callback' => 'elite_render_home_popular_section',
			),
		);

		foreach ( $partials as $partial ) {
			$wp_customize->selective_refresh->add_partial(
				$partial['id'],
				array(
					'selector'            => $partial['selector'],
					'container_inclusive' => true,
					'render_callback'     => $partial['callback'],
				)
			);
		}
	}
}
add_action( 'customize_register', 'elite_shipping_customize_register' );

/**
 * Sanitize a Customizer category dropdown value.
 *
 * @param mixed $value Raw setting value.
 * @return int
 */
function elite_shipping_sanitize_category_choice( $value ) {
	$term_id = absint( $value );
	if ( $term_id <= 0 ) {
		return 0;
	}

	$term = get_term( $term_id, 'product_cat' );
	if ( $term instanceof WP_Term && ! is_wp_error( $term ) ) {
		return (int) $term->term_id;
	}

	return 0;
}

/**
 * Sanitize a Customizer product dropdown value.
 *
 * @param mixed $value Raw setting value.
 * @return int
 */
function elite_shipping_sanitize_product_choice( $value ) {
	$product_id = absint( $value );
	if ( $product_id <= 0 || ! class_exists( 'WooCommerce' ) ) {
		return 0;
	}

	$product = wc_get_product( $product_id );
	if ( $product && $product->is_visible() ) {
		return (int) $product_id;
	}

	return 0;
}

/**
 * Selective refresh callback for Top Picks cards only.
 */
function elite_shipping_render_top_picks_customizer_partial() {
	if ( function_exists( 'elite_render_category_grid' ) ) {
		elite_render_category_grid();
	}
}

/**
 * Enqueue Customizer controls script/styles for homepage display lists.
 */
function elite_shipping_customize_controls_enqueue_scripts() {
	wp_enqueue_media();

	$top_picks_script = ELITE_SHIPPING_DIR . '/assets/js/customizer-top-picks.js';
	if ( file_exists( $top_picks_script ) ) {
		wp_enqueue_script(
			'elite-top-picks-customizer',
			ELITE_SHIPPING_URI . '/assets/js/customizer-top-picks.js',
			array( 'jquery', 'customize-controls' ),
			(string) filemtime( $top_picks_script ),
			true
		);
	}

	$mods_script = ELITE_SHIPPING_DIR . '/assets/js/customizer-mods.js';
	if ( file_exists( $mods_script ) ) {
		wp_enqueue_script(
			'elite-mods-customizer',
			ELITE_SHIPPING_URI . '/assets/js/customizer-mods.js',
			array( 'jquery', 'customize-controls', 'media-editor' ),
			(string) filemtime( $mods_script ),
			true
		);
	}

	wp_add_inline_style(
		'customize-controls',
		'.elite-top-picks-list-control__head,.elite-mods-list-control__head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:6px;}'
		. '.elite-top-picks-list-control__head .customize-control-title,.elite-mods-list-control__head .customize-control-title{margin:0;}'
		. '.elite-top-picks-list-items,.elite-mods-list-items{margin:10px 0 0;padding:0;list-style:none;}'
		. '.elite-top-picks-list-items__empty,.elite-mods-list-items__empty{margin:0;padding:12px 14px;background:#f6f7f7;border:1px solid #dcdcde;border-radius:4px;color:#646970;font-style:italic;font-size:13px;line-height:1.45;}'
		. '.elite-top-picks-list-item{display:flex;align-items:center;gap:10px;margin:0 0 8px;padding:10px 12px;background:#f6f7f7;border:1px solid #dcdcde;border-radius:4px;}'
		. '.elite-top-picks-list-item__num,.elite-mods-list-item__num{flex:0 0 auto;min-width:1.4em;font-weight:700;color:#1d2327;}'
		. '.elite-top-picks-list-item__select{flex:1 1 auto;min-width:0;max-width:none;}'
		. '.elite-top-picks-remove{flex:0 0 auto;margin-left:auto;color:#b32d2e;text-decoration:none;}'
		. '.elite-top-picks-remove:hover{color:#8a2424;}'
		. '.elite-mods-list-item{display:flex;align-items:center;gap:8px;margin:0 0 8px;padding:10px 12px;background:#f6f7f7;border:1px solid #dcdcde;border-radius:4px;}'
		. '.elite-mods-list-item__title{flex:1 1 auto;min-width:0;margin:0;max-width:none;}'
		. '.elite-mods-list-item__actions{display:flex;align-items:center;gap:6px;flex:0 0 auto;margin-left:auto;}'
		. '.elite-mods-icon-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;padding:0;border:1px solid #c3c4c7;border-radius:4px;background:#fff;color:#1d2327;cursor:pointer;box-shadow:none;}'
		. '.elite-mods-icon-btn .dashicons{width:18px;height:18px;font-size:18px;line-height:1;}'
		. '.elite-mods-icon-btn:hover,.elite-mods-icon-btn:focus{background:#f0f0f1;border-color:#8c8f94;color:#1d2327;}'
		. '.elite-mods-icon-btn--remove{color:#b32d2e;border-color:#dba4a4;}'
		. '.elite-mods-icon-btn--remove:hover,.elite-mods-icon-btn--remove:focus{color:#8a2424;background:#fcf0f1;border-color:#b32d2e;}'
		. '.elite-mods-select-image.has-image{background-size:cover;background-position:center;background-repeat:no-repeat;}'
		. '.elite-mods-select-image.has-image .dashicons{opacity:0;}'
	);
}
add_action( 'customize_controls_enqueue_scripts', 'elite_shipping_customize_controls_enqueue_scripts' );
