import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

registerBlockType(metadata, {
	edit({ attributes, setAttributes }) {
		const { question } = attributes;
		const blockProps = useBlockProps({ className: 'accordion-item' });

		return (
			<div {...blockProps}>
				<RichText
					tagName="p"
					className="accordion-item__summary"
					value={question}
					onChange={(value) => setAttributes({ question: value })}
					placeholder={__('Question', 'chargenet')}
					allowedFormats={[]}
				/>
				<div className="accordion-item__panel">
					<InnerBlocks
						allowedBlocks={['core/paragraph', 'core/list']}
						template={[['core/paragraph', { placeholder: __('Answer…', 'chargenet') }]]}
					/>
				</div>
			</div>
		);
	},
	save: () => <InnerBlocks.Content />,
});
