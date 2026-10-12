import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import { Icon, IconControl } from '../_shared/icons.js';
import { ImagePanel } from '../_shared/media.js';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

registerBlockType(metadata, {
	edit({ attributes, setAttributes, context }) {
		const { title, tone, icon, imageUrl, url, linkLabel } = attributes;
		const level = Math.max(3, Math.min(4, (context['chargenet/headingLevel'] ?? 2) + 1));
		const blockProps = useBlockProps({ className: `bento-item bento-item--${tone}` });

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Card', 'chargenet')}>
						<SelectControl
							label={__('Look', 'chargenet')}
							value={tone}
							options={[
								{ value: 'light', label: __('Light', 'chargenet') },
								{ value: 'dark', label: __('Dark green', 'chargenet') },
								{ value: 'accent', label: __('Yellow accent', 'chargenet') },
								{ value: 'photo', label: __('Photo background', 'chargenet') },
							]}
							onChange={(value) => setAttributes({ tone: value })}
						/>
						<IconControl value={icon} onChange={(value) => setAttributes({ icon: value })} />
					</PanelBody>
					<PanelBody title={__('Card link', 'chargenet')} initialOpen={false}>
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
				{tone === 'photo' && (
					<ImagePanel
						title={__('Photo', 'chargenet')}
						id={attributes.imageId}
						url={imageUrl}
						onSelect={({ id, url: src }) => setAttributes({ imageId: id, imageUrl: src })}
						onRemove={() => setAttributes({ imageId: 0, imageUrl: '' })}
					/>
				)}
				<article {...blockProps}>
					{tone === 'photo' && imageUrl && (
						<div className="bento-item__media">
							<img className="bento-item__img" src={imageUrl} alt="" />
						</div>
					)}
					<div className="bento-item__inner">
						{icon && (
							<span className="bento-item__icon">
								<Icon name={icon} />
							</span>
						)}
						<RichText
							tagName={`h${level}`}
							className="bento-item__title"
							value={title}
							onChange={(value) => setAttributes({ title: value })}
							placeholder={__('Column title', 'chargenet')}
							allowedFormats={[]}
						/>
						<div className="bento-item__body">
							<InnerBlocks
								allowedBlocks={[
									'core/heading',
									'core/list',
									'core/paragraph',
									'chargenet/icon-list',
								]}
								template={[['core/paragraph', { placeholder: __('Short text', 'chargenet') }]]}
							/>
						</div>
						{url && linkLabel && (
							<span className="link-arrow bento-item__link" aria-hidden="true">
								{linkLabel}
							</span>
						)}
					</div>
				</article>
			</>
		);
	},
	save: () => <InnerBlocks.Content />,
});
