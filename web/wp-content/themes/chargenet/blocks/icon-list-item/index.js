import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import { Icon, IconControl } from '../_shared/icons.js';

registerBlockType(metadata, {
	edit({ attributes, setAttributes, context }) {
		const { icon, title, text } = attributes;
		const variant = context['chargenet/iconListVariant'] ?? 'tiles';
		const level = Math.max(3, Math.min(4, (context['chargenet/headingLevel'] ?? 2) + 2));
		const blockProps = useBlockProps({ className: 'icon-item' });

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Item', 'chargenet')}>
						<IconControl value={icon} onChange={(value) => setAttributes({ icon: value })} />
					</PanelBody>
				</InspectorControls>
				<li {...blockProps}>
					{icon && (
						<span className="icon-item__icon">
							<Icon name={icon} />
						</span>
					)}
					<div className="icon-item__body">
						<RichText
							tagName={variant === 'chips' ? 'span' : `h${level}`}
							className="icon-item__title"
							value={title}
							onChange={(value) => setAttributes({ title: value })}
							placeholder={__('Title', 'chargenet')}
							allowedFormats={[]}
						/>
						<RichText
							tagName="p"
							className="icon-item__text"
							value={text}
							onChange={(value) => setAttributes({ text: value })}
							placeholder={__('Short text (optional)', 'chargenet')}
							allowedFormats={['core/bold', 'core/italic']}
						/>
					</div>
				</li>
			</>
		);
	},
	save: () => null,
});
