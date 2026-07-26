<?php
/**
 * Custom Direct bank transfer (BACS) checkout output.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace default BACS gateway with themed checkout markup.
 *
 * @param array<int, string> $gateways Payment gateway class names.
 * @return array<int, string>
 */
function elite_shipping_register_bacs_gateway( $gateways ) {
	if ( ! class_exists( 'Elite_Gateway_BACS' ) ) {
		return $gateways;
	}

	foreach ( $gateways as $index => $gateway ) {
		if ( 'WC_Gateway_BACS' === $gateway ) {
			$gateways[ $index ] = 'Elite_Gateway_BACS';
		}
	}

	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'elite_shipping_register_bacs_gateway' );

/**
 * Load themed BACS gateway after WooCommerce is available.
 */
function elite_shipping_init_bacs_gateway() {
	if ( ! class_exists( 'WC_Gateway_BACS' ) || class_exists( 'Elite_Gateway_BACS' ) ) {
		return;
	}

	/**
	 * Styled BACS gateway for checkout.
	 */
	class Elite_Gateway_BACS extends WC_Gateway_BACS {

		/**
		 * Output checkout payment fields.
		 */
		public function payment_fields() {
			elite_shipping_render_bacs_payment_fields( $this );
		}
	}
}
add_action( 'plugins_loaded', 'elite_shipping_init_bacs_gateway', 20 );

/**
 * Render styled BACS payment fields for any BACS gateway instance.
 *
 * @param WC_Payment_Gateway $gateway Payment gateway.
 */
function elite_shipping_render_bacs_payment_fields( $gateway ) {
	if ( ! is_object( $gateway ) || ! method_exists( $gateway, 'get_description' ) ) {
		return;
	}

	$description  = $gateway->get_description();
	$accounts     = isset( $gateway->account_details ) ? $gateway->account_details : array();
	$has_accounts = elite_shipping_bacs_has_account_details( $accounts );

	if ( $description ) {
		$parsed = elite_shipping_parse_bacs_description( $description );

		$greeting       = array();
		$instructions   = array();

		foreach ( $parsed['intro'] as $line ) {
			if ( empty( $instructions ) && preg_match( '/^(hello|dear|hi)\b/i', $line ) ) {
				$greeting[] = $line;
			} else {
				$instructions[] = $line;
			}
		}

		if ( ! empty( $greeting ) ) {
			echo '<p class="apex-bacs-greeting">' . esc_html( implode( ' ', $greeting ) ) . '</p>';
		}

		if ( ! empty( $instructions ) ) {
			echo '<div class="apex-payment-alert">';
			echo wp_kses_post( wpautop( wptexturize( implode( "\n\n", $instructions ) ) ) );
			echo '</div>';
		}

		if ( ! $has_accounts && ! empty( $parsed['details'] ) ) {
			elite_shipping_render_bacs_detail_rows( $parsed['details'] );
		}
	}

	if ( $has_accounts ) {
		$bacs_gateway = $gateway instanceof WC_Gateway_BACS ? $gateway : null;
		elite_shipping_render_bacs_accounts( $accounts, $bacs_gateway );
	}
}

/**
 * Split free-text BACS description into intro and bank detail lines.
 *
 * @param string $description Gateway description.
 * @return array{intro: string[], details: string[]}
 */
function elite_shipping_parse_bacs_description( $description ) {
	$intro   = array();
	$details = array();

	$normalized = preg_replace( '/<\s*br\s*\/?>/i', "\n", $description );
	$normalized = preg_replace( '/<\/p>\s*<p[^>]*>/i', "\n\n", $normalized );
	$normalized = preg_replace( '/<\/?(?:p|div|li|strong|b)[^>]*>/i', "\n", $normalized );
	$normalized = html_entity_decode( wp_strip_all_tags( $normalized ) );
	$lines      = preg_split( '/\r\n|\r|\n/', $normalized );

	foreach ( $lines as $line ) {
		$line = trim( preg_replace( '/\s+/', ' ', $line ) );
		if ( '' === $line ) {
			continue;
		}

		if ( preg_match( '/^(beneficiary(?:\s+name)?|bank name|account number|routine number|routing number|sort code|iban|bic|bank address|account name)\s*:/i', $line ) ) {
			$details[] = $line;
			continue;
		}

		if ( empty( $details ) ) {
			$intro[] = $line;
		} else {
			$details[] = $line;
		}
	}

	return array(
		'intro'   => $intro,
		'details' => $details,
	);
}

/**
 * Render parsed bank detail lines.
 *
 * @param string[] $lines Detail lines.
 */
function elite_shipping_render_bacs_detail_rows( $lines ) {
	if ( empty( $lines ) ) {
		return;
	}

	echo '<div class="apex-bacs-details">';
	echo '<h4 class="apex-bacs-details-title">' . esc_html__( 'Bank details', 'elite-shipping' ) . '</h4>';
	echo '<dl class="apex-bacs-details-list">';

	foreach ( $lines as $line ) {
		if ( preg_match( '/^([^:]+):\s*(.+)$/', $line, $match ) ) {
			echo '<div class="apex-bacs-details-row">';
			echo '<dt>' . esc_html( trim( $match[1] ) ) . '</dt>';
			echo '<dd>' . esc_html( trim( $match[2] ) ) . '</dd>';
			echo '</div>';
			continue;
		}

		echo '<div class="apex-bacs-details-row apex-bacs-details-row--plain">';
		echo '<dd>' . esc_html( $line ) . '</dd>';
		echo '</div>';
	}

	echo '</dl>';
	echo '</div>';
}

/**
 * Check whether BACS account rows contain usable data.
 *
 * @param array<int, array<string, string>>|mixed $accounts BACS accounts.
 * @return bool
 */
function elite_shipping_bacs_has_account_details( $accounts ) {
	if ( ! is_array( $accounts ) ) {
		return false;
	}

	foreach ( $accounts as $account ) {
		if ( ! is_array( $account ) ) {
			continue;
		}

		unset( $account['account_name'] );
		if ( ! empty( array_filter( $account ) ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Render configured WooCommerce BACS accounts.
 *
 * @param array<int, array<string, string>> $accounts BACS accounts.
 * @param WC_Gateway_BACS|null              $gateway  Gateway instance.
 */
function elite_shipping_render_bacs_accounts( $accounts, $gateway = null ) {
	$country  = function_exists( 'WC' ) && WC()->countries ? WC()->countries->get_base_country() : 'GB';
	$sortcode = __( 'Sort code', 'woocommerce' );

	if ( $gateway instanceof WC_Gateway_BACS ) {
		$locales  = $gateway->get_country_locale();
		$sortcode = isset( $locales[ $country ]['sortcode']['label'] ) ? $locales[ $country ]['sortcode']['label'] : $sortcode;
	} elseif ( 'US' === $country ) {
		$sortcode = __( 'Routing number', 'woocommerce' );
	}

	$field_labels = array(
		'account_name'   => __( 'Beneficiary name', 'elite-shipping' ),
		'bank_name'      => __( 'Bank', 'woocommerce' ),
		'account_number' => __( 'Account number', 'woocommerce' ),
		'sort_code'      => $sortcode,
		'iban'           => __( 'IBAN', 'woocommerce' ),
		'bic'            => __( 'BIC', 'woocommerce' ),
	);

	foreach ( $accounts as $account ) {
		if ( empty( array_filter( $account ) ) ) {
			continue;
		}

		echo '<div class="apex-bacs-details">';
		echo '<h4 class="apex-bacs-details-title">' . esc_html__( 'Bank details', 'elite-shipping' ) . '</h4>';
		echo '<dl class="apex-bacs-details-list">';

		foreach ( $field_labels as $key => $label ) {
			if ( empty( $account[ $key ] ) ) {
				continue;
			}

			$value = wp_unslash( $account[ $key ] );
			$row_label = $label;

			if ( 'iban' === $key && preg_match( '/^bank address\s*:\s*(.+)$/i', $value, $address_match ) ) {
				$row_label = __( 'Bank address', 'elite-shipping' );
				$value     = trim( $address_match[1] );
			} elseif ( 'iban' === $key && ! preg_match( '/^[A-Z]{2}[0-9A-Z]{10,}$/i', preg_replace( '/\s+/', '', $value ) ) ) {
				continue;
			}

			echo '<div class="apex-bacs-details-row">';
			echo '<dt>' . esc_html( $row_label ) . '</dt>';
			echo '<dd>' . esc_html( $value ) . '</dd>';
			echo '</div>';
		}

		echo '</dl>';
		echo '</div>';
	}
}
