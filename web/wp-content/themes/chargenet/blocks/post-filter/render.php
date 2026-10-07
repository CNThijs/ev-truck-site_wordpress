<?php
/**
 * News filter. Variables: $attributes. Category links of the page's language (the current one marked) and a search
 * box that searches the whole site. Plain links and a plain form: no JavaScript.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_categories = ! empty( $attributes['showCategories'] ?? true ) ? get_categories(
	array(
		'hide_empty' => true,
		'orderby'    => 'name',
	)
) : array();
$chargenet_blog       = (int) get_option( 'page_for_posts' ); // Polylang returns the current language's page.
$chargenet_current    = is_category() ? (int) get_queried_object_id() : 0;
$chargenet_input_id   = wp_unique_id( 'post-filter-search-' );

chargenet_section_open(
	$attributes,
	array(
		'name'  => 'post-filter',
		'label' => __( 'Filter the news', 'chargenet' ),
	)
);
?>
<div class="post-filter">
	<?php if ( $chargenet_categories && $chargenet_blog ) : ?>
		<nav aria-label="<?php esc_attr_e( 'News categories', 'chargenet' ); ?>">
			<ul class="post-filter__list" role="list">
				<li><a class="post-filter__link" href="<?php echo esc_url( (string) get_permalink( $chargenet_blog ) ); ?>"<?php echo 0 === $chargenet_current && is_home() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'All news', 'chargenet' ); ?></a></li>
				<?php foreach ( $chargenet_categories as $chargenet_term ) : ?>
					<li><a class="post-filter__link" href="<?php echo esc_url( (string) get_term_link( $chargenet_term ) ); ?>"<?php echo $chargenet_current === (int) $chargenet_term->term_id ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $chargenet_term->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>
	<?php if ( ! empty( $attributes['showSearch'] ?? true ) ) : ?>
		<form class="post-filter__search" role="search" method="get" action="<?php echo esc_url( function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' ) ); ?>">
			<label class="visually-hidden" for="<?php echo esc_attr( $chargenet_input_id ); ?>"><?php esc_html_e( 'Search the site', 'chargenet' ); ?></label>
			<input class="input" type="search" id="<?php echo esc_attr( $chargenet_input_id ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search the site', 'chargenet' ); ?>">
			<button type="submit" class="btn btn--secondary"><?php esc_html_e( 'Search', 'chargenet' ); ?></button>
		</form>
	<?php endif; ?>
</div>
<?php
chargenet_section_close();
