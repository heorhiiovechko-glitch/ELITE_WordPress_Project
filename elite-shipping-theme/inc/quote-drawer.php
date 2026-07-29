<?php
/**
 * Get a Quote slide-out drawer.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compact quote form for the header drawer.
 */
function elite_shipping_render_quote_drawer_form() {
	$container_types = elite_shipping_get_contact_container_types();
	$status          = isset( $_GET['quote'] ) ? sanitize_key( wp_unslash( $_GET['quote'] ) ) : '';

	if ( 'sent' === $status ) {
		echo '<p class="elite-quote-drawer-notice elite-quote-drawer-notice--success">' . esc_html__( 'Thank you — your quote request has been sent. We will get back to you shortly.', 'elite-shipping' ) . '</p>';
		return;
	}

	if ( 'error' === $status ) {
		echo '<p class="elite-quote-drawer-notice elite-quote-drawer-notice--error">' . esc_html__( 'Sorry, something went wrong. Please try again.', 'elite-shipping' ) . '</p>';
	}
	?>
	<form class="elite-quote-drawer-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="elite_contact_form">
		<input type="hidden" name="elite_contact_source" value="drawer">
		<?php wp_nonce_field( 'elite_contact_form', 'elite_contact_nonce' ); ?>
		<input type="text" name="elite_contact_company" value="" tabindex="-1" autocomplete="off" class="apex-contact-honeypot" aria-hidden="true">

		<p>
			<label for="elite-quote-name"><?php esc_html_e( 'Full Name', 'elite-shipping' ); ?> *</label>
			<input id="elite-quote-name" type="text" name="elite_contact_name" required maxlength="400" placeholder="<?php esc_attr_e( 'Jane Smith', 'elite-shipping' ); ?>">
		</p>
		<p>
			<label for="elite-quote-email"><?php esc_html_e( 'Email Address', 'elite-shipping' ); ?> *</label>
			<input id="elite-quote-email" type="email" name="elite_contact_email" required maxlength="400" placeholder="<?php esc_attr_e( 'you@example.com', 'elite-shipping' ); ?>">
		</p>
		<p>
			<label for="elite-quote-phone"><?php esc_html_e( 'Phone Number', 'elite-shipping' ); ?> *</label>
			<input id="elite-quote-phone" type="tel" name="elite_contact_phone" required maxlength="400" placeholder="<?php echo esc_attr( ELITE_CONTACT_PHONE ); ?>">
		</p>
		<p>
			<label for="elite-quote-location"><?php esc_html_e( 'Delivery Location', 'elite-shipping' ); ?> *</label>
			<input id="elite-quote-location" type="text" name="elite_contact_location" required maxlength="400" placeholder="<?php esc_attr_e( 'City, County or Postcode', 'elite-shipping' ); ?>">
		</p>
		<p>
			<label for="elite-quote-container"><?php esc_html_e( 'Container Type', 'elite-shipping' ); ?> *</label>
			<select id="elite-quote-container" name="elite_contact_container" required>
				<option value=""><?php esc_html_e( 'Select a category…', 'elite-shipping' ); ?></option>
				<?php foreach ( $container_types as $type ) : ?>
					<option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="elite-quote-quantity"><?php esc_html_e( 'Quantity', 'elite-shipping' ); ?> *</label>
			<input id="elite-quote-quantity" type="number" name="elite_contact_quantity" required min="1" max="100" placeholder="<?php esc_attr_e( 'e.g. 2', 'elite-shipping' ); ?>">
		</p>
		<p>
			<label for="elite-quote-requirements"><?php esc_html_e( 'Additional Requirements', 'elite-shipping' ); ?></label>
			<textarea id="elite-quote-requirements" name="elite_contact_requirements" rows="4" maxlength="2000" placeholder="<?php esc_attr_e( 'Special instructions, modifications, delivery access etc.', 'elite-shipping' ); ?>"></textarea>
		</p>
		<p class="elite-quote-drawer-form__submit">
			<button type="submit" class="elite-btn elite-btn-primary elite-quote-drawer-submit"><?php esc_html_e( 'Request Quote', 'elite-shipping' ); ?></button>
		</p>
	</form>
	<?php
}

/**
 * Render quote drawer markup in the footer.
 */
function elite_shipping_render_quote_drawer() {
	?>
	<div class="elite-quote-drawer-overlay" id="elite-quote-drawer-overlay" hidden aria-hidden="true"></div>
	<aside class="elite-quote-drawer" id="elite-quote-drawer" aria-label="<?php esc_attr_e( 'Get a quote', 'elite-shipping' ); ?>" hidden aria-hidden="true">
		<div class="elite-quote-drawer-head">
			<h2 class="elite-quote-drawer-title"><?php esc_html_e( 'Get a Quote', 'elite-shipping' ); ?></h2>
			<button type="button" class="elite-quote-drawer-close" aria-label="<?php esc_attr_e( 'Close', 'elite-shipping' ); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
				<span><?php esc_html_e( 'Close', 'elite-shipping' ); ?></span>
			</button>
		</div>
		<div class="elite-quote-drawer-body">
			<?php elite_shipping_render_quote_drawer_form(); ?>
		</div>
	</aside>
	<?php
}
add_action( 'wp_footer', 'elite_shipping_render_quote_drawer', 20 );
