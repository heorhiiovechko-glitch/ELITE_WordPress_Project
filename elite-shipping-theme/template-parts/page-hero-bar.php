<?php
/**
 * Page hero bar with image background.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$kicker   = isset( $args['kicker'] ) ? (string) $args['kicker'] : 'OUR BLOG';
$title    = isset( $args['title'] ) ? (string) $args['title'] : '';
$desc     = isset( $args['desc'] ) ? (string) $args['desc'] : '';
$image    = isset( $args['image'] ) ? (string) $args['image'] : '';
$modifier = isset( $args['modifier'] ) ? (string) $args['modifier'] : '';

$classes = trim( 'apex-page-hero apex-page-hero--bar ' . $modifier );
$style   = $image
	? 'background-image: linear-gradient(rgba(0, 18, 40, 0.72), rgba(0, 18, 40, 0.72)), url(' . esc_url( $image ) . ');'
	: '';
?>
<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $style ? ' style="' . esc_attr( $style ) . '"' : ''; ?>>
	<div class="elite-container">
		<?php if ( $kicker ) : ?>
			<span class="apex-kicker"><?php echo esc_html( $kicker ); ?></span>
		<?php endif; ?>
		<?php if ( $title ) : ?>
			<h1 class="apex-page-hero-title"><?php echo esc_html( $title ); ?></h1>
		<?php endif; ?>
		<?php if ( $desc ) : ?>
			<p class="apex-page-hero-desc"><?php echo esc_html( $desc ); ?></p>
		<?php endif; ?>
	</div>
</section>
