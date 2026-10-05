import { InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

// Inspector panel to pick or remove one image. Pass `alt`/`onAlt` to show the alt text override
// (leave them out for decorative images).
export function ImagePanel({ title, id, url, onSelect, onRemove, alt, onAlt, help }) {
	return (
		<InspectorControls>
			<PanelBody title={title} initialOpen={!!url}>
				{url && <img src={url} alt="" style={{ maxWidth: '100%', height: 'auto' }} />}
				<MediaUploadCheck>
					<MediaUpload
						allowedTypes={['image']}
						value={id}
						onSelect={(media) => onSelect({ id: media.id, url: media.url })}
						render={({ open }) => (
							<Button variant="secondary" onClick={open} style={{ marginTop: 8 }}>
								{url ? __('Replace image', 'chargenet') : __('Choose image', 'chargenet')}
							</Button>
						)}
					/>
				</MediaUploadCheck>
				{url && (
					<Button
						variant="link"
						isDestructive
						onClick={onRemove}
						style={{ marginTop: 8, marginLeft: 8 }}
					>
						{__('Remove', 'chargenet')}
					</Button>
				)}
				{onAlt && (
					<TextControl
						label={__('Alt text override', 'chargenet')}
						help={
							help ??
							__(
								'Leave empty to use the alt text of the media library item. Fill it in per language.',
								'chargenet',
							)
						}
						value={alt}
						onChange={onAlt}
						style={{ marginTop: 12 }}
					/>
				)}
			</PanelBody>
		</InspectorControls>
	);
}
