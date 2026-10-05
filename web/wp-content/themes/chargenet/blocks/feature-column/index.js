import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

registerBlockType(metadata, {
	edit({ attributes, setAttributes, context }) {
		const { title, url, linkLabel } = attributes;
		const level = Math.max(3, Math.min(4, (context['chargenet/headingLevel'] ?? 2) + 1));
		const blockProps = useBlockProps({ className: 'feature-column' });

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Column link', 'chargenet')}>
						<TextControl
							label={__('Link URL', 'chargenet')}
							value={url}
							type="url"
							onChange={(value) => setAttributes({ url: value })}
						/>
						<TextControl
							label={__('Link label', 'chargenet')}
							value={linkLabel}
							onChange={(value) => setAttributes({ linkLabel: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<RichText
						tagName={`h${level}`}
						className="feature-column__title"
						value={title}
						onChange={(value) => setAttributes({ title: value })}
						placeholder={__('Column title', 'chargenet')}
						allowedFormats={[]}
					/>
					<div className="feature-column__body">
						<InnerBlocks
							allowedBlocks={['core/heading', 'core/list', 'core/paragraph']}
							template={[
								['core/heading', { level: 4, placeholder: __('Sub-heading', 'chargenet') }],
								['core/list'],
							]}
						/>
					</div>
					{url && linkLabel && <span className="link-arrow">{linkLabel}</span>}
				</div>
			</>
		);
	},
	save: () => <InnerBlocks.Content />,
});
