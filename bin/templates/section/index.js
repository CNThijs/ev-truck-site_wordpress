import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import { HeadingLevelControl, SectionInspector, sectionProps, withSectionAttributes } from '../_shared/section.js';
import './style.scss';
import './editor.scss';

registerBlockType(metadata, {
	attributes: withSectionAttributes(metadata),
	edit({ attributes, setAttributes }) {
		const { heading, headingLevel } = attributes;
		const blockProps = useBlockProps(sectionProps(attributes, '{{slug}}'));

		// Keep this markup in sync with render.php so the editor shows the real theme styles.
		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<section {...blockProps}>
					<div className="container">
						<div className="{{slug}} stack">
							<RichText
								tagName={`h${headingLevel}`}
								value={heading}
								onChange={(value) => setAttributes({ heading: value })}
								placeholder={__('Section heading', 'chargenet')}
								allowedFormats={['core/bold', 'core/italic']}
							/>
							<InnerBlocks
								allowedBlocks={['core/paragraph', 'core/list']}
								template={[['core/paragraph', { placeholder: __('Write the text…', 'chargenet') }]]}
							/>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => <InnerBlocks.Content />,
});
