<?php
/**
 * News: the posts page. Its content is printed by the theme's home.php: a title band, the filter (categories and
 * search) and the post grid with paging.
 */
return array(
	'key'    => 'blog',
	'slugs'  => array(
		'en' => 'blog',
		'nl' => 'nieuws',
	),
	'titles' => array(
		'en' => 'News',
		'nl' => 'Nieuws',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'h1'    => 'News',
				'intro' => 'Discover the latest trends, updates and news from our network; new charging locations, ChargeNet milestones, and key developments in EV trucking and charging technology.',
			),
			'nl' => array(
				'h1'    => 'Nieuws',
				'intro' => 'Ontdek de nieuwste trends, updates en nieuwtjes uit ons netwerk; nieuwe laadlocaties, ChargeNet-mijlpalen en belangrijke ontwikkelingen in elektrisch vrachtvervoer en laadtechnologie.',
			),
		)[ $lang ];

		return cn_block(
			'chargenet/hero',
			array(
				'variant' => 'title-band',
				'heading' => $c['h1'],
				'intro'   => $c['intro'],
			)
		)
		. cn_block(
			'chargenet/post-filter',
			array(
				'spaceTop'    => 'md',
				'spaceBottom' => 'none',
			)
		)
		. cn_block(
			'chargenet/post-grid',
			array(
				'variant'  => 'featured-grid',
				'count'    => 9,
				'paginate' => true,
				'spaceTop' => 'sm',
			)
		);
	},
);
