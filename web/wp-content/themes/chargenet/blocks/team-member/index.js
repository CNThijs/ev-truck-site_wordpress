import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import { ImagePanel } from '../_shared/media.js';

const initials = (name) =>
	name
		.trim()
		.split(/\s+/)
		.slice(0, 2)
		.map((part) => part.charAt(0).toUpperCase())
		.join('');

registerBlockType(metadata, {
	edit({ attributes, setAttributes, context }) {
		const { imageId, imageUrl, imageAlt, name, role, bio, email, linkedin } = attributes;
		const level = Math.max(3, Math.min(4, (context['chargenet/headingLevel'] ?? 2) + 1));
		const blockProps = useBlockProps({ className: 'person' });

		return (
			<>
				<ImagePanel
					title={__('Photo', 'chargenet')}
					id={imageId}
					url={imageUrl}
					onSelect={({ id, url }) => setAttributes({ imageId: id, imageUrl: url })}
					onRemove={() => setAttributes({ imageId: 0, imageUrl: '' })}
					alt={imageAlt}
					onAlt={(value) => setAttributes({ imageAlt: value })}
					help={__(
						'Leave empty: the name sits next to the photo, so the photo is treated as decoration.',
						'chargenet',
					)}
				/>
				<InspectorControls>
					<PanelBody title={__('Short bio', 'chargenet')}>
						<TextareaControl
							label={__('Bio', 'chargenet')}
							help={__('One paragraph per line. Optional.', 'chargenet')}
							value={bio}
							onChange={(value) => setAttributes({ bio: value })}
						/>
					</PanelBody>
					<PanelBody title={__('Contact links', 'chargenet')}>
						<TextControl
							label={__('Email', 'chargenet')}
							value={email}
							type="email"
							onChange={(value) => setAttributes({ email: value })}
						/>
						<TextControl
							label={__('LinkedIn URL', 'chargenet')}
							value={linkedin}
							type="url"
							onChange={(value) => setAttributes({ linkedin: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<figure className="person__photo">
						{imageUrl ? (
							<img className="person__img" src={imageUrl} alt="" />
						) : (
							<span className="person__initials" aria-hidden="true">
								{initials(name)}
							</span>
						)}
					</figure>
					<RichText
						tagName={`h${level}`}
						className="person__name"
						value={name}
						onChange={(value) => setAttributes({ name: value })}
						placeholder={__('Name', 'chargenet')}
						allowedFormats={[]}
					/>
					<RichText
						tagName="p"
						className="person__role"
						value={role}
						onChange={(value) => setAttributes({ role: value })}
						placeholder={__('Role', 'chargenet')}
						allowedFormats={[]}
					/>
					{bio.trim() && (
						<div className="person__bio stack">
							{bio
								.split(/\r?\n+/)
								.filter(Boolean)
								.map((line) => (
									<p key={line}>{line}</p>
								))}
						</div>
					)}
				</div>
			</>
		);
	},
	save: () => null,
});
