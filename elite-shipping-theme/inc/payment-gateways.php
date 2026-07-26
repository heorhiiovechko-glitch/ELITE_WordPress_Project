<?php
/**
 * Checkout payment gateways — ensures multiple methods appear at checkout.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme payment gateways.
 *
 * @param array<int, string> $gateways Gateway class names.
 * @return array<int, string>
 */
function elite_shipping_register_payment_gateways( $gateways ) {
	$gateways[] = 'Elite_Gateway_Payment_Options';
	$gateways[] = 'Elite_Gateway_Cards';
	$gateways[] = 'Elite_Gateway_PayPal';

	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'elite_shipping_register_payment_gateways' );

/**
 * Load gateway classes after WooCommerce is available.
 */
function elite_shipping_init_payment_gateways() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	if ( ! class_exists( 'Elite_Gateway_Base' ) ) {
		/**
		 * Shared checkout styling for theme gateways.
		 */
		abstract class Elite_Gateway_Base extends WC_Payment_Gateway {

			/**
			 * Render a mustard checkout alert.
			 *
			 * @param string $message Alert message.
			 */
			protected function render_checkout_alert( $message ) {
				echo '<div class="apex-payment-alert">';
				echo wp_kses_post( wpautop( wptexturize( $message ) ) );
				echo '</div>';
			}

			/**
			 * Optional save-payment checkbox for logged-in customers.
			 */
			protected function render_save_payment_checkbox() {
				if ( ! is_user_logged_in() ) {
					return;
				}

				echo '<p class="form-row woocommerce-SavedPaymentMethods-saveNew">';
				echo '<input id="' . esc_attr( $this->id . '_save' ) . '" type="checkbox" name="' . esc_attr( $this->id . '_save' ) . '" value="1" />';
				echo '<label for="' . esc_attr( $this->id . '_save' ) . '">';
				esc_html_e( 'Save payment information to my account for future purchases.', 'elite-shipping' );
				echo '</label></p>';
			}

			/**
			 * Place order on hold while payment is completed manually.
			 *
			 * @param int $order_id Order ID.
			 * @return array<string, string>
			 */
			public function process_payment( $order_id ) {
				$order = wc_get_order( $order_id );

				if ( ! $order ) {
					return array(
						'result'   => 'fail',
						'redirect' => '',
					);
				}

				if ( $order->get_total() > 0 ) {
					$order->update_status(
						'on-hold',
						sprintf(
							/* translators: %s: payment method title */
							__( 'Awaiting %s confirmation.', 'elite-shipping' ),
							$this->get_title()
						)
					);
				} else {
					$order->payment_complete();
				}

				if ( WC()->cart && $order->has_cart_hash( WC()->cart->get_cart_hash() ) ) {
					WC()->cart->empty_cart();
				}

				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				);
			}
		}
	}

	if ( ! class_exists( 'Elite_Gateway_Payment_Options' ) ) {
		/**
		 * Generic online payment options gateway.
		 */
		class Elite_Gateway_Payment_Options extends Elite_Gateway_Base {

			/**
			 * Constructor.
			 */
			public function __construct() {
				$this->id                 = 'elite_payment_options';
				$this->icon               = '';
				$this->has_fields         = true;
				$this->method_title       = __( 'Payment options', 'elite-shipping' );
				$this->method_description = __( 'General online payment option shown at checkout.', 'elite-shipping' );
				$this->supports           = array( 'products' );

				$this->init_form_fields();
				$this->init_settings();

				$this->title   = $this->get_option( 'title', __( 'Payment options', 'elite-shipping' ) );
				$this->enabled = $this->get_option( 'enabled', 'yes' );

				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			/**
			 * Admin settings.
			 */
			public function init_form_fields() {
				$this->form_fields = array(
					'enabled' => array(
						'title'   => __( 'Enable/Disable', 'elite-shipping' ),
						'type'    => 'checkbox',
						'label'   => __( 'Enable payment options', 'elite-shipping' ),
						'default' => 'yes',
					),
					'title'   => array(
						'title'   => __( 'Title', 'elite-shipping' ),
						'type'    => 'text',
						'default' => __( 'Payment options', 'elite-shipping' ),
					),
				);
			}

			/**
			 * Checkout fields.
			 */
			public function payment_fields() {
				$this->render_checkout_alert(
					__( 'Choose your preferred payment method below. Card, PayPal, and bank transfer are all available for UK orders.', 'elite-shipping' )
				);
				$this->render_save_payment_checkbox();
			}
		}
	}

	if ( ! class_exists( 'Elite_Gateway_Cards' ) ) {
		/**
		 * Debit and credit card gateway placeholder / fallback.
		 */
		class Elite_Gateway_Cards extends Elite_Gateway_Base {

			/**
			 * Constructor.
			 */
			public function __construct() {
				$this->id                 = 'elite_cards';
				$this->icon               = '';
				$this->has_fields         = true;
				$this->method_title       = __( 'Debit & Credit Cards', 'elite-shipping' );
				$this->method_description = __( 'Card payments at checkout.', 'elite-shipping' );
				$this->supports           = array( 'products' );

				$this->init_form_fields();
				$this->init_settings();

				$this->title   = $this->get_option( 'title', __( 'Debit & Credit Cards', 'elite-shipping' ) );
				$this->enabled = $this->get_option( 'enabled', 'yes' );

				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			/**
			 * Admin settings.
			 */
			public function init_form_fields() {
				$this->form_fields = array(
					'enabled' => array(
						'title'   => __( 'Enable/Disable', 'elite-shipping' ),
						'type'    => 'checkbox',
						'label'   => __( 'Enable debit & credit cards', 'elite-shipping' ),
						'default' => 'yes',
					),
					'title'   => array(
						'title'   => __( 'Title', 'elite-shipping' ),
						'type'    => 'text',
						'default' => __( 'Debit & Credit Cards', 'elite-shipping' ),
					),
				);
			}

			/**
			 * Checkout fields.
			 */
			public function payment_fields() {
				$this->render_checkout_alert(
					__( 'Pay securely by debit or credit card. Your order will be confirmed once payment is received.', 'elite-shipping' )
				);
				$this->render_save_payment_checkbox();
			}
		}
	}

	if ( ! class_exists( 'Elite_Gateway_PayPal' ) ) {
		/**
		 * PayPal gateway placeholder / fallback.
		 */
		class Elite_Gateway_PayPal extends Elite_Gateway_Base {

			/**
			 * Constructor.
			 */
			public function __construct() {
				$this->id                 = 'elite_paypal';
				$this->icon               = '';
				$this->has_fields         = true;
				$this->method_title       = __( 'PayPal', 'elite-shipping' );
				$this->method_description = __( 'PayPal payments at checkout.', 'elite-shipping' );
				$this->supports           = array( 'products' );

				$this->init_form_fields();
				$this->init_settings();

				$this->title   = $this->get_option( 'title', __( 'PayPal', 'elite-shipping' ) );
				$this->enabled = $this->get_option( 'enabled', 'yes' );

				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			/**
			 * Admin settings.
			 */
			public function init_form_fields() {
				$this->form_fields = array(
					'enabled' => array(
						'title'   => __( 'Enable/Disable', 'elite-shipping' ),
						'type'    => 'checkbox',
						'label'   => __( 'Enable PayPal', 'elite-shipping' ),
						'default' => 'yes',
					),
					'title'   => array(
						'title'   => __( 'Title', 'elite-shipping' ),
						'type'    => 'text',
						'default' => __( 'PayPal', 'elite-shipping' ),
					),
				);
			}

			/**
			 * Checkout fields.
			 */
			public function payment_fields() {
				$this->render_checkout_alert(
					__( 'Pay quickly and securely with PayPal. You will receive payment instructions after placing your order.', 'elite-shipping' )
				);
			}
		}
	}
}
add_action( 'plugins_loaded', 'elite_shipping_init_payment_gateways', 21 );

/**
 * Hide theme fallback gateways when real plugin gateways are available.
 *
 * @param array<string, WC_Payment_Gateway> $gateways Available gateways.
 * @return array<string, WC_Payment_Gateway>
 */
function elite_shipping_filter_checkout_payment_gateways( $gateways ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $gateways;
	}

	$has_live_cards = false;
	$has_live_paypal = false;
	$has_live_wcpay = false;

	foreach ( array( 'stripe', 'woocommerce_payments', 'ppcp-card-button-gateway', 'square_credit_card' ) as $gateway_id ) {
		if ( isset( $gateways[ $gateway_id ] ) ) {
			$has_live_cards = true;
			break;
		}
	}

	foreach ( array( 'ppcp-gateway', 'paypal', 'ppec_paypal' ) as $gateway_id ) {
		if ( isset( $gateways[ $gateway_id ] ) ) {
			$has_live_paypal = true;
			break;
		}
	}

	if ( isset( $gateways['woocommerce_payments'] ) ) {
		$has_live_wcpay = true;
	}

	if ( $has_live_wcpay || $has_live_cards ) {
		unset( $gateways['elite_payment_options'], $gateways['elite_cards'] );
	}

	if ( $has_live_paypal ) {
		unset( $gateways['elite_paypal'] );
	}

	return elite_shipping_sort_payment_gateways( $gateways );
}
add_filter( 'woocommerce_available_payment_gateways', 'elite_shipping_filter_checkout_payment_gateways', 50 );

/**
 * Sort checkout gateways to match the reference order.
 *
 * @param array<string, WC_Payment_Gateway> $gateways Available gateways.
 * @return array<string, WC_Payment_Gateway>
 */
function elite_shipping_sort_payment_gateways( $gateways ) {
	$preferred = array(
		'woocommerce_payments',
		'elite_payment_options',
		'stripe',
		'ppcp-card-button-gateway',
		'elite_cards',
		'ppcp-gateway',
		'paypal',
		'elite_paypal',
		'bacs',
	);

	$sorted = array();

	foreach ( $preferred as $gateway_id ) {
		if ( isset( $gateways[ $gateway_id ] ) ) {
			$sorted[ $gateway_id ] = $gateways[ $gateway_id ];
		}
	}

	foreach ( $gateways as $gateway_id => $gateway ) {
		if ( ! isset( $sorted[ $gateway_id ] ) ) {
			$sorted[ $gateway_id ] = $gateway;
		}
	}

	return $sorted;
}

/**
 * Enable default checkout gateways once.
 */
function elite_shipping_bootstrap_checkout_gateways() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$bootstrapped = get_option( 'elite_shipping_gateways_bootstrapped', '' );
	if ( $bootstrapped === ELITE_SHIPPING_VERSION ) {
		return;
	}

	$gateway_defaults = array(
		'woocommerce_bacs_settings'                 => array(
			'enabled' => 'yes',
			'title'   => __( 'Direct bank transfer', 'elite-shipping' ),
		),
		'woocommerce_elite_payment_options_settings' => array(
			'enabled' => 'yes',
			'title'   => __( 'Payment options', 'elite-shipping' ),
		),
		'woocommerce_elite_cards_settings'          => array(
			'enabled' => 'yes',
			'title'   => __( 'Debit & Credit Cards', 'elite-shipping' ),
		),
		'woocommerce_elite_paypal_settings'         => array(
			'enabled' => 'yes',
			'title'   => __( 'PayPal', 'elite-shipping' ),
		),
	);

	foreach ( $gateway_defaults as $option_name => $defaults ) {
		$settings = get_option( $option_name, array() );
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		$settings = array_merge( $settings, $defaults );
		update_option( $option_name, $settings );
	}

	update_option( 'elite_shipping_gateways_bootstrapped', ELITE_SHIPPING_VERSION );
}
add_action( 'init', 'elite_shipping_bootstrap_checkout_gateways', 20 );
