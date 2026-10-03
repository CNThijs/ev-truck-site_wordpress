<?php
/**
 * Title: Landingspagina (Nederlands)
 * Slug: chargenet/page-landing-nl
 * Categories: chargenet-pages
 * Post Types: page
 * Description: Een call-to-action, twee wisselende secties met tekst en afbeelding en een afsluitende call-to-action. Nederlands.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!-- wp:chargenet/cta-band {"heading":"De kop van de campagne"} -->
<!-- wp:chargenet/button {"label":"Download het rapport","url":"#download"} /-->
<!-- /wp:chargenet/cta-band -->

<!-- wp:chargenet/rich-text-image {"heading":"Eerste voordeel"} -->
<!-- wp:paragraph -->
<p>Beschrijf het voordeel.</p>
<!-- /wp:paragraph -->
<!-- /wp:chargenet/rich-text-image -->

<!-- wp:chargenet/rich-text-image {"sectionBackground":"paper","imagePosition":"left","heading":"Tweede voordeel"} -->
<!-- wp:paragraph -->
<p>Beschrijf het voordeel.</p>
<!-- /wp:paragraph -->
<!-- /wp:chargenet/rich-text-image -->

<!-- wp:chargenet/cta-band {"heading":"Neem contact met ons op","anchor":"download"} -->
<!-- wp:chargenet/button {"label":"Neem contact op","url":"#contact"} /-->
<!-- /wp:chargenet/cta-band -->
