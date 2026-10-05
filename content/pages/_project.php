<?php
/**
 * Shared layout of the project pages: title band, the project card text with its image, who is involved, and a
 * contact call to action. The old site only has this card text for each project (its detail pages were copies of
 * one unrelated template and were never live), so the pages hold exactly that. Add more sections here per project
 * when there is more to tell.
 *
 * @param string   $lang  Language.
 * @param array    $c     Copy of this language: label, title, text, tags (list), tags_h, cta_h, cta_b.
 * @param string   $image Media key of the project image.
 * @param callable $media Media resolver.
 */
function cn_project_page( string $lang, array $c, string $image, callable $media ): string {
	$out = cn_block(
		'chargenet/hero',
		array(
			'variant' => 'title-band',
			'eyebrow' => $c['label'],
			'heading' => $c['title'],
			'intro'   => $c['text'],
		)
	);

	$out .= cn_block(
		'chargenet/rich-text-image',
		array(
			'sectionBackground' => 'paper',
			'animation'         => 'fade-rise',
			'heading'           => $c['tags_h'],
			'imageId'           => $media( $image ),
		),
		cn_list( $c['tags'] )
	);

	$out .= cn_block(
		'chargenet/cta-band',
		array(
			'animation' => 'fade-rise',
			'heading'   => $c['cta_h'],
		),
		cn_button( $c['cta_b'], '#contact-info' )
	);

	return $out . cn_contact_team( $lang, $media );
}
