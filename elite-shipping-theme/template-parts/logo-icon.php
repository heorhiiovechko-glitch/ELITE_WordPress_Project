<?php
/**
 * Elite Shipping Containers — brand container icon.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$size = isset( $args['size'] ) ? (int) $args['size'] : 44;
?>
<svg class="elite-logo-icon" width="<?php echo esc_attr( $size ); ?>" height="<?php echo esc_attr( $size ); ?>" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
	<path d="M6 20L24 9L42 20V36L24 47L6 36V20Z" fill="#FF6600"/>
	<path d="M6 20L24 30L42 20L24 9L6 20Z" fill="#FF8533"/>
	<path d="M24 30V47L42 36V20L24 30Z" fill="#E65C00"/>
	<path d="M6 20V36L24 47V30L6 20Z" fill="#FF6600"/>
	<path d="M11 24V34" stroke="#001529" stroke-width="1.2" stroke-opacity="0.35"/>
	<path d="M16 27V37" stroke="#001529" stroke-width="1.2" stroke-opacity="0.35"/>
	<path d="M11 24H21" stroke="#001529" stroke-width="1.2" stroke-opacity="0.35"/>
	<path d="M28 26H37" stroke="#001529" stroke-width="1" stroke-opacity="0.25"/>
	<path d="M28 30H37" stroke="#001529" stroke-width="1" stroke-opacity="0.25"/>
	<path d="M28 34H37" stroke="#001529" stroke-width="1" stroke-opacity="0.25"/>
	<path d="M28 38H37" stroke="#001529" stroke-width="1" stroke-opacity="0.25"/>
</svg>
