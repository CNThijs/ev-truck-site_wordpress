<?php
/**
 * Accessibility statement. Facts come from docs/accessibility.md. The conformance sentence says "aims to" and names
 * what was checked; update it when the manual test script has been run. Dutch texts are listed in
 * docs/translation-review.md.
 */
return array(
	'key'    => 'accessibility',
	'slugs'  => array(
		'en' => 'accessibility',
		'nl' => 'toegankelijkheid',
	),
	'titles' => array(
		'en' => 'Accessibility',
		'nl' => 'Toegankelijkheid',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'h1'       => 'Accessibility statement',
				'intro'    => 'ChargeNet wants everyone to be able to use chargenet.energy.',
				'sections' => array(
					array( 'Our standard', 'We aim to meet the Web Content Accessibility Guidelines (WCAG) 2.2, level AA, on every page of this website in English and Dutch.' ),
					array( 'What we checked', 'Every page is tested automatically for accessibility problems, including with the cookie banner open. The site can be used with a keyboard, text can be enlarged, and animation is reduced or switched off when your device asks for reduced motion. Forms have labels and clear error messages. A manual test with a screen reader is part of our release checklist.' ),
					array( 'Known limitations', 'Some older news articles contain images or documents that came from the previous website and may lack a full text description. The Trend Report is a PDF and may not be fully accessible. Ask us for another format if you need one.' ),
					array( 'Report a problem', 'Cannot use something on this site, or found a barrier? Email <a href="mailto:info@chargenet.energy">info@chargenet.energy</a> and tell us the page and what went wrong. We reply within five working days and will offer you the information in another way where we can.' ),
				),
				'updated'  => 'Last updated: 14 October 2026',
			),
			'nl' => array(
				'h1'       => 'Toegankelijkheidsverklaring',
				'intro'    => 'ChargeNet wil dat iedereen chargenet.energy kan gebruiken.',
				'sections' => array(
					array( 'Onze norm', 'Wij streven ernaar dat elke pagina van deze website, in het Nederlands en Engels, voldoet aan de Web Content Accessibility Guidelines (WCAG) 2.2, niveau AA.' ),
					array( 'Wat wij hebben gecontroleerd', 'Elke pagina wordt automatisch getest op toegankelijkheidsproblemen, ook met de cookiebanner geopend. De site is met het toetsenbord te gebruiken, tekst kan worden vergroot en animaties worden beperkt of uitgezet als uw apparaat daarom vraagt. Formulieren hebben labels en duidelijke foutmeldingen. Een handmatige test met een schermlezer maakt deel uit van onze checklist voor elke release.' ),
					array( 'Bekende beperkingen', 'Enkele oudere nieuwsberichten bevatten afbeeldingen of documenten van de vorige website die mogelijk geen volledige tekstbeschrijving hebben. Het Trendrapport is een pdf en is mogelijk niet volledig toegankelijk. Vraag ons om een ander formaat als u dat nodig heeft.' ),
					array( 'Een probleem melden', 'Kunt u iets op deze site niet gebruiken of vond u een drempel? Mail naar <a href="mailto:info@chargenet.energy">info@chargenet.energy</a> en vertel ons de pagina en wat er misging. Wij reageren binnen vijf werkdagen en bieden u de informatie waar mogelijk op een andere manier aan.' ),
				),
				'updated'  => 'Laatst bijgewerkt: 14 oktober 2026',
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
