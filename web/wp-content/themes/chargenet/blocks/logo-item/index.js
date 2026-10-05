import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import { ImagePanel } from '../_shared/media.js';

registerBlockType(metadata, {
	edit({ attributes, setAttributes }) {
		const { imageId, imageUrl, name, url } = attributes;
		const blockProps = useBlockProps({ className: 'logo-item' });

		return (
			<>
				<ImagePanel
					title={__('Logo image', 'chargenet')}
					id={imageId}
					url={imageUrl}
					onSelect={({ id, url: src }) => setAttributes({ imageId: id, imageUrl: src })}
					onRemove={() => setAttributes({ imageId: 0, imageUrl: '' })}
				/>
				<InspectorControls>
					<PanelBody title={__('Organisation', 'chargenet')}>
						<TextControl
							label={__('Name', 'chargenet')}
							help={__(
								'Used as the alt text of the logo. Required for a linked logo.',
								'chargenet',
							)}
							value={name}
							onChange={(value) => setAttributes({ name: value })}
						/>
						<TextControl
							label={__('Link URL (optional)', 'chargenet')}
							value={url}
							type="url"
							onChange={(value) => setAttributes({ url: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					{imageUrl ? (
						<img className="logo-item__img" src={imageUrl} alt={name} />
					) : (
						<p className="logo-item__placeholder">{__('Choose a logo', 'chargenet')}</p>
					)}
				</div>
			</>
		);
	},
	save: () => null,
});
