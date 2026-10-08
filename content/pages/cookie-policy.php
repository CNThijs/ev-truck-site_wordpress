<?php
/**
 * Cookie policy. The cookie table is the WPConsent shortcode (it lists what bin/setup-wpconsent.php registered);
 * the texts around it are ours. Dutch texts are listed in docs/translation-review.md.
 */
return array(
	'key'    => 'cookie-policy',
	'slugs'  => array(
		'en' => 'cookie-policy',
		'nl' => 'cookiebeleid',
	),
	'titles' => array(
		'en' => 'Cookie Policy',
		'nl' => 'Cookiebeleid',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'h1'       => 'Cookie Policy',
				'intro'    => 'How chargenet.energy uses cookies, and how you change your choice.',
				'sections' => array(
					array( 'What are cookies?', 'Cookies are small files that a website stores in your browser. Some are needed to make the site work. Others tell us how the site is used. We only place the second kind if you accept them.' ),
					array( 'Which cookies we use', 'Essential cookies are always placed. Statistics cookies are placed only after you accept them. We do not use marketing or advertising cookies. The list below is the complete list.' ),
					array( 'Statistics: Google Analytics', 'With your consent we load Google Tag Manager, which runs Google Analytics. It counts page views, clicks on buttons and links, and form submissions, so that we can see which pages and campaigns work. We do not use it for advertising. Before you accept, nothing is loaded from Google. Google processes this data as described in its own privacy statement.' ),
					array( 'Campaign links', 'If you arrive through a campaign link (for example with utm_source), we remember it in the cookie cn_campaign for 30 days, but only if you accepted statistics cookies. If you send us a form, the campaign details are stored with your request. Without your consent they are used only on the page you opened and are not remembered.' ),
					array( 'Change your choice', 'You can change or withdraw your choice at any time with the button below, or with "Cookie settings" in the footer of every page. Your choice is remembered for 180 days. To be able to show that we asked, we keep a record of each choice for 36 months: a random ID (stored in your browser as cn_consent_id), the time, your choice, the language and the version of the banner text. It holds no IP address and nothing about your device.' ),
					array( 'Questions', 'Read our <a href="{privacy}">Privacy Policy</a> or email <a href="mailto:info@chargenet.energy">info@chargenet.energy</a>.' ),
				),
				'button'   => 'Cookie settings',
				'updated'  => 'Last updated: 8 October 2026',
			),
			'nl' => array(
				'h1'       => 'Cookiebeleid',
				'intro'    => 'Hoe chargenet.energy cookies gebruikt en hoe u uw keuze wijzigt.',
				'sections' => array(
					array( 'Wat zijn cookies?', 'Cookies zijn kleine bestanden die een website in uw browser opslaat. Sommige zijn nodig om de site te laten werken. Andere vertellen ons hoe de site wordt gebruikt. Die tweede soort plaatsen wij alleen als u ze accepteert.' ),
					array( 'Welke cookies wij gebruiken', 'Essentiële cookies worden altijd geplaatst. Statistiekcookies plaatsen wij pas nadat u ze heeft geaccepteerd. Wij gebruiken geen marketing- of advertentiecookies. De lijst hieronder is de volledige lijst.' ),
					array( 'Statistieken: Google Analytics', 'Met uw toestemming laden wij Google Tag Manager, dat Google Analytics uitvoert. Het telt paginaweergaven, klikken op knoppen en links en verzonden formulieren, zodat wij zien welke pagina’s en campagnes werken. Wij gebruiken het niet voor advertenties. Zolang u niet accepteert, wordt er niets van Google geladen. Google verwerkt deze gegevens zoals beschreven in de eigen privacyverklaring.' ),
					array( 'Campagnelinks', 'Komt u binnen via een campagnelink (bijvoorbeeld met utm_source), dan onthouden wij dat 30 dagen in de cookie cn_campaign, maar alleen als u statistiekcookies heeft geaccepteerd. Stuurt u ons een formulier, dan worden de campagnegegevens bij uw aanvraag opgeslagen. Zonder uw toestemming worden ze alleen gebruikt op de pagina die u opende en niet onthouden.' ),
					array( 'Uw keuze wijzigen', 'U kunt uw keuze op elk moment wijzigen of intrekken met de knop hieronder, of met "Cookie-instellingen" onderaan elke pagina. Uw keuze wordt 180 dagen onthouden. Om te kunnen aantonen dat wij het vroegen, bewaren wij van elke keuze 36 maanden een registratie: een willekeurig ID (in uw browser opgeslagen als cn_consent_id), het tijdstip, uw keuze, de taal en de versie van de bannertekst. Er staan geen IP-adres en geen gegevens over uw apparaat in.' ),
					array( 'Vragen', 'Lees ons <a href="{privacy}">privacybeleid</a> of mail naar <a href="mailto:info@chargenet.energy">info@chargenet.energy</a>.' ),
				),
				'button'   => 'Cookie-instellingen',
				'updated'  => 'Laatst bijgewerkt: 8 oktober 2026',
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
			$body = cn_p( str_replace( '{privacy}', esc_url( $link( 'privacy' ) ), $s[1] ) );
			if ( 1 === $i ) {
				$body .= "<!-- wp:shortcode -->\n[wpconsent_cookie_policy]\n<!-- /wp:shortcode -->\n";
			}
			if ( 4 === $i ) {
				$body .= "<!-- wp:shortcode -->\n[wpconsent_preferences_button text=\"" . $c['button'] . "\"]\n<!-- /wp:shortcode -->\n";
			}
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
