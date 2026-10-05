import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

registerBlockType(metadata, {
	edit({ attributes, setAttributes }) {
		const { value, prefix, suffix, label } = attributes;
		const blockProps = useBlockProps({ className: 'stat' });

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Number', 'chargenet')}>
						<TextControl
							label={__('Number', 'chargenet')}
							help={__('Plain number such as 80 or 3.8, so it can count up.', 'chargenet')}
							value={value}
							onChange={(next) => setAttributes({ value: next })}
						/>
						<TextControl
							label={__('Before the number', 'chargenet')}
							value={prefix}
							onChange={(next) => setAttributes({ prefix: next })}
						/>
						<TextControl
							label={__('After the number', 'chargenet')}
							help={__('For example % or Bn.', 'chargenet')}
							value={suffix}
							onChange={(next) => setAttributes({ suffix: next })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<span className="stat__value">
						{prefix}
						{value || '0'}
						{suffix}
					</span>
					<RichText
						tagName="span"
						className="stat__label"
						value={label}
						onChange={(next) => setAttributes({ label: next })}
						placeholder={__('Label', 'chargenet')}
						allowedFormats={[]}
					/>
				</div>
			</>
		);
	},
	save: () => null,
});
