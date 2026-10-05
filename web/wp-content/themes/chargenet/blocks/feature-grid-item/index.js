import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import { Icon, IconControl } from '../_shared/icons.js';

registerBlockType(metadata, {
	edit({ attributes, setAttributes, context }) {
		const { icon, title, text, url, linkLabel } = attributes;
		const level = Math.max(3, Math.min(4, (context['chargenet/headingLevel'] ?? 2) + 1));
		const cards = (context['chargenet/gridVariant'] ?? 'cards') === 'cards';
		const blockProps = useBlockProps({
			className: `feature-grid__item${cards ? ' card' : ''}${url ? ' has-link' : ''}`,
		});

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Item', 'chargenet')}>
						<IconControl value={icon} onChange={(value) => setAttributes({ icon: value })} />
						<TextControl
							label={__('Link URL (optional)', 'chargenet')}
							value={url}
							type="url"
							onChange={(value) => setAttributes({ url: value })}
						/>
						<TextControl
							label={__('Link label (optional)', 'chargenet')}
							help={__(
								'Empty: the title is the link. Filled: a separate "learn more" link below the text.',
								'chargenet',
							)}
							value={linkLabel}
							onChange={(value) => setAttributes({ linkLabel: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					{icon && (
						<span className="feature-grid__icon">
							<Icon name={icon} />
						</span>
					)}
					<RichText
						tagName={`h${level}`}
						className="feature-grid__title"
						value={title}
						onChange={(value) => setAttributes({ title: value })}
						placeholder={__('Title', 'chargenet')}
						allowedFormats={[]}
					/>
					<RichText
						tagName="p"
						className="feature-grid__text"
						value={text}
						onChange={(value) => setAttributes({ text: value })}
						placeholder={__('Short text', 'chargenet')}
						allowedFormats={['core/bold', 'core/italic']}
					/>
				</div>
			</>
		);
	},
	save: () => null,
});
