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
		const { heading, headingLevel, text } = attributes;
		const blockProps = useBlockProps(sectionProps(attributes, 'cta-band'));

		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<section {...blockProps}>
					<div className="container">
						<div className="cta-band stack">
							<RichText
								tagName={`h${headingLevel}`}
								value={heading}
								onChange={(value) => setAttributes({ heading: value })}
								placeholder={__('Call to action heading', 'chargenet')}
								allowedFormats={['core/bold', 'core/italic']}
							/>
							<RichText
								tagName="p"
								className="t-lead"
								value={text}
								onChange={(value) => setAttributes({ text: value })}
								placeholder={__('Short supporting text (optional)', 'chargenet')}
								allowedFormats={['core/bold', 'core/italic', 'core/link']}
							/>
							<div className="cluster cta-band__actions">
								<InnerBlocks
									allowedBlocks={['chargenet/button']}
									template={[['chargenet/button', { label: 'Contact us', url: '#contact' }]]}
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
