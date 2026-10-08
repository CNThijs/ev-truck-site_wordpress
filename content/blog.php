<?php
/**
 * News posts for the content seeder: the 18 posts of the old site (content/blog/<slug>/: post.json, body.en.html,
 * body.nl.html and the images), as English and Dutch posts linked with Polylang. Called by bin/seed-content.php,
 * which provides chargenet_seed_find() and the language list.
 *
 * Per post: title, slug (the Dutch one with a -nl suffix), date, excerpt, category (content/blog/_categories.php), author
 * reference, featured image, text as blocks and the original source (post meta _chargenet_source_url, shown as "Read the original on ..."). The SEO title and
 * description go into the Rank Math meta keys (a description that two posts share is replaced by the excerpt). Not
 * imported: the keyword list (no tags on the new site).
 */

/**
 * Inner HTML of a DOM node with only the inline markup the blocks allow.
 *
 * @param DOMNode $node Node.
 */
function chargenet_blog_inner( DOMNode $node ): string {
	$html = '';
	foreach ( $node->childNodes as $child ) {
		$html .= $node->ownerDocument->saveHTML( $child );
	}
	return trim( $html );
}

/**
 * Image block for an <img> of a post body. The file is imported once per language.
 *
 * @param DOMElement $img     The img element.
 * @param string     $caption Caption HTML ('' for none).
 * @param callable   $import  Import callback: file name, alt text => attachment id (0 when the file is missing).
 */
function chargenet_blog_image_block( DOMElement $img, string $caption, callable $import ): string {
	$file = basename( (string) wp_parse_url( $img->getAttribute( 'src' ), PHP_URL_PATH ) );
	$alt  = html_entity_decode( $img->getAttribute( 'alt' ), ENT_QUOTES | ENT_HTML5 );
	$id   = $import( $file, $alt );
	if ( ! $id ) {
		return '';
	}
	$url = (string) wp_get_attachment_image_url( $id, 'large' );
	$cap = '' !== $caption ? '<figcaption class="wp-element-caption">' . $caption . '</figcaption>' : '';
	return '<!-- wp:image {"id":' . $id . ',"sizeSlug":"large","linkDestination":"none"} -->' . "\n"
		. '<figure class="wp-block-image size-large"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" class="wp-image-' . $id . '"/>' . $cap . "</figure>\n"
		. "<!-- /wp:image -->\n";
}

/**
 * Block markup for the body HTML of a post.
 *
 * @param string   $html   Body HTML from the old site.
 * @param callable $import Image import callback (see chargenet_blog_image_block()).
 */
function chargenet_blog_blocks( string $html, callable $import ): string {
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="utf-8"?><body>' . $html . '</body>' );
	libxml_clear_errors();
	$body = $dom->getElementsByTagName( 'body' )->item( 0 );

	// The old posts start at h3 under the page's h1: shift by the first heading's level so it becomes h2 (never above h2).
	$shift = 0;
	foreach ( $body->getElementsByTagName( '*' ) as $el ) {
		if ( preg_match( '/^h([2-4])$/', $el->nodeName, $m ) ) {
			$shift = (int) $m[1] - 2;
			break;
		}
	}

	$convert = static function ( DOMNode $parent ) use ( &$convert, $import, $dom, $shift ): string {
		$out = '';
		foreach ( $parent->childNodes as $node ) {
			if ( ! $node instanceof DOMElement ) {
				continue;
			}
			$inner = chargenet_blog_inner( $node );
			switch ( $node->nodeName ) {
				case 'p':
					$out .= '' === $inner ? '' : cn_p( $inner );
					break;
				case 'h2':
				case 'h3':
				case 'h4':
					$out .= cn_h( $inner, max( 2, (int) substr( $node->nodeName, 1 ) - $shift ) );
					break;
				case 'ul':
				case 'ol':
					$items = array();
					foreach ( $node->childNodes as $li ) {
						if ( $li instanceof DOMElement && 'li' === $li->nodeName ) {
							$items[] = chargenet_blog_inner( $li );
						}
					}
					$out .= cn_list( $items, 'ol' === $node->nodeName );
					break;
				case 'blockquote':
					$out .= "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\">" . cn_p( $inner ) . "</blockquote>\n<!-- /wp:quote -->\n";
					break;
				case 'table':
					$out .= "<!-- wp:table -->\n<figure class=\"wp-block-table\">" . $dom->saveHTML( $node ) . "</figure>\n<!-- /wp:table -->\n";
					break;
				case 'figure':
					$img = $node->getElementsByTagName( 'img' )->item( 0 );
					$cap = $node->getElementsByTagName( 'figcaption' )->item( 0 );
					if ( $img instanceof DOMElement ) {
						$out .= chargenet_blog_image_block( $img, $cap ? chargenet_blog_inner( $cap ) : '', $import );
					}
					break;
				case 'img':
					$out .= chargenet_blog_image_block( $node, '', $import );
					break;
				default: // div and anything else: keep its content.
					$out .= $convert( $node );
			}
		}
		return $out;
	};
	return $convert( $body );
}

/**
 * Import an image file of a post once per language and return its attachment id.
 *
 * @param string $file Image file name in the post's folder.
 * @param string $dir  Folder of the post.
 * @param string $lang Language slug.
 * @param string $alt  Alt text for this language ('' marks the image as decoration).
 */
function chargenet_blog_media( string $file, string $dir, string $lang, string $alt ): int {
	$key = 'blog:' . basename( $dir ) . '/' . $file;
	$id  = chargenet_seed_find( '_chargenet_seed_media', $key, $lang );
	if ( ! $id ) {
		$source = $dir . '/' . $file;
		if ( ! is_readable( $source ) ) {
			return 0;
		}
		$tmp = wp_tempnam( $file );
		copy( $source, $tmp );
		$id = media_handle_sideload(
			array(
				'name'     => $file,
				'tmp_name' => $tmp,
			),
			0,
			pathinfo( $file, PATHINFO_FILENAME )
		);
		if ( is_wp_error( $id ) ) {
			WP_CLI::warning( "Blog image {$file}: " . $id->get_error_message() );
			return 0;
		}
		pll_set_post_language( $id, $lang );
		update_post_meta( $id, '_chargenet_seed_media', $key );
	}
	if ( '' !== $alt || '' === (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt ); // The same file as an inline image keeps its alt text.
	}
	return (int) $id;
}

/**
 * The first paragraph of a body as plain text, cut at a word after $max characters (for a description or excerpt).
 *
 * @param string $html Body HTML.
 * @param int    $max  Maximum length.
 */
function chargenet_blog_first_text( string $html, int $max = 155 ): string {
	if ( ! preg_match( '#<p[^>]*>(.*?)</p>#is', $html, $m ) ) {
		return '';
	}
	$text = trim( (string) preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
	return mb_strlen( $text ) <= $max ? $text : rtrim( mb_substr( $text, 0, (int) mb_strrpos( mb_substr( $text, 0, $max ), ' ' ) ), " ,;:.-" ) . '…';
}

/**
 * Author reference of a post: the old label without the "External: " prefix; "Connectr3" was a typo for Connectr.
 *
 * @param string $label Label from post.json.
 */
function chargenet_blog_author( string $label ): string {
	$label = trim( (string) preg_replace( '/^External:\s*/i', '', $label ) );
	return 'Connectr3' === $label ? 'Connectr' : $label;
}

/**
 * Category of a language, created on first use; the two languages are linked as translations.
 *
 * @param array<string, string> $names Name per language.
 * @return array<string, int> Term id per language.
 */
function chargenet_blog_category( array $names ): array {
	$ids = array();
	foreach ( $names as $lang => $name ) {
		$name = (string) $name;
		$found = get_terms(
			array(
				'taxonomy'   => 'category',
				'name'       => $name,
				'hide_empty' => false,
				'lang'       => $lang,
			)
		);
		if ( $found && ! is_wp_error( $found ) ) {
			$ids[ $lang ] = (int) $found[0]->term_id;
			continue;
		}
		$term = wp_insert_term( $name, 'category', array( 'slug' => sanitize_title( $name ) ) );
		if ( is_wp_error( $term ) ) { // The same name in the other language.
			$term = wp_insert_term( $name, 'category', array( 'slug' => sanitize_title( $name ) . '-' . $lang ) );
		}
		if ( is_wp_error( $term ) ) {
			WP_CLI::warning( "Category {$name} ({$lang}): " . $term->get_error_message() );
			continue;
		}
		$ids[ $lang ] = (int) $term['term_id'];
		pll_set_term_language( $ids[ $lang ], $lang );
	}
	if ( count( $ids ) > 1 ) {
		pll_save_term_translations( $ids );
	}
	return $ids;
}

/**
 * Create or update all posts.
 *
 * @param string[] $langs  Language slugs.
 * @param bool     $force  Overwrite posts that were edited since seeding.
 */
function chargenet_seed_blog( array $langs, bool $force ): void {
	global $chargenet_root;
	$count   = 0;
	$map     = require $chargenet_root . '/content/blog/_categories.php';
	$posts   = array();
	$described = array();
	foreach ( glob( $chargenet_root . '/content/blog/*', GLOB_ONLYDIR ) ?: array() as $dir ) {
		$data                 = json_decode( (string) file_get_contents( $dir . '/post.json' ), true );
		$posts[ $dir ]        = $data;
		foreach ( $langs as $lang ) {
			$key               = $lang . '|' . trim( (string) $data['metaDescription'][ $lang ] );
			$described[ $key ] = ( $described[ $key ] ?? 0 ) + 1;
			$key               = 'x' . $lang . '|' . trim( (string) $data['excerpt'][ $lang ] );
			$described[ $key ] = ( $described[ $key ] ?? 0 ) + 1;
		}
	}
	foreach ( $posts as $dir => $data ) {
		$slug = $data['slug'];
		$when = date_create( str_replace( 'Sept ', 'Sep ', $data['date'] ) . ' 09:00:00' );
		if ( ! $when ) {
			WP_CLI::warning( "Post {$slug}: date '{$data['date']}' not understood, skipped." );
			continue;
		}
		$cats  = chargenet_blog_category( $map['categories'][ $map['posts'][ $slug ] ?? '' ] ?? $data['category'] );
		$group = array();

		foreach ( $langs as $lang ) {
			$import  = static fn( string $file, string $alt ): int => chargenet_blog_media( $file, $dir, $lang, $alt );
			$content = chargenet_blog_blocks( str_replace( '`', '’', (string) file_get_contents( "{$dir}/body.{$lang}.html" ) ), $import ); // The old text has backticks for apostrophes.
			// An excerpt that two old posts share was copied from the other post: take the first paragraph instead.
			$excerpt = (string) $data['excerpt'][ $lang ];
			if ( ( $described[ 'x' . $lang . '|' . trim( $excerpt ) ] ?? 0 ) > 1 || '' === trim( $excerpt ) ) {
				$excerpt = chargenet_blog_first_text( (string) file_get_contents( "{$dir}/body.{$lang}.html" ) );
			}
			$id      = chargenet_seed_find( '_chargenet_seed_key', 'post:' . $slug, $lang );
			$postarr = array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_title'    => str_replace( '`', '’', $data['title'][ $lang ] ),
				'post_excerpt'  => str_replace( '`', '’', $excerpt ),
				'post_date'     => $when->format( 'Y-m-d H:i:s' ),
				'post_date_gmt' => get_gmt_from_date( $when->format( 'Y-m-d H:i:s' ) ),
				'post_content'  => wp_slash( $content ),
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			);
			if ( $id ) {
				$stored = (string) get_post_meta( $id, '_chargenet_seed_hash', true );
				if ( '' !== $stored && md5( (string) get_post_field( 'post_content', $id ) ) !== $stored && ! $force ) {
					WP_CLI::warning( "Post {$slug} ({$lang}) was edited since seeding: left alone (use force to overwrite)." );
					$group[ $lang ] = $id;
					continue;
				}
				$postarr['ID'] = $id;
				wp_update_post( $postarr );
			} else {
				$id = (int) wp_insert_post( $postarr );
				pll_set_post_language( $id, $lang );
				++$count;
			}
			// Polylang does not tell two posts of one slug apart in the main query, so the Dutch slug gets a suffix.
			chargenet_seed_set_slug( $id, 'en' === $lang ? $slug : "{$slug}-{$lang}" );

			if ( isset( $cats[ $lang ] ) ) {
				wp_set_post_categories( $id, array( $cats[ $lang ] ) );
			}
			$featured = chargenet_blog_media( basename( $data['featuredImage'] ), $dir, $lang, '' );
			if ( $featured ) {
				set_post_thumbnail( $id, $featured );
			}
			if ( ! empty( $data['externalUrl'] ) ) {
				update_post_meta( $id, '_chargenet_source_url', esc_url_raw( $data['externalUrl'] ) );
			}
			update_post_meta( $id, 'rank_math_title', $data['metaTitle'][ $lang ] );
			// Several old posts share one description (copied from another post): use the excerpt for those.
			$description = trim( (string) $data['metaDescription'][ $lang ] );
			if ( ( $described[ $lang . '|' . $description ] ?? 0 ) > 1 || '' === $description ) {
				$description = $excerpt;
			}
			update_post_meta( $id, 'rank_math_description', str_replace( '`', '’', $description ) );
			$author = chargenet_blog_author( (string) ( $data['author'] ?? '' ) );
			if ( '' !== $author && 'ChargeNet' !== $author ) {
				update_post_meta( $id, '_chargenet_author', $author );
			} else {
				delete_post_meta( $id, '_chargenet_author' );
			}
			update_post_meta( $id, '_chargenet_seed_key', 'post:' . $slug );
			update_post_meta( $id, '_chargenet_seed_hash', md5( (string) get_post_field( 'post_content', $id ) ) );
			$group[ $lang ] = $id;
		}
		pll_save_post_translations( $group );
	}
	chargenet_blog_remove_old_categories( $map );
	echo "News posts: {$count} created.\n";
}

/**
 * Delete the categories of the old import that are empty now (everything but the default and the current ones).
 *
 * @param array<string, mixed> $map Content of content/blog/_categories.php.
 */
function chargenet_blog_remove_old_categories( array $map ): void {
	$keep    = array();
	foreach ( $map['categories'] as $names ) {
		foreach ( $names as $name ) {
			$keep[] = (string) $name;
		}
	}
	$default = (int) get_option( 'default_category' );
	foreach ( get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
			'lang'       => '',
		)
	) as $term ) {
		if ( $term->term_id !== $default && 0 === (int) $term->count && ! in_array( $term->name, $keep, true ) && 'uncategorized' !== substr( $term->slug, 0, 13 ) ) {
			wp_delete_term( $term->term_id, 'category' );
		}
	}
}
