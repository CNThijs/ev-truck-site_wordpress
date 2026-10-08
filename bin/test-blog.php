<?php
/**
 * Checks for the news posts: author reference, categories, comments, reading time, table of contents, share links,
 * structured data author, alt-text check, and what the importer did. Run: `ddev wp eval-file bin/test-blog.php`
 * (npm run test:blog). Read-only: it changes nothing.
 */

$GLOBALS['cn_failures'] = 0;
$GLOBALS['cn_checks']   = 0;

/**
 * Assertion.
 *
 * @param bool   $ok   Condition.
 * @param string $what Description.
 */
function cn_check( bool $ok, string $what ): void {
	++$GLOBALS['cn_checks'];
	if ( ! $ok ) {
		++$GLOBALS['cn_failures'];
		echo "FAIL: {$what}\n";
	}
}

$map   = require dirname( __DIR__ ) . '/content/blog/_categories.php';
$posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => -1, 'lang' => '' ) );
cn_check( 36 === count( $posts ), '18 posts in two languages' );

$open = 0;
$authors = array();
foreach ( $posts as $post ) {
	$open += 'open' === $post->comment_status ? 1 : 0;
	$authors[ chargenet_post_author( $post->ID ) ] = true;
	$cats = wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) );
	cn_check( 1 === count( $cats ), "one category: {$post->post_name}" );
	cn_check( '' !== (string) get_post_meta( $post->ID, 'rank_math_description', true ), "meta description: {$post->post_name}" );
	cn_check( has_post_thumbnail( $post ), "featured image: {$post->post_name}" );
}
cn_check( 0 === $open, 'comments are closed on every post' );
cn_check( ! comments_open( $posts[0]->ID ) && ! pings_open( $posts[0]->ID ), 'comments_open and pings_open say no' );
cn_check( isset( $authors['ChargeNet'], $authors['Connectr'], $authors['SIRA'] ) && ! isset( $authors['Connectr3'] ) && ! array_filter( array_keys( $authors ), static fn( $a ) => 0 === strpos( $a, 'External:' ) ), 'author references: ChargeNet by default, partners by name' );

$descriptions = array();
foreach ( $posts as $post ) {
	if ( 'en' === pll_get_post_language( $post->ID ) ) {
		$descriptions[] = (string) get_post_meta( $post->ID, 'rank_math_description', true );
	}
}
cn_check( count( $descriptions ) === count( array_unique( $descriptions ) ), 'no two posts share a meta description' );
$excerpts = array();
foreach ( $posts as $post ) {
	if ( 'en' === pll_get_post_language( $post->ID ) ) {
		$excerpts[] = $post->post_excerpt;
	}
}
cn_check( count( $excerpts ) === count( array_unique( $excerpts ) ), 'no two posts share an excerpt' );

$names = array();
foreach ( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false, 'lang' => '' ) ) as $term ) {
	if ( (int) get_option( 'default_category' ) !== $term->term_id && 0 !== strpos( $term->slug, 'uncategorized' ) ) {
		$names[] = html_entity_decode( $term->name );
	}
}
$wanted = array();
foreach ( $map['categories'] as $pair ) {
	$wanted = array_merge( $wanted, array_values( $pair ) );
}
sort( $names );
sort( $wanted );
cn_check( $names === $wanted, 'only the six new categories exist (in both languages)' );

// Helpers.
cn_check( 1 === chargenet_reading_minutes( 'two words' ) && 3 === chargenet_reading_minutes( str_repeat( 'word ', 450 ) ), 'reading time' );
$share = chargenet_share_links( 'https://example.com/a b', 'A & B' );
cn_check( 0 === strpos( $share['linkedin'], 'https://www.linkedin.com/sharing/share-offsite/?url=https%3A%2F%2Fexample.com%2Fa%20b' ) && 0 === strpos( $share['email'], 'mailto:?subject=A%20%26%20B' ), 'share links are encoded' );
$cn_problems = chargenet_content_problems( '<h2>A</h2><h4>B</h4><h1>C</h1><p><a href="/x">Read more</a> <a href="/y">Charging prices</a></p>' );
cn_check( 1 === $cn_problems['h1'] && 1 === $cn_problems['skip'] && 1 === $cn_problems['link'], 'heading and link text check' );
cn_check( 0 === chargenet_images_without_alt( '<img src="a.jpg" alt="A truck">' ) && 2 === chargenet_images_without_alt( '<img src="a.jpg"><img src="b.jpg" alt="">' ), 'alt text check' );

// Table of contents: only in the main text of a single post, from three h2 headings.
$long = get_posts( array( 'name' => 'chargenet-starts-pilot-testing-at-mvs', 'post_type' => 'post', 'numberposts' => 1 ) )[0];
$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'] = new WP_Query( array( 'p' => $long->ID, 'post_type' => 'post' ) );
$GLOBALS['wp_query']->the_post();
$html = apply_filters( 'the_content', $long->post_content );
cn_check( false !== strpos( $html, 'class="post-toc"' ) && 1 === preg_match( '/<h2[^>]* id="[a-z0-9-]+"/', $html ), 'long post: table of contents and heading ids' );
$short = chargenet_post_toc( '<h2>One</h2><p>a</p><h2>Two</h2>' );
cn_check( false === strpos( $short, 'post-toc' ), 'short text: no table of contents' );
wp_reset_postdata();

// Structured data: author is the reference, not the WordPress user.
$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'] = new WP_Query( array( 'p' => $long->ID, 'post_type' => 'post' ) );
$GLOBALS['wp_query']->the_post();
$schema = chargenet_schema_author( array( 'Person' => array( '@type' => 'Person', 'name' => 'admin' ), 'BlogPosting' => array( '@type' => 'BlogPosting', 'author' => array( 'name' => 'admin' ) ) ) );
cn_check( ! isset( $schema['Person'] ) && 'Organization' === $schema['BlogPosting']['author']['@type'] && 'ChargeNet' === $schema['BlogPosting']['author']['name'], 'structured data: the author is an organisation' );
wp_reset_postdata();

echo "{$GLOBALS['cn_checks']} checks, {$GLOBALS['cn_failures']} failed.\n";
if ( $GLOBALS['cn_failures'] ) {
	WP_CLI::halt( 1 );
}
