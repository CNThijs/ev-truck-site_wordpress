import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

registerBlockType(metadata, {
	edit({ attributes, setAttributes, context }) {
		const { variant, title } = attributes;
		const level = Math.max(3, Math.min(4, (context['chargenet/headingLevel'] ?? 2) + 2));
		const blockProps = useBlockProps({ className: `icon-list icon-list--${variant}` });

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Icon list', 'chargenet')}>
						<SelectControl
							label={__('Look', 'chargenet')}
							value={variant}
							options={[
								{ value: 'tiles', label: __('Tiles (icon, title, text)', 'chargenet') },
								{ value: 'chips', label: __('Chips (icon and short label)', 'chargenet') },
								{
									value: 'journey',
									label: __('Journey (numbered steps with a line)', 'chargenet'),
								},
							]}
							onChange={(value) => setAttributes({ variant: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<RichText
						tagName={`h${level}`}
						className="icon-list__title"
						value={title}
						onChange={(value) => setAttributes({ title: value })}
						placeholder={__('Heading (optional)', 'chargenet')}
						allowedFormats={[]}
					/>
					<div className="icon-list__items">
						<InnerBlocks
							allowedBlocks={['chargenet/icon-list-item']}
							template={[['chargenet/icon-list-item'], ['chargenet/icon-list-item']]}
							orientation="horizontal"
						/>
					</div>
				</div>
			</>
		);
	},
	save: () => <InnerBlocks.Content />,
});
