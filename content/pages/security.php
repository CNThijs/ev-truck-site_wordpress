<?php
/**
 * Security. English is the live copy. The live Dutch page has English intro and last-updated line, translated here
 * (listed in docs/translation-review.md); the Dutch body is the live copy.
 */
return array(
	'key'    => 'security',
	'slugs'  => array(
		'en' => 'security',
		'nl' => 'beveiliging',
	),
	'titles' => array(
		'en' => 'Security',
		'nl' => 'Beveiliging',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'h1'       => 'Security',
				'intro'    => 'Security is important to us at ChargeNet.',
				'lead'     => 'Security is a fundamental part of how we design, build, and operate our platform. We understand that our customers rely on us to protect sensitive data and to ensure the availability and integrity of their operations. For that reason, cybersecurity and information security are embedded into every stage of our product development and operational processes.',
				'sections' => array(
					array( 'ISO 27001 Compliant Information Security Management System', 'We operate an Information Security Management System that is compliant with ISO 27001 principles. This structured framework helps us identify, assess, and mitigate information security risks in a consistent and auditable way. Our ISMS covers people, processes, and technology, and is continuously reviewed to address evolving threats and regulatory requirements.' ),
					array( 'Secure Product Development', 'We apply the latest cybersecurity standards and industry best practices throughout our product development lifecycle. Security requirements are considered from the design phase onward, including secure architecture, access control, and data protection measures. Regular code reviews, automated testing, and vulnerability assessments help us reduce the risk of security issues before they reach production.' ),
					array( 'Risk Minimisation and Operational Continuity', 'Our security approach is focused on minimising risk to your operations. This includes preventive controls, monitoring, and incident response procedures designed to detect and respond to potential threats quickly. By combining technical safeguards with clear internal policies and employee awareness, we aim to maintain a high level of resilience and reliability.' ),
					array( 'Continuous Improvement', 'Cybersecurity is not a one time effort. We continuously evaluate and improve our security controls based on risk assessments, technological developments, and changes in the threat landscape. This ensures that our platform remains secure, trustworthy, and aligned with current security standards.' ),
				),
				'updated'  => 'Last updated: January 16th, 2026',
			),
			'nl' => array(
				'h1'       => 'Beveiliging',
				'intro'    => 'Beveiliging is belangrijk voor ons bij ChargeNet.',
				'lead'     => 'Beveiliging staat centraal in de manier waarop wij ons platform ontwerpen, ontwikkelen en beheren. Wij begrijpen dat onze klanten op ons vertrouwen voor de bescherming van gevoelige gegevens en de continuïteit van hun bedrijfsvoering. Daarom is cybersecurity integraal onderdeel van al onze ontwikkel en operationele processen.',
				'sections' => array(
					array( 'ISO 27001 Compliant Information Security Management System', 'Wij werken met een Information Security Management System dat compliant met de ISO27001 principes. Dit gestructureerde raamwerk stelt ons in staat om informatiebeveiligingsrisico’s systematisch te identificeren, beoordelen en beheersen. Ons ISMS omvat mensen, processen en technologie, en wordt continu geëvalueerd om in te spelen op nieuwe dreigingen en regelgeving.' ),
					array( 'Veilige Productontwikkeling', 'Tijdens de volledige productontwikkelingscyclus passen wij de nieuwste cybersecurity standaarden en best practices toe. Beveiliging wordt vanaf de ontwerpfase meegenomen, waaronder veilige architectuur, toegangsbeheer en databescherming. Door middel van code reviews, geautomatiseerde tests en kwetsbaarheidsanalyses verkleinen wij het risico op beveiligingsproblemen.' ),
					array( 'Risicobeperking en Continuïteit', 'Onze beveiligingsmaatregelen zijn gericht op het minimaliseren van risico’s voor uw bedrijfsvoering. Dit omvat preventieve maatregelen, monitoring en duidelijke procedures voor incidentrespons, zodat potentiële dreigingen tijdig worden gedetecteerd en aangepakt. Door technische beveiliging te combineren met interne richtlijnen en bewustwording bij medewerkers, waarborgen wij stabiliteit en betrouwbaarheid.' ),
					array( 'Continue Verbetering', 'Cybersecurity is een continu proces. Wij verbeteren onze beveiligingsmaatregelen voortdurend op basis van risicoanalyses, technologische ontwikkelingen en veranderingen in het dreigingslandschap. Zo blijft ons platform veilig, betrouwbaar en in lijn met actuele beveiligingsstandaarden.' ),
				),
				'updated'  => 'Laatst bijgewerkt: 16 januari 2026',
			),
		)[ $lang ];

		$out = cn_block(
			'chargenet/hero',
			array(
				'variant' => 'title-band',
				'heading' => $c['h1'],
				'intro'   => $c['intro'],
			)
		);
		$out .= cn_block( 'chargenet/rich-text', array( 'spaceBottom' => 'sm' ), cn_p( $c['lead'] ) );
		foreach ( $c['sections'] as $i => $s ) {
			$body = cn_p( $s[1] );
			if ( count( $c['sections'] ) - 1 === $i ) {
				$body .= cn_p( '<em>' . $c['updated'] . '</em>' );
			}
			$out .= cn_block(
				'chargenet/rich-text',
				array(
					'spaceTop'    => 'sm',
					'spaceBottom' => 'sm',
					'heading'     => $s[0],
				),
				$body
			);
		}
		return $out;
	},
);
