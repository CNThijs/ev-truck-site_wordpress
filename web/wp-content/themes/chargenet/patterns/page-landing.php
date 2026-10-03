<?php
/**
 * Title: Landing page
 * Slug: chargenet/page-landing
 * Categories: chargenet-pages
 * Post Types: page
 * Description: A call to action, two alternating text-and-image sections and a closing call to action. English.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!-- wp:chargenet/cta-band {"heading":"The headline of the campaign"} -->
<!-- wp:chargenet/button {"label":"Download the report","url":"#download"} /-->
<!-- /wp:chargenet/cta-band -->

<!-- wp:chargenet/rich-text-image {"heading":"First benefit"} -->
<!-- wp:paragraph -->
<p>Describe the benefit.</p>
<!-- /wp:paragraph -->
<!-- /wp:chargenet/rich-text-image -->

<!-- wp:chargenet/rich-text-image {"sectionBackground":"paper","imagePosition":"left","heading":"Second benefit"} -->
<!-- wp:paragraph -->
<p>Describe the benefit.</p>
<!-- /wp:paragraph -->
<!-- /wp:chargenet/rich-text-image -->

<!-- wp:chargenet/cta-band {"heading":"Talk to us","anchor":"download"} -->
<!-- wp:chargenet/button {"label":"Contact us","url":"#contact"} /-->
<!-- /wp:chargenet/cta-band -->
