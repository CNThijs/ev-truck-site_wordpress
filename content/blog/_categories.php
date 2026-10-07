<?php
/**
 * Categories of the news posts and which post belongs to which (by slug). Six categories, each with an English and a
 * Dutch name; the old site's seven categories (Accelerate, Event, Partnership, Collaboration, Product, Business,
 * Update) were mixed, for example "Accelerate" held partnerships, funding, a new location and a media article.
 */
return array(
	'categories' => array(
		'partnerships' => array(
			'en' => 'Partnerships',
			'nl' => 'Partnerschappen',
		),
		'funding'      => array(
			'en' => 'Funding & programmes',
			'nl' => 'Financiering & programma’s',
		),
		'events'       => array(
			'en' => 'Events',
			'nl' => 'Evenementen',
		),
		'product'      => array(
			'en' => 'Product & network',
			'nl' => 'Product & netwerk',
		),
		'market'       => array(
			'en' => 'Market & media',
			'nl' => 'Markt & media',
		),
		'company'      => array(
			'en' => 'Company',
			'nl' => 'Bedrijfsnieuws',
		),
	),
	'posts'      => array(
		'chargenet-and-maxem-announce-ev-truck-charging-partnership' => 'partnerships',
		'chargenet-and-den-hartog-start-a-partnership'               => 'partnerships',
		'chargenet-and-equans-e-mobility-partnership'                => 'partnerships',
		'chargenet-leap24-public-charging-partnership'               => 'partnerships',
		'chargenet-secures-funding-oostnl'                           => 'funding',
		'growth-accelerator-demo-voucher-energy-awarded-to-chargenet' => 'funding',
		'expand-use-cases-with-the-growth-accelerator-demonstration-energy-voucher' => 'funding',
		'kick-off-jtf-ijmond-aan-zet'                                => 'funding',
		'chargenet-present-at-charge-and-connect-event'              => 'events',
		'chargenet-in-the-spotlight-during-the-arnhem-electricity-week-2025' => 'events',
		'chargebase-at-arhem-electricity-week-2025'                  => 'events',
		'move-east-2025'                                             => 'events',
		'chargenet-pitches-at-the-clean-emissionless-construction-market-meeting-of-utrecht' => 'events',
		'chargenet-starts-pilot-testing-at-mvs'                      => 'product',
		'chargenet-adding-new-nieuwegein-charging-site'              => 'product',
		'sales-of-electric-trucks-will-accelerate-ing-research-expects' => 'market',
		'chargenet-featured-by-transport-and-logistiek-nederland'    => 'market',
		'chargenet-moves-to-the-connectr-office-in-arnhem'           => 'company',
	),
);
