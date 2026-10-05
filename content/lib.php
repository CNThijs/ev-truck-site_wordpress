<?php
/**
 * Helpers for the content seeder (bin/seed-content.php): block markup builders and shared sections.
 * Pages are written in PHP with the copy of both languages next to each other, so English and Dutch always have the
 * same structure. Not part of the theme zip.
 */

/**
 * Block markup for one block (dynamic sections have attributes and inner blocks, no saved HTML).
 *
 * @param string               $name     Block name, e.g. chargenet/hero.
 * @param array<string, mixed> $attrs    Attributes.
 * @param string               $children Markup of the inner blocks.
 */
function cn_block( string $name, array $attrs = array(), string $children = '' ): string {
	$name = preg_replace( '#^core/#', '', $name );
	$json = $attrs ? ' ' . serialize_block_attributes( $attrs ) : '';
	return '' === $children ? "<!-- wp:{$name}{$json} /-->\n" : "<!-- wp:{$name}{$json} -->\n{$children}<!-- /wp:{$name} -->\n";
}

/**
 * Paragraph. Inline HTML (strong, em, br, a) is allowed.
 *
 * @param string $html Content.
 */
function cn_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n";
}

/**
 * Heading inside a section (sub-heading).
 *
 * @param string $text  Text.
 * @param int    $level Level.
 */
function cn_h( string $text, int $level = 4 ): string {
	return "<!-- wp:heading {\"level\":{$level}} -->\n<h{$level} class=\"wp-block-heading\">{$text}</h{$level}>\n<!-- /wp:heading -->\n";
}

/**
 * List.
 *
 * @param string[] $items   List items (inline HTML allowed).
 * @param bool     $ordered Numbered list.
 */
function cn_list( array $items, bool $ordered = false ): string {
	$tag  = $ordered ? 'ol' : 'ul';
	$html = '';
	foreach ( $items as $item ) {
		$html .= "<!-- wp:list-item -->\n<li>{$item}</li>\n<!-- /wp:list-item -->\n";
	}
	$attr = $ordered ? ' {"ordered":true}' : '';
	return "<!-- wp:list{$attr} -->\n<{$tag} class=\"wp-block-list\">{$html}</{$tag}>\n<!-- /wp:list -->\n";
}

/**
 * Table with a header row. Cells hold inline HTML (the editor's table cells do not take lists or paragraphs).
 *
 * @param string[]   $headers Header cells.
 * @param string[][] $rows    Rows of cells.
 */
function cn_table( array $headers, array $rows ): string {
	$head = '<tr><th>' . implode( '</th><th>', $headers ) . '</th></tr>';
	$body = '';
	foreach ( $rows as $row ) {
		$body .= '<tr><td>' . implode( '</td><td>', $row ) . '</td></tr>';
	}
	return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead>{$head}</thead><tbody>{$body}</tbody></table></figure>\n<!-- /wp:table -->\n";
}

/**
 * Button (child of a section).
 *
 * @param string $label   Label.
 * @param string $url     URL; an empty URL prints nothing (the button block hides itself).
 * @param string $variant primary, secondary or ghost.
 */
function cn_button( string $label, string $url, string $variant = 'primary' ): string {
	if ( '' === $url ) {
		return '';
	}
	return cn_block(
		'chargenet/button',
		array(
			'label'   => $label,
			'url'     => $url,
			'variant' => $variant,
		)
	);
}

/**
 * Bold lead and text for a list item or step: "<strong>Lead:</strong> text".
 *
 * @param string $lead Lead.
 * @param string $text Text.
 */
function cn_lead( string $lead, string $text ): string {
	return "<strong>{$lead}:</strong> {$text}";
}

/**
 * The founders' contact cards, shown at the bottom of every page.
 *
 * @param string   $lang  Language.
 * @param callable $media Media resolver: media( key ) returns the attachment id for the language.
 */
function cn_contact_team( string $lang, callable $media ): string {
	$t      = array(
		'en' => array(
			'eyebrow' => 'Get In Touch',
			'heading' => 'Contact Us Today',
			'intro'   => 'Have questions about our ChargeNet network? Reach out to our team and let us discuss how we can help you to electrify your logistics.',
		),
		'nl' => array(
			'eyebrow' => 'Neem Contact Op',
			'heading' => 'Neem vandaag contact op',
			'intro'   => 'Of u nu een locatiemanager bent die zijn laadinfrastructuur wil geldelijker maken of een wagenpark manager die op zoek is naar betrouwbare laadoplossingen: wij maken het u gemakkelijk om aan de slag te gaan.',
		),
	)[ $lang ];
	$people = array(
		array( 'Sebastiaan de Vries', 'CEO & CoFounder', 'person-seb' ),
		array( 'Thijs Verwaal', 'CISO & CoFounder', 'person-thijs' ),
		array( 'Piotr Krzepczak', 'CTO & CoFounder', 'person-piotr' ),
	);
	$cards  = '';
	foreach ( $people as $person ) {
		$cards .= cn_block(
			'chargenet/team-member',
			array(
				'name'    => $person[0],
				'role'    => $person[1],
				'email'   => 'info@chargenet.energy',
				'imageId' => $media( $person[2] ),
			)
		);
	}
	return cn_block(
		'chargenet/team',
		array(
			'variant'           => 'compact',
			'sectionBackground' => 'paper',
			'animation'         => 'fade-rise',
			'anchor'            => 'contact-info',
			'eyebrow'           => $t['eyebrow'],
			'heading'           => $t['heading'],
			'intro'             => $t['intro'],
		),
		$cards
	);
}

/**
 * Logo strip with the two app store badges (only when the badge files are present in content/media).
 *
 * @param string   $lang  Language.
 * @param callable $media Media resolver.
 */
function cn_app_badges( string $lang, callable $media ): string {
	$items = '';
	$names = array(
		'en' => array( 'Download on the App Store', 'Get it on Google Play' ),
		'nl' => array( 'Download in de App Store', 'Ontdek het op Google Play' ),
	)[ $lang ];
	foreach ( array(
		array( 'badge-app-store', $names[0], 'https://apps.apple.com/us/app/chargenet-driver/id6745788861' ),
		array( 'badge-google-play', $names[1], 'https://play.google.com/store/apps/details?id=energy.chargenet.app' ),
	) as $badge ) {
		$id = $media( $badge[0] );
		if ( $id ) {
			$items .= cn_block(
				'chargenet/logo-item',
				array(
					'imageId' => $id,
					'name'    => $badge[1],
					'url'     => $badge[2],
				)
			);
		}
	}
	return '' === $items ? '' : cn_block(
		'chargenet/logo-strip',
		array(
			'variant'     => 'colour',
			'spaceTop'    => 'none',
			'spaceBottom' => 'md',
		),
		$items
	);
}
