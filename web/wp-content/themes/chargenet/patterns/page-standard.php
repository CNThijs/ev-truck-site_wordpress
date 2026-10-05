<?php
/**
 * Title: Standard page
 * Slug: chargenet/page-standard
 * Categories: chargenet-pages
 * Post Types: page
 * Description: Title band, intro text, a text-and-image section and a call to action, with animations set. English.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!-- wp:chargenet/hero {"variant":"title-band","heading":"Page title","intro":"One or two lines that introduce the page."} /-->

<!-- wp:chargenet/rich-text {"animation":"fade-rise","heading":"Introduction"} -->
<!-- wp:paragraph -->
<p>Introduce the page in two or three sentences.</p>
<!-- /wp:paragraph -->
<!-- /wp:chargenet/rich-text -->

<!-- wp:chargenet/rich-text-image {"sectionBackground":"paper","animation":"fade-rise","heading":"A benefit or story"} -->
<!-- wp:paragraph -->
<p>Explain it here and add an image at the side.</p>
<!-- /wp:paragraph -->
<!-- /wp:chargenet/rich-text-image -->

<!-- wp:chargenet/cta-band {"animation":"fade-rise","heading":"Ready to start?"} -->
<!-- wp:chargenet/button {"label":"Contact us","url":"#contact"} /-->
<!-- /wp:chargenet/cta-band -->
