<?php
/**
 * Live product search for the mobile navigation drawer.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Search published WooCommerce products by keyword.
 *
 * @param string $term  Search term.
 * @param int    $limit Max results.
 * @return array<int, array{id:int,title:string,url:string,price_html:string,image:string}>
 */
function elite_shipping_live_search_products( $term, $limit = 12 ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return array();
	}

	$term = trim( (string) $term );
	if ( strlen( $term ) < 2 ) {
		return array();
	}

	$limit = max( 1, min( 20, absint( $limit ) ) );
	$ids   = array();

	if ( class_exists( 'WC_Data_Store' ) ) {
		$data_store = WC_Data_Store::load( 'product' );
		if ( $data_store && method_exists( $data_store, 'search_products' ) ) {
			$ids = $data_store->search_products( $term, '', true, false, $limit );
		}
	}

	if ( empty( $ids ) ) {
		$query = new WP_Query(
			array(
				'post_type'              => 'product',
				'post_status'            => 'publish',
				's'                      => $term,
				'posts_per_page'         => $limit,
				'orderby'                => 'relevance',
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		if ( $query->have_posts() ) {
			$ids = wp_list_pluck( $query->posts, 'ID' );
		}
	}

	$results = array();

	foreach ( (array) $ids as $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! $product || ! $product->is_visible() ) {
			continue;
		}

		$thumb_id  = $product->get_image_id();
		$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );

		$results[] = array(
			'id'         => (int) $product_id,
			'title'      => $product->get_name(),
			'url'        => get_permalink( $product_id ),
			'price_html' => $product->get_price_html(),
			'image'      => $thumb_url ? (string) $thumb_url : '',
		);
	}

	return $results;
}

/**
 * AJAX: return live product search results.
 */
function elite_shipping_ajax_live_product_search() {
	check_ajax_referer( 'elite_live_search', 'nonce' );

	$term = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';

	if ( strlen( trim( $term ) ) < 2 ) {
		wp_send_json_success(
			array(
				'items' => array(),
				'count' => 0,
			)
		);
	}

	$items = elite_shipping_live_search_products( $term, 12 );

	wp_send_json_success(
		array(
			'items' => $items,
			'count' => count( $items ),
			'term'  => $term,
		)
	);
}
add_action( 'wp_ajax_elite_live_product_search', 'elite_shipping_ajax_live_product_search' );
add_action( 'wp_ajax_nopriv_elite_live_product_search', 'elite_shipping_ajax_live_product_search' );
