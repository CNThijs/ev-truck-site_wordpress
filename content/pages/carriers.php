<?php
/**
 * Carriers. English with typos fixed; Dutch is the live copy ("Key Activities:" labels and two untranslated
 * words filled in, listed in docs/translation-review.md).
 */
require_once __DIR__ . '/_audience.php';

return array(
	'key'    => 'carriers',
	'slugs'  => array(
		'en' => 'carriers',
		'nl' => 'vervoerders',
	),
	'titles' => array(
		'en' => 'Carriers',
		'nl' => 'Vervoerders',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'h1'         => 'Expand your charging opportunities today',
				'intro'      => 'Get access to our network of privately-owned charging locations, to charge your EV trucks on your destination against a lower price.',
				'pitch_h'    => 'Reduce your down-time: Start charging on your destination!',
				'pitch'      => array(
					'Are you limited by netcongestion? Do you need to stop during your trip to charge your EV truck at an expensive charging station next to the highway?',
					'We have built a nationwide charging network of privately-owned chargers for EV trucks, so that you can charge at the right price, at the right place, at the right time.',
					'You can find charging locations on your destination using our mobile app and get access to start charging by a push of a button.',
					'For frequent visits, we offer the opportunity to create pricing proposals with the location owners, to contractually agree on even better pricing per kWh for a fixed minimum of capacity.',
				),
				'benefits_h' => 'Our Benefits',
				'benefits'   => array(
					array( 'euro', 'Reduce charging costs', 'Reduce up to 60% of your charging costs compared to roaming or public charging.' ),
					array( 'layers', 'One Platform', 'Work with an unlimited number of partners, all on one platform for your whole supply chain.' ),
					array( 'sliders', 'Control & Flexibility', 'Fully adaptable to your wishes on location and partner level, as each relation is unique.' ),
					array( 'smartphone', 'Easy to Use', 'Our platform is uniquely developed to collaborate in the 3PL road transport relationship.' ),
					array( 'lock', 'Private access', 'Get access to privately-owned charging locations that are part of the ChargeNet network.' ),
					array( 'shield-check', 'Security by Design', 'We apply the latest cybersecurity standards in our product development to minimise risk to your operations.' ),
				),
				'steps_h'    => 'How it works',
				'activities' => 'Key Activities:',
				'steps'      => array(
					array( 'Sign Up to Join our Network', 'Contact us to sign up for the ChargeNet Network.', array( 'Contact us to Sign Up', 'Create your organisation administrator account', 'Configure your organisation' ) ),
					array( 'Add your Drivers & Plan their Routes', 'After sign up, your organisation administrator can add all the drivers and plan their routes.', array( 'Invite your drivers', 'Plan their routes, to identify the best charging locations' ) ),
					array( 'Download the app & Start Charging', 'After the drivers have been invited, they can download the app, complete their registration and start charging.', array( 'Drivers receive invite per email', 'They download the app & complete their registration', 'They can start charging' ) ),
					array( 'Optimize costs with pricing proposals', 'For frequent visits, we offer the opportunity to create pricing proposals with the location owners, to contractually agree on even better pricing per kWh for a fixed minimum of capacity.', array( 'Identify frequently used charging locations', 'Create a pricing proposal', 'Reach pricing & volume agreement', 'Immediately charge against new pricing' ) ),
					array( 'Automated administration & billing process', 'The organisation admin will automatically receive periodic overviews of the charging behaviour as well as the invoices.', array( 'Automated charging reporting', 'Automated invoicing' ) ),
				),
				'cta_h'      => 'ChargeNet; the private charging network for you!',
				'cta_t'      => 'Contact us today to learn more about the benefits of ChargeNet for your fleet of EV trucks.',
				'claims'     => array(
					array( 'map-pin', 'Wide Coverage', 'Access a nationwide growing CCS2 charging network.' ),
					array( 'truck', 'Optimized for Trucks', 'Locations designed for heavy vehicles (turning radius, parking space).' ),
					array( 'wallet', 'Simple Payments', 'One app, one invoice, transparent pricing.' ),
					array( 'clock', 'Reduced Downtime', 'Don\'t stop during your trip, but charge on your destination.' ),
				),
				'join_h'     => 'Join today & start charging on your destination!',
				'join_b'     => 'Contact us',
			),
			'nl' => array(
				'h1'         => 'Breid vandaag uw laadmogelijkheden uit',
				'intro'      => 'Krijg toegang tot ons netwerk van private oplaadlocaties, waar u uw elektrische vrachtwagens op uw bestemming kunt opladen tegen een lagere prijs.',
				'pitch_h'    => 'Verminder uw downtime: begin met laden op uw bestemming!',
				'pitch'      => array(
					'Wordt u beperkt door netwerkcongestie? Moet u tijdens uw reis stoppen om uw elektrische vrachtwagen op te laden bij een duur laadstation langs de snelweg?',
					'We hebben een landelijk netwerk van private laadpunten voor elektrische vrachtwagens aangelegd, zodat u tegen de juiste prijs, op de juiste plaats en op het juiste moment kunt laden.',
					'U kunt laadlocaties op uw bestemming vinden via onze mobiele app en met één druk op de knop toegang krijgen om te beginnen met laden.',
					'Voor frequente bezoeken bieden we de mogelijkheid om prijsvoorstellen te maken met de locatie-eigenaren, om contractueel nog betere prijzen per kWh af te spreken voor een vaste minimale capaciteit.',
				),
				'benefits_h' => 'Onze meerwaarde',
				'benefits'   => array(
					array( 'euro', 'Verlaag laadkosten', 'Bespaar tot 60% op uw laadkosten vergeleken met roaming of publiek laden.' ),
					array( 'layers', 'Eén platform', 'Werk met een onbeperkt aantal partners, allemaal op één platform voor uw volledige supply chain.' ),
					array( 'sliders', 'Controle & flexibiliteit', 'Volledig aanpasbaar aan uw wensen op locatie- en partnerniveau – elke samenwerking is uniek.' ),
					array( 'smartphone', 'Gebruiksvriendelijk', 'Ons platform is speciaal ontwikkeld voor samenwerking binnen 3PL-wegtransport.' ),
					array( 'lock', 'Privétoegang', 'Krijg toegang tot privé laadlocaties binnen het ChargeNet-netwerk.' ),
					array( 'shield-check', 'Beveiliging vanaf ontwerp', 'Wij passen de nieuwste cybersecurity-standaarden toe om risico’s voor uw operatie te minimaliseren.' ),
				),
				'steps_h'    => 'Hoe het werkt',
				'activities' => 'Belangrijkste activiteiten:',
				'steps'      => array(
					array( 'Meld u aan voor ons netwerk', 'Neem contact met ons op om u aan te melden voor het ChargeNet-netwerk.', array( 'Neem contact met ons op om u aan te melden', 'Maak uw organisatiebeheerdersaccount aan', 'Configureer uw organisatie' ) ),
					array( 'Voeg uw chauffeurs toe en plan hun routes', 'Nadat u zich heeft aangemeld, kan u als beheerder van de organisatie alle chauffeurs toevoegen en hun routes plannen.', array( 'Nodig uw chauffeurs uit', 'Plan hun routes om de beste laadlocaties te identificeren' ) ),
					array( 'Download de app & begin met laden', 'Nadat de chauffeurs zijn uitgenodigd, kunnen ze de app downloaden, hun registratie voltooien en beginnen met laden.', array( 'Bestuurders ontvangen een uitnodiging per e-mail', 'Ze downloaden de app en voltooien hun registratie', 'Ze kunnen beginnen met opladen' ) ),
					array( 'Optimaliseer kosten met prijsvoorstellen', 'Voor frequente bezoeken kunt u prijsvoorstellen maken voor de locatie-eigenaren, om contractueel nog lagere prijzen per kWh af te spreken voor een bepaalde minimale vraagcapaciteit.', array( 'Veelgebruikte laadlocaties identificeren', 'Een prijsvoorstel maken', 'Prijs- en volumeafspraken overeenkomen', 'Direct laden tegen de nieuwe prijzen' ) ),
					array( 'Geautomatiseerd administratie- & facturatieproces', 'De beheerder van de organisatie ontvangt automatisch periodieke overzichten van het laadgedrag en de facturen.', array( 'Geautomatiseerde laadrapportage', 'Geautomatiseerde facturatie' ) ),
				),
				'cta_h'      => 'ChargeNet; hét private laadnetwerk voor u!',
				'cta_t'      => 'Neem vandaag nog contact met ons op voor meer informatie over de voordelen van ChargeNet voor uw wagenpark met elektrische vrachtwagens.',
				'claims'     => array(
					array( 'map-pin', 'Hoge dekkingsgraad', 'Toegang tot een landelijk groeiend CCS2-laadnetwerk.' ),
					array( 'truck', 'Geoptimaliseerd voor vrachtwagens', 'Locaties speciaal ontworpen voor zware voertuigen (draaicirkel, parkeerruimte).' ),
					array( 'wallet', 'Eenvoudige betalingen', 'Eén app, één factuur, transparante prijzen.' ),
					array( 'clock', 'Minder stilstand', 'Stop niet tijdens uw reis, maar laad op uw bestemming.' ),
				),
				'join_h'     => 'Word vandaag nog lid en begin met laden op uw bestemming!',
				'join_b'     => 'Neem contact op',
			),
		)[ $lang ];

		return cn_audience_page( $lang, $c, $media );
	},
);
