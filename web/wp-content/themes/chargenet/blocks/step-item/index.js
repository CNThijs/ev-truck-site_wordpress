import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

registerBlockType(metadata, {
	edit({ attributes, setAttributes, context }) {
		const { title } = attributes;
		const level = Math.max(3, Math.min(4, (context['chargenet/headingLevel'] ?? 2) + 1));
		const blockProps = useBlockProps({ className: 'step' });

		return (
			<div {...blockProps}>
				<span className="step__number" aria-hidden="true" />
				<div className="step__body">
					<RichText
						tagName={`h${level}`}
						className="step__title"
						value={title}
						onChange={(value) => setAttributes({ title: value })}
						placeholder={__('Step title', 'chargenet')}
						allowedFormats={[]}
					/>
					<div className="step__content">
						<InnerBlocks
							allowedBlocks={['core/paragraph', 'core/list']}
							template={[
								['core/paragraph', { placeholder: __('Describe the step…', 'chargenet') }],
							]}
						/>
					</div>
				</div>
			</div>
		);
	},
	save: () => <InnerBlocks.Content />,
});
