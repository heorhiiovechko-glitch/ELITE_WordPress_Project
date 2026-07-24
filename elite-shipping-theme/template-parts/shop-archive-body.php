<?php

/**

 * Shop / category archive body.

 *

 * @package Elite_Shipping

 */



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



$term         = get_queried_object();

$is_category  = is_product_category() && $term instanceof WP_Term;

$title        = $is_category ? $term->name : __( 'Shop', 'elite-shipping' );

$current_slug = $is_category ? $term->slug : '';

$hero_desc    = $is_category

	? __( 'Browse our range of quality shipping containers with nationwide UK delivery and expert support.', 'elite-shipping' )

	: __( 'Shop new, used, and modified shipping containers with transparent pricing and fast UK delivery.', 'elite-shipping' );

?>

<?php

get_template_part(

	'template-parts/page',

	'hero-bar',

	array(

		'kicker'   => $is_category ? 'CONTAINERS' : 'SHOP',

		'title'    => $title,

		'desc'     => $hero_desc,

		'image'    => elite_shipping_get_shop_hero_image(),

		'modifier' => 'apex-page-hero--shop',

	)

);

?>



<section class="apex-shop-archive">

	<div class="elite-container apex-shop-layout">

		<?php get_template_part( 'template-parts/shop', 'sidebar', array( 'current_slug' => $current_slug ) ); ?>



		<div class="apex-shop-main">

			<?php if ( woocommerce_product_loop() ) : ?>

				<?php

				$grid_cols = isset( $_GET['cols'] ) ? absint( wp_unslash( $_GET['cols'] ) ) : 4;

				if ( ! in_array( $grid_cols, array( 2, 4, 6 ), true ) ) {

					$grid_cols = 4;

				}

				get_template_part( 'template-parts/shop', 'toolbar' );

				?>



				<div class="apex-shop-products apex-shop-products--cols-<?php echo esc_attr( (string) $grid_cols ); ?>">

					<?php elite_render_wc_shop_product_loop( $grid_cols ); ?>

				</div>



				<div class="apex-shop-pagination">

					<?php woocommerce_pagination(); ?>

				</div>

			<?php else : ?>

				<p class="apex-empty"><?php esc_html_e( 'No products found in this category.', 'elite-shipping' ); ?></p>

			<?php endif; ?>

		</div>

	</div>

</section>


