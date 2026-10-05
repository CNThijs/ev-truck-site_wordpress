<?php
/**
 * Menus per language and location. Items point to seeded pages by key; an item whose page does not exist (yet) is
 * skipped, and so is a parent without children. Custom items have a url.
 */
return array(
	'en' => array(
		'primary' => array(
			array(
				'key'   => 'home',
				'label' => 'Home',
			),
			array(
				'key'   => 'about',
				'label' => 'About Us',
			),
			array(
				'label'    => 'Customers',
				'children' => array(
					array(
						'key'   => 'locations',
						'label' => 'Locations',
					),
					array(
						'key'   => 'carriers',
						'label' => 'Carriers',
					),
				),
			),
			array(
				'key'   => 'blog',
				'label' => 'News',
			),
		),
		'utility' => array(
			array(
				'label'   => 'Login',
				'url'     => 'https://portal.chargenet.energy',
				'new_tab' => true,
			),
		),
		'footer'  => array(
			array(
				'key'   => 'about',
				'label' => 'About Us',
			),
			array(
				'key'   => 'faq',
				'label' => 'FAQ',
			),
			array(
				'key'   => 'careers',
				'label' => 'Careers',
			),
			array(
				'key'   => 'security',
				'label' => 'Security',
			),
			array(
				'key'   => 'privacy',
				'label' => 'Privacy Policy',
			),
		),
		'legal'   => array(
			array(
				'key'   => 'privacy',
				'label' => 'Privacy Policy',
			),
			array(
				'key'   => 'security',
				'label' => 'Security',
			),
			array(
				'key'   => 'terms',
				'label' => 'Terms & Conditions',
			),
		),
	),
	'nl' => array(
		'primary' => array(
			array(
				'key'   => 'home',
				'label' => 'Home',
			),
			array(
				'key'   => 'about',
				'label' => 'Over Ons',
			),
			array(
				'label'    => 'Klanten',
				'children' => array(
					array(
						'key'   => 'locations',
						'label' => 'Locaties',
					),
					array(
						'key'   => 'carriers',
						'label' => 'Vervoerders',
					),
				),
			),
			array(
				'key'   => 'blog',
				'label' => 'Nieuws',
			),
		),
		'utility' => array(
			array(
				'label'   => 'Login',
				'url'     => 'https://portal.chargenet.energy',
				'new_tab' => true,
			),
		),
		'footer'  => array(
			array(
				'key'   => 'about',
				'label' => 'Over Ons',
			),
			array(
				'key'   => 'faq',
				'label' => 'Veelgestelde Vragen (FAQ)',
			),
			array(
				'key'   => 'careers',
				'label' => 'Carrière',
			),
			array(
				'key'   => 'security',
				'label' => 'Beveiliging',
			),
			array(
				'key'   => 'privacy',
				'label' => 'Privacybeleid',
			),
		),
		'legal'   => array(
			array(
				'key'   => 'privacy',
				'label' => 'Privacybeleid',
			),
			array(
				'key'   => 'security',
				'label' => 'Beveiliging',
			),
			array(
				'key'   => 'terms',
				'label' => 'Algemene voorwaarden',
			),
		),
	),
);
