<?php
/**
 * Rank Math SEO settings for the site. Run by bin/setup-wp.sh after the plugin is activated; safe to run again:
 * `wp eval-file bin/setup-rankmath.php`. Free plugin, no Rank Math account: the registration step is skipped.
 *
 * Choices: only the sitemap and the structured data modules stay on (analytics, AI, link counter, 404 and
 * redirection tools off: lighter admin, no data sent to Rank Math); the site is an organisation; posts are
 * BlogPostings; Twitter card with a large image; author and date archives are off.
 */

if ( ! defined( 'RANK_MATH_VERSION' ) ) {
	WP_CLI::error( 'Rank Math is not active.' );
}

update_option( 'rank_math_registration_skip', true );
update_option( 'rank_math_wizard_completed', true );
update_option( 'rank_math_is_configured', true );
update_option( 'rank_math_modules', array( 'sitemap', 'rich-snippet' ) );

$general                          = (array) get_option( 'rank-math-options-general', array() );
$general['frontend_seo_score']     = 'off';
$general['new_window_external_links'] = 'off';
$general['setup_mode']            = 'advanced';
update_option( 'rank-math-options-general', $general );

$titles                                = (array) get_option( 'rank-math-options-titles', array() );
$titles['knowledgegraph_type']         = 'company';
$titles['knowledgegraph_name']         = 'ChargeNet';
$titles['website_name']                = 'ChargeNet';
$titles['twitter_card_type']           = 'summary_large_image';
$titles['disable_author_archives']     = 'on';
$titles['disable_date_archives']       = 'on';
$titles['pt_post_default_rich_snippet'] = 'article';
$titles['pt_post_default_article_type'] = 'BlogPosting';
$titles['pt_post_title']               = '%title% %sep% %sitename%';
$titles['pt_post_slack_enhanced_sharing'] = 'off'; // The "Written by" and "Time to read" labels name a WordPress user; the author is the post's own reference.
$titles['pt_post_description']         = '%excerpt%';
$titles['pt_page_default_rich_snippet'] = 'off';
$titles['tax_category_title']          = '%term% %sep% %sitename%';
$titles['homepage_title']              = '%sitename% %sep% Keep charging ahead';
update_option( 'rank-math-options-titles', $titles );

$sitemap                       = (array) get_option( 'rank-math-options-sitemap', array() );
$sitemap['authors_sitemap']    = 'off';
$sitemap['html_sitemap']       = 'off';
$sitemap['include_images']     = 'on';
$sitemap['pt_post_sitemap']    = 'on';
$sitemap['pt_page_sitemap']    = 'on';
$sitemap['tax_category_sitemap'] = 'on';
update_option( 'rank-math-options-sitemap', $sitemap );

flush_rewrite_rules( false );
echo "Rank Math configured.\n";
