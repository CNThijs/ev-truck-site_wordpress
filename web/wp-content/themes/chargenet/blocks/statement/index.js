import { registerBlockType } from '@wordpress/blocks';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import { SectionInspector, sectionProps, withSectionAttributes } from '../_shared/section.js';
import './style.scss';

registerBlockType(metadata, {
	attributes: withSectionAttributes(metadata),
	edit({ attributes, setAttributes }) {
		const { text } = attributes;
		const blockProps = useBlockProps(sectionProps(attributes, 'statement'));

		// Keep this markup in sync with render.php so the editor shows the real theme styles.
		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<section {...blockProps}>
					<div className="container container--wide">
						<RichText
							tagName="p"
							className="statement__text"
							value={text}
							onChange={(value) => setAttributes({ text: value })}
							placeholder={__('One large sentence…', 'chargenet')}
							allowedFormats={['core/bold', 'core/italic']}
						/>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
});
