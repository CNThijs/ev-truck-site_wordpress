<?php
/**
 * FAQ for drivers. English with typos fixed; Dutch is the live copy with je/jouw changed to u/uw (listed in
 * docs/translation-review.md).
 */
return array(
	'key'    => 'faq',
	'slugs'  => array(
		'en' => 'faq',
		'nl' => 'veelgestelde-vragen',
	),
	'titles' => array(
		'en' => 'FAQ',
		'nl' => 'Veelgestelde vragen',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'h1'      => 'Frequently Asked Questions for Electric Truck Drivers',
				'intro'   => 'Find answers to common questions about using our mobile app for EV truck charging.',
				'aside_h' => 'Still have questions?',
				'aside_t' => 'Our support team is here to help with any issues or questions when connecting to ChargeNet.',
				'aside_b' => 'Contact Support',
				'items'   => array(
					array( 'Where can I download the ChargeNet driver app?', 'Our mobile app is published by ChargeNet BV and can be found in the Google Play Store and the Apple Store.' ),
					array( 'How can I get access to the ChargeNet driver app?', 'Contact your fleet manager to request an account to access the ChargeNet app.' ),
					array( 'How do I find charging stations compatible with my electric truck?', 'Our mobile app gives you access to the ChargeNet network of charging locations. Use the \'Locations\' button to find any charging locations in the area. You can also filter by connector type, charging speed, and availability status in real-time.' ),
					array( 'Can I reserve a charging station in advance for my delivery route?', 'Not yet, but ChargeNet is working hard on offering reservations to its users.' ),
					array( 'How do I track my charging costs and create expense reports?', 'The app automatically tracks all your charging sessions and costs, which will be visible on our platform for now. Navigate to \'Charging Sessions\' on the platform to view detailed reports including date, location, energy consumed, and total cost. You can export monthly reports as PDF or CSV files for easy expense reporting to your fleet manager or accounting department.' ),
					array( 'What should I do if a charging station seems available in the app but turns out to be out of order?', 'If you arrive at a station that\'s not working correctly, please report the issue immediately using the \'Support\' button. Our system will automatically suggest the nearest alternative charging locations with real-time availability.' ),
					array( 'How long does it typically take to charge an electric truck?', 'Charging times vary based on your truck\'s battery capacity and the station\'s power output. For a typical electric truck (300-500 kWh battery), expect 45-90 minutes for an 80% charge at our high-power stations (150-350kW). The app provides real-time charging estimates and sends notifications when your vehicle is ready.' ),
					array( 'Can I monitor my truck\'s charging progress remotely?', 'Absolutely! Once connected, you can monitor your charging session from anywhere. The app shows real-time charging status and accumulated costs.' ),
					array( 'How do I set up fleet billing for multiple trucks?', 'Fleet managers are able to manage multiple vehicles and drivers. Each truck gets a unique identifier, and all charging costs are consolidated into a single weekly invoice. Individual drivers can use the app with their login credentials, and all usage is automatically tracked and billed to the company account.' ),
					array( 'What payment methods are accepted through the app?', 'We currently only support consolidated payments for organisations, and no direct payments. This enables you as driver to start charging immediately - no need to handle cash or cards at the station.' ),
					array( 'What support is available if I need help during charging?', 'ChargeNet provides customer support for its users, via the support button. Our support team is specially trained to help with heavy-duty vehicle charging issues.' ),
					array( 'How can I request to delete my account and associated data?', 'For account deletion, you can reach out to your Fleet Manager to delete your account from the ChargeNet platform. If that is not possible, you can send your account deletion request to <a href="mailto:privacy@chargenet.energy">privacy@chargenet.energy</a>, so that our privacy team can reach out to validate your ownership of the account and then support your request.' ),
				),
			),
			'nl' => array(
				'h1'      => 'Veelgestelde vragen voor elektrische vrachtwagenchauffeurs',
				'intro'   => 'Vind uw antwoord op veelgestelde vragen over het gebruik van onze mobiele app voor het opladen van elektrische vrachtwagens.',
				'aside_h' => 'Heeft u nog vragen?',
				'aside_t' => 'Ons support team staat voor u klaar om u te helpen met eventuele problemen of vragen bij het verbinden met ChargeNet.',
				'aside_b' => 'Contact Support',
				'items'   => array(
					array( 'Waar kan ik de ChargeNet driver app downloaden?', 'Onze mobiele app is uitgegeven door ChargeNet BV en is te vinden in de Google Play Store en de Apple Store.' ),
					array( 'Hoe krijg ik toegang tot de ChargeNet driver app?', 'Neem contact op met uw wagenparkbeheerder om een account aan te vragen voor toegang tot de ChargeNet-app.' ),
					array( 'Hoe vind ik laadstations die compatibel zijn met mijn elektrische vrachtwagen?', 'Onze mobiele app geeft u toegang tot het ChargeNet-netwerk van laadlocaties. Gebruik de knop \'Locaties\' om laadpunten in de buurt te vinden. U kunt ook filteren op stekkertype, laadsnelheid en realtime beschikbaarheid.' ),
					array( 'Kan ik een laadstation vooraf reserveren voor mijn bezorgroute?', 'Nog niet, maar ChargeNet werkt hard aan de mogelijkheid om reserveringen aan te bieden.' ),
					array( 'Hoe houd ik mijn laadkosten bij en maak ik onkostendeclaraties?', 'De app houdt automatisch al uw laadsessies en kosten bij, die voorlopig zichtbaar zijn op ons platform. Ga naar \'Laadsessies\' op het platform om gedetailleerde rapporten te bekijken, inclusief datum, locatie, verbruikte energie en totale kosten. U kunt maandelijkse rapporten exporteren als PDF of CSV-bestand voor eenvoudige declaraties bij uw wagenparkbeheerder of boekhouding.' ),
					array( 'Wat moet ik doen als een laadstation als beschikbaar wordt weergegeven in de app, maar defect blijkt te zijn?', 'Als u bij een station aankomt dat niet goed werkt, meld dit dan direct via de knop \'Support\'. Ons systeem zal automatisch alternatieve laadlocaties in de buurt voorstellen, met realtime beschikbaarheid.' ),
					array( 'Hoe lang duurt het meestal om een elektrische vrachtwagen op te laden?', 'De laadtijd varieert afhankelijk van de batterijcapaciteit van uw vrachtwagen en het vermogen van het laadstation. Voor een typische elektrische vrachtwagen (300-500 kWh batterij) duurt het 45-90 minuten om tot 80% op te laden bij onze snellaadstations (150-350 kW). De app geeft realtime schattingen van laadtijden en stuurt meldingen zodra uw voertuig klaar is.' ),
					array( 'Kan ik op afstand de voortgang van het laden van mijn vrachtwagen volgen?', 'Zeker! Zodra u verbonden bent, kunt u uw laadsessie overal volgen. De app toont de realtime laadstatus en de opgebouwde kosten.' ),
					array( 'Hoe stel ik wagenparkfacturatie in voor meerdere vrachtwagens?', 'Wagenparkbeheerders kunnen meerdere voertuigen en chauffeurs beheren. Elke vrachtwagen krijgt een unieke identificatie, en alle laadkosten worden gebundeld in één wekelijkse factuur. Individuele chauffeurs kunnen de app gebruiken met hun inloggegevens, en alle verbruik wordt automatisch bijgehouden en gefactureerd aan het bedrijfsaccount.' ),
					array( 'Welke betaalmethoden worden via de app geaccepteerd?', 'We ondersteunen momenteel alleen gebundelde betalingen voor organisaties, en geen directe betalingen. Dit stelt u als chauffeur in staat om direct te beginnen met laden – zonder contant geld of betaalpassen te hoeven gebruiken bij het station.' ),
					array( 'Welke ondersteuning is beschikbaar als ik hulp nodig heb tijdens het laden?', 'ChargeNet biedt klantenondersteuning via de support-knop in de app. Ons ondersteuningsteam is speciaal getraind om te helpen bij problemen met het laden van zware voertuigen.' ),
					array( 'Hoe kan ik verzoeken mijn account en bijbehorende gegevens te verwijderen?', 'Voor het verwijderen van een account kunt u uw wagenparkbeheerder vragen om uw account van het ChargeNet-platform te verwijderen. Als dat niet mogelijk is, kunt u een verwijderingsverzoek sturen naar <a href="mailto:privacy@chargenet.energy">privacy@chargenet.energy</a>. Ons privacyteam zal contact met u opnemen om uw eigenaarschap te verifiëren en uw verzoek te ondersteunen.' ),
				),
			),
		)[ $lang ];

		$items = '';
		foreach ( $c['items'] as $item ) {
			$items .= cn_block( 'chargenet/accordion-item', array( 'question' => $item[0] ), cn_p( $item[1] ) );
		}

		return cn_block(
			'chargenet/hero',
			array(
				'variant' => 'title-band',
				'heading' => $c['h1'],
				'intro'   => $c['intro'],
			)
		)
		. cn_block(
			'chargenet/accordion',
			array(
				'variant'      => 'with-aside',
				'faqSchema'    => true,
				'asideHeading' => $c['aside_h'],
				'asideText'    => $c['aside_t'],
				'asideLabel'   => $c['aside_b'],
				'asideUrl'     => '#contact-info',
			),
			$items
		)
		. cn_app_badges( $lang, $media )
		. cn_contact_team( $lang, $media );
	},
);
