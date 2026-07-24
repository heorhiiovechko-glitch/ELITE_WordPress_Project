<?php
/**
 * WooCommerce fallback template (single product, cart, checkout, etc.).
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main class="elite-wc-main">
	<div class="elite-container">
		<?php woocommerce_content(); ?>
	</div>
</main>
<?php
get_footer();
