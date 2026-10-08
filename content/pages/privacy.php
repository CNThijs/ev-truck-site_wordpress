<?php
/**
 * Privacy policy. English is the live policy (privacy-en.json, converted from the old site); privacy-nl.json is a
 * Dutch translation of it (the live Dutch page shows the English text), listed in docs/translation-review.md.
 */

/**
 * One table cell: text, a list (bulleted lines) or paragraphs.
 *
 * @param string|array $cell Cell data.
 */
function cn_privacy_cell( $cell ): string {
	if ( is_string( $cell ) ) {
		return cn_privacy_inline( $cell );
	}
	if ( isset( $cell['ul'] ) ) {
		return implode( '<br>', array_map( static fn( $item ) => '– ' . cn_privacy_inline( $item ), $cell['ul'] ) );
	}
	return implode( '<br><br>', array_map( 'cn_privacy_inline', $cell['p'] ) );
}

/**
 * Inline text of the policy: escaped, with the privacy address linked and <strong> kept.
 *
 * @param string $text Text.
 */
function cn_privacy_inline( string $text ): string {
	$html = str_replace( array( '&lt;strong&gt;', '&lt;/strong&gt;' ), array( '<strong>', '</strong>' ), esc_html( $text ) );
	$html = str_replace( 'privacy@chargenet.energy', '<a href="mailto:privacy@chargenet.energy">privacy@chargenet.energy</a>', $html );
	// The live text points to a list, a page and a page without giving URLs: placeholders until they are filled in.
	return preg_replace( '/\b(this link|this webpage|deze link|deze webpagina)\b/', '<a href="#">$1</a>', $html );
}

return array(
	'key'    => 'privacy',
	'slugs'  => array(
		'en' => 'privacy-policy',
		'nl' => 'privacybeleid',
	),
	'titles' => array(
		'en' => 'Privacy Policy',
		'nl' => 'Privacybeleid',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$data = json_decode( (string) file_get_contents( __DIR__ . "/privacy-{$lang}.json" ), true );
		$t    = array(
			'en' => array(
				'h1'    => 'Privacy Policy',
				'intro' => 'Your privacy is important to us at ChargeNet.',
			),
			'nl' => array(
				'h1'    => 'Privacybeleid',
				'intro' => 'Uw privacy is belangrijk voor ons bij ChargeNet.',
			),
		)[ $lang ];

		$cookie_link = '<a href="' . esc_url( $link( 'cookie-policy' ) ) . '">' . ( 'nl' === $lang ? 'cookiebeleid' : 'Cookie Policy' ) . '</a>';

		$out = cn_block(
			'chargenet/hero',
			array(
				'variant' => 'title-band',
				'heading' => $t['h1'],
				'intro'   => $t['intro'],
			)
		);

		$out .= cn_block( 'chargenet/rich-text', array( 'spaceBottom' => 'sm' ), cn_p( cn_privacy_inline( $data['intro'] ) ) );

		foreach ( $data['sections'] as $i => $section ) {
			$body = '';
			foreach ( $section['nodes'] as $node ) {
				if ( isset( $node['p'] ) ) {
					$body .= cn_p( str_replace( '{cookie_policy}', $cookie_link, cn_privacy_inline( $node['p'] ) ) );
				} elseif ( isset( $node['table'] ) ) {
					$body .= cn_table(
						array_map( 'cn_privacy_inline', $node['table']['headers'] ),
						array_map( static fn( $row ) => array_map( 'cn_privacy_cell', $row ), $node['table']['rows'] )
					);
				} else {
					$body .= cn_list( array_map( 'cn_privacy_inline', $node['ul'] ) );
				}
			}
			if ( count( $data['sections'] ) - 1 === $i ) {
				$body .= cn_p( '<em>' . esc_html( $data['updated'] ) . '</em>' );
			}
			$out .= cn_block(
				'chargenet/rich-text',
				array(
					'spaceTop'    => 'sm',
					'spaceBottom' => 'sm',
					'heading'     => ( $i + 1 ) . '. ' . $section['title'],
				),
				$body
			);
		}

		return $out;
	},
);
