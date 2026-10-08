<?php
/**
 * SEO rules Rank Math (free) does not cover: canonical for the News pages, no indexable duplicates, staging
 * protection, robots.txt, Organization details and structured data on every page. Settings: bin/setup-rankmath.php.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Only production may be indexed. A staging or development copy sets WP_ENVIRONMENT_TYPE (wp-config.php) to staging
 * or development; WordPress treats an unset value as production. Local (DDEV) behaves like production, so the SEO
 * checks run against it; it is not on the internet. Launch checklist: docs/seo.md.
 */
function chargenet_is_indexable_environment(): bool {
	return in_array( wp_get_environment_type(), array( 'production', 'local' ), true );
}

/**
 * Canonical of the News pages. Rank Math with Polylang printed `/en/blog/en/` and `/en/blog/page/2/en/`.
 *
 * @param string $canonical Canonical URL.
 */
function chargenet_canonical( $canonical ) {
	if ( is_home() && ! is_front_page() ) {
		$canonical = (string) get_permalink( get_queried_object_id() );
		$paged     = (int) get_query_var( 'paged' );
		if ( $paged > 1 ) {
			$canonical = trailingslashit( $canonical ) . 'page/' . $paged . '/';
		}
	}
	return $canonical;
}
add_filter( 'rank_math/frontend/canonical', 'chargenet_canonical' );

/**
 * Robots directives: page 2 and later of a list stay out of the index (their posts are in the sitemap and on page 1
 * links), search results too (Rank Math), and a non-production environment is never indexed.
 *
 * @param array<string,string> $robots Directives.
 * @return array<string,string>
 */
function chargenet_robots( $robots ) {
	if ( ! chargenet_is_indexable_environment() || ( is_paged() && ( is_home() || is_archive() ) ) || is_search() ) {
		$robots['index'] = 'noindex';
	}
	if ( ! chargenet_is_indexable_environment() ) {
		$robots['follow'] = 'nofollow';
	}
	return $robots;
}
add_filter( 'rank_math/frontend/robots', 'chargenet_robots' );

/**
 * Header version of the staging rule: it also covers the sitemaps, feeds and files.
 */
function chargenet_staging_header(): void {
	if ( ! chargenet_is_indexable_environment() ) {
		header( 'X-Robots-Tag: noindex, nofollow' );
	}
}
add_action( 'send_headers', 'chargenet_staging_header' );

/**
 * Block everything in robots.txt outside production; in production keep the internal search out.
 *
 * @param string $output Output.
 */
function chargenet_robots_txt( $output ): string {
	if ( ! chargenet_is_indexable_environment() ) {
		return "User-agent: *\nDisallow: /\n";
	}
	return $output . "\nUser-agent: *\nDisallow: /*?s=\nDisallow: /*/search/\n\nSitemap: " . home_url( '/sitemap_index.xml' ) . "\n";
}
add_filter( 'robots_txt', 'chargenet_robots_txt', 20 );

/**
 * Organization details for the structured data (Rank Math's free fields hold only name and logo). Company data as
 * published in the Chamber of Commerce register and the privacy policy.
 *
 * @return array<string,mixed>
 */
function chargenet_organization(): array {
	return array(
		'legalName'    => 'ChargeNet B.V.',
		'url'          => trailingslashit( (string) get_option( 'home' ) ),
		'logo'         => array(
			'@type' => 'ImageObject',
			'url'   => CHARGENET_URI . '/assets/img/chargenet-logo.svg',
		),
		'address'      => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'Westervoortsedijk 73 KB1',
			'postalCode'      => '6827 AV',
			'addressLocality' => 'Arnhem',
			'addressCountry'  => 'NL',
		),
		'identifier'   => array(
			'@type' => 'PropertyValue',
			'name'  => 'KvK',
			'value' => '91202876',
		),
		'email'        => 'info@chargenet.energy',
		'contactPoint' => array(
			'@type'       => 'ContactPoint',
			'contactType' => 'customer support',
			'email'       => 'info@chargenet.energy',
		),
		'sameAs'       => array( 'https://www.linkedin.com/company/chargenet-eu/' ),
	);
}

/**
 * Add the Organization details to Rank Math's Organization entity.
 *
 * @param array<string,mixed> $data JSON-LD graph.
 * @return array<string,mixed>
 */
function chargenet_jsonld_organization( $data ) {
	foreach ( $data as $key => $entity ) {
		if ( is_array( $entity ) && 'Organization' === ( $entity['@type'] ?? '' ) ) {
			$data[ $key ] = array_merge( $entity, chargenet_organization() );
		}
	}
	return $data;
}
add_filter( 'rank_math/json_ld', 'chargenet_jsonld_organization', 99 );

/**
 * Organization, WebSite and WebPage (with the breadcrumb) on every page. Rank Math skips them on pages whose schema
 * type is "off", which is how the theme sets pages.
 */
add_filter( 'rank_math/schema/add_global_entities', '__return_true' );

/**
 * Breadcrumb home label and link in the page's language.
 *
 * @param array<string,string> $strings Breadcrumb strings.
 * @return array<string,string>
 */
function chargenet_breadcrumb_home( $strings ) {
	$strings['home']      = __( 'Home', 'chargenet' );
	$strings['home_link'] = home_url( '/' ); // Polylang: the language home with its trailing slash (`/en/`).
	return $strings;
}
add_filter( 'rank_math/frontend/breadcrumb/strings', 'chargenet_breadcrumb_home' );

/**
 * Share image (1200 x 630) for pages and posts without a featured image. Source: docs/seo.md.
 *
 * @param string $url Image URL Rank Math found.
 */
function chargenet_default_share_image( $url ) {
	return '' === $url ? CHARGENET_URI . '/assets/img/social-default.png' : $url;
}
add_filter( 'rank_math/opengraph/facebook/image', 'chargenet_default_share_image' );
add_filter( 'rank_math/opengraph/twitter/image', 'chargenet_default_share_image' );

/**
 * Meta description of a category archive (the categories have no description of their own).
 *
 * @param string $description Description Rank Math found.
 */
function chargenet_category_description( $description ) {
	if ( '' === $description && is_category() ) {
		/* translators: %s: category name. */
		return sprintf( __( 'News about %s from ChargeNet.', 'chargenet' ), single_cat_title( '', false ) );
	}
	return $description;
}
add_filter( 'rank_math/frontend/description', 'chargenet_category_description' );

/**
 * The News page's own crumb has no link in Rank Math, so the BreadcrumbList data lost it and had one item only.
 *
 * @param array<int,array<int|string,mixed>> $crumbs Breadcrumb trail.
 * @return array<int,array<int|string,mixed>>
 */
function chargenet_breadcrumb_news_link( $crumbs ) {
	if ( is_home() && ! is_front_page() ) {
		foreach ( $crumbs as $i => $crumb ) {
			if ( empty( $crumb[1] ) && empty( $crumb['hide_in_schema'] ) ) { // "Page 2" stays out of the data.
				$crumbs[ $i ][1] = (string) get_permalink( get_queried_object_id() );
			}
		}
	}
	return $crumbs;
}
add_filter( 'rank_math/frontend/breadcrumb/items', 'chargenet_breadcrumb_news_link' );
