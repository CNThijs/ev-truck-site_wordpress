<?php
/**
 * Style guide: renders every design token and component. Admin only (see inc/style-guide.php).
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Section heading helper.
 *
 * @param string $id    Anchor id.
 * @param string $title Heading text.
 */
function chargenet_sg_heading( string $id, string $title ): void {
	printf( '<h2 id="%s">%s</h2>', esc_attr( $id ), esc_html( $title ) );
}

/**
 * Render the style guide body.
 */
function chargenet_render_style_guide(): void {
	$tokens = wp_json_file_decode( CHARGENET_DIR . '/tokens.json', array( 'associative' => true ) );
	?>
<section class="section is-dark">
	<div class="container stack">
		<p class="t-eyebrow">Admin only</p>
		<h1 class="t-hero">Style guide</h1>
		<p class="t-lead">Every token and component of the ChargeNet theme. Source of truth: <code>tokens.json</code>. Not public, not indexed.</p>
		<nav aria-label="Style guide sections" class="cluster">
			<?php foreach ( array( 'colours', 'contrast', 'variants', 'type', 'buttons', 'forms', 'badges', 'cards', 'layout', 'spacing', 'shape', 'motion', 'icons', 'focus' ) as $sg_id ) : ?>
				<a class="btn btn--secondary btn--sm" href="#<?php echo esc_attr( $sg_id ); ?>"><?php echo esc_html( ucfirst( $sg_id ) ); ?></a>
			<?php endforeach; ?>
		</nav>
	</div>
</section>

<div class="section sg">
	<div class="container stack" style="--stack-gap: var(--space-9)">

		<section class="stack" aria-labelledby="colours">
			<?php chargenet_sg_heading( 'colours', 'Colours' ); ?>
			<div class="sg__swatches">
				<?php foreach ( $tokens['colors'] as $name => $hex ) : ?>
					<div class="sg__swatch">
						<div class="sg__chip" style="background: <?php echo esc_attr( $hex ); ?>"></div>
						<div class="sg__swatch-body">
							<strong><?php echo esc_html( $name ); ?></strong><br>
							<code><?php echo esc_html( $hex ); ?></code><br>
							<code>--color-<?php echo esc_html( $name ); ?></code>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="stack" aria-labelledby="contrast">
			<?php chargenet_sg_heading( 'contrast', 'Contrast (WCAG 2.2 AA)' ); ?>
			<p>Pairs used in the design. Text needs 4.5:1, non-text UI needs 3:1. <code>npm run tokens</code> fails the build if a pair drops below its target.</p>
			<table class="sg__table">
				<thead><tr><th scope="col">Use</th><th scope="col">Sample</th><th scope="col">Ratio</th><th scope="col">Target</th></tr></thead>
				<tbody>
				<?php
				foreach ( $tokens['contrast'] as $pair ) :
					list( $fg, $bg, $min, $use ) = $pair;
					$ratio                       = chargenet_contrast_ratio( $tokens['colors'][ $fg ], $tokens['colors'][ $bg ] );
					?>
					<tr>
						<td><?php echo esc_html( $use ); ?> <code><?php echo esc_html( "$fg / $bg" ); ?></code></td>
						<td><span style="display:inline-block;padding:.25rem .75rem;border-radius:.25rem;background:<?php echo esc_attr( $tokens['colors'][ $bg ] ); ?>;color:<?php echo esc_attr( $tokens['colors'][ $fg ] ); ?>;border:1px solid #D1D5DB">Aa Charge</span></td>
						<td><?php echo esc_html( number_format( $ratio, 2 ) ); ?>:1 <?php echo $ratio >= $min ? '✓ pass' : '✗ FAIL'; ?></td>
						<td><?php echo esc_html( $min ); ?>:1</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</section>

		<section class="stack" aria-labelledby="variants">
			<?php chargenet_sg_heading( 'variants', 'Section variants' ); ?>
			<p>Add <code>is-light</code> (default), <code>is-paper</code> or <code>is-dark</code> to a section. Components read the semantic variables from it.</p>
			<?php
			foreach ( array(
				'is-light' => 'Light',
				'is-paper' => 'Paper',
				'is-dark'  => 'Dark',
			) as $class => $label ) :
				?>
				<div class="section <?php echo esc_attr( $class ); ?>" style="border-radius: var(--radius-lg); border: 1px solid var(--color-ink-300)">
					<div class="stack">
						<span class="badge"><?php echo esc_html( $label ); ?></span>
						<h3>Charge at the right price, place and time</h3>
						<p>Body copy with a <a href="#variants">text link</a>. <span style="color: var(--fg-muted)">Muted copy for secondary information.</span></p>
						<div class="cluster">
							<a class="btn btn--primary" href="#variants">Primary</a>
							<a class="btn btn--secondary" href="#variants">Secondary</a>
							<a class="btn btn--ghost" href="#variants">Ghost</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</section>

		<section class="stack" aria-labelledby="type">
			<?php chargenet_sg_heading( 'type', 'Typography' ); ?>
			<p>Source Sans 3, variable, self-hosted. Fluid scale from <?php echo esc_html( $tokens['type']['minViewport'] ); ?>px to <?php echo esc_html( $tokens['type']['maxViewport'] ); ?>px viewports.</p>
			<?php foreach ( array_reverse( array_keys( $tokens['type']['steps'] ) ) as $step ) : ?>
				<div class="sg__demo">
					<code>--text-<?php echo esc_html( $step ); ?></code>
					<p style="font-size: var(--text-<?php echo esc_attr( $step ); ?>); max-width: none; font-weight: var(--weight-bold); line-height: var(--leading-snug)">Keep charging ahead</p>
				</div>
			<?php endforeach; ?>
			<div class="sg__demo stack">
				<p class="t-eyebrow">Eyebrow label, 600 uppercase</p>
				<p class="t-hero">Hero 700</p>
				<p class="t-lead">Lead paragraph in muted colour, used under headings.</p>
				<p>Body 400. Charging infrastructure at destination, shared between shippers and carriers. <strong>Bold 700 for emphasis.</strong> <a href="#type">A link.</a></p>
				<p class="t-small">Small 14–15px for captions and legal copy.</p>
				<p class="t-stat">80%</p>
				<p class="t-small">Stat number, weight 300.</p>
				<p><a class="link-arrow" href="#type">Arrow link</a></p>
				<h1>Heading 1</h1><h2>Heading 2</h2><h3>Heading 3</h3><h4>Heading 4</h4><h5>Heading 5</h5><h6>Heading 6</h6>
			</div>
			<table class="sg__table">
				<thead><tr><th scope="col">Weight</th><th scope="col">Token</th><th scope="col">Sample</th></tr></thead>
				<tbody>
				<?php foreach ( $tokens['weight'] as $name => $weight ) : ?>
					<tr><td><?php echo esc_html( $weight ); ?></td><td><code>--weight-<?php echo esc_html( $name ); ?></code></td><td style="font-weight: <?php echo esc_attr( $weight ); ?>">ChargeNet 0123456789 ĳë€</td></tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</section>

		<section class="stack" aria-labelledby="buttons">
			<?php chargenet_sg_heading( 'buttons', 'Buttons and links' ); ?>
			<?php foreach ( array( 'is-light', 'is-dark' ) as $class ) : ?>
				<div class="section <?php echo esc_attr( $class ); ?>" style="border-radius: var(--radius-lg); border: 1px solid var(--color-ink-300)">
					<div class="cluster">
						<a class="btn btn--primary" href="#buttons">Primary</a>
						<a class="btn btn--secondary" href="#buttons">Secondary</a>
						<a class="btn btn--ghost" href="#buttons">Ghost</a>
						<a class="btn btn--primary btn--sm" href="#buttons">Small</a>
						<button class="btn btn--primary" type="button" disabled>Disabled</button>
						<a class="link-arrow" href="#buttons">Arrow link</a>
					</div>
				</div>
			<?php endforeach; ?>
		</section>

		<section class="stack" aria-labelledby="forms">
			<?php chargenet_sg_heading( 'forms', 'Form fields' ); ?>
			<form class="grid grid--2" onsubmit="return false">
				<div class="field">
					<label class="field__label is-required" for="sg-name">Name</label>
					<input class="input" id="sg-name" type="text" autocomplete="name" required aria-describedby="sg-name-hint">
					<span class="field__hint" id="sg-name-hint">Your full name.</span>
				</div>
				<div class="field">
					<label class="field__label" for="sg-email">Email (error state)</label>
					<input class="input" id="sg-email" type="email" value="not-an-email" aria-invalid="true" aria-describedby="sg-email-error">
					<span class="field__error" id="sg-email-error">Enter an email address like name@company.com.</span>
				</div>
				<div class="field">
					<label class="field__label" for="sg-type">Select</label>
					<select class="select" id="sg-type"><option>Location owner</option><option>Carrier</option></select>
				</div>
				<div class="field">
					<label class="field__label" for="sg-disabled">Disabled</label>
					<input class="input" id="sg-disabled" type="text" value="Disabled" disabled>
				</div>
				<div class="field" style="grid-column: 1 / -1">
					<label class="field__label" for="sg-message">Message</label>
					<textarea class="textarea" id="sg-message"></textarea>
				</div>
				<fieldset class="field" style="border:0;padding:0;margin:0">
					<legend class="field__label">Choice</legend>
					<label class="choice"><input type="checkbox"> I agree to the privacy policy</label>
					<label class="choice"><input type="radio" name="sg-radio" checked> Option A</label>
					<label class="choice"><input type="radio" name="sg-radio"> Option B</label>
				</fieldset>
				<div><button class="btn btn--primary" type="submit">Send message</button></div>
			</form>
		</section>

		<section class="stack" aria-labelledby="badges">
			<?php chargenet_sg_heading( 'badges', 'Badges' ); ?>
			<div class="cluster">
				<span class="badge">Locations</span>
				<span class="badge badge--outline">Carriers</span>
			</div>
			<div class="cluster is-dark" style="padding: var(--space-5); border-radius: var(--radius-lg); background: var(--bg)">
				<span class="badge">Locations</span>
				<span class="badge badge--outline">Carriers</span>
			</div>
		</section>

		<section class="stack" aria-labelledby="cards">
			<?php chargenet_sg_heading( 'cards', 'Cards' ); ?>
			<div class="grid grid--3">
				<article class="card">
					<span class="badge">News</span>
					<h3 class="card__title">Static card</h3>
					<p class="card__meta">1 Sept 2026</p>
					<p>A card with a border and a soft shadow.</p>
				</article>
				<article class="card card--link">
					<span class="badge">News</span>
					<h3 class="card__title"><a class="card__link" href="#cards">Linked card, one tab stop</a></h3>
					<p class="card__meta">1 Sept 2026</p>
					<p>The whole card is clickable. Hover and keyboard focus lift it.</p>
				</article>
				<article class="card is-dark" style="background: var(--surface)">
					<span class="badge">Dark</span>
					<h3 class="card__title">Card on dark</h3>
					<p>Uses the dark surface token.</p>
				</article>
			</div>
		</section>

		<section class="stack" aria-labelledby="layout">
			<?php chargenet_sg_heading( 'layout', 'Container, grid, stack' ); ?>
			<table class="sg__table">
				<thead><tr><th scope="col">Token</th><th scope="col">Value</th></tr></thead>
				<tbody>
				<?php foreach ( $tokens['container'] as $name => $value ) : ?>
					<tr><td><code>--container-<?php echo esc_html( $name ); ?></code></td><td><?php echo esc_html( $value ); ?></td></tr>
				<?php endforeach; ?>
				<?php foreach ( $tokens['breakpoints'] as $name => $value ) : ?>
					<tr><td>Breakpoint <code><?php echo esc_html( $name ); ?></code></td><td><?php echo esc_html( $value ); ?></td></tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php foreach ( array( 2, 3, 4 ) as $cols ) : ?>
				<div class="grid grid--<?php echo esc_attr( $cols ); ?>">
					<?php for ( $i = 1; $i <= $cols; $i++ ) : ?>
						<div class="sg__demo sg__box"><code>.grid--<?php echo esc_html( $cols ); ?></code></div>
					<?php endfor; ?>
				</div>
			<?php endforeach; ?>
		</section>

		<section class="stack" aria-labelledby="spacing">
			<?php chargenet_sg_heading( 'spacing', 'Spacing' ); ?>
			<table class="sg__table">
				<thead><tr><th scope="col">Token</th><th scope="col">Value</th><th scope="col">Sample</th></tr></thead>
				<tbody>
				<?php foreach ( $tokens['spacing'] as $name => $value ) : ?>
					<tr><td><code>--space-<?php echo esc_html( $name ); ?></code></td><td><?php echo esc_html( $value ); ?></td><td><div class="sg__bar" style="width: var(--space-<?php echo esc_attr( $name ); ?>)"></div></td></tr>
				<?php endforeach; ?>
				<?php foreach ( $tokens['fluidSpacing'] as $name => $range ) : ?>
					<tr><td><code>--space-<?php echo esc_html( $name ); ?></code></td><td>fluid <?php echo esc_html( $range[0] . 'rem to ' . $range[1] . 'rem' ); ?></td><td><div class="sg__bar" style="width: var(--space-<?php echo esc_attr( $name ); ?>)"></div></td></tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</section>

		<section class="stack" aria-labelledby="shape">
			<?php chargenet_sg_heading( 'shape', 'Radius and shadow' ); ?>
			<div class="grid">
				<?php foreach ( $tokens['radius'] as $name => $value ) : ?>
					<div class="sg__demo sg__box" style="border-radius: var(--radius-<?php echo esc_attr( $name ); ?>)"><code>--radius-<?php echo esc_html( $name ); ?></code> <?php echo esc_html( $value ); ?></div>
				<?php endforeach; ?>
			</div>
			<div class="grid">
				<?php foreach ( $tokens['shadow'] as $name => $value ) : ?>
					<div class="sg__box" style="border-radius: var(--radius-md); box-shadow: var(--shadow-<?php echo esc_attr( $name ); ?>)"><code>--shadow-<?php echo esc_html( $name ); ?></code></div>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="stack" aria-labelledby="motion">
			<?php chargenet_sg_heading( 'motion', 'Motion' ); ?>
			<p>Hover or focus a row to run the transition. All durations collapse to ~0 when the visitor prefers reduced motion.</p>
			<?php foreach ( $tokens['motion']['duration'] as $name => $value ) : ?>
				<div class="sg__motion-row cluster" tabindex="0" data-reveal style="--sg-duration: var(--duration-<?php echo esc_attr( $name ); ?>)">
					<div class="sg__motion"></div><code>--duration-<?php echo esc_html( $name ); ?></code> <?php echo esc_html( $value ); ?>
				</div>
			<?php endforeach; ?>
			<?php foreach ( $tokens['motion']['ease'] as $name => $value ) : ?>
				<div class="sg__motion-row cluster" tabindex="0" style="--sg-duration: var(--duration-slow); --sg-ease: var(--ease-<?php echo esc_attr( $name ); ?>)">
					<div class="sg__motion"></div><code>--ease-<?php echo esc_html( $name ); ?></code> <?php echo esc_html( $value ); ?>
				</div>
			<?php endforeach; ?>
		</section>

		<section class="stack" aria-labelledby="icons">
			<?php chargenet_sg_heading( 'icons', 'Icons' ); ?>
			<p>Built-in outline icons (<code>chargenet_the_icon( 'name' )</code>, set in <code>inc/icons.json</code>). They take the current text colour and are decorative: always pair them with visible text.</p>
			<ul class="sg__icons" role="list">
				<?php foreach ( array_keys( chargenet_icons() ) as $sg_icon ) : ?>
					<li class="sg__icon">
						<?php chargenet_the_icon( $sg_icon ); ?>
						<code><?php echo esc_html( $sg_icon ); ?></code>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>

		<section class="stack" aria-labelledby="focus">
			<?php chargenet_sg_heading( 'focus', 'Focus states and helpers' ); ?>
			<p>Tab through these. Every interactive element shows a 3px ring in <code>--focus</code> (green on light, yellow on dark).</p>
			<div class="cluster">
				<a class="btn btn--primary" href="#focus">Focus me</a>
				<a href="#focus">A link</a>
				<input class="input" type="text" aria-label="Focus demo field" style="max-width: 14rem">
			</div>
			<p>Helper: <code>.visually-hidden</code> hides text visually but keeps it for screen readers: <span class="visually-hidden">(you cannot see this)</span> there is hidden text between the colon and the full stop.</p>
		</section>
	</div>
</div>
	<?php
}

get_header();
chargenet_render_style_guide();
get_footer();
