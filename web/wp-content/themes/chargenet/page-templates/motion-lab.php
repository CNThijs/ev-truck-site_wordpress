<?php
/**
 * Motion Lab: every animation preset with sample content. Admin only (see inc/motion-lab.php).
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_img = chargenet_gallery_samples();
$chargenet_b   = 'chargenet_gallery_block';

$chargenet_item = static fn( string $icon, string $title, string $text ): string => chargenet_gallery_block(
	'chargenet/feature-grid-item',
	array(
		'icon'  => $icon,
		'title' => $title,
		'text'  => $text,
	)
);
$chargenet_six  = $chargenet_item( 'euro', 'Competitive pricing', 'Charge at the right price.' )
	. $chargenet_item( 'layers', 'One platform', 'Everything in one place.' )
	. $chargenet_item( 'sliders', 'Control', 'You decide who charges.' )
	. $chargenet_item( 'smartphone', 'Easy to use', 'Find, start and pay.' )
	. $chargenet_item( 'lock', 'Private access', 'Only for your network.' )
	. $chargenet_item( 'shield-check', 'Secure', 'Built to your standards.' );

$chargenet_step = static fn( string $title, string $text ): string => chargenet_gallery_block( 'chargenet/step-item', array( 'title' => $title ), chargenet_gallery_p( $text ) );
$chargenet_five = $chargenet_step( 'Intake', 'We map your site and grid capacity.' )
	. $chargenet_step( 'Setup', 'We connect your chargers.' )
	. $chargenet_step( 'Go live', 'Approved drivers can charge.' );

$chargenet_stat = static fn( string $value, string $suffix, string $label ): string => chargenet_gallery_block(
	'chargenet/stat-item',
	array(
		'value'  => $value,
		'suffix' => $suffix,
		'label'  => $label,
	)
);

$chargenet_slide      = static fn( string $title, int $key ): string => chargenet_gallery_block(
	'chargenet/slide-card',
	array(
		'title'   => $title,
		'text'    => 'A card in the pinned row.',
		'imageId' => $chargenet_img[ $key ] ?? 0,
		'url'     => '#',
	)
);
$chargenet_six_slides = $chargenet_slide( 'Destination charging', 1 ) . $chargenet_slide( 'ChargeBase', 2 ) . $chargenet_slide( 'IJmond aan zet', 1 )
	. $chargenet_slide( 'Construction power', 2 ) . $chargenet_slide( 'Depot charging', 1 ) . $chargenet_slide( 'Public sites', 2 )
	. $chargenet_slide( 'Retail parks', 1 ) . $chargenet_slide( 'Ports', 2 ) . $chargenet_slide( 'Distribution centres', 1 )
	. $chargenet_slide( 'Construction depots', 2 ) . $chargenet_slide( 'City hubs', 1 ) . $chargenet_slide( 'Highway sites', 2 );

$chargenet_specimens = array(
	array(
		'id'       => 'fade-rise',
		'title'    => 'Fade and rise on enter',
		'preset'   => 'fade-rise',
		'text'     => 'Each [data-reveal] item fades in and rises when it scrolls into view. Defaults come from --duration-slow and --distance-md. Nothing in the first section or first viewport is touched.',
		'variants' => array(
			'Defaults'                       => $chargenet_b(
				'chargenet/feature-grid',
				array(
					'sectionBackground' => 'paper',
					'animation'         => 'fade-rise',
					'heading'           => 'Fade and rise',
				),
				$chargenet_six
			),
			'data-motion-stagger="0.12" data-motion-distance="40"' => '<div data-motion-stagger="0.12" data-motion-distance="40">' . $chargenet_b(
				'chargenet/feature-grid',
				array(
					'animation' => 'fade-rise',
					'heading'   => 'Staggered, further',
				),
				$chargenet_six
			) . '</div>',
			'Text and image, short distance' => '<div data-motion-distance="12" data-motion-duration="0.8">' . $chargenet_b(
				'chargenet/rich-text-image',
				array(
					'sectionBackground' => 'paper',
					'animation'         => 'fade-rise',
					'heading'           => 'Subtle entrance',
					'imageId'           => $chargenet_img[2] ?? 0,
				),
				chargenet_gallery_p( 'Short distance and a slower duration for large blocks.' )
			) . '</div>',
		),
	),
	array(
		'id'       => 'parallax',
		'title'    => 'Parallax background image',
		'preset'   => 'parallax',
		'text'     => 'The section background image drifts slower than the page: scroll-scrubbed transform, screens from 48rem up. A hero at the top of a page starts at rest. Needs data-hero-media (Hero and Statistics).',
		'variants' => array(
			'Hero banner' => $chargenet_b(
				'chargenet/hero',
				array(
					'variant'   => 'banner',
					'animation' => 'parallax',
					'heading'   => 'Parallax hero',
					'intro'     => 'Scroll to see the image move slower than the text.',
					'imageId'   => $chargenet_img[1] ?? 0,
				)
			),
			'Statistics'  => $chargenet_b(
				'chargenet/stats',
				array(
					'animation' => 'parallax',
					'heading'   => 'Parallax behind the numbers',
					'imageId'   => $chargenet_img[2] ?? 0,
				),
				$chargenet_stat( '80', '%', 'of trucks charge at their destination' ) . $chargenet_stat( '18', '%', 'lower charging cost' )
			),
		),
	),
	array(
		'id'       => 'stagger',
		'title'    => 'Staggered reveal',
		'preset'   => 'stagger',
		'text'     => 'Items fade in one after another in the order they enter the viewport (default step 0.08 s, data-motion-stagger).',
		'variants' => array(
			'Cards'                     => $chargenet_b(
				'chargenet/feature-grid',
				array(
					'sectionBackground' => 'paper',
					'animation'         => 'stagger',
					'heading'           => 'Staggered cards',
				),
				$chargenet_six
			),
			'data-motion-stagger="0.2"' => '<div data-motion-stagger="0.2">' . $chargenet_b(
				'chargenet/feature-grid',
				array(
					'animation' => 'stagger',
					'columns'   => 2,
					'heading'   => 'Slower step',
				),
				$chargenet_six
			) . '</div>',
		),
	),
	array(
		'id'       => 'counters',
		'title'    => 'Counting numbers',
		'preset'   => 'counters',
		'text'     => 'Statistics count up from 0 when they enter. The final figure is in the HTML and is announced as such while counting.',
		'variants' => array(
			'Statistics' => $chargenet_b(
				'chargenet/stats',
				array(
					'animation' => 'counters',
					'heading'   => 'Numbers that count',
				),
				$chargenet_stat( '3.8', 'Bn', 'tonne of CO₂ from road freight' ) . $chargenet_stat( '18', '%', 'lower charging cost' ) . $chargenet_stat( '80', '%', 'of trucks charge at destination' ) . $chargenet_stat( '24', '/7', 'access' )
			),
		),
	),
	array(
		'id'       => 'text-lines',
		'title'    => 'Headline line reveal',
		'preset'   => 'text-lines',
		'text'     => 'Headings (data-split) reveal line by line from a clipped edge. The text is real HTML; lines are re-split on resize and when fonts load.',
		'variants' => array(
			'Long headings' => $chargenet_b(
				'chargenet/feature-columns',
				array(
					'sectionBackground' => 'paper',
					'animation'         => 'text-lines',
					'heading'           => 'A long heading that wraps over two or three lines on narrow screens so you can see the line reveal',
				),
				chargenet_gallery_block( 'chargenet/feature-column', array( 'title' => 'Column heading that is rather long as well' ), chargenet_gallery_ul( array( 'One', 'Two' ) ) )
			),
		),
	),
	array(
		'id'       => 'horizontal',
		'title'    => 'Horizontal scroll',
		'preset'   => 'horizontal',
		'text'     => 'On wide screens with a mouse the Card Slider pins and the cards move sideways as you scroll down. On touch, narrow screens and with reduced motion it stays the swipeable row. Tab into a card: the page scrolls to it. The sample has 12 cards: at most 10 are shown, picked at random on each load.',
		'variants' => array(
			'Card Slider' => $chargenet_b(
				'chargenet/card-slider',
				array(
					'sectionBackground' => 'paper',
					'animation'         => 'horizontal',
					'heading'           => 'Pinned row',
				),
				$chargenet_six_slides
			),
		),
	),
	array(
		'id'       => 'draw',
		'title'    => 'Line drawing',
		'preset'   => 'draw',
		'text'     => 'SVG paths marked data-draw (pathLength="1") draw themselves. Vertical timeline: scrubbed with the scroll. Horizontal line: plays once on entering (data-draw-mode="enter").',
		'variants' => array(
			'Steps, vertical (scrub)'      => $chargenet_b(
				'chargenet/steps',
				array(
					'variant'   => 'vertical',
					'animation' => 'draw',
					'heading'   => 'Line follows the scroll',
				),
				$chargenet_five
			),
			'Steps, horizontal (on enter)' => $chargenet_b(
				'chargenet/steps',
				array(
					'sectionBackground' => 'paper',
					'animation'         => 'draw',
					'heading'           => 'Line draws once',
				),
				$chargenet_five
			),
		),
	),
);

get_header();
?>
<section class="section is-dark is-first-section" data-space-bottom="md">
	<div class="container stack">
		<p class="t-eyebrow">Admin only</p>
		<h1 class="t-hero">Motion Lab</h1>
		<p class="t-lead">Every animation preset with sample content. Presets are chosen per section in Section Settings. This first section is never animated, like the first section of a real page. Not public, not indexed.</p>
		<nav aria-label="Presets" class="cluster">
			<?php foreach ( $chargenet_specimens as $chargenet_specimen ) : ?>
				<a class="btn btn--secondary btn--sm" href="#<?php echo esc_attr( $chargenet_specimen['id'] ); ?>"><?php echo esc_html( $chargenet_specimen['title'] ); ?></a>
			<?php endforeach; ?>
		</nav>
	</div>
</section>

<div class="scroll-progress" data-scroll-progress aria-hidden="true" hidden></div>

<div class="lab__bar is-paper" role="region" aria-label="Motion Lab controls">
	<div class="container cluster">
		<label class="lab__switch">
			<input type="checkbox" data-lab-reduce>
			Simulate reduced motion
		</label>
		<button type="button" class="btn btn--primary btn--sm" data-lab-replay>Replay animations</button>
		<p class="t-small lab__status" data-lab-status aria-live="polite"></p>
	</div>
</div>

<div class="lab__spacer is-paper"><div class="container"><p class="t-lead">Scroll down: the sections below start hidden or still and animate as they arrive. Also on this page: the reading-progress bar at the very top and the header that hides when you scroll down and returns when you scroll up.</p></div></div>

<?php foreach ( $chargenet_specimens as $chargenet_specimen ) : ?>
	<div class="gallery__group" id="<?php echo esc_attr( $chargenet_specimen['id'] ); ?>">
		<div class="gallery__label is-paper">
			<div class="container">
				<h2 class="gallery__name"><?php echo esc_html( $chargenet_specimen['title'] ); ?> <code>data-animation="<?php echo esc_html( $chargenet_specimen['preset'] ); ?>"</code></h2>
				<p><?php echo esc_html( $chargenet_specimen['text'] ); ?></p>
			</div>
		</div>
		<?php foreach ( $chargenet_specimen['variants'] as $chargenet_label => $chargenet_markup ) : ?>
			<div class="gallery__variant">
				<p class="gallery__variant-label"><?php echo esc_html( $chargenet_label ); ?></p>
				<?php echo do_blocks( $chargenet_markup ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered blocks. ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>

<div class="lab__spacer is-paper"><div class="container"><p class="t-lead">End of the lab. Scroll back up and press “Replay animations”.</p></div></div>
<?php
wp_print_inline_script_tag(
	<<<'JS'
(function () {
	var root = document.documentElement;
	var reduce = document.querySelector('[data-lab-reduce]');
	var replay = document.querySelector('[data-lab-replay]');
	var status = document.querySelector('[data-lab-status]');
	var os = window.matchMedia('(prefers-reduced-motion: reduce)');
	function show() {
		status.textContent = 'Motion: ' + (root.dataset.motion || 'starting') + ' · OS reduced motion: ' + (os.matches ? 'on' : 'off') + (reduce.checked ? ' · simulated: on' : '');
	}
	function restart() {
		document.dispatchEvent(new CustomEvent('chargenet:motion-restart'));
		setTimeout(show, 150);
	}
	reduce.addEventListener('change', function () {
		if (reduce.checked) { root.dataset.motionSimulate = 'reduce'; } else { delete root.dataset.motionSimulate; }
		restart();
	});
	replay.addEventListener('click', function () {
		window.scrollTo({ top: 0, behavior: 'auto' });
		restart();
	});
	new MutationObserver(show).observe(root, { attributes: true, attributeFilter: ['data-motion', 'data-motion-simulate'] });
	show();
})();
JS
);
get_footer();
