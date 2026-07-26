<?php
/**
 * Elite Shipping Containers — brand logo image.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$variant = isset( $args['variant'] ) ? $args['variant'] : 'light';
$height  = isset( $args['height'] ) ? (int) $args['height'] : 42;
$logo    = ELITE_SHIPPING_URI . '/assets/images/elite-logo.png';
$classes = 'elite-logo-img';

if ( 'dark' === $variant ) {
	$logo    = ELITE_SHIPPING_URI . '/assets/images/elite-logo-footer.png';
	$classes .= ' elite-logo-img--footer';
}
?>
<img
	class="<?php echo esc_attr( $classes ); ?>"
	src="<?php echo esc_url( $logo ); ?>"
	alt="<?php echo esc_attr( ELITE_COMPANY_NAME ); ?>"
	height="<?php echo esc_attr( $height ); ?>"
	width="auto"
	loading="eager"
	decoding="async"
/>
