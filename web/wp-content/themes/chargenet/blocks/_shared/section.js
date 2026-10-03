import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import sectionAttributes from '../../inc/section-attributes.json';

// Shared section settings. Definitions live in inc/section-attributes.json (also read by PHP).
// Add the same keys to inc/section.php when you add a setting here.
export { sectionAttributes };

const backgrounds = [
	{ value: 'light', label: __('Light (white)', 'chargenet') },
	{ value: 'paper', label: __('Paper (warm off-white)', 'chargenet') },
	{ value: 'dark', label: __('Dark (forest green)', 'chargenet') },
];

const spacing = [
	{ value: 'none', label: __('None', 'chargenet') },
	{ value: 'sm', label: __('Small', 'chargenet') },
	{ value: 'md', label: __('Medium', 'chargenet') },
	{ value: 'lg', label: __('Large (default)', 'chargenet') },
];

const hideOptions = [
	{ value: 'none', label: __('Show everywhere', 'chargenet') },
	{ value: 'mobile', label: __('Hide on mobile', 'chargenet') },
	{ value: 'desktop', label: __('Hide on desktop', 'chargenet') },
];

// Provisional presets; the real set comes with the animation epic. The value is stored and output as data-animation.
const animations = [
	{ value: '', label: __('None', 'chargenet') },
	{ value: 'fade-up', label: 'fade-up' },
	{ value: 'fade-in', label: 'fade-in' },
	{ value: 'slide-left', label: 'slide-left' },
	{ value: 'slide-right', label: 'slide-right' },
];

// Attributes for registerBlockType: shared ones first so a block's own definition wins.
export const withSectionAttributes = (metadata) => ({
	...sectionAttributes,
	...metadata.attributes,
});

// Props for the section element in the editor (mirrors chargenet_section_open() in PHP).
export const sectionProps = (attributes, name) => ({
	className: `section is-${attributes.sectionBackground} section--${name}`,
	'data-space-top': attributes.spaceTop,
	'data-space-bottom': attributes.spaceBottom,
	...(attributes.animation ? { 'data-animation': attributes.animation } : {}),
});

export function SectionInspector({ attributes, setAttributes }) {
	return (
		<InspectorControls>
			<PanelBody title={__('Section settings', 'chargenet')} initialOpen={false}>
				<SelectControl
					label={__('Background', 'chargenet')}
					value={attributes.sectionBackground}
					options={backgrounds}
					onChange={(sectionBackground) => setAttributes({ sectionBackground })}
				/>
				<SelectControl
					label={__('Space above', 'chargenet')}
					value={attributes.spaceTop}
					options={spacing}
					onChange={(spaceTop) => setAttributes({ spaceTop })}
				/>
				<SelectControl
					label={__('Space below', 'chargenet')}
					value={attributes.spaceBottom}
					options={spacing}
					onChange={(spaceBottom) => setAttributes({ spaceBottom })}
				/>
				<SelectControl
					label={__('Visibility', 'chargenet')}
					value={attributes.hideOn}
					options={hideOptions}
					onChange={(hideOn) => setAttributes({ hideOn })}
				/>
				<SelectControl
					label={__('Animation', 'chargenet')}
					value={attributes.animation}
					options={animations}
					onChange={(animation) => setAttributes({ animation })}
				/>
			</PanelBody>
		</InspectorControls>
	);
}

// Shared heading-level picker for sections with a heading (h2 to h4; h1 is the page title).
export function HeadingLevelControl({ attributes, setAttributes }) {
	return (
		<InspectorControls>
			<PanelBody title={__('Heading', 'chargenet')} initialOpen={false}>
				<SelectControl
					label={__('Heading level', 'chargenet')}
					value={String(attributes.headingLevel)}
					options={[2, 3, 4].map((n) => ({ value: String(n), label: `H${n}` }))}
					onChange={(value) => setAttributes({ headingLevel: Number(value) })}
				/>
			</PanelBody>
		</InspectorControls>
	);
}
