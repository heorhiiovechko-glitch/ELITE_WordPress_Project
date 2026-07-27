<?php
/**
 * Tawk.to live chat widget.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELITE_TAWK_PROPERTY_ID', '6a661926846c4d1d49b06810' );
define( 'ELITE_TAWK_WIDGET_ID', '1jufd4gq6' );

add_action( 'wp_footer', 'elite_shipping_tawk_embed', 99 );

/**
 * Output Tawk.to live chat on the front end.
 */
function elite_shipping_tawk_embed() {
	if ( is_admin() ) {
		return;
	}

	$property_id = ELITE_TAWK_PROPERTY_ID;
	$widget_id   = ELITE_TAWK_WIDGET_ID;
	?>
	<!--Start of Tawk.to Script-->
	<script>
	var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
	(function(){
	var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
	s1.async=true;
	s1.src='https://embed.tawk.to/<?php echo esc_js( $property_id ); ?>/<?php echo esc_js( $widget_id ); ?>';
	s1.charset='UTF-8';
	s1.setAttribute('crossorigin','*');
	s0.parentNode.insertBefore(s1,s0);
	})();
	</script>
	<!--End of Tawk.to Script-->
	<?php
}
