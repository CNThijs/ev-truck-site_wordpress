import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import {
	HeadingLevelControl,
	SectionInspector,
	sectionProps,
	withSectionAttributes,
} from '../_shared/section.js';
import './style.scss';
import './editor.scss';

registerBlockType(metadata, {
	attributes: withSectionAttributes(metadata),
	edit({ attributes, setAttributes }) {
		const { eyebrow, heading, headingLevel, intro } = attributes;
		const blockProps = useBlockProps(sectionProps(attributes, 'feature-columns'));

		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<section {...blockProps}>
					<div className="container">
						<div className="feature-columns stack">
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
							<div className="feature-columns__list">
								<InnerBlocks
									allowedBlocks={['chargenet/feature-column']}
									template={[['chargenet/feature-column'], ['chargenet/feature-column']]}
									orientation="horizontal"
								/>
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => <InnerBlocks.Content />,
});
