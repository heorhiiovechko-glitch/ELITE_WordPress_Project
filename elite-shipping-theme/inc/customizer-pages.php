<?php
/**
 * Theme Customizer — About, Contact, and Blog pages.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register About, Contact, and Blog Customizer panels.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function elite_shipping_customize_register_pages( $wp_customize ) {
	if ( ! function_exists( 'elite_shipping_add_text_setting' ) || ! function_exists( 'elite_shipping_add_image_setting' ) ) {
		return;
	}

	/* ── About panel ── */
	$wp_customize->add_panel(
		'elite_about',
		array(
			'title'       => __( 'About', 'elite-shipping' ),
			'description' => __( 'Customize the About Us page.', 'elite-shipping' ),
			'priority'    => 125,
		)
	);

	$wp_customize->add_section(
		'elite_about_hero',
		array(
			'title'    => __( 'Hero', 'elite-shipping' ),
			'panel'    => 'elite_about',
			'priority' => 10,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_page_kicker',
		array(
			'label'   => __( 'Kicker', 'elite-shipping' ),
			'section' => 'elite_about_hero',
			'default' => 'ABOUT US',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_page_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_about_hero',
			'default' => 'About Elite Shipping Containers',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_page_desc',
		array(
			'label'    => __( 'Description', 'elite-shipping' ),
			'section'  => 'elite_about_hero',
			'type'     => 'textarea',
			'default'  => 'Your trusted UK partner for premium shipping containers, nationwide delivery, and expert support.',
			'sanitize' => 'wp_kses_post',
		)
	);

	$wp_customize->add_section(
		'elite_about_who',
		array(
			'title'    => __( 'Who We Are', 'elite-shipping' ),
			'panel'    => 'elite_about',
			'priority' => 20,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_who_kicker',
		array(
			'label'   => __( 'Kicker', 'elite-shipping' ),
			'section' => 'elite_about_who',
			'default' => 'WHO WE ARE',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_who_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_about_who',
			'default' => 'Your Ultimate Partner in Premium Container Solutions',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_who_text',
		array(
			'label'    => __( 'Description', 'elite-shipping' ),
			'section'  => 'elite_about_who',
			'type'     => 'textarea',
			'default'  => 'At Elite Shipping Containers Ltd, we lead the way in delivering high-quality shipping containers through a modern, customer-first platform. Whether for personal or commercial use, our goal is to make finding and buying containers seamless, transparent, and efficient.',
			'sanitize' => 'wp_kses_post',
		)
	);
	elite_shipping_add_image_setting(
		$wp_customize,
		'elite_about_who_image_main',
		array(
			'label'   => __( 'Main image', 'elite-shipping' ),
			'section' => 'elite_about_who',
		)
	);
	elite_shipping_add_image_setting(
		$wp_customize,
		'elite_about_who_image_sub',
		array(
			'label'   => __( 'Inset image', 'elite-shipping' ),
			'section' => 'elite_about_who',
		)
	);

	$wp_customize->add_section(
		'elite_about_mission',
		array(
			'title'    => __( 'Our Mission', 'elite-shipping' ),
			'panel'    => 'elite_about',
			'priority' => 30,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_mission_kicker',
		array(
			'label'   => __( 'Kicker', 'elite-shipping' ),
			'section' => 'elite_about_mission',
			'default' => 'OUR MISSION',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_mission_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_about_mission',
			'default' => 'Empowering You with Choice, Confidence & Convenience',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_mission_text',
		array(
			'label'    => __( 'Description', 'elite-shipping' ),
			'section'  => 'elite_about_mission',
			'type'     => 'textarea',
			'default'  => 'Our mission is simple yet powerful: to offer a wide selection of shipping containers, unmatched service, and competitive prices. We strive to make every customer interaction stress-free, ensuring you get exactly what you need, when you need it — with complete peace of mind.',
			'sanitize' => 'wp_kses_post',
		)
	);
	elite_shipping_add_image_setting(
		$wp_customize,
		'elite_about_mission_image_main',
		array(
			'label'   => __( 'Main image', 'elite-shipping' ),
			'section' => 'elite_about_mission',
		)
	);
	elite_shipping_add_image_setting(
		$wp_customize,
		'elite_about_mission_image_sub',
		array(
			'label'   => __( 'Inset image', 'elite-shipping' ),
			'section' => 'elite_about_mission',
		)
	);

	$wp_customize->add_section(
		'elite_about_why',
		array(
			'title'    => __( 'Why Choose Us', 'elite-shipping' ),
			'panel'    => 'elite_about',
			'priority' => 40,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_why_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_about_why',
			'default' => 'Why Choose Elite Shipping Containers',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_why_tagline',
		array(
			'label'   => __( 'Tagline', 'elite-shipping' ),
			'section' => 'elite_about_why',
			'default' => 'Trusted across the UK for quality, value, and service',
		)
	);

	$why_defaults = array(
		1 => array(
			'Quality Products',
			'We take pride in offering best-in-class containers made from highly durable steel, suitable for industrial, commercial, and residential applications.',
		),
		2 => array(
			'Competitive Pricing',
			'With strong market insight and industry experience, we provide top-tier containers at unbeatable prices — delivering maximum value with no compromise on quality.',
		),
		3 => array(
			'Personalized Service',
			'We tailor every solution to your unique needs. From modified units to custom layouts, we deliver containers that match your specifications perfectly.',
		),
	);
	foreach ( $why_defaults as $i => $defaults ) {
		elite_shipping_add_text_setting(
			$wp_customize,
			'elite_about_why_' . $i . '_title',
			array(
				'label'   => sprintf( __( 'Card %d title', 'elite-shipping' ), $i ),
				'section' => 'elite_about_why',
				'default' => $defaults[0],
			)
		);
		elite_shipping_add_text_setting(
			$wp_customize,
			'elite_about_why_' . $i . '_text',
			array(
				'label'    => sprintf( __( 'Card %d text', 'elite-shipping' ), $i ),
				'section'  => 'elite_about_why',
				'type'     => 'textarea',
				'default'  => $defaults[1],
				'sanitize' => 'wp_kses_post',
			)
		);
		elite_shipping_add_image_setting(
			$wp_customize,
			'elite_about_why_' . $i . '_image',
			array(
				'label'   => sprintf( __( 'Card %d image', 'elite-shipping' ), $i ),
				'section' => 'elite_about_why',
			)
		);
	}
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_why_btn_text',
		array(
			'label'   => __( 'Card button text', 'elite-shipping' ),
			'section' => 'elite_about_why',
			'default' => 'Shop Now →',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_why_btn_url',
		array(
			'label'    => __( 'Card button URL', 'elite-shipping' ),
			'section'  => 'elite_about_why',
			'default'  => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
			'sanitize' => 'esc_url_raw',
		)
	);

	$wp_customize->add_section(
		'elite_about_features',
		array(
			'title'    => __( 'What Sets Us Apart', 'elite-shipping' ),
			'panel'    => 'elite_about',
			'priority' => 50,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_features_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_about_features',
			'default' => 'What Sets Us Apart',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_features_sub',
		array(
			'label'   => __( 'Subheading', 'elite-shipping' ),
			'section' => 'elite_about_features',
			'default' => 'Quality, transparency & customer satisfaction guaranteed',
		)
	);

	$feature_defaults = array(
		1 => array( 'Quality Assurance', 'Every container is rigorously inspected for long-lasting durability.' ),
		2 => array( 'Vast Selection', 'From brand-new units to affordable used containers, we have options for every need.' ),
		3 => array( 'Customer-Centric Service', 'Our expert team is with you at every step — from quote to delivery.' ),
		4 => array( 'Transparent Pricing', 'No hidden fees — just honest, upfront costs every time.' ),
		5 => array( 'Easy Online Experience', 'Shop confidently with a fast, secure, and intuitive digital process.' ),
		6 => array( 'Secure Payments', 'Enjoy safe and seamless transactions through our trusted payment gateway.' ),
		7 => array( 'UK Nationwide Delivery', 'We deliver across the United Kingdom — reliably and quickly.' ),
		8 => array( '24/7 Support', 'Our friendly support team is ready around the clock to help you anytime.' ),
	);
	foreach ( $feature_defaults as $i => $defaults ) {
		elite_shipping_add_text_setting(
			$wp_customize,
			'elite_about_feature_' . $i . '_title',
			array(
				'label'   => sprintf( __( 'Feature %d title', 'elite-shipping' ), $i ),
				'section' => 'elite_about_features',
				'default' => $defaults[0],
			)
		);
		elite_shipping_add_text_setting(
			$wp_customize,
			'elite_about_feature_' . $i . '_text',
			array(
				'label'   => sprintf( __( 'Feature %d text', 'elite-shipping' ), $i ),
				'section' => 'elite_about_features',
				'default' => $defaults[1],
			)
		);
	}

	$wp_customize->add_section(
		'elite_about_expertise',
		array(
			'title'    => __( 'Our Expertise', 'elite-shipping' ),
			'panel'    => 'elite_about',
			'priority' => 60,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_expertise_kicker',
		array(
			'label'   => __( 'Kicker', 'elite-shipping' ),
			'section' => 'elite_about_expertise',
			'default' => 'OUR EXPERTISE',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_expertise_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_about_expertise',
			'default' => 'Driven by Experience, Built on Trust',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_expertise_text',
		array(
			'label'    => __( 'Description', 'elite-shipping' ),
			'section'  => 'elite_about_expertise',
			'type'     => 'textarea',
			'default'  => 'With years of experience in the shipping container industry, we understand what our customers need. Our carefully curated inventory spans various sizes, conditions, and applications — tailored for construction, retail, agriculture, events, and more.',
			'sanitize' => 'wp_kses_post',
		)
	);
	elite_shipping_add_image_setting(
		$wp_customize,
		'elite_about_expertise_image',
		array(
			'label'   => __( 'Image', 'elite-shipping' ),
			'section' => 'elite_about_expertise',
		)
	);

	$wp_customize->add_section(
		'elite_about_sustainability',
		array(
			'title'    => __( 'Sustainability', 'elite-shipping' ),
			'panel'    => 'elite_about',
			'priority' => 70,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_sustain_kicker',
		array(
			'label'   => __( 'Kicker', 'elite-shipping' ),
			'section' => 'elite_about_sustainability',
			'default' => 'SUSTAINABILITY',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_sustain_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_about_sustainability',
			'default' => 'Supporting a Circular Economy with Smarter Choices',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_sustain_text',
		array(
			'label'    => __( 'Description', 'elite-shipping' ),
			'section'  => 'elite_about_sustainability',
			'type'     => 'textarea',
			'default'  => 'We are committed to sustainability through the promotion of high-quality used containers. By extending the lifecycle of these robust units, we help reduce waste and support environmentally responsible business practices — for a greener tomorrow.',
			'sanitize' => 'wp_kses_post',
		)
	);
	elite_shipping_add_image_setting(
		$wp_customize,
		'elite_about_sustain_image',
		array(
			'label'   => __( 'Image', 'elite-shipping' ),
			'section' => 'elite_about_sustainability',
		)
	);

	$wp_customize->add_section(
		'elite_about_cta',
		array(
			'title'    => __( 'Testimonials & CTA', 'elite-shipping' ),
			'panel'    => 'elite_about',
			'priority' => 80,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_testimonials_title',
		array(
			'label'   => __( 'Testimonials heading', 'elite-shipping' ),
			'section' => 'elite_about_cta',
			'default' => 'What Our Clients Say About Us',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_cta_primary_text',
		array(
			'label'   => __( 'Primary button text', 'elite-shipping' ),
			'section' => 'elite_about_cta',
			'default' => 'Browse Containers',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_cta_primary_url',
		array(
			'label'    => __( 'Primary button URL', 'elite-shipping' ),
			'section'  => 'elite_about_cta',
			'default'  => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
			'sanitize' => 'esc_url_raw',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_cta_secondary_text',
		array(
			'label'   => __( 'Secondary button text', 'elite-shipping' ),
			'section' => 'elite_about_cta',
			'default' => 'Contact Us',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_about_cta_secondary_url',
		array(
			'label'    => __( 'Secondary button URL', 'elite-shipping' ),
			'section'  => 'elite_about_cta',
			'default'  => home_url( '/contact-us/' ),
			'sanitize' => 'esc_url_raw',
		)
	);

	/* ── Contact panel ── */
	$wp_customize->add_panel(
		'elite_contact',
		array(
			'title'       => __( 'Contact', 'elite-shipping' ),
			'description' => __( 'Customize the Contact Us page and company contact details.', 'elite-shipping' ),
			'priority'    => 126,
		)
	);

	$wp_customize->add_section(
		'elite_contact_hero',
		array(
			'title'    => __( 'Hero', 'elite-shipping' ),
			'panel'    => 'elite_contact',
			'priority' => 10,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_page_kicker',
		array(
			'label'   => __( 'Kicker', 'elite-shipping' ),
			'section' => 'elite_contact_hero',
			'default' => 'CONTACT US',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_page_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_contact_hero',
			'default' => 'Get In Touch',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_page_desc',
		array(
			'label'    => __( 'Description', 'elite-shipping' ),
			'section'  => 'elite_contact_hero',
			'type'     => 'textarea',
			'default'  => 'Speak with our team about container quotes, delivery, modifications, and support — we are here to help across the UK.',
			'sanitize' => 'wp_kses_post',
		)
	);
	elite_shipping_add_image_setting(
		$wp_customize,
		'elite_contact_hero_image',
		array(
			'label'   => __( 'Background image', 'elite-shipping' ),
			'section' => 'elite_contact_hero',
		)
	);

	$wp_customize->add_section(
		'elite_contact_details',
		array(
			'title'       => __( 'Company details', 'elite-shipping' ),
			'description' => __( 'Used on Contact, footer, header, and policy pages.', 'elite-shipping' ),
			'panel'       => 'elite_contact',
			'priority'    => 20,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_company_name',
		array(
			'label'   => __( 'Company name', 'elite-shipping' ),
			'section' => 'elite_contact_details',
			'default' => ELITE_COMPANY_NAME,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_phone',
		array(
			'label'   => __( 'Phone', 'elite-shipping' ),
			'section' => 'elite_contact_details',
			'default' => ELITE_CONTACT_PHONE,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_email',
		array(
			'label'    => __( 'Email', 'elite-shipping' ),
			'section'  => 'elite_contact_details',
			'default'  => ELITE_CONTACT_EMAIL,
			'sanitize' => 'sanitize_email',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_address',
		array(
			'label'   => __( 'Address', 'elite-shipping' ),
			'section' => 'elite_contact_details',
			'default' => ELITE_CONTACT_ADDRESS,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_website',
		array(
			'label'   => __( 'Website label', 'elite-shipping' ),
			'section' => 'elite_contact_details',
			'default' => 'eliteshippingcontainers.co.uk',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_website_url',
		array(
			'label'    => __( 'Website URL', 'elite-shipping' ),
			'section'  => 'elite_contact_details',
			'default'  => ELITE_SITE_URL,
			'sanitize' => 'esc_url_raw',
		)
	);

	$wp_customize->add_section(
		'elite_contact_info_blocks',
		array(
			'title'    => __( 'Info blocks', 'elite-shipping' ),
			'panel'    => 'elite_contact',
			'priority' => 30,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_info_heading',
		array(
			'label'   => __( 'Left block heading', 'elite-shipping' ),
			'section' => 'elite_contact_info_blocks',
			'default' => 'Get in touch',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_info_intro',
		array(
			'label'    => __( 'Left block intro', 'elite-shipping' ),
			'section'  => 'elite_contact_info_blocks',
			'type'     => 'textarea',
			'default'  => 'Contact %s for container quotes, delivery questions, modifications, and order support across the UK. You can also speak with us anytime using the live chat widget on this page.',
			'sanitize' => 'wp_kses_post',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_form_kicker',
		array(
			'label'   => __( 'Form kicker', 'elite-shipping' ),
			'section' => 'elite_contact_info_blocks',
			'default' => 'INFORMATION ABOUT US',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_form_title',
		array(
			'label'   => __( 'Form heading', 'elite-shipping' ),
			'section' => 'elite_contact_info_blocks',
			'default' => 'Contact Us For Any Questions',
		)
	);

	$wp_customize->add_section(
		'elite_contact_map',
		array(
			'title'    => __( 'Map', 'elite-shipping' ),
			'panel'    => 'elite_contact',
			'priority' => 40,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_contact_map_embed',
		array(
			'label'       => __( 'Google Maps embed URL', 'elite-shipping' ),
			'section'     => 'elite_contact_map',
			'type'        => 'textarea',
			'default'     => '',
			'sanitize'    => 'esc_url_raw',
		)
	);

	/* ── Blog panel ── */
	$wp_customize->add_panel(
		'elite_blog',
		array(
			'title'       => __( 'Blog', 'elite-shipping' ),
			'description' => __( 'Customize the Our Blog archive and single post chrome.', 'elite-shipping' ),
			'priority'    => 127,
		)
	);

	$wp_customize->add_section(
		'elite_blog_archive_hero',
		array(
			'title'    => __( 'Archive hero', 'elite-shipping' ),
			'panel'    => 'elite_blog',
			'priority' => 10,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_blog_kicker',
		array(
			'label'   => __( 'Kicker', 'elite-shipping' ),
			'section' => 'elite_blog_archive_hero',
			'default' => 'OUR BLOG',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_blog_title',
		array(
			'label'   => __( 'Heading', 'elite-shipping' ),
			'section' => 'elite_blog_archive_hero',
			'default' => 'Our Blog',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_blog_desc',
		array(
			'label'    => __( 'Description', 'elite-shipping' ),
			'section'  => 'elite_blog_archive_hero',
			'type'     => 'textarea',
			'default'  => 'Expert guides, market insights, and practical advice on buying, using, and modifying shipping containers across the UK.',
			'sanitize' => 'wp_kses_post',
		)
	);
	elite_shipping_add_image_setting(
		$wp_customize,
		'elite_blog_hero_image',
		array(
			'label'   => __( 'Background image', 'elite-shipping' ),
			'section' => 'elite_blog_archive_hero',
		)
	);

	$wp_customize->add_section(
		'elite_blog_cards',
		array(
			'title'       => __( 'Post cards', 'elite-shipping' ),
			'description' => __( 'Build unlimited blog cards. Add a card, then set title, date, image, introduction, and details.', 'elite-shipping' ),
			'panel'       => 'elite_blog',
			'priority'    => 20,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_blog_card_cat',
		array(
			'label'   => __( 'Category label', 'elite-shipping' ),
			'section' => 'elite_blog_cards',
			'default' => 'Blog',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_blog_card_more',
		array(
			'label'   => __( 'Continue reading text', 'elite-shipping' ),
			'section' => 'elite_blog_cards',
			'default' => 'Continue reading',
		)
	);

	if ( class_exists( 'Elite_Blog_Cards_List_Control' ) ) {
		$wp_customize->add_setting(
			'elite_blog_cards_list',
			array(
				'default'           => '[]',
				'sanitize_callback' => 'elite_shipping_sanitize_blog_cards_list',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new Elite_Blog_Cards_List_Control(
				$wp_customize,
				'elite_blog_cards_list',
				array(
					'label'       => __( 'Card list', 'elite-shipping' ),
					'description' => __( 'No limit. Use the image and trash icons on each card. Open Details to edit the full article body.', 'elite-shipping' ),
					'section'     => 'elite_blog_cards',
					'priority'    => 30,
				)
			)
		);
	}

	$wp_customize->add_section(
		'elite_blog_single',
		array(
			'title'    => __( 'Single post', 'elite-shipping' ),
			'panel'    => 'elite_blog',
			'priority' => 30,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_blog_single_kicker',
		array(
			'label'   => __( 'Hero kicker', 'elite-shipping' ),
			'section' => 'elite_blog_single',
			'default' => 'OUR BLOG',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_blog_single_title',
		array(
			'label'   => __( 'Hero heading', 'elite-shipping' ),
			'section' => 'elite_blog_single',
			'default' => 'Our Blog',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_blog_single_desc',
		array(
			'label'    => __( 'Hero description', 'elite-shipping' ),
			'section'  => 'elite_blog_single',
			'type'     => 'textarea',
			'default'  => 'Expert guides, market insights, and practical advice on buying, using, and modifying shipping containers across the UK.',
			'sanitize' => 'wp_kses_post',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_blog_single_cat',
		array(
			'label'   => __( 'Category label', 'elite-shipping' ),
			'section' => 'elite_blog_single',
			'default' => 'Blog',
		)
	);

	$wp_customize->add_section(
		'elite_blog_sidebar',
		array(
			'title'    => __( 'Sidebar', 'elite-shipping' ),
			'panel'    => 'elite_blog',
			'priority' => 40,
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_blog_sidebar_categories_title',
		array(
			'label'   => __( 'Categories heading', 'elite-shipping' ),
			'section' => 'elite_blog_sidebar',
			'default' => 'Categories',
		)
	);
	elite_shipping_add_text_setting(
		$wp_customize,
		'elite_blog_sidebar_recent_title',
		array(
			'label'   => __( 'Recent posts heading', 'elite-shipping' ),
			'section' => 'elite_blog_sidebar',
			'default' => 'Recent Posts',
		)
	);
}
add_action( 'customize_register', 'elite_shipping_customize_register_pages', 20 );
