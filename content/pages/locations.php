<?php
/**
 * Locations. English with typos fixed; Dutch is the live copy with the audience fixes listed in
 * docs/translation-review.md (steps 4 and 5 and the closing text spoke to carriers, not location owners).
 */
require_once __DIR__ . '/_audience.php';

return array(
	'key'    => 'locations',
	'slugs'  => array(
		'en' => 'locations',
		'nl' => 'locaties',
	),
	'titles' => array(
		'en' => 'Locations',
		'nl' => 'Locaties',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'h1'         => 'Solve netcongestion & start earning!',
				'intro'      => 'Become part of our network of privately-owned charging locations and start earning by selling your unused charging capacity to your partners & neighbours.',
				'pitch_h'    => 'Sell unused charging capacity to your partners & neighbours',
				'pitch'      => array(
					'Most charge points for EV Trucks have a low occupancy rate, as trucks are on the road during the day and only parked in the evenings. The unused charging potential can be a major contribution to solving netcongestion in your region.',
					'We have built a nationwide charging network of privately-owned chargers for EV trucks, so that you can share your unused charging capacity without impacting your own operations. You have full control over your locations and charge points, pricing and opening hours.',
					'For frequent visitors, we offer the opportunity to create pricing proposals with the carriers, to contractually agree on even better pricing per kWh for a fixed minimum of capacity.',
				),
				'benefits_h' => 'Our Benefits',
				'benefits'   => array(
					array( 'bar-chart', 'Competitive pricing', 'Offer competitive prices up to 40% more attractive than roaming or public charging.' ),
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
					array( 'Add locations & connect your charge points', 'After sign up, your organisation administrator can add locations and connect to the existing charge points.', array( 'Create your locations', 'Insert location token to connect to your CPO / integrator', 'Automatically retrieve your existing charge points' ) ),
					array( 'Configure location settings', 'After the locations have been created, your organisation administrator can configure the settings of each location, like the opening hours & pricing.', array( 'Configure location type: private / semi-public / public', 'Set your kWh pricing', 'Set opening hours & location details', 'Add allowed parties for semi-public access' ) ),
					array( 'Optimize revenue with pricing proposals', 'For frequent visitors, we offer the opportunity to create pricing proposals with the carriers, to contractually agree on even better pricing per kWh for a fixed minimum of capacity.', array( 'Identify frequent visiting carriers', 'Create a pricing proposal', 'Reach pricing & volume agreement', 'Immediately charge against new pricing' ) ),
					array( 'Automated administration & billing process', 'The organisation admin will automatically receive periodic overviews of the usage of your charge points as well as the invoices.', array( 'Automated charging reporting', 'Automated invoicing' ) ),
				),
				'cta_h'      => 'ChargeNet; the private charging network for you!',
				'cta_t'      => 'Contact us today to learn more about the benefits of adding your locations to ChargeNet.',
				'claims'     => array(
					array( 'trending-up', 'New Revenue Stream', 'Monetize unused charging capacity.' ),
					array( 'plug', 'Easy Setup', 'We handle connectivity and customer access.' ),
					array( 'truck', 'Existing Demand', 'Connect with fleets already on our network.' ),
					array( 'wallet', 'Simple Payments', 'One app, one invoice, transparent pricing.' ),
				),
				'join_h'     => 'Join today to help solving netcongestion & start earning!',
				'join_b'     => 'Contact us',
			),
			'nl' => array(
				'h1'         => 'Help netwerkcongestie oplossen en start met geld verdienen!',
				'intro'      => 'Sluit u aan bij ons netwerk van private laadlocaties en begin met verdienen door uw ongebruikte laadcapaciteit te verkopen aan uw partners en buren.',
				'pitch_h'    => 'Verkoop ongebruikte laadcapaciteit aan uw partners & buren',
				'pitch'      => array(
					'De meeste laadpunten voor elektrische vrachtwagens hebben een lage bezettingsgraad, omdat vrachtwagens overdag onderweg zijn en alleen ’s avonds geparkeerd staan. Het onbenutte laadpotentieel kan een belangrijke bijdrage leveren aan het oplossen van de netcongestie in uw regio.',
					'We hebben een landelijk netwerk van private laadpunten voor elektrische vrachtwagens opgebouwd, zodat u uw ongebruikte laadcapaciteit kunt delen zonder uw eigen activiteiten te beïnvloeden. U heeft volledige controle over uw locaties en laadpunten, prijzen en openingstijden.',
					'Voor frequente bezoekers bieden we de mogelijkheid om prijsvoorstellen te maken met de vervoerders, om contractueel nog betere prijzen per kWh af te spreken voor een vaste minimale capaciteit.',
				),
				'benefits_h' => 'Onze meerwaarde',
				'benefits'   => array(
					array( 'bar-chart', 'Concurrerende prijzen', 'Bied concurrerende prijzen die tot 40% aantrekkelijker zijn dan roaming of openbare oplaadpunten.' ),
					array( 'layers', 'Eén platform', 'Werk met een onbeperkt aantal partners, allemaal op één platform voor uw volledige supply chain.' ),
					array( 'sliders', 'Controle & flexibiliteit', 'Volledig aanpasbaar aan uw wensen op locatie- en partnerniveau – elke samenwerking is uniek.' ),
					array( 'smartphone', 'Gebruiksvriendelijk', 'Ons platform is speciaal ontwikkeld voor samenwerking binnen 3PL-wegtransport.' ),
					array( 'lock', 'Privétoegang', 'Beheer de toegang tot uw privé laadlocaties binnen het ChargeNet-netwerk.' ),
					array( 'shield-check', 'Beveiliging vanaf ontwerp', 'Wij passen de nieuwste cybersecurity-standaarden toe om risico’s voor uw operatie te minimaliseren.' ),
				),
				'steps_h'    => 'Hoe het werkt',
				'activities' => 'Belangrijkste activiteiten:',
				'steps'      => array(
					array( 'Meld u aan voor ons netwerk', 'Neem contact met ons op om u aan te melden voor het ChargeNet-netwerk.', array( 'Neem contact met ons op om u aan te melden', 'Maak uw organisatiebeheerdersaccount aan', 'Configureer uw organisatie' ) ),
					array( 'Voeg locaties toe en verbind uw laadpunten', 'Na aanmelding kan de beheerder van uw organisatie locaties toevoegen en verbinding maken met de bestaande laadpunten.', array( 'Maak uw locaties aan', 'Voer een locatietoken in om verbinding te maken met uw CPO / integrator', 'Haal automatisch uw bestaande laadpunten op' ) ),
					array( 'Locatie-instellingen configureren', 'Nadat de locaties zijn aangemaakt, kan de beheerder van uw organisatie de instellingen van elke locatie configureren, zoals de openingstijden en prijzen.', array( 'Configureer locatietype: privé / semi-publiek / publiek', 'Stel uw kWh-prijs in', 'Stel openingstijden en locatiegegevens in', 'Voeg toegestane partijen toe voor semi-openbare toegang' ) ),
					array( 'Optimaliseer opbrengst met prijsvoorstellen', 'Voor frequente bezoekers kunt u prijsvoorstellen maken met de vervoerders, om contractueel nog betere prijzen per kWh af te spreken voor een vaste minimale capaciteit.', array( 'Identificeer vervoerders die vaak langskomen', 'Een prijsvoorstel maken', 'Prijs- en volumeafspraken overeenkomen', 'Direct laden tegen de nieuwe prijzen' ) ),
					array( 'Geautomatiseerd administratie- & facturatieproces', 'De beheerder van de organisatie ontvangt automatisch periodieke overzichten van het gebruik van uw laadpunten en de facturen.', array( 'Geautomatiseerde laadrapportage', 'Geautomatiseerde facturatie' ) ),
				),
				'cta_h'      => 'ChargeNet; het private laadnetwerk voor u!',
				'cta_t'      => 'Neem vandaag nog contact met ons op voor meer informatie over de voordelen van het toevoegen van uw locaties aan ChargeNet.',
				'claims'     => array(
					array( 'trending-up', 'Nieuwe inkomstenbron', 'Verdien geld met ongebruikte laadcapaciteit.' ),
					array( 'plug', 'Eenvoudige installatie', 'Wij regelen de connectiviteit en toegang voor klanten.' ),
					array( 'truck', 'Bestaande vraag', 'Maak verbinding met wagenparken die al op ons netwerk zijn aangesloten.' ),
					array( 'wallet', 'Eenvoudige betalingen', 'Eén app, één factuur, transparante prijzen.' ),
				),
				'join_h'     => 'Word vandaag nog lid, help netwerkcongestie oplossen en begin met verdienen!',
				'join_b'     => 'Neem contact op',
			),
		)[ $lang ];

		return cn_audience_page( $lang, $c, $media );
	},
);
