import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import { ImagePanel } from '../_shared/media.js';

registerBlockType(metadata, {
	edit({ attributes, setAttributes, context }) {
		const { imageId, imageUrl, imageAlt, title, text, url, linkLabel } = attributes;
		const level = Math.max(3, Math.min(4, (context['chargenet/headingLevel'] ?? 2) + 1));
		const isBg = (context['chargenet/sliderVariant'] ?? 'image-bg') === 'image-bg';
		const blockProps = useBlockProps({ className: `slide${isBg ? ' is-dark' : ''}` });

		return (
			<>
				<ImagePanel
					title={__('Image', 'chargenet')}
					id={imageId}
					url={imageUrl}
					onSelect={({ id, url: src }) => setAttributes({ imageId: id, imageUrl: src })}
					onRemove={() => setAttributes({ imageId: 0, imageUrl: '' })}
					alt={imageAlt}
					onAlt={(value) => setAttributes({ imageAlt: value })}
					help={__(
						'Leave empty: the title sits next to the image, so the image is treated as decoration.',
						'chargenet',
					)}
				/>
				<InspectorControls>
					<PanelBody title={__('Card link', 'chargenet')}>
						<TextControl
							label={__('Link URL (optional)', 'chargenet')}
							value={url}
							type="url"
							onChange={(value) => setAttributes({ url: value })}
						/>
						<TextControl
							label={__('Link label (optional)', 'chargenet')}
							help={__(
								'Shown as a visual cue, for example "Read more". The title is the link.',
								'chargenet',
							)}
							value={linkLabel}
							onChange={(value) => setAttributes({ linkLabel: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					{imageUrl && (
						<figure className="slide__media">
							<img className="slide__img" src={imageUrl} alt="" />
						</figure>
					)}
					<div className="slide__body">
						<RichText
							tagName={`h${level}`}
							className="slide__title"
							value={title}
							onChange={(value) => setAttributes({ title: value })}
							placeholder={__('Card title', 'chargenet')}
							allowedFormats={[]}
						/>
						<RichText
							tagName="p"
							className="slide__text"
							value={text}
							onChange={(value) => setAttributes({ text: value })}
							placeholder={__('Short text', 'chargenet')}
							allowedFormats={['core/bold', 'core/italic']}
						/>
						{url && linkLabel && <span className="slide__more">{linkLabel} →</span>}
					</div>
				</div>
			</>
		);
	},
	save: () => null,
});
