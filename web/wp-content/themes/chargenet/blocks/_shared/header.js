import { RichText } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

// Eyebrow, heading and introduction above a section's content. Mirrors chargenet_section_header() in PHP.
export function SectionHeaderFields({ attributes, setAttributes }) {
	const { eyebrow, heading, headingLevel, intro } = attributes;
	return (
		<header className="section-intro stack">
			<RichText
				tagName="p"
				className="t-eyebrow"
				value={eyebrow}
				onChange={(value) => setAttributes({ eyebrow: value })}
				placeholder={__('Eyebrow (optional)', 'chargenet')}
				allowedFormats={[]}
			/>
			<RichText
				tagName={`h${headingLevel}`}
				value={heading}
				onChange={(value) => setAttributes({ heading: value })}
				placeholder={__('Section heading', 'chargenet')}
				allowedFormats={['core/bold', 'core/italic']}
			/>
			<RichText
				tagName="p"
				className="t-lead"
				value={intro}
				onChange={(value) => setAttributes({ intro: value })}
				placeholder={__('Introduction (optional)', 'chargenet')}
				allowedFormats={['core/bold', 'core/italic', 'core/link']}
			/>
		</header>
	);
}
