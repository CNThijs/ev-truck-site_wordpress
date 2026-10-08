<?php
/**
 * Breadcrumb trail under the header (Rank Math builds it and prints the BreadcrumbList data from the same trail).
 * Not on the front page, the 404 page or the admin-only tool pages.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'rank_math_the_breadcrumbs' ) || is_front_page() || is_404() || is_page_template( array( 'page-templates/style-guide.php' ) ) ) {
	return;
}
?>
<div class="breadcrumbs is-dark">
	<div class="container">
		<?php rank_math_the_breadcrumbs(); ?>
	</div>
</div>
