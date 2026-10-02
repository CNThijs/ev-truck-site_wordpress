<?php
/**
 * Empty state for lists and search.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="no-results">
	<h2><?php esc_html_e( 'Nothing found', 'chargenet' ); ?></h2>
	<?php get_search_form(); ?>
</section>
