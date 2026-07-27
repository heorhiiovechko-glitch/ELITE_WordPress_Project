<?php
/**
 * My Account page — sidebar + content layout.
 *
 * @package Elite_Shipping
 * @see     woocommerce/templates/myaccount/my-account.php
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="apex-account-layout">
	<?php do_action( 'woocommerce_account_navigation' ); ?>

	<div class="woocommerce-MyAccount-content">
		<?php do_action( 'woocommerce_account_content' ); ?>
	</div>
</div>
