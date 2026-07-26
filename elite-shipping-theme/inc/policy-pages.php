<?php
/**
 * Policy pages — registration, URLs, and default content.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared company details for policy pages.
 *
 * @return array{
 *   company_name: string,
 *   company_legal_name: string,
 *   website: string,
 *   website_url: string,
 *   phone: string,
 *   phone_href: string,
 *   email: string,
 *   address: string,
 *   address_url: string
 * }
 */
function elite_shipping_get_policy_company_details() {
	$contact = elite_shipping_get_contact_details();

	return array(
		'company_name'       => $contact['company_name'],
		'company_legal_name' => $contact['company_legal_name'],
		'website'            => $contact['website'],
		'website_url'        => $contact['website_url'],
		'phone'              => $contact['phone'],
		'phone_href'         => $contact['phone_href'],
		'email'              => $contact['email'],
		'address'            => $contact['address'],
		'address_url'        => $contact['address_url'],
	);
}

/**
 * Policy page definitions.
 *
 * @return array<string, array{title: string, kicker: string, intro: string, sections: array<int, array{heading: string, paragraphs: string[]}>}>
 */
function elite_shipping_get_policy_pages_config() {
	$details = elite_shipping_get_policy_company_details();
	$company = $details['company_legal_name'];
	$brand   = $details['company_name'];
	$website = $details['website'];
	$email   = $details['email'];
	$phone   = $details['phone'];
	$address = $details['address'];

	return array(
		'shipping-policy'      => array(
			'title'    => __( 'Shipping Policy', 'elite-shipping' ),
			'kicker'   => __( 'DELIVERY', 'elite-shipping' ),
			'intro'    => sprintf(
				/* translators: 1: company legal name, 2: website domain */
				__( 'Thank you for choosing %1$s. We are committed to delivering your order accurately, in good condition, and within an agreed timeframe. This Shipping Policy explains how delivery is arranged, timed, and priced for orders placed on %2$s.', 'elite-shipping' ),
				$company,
				$website
			),
			'sections' => array(
				array(
					'heading'    => __( '1. About Elite Shipping Containers', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: 1: brand name, 2: address */
							__( '%1$s is a UK supplier of new, used, and modified shipping containers. Our registered office is %2$s.', 'elite-shipping' ),
							$brand,
							$address
						),
					),
				),
				array(
					'heading'    => __( '2. UK Nationwide Delivery', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We deliver to commercial and residential addresses throughout the United Kingdom, subject to safe access for delivery vehicles, cranes, and offloading equipment.', 'elite-shipping' ),
						__( 'Please confirm that your site can accept container delivery before ordering. Restricted access, low bridges, or soft ground may require a pre-delivery survey.', 'elite-shipping' ),
						__( 'Typical container delivery times are arranged after order confirmation and usually fall within an agreed window based on location, unit availability, and site access. Business days exclude weekends and UK public holidays.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '3. Order Processing and Shipment', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Orders are processed Monday through Friday, excluding UK public holidays. Orders placed on weekends or holidays are processed on the next business day.', 'elite-shipping' ),
						__( 'After payment confirmation, our logistics team will contact you to arrange a suitable delivery date and time window.', 'elite-shipping' ),
						__( 'Someone must be available on site to accept delivery and confirm placement unless otherwise agreed in advance.', 'elite-shipping' ),
						__( 'Where tracking is available, you will receive confirmation and tracking details by email after dispatch.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '4. Shipping Costs', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Orders that include shipping containers are subject to a flat Container Delivery Fee of £237 per order unless otherwise quoted in writing by our sales team.', 'elite-shipping' ),
						__( 'Accessory-only orders may use alternative shipping rates calculated at checkout.', 'elite-shipping' ),
						__( 'Any site-specific services (for example crane hire, specialised offloading, or difficult access) may be quoted separately before delivery.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '5. Tracking Your Order', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Where a tracking number is provided, you can follow your shipment using the details in your confirmation email.', 'elite-shipping' ),
						__( 'It may take up to 24 hours after dispatch for tracking information to become active.', 'elite-shipping' ),
						sprintf(
							/* translators: 1: email address, 2: phone number */
							__( 'If you need delivery status updates, email %1$s or call %2$s with your order number.', 'elite-shipping' ),
							$email,
							$phone
						),
					),
				),
				array(
					'heading'    => __( '6. Lost or Damaged Shipments', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: 1: email address, 2: phone number */
							__( 'If your order is lost or damaged during transit, contact us as soon as possible at %1$s or %2$s with your order number and photos of any damage.', 'elite-shipping' ),
							$email,
							$phone
						),
						__( 'We will investigate promptly and work with the carrier or logistics partner to resolve the issue where appropriate.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '7. Changes to This Shipping Policy', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We reserve the right to update or modify this Shipping Policy at any time. The latest version will always be published on this page and takes effect when posted.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '8. Contact Us', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: 1: company legal name, 2: address, 3: email, 4: phone, 5: website */
							__( 'If you have questions about this Shipping Policy, contact %1$s. Address: %2$s. Email: %3$s. Phone: %4$s. Website: %5$s.', 'elite-shipping' ),
							$company,
							$address,
							$email,
							$phone,
							$website
						),
					),
				),
			),
		),
		'return-policy'        => array(
			'title'    => __( 'Return Policy', 'elite-shipping' ),
			'kicker'   => __( 'RETURNS', 'elite-shipping' ),
			'intro'    => sprintf(
				/* translators: %s: company legal name */
				__( '%s supplies large, specialised shipping containers and accessories. Because of their size and nature, returns are handled individually and in line with UK consumer law.', 'elite-shipping' ),
				$company
			),
			'sections' => array(
				array(
					'heading'    => __( 'Before You Order', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Please review product specifications, dimensions, condition grading, door type, and delivery access requirements carefully before purchase.', 'elite-shipping' ),
						sprintf(
							/* translators: %s: phone number */
							__( 'Our team at Elite Shipping Containers can help you choose the right unit. Call %s if you need advice before ordering.', 'elite-shipping' ),
							$phone
						),
					),
				),
				array(
					'heading'    => __( 'Return Eligibility', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Returns may be considered where an item is materially different from its description, arrives damaged, or cannot be delivered due to an error on our part.', 'elite-shipping' ),
						__( 'Custom modifications, bespoke conversions, and used containers sold as seen may not be eligible for return unless required by law.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( 'How to Request a Return', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: 1: email address, 2: phone number */
							__( 'Contact us within 7 days of delivery at %1$s or call %2$s with your order number, photos, and a clear description of the issue. We will review your request and advise next steps.', 'elite-shipping' ),
							$email,
							$phone
						),
					),
				),
				array(
					'heading'    => __( 'Collection and Restocking', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If a return is approved, collection may be arranged by us or by the customer depending on the circumstances. Any applicable restocking or collection costs will be confirmed in writing before proceeding.', 'elite-shipping' ),
					),
				),
			),
		),
		'refund-policy'        => array(
			'title'    => __( 'Refund Policy', 'elite-shipping' ),
			'kicker'   => __( 'REFUNDS', 'elite-shipping' ),
			'intro'    => sprintf(
				/* translators: %s: company legal name */
				__( 'This Refund Policy explains when %s may issue refunds and how they are processed.', 'elite-shipping' ),
				$company
			),
			'sections' => array(
				array(
					'heading'    => __( 'Approved Refunds', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If a return is approved, or an order is cancelled before dispatch due to our error or stock unavailability, a refund will be issued to the original payment method where possible.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( 'Processing Time', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Refunds are normally processed within 5–10 business days after approval. Bank and card processing times may vary depending on your provider.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( 'Non-Refundable Costs', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Container Delivery Fees, site-specific services, and completed custom work may be non-refundable once dispatch or fabrication has started, unless otherwise agreed in writing.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( 'Refund Enquiries', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: 1: email address, 2: phone number */
							__( 'For refund questions, email %1$s or call %2$s with your order number.', 'elite-shipping' ),
							$email,
							$phone
						),
					),
				),
			),
		),
		'terms-and-conditions' => array(
			'title'    => __( 'Terms and Conditions', 'elite-shipping' ),
			'kicker'   => __( 'LEGAL', 'elite-shipping' ),
			'intro'    => sprintf(
				/* translators: 1: company legal name, 2: website domain */
				__( 'Welcome to %1$s. These Terms and Conditions (“Terms”) govern your access to and use of %2$s (the “Website”), including the purchase of any goods or services offered through the Website. By accessing or using the Website, you agree to be bound by these Terms. If you do not agree with these Terms, please do not use our Website or services.', 'elite-shipping' ),
				$company,
				$website
			),
			'sections' => array(
				array(
					'heading'    => __( '1. Company Information', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: 1: company legal name, 2: address, 3: phone, 4: email, 5: website */
							__( '%1$s operates this Website. Registered address: %2$s. Phone: %3$s. Email: %4$s. Website: %5$s.', 'elite-shipping' ),
							$company,
							$address,
							$phone,
							$email,
							$website
						),
					),
				),
				array(
					'heading'    => __( '2. Use of the Website', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'You agree to use the Website for lawful purposes only. You must not use the Website in violation of any applicable laws or regulations, to infringe upon the rights of others, to upload or distribute viruses or malicious software, or to collect or track personal information of others without authorisation.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '3. Eligibility', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'By using this Website, you confirm that you are at least 18 years old (or have legal parental or guardian consent) and are fully able to enter into a binding agreement under applicable law.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '4. Products and Services', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We offer shipping containers, modifications, accessories, and related services for sale. All product descriptions, images, and pricing are for informational purposes and may change without notice.', 'elite-shipping' ),
						__( 'We reserve the right to modify or discontinue products at any time, correct errors or omissions in product information, and limit sales to any person, geographic region, or jurisdiction.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '5. Orders, Quotes and Payments', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'All orders are subject to acceptance and availability. Written quotations remain valid for the period stated on the quote. We reserve the right to cancel or refuse any order.', 'elite-shipping' ),
						__( 'By placing an order, you confirm that billing, delivery location details, and product specifications are correct, and you agree to pay all charges shown at checkout, including delivery fees and VAT where applicable.', 'elite-shipping' ),
						__( 'Payment methods, processing, and related rules are set out in our Payment Policy.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '6. Pricing and VAT', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Prices are shown in GBP. Container orders include VAT at 20% where applicable. Container Delivery Fee and tax totals are displayed during checkout.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '7. Shipping and Delivery', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Shipping and delivery are governed by our Shipping Policy. While we strive to ensure timely delivery, we are not liable for delays caused by third-party carriers, site access issues, or unforeseen circumstances outside our reasonable control.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '8. Returns and Refunds', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Please refer to our Return Policy and Refund Policy for information about returns, refunds, and exchanges. Approved refunds are issued to the original method of payment where possible.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '9. Risk and Ownership', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Ownership passes to you once full payment has cleared and the goods have been delivered or made available for collection, unless otherwise stated on your invoice.', 'elite-shipping' ),
						__( 'Risk in the goods transfers on delivery or collection, whichever occurs first.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '10. Intellectual Property', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: %s: company legal name */
							__( 'All content on the Website — including logos, text, images, and graphics — is the property of %s or its licensors and is protected by copyright, trademark, and other intellectual property laws. You may not use, reproduce, or distribute our content without prior written permission.', 'elite-shipping' ),
							$company
						),
					),
				),
				array(
					'heading'    => __( '11. Limitation of Liability', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: %s: company legal name */
							__( 'To the fullest extent permitted by law, %s will not be liable for any indirect, incidental, or consequential damages; loss of profits or revenue; errors or inaccuracies in Website content; or damages resulting from unauthorised access to your account or data.', 'elite-shipping' ),
							$company
						),
						__( 'Nothing in these Terms excludes or limits liability for death or personal injury caused by negligence, fraud, or any other liability that cannot be excluded under English law.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '12. Disclaimer of Warranties', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'The Website and related online services are provided on an “as is” and “as available” basis, without warranties of any kind, either express or implied, except where required by law. We do not guarantee that the Website will be error-free or uninterrupted.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '13. Governing Law', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'These Terms are governed by and construed in accordance with the laws of England and Wales. Any disputes arising under these Terms shall be subject to the exclusive jurisdiction of the courts of England and Wales.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '14. Changes to These Terms', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We reserve the right to update or change these Terms at any time. Changes will be posted on this page. Continued use of the Website after changes are posted constitutes acceptance of the revised Terms.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '15. Contact Us', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: 1: company legal name, 2: address, 3: email, 4: phone, 5: website */
							__( 'If you have questions about these Terms and Conditions, contact %1$s. Address: %2$s. Email: %3$s. Phone: %4$s. Website: %5$s.', 'elite-shipping' ),
							$company,
							$address,
							$email,
							$phone,
							$website
						),
					),
				),
			),
		),
		'privacy-policy'       => array(
			'title'    => __( 'Privacy Policy', 'elite-shipping' ),
			'kicker'   => __( 'PRIVACY', 'elite-shipping' ),
			'intro'    => sprintf(
				/* translators: 1: company legal name, 2: website domain */
				__( '%1$s (%2$s) respects your privacy and is committed to protecting personal information you share with us.', 'elite-shipping' ),
				$company,
				$website
			),
			'sections' => array(
				array(
					'heading'    => __( 'Who We Are', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: 1: company legal name, 2: address */
							__( 'The data controller is %1$s, located at %2$s.', 'elite-shipping' ),
							$company,
							$address
						),
					),
				),
				array(
					'heading'    => __( 'Information We Collect', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We may collect your name, contact details, billing and delivery information, order history, and communications when you request a quote, place an order, or contact support.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( 'How We Use Information', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We use your information to process orders, arrange delivery, provide customer support, improve our services, and comply with legal obligations.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( 'Data Security', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We apply appropriate technical and organisational measures to protect personal data. Payment processing is handled through secure third-party providers.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( 'Your Rights', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: 1: email address, 2: phone number */
							__( 'You may request access, correction, or deletion of your personal data by contacting %1$s or calling %2$s.', 'elite-shipping' ),
							$email,
							$phone
						),
					),
				),
			),
		),
		'payment-policy'       => array(
			'title'    => __( 'Payment Policy', 'elite-shipping' ),
			'kicker'   => __( 'PAYMENTS', 'elite-shipping' ),
			'intro'    => sprintf(
				/* translators: 1: company legal name, 2: website domain */
				__( 'This Payment Policy outlines accepted payment methods and how %1$s handles payments for orders placed on %2$s.', 'elite-shipping' ),
				$company,
				$website
			),
			'sections' => array(
				array(
					'heading'    => __( '1. Accepted Payment Methods', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We accept the following payment options for your convenience, subject to availability at checkout:', 'elite-shipping' ),
						__( 'Debit and credit cards (where enabled at checkout), PayPal, and bank transfer (BACS) for selected orders.', 'elite-shipping' ),
						__( 'We do not accept cash on delivery (COD), personal cheques, or money orders.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '2. Payment Processing', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'All online payments are processed securely through certified third-party payment providers. We do not store full card details on our servers.', 'elite-shipping' ),
						__( 'Card and PayPal payments are normally charged when your order is placed, unless a gateway applies an authorisation hold first.', 'elite-shipping' ),
						__( 'For bank transfer orders, dispatch and delivery scheduling begin only after cleared funds are received in our account. Please use your order number as the payment reference.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '3. Pricing, Invoices and Receipts', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Your order total includes product price, Container Delivery Fee where applicable, and VAT at 20% for qualifying container products.', 'elite-shipping' ),
						__( 'After a successful order, you will receive a confirmation email with your order and payment summary. Account holders can also view order history under My Account.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '4. Refunds and Cancellations', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: 1: email address, 2: phone number */
							__( 'To cancel an order, contact us as soon as possible at %1$s or %2$s with your order number.', 'elite-shipping' ),
							$email,
							$phone
						),
						__( 'Orders that have not yet been dispatched can usually be cancelled. Once fabrication, modification, or delivery has started, cancellation and refund options may be limited.', 'elite-shipping' ),
						__( 'Approved refunds are issued to the original payment method where possible. Bank and card processing times may vary. Full details are set out in our Refund Policy and Return Policy.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '5. Security', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Transactions on our website are protected using SSL encryption. Our site is monitored for suspicious activity.', 'elite-shipping' ),
						__( 'If we detect unusual payment activity, we may place a temporary hold on the order and contact you to verify the transaction before continuing.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '6. Changes to This Payment Policy', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We may update this Payment Policy from time to time. The latest version will always be published on this page and takes effect when posted.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '7. Contact Us', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: 1: company legal name, 2: address, 3: email, 4: phone, 5: website */
							__( 'If you have questions about this Payment Policy, contact %1$s. Address: %2$s. Email: %3$s. Phone: %4$s. Website: %5$s.', 'elite-shipping' ),
							$company,
							$address,
							$email,
							$phone,
							$website
						),
					),
				),
			),
		),
	);
}

/**
 * Render shared company contact block on policy pages.
 */
function elite_shipping_render_policy_contact_block() {
	$details = elite_shipping_get_policy_company_details();
	$urls    = elite_shipping_get_urls();
	?>
	<div class="apex-policy-contact">
		<p class="apex-policy-contact__kicker"><?php esc_html_e( 'Need help?', 'elite-shipping' ); ?></p>
		<h2 class="apex-policy-contact__title"><?php echo esc_html( $details['company_name'] ); ?></h2>
		<p class="apex-policy-contact__text"><?php esc_html_e( 'Our UK team can help with delivery, orders, and policy questions.', 'elite-shipping' ); ?></p>
		<ul class="apex-policy-contact__list">
			<li>
				<span class="apex-policy-contact__label"><?php esc_html_e( 'Phone', 'elite-shipping' ); ?></span>
				<a href="<?php echo esc_url( $details['phone_href'] ); ?>"><?php echo esc_html( $details['phone'] ); ?></a>
			</li>
			<li>
				<span class="apex-policy-contact__label"><?php esc_html_e( 'Email', 'elite-shipping' ); ?></span>
				<a href="mailto:<?php echo esc_attr( $details['email'] ); ?>"><?php echo esc_html( $details['email'] ); ?></a>
			</li>
			<li>
				<span class="apex-policy-contact__label"><?php esc_html_e( 'Address', 'elite-shipping' ); ?></span>
				<a href="<?php echo esc_url( $details['address_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $details['address'] ); ?></a>
			</li>
		</ul>
		<a class="apex-policy-contact__cta" href="<?php echo esc_url( $urls['contact'] ); ?>">
			<?php esc_html_e( 'Contact us', 'elite-shipping' ); ?>
		</a>
	</div>
	<?php
}

/**
 * Registered policy page slugs.
 *
 * @return string[]
 */
function elite_shipping_get_policy_page_slugs() {
	return array_keys( elite_shipping_get_policy_pages_config() );
}

/**
 * Policy page URLs.
 *
 * @return array<string, string>
 */
function elite_shipping_get_policy_urls() {
	$urls = array();

	foreach ( elite_shipping_get_policy_pages_config() as $slug => $config ) {
		$urls[ $slug ] = elite_shipping_get_page_url( $slug, '/' . $slug . '/' );
	}

	return $urls;
}

/**
 * Create policy pages if they do not exist.
 */
function elite_shipping_ensure_policy_pages() {
	if ( ! function_exists( 'wp_insert_post' ) ) {
		return;
	}

	foreach ( elite_shipping_get_policy_pages_config() as $slug => $config ) {
		$existing = get_page_by_path( $slug );
		if ( $existing instanceof WP_Post ) {
			continue;
		}

		wp_insert_post(
			array(
				'post_title'   => $config['title'],
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			)
		);
	}

	update_option( 'elite_shipping_policy_pages_created', 'yes' );
}
add_action( 'after_setup_theme', 'elite_shipping_ensure_policy_pages', 30 );

/**
 * Policy page footer links.
 *
 * @return array<int, array{label: string, url: string}>
 */
function elite_shipping_get_policy_footer_links() {
	$links = array();

	foreach ( elite_shipping_get_policy_pages_config() as $slug => $config ) {
		$links[] = array(
			'label' => $config['title'],
			'url'   => elite_shipping_get_page_url( $slug, '/' . $slug . '/' ),
		);
	}

	return $links;
}
