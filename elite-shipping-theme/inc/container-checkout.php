<?php
/**
 * Container checkout rules — 20% VAT and flat £237 delivery.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELITE_CONTAINER_VAT_PERCENT', 20 );
define( 'ELITE_CONTAINER_FLAT_SHIPPING_COST', 237 );

/**
 * Display label for container flat delivery.
 *
 * @return string
 */
function elite_shipping_container_delivery_fee_label() {
	return __( 'Container Delivery Fee', 'elite-shipping' );
}

/**
 * Display label for container VAT line.
 *
 * @return string
 */
function elite_shipping_container_vat_display_label() {
	return sprintf(
		/* translators: %s: VAT percentage */
		__( 'VAT: %s%%', 'elite-shipping' ),
		(string) ELITE_CONTAINER_VAT_PERCENT
	);
}

/**
 * Accessory category slugs that are not shipping containers.
 *
 * @return string[]
 */
function elite_shipping_get_accessory_category_slugs() {
	return apply_filters(
		'elite_shipping_accessory_category_slugs',
		array(
			'container-accessories',
			'accessories-container-accessories',
			'cabin-accessories-furniture',
		)
	);
}

/**
 * Whether a product category is treated as accessories (not containers).
 *
 * @param WP_Term $term Product category term.
 * @return bool
 */
function elite_shipping_is_accessory_category( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return false;
	}

	foreach ( elite_shipping_get_accessory_category_slugs() as $slug ) {
		if ( $term->slug === $slug ) {
			return true;
		}
	}

	$slug = strtolower( $term->slug );
	$name = strtolower( $term->name );

	return false !== strpos( $slug, 'accessor' ) || false !== strpos( $name, 'accessor' );
}

/**
 * Whether a product is a shipping container (not an accessory).
 *
 * @param WC_Product|int|null $product Product object or ID.
 * @return bool
 */
function elite_shipping_is_container_product( $product ) {
	if ( ! class_exists( 'WC_Product' ) ) {
		return false;
	}

	if ( is_numeric( $product ) ) {
		$product = wc_get_product( (int) $product );
	}

	if ( ! $product instanceof WC_Product ) {
		return false;
	}

	if ( ! $product->needs_shipping() || $product->is_downloadable() ) {
		return false;
	}

	$terms = wp_get_post_terms( $product->get_id(), 'product_cat' );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return true;
	}

	foreach ( $terms as $term ) {
		if ( elite_shipping_is_accessory_category( $term ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Whether the cart contains at least one container product.
 *
 * @return bool
 */
function elite_shipping_cart_has_containers() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return false;
	}

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		$product = $cart_item['data'] ?? null;
		if ( $product instanceof WC_Product && elite_shipping_is_container_product( $product ) ) {
			return true;
		}
	}

	return false;
}

/**
 * One-time WooCommerce tax/shipping defaults for UK container sales.
 */
function elite_shipping_configure_container_checkout() {
	if ( 'yes' === get_option( 'elite_shipping_container_checkout_configured', '' ) ) {
		return;
	}

	update_option( 'woocommerce_calc_taxes', 'yes' );
	update_option( 'woocommerce_enable_shipping_calc', 'yes' );
	update_option( 'woocommerce_tax_based_on', 'shipping' );
	update_option( 'woocommerce_shipping_tax_class', '' );

	elite_shipping_ensure_vat_rate();

	update_option( 'elite_shipping_container_checkout_configured', 'yes' );
}
add_action( 'init', 'elite_shipping_configure_container_checkout', 5 );

/**
 * Insert a 20% UK VAT rate when none exists.
 *
 * @return int Tax rate ID.
 */
function elite_shipping_ensure_vat_rate() {
	global $wpdb;

	$table  = $wpdb->prefix . 'woocommerce_tax_rates';
	$exists = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT tax_rate_id FROM {$table} WHERE tax_rate_country = %s AND tax_rate = %s AND tax_rate_class = '' LIMIT 1",
			'GB',
			'20.0000'
		)
	);

	if ( $exists ) {
		return (int) $exists;
	}

	$inserted = $wpdb->insert(
		$table,
		array(
			'tax_rate_country'  => 'GB',
			'tax_rate_state'    => '',
			'tax_rate'          => '20.0000',
			'tax_rate_name'     => 'VAT',
			'tax_rate_priority' => 1,
			'tax_rate_compound' => 0,
			'tax_rate_shipping' => 1,
			'tax_rate_order'    => 0,
			'tax_rate_class'    => '',
		),
		array( '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s' )
	);

	return $inserted ? (int) $wpdb->insert_id : 0;
}

/**
 * Force container products to be taxable at checkout.
 *
 * @param string     $status  Tax status.
 * @param WC_Product $product Product.
 * @return string
 */
function elite_shipping_container_tax_status( $status, $product ) {
	if ( elite_shipping_is_container_product( $product ) ) {
		return 'taxable';
	}

	return $status;
}
add_filter( 'woocommerce_product_get_tax_status', 'elite_shipping_container_tax_status', 10, 2 );
add_filter( 'woocommerce_product_variation_get_tax_status', 'elite_shipping_container_tax_status', 10, 2 );

/**
 * Use the standard tax class for containers (20% VAT rate).
 *
 * @param string     $class   Tax class.
 * @param WC_Product $product Product.
 * @return string
 */
function elite_shipping_container_tax_class( $class, $product ) {
	if ( elite_shipping_is_container_product( $product ) ) {
		return '';
	}

	return $class;
}
add_filter( 'woocommerce_product_get_tax_class', 'elite_shipping_container_tax_class', 10, 2 );
add_filter( 'woocommerce_product_variation_get_tax_class', 'elite_shipping_container_tax_class', 10, 2 );

/**
 * Fallback VAT rate when WooCommerce has no GB rate configured yet.
 *
 * @param array<string, array<string, mixed>> $rates Matched rates.
 * @param array<string, mixed>                $args  Lookup args.
 * @return array<string, array<string, mixed>>
 */
function elite_shipping_container_vat_fallback_rates( $rates, $args ) {
	if ( ! empty( $rates ) || ! elite_shipping_cart_has_containers() ) {
		return $rates;
	}

	$country = isset( $args['country'] ) ? (string) $args['country'] : '';

	if ( 'GB' !== $country && 'UK' !== $country ) {
		return $rates;
	}

	$tax_class = isset( $args['tax_class'] ) ? (string) $args['tax_class'] : '';
	if ( '' !== $tax_class ) {
		return $rates;
	}

	return array(
		'elite-container-vat' => array(
			'rate'     => (string) ELITE_CONTAINER_VAT_PERCENT,
			'label'    => elite_shipping_container_vat_display_label(),
			'shipping' => 'yes',
			'compound' => 'no',
		),
	);
}
add_filter( 'woocommerce_find_rates', 'elite_shipping_container_vat_fallback_rates', 20, 2 );

/**
 * Apply flat container delivery when the cart includes containers.
 *
 * @param array<string, WC_Shipping_Rate> $rates   Package rates.
 * @param array<string, mixed>            $package Shipping package.
 * @return array<string, WC_Shipping_Rate>
 */
function elite_shipping_apply_container_flat_shipping( $rates, $package ) {
	unset( $package );

	if ( ! elite_shipping_cart_has_containers() || ! class_exists( 'WC_Shipping_Rate' ) ) {
		return $rates;
	}

	$cost  = (float) apply_filters( 'elite_shipping_container_flat_shipping_cost', ELITE_CONTAINER_FLAT_SHIPPING_COST );
	$label = (string) apply_filters(
		'elite_shipping_container_flat_shipping_label',
		elite_shipping_container_delivery_fee_label()
	);

	$rate = new WC_Shipping_Rate(
		'elite_container_delivery',
		$label,
		$cost,
		array(),
		'elite_container_delivery'
	);

	return array(
		'elite_container_delivery' => $rate,
	);
}
add_filter( 'woocommerce_package_rates', 'elite_shipping_apply_container_flat_shipping', 100, 2 );

/**
 * Shipping totals row heading for container carts.
 *
 * @param string               $name           Default package name.
 * @param int                  $package_index  Package index.
 * @param array<string, mixed> $package        Shipping package.
 * @return string
 */
function elite_shipping_container_shipping_package_name( $name, $package_index, $package ) {
	unset( $package_index, $package );

	if ( elite_shipping_cart_has_containers() ) {
		return elite_shipping_container_delivery_fee_label();
	}

	return $name;
}
add_filter( 'woocommerce_shipping_package_name', 'elite_shipping_container_shipping_package_name', 20, 3 );

/**
 * Show only the delivery price in the totals value column.
 *
 * @param string           $label  Default method label.
 * @param WC_Shipping_Rate $method Shipping rate.
 * @return string
 */
function elite_shipping_container_shipping_method_full_label( $label, $method ) {
	if ( ! elite_shipping_cart_has_containers() || ! is_object( $method ) ) {
		return $label;
	}

	$method_id = method_exists( $method, 'get_method_id' ) ? (string) $method->get_method_id() : '';
	$rate_id   = isset( $method->id ) ? (string) $method->id : '';

	if ( 'elite_container_delivery' !== $method_id && 'elite_container_delivery' !== $rate_id && false === strpos( $rate_id, 'elite_container_delivery' ) ) {
		return $label;
	}

	$cost = (float) $method->cost;

	if ( WC()->cart && WC()->cart->display_prices_including_tax() ) {
		return wc_price( $cost + (float) $method->get_shipping_tax() );
	}

	return wc_price( $cost );
}
add_filter( 'woocommerce_cart_shipping_method_full_label', 'elite_shipping_container_shipping_method_full_label', 20, 2 );

/**
 * Show VAT as "VAT: 20%" on cart and checkout when containers are present.
 *
 * @param string $label Default tax label.
 * @return string
 */
function elite_shipping_container_tax_or_vat_label( $label ) {
	if ( elite_shipping_cart_has_containers() ) {
		return elite_shipping_container_vat_display_label();
	}

	return $label;
}
add_filter( 'woocommerce_countries_tax_or_vat', 'elite_shipping_container_tax_or_vat_label' );

/**
 * Itemized tax rows — use "VAT: 20%" for container carts.
 *
 * @param array<string, stdClass> $tax_totals Tax total rows.
 * @return array<string, stdClass>
 */
function elite_shipping_container_cart_tax_totals( $tax_totals ) {
	if ( ! elite_shipping_cart_has_containers() || empty( $tax_totals ) ) {
		return $tax_totals;
	}

	$label = elite_shipping_container_vat_display_label();

	foreach ( $tax_totals as $code => $tax ) {
		if ( isset( $tax_totals[ $code ]->label ) ) {
			$tax_totals[ $code ]->label = $label;
		}
	}

	return $tax_totals;
}
add_filter( 'woocommerce_cart_tax_totals', 'elite_shipping_container_cart_tax_totals' );

/**
 * Tax rate labels in totals tables.
 *
 * @param string $label Rate label.
 * @param string $key   Rate key.
 * @return string
 */
function elite_shipping_container_rate_label( $label, $key ) {
	unset( $key );

	if ( elite_shipping_cart_has_containers() ) {
		return elite_shipping_container_vat_display_label();
	}

	return $label;
}
add_filter( 'woocommerce_rate_label', 'elite_shipping_container_rate_label', 20, 2 );

/**
 * Default new customers to UK for tax and shipping calculation.
 *
 * @param string $location Default location.
 * @return string
 */
function elite_shipping_default_customer_location( $location ) {
	return 'GB';
}
add_filter( 'woocommerce_customer_default_location', 'elite_shipping_default_customer_location' );
