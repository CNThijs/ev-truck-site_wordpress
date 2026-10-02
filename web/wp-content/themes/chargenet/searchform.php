<?php
/**
 * Search form.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'chargenet' ); ?></span>
		<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>">
	</label>
	<button type="submit"><?php esc_html_e( 'Search', 'chargenet' ); ?></button>
</form>
