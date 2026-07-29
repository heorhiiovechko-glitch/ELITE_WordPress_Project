<?php
/**
 * FAQ page content helpers.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build an HTML bullet list for FAQ answers.
 *
 * @param array<int, string> $items List items.
 * @return string
 */
function elite_shipping_faq_list_html( $items ) {
	if ( empty( $items ) ) {
		return '';
	}

	$html = '<ul class="apex-faq-item__list">';

	foreach ( $items as $item ) {
		$html .= '<li>' . esc_html( $item ) . '</li>';
	}

	$html .= '</ul>';

	return $html;
}

/**
 * Default FAQ sections for the FAQ page.
 *
 * @return array<int, array{title: string, items: array<int, array{question: string, answer: string}>}>
 */
function elite_shipping_get_default_faq_sections() {
	return array(
		array(
			'title' => __( 'General Questions', 'elite-shipping' ),
			'items' => array(
				array(
					'question' => __( 'What types of shipping containers do you sell?', 'elite-shipping' ),
					'answer'   => __( 'We supply an extensive range of both new and used shipping containers to suit a variety of storage, commercial and industrial applications, including:', 'elite-shipping' )
						. elite_shipping_faq_list_html(
							array(
								__( '10ft Shipping Containers', 'elite-shipping' ),
								__( '20ft Standard (Dry) Containers', 'elite-shipping' ),
								__( '20ft High Cube Containers', 'elite-shipping' ),
								__( '40ft Standard Containers', 'elite-shipping' ),
								__( '40ft High Cube Containers', 'elite-shipping' ),
								__( '45ft Containers', 'elite-shipping' ),
								__( '8ft x 10ft Containers', 'elite-shipping' ),
								__( 'Refrigerated (Reefer) Containers', 'elite-shipping' ),
								__( 'Tunnel Containers', 'elite-shipping' ),
								__( 'Open Top Containers', 'elite-shipping' ),
								__( 'Side Opening Containers', 'elite-shipping' ),
								__( 'Flat Rack Containers', 'elite-shipping' ),
								__( 'Double Door Containers', 'elite-shipping' ),
								__( 'Custom-Modified Containers', 'elite-shipping' ),
							)
						),
				),
				array(
					'question' => __( 'Do you sell both new and used containers?', 'elite-shipping' ),
					'answer'   => __( 'Yes. We offer both new (one-trip) and quality used shipping containers. The condition of every container is clearly stated on its individual product page.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Are your used containers wind and watertight?', 'elite-shipping' ),
					'answer'   => __( 'Yes. All of our used shipping containers are supplied as structurally sound, windproof and watertight unless otherwise specified in the product description.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Can I inspect a container before making a purchase?', 'elite-shipping' ),
					'answer'   => __( 'Yes. Container viewings can be arranged at selected locations. Please contact our sales team to schedule an appointment before visiting.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Do your shipping containers come with a warranty?', 'elite-shipping' ),
					'answer'   => __( 'Warranty coverage varies depending on the container\'s type, condition and specification. Please refer to the individual product listing or contact our team for full warranty details.', 'elite-shipping' ),
				),
			),
		),
		array(
			'title' => __( 'Ordering & Payment', 'elite-shipping' ),
			'items' => array(
				array(
					'question' => __( 'How do I place an order?', 'elite-shipping' ),
					'answer'   => __( 'You can place your order securely through our website or contact our sales team for assistance throughout the purchasing process.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Which payment methods do you accept?', 'elite-shipping' ),
					'answer'   => __( 'We accept secure payments via major debit and credit cards, bank transfer and other approved payment methods available during checkout.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Is payment required before delivery?', 'elite-shipping' ),
					'answer'   => __( 'Yes. In most cases, full payment must be received before your container is dispatched, unless alternative arrangements have been agreed in writing.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Will I receive an invoice?', 'elite-shipping' ),
					'answer'   => __( 'Yes. Once your order has been confirmed, we will issue either a VAT invoice or a standard sales invoice, depending on your purchase.', 'elite-shipping' ),
				),
			),
		),
		array(
			'title' => __( 'Delivery', 'elite-shipping' ),
			'items' => array(
				array(
					'question' => __( 'Do you deliver across the UK?', 'elite-shipping' ),
					'answer'   => __( 'Yes. We provide nationwide delivery throughout England, Scotland, Wales and Northern Ireland, subject to suitable site access.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'How long does delivery take?', 'elite-shipping' ),
					'answer'   => __( 'Delivery times depend on stock availability and your location. Most orders are delivered within 2 to 10 working days.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'How much does delivery cost?', 'elite-shipping' ),
					'answer'   => __( 'Delivery charges are calculated based on your postcode, the size of the container and site accessibility. A delivery quotation will be provided before your order is confirmed.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'What access is required for delivery?', 'elite-shipping' ),
					'answer'   => __( 'The delivery vehicle requires adequate access, firm and level ground, and sufficient space to unload the container safely. If you are uncertain about site access, please contact us before placing your order.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Will you unload the container?', 'elite-shipping' ),
					'answer'   => __( 'Yes. Most containers are delivered using HIAB crane vehicles, allowing safe unloading and positioning at your chosen location.', 'elite-shipping' ),
				),
			),
		),
		array(
			'title' => __( 'Container Condition', 'elite-shipping' ),
			'items' => array(
				array(
					'question' => __( 'What is a One-Trip shipping container?', 'elite-shipping' ),
					'answer'   => __( 'A One-Trip container is manufactured overseas, loaded with cargo only once and then shipped into the UK. These containers are considered almost new and are typically in excellent condition.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Will a used container have dents or scratches?', 'elite-shipping' ),
					'answer'   => __( 'Yes. Minor cosmetic dents, scratches and signs of previous use are normal for used containers and do not affect their structural integrity or performance.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Are your containers secure?', 'elite-shipping' ),
					'answer'   => __( 'Absolutely. Our shipping containers are manufactured from heavy-duty steel and fitted with robust locking mechanisms for maximum security. Additional security upgrades, including lock boxes, are also available.', 'elite-shipping' ),
				),
			),
		),
		array(
			'title' => __( 'Modifications', 'elite-shipping' ),
			'items' => array(
				array(
					'question' => __( 'Can you modify shipping containers?', 'elite-shipping' ),
					'answer'   => __( 'Yes. We provide a comprehensive range of container modification services, including:', 'elite-shipping' )
						. elite_shipping_faq_list_html(
							array(
								__( 'Personnel doors', 'elite-shipping' ),
								__( 'Windows', 'elite-shipping' ),
								__( 'Roller shutters', 'elite-shipping' ),
								__( 'Electrical installations', 'elite-shipping' ),
								__( 'Interior and exterior lighting', 'elite-shipping' ),
								__( 'Insulation', 'elite-shipping' ),
								__( 'Shelving', 'elite-shipping' ),
								__( 'Ventilation systems', 'elite-shipping' ),
								__( 'Internal lining', 'elite-shipping' ),
								__( 'Custom paint finishes', 'elite-shipping' ),
							)
						),
				),
				array(
					'question' => __( 'Can I order a bespoke container?', 'elite-shipping' ),
					'answer'   => __( 'Yes. We specialise in designing and manufacturing bespoke shipping containers tailored to your exact requirements and intended application.', 'elite-shipping' ),
				),
			),
		),
		array(
			'title' => __( 'Storage & Use', 'elite-shipping' ),
			'items' => array(
				array(
					'question' => __( 'Can shipping containers be used for storage?', 'elite-shipping' ),
					'answer'   => __( 'Yes. Shipping containers offer secure, durable and weather-resistant storage solutions for domestic, commercial and industrial use.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Can containers be converted into offices or workshops?', 'elite-shipping' ),
					'answer'   => __( 'Yes. We supply professionally converted containers for offices, workshops, welfare units, retail spaces and a wide range of specialist applications.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Do I need planning permission?', 'elite-shipping' ),
					'answer'   => __( 'Planning requirements vary depending on your intended use and local authority regulations. We recommend contacting your local planning department before installation.', 'elite-shipping' ),
				),
			),
		),
		array(
			'title' => __( 'Returns & Cancellations', 'elite-shipping' ),
			'items' => array(
				array(
					'question' => __( 'Can I cancel my order?', 'elite-shipping' ),
					'answer'   => __( 'Orders may be cancelled before dispatch in accordance with our cancellation policy. Please note that bespoke or modified containers may not be eligible for cancellation.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Do you accept returns?', 'elite-shipping' ),
					'answer'   => __( 'Returns are assessed on a case-by-case basis. If your container has been delivered incorrectly or arrives damaged, please contact us immediately so we can resolve the issue.', 'elite-shipping' ),
				),
			),
		),
		array(
			'title' => __( 'Customer Support', 'elite-shipping' ),
			'items' => array(
				array(
					'question' => __( 'How can I contact Elite Shipping Containers?', 'elite-shipping' ),
					'answer'   => __( 'Our customer support team can be contacted by telephone, email or via the contact form on our website.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'What are your business hours?', 'elite-shipping' ),
					'answer'   => __( 'Our support team is available Monday to Friday during normal business hours. Any enquiries received outside these times will be answered as soon as possible on the next working day.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Can I request a quotation?', 'elite-shipping' ),
					'answer'   => __( 'Yes. We provide free, no-obligation quotations for all shipping containers, container conversions and bespoke modification projects.', 'elite-shipping' ),
				),
			),
		),
		array(
			'title' => __( 'Technical Questions', 'elite-shipping' ),
			'items' => array(
				array(
					'question' => __( 'What container sizes do you offer?', 'elite-shipping' ),
					'answer'   => __( 'Our standard container sizes include 10ft, 20ft and 40ft models, with both standard-height and high cube options available.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'How long do shipping containers last?', 'elite-shipping' ),
					'answer'   => __( 'With proper care and routine maintenance, a high-quality shipping container can provide reliable service for 25 years or more.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Are your containers weatherproof?', 'elite-shipping' ),
					'answer'   => __( 'Yes. All of our containers are designed to withstand harsh weather conditions and provide reliable protection against wind, rain and the elements.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Can shipping containers be relocated?', 'elite-shipping' ),
					'answer'   => __( 'Yes. Containers can be transported and repositioned whenever required using suitable lifting equipment and transport vehicles.', 'elite-shipping' ),
				),
				array(
					'question' => __( 'Do your containers have CSC certification?', 'elite-shipping' ),
					'answer'   => __( 'Where applicable, our containers are supplied with valid CSC plates suitable for international shipping. If CSC certification is required, please confirm your requirements before placing your order.', 'elite-shipping' ),
				),
			),
		),
	);
}

/**
 * FAQ sections shown on the FAQ page.
 *
 * @return array<int, array{title: string, items: array<int, array{question: string, answer: string}>}>
 */
function elite_shipping_get_faq_sections() {
	$sections = elite_shipping_get_default_faq_sections();

	/**
	 * Filter FAQ page sections.
	 *
	 * @param array<int, array{title: string, items: array<int, array{question: string, answer: string}>}> $sections FAQ sections.
	 */
	return apply_filters( 'elite_shipping_faq_sections', $sections );
}

/**
 * Flat FAQ items for backward compatibility.
 *
 * @return array<int, array{question: string, answer: string}>
 */
function elite_shipping_get_faq_items() {
	$items = array();

	foreach ( elite_shipping_get_faq_sections() as $section ) {
		foreach ( $section['items'] as $item ) {
			$items[] = $item;
		}
	}

	return $items;
}

/**
 * Render FAQ answer markup.
 *
 * @param string $answer Answer HTML or plain text.
 */
function elite_shipping_render_faq_answer( $answer ) {
	$answer = trim( (string) $answer );

	if ( '' === $answer ) {
		return;
	}

	if ( false !== strpos( $answer, '<' ) ) {
		echo wp_kses_post( $answer );
		return;
	}

	echo wp_kses_post( wpautop( $answer ) );
}
