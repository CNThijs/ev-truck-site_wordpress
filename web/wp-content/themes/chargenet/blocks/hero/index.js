import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import { SectionInspector, sectionProps, withSectionAttributes } from '../_shared/section.js';
import { ImagePanel } from '../_shared/media.js';
import './style.scss';
import './editor.scss';

registerBlockType(metadata, {
	attributes: withSectionAttributes(metadata),
	edit({ attributes, setAttributes }) {
		const { variant, eyebrow, heading, intro, imageUrl, overlay, coverId, coverUrl, coverAlt } =
			attributes;
		const blockProps = useBlockProps(sectionProps(attributes, 'hero'));
		blockProps.className += ` hero hero--${variant}`;
		const hasBg = variant !== 'title-band' && imageUrl;

		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<InspectorControls>
					<PanelBody title={__('Hero', 'chargenet')}>
						<SelectControl
							label={__('Variant', 'chargenet')}
							value={variant}
							options={[
								{ value: 'banner', label: __('Banner (background image)', 'chargenet') },
								{ value: 'title-band', label: __('Title band (short)', 'chargenet') },
								{ value: 'campaign', label: __('Campaign (cover image)', 'chargenet') },
							]}
							onChange={(value) => setAttributes({ variant: value })}
						/>
						{variant !== 'title-band' && (
							<SelectControl
								label={__('Image overlay', 'chargenet')}
								value={overlay}
								options={[
									{ value: 'standard', label: __('Standard', 'chargenet') },
									{ value: 'strong', label: __('Strong (busy photos)', 'chargenet') },
								]}
								onChange={(value) => setAttributes({ overlay: value })}
							/>
						)}
					</PanelBody>
				</InspectorControls>
				{variant !== 'title-band' && (
					<ImagePanel
						title={__('Background image', 'chargenet')}
						id={attributes.imageId}
						url={imageUrl}
						onSelect={({ id, url }) => setAttributes({ imageId: id, imageUrl: url })}
						onRemove={() => setAttributes({ imageId: 0, imageUrl: '' })}
					/>
				)}
				{variant === 'campaign' && (
					<ImagePanel
						title={__('Cover image', 'chargenet')}
						id={coverId}
						url={coverUrl}
						onSelect={({ id, url }) => setAttributes({ coverId: id, coverUrl: url })}
						onRemove={() => setAttributes({ coverId: 0, coverUrl: '' })}
						alt={coverAlt}
						onAlt={(value) => setAttributes({ coverAlt: value })}
					/>
				)}
				<section {...blockProps}>
					<div className="container">
						{hasBg && (
							<div className="hero__bg">
								<img className="hero__bg-img" src={imageUrl} alt="" />
								<span className="hero__overlay" data-overlay={overlay} />
							</div>
						)}
						<div className="hero__inner">
							<div className="hero__body stack">
								<RichText
									tagName="p"
									className="t-eyebrow"
									value={eyebrow}
									onChange={(value) => setAttributes({ eyebrow: value })}
									placeholder={__('Eyebrow (optional)', 'chargenet')}
									allowedFormats={[]}
								/>
								<RichText
									tagName="h1"
									className="hero__title"
									value={heading}
									onChange={(value) => setAttributes({ heading: value })}
									placeholder={__('Page heading (h1)', 'chargenet')}
									allowedFormats={['core/bold', 'core/italic']}
								/>
								<RichText
									tagName="p"
									className="t-lead hero__intro"
									value={intro}
									onChange={(value) => setAttributes({ intro: value })}
									placeholder={__('Short introduction (optional)', 'chargenet')}
									allowedFormats={['core/bold', 'core/italic', 'core/link']}
								/>
								{variant !== 'title-band' && (
									<div className="cluster hero__actions">
										<InnerBlocks
											allowedBlocks={['chargenet/button']}
											template={[['chargenet/button', { label: 'Contact us', url: '#contact' }]]}
											orientation="horizontal"
										/>
									</div>
								)}
							</div>
							{variant === 'campaign' && (
								<figure className="hero__cover">
									{coverUrl ? (
										<img className="hero__cover-img" src={coverUrl} alt={coverAlt} />
									) : (
										<p className="hero__cover-empty">{__('Choose a cover image', 'chargenet')}</p>
									)}
								</figure>
							)}
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => <InnerBlocks.Content />,
});
