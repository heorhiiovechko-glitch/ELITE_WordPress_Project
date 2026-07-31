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
				__( 'Thank you for choosing %1$s. We are committed to providing a reliable, transparent, and efficient delivery service across the United Kingdom. This Shipping Policy explains how we process, dispatch, and deliver orders placed through %2$s and is designed to align with UK consumer expectations and Google Merchant Centre policies.', 'elite-shipping' ),
				$company,
				$website
			),
			'sections' => array(
				array(
					'heading'    => __( '1. About Elite Shipping Containers Ltd', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Elite Shipping Containers Ltd supplies new, used, and modified shipping containers to residential, commercial, and industrial customers throughout the United Kingdom.', 'elite-shipping' ),
						__( 'Registered Office:', 'elite-shipping' ),
						"Rainham House\nManor Way\nRainham RM13 8RH",
						sprintf(
							/* translators: %s: website domain */
							__( 'Website: %s', 'elite-shipping' ),
							$website
						),
					),
				),
				array(
					'heading'    => __( '2. Delivery Areas', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We deliver throughout mainland England, Scotland, and Wales. Deliveries to Northern Ireland, the Scottish Highlands, offshore islands, and other remote locations may be available but may require additional delivery charges or extended delivery times.', 'elite-shipping' ),
						__( 'If we are unable to deliver to your location, we will notify you before processing your order.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '3. Order Processing', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Orders are processed Monday to Friday, excluding UK bank holidays.', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Orders are processed after full payment has been received and verified.', 'elite-shipping' ),
						__( 'Once your order has been confirmed, our logistics team will contact you to arrange a convenient delivery date.', 'elite-shipping' ),
						__( 'Delivery dates are subject to stock availability, transport scheduling, weather conditions, and site accessibility.', 'elite-shipping' ),
						__( 'Orders placed outside business hours will be processed on the next working day.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '4. Delivery Timeframes', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Estimated delivery times are provided for guidance only and are not guaranteed.', 'elite-shipping' ),
						__( 'Typical delivery times are:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'In-stock containers: 2–7 business days', 'elite-shipping' ),
						__( 'Modified or customised containers: Delivery timeframe provided upon order confirmation.', 'elite-shipping' ),
						__( 'Remote locations: May require additional transit time.', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'If unexpected delays occur, we will contact you as soon as reasonably possible.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '5. Delivery Requirements', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Customers are responsible for ensuring the delivery location is suitable before placing an order.', 'elite-shipping' ),
						__( 'Please ensure:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Adequate access for delivery vehicles.', 'elite-shipping' ),
						__( 'Suitable ground conditions for unloading.', 'elite-shipping' ),
						__( 'Sufficient clearance from trees, buildings, cables, and other obstacles.', 'elite-shipping' ),
						__( 'Any required permissions have been obtained before delivery.', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'If delivery cannot be completed because the site is unsuitable or inaccessible, additional transportation or re-delivery charges may apply.', 'elite-shipping' ),
						__( 'An authorised person aged 18 or over must be present to receive and sign for the delivery unless alternative arrangements have been agreed in writing.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '6. Delivery Charges', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Delivery charges are clearly displayed before checkout.', 'elite-shipping' ),
						__( 'Unless otherwise agreed in writing, container delivery fees are charged per container by size/category:', 'elite-shipping' ),
					),
					'list'       => array(
						__( '8ft — £135', 'elite-shipping' ),
						__( '10ft — £175', 'elite-shipping' ),
						__( '16ft — £195', 'elite-shipping' ),
						__( '20ft — £210', 'elite-shipping' ),
						__( '30ft — £225', 'elite-shipping' ),
						__( '40ft — £240', 'elite-shipping' ),
						__( '45ft — £250', 'elite-shipping' ),
						__( '1-Trip Shipping Containers — £240', 'elite-shipping' ),
						__( 'Shipping Container Pool — £225', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'Additional charges may apply for:', 'elite-shipping' ),
					),
					'list_after' => array(
						__( 'Remote or offshore locations', 'elite-shipping' ),
						__( 'Crane hire', 'elite-shipping' ),
						__( 'Hiab or specialist lifting equipment', 'elite-shipping' ),
						__( 'Difficult site access', 'elite-shipping' ),
						__( 'Failed delivery attempts', 'elite-shipping' ),
						__( 'Customer-requested re-delivery', 'elite-shipping' ),
					),
					'paragraphs_final' => array(
						__( 'Accessory-only orders may be shipped using standard parcel delivery services, with shipping costs calculated during checkout.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '7. Shipment Confirmation and Tracking', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Once your order has been dispatched, you will receive a shipment confirmation email.', 'elite-shipping' ),
						__( 'Where tracking is available, the email will include:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Order number', 'elite-shipping' ),
						__( 'Delivery information', 'elite-shipping' ),
						__( 'Carrier details (where applicable)', 'elite-shipping' ),
						__( 'Tracking number or delivery reference', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'Tracking information may take up to 24 hours after dispatch to become active.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '8. Delivery Delays', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'While we make every effort to deliver within the estimated timeframe, delays may occasionally occur due to circumstances beyond our reasonable control, including:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Severe weather', 'elite-shipping' ),
						__( 'Road closures', 'elite-shipping' ),
						__( 'Traffic disruption', 'elite-shipping' ),
						__( 'Vehicle breakdown', 'elite-shipping' ),
						__( 'Supplier delays', 'elite-shipping' ),
						__( 'Industrial action', 'elite-shipping' ),
						__( 'Force majeure events', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'We will keep you informed if your delivery is significantly delayed.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '9. Failed Deliveries', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If delivery cannot be completed because:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'nobody is available to receive the order,', 'elite-shipping' ),
						__( 'access is restricted,', 'elite-shipping' ),
						__( 'the delivery site is unsuitable,', 'elite-shipping' ),
						__( 'or incorrect delivery information has been provided,', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'additional delivery or storage charges may be payable before re-delivery can be arranged.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '10. Damaged or Missing Deliveries', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Please inspect your order immediately upon delivery.', 'elite-shipping' ),
						__( 'If your shipment arrives damaged, incomplete, or appears to have been lost in transit, please notify us as soon as possible and, where applicable, within 48 hours of delivery.', 'elite-shipping' ),
						__( 'When reporting damage, please provide:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Your order number', 'elite-shipping' ),
						__( 'Photographs of the damage', 'elite-shipping' ),
						__( 'Photographs of the packaging (if applicable)', 'elite-shipping' ),
						__( 'A brief description of the issue', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'We will investigate promptly and work to resolve the matter as quickly as possible.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '11. Risk and Ownership', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Responsibility for the goods transfers to the customer once the delivery has been successfully completed at the agreed delivery location and acknowledged by the customer or their authorised representative.', 'elite-shipping' ),
						__( 'Ownership of the goods passes only after full payment has been received.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '12. Incorrect Delivery Information', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Customers are responsible for ensuring all delivery details are accurate.', 'elite-shipping' ),
						__( 'Elite Shipping Containers Ltd is not responsible for delays or additional costs resulting from incorrect addresses, inaccurate contact information, or failure to provide necessary site access details.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '13. Changes to Orders', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If you need to amend your delivery address or delivery date, please contact us as soon as possible.', 'elite-shipping' ),
						__( 'Once an order has entered dispatch or transport scheduling, changes may not be possible or may incur additional charges.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '14. Contact Us', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If you have any questions regarding this Shipping Policy or your delivery, please contact us:', 'elite-shipping' ),
						$company,
						__( 'Address:', 'elite-shipping' ),
						"Rainham House\nManor Way\nRainham RM13 8RH",
						sprintf( __( 'Email: %s', 'elite-shipping' ), $email ),
						sprintf( __( 'Telephone: %s', 'elite-shipping' ), $phone ),
						sprintf(
							/* translators: %s: website domain */
							__( 'Website: %s', 'elite-shipping' ),
							$website
						),
					),
				),
				array(
					'heading'    => __( 'Policy Updates', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: %s: company legal name */
							__( '%s reserves the right to amend this Shipping Policy at any time to reflect operational, legal, or regulatory changes. The most current version will always be published on our website and will apply to all orders placed after its publication.', 'elite-shipping' ),
							$company
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
				__( 'Thank you for choosing %s. We are committed to ensuring that every order is delivered as described and in good condition. This Return Policy explains your rights and our procedures for returns, refunds, and exchanges in accordance with UK consumer law and Google Merchant Center requirements.', 'elite-shipping' ),
				$company
			),
			'sections' => array(
				array(
					'heading'    => __( '1. Before You Order', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Please carefully review all product information before placing your order, including:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Container dimensions and specifications', 'elite-shipping' ),
						__( 'New or used condition grading', 'elite-shipping' ),
						__( 'Door configuration and features', 'elite-shipping' ),
						__( 'Delivery access requirements', 'elite-shipping' ),
						__( 'Any optional modifications or accessories', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'If you need assistance selecting the right container, our team is happy to help before you place your order.', 'elite-shipping' ),
						__( 'Contact us:', 'elite-shipping' ),
					),
					'list_after' => array(
						sprintf( __( 'Phone: %s', 'elite-shipping' ), $phone ),
						sprintf( __( 'Email: %s', 'elite-shipping' ), $email ),
					),
				),
				array(
					'heading'    => __( '2. Your Right to Cancel', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If you are purchasing as a consumer, you may have the right to cancel your order under the Consumer Contracts Regulations 2013, subject to applicable legal exceptions.', 'elite-shipping' ),
						__( 'Because shipping containers are large, heavy goods and may be made to your specifications, cancellation rights may vary depending on the product purchased.', 'elite-shipping' ),
						__( 'If your order has not yet been dispatched, please contact us immediately. If delivery has already been arranged or completed, collection and transport charges may apply where permitted by law.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '3. Return Eligibility', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'You may be eligible for a return, replacement, or refund if:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'The product arrives damaged during delivery.', 'elite-shipping' ),
						__( 'The product received is significantly different from its description.', 'elite-shipping' ),
						__( 'We supplied the wrong item.', 'elite-shipping' ),
						__( 'The product has a manufacturing defect that was not disclosed before purchase.', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'Returns are generally not accepted for:', 'elite-shipping' ),
					),
					'list_after' => array(
						__( 'Custom-built or modified containers.', 'elite-shipping' ),
						__( 'Bespoke conversions made to your specifications.', 'elite-shipping' ),
						__( 'Used containers sold with their stated condition, unless they are not as described or otherwise protected by your statutory rights.', 'elite-shipping' ),
						__( 'Products damaged after delivery due to misuse, neglect, or improper handling.', 'elite-shipping' ),
					),
					'paragraphs_final' => array(
						__( 'Nothing in this policy limits your statutory rights under UK consumer law.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '4. Reporting a Problem', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Please inspect your delivery as soon as it arrives.', 'elite-shipping' ),
						__( 'If there is any issue with your order, contact us within 7 days of delivery by providing:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Your order number', 'elite-shipping' ),
						__( 'Photographs of the product and any damage', 'elite-shipping' ),
						__( 'A clear description of the issue', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'Contact us at:', 'elite-shipping' ),
						sprintf( __( 'Email: %s', 'elite-shipping' ), $email ),
						sprintf( __( 'Phone: %s', 'elite-shipping' ), $phone ),
						__( 'We aim to respond to all return requests within 2 business days.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '5. Return Approval', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Returns must be authorised by Elite Shipping Containers Ltd before any collection or shipment is arranged.', 'elite-shipping' ),
						__( 'If your return is approved, we will provide instructions regarding:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Collection arrangements', 'elite-shipping' ),
						__( 'Return location (if applicable)', 'elite-shipping' ),
						__( 'Packaging requirements (where applicable)', 'elite-shipping' ),
						__( 'Any documentation required', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'Unauthorised returns may be refused.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '6. Collection and Restocking', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Due to the size and weight of shipping containers, approved returns may require specialist transport.', 'elite-shipping' ),
						__( 'Where a return is not the result of our error or a faulty product, the customer may be responsible for:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Collection costs', 'elite-shipping' ),
						__( 'Transport charges', 'elite-shipping' ),
						__( 'Reasonable restocking fees where permitted by law', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'Any applicable charges will always be confirmed in writing before collection is arranged.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '7. Refunds', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Once the returned item has been inspected and approved, any eligible refund will be processed to the original payment method.', 'elite-shipping' ),
						__( 'Where applicable, refunds are typically issued within 5–10 business days, although processing times may vary depending on your payment provider.', 'elite-shipping' ),
						__( 'Delivery, transport, or collection charges may not be refundable unless required by law or where the return results from our error.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '8. Damaged or Incorrect Deliveries', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If your container arrives damaged or you receive the wrong product, please notify us as soon as reasonably possible.', 'elite-shipping' ),
						__( 'Where appropriate, we will arrange one of the following at no additional cost to you:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'A replacement', 'elite-shipping' ),
						__( 'A repair', 'elite-shipping' ),
						__( 'A full or partial refund', 'elite-shipping' ),
						__( 'Collection of the incorrect item', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '9. Contact Us', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If you have any questions regarding this Return Policy or wish to request a return, please contact:', 'elite-shipping' ),
						$company,
						sprintf( __( 'Email: %s', 'elite-shipping' ), $email ),
						sprintf( __( 'Phone: %s', 'elite-shipping' ), $phone ),
						__( 'Our customer support team is available to assist you during normal business hours.', 'elite-shipping' ),
					),
				),
			),
		),
		'refund-policy'        => array(
			'title'    => __( 'Refund Policy', 'elite-shipping' ),
			'kicker'   => __( 'REFUNDS', 'elite-shipping' ),
			'intro'    => sprintf(
				/* translators: %s: company legal name */
				__( 'At %s, we want you to shop with confidence. If you\'re entitled to a refund, we\'ll make the process as straightforward and transparent as possible. This policy explains when refunds are available and how they\'re processed.', 'elite-shipping' ),
				$company
			),
			'sections' => array(
				array(
					'heading'    => __( '1. When You May Receive a Refund', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'You may be eligible for a refund if:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'You cancel your order before it has been dispatched, where your cancellation is permitted under UK consumer law.', 'elite-shipping' ),
						__( 'We are unable to fulfil your order due to stock availability.', 'elite-shipping' ),
						__( 'Your return has been approved under our Return Policy.', 'elite-shipping' ),
						__( 'The item arrives damaged, faulty, or significantly different from its description.', 'elite-shipping' ),
						__( 'We have made an error with your order, such as sending the wrong product.', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'Where a refund is approved, it will normally be issued using the same payment method used to place the order.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '2. How Long Refunds Take', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Once your refund has been approved, we\'ll process it as quickly as possible.', 'elite-shipping' ),
						__( 'Most refunds are completed within 5–10 business days. Depending on your bank or payment provider, it may take a little longer for the funds to appear in your account.', 'elite-shipping' ),
						__( 'If you haven\'t received your refund after this time, we recommend checking with your bank before contacting us.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '3. Charges That May Not Be Refundable', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Because shipping containers are large, specialist products, some costs may not be refundable once work has begun or services have been provided.', 'elite-shipping' ),
						__( 'These may include:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Delivery or transport charges after delivery has been completed.', 'elite-shipping' ),
						__( 'Collection or return transport costs where the return is not due to our error.', 'elite-shipping' ),
						__( 'Custom-built, modified, or bespoke containers once fabrication has started.', 'elite-shipping' ),
						__( 'Site-specific services or installation work that has already been carried out.', 'elite-shipping' ),
					),
					'paragraphs_final' => array(
						__( 'These conditions do not affect your statutory rights if the product is faulty, not as described, or does not meet legal requirements.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '4. Partial Refunds', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'In some situations, a partial refund may be appropriate. For example:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Only part of an order is returned.', 'elite-shipping' ),
						__( 'A deduction is permitted by law because the goods have been used beyond what is reasonably necessary to inspect them.', 'elite-shipping' ),
						__( 'Certain agreed delivery or service charges are non-refundable.', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'If this applies, we\'ll explain any deductions before your refund is processed.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '5. Need Help With a Refund?', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If you have any questions about your refund or would like an update on its progress, our team is here to help.', 'elite-shipping' ),
						sprintf( __( 'Email: %s', 'elite-shipping' ), $email ),
						sprintf( __( 'Phone: %s', 'elite-shipping' ), $phone ),
						__( 'To help us assist you quickly, please include your order number whenever you get in touch.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '6. Your Consumer Rights', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Nothing in this Refund Policy limits or affects your statutory rights under the Consumer Rights Act 2015, the Consumer Contracts Regulations 2013, or any other applicable UK consumer protection laws.', 'elite-shipping' ),
						__( 'If the goods you receive are faulty, not as described, or not fit for purpose, you may be entitled to a repair, replacement, price reduction, or refund in accordance with your legal rights.', 'elite-shipping' ),
					),
				),
			),
		),
		'terms-and-conditions' => array(
			'title'    => __( 'Terms and Conditions', 'elite-shipping' ),
			'kicker'   => __( 'LEGAL', 'elite-shipping' ),
			'intro'    => sprintf(
				/* translators: 1: company legal name, 2: website domain */
				__( 'Welcome to %1$s. These Terms and Conditions explain the rules for using our website and purchasing products or services from us. By accessing %2$s or placing an order, you agree to these Terms. If you do not agree, please do not use our website or services.', 'elite-shipping' ),
				$company,
				$website
			),
			'sections' => array(
				array(
					'heading'    => __( '1. About Elite Shipping Containers Ltd', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Elite Shipping Containers Ltd supplies new, used, and modified shipping containers, container accessories, and related services throughout the United Kingdom.', 'elite-shipping' ),
						__( 'Registered Address:', 'elite-shipping' ),
						"Rainham House\nManor Way\nRainham RM13 8RH",
						sprintf( __( 'Email: %s', 'elite-shipping' ), $email ),
						sprintf( __( 'Phone: %s', 'elite-shipping' ), $phone ),
						sprintf( __( 'Website: %s', 'elite-shipping' ), $website ),
					),
				),
				array(
					'heading'    => __( '2. Using Our Website', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We ask that you use our website responsibly and only for lawful purposes.', 'elite-shipping' ),
						__( 'When using our website, you agree not to:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Break any applicable laws or regulations.', 'elite-shipping' ),
						__( 'Attempt to gain unauthorised access to our systems or customer information.', 'elite-shipping' ),
						__( 'Upload viruses, malware, or any harmful software.', 'elite-shipping' ),
						__( 'Interfere with the operation or security of our website.', 'elite-shipping' ),
						__( 'Copy or reproduce our content without our written permission.', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'If we believe our website is being misused, we may suspend or restrict access without prior notice.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '3. Who Can Place an Order?', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'By placing an order, you confirm that:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'You are at least 18 years old, or you have permission from a parent or legal guardian.', 'elite-shipping' ),
						__( 'You are legally able to enter into a binding contract.', 'elite-shipping' ),
						__( 'The information you provide is accurate, complete, and up to date.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '4. Our Products', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We offer a range of new, used, and customised shipping containers, together with accessories and related services.', 'elite-shipping' ),
						__( 'We make every effort to ensure that product descriptions, photographs, specifications, and prices are accurate. However, occasional errors may occur, and images are provided for illustration purposes only.', 'elite-shipping' ),
						__( 'We reserve the right to:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Correct any errors or omissions.', 'elite-shipping' ),
						__( 'Update prices or product information.', 'elite-shipping' ),
						__( 'Withdraw products from sale.', 'elite-shipping' ),
						__( 'Refuse or limit orders where necessary.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '5. Orders', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Submitting an order does not automatically mean it has been accepted.', 'elite-shipping' ),
						__( 'Your order becomes binding only after we have confirmed it and, where applicable, successfully received your payment.', 'elite-shipping' ),
						__( 'In some circumstances, we may need to cancel or refuse an order. This may happen if:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'A product is no longer available.', 'elite-shipping' ),
						__( 'Payment cannot be authorised.', 'elite-shipping' ),
						__( 'Pricing or product information contains an error.', 'elite-shipping' ),
						__( 'We reasonably suspect fraudulent activity.', 'elite-shipping' ),
						__( 'Delivery cannot safely or reasonably be completed.', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'If we cancel an order after payment has been received, any eligible refund will be processed in accordance with our Refund Policy.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '6. Prices and VAT', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'All prices shown on our website are in Pounds Sterling (GBP).', 'elite-shipping' ),
						__( 'VAT is included where applicable and will be clearly shown during checkout, together with any delivery charges before you complete your purchase.', 'elite-shipping' ),
						__( 'Although we take great care to ensure our prices are accurate, genuine pricing errors may occasionally occur. If this happens, we\'ll contact you before processing your order.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '7. Payment', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Payment must be received before goods are dispatched unless we\'ve agreed otherwise in writing.', 'elite-shipping' ),
						__( 'Our accepted payment methods are displayed during checkout.', 'elite-shipping' ),
						__( 'We use trusted payment providers to process transactions securely. While we take every reasonable step to protect your information, we cannot be responsible for delays caused by banks or payment service providers.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '8. Delivery', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Estimated delivery times are provided as a guide only.', 'elite-shipping' ),
						__( 'While we do everything we reasonably can to deliver orders on time, delays may occur due to circumstances outside our control, including severe weather, transport disruptions, supplier delays, or site access issues.', 'elite-shipping' ),
						__( 'For full details, please refer to our Shipping Policy.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '9. Returns and Refunds', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If you need to return a product or request a refund, please refer to our Return Policy and Refund Policy.', 'elite-shipping' ),
						__( 'Nothing in these Terms affects your statutory rights under UK consumer protection legislation.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '10. Ownership and Risk', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Ownership of your goods passes to you once full payment has been received and the goods have been delivered or collected.', 'elite-shipping' ),
						__( 'Responsibility for the goods transfers to you when delivery is completed or when you collect your order.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '11. Intellectual Property', 'elite-shipping' ),
					'paragraphs' => array(
						sprintf(
							/* translators: %s: company legal name */
							__( 'Everything on this website—including our logo, text, images, graphics, and other content—is owned by or licensed to %s and is protected by intellectual property laws.', 'elite-shipping' ),
							$company
						),
						__( 'You may not copy, reproduce, distribute, or use any content from this website without our prior written permission.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '12. Our Liability', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Nothing in these Terms limits or excludes any liability that cannot legally be excluded under UK law, including liability for death or personal injury caused by negligence or for fraud.', 'elite-shipping' ),
						__( 'To the extent permitted by law, we are not responsible for indirect or consequential losses, including loss of profits, business interruption, or loss of data arising from the use of our website or products.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '13. Website Availability', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We work hard to keep our website running smoothly, but we cannot guarantee that it will always be available or free from interruptions.', 'elite-shipping' ),
						__( 'From time to time, we may temporarily suspend access to carry out maintenance, improve our services, or address technical or security issues.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '14. Privacy', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We respect your privacy and handle your personal information in accordance with our Privacy Policy and applicable UK data protection laws.', 'elite-shipping' ),
						__( 'By using our website, you agree to the way we collect, use, and protect your information as described in our Privacy Policy.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '15. Changes to These Terms', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We may update these Terms and Conditions from time to time to reflect changes in our business, legal requirements, or the services we provide.', 'elite-shipping' ),
						__( 'Any updates will be published on this page, and the revised Terms will take effect from the date they are posted.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '16. Governing Law', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'These Terms and Conditions are governed by the laws of England and Wales.', 'elite-shipping' ),
						__( 'Any dispute arising from these Terms or your use of our website will be subject to the exclusive jurisdiction of the courts of England and Wales.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '17. Contact Us', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If you have any questions about these Terms and Conditions or need assistance with your order, we\'d be happy to help.', 'elite-shipping' ),
						$company,
						__( 'Registered Address:', 'elite-shipping' ),
						"Rainham House\nManor Way\nRainham RM13 8RH",
						sprintf( __( 'Email: %s', 'elite-shipping' ), $email ),
						sprintf( __( 'Phone: %s', 'elite-shipping' ), $phone ),
						sprintf( __( 'Website: %s', 'elite-shipping' ), $website ),
						__( 'Our customer support team is committed to providing prompt, friendly, and professional assistance whenever you need it.', 'elite-shipping' ),
					),
				),
			),
		),
		'privacy-policy'       => array(
			'title'    => __( 'Privacy Policy', 'elite-shipping' ),
			'kicker'   => __( 'PRIVACY', 'elite-shipping' ),
			'intro'    => sprintf(
				/* translators: 1: company legal name, 2: website domain */
				__( 'At %1$s, we take your privacy seriously. We understand the importance of protecting your personal information and are committed to handling it responsibly, securely, and in line with UK data protection laws. This Privacy Policy explains what information we collect, how we use it, when we may share it, and the rights you have over your personal data when you use %2$s or purchase products from us.', 'elite-shipping' ),
				$company,
				$website
			),
			'sections' => array(
				array(
					'heading'    => __( '1. Who We Are', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Elite Shipping Containers Ltd is the organisation responsible for collecting and managing the personal information you provide through our website.', 'elite-shipping' ),
						__( 'Registered Address:', 'elite-shipping' ),
						"Rainham House\nManor Way\nRainham RM13 8RH",
						sprintf( __( 'Website: %s', 'elite-shipping' ), $website ),
						sprintf( __( 'Email: %s', 'elite-shipping' ), $email ),
						sprintf( __( 'Phone: %s', 'elite-shipping' ), $phone ),
						__( 'If you have any questions about this Privacy Policy or how we use your information, please don\'t hesitate to contact us.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '2. The Information We Collect', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Depending on how you interact with us, we may collect information such as:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Your name', 'elite-shipping' ),
						__( 'Email address', 'elite-shipping' ),
						__( 'Telephone number', 'elite-shipping' ),
						__( 'Billing and delivery addresses', 'elite-shipping' ),
						__( 'Company details (where applicable)', 'elite-shipping' ),
						__( 'Order history and purchase information', 'elite-shipping' ),
						__( 'Messages or enquiries you send to us', 'elite-shipping' ),
						__( 'Payment confirmation details (we never store your full payment card details)', 'elite-shipping' ),
						__( 'Technical information, including your IP address, browser type, device information, and website activity collected through cookies and similar technologies', 'elite-shipping' ),
					),
					'paragraphs_final' => array(
						__( 'We only collect the information we genuinely need to provide our products and services or to meet our legal obligations.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '3. How We Use Your Information', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Your personal information helps us provide a better service. We use it to:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Process and manage your orders', 'elite-shipping' ),
						__( 'Arrange deliveries and collections', 'elite-shipping' ),
						__( 'Respond to your enquiries and provide customer support', 'elite-shipping' ),
						__( 'Process returns and refunds', 'elite-shipping' ),
						__( 'Send order updates and important service notifications', 'elite-shipping' ),
						__( 'Improve our website, products, and customer experience', 'elite-shipping' ),
						__( 'Help prevent fraud and protect our business', 'elite-shipping' ),
						__( 'Meet our legal, tax, and regulatory responsibilities', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'If you choose to receive marketing emails from us, you can unsubscribe at any time by following the instructions in the email or by contacting us directly.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '4. When We Share Your Information', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We never sell or rent your personal information to third parties.', 'elite-shipping' ),
						__( 'Where necessary, we may share your information with trusted service providers who help us operate our business, including:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Delivery and transport companies', 'elite-shipping' ),
						__( 'Secure payment providers', 'elite-shipping' ),
						__( 'Website hosting and IT support providers', 'elite-shipping' ),
						__( 'Professional advisers, such as accountants or legal advisers', 'elite-shipping' ),
						__( 'Government authorities where disclosure is required by law', 'elite-shipping' ),
					),
					'paragraphs_final' => array(
						__( 'These organisations are only permitted to use your information for the services they provide to us and must keep it secure.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '5. Keeping Your Information Safe', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Protecting your personal information is important to us.', 'elite-shipping' ),
						__( 'We use appropriate technical and organisational security measures to help safeguard your data against unauthorised access, loss, misuse, or disclosure.', 'elite-shipping' ),
						__( 'All online payments are processed through secure third-party payment providers. For your security, we do not store your full payment card details on our website.', 'elite-shipping' ),
						__( 'While no online system can guarantee complete security, we continually review and improve our security measures to help keep your information protected.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '6. Cookies', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Like most websites, we use cookies to make your browsing experience smoother and more personalised.', 'elite-shipping' ),
						__( 'Cookies help us remember your preferences, understand how visitors use our website, and improve the performance of our services.', 'elite-shipping' ),
						__( 'You can control or disable cookies through your browser settings at any time. Please note that disabling certain cookies may affect how some parts of our website function.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '7. How Long We Keep Your Information', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We only keep your personal information for as long as it\'s needed to:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Complete your order and provide our services', 'elite-shipping' ),
						__( 'Meet our legal and accounting obligations', 'elite-shipping' ),
						__( 'Resolve disputes', 'elite-shipping' ),
						__( 'Enforce our agreements', 'elite-shipping' ),
					),
					'paragraphs_final' => array(
						__( 'Once your information is no longer required, it is securely deleted or anonymised.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '8. Your Privacy Rights', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Under the UK General Data Protection Regulation (UK GDPR), you have the right to:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Request a copy of the personal information we hold about you.', 'elite-shipping' ),
						__( 'Ask us to correct inaccurate or incomplete information.', 'elite-shipping' ),
						__( 'Request the deletion of your personal information where appropriate.', 'elite-shipping' ),
						__( 'Object to or restrict certain types of processing.', 'elite-shipping' ),
						__( 'Request a copy of your information in a portable format where applicable.', 'elite-shipping' ),
						__( 'Withdraw your consent where we rely on it to process your information.', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'If you would like to exercise any of these rights, simply contact us using the details below.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '9. Links to Other Websites', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'Our website may include links to third-party websites for your convenience.', 'elite-shipping' ),
						__( 'Please note that we are not responsible for the privacy practices or content of external websites. We recommend reading their privacy policies before providing any personal information.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '10. Changes to This Privacy Policy', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'From time to time, we may update this Privacy Policy to reflect changes in our business, legal requirements, or the way we handle personal information.', 'elite-shipping' ),
						__( 'Any updates will be published on this page, and the revised policy will take effect from the date it is posted.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '11. Contact Us', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If you have any questions about this Privacy Policy or would like to exercise your data protection rights, we\'re here to help.', 'elite-shipping' ),
						$company,
						__( 'Registered Address:', 'elite-shipping' ),
						"Rainham House\nManor Way\nRainham RM13 8RH",
						sprintf( __( 'Email: %s', 'elite-shipping' ), $email ),
						sprintf( __( 'Phone: %s', 'elite-shipping' ), $phone ),
						__( 'If you\'re not satisfied with how we\'ve handled your personal information, you also have the right to raise a concern with the Information Commissioner\'s Office (ICO), the UK\'s independent authority for data protection.', 'elite-shipping' ),
						__( 'We are committed to treating your personal information with care, transparency, and respect, so you can use our website and services with confidence.', 'elite-shipping' ),
					),
				),
			),
		),
		'payment-policy'       => array(
			'title'    => __( 'Payment Policy', 'elite-shipping' ),
			'kicker'   => __( 'PAYMENTS', 'elite-shipping' ),
			'intro'    => sprintf(
				/* translators: 1: company legal name, 2: website domain */
				__( 'At %1$s, we want paying for your order to be simple, secure, and hassle-free. This Payment Policy explains the payment methods we accept, how payments are processed, and what happens after you place an order through %2$s.', 'elite-shipping' ),
				$company,
				$website
			),
			'sections' => array(
				array(
					'heading'    => __( '1. Payment Methods We Accept', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We offer secure and convenient payment options to make ordering as easy as possible.', 'elite-shipping' ),
						__( 'Depending on availability, you can pay using:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'Debit or credit card', 'elite-shipping' ),
						__( 'PayPal', 'elite-shipping' ),
						__( 'Bank transfer (BACS) for selected orders', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'For security and operational reasons, we do not accept:', 'elite-shipping' ),
					),
					'list_after' => array(
						__( 'Cash on delivery', 'elite-shipping' ),
						__( 'Personal cheques', 'elite-shipping' ),
						__( 'Money orders', 'elite-shipping' ),
					),
					'paragraphs_final' => array(
						__( 'The available payment options for your order will always be shown during checkout.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '2. How Payments Are Processed', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'All online payments are processed securely through trusted payment providers.', 'elite-shipping' ),
						__( 'For your protection:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'We do not store your full card details on our website.', 'elite-shipping' ),
						__( 'Your payment information is handled securely by approved payment providers.', 'elite-shipping' ),
						__( 'Transactions are protected using secure encryption technology.', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'Card and PayPal payments are normally taken when your order is placed. In some cases, your payment provider may first place a temporary authorisation hold before completing the transaction.', 'elite-shipping' ),
						__( 'For payments made by bank transfer:', 'elite-shipping' ),
					),
					'list_after' => array(
						__( 'Your order will only move forward once cleared funds have been received.', 'elite-shipping' ),
						__( 'Delivery arrangements or any required work will begin after payment confirmation.', 'elite-shipping' ),
						__( 'Please include your order number as the payment reference so we can match your payment quickly.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '3. Pricing, VAT and Order Confirmation', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'All prices on our website are displayed in GBP (£).', 'elite-shipping' ),
						__( 'Your final order amount may include:', 'elite-shipping' ),
					),
					'list'       => array(
						__( 'The cost of your selected container or product', 'elite-shipping' ),
						__( 'Delivery or transport charges where applicable', 'elite-shipping' ),
						__( 'VAT where required', 'elite-shipping' ),
					),
					'paragraphs_after' => array(
						__( 'Before completing your purchase, you will have the opportunity to review your order details and confirm the total amount payable.', 'elite-shipping' ),
						__( 'After your order has been successfully placed, we will send you an order confirmation with your purchase details and payment information.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '4. Invoices and Receipts', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We provide electronic order confirmations and invoices where applicable.', 'elite-shipping' ),
						__( 'If you need a copy of your invoice or payment receipt, please contact our customer support team and we will be happy to assist.', 'elite-shipping' ),
						__( 'Customers with an online account may also be able to view their previous order information through their account area.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '5. Cancelling an Order and Refunds', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If you need to cancel your order, please contact us as soon as possible.', 'elite-shipping' ),
						sprintf( __( 'Email: %s', 'elite-shipping' ), $email ),
						sprintf( __( 'Phone: %s', 'elite-shipping' ), $phone ),
						__( 'Where an order has not yet been processed, dispatched, modified, or prepared for delivery, cancellation may be possible.', 'elite-shipping' ),
						__( 'Please note that shipping containers are specialist products, and some orders may involve transport arrangements, fabrication, or custom modifications. Once these processes have started, cancellation or refund options may be limited.', 'elite-shipping' ),
						__( 'Any approved refund will normally be returned to the original payment method used.', 'elite-shipping' ),
						__( 'For more information, please see our Refund Policy and Return Policy.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '6. Keeping Your Payments Secure', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We take payment security seriously.', 'elite-shipping' ),
						__( 'Our website uses SSL encryption to help protect your personal and payment information during checkout.', 'elite-shipping' ),
						__( 'We also monitor transactions for unusual activity. If a payment appears suspicious, we may temporarily delay processing and contact you to confirm certain details before continuing with your order.', 'elite-shipping' ),
						__( 'This helps protect our customers and maintain a safe shopping environment.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '7. Payment Issues', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If your payment does not go through, please check your payment details and try again.', 'elite-shipping' ),
						__( 'If the issue continues, you may need to contact your bank, card provider, or payment service provider.', 'elite-shipping' ),
						__( 'If you need help completing your payment, our team will be happy to assist.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '8. Updates to This Payment Policy', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'We may update this Payment Policy from time to time to reflect changes to our payment methods, services, or legal requirements.', 'elite-shipping' ),
						__( 'Any updates will be published on this page, and the latest version will apply from the date it is posted.', 'elite-shipping' ),
					),
				),
				array(
					'heading'    => __( '9. Contact Us', 'elite-shipping' ),
					'paragraphs' => array(
						__( 'If you have any questions about payments, invoices, or your order, please contact us.', 'elite-shipping' ),
						$company,
						__( 'Registered Address:', 'elite-shipping' ),
						"Rainham House\nManor Way\nRainham RM13 8RH",
						sprintf( __( 'Email: %s', 'elite-shipping' ), $email ),
						sprintf( __( 'Phone: %s', 'elite-shipping' ), $phone ),
						sprintf( __( 'Website: %s', 'elite-shipping' ), $website ),
						__( 'We are committed to providing a secure, transparent, and reliable payment experience for all our customers.', 'elite-shipping' ),
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
