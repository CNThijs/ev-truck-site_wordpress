<?php
/**
 * Sample content for the Section Gallery. One entry per section; add a section's variants here when you add the section.
 * Text is English sample copy, only ever shown to administrators.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gallery entries: id, title, description, variants (label => block markup).
 *
 * @return array<int, array{id: string, title: string, description: string, variants: array<string, string>}>
 */
function chargenet_gallery_sections(): array {
	$img = chargenet_gallery_samples();
	$b   = 'chargenet_gallery_block';
	$p   = 'chargenet_gallery_p';

	$grid_item = static function ( string $icon, string $title, string $text, array $extra = array() ) use ( $b ): string {
		return $b(
			'chargenet/feature-grid-item',
			array_merge(
				array(
					'icon'  => $icon,
					'title' => $title,
					'text'  => $text,
				),
				$extra
			)
		);
	};
	$benefits  = static fn( array $extra = array() ): string => $grid_item( 'euro', 'Competitive pricing', 'Charge at the right price, at the right place, at the right time.', $extra )
		. $grid_item( 'layers', 'One platform', 'Locations, drivers and invoices in a single place.', $extra )
		. $grid_item( 'sliders', 'Control and flexibility', 'Decide who may charge, when and at what tariff.', $extra )
		. $grid_item( 'smartphone', 'Easy to use', 'Find, start and pay from the app.', $extra )
		. $grid_item( 'lock', 'Private access', 'Your site stays private to the network you choose.', $extra )
		. $grid_item( 'shield-check', 'Security by design', 'Built to the standards your IT team expects.', $extra );

	$stat  = static fn( string $value, string $suffix, string $label, string $prefix = '' ): string => $b(
		'chargenet/stat-item',
		array(
			'value'  => $value,
			'suffix' => $suffix,
			'prefix' => $prefix,
			'label'  => $label,
		)
	);
	$stats = $stat( '3.8', 'Bn', 'tonne of CO₂ from road freight each year' )
		. $stat( '18', '%', 'lower charging cost for carriers' )
		. $stat( '80', '%', 'of trucks charge at their destination' )
		. $stat( '24', '/7', 'access on private sites', '' );

	$column = static fn( string $title, string $link ): string => $b(
		'chargenet/feature-column',
		array(
			'title'     => $title,
			'url'       => '#',
			'linkLabel' => $link,
		),
		chargenet_gallery_h( 'Key benefits' )
		. chargenet_gallery_ul( array( 'Earn on idle charging capacity', 'Keep full control of your site', 'No extra hardware needed' ) )
		. chargenet_gallery_h( 'Platform features' )
		. chargenet_gallery_ul( array( 'Tariffs per driver group', 'Live availability', 'Automatic invoicing' ) )
	);

	$step = static fn( string $title, string $text, array $bullets = array() ): string => $b(
		'chargenet/step-item',
		array( 'title' => $title ),
		$p( $text ) . ( $bullets ? chargenet_gallery_ul( $bullets ) : '' )
	);

	$qa  = static fn( string $question, string $answer ): string => $b( 'chargenet/accordion-item', array( 'question' => $question ), $p( $answer ) );
	$faq = $qa( 'How do I start charging?', 'Open the app, pick a site you have access to and start the session.' )
		. $qa( 'Who can use a private site?', 'Only drivers the site owner has approved, with the tariff the owner set.' )
		. $qa( 'How am I invoiced?', 'Automatically, per site owner, with one overview for your fleet.' )
		. $qa( 'Do I need extra hardware?', 'No. ChargeNet works with the chargers already on site.' );

	$person = static fn( string $name, string $role, array $extra = array() ) => $b(
		'chargenet/team-member',
		array_merge(
			array(
				'name' => $name,
				'role' => $role,
			),
			$extra
		)
	);

	$logo = static fn( int $key, string $name, array $extra = array() ): string => $b(
		'chargenet/logo-item',
		array_merge(
			array(
				'imageId' => $img[ $key ] ?? 0,
				'name'    => $name,
			),
			$extra
		)
	);

	$slide  = static fn( string $title, string $text, int $key, array $extra = array() ): string => $b(
		'chargenet/slide-card',
		array_merge(
			array(
				'title'   => $title,
				'text'    => $text,
				'imageId' => $img[ $key ] ?? 0,
				'url'     => '#',
			),
			$extra
		)
	);
	$slides = $slide( 'Destination charging', 'Charging where trucks already stop for hours.', 1, array( 'linkLabel' => 'Read more' ) )
		. $slide( 'ChargeBase', 'Mobile charging capacity, anywhere you need it.', 2, array( 'linkLabel' => 'Read more' ) )
		. $slide( 'IJmond aan zet', 'A regional programme for zero-emission logistics.', 1, array( 'linkLabel' => 'Read more' ) )
		. $slide( 'Construction power', 'Clean power for emission-free building sites.', 2, array( 'linkLabel' => 'Read more' ) )
		. $slide( 'Card without link', 'A card does not need a link or an image.', 0, array( 'url' => '' ) );

	return array(
		array(
			'id'          => 'hero',
			'title'       => 'Hero',
			'description' => 'Opening section and the only place for a page’s h1. The banner image loads eagerly with high priority.',
			'variants'    => array(
				'Banner, standard overlay'        => $b(
					'chargenet/hero',
					array(
						'variant' => 'banner',
						'eyebrow' => 'Private charging network',
						'heading' => 'Keep charging ahead',
						'intro'   => 'ChargeNet connects location owners and carriers on one private charging network for electric trucks.',
						'imageId' => $img[1] ?? 0,
					),
					chargenet_gallery_button( 'Explore use cases' ) . chargenet_gallery_button( 'Contact us', 'secondary' )
				),
				'Banner, strong overlay'          => $b(
					'chargenet/hero',
					array(
						'variant' => 'banner',
						'overlay' => 'strong',
						'heading' => 'A busy photo still stays readable',
						'imageId' => $img[2] ?? 0,
					),
					chargenet_gallery_button( 'Contact us' )
				),
				'Title band with intro'           => $b(
					'chargenet/hero',
					array(
						'variant' => 'title-band',
						'heading' => 'Locations',
						'intro'   => 'Turn spare grid capacity into a charging site that earns.',
					)
				),
				'Title band, heading only, light' => $b(
					'chargenet/hero',
					array(
						'variant'           => 'title-band',
						'sectionBackground' => 'light',
						'heading'           => 'Frequently asked questions',
					)
				),
				'Campaign with cover'             => $b(
					'chargenet/hero',
					array(
						'variant'  => 'campaign',
						'eyebrow'  => 'Trend report',
						'heading'  => 'The numbers behind the e-transition',
						'intro'    => 'Five insights that decide when electric trucking pays off.',
						'imageId'  => $img[1] ?? 0,
						'coverId'  => $img[3] ?? 0,
						'coverAlt' => 'Cover of the trend report',
					),
					chargenet_gallery_button( 'Download the report' ) . chargenet_gallery_button( 'Read the insights', 'secondary' )
				),
				'Empty (renders nothing)'         => $b( 'chargenet/hero', array( 'heading' => '' ) ),
			),
		),
		array(
			'id'          => 'feature-grid',
			'title'       => 'Feature Grid',
			'description' => 'Icon, title and text items. Items with a link make the whole item clickable.',
			'variants'    => array(
				'Cards, 3 columns'            => $b(
					'chargenet/feature-grid',
					array(
						'sectionBackground' => 'paper',
						'eyebrow'           => 'Benefits',
						'heading'           => 'Why location owners choose ChargeNet',
						'intro'             => 'Six reasons to open your site to the network.',
					),
					$benefits()
				),
				'Cards with links, 2 columns' => $b(
					'chargenet/feature-grid',
					array(
						'columns' => 2,
						'heading' => 'Where to start',
					),
					$grid_item( 'building', 'For location owners', 'Sell unused charging capacity.', array( 'url' => '#' ) )
					. $grid_item( 'truck', 'For carriers', 'Charge where your trucks already stop.', array( 'url' => '#' ) )
				),
				'Plain, 4 columns'            => $b(
					'chargenet/feature-grid',
					array(
						'variant' => 'plain',
						'columns' => 4,
						'heading' => 'Our values',
					),
					$grid_item( 'lightbulb', 'Innovation', 'We build what the market lacks.' )
					. $grid_item( 'leaf', 'Impact', 'Every charge replaces a diesel kilometre.' )
					. $grid_item( 'trending-up', 'Growth', 'We grow with our partners.' )
					. $grid_item( 'heart', 'Care', 'We look after sites and drivers.' )
				),
				'Numbered, dark'              => $b(
					'chargenet/feature-grid',
					array(
						'variant'           => 'numbered',
						'sectionBackground' => 'dark',
						'heading'           => 'Five insights for 2027',
					),
					$grid_item( '', 'Truck levy', 'Road charges change the business case.' )
					. $grid_item( '', 'Excise duty', 'Diesel gets more expensive.' )
					. $grid_item( '', 'ETS 2', 'A carbon price reaches road fuel.' )
				),
				'Empty (renders nothing)'     => $b( 'chargenet/feature-grid', array( 'heading' => 'No items' ) ),
			),
		),
		array(
			'id'          => 'feature-columns',
			'title'       => 'Feature Columns',
			'description' => 'Two or three columns with sub-headings, bullet lists and a link each.',
			'variants'    => array(
				'Two columns'   => $b(
					'chargenet/feature-columns',
					array( 'heading' => 'Built for both sides of the network' ),
					$column( 'Location managers', 'Explore location benefits' ) . $column( 'Fleet managers', 'Explore carrier benefits' )
				),
				'Three columns' => $b(
					'chargenet/feature-columns',
					array(
						'sectionBackground' => 'paper',
						'heading'           => 'Three roles, one platform',
					),
					$column( 'Owners', 'Learn more' ) . $column( 'Carriers', 'Learn more' ) . $column( 'Drivers', 'Learn more' )
				),
			),
		),
		array(
			'id'          => 'stats',
			'title'       => 'Statistics',
			'description' => 'Numbers print in their final form; data-count lets the animation system count up.',
			'variants'    => array(
				'Row with background image' => $b(
					'chargenet/stats',
					array(
						'heading' => 'Why ChargeNet',
						'imageId' => $img[1] ?? 0,
					),
					$stats
				),
				'Text beside the numbers'   => $b(
					'chargenet/stats',
					array(
						'variant' => 'with-text',
						'eyebrow' => 'Impact',
						'heading' => 'What ChargeNet does for you',
						'text'    => 'Less cost, less waiting, less CO₂.',
					),
					$stat( '80', '%', 'of trucks charge at their destination' ) . $stat( '18', '%', 'lower charging cost' )
				),
				'Row, light, no heading'    => $b(
					'chargenet/stats',
					array( 'sectionBackground' => 'light' ),
					$stats
				),
			),
		),
		array(
			'id'          => 'steps',
			'title'       => 'Steps',
			'description' => 'A numbered process; the number is a CSS counter and the list stays a real ordered list.',
			'variants'    => array(
				'Horizontal'              => $b(
					'chargenet/steps',
					array(
						'sectionBackground' => 'paper',
						'heading'           => 'How it works',
						'intro'             => 'From registration to your first charging session.',
					),
					$step( 'Registration', 'Create your account and add your site or fleet.' )
					. $step( 'Configuration', 'Set tariffs, access rules and opening hours.' )
					. $step( 'Monetisation', 'Start earning from the first approved session.' )
				),
				'Vertical, with lists'    => $b(
					'chargenet/steps',
					array(
						'variant' => 'vertical',
						'heading' => 'Five steps to a live site',
					),
					$step( 'Intake', 'We map your site and its grid capacity.', array( 'Site visit', 'Capacity check' ) )
					. $step( 'Setup', 'We connect your chargers to the platform.', array( 'Charger link', 'Tariff setup' ) )
					. $step( 'Go live', 'Approved drivers can charge.', array( 'Driver onboarding', 'First session' ) )
				),
				'Empty (renders nothing)' => $b( 'chargenet/steps', array( 'heading' => 'No steps' ) ),
			),
		),
		array(
			'id'          => 'accordion',
			'title'       => 'Accordion (FAQ)',
			'description' => 'Native details elements: keyboard operable and working without JavaScript. All closed on load.',
			'variants'    => array(
				'Single column'           => $b(
					'chargenet/accordion',
					array( 'heading' => 'Frequently asked questions' ),
					$faq
				),
				'With help box, FAQ data' => $b(
					'chargenet/accordion',
					array(
						'variant'           => 'with-aside',
						'sectionBackground' => 'paper',
						'heading'           => 'Questions about charging',
						'asideHeading'      => 'Still have questions?',
						'asideText'         => 'Our team is happy to help.',
						'asideLabel'        => 'Contact support',
						'asideUrl'          => '#',
						'faqSchema'         => true,
					),
					$faq
				),
				'Empty (renders nothing)' => $b( 'chargenet/accordion', array( 'heading' => 'No questions' ) ),
			),
		),
		array(
			'id'          => 'team',
			'title'       => 'Team',
			'description' => 'Without a photo the initials are shown. Email and LinkedIn are optional icon links with hidden text for screen readers.',
			'variants'    => array(
				'Cards, with photos'      => $b(
					'chargenet/team',
					array(
						'heading' => 'Our team',
						'intro'   => 'The people behind the network.',
					),
					$person(
						'Alex Example',
						'Chief Executive',
						array(
							'imageId'  => $img[3] ?? 0,
							'email'    => 'alex@example.com',
							'linkedin' => 'https://www.linkedin.com/',
						)
					)
					. $person(
						'Sam Sample',
						'Chief Technology Officer',
						array(
							'imageId'  => $img[3] ?? 0,
							'linkedin' => 'https://www.linkedin.com/',
						)
					)
					. $person( 'Robin Voorbeeld', 'Head of Partnerships' )
				),
				'Compact, contact cards'  => $b(
					'chargenet/team',
					array(
						'variant'           => 'compact',
						'sectionBackground' => 'paper',
						'heading'           => 'Contact us today',
					),
					$person(
						'Alex Example',
						'Sales',
						array(
							'imageId' => $img[3] ?? 0,
							'email'   => 'info@example.com',
						)
					)
					. $person( 'Sam Sample', 'Support', array( 'email' => 'info@example.com' ) )
					. $person( 'Robin Voorbeeld', 'Partnerships', array( 'email' => 'info@example.com' ) )
				),
				'Empty (renders nothing)' => $b( 'chargenet/team', array( 'heading' => 'No people' ) ),
			),
		),
		array(
			'id'          => 'logo-strip',
			'title'       => 'Logo Strip',
			'description' => 'Logos use the organisation name as alt text; a linked logo opens in a new tab. Empty until logos are added.',
			'variants'    => array(
				'Grayscale, light'        => $b(
					'chargenet/logo-strip',
					array( 'heading' => 'Our partners' ),
					$logo( 4, 'Partner one', array( 'url' => '#' ) ) . $logo( 5, 'Partner two', array( 'url' => '#' ) ) . $logo( 6, 'Partner three' ) . $logo( 4, 'Partner four' )
				),
				'Grayscale, dark'         => $b(
					'chargenet/logo-strip',
					array(
						'sectionBackground' => 'dark',
						'heading'           => 'Co-funded by',
					),
					$logo( 4, 'Funder one' ) . $logo( 5, 'Funder two' )
				),
				'Colour'                  => $b(
					'chargenet/logo-strip',
					array(
						'variant'           => 'colour',
						'sectionBackground' => 'paper',
					),
					$logo( 4, 'Partner one' ) . $logo( 5, 'Partner two' ) . $logo( 6, 'Partner three' )
				),
				'Empty (renders nothing)' => $b( 'chargenet/logo-strip', array( 'heading' => 'No logos yet' ) ),
			),
		),
		array(
			'id'          => 'card-slider',
			'title'       => 'Card Slider',
			'description' => 'A scrollable, snapping row of cards. Previous and next buttons appear when JavaScript runs and the cards overflow; there is no auto-rotation. The title link covers the whole card.',
			'variants'    => array(
				'Image as background'     => $b(
					'chargenet/card-slider',
					array(
						'sectionBackground' => 'paper',
						'eyebrow'           => 'Projects',
						'heading'           => 'What we are working on',
					),
					$slides
				),
				'Image on top'            => $b(
					'chargenet/card-slider',
					array(
						'variant' => 'image-top',
						'heading' => 'Projects',
					),
					$slides
				),
				'Empty (renders nothing)' => $b( 'chargenet/card-slider', array( 'heading' => 'No cards' ) ),
			),
		),
		array(
			'id'          => 'post-grid',
			'title'       => 'Post Grid',
			'description' => 'Newest posts of the current language, as cards. The gallery shows sample posts; on a page it queries real posts and prints nothing when there are none.',
			'variants'    => array(
				'Latest, 3 cards'         => $b(
					'chargenet/post-grid',
					array(
						'sectionBackground' => 'paper',
						'eyebrow'           => 'News',
						'heading'           => 'Latest news',
						'linkLabel'         => 'View all news',
						'linkUrl'           => '#',
					)
				),
				'Featured plus grid'      => $b(
					'chargenet/post-grid',
					array(
						'variant' => 'featured-grid',
						'heading' => 'News and insights',
						'count'   => 5,
					)
				),
				'Empty (renders nothing)' => $b(
					'chargenet/post-grid',
					array(
						'heading'    => 'No posts',
						'categoryId' => 999999,
					)
				),
			),
		),
		array(
			'id'          => 'rich-text',
			'title'       => 'Rich Text',
			'description' => 'A plain column for headings, paragraphs, lists and quotes.',
			'variants'    => array(
				'Default' => $b(
					'chargenet/rich-text',
					array( 'heading' => 'Security by design' ),
					$p( 'ChargeNet runs on infrastructure built to the standards your IT team expects.' ) . chargenet_gallery_ul( array( 'Encrypted traffic', 'Role-based access', 'Audit logs' ) )
				),
			),
		),
		array(
			'id'          => 'rich-text-image',
			'title'       => 'Rich Text and Image',
			'description' => 'Heading and text beside an image; the image sits left or right on wide screens.',
			'variants'    => array(
				'Image right'       => $b(
					'chargenet/rich-text-image',
					array(
						'eyebrow' => 'How it works',
						'heading' => 'Charge at the right price, in the right place',
						'imageId' => $img[2] ?? 0,
					),
					$p( 'ChargeNet connects location owners and carriers on one private charging network.' )
				),
				'Image left, paper' => $b(
					'chargenet/rich-text-image',
					array(
						'sectionBackground' => 'paper',
						'imagePosition'     => 'left',
						'heading'           => 'Your site, your rules',
						'imageId'           => $img[1] ?? 0,
					),
					$p( 'You decide who may charge, and when.' ) . chargenet_gallery_button( 'Contact us' )
				),
			),
		),
		array(
			'id'          => 'cta-band',
			'title'       => 'Call To Action Band',
			'description' => 'A band with a heading, short text and one or more buttons.',
			'variants'    => array(
				'Dark (default)' => $b(
					'chargenet/cta-band',
					array(
						'heading' => 'ChargeNet: the private charging network for you',
						'text'    => 'Join today and charge at the right price, in the right place.',
					),
					chargenet_gallery_button( 'Contact us' )
				),
				'Light'          => $b(
					'chargenet/cta-band',
					array(
						'sectionBackground' => 'light',
						'heading'           => 'Questions?',
					),
					chargenet_gallery_button( 'Contact us' ) . chargenet_gallery_button( 'Read the FAQ', 'secondary' )
				),
			),
		),
	);
}
