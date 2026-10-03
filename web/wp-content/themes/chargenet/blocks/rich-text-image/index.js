import { registerBlockType } from '@wordpress/blocks';
import {
	BlockControls,
	InnerBlocks,
	InspectorControls,
	MediaPlaceholder,
	MediaReplaceFlow,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import {
	HeadingLevelControl,
	SectionInspector,
	sectionProps,
	withSectionAttributes,
} from '../_shared/section.js';
import './style.scss';
import './editor.scss';

registerBlockType(metadata, {
	attributes: withSectionAttributes(metadata),
	edit({ attributes, setAttributes }) {
		const { eyebrow, heading, headingLevel, imageId, imageUrl, imageAlt, imagePosition } =
			attributes;
		const blockProps = useBlockProps(sectionProps(attributes, 'rich-text-image'));
		const onSelect = (media) => setAttributes({ imageId: media.id, imageUrl: media.url });

		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<InspectorControls>
					<PanelBody title={__('Image', 'chargenet')}>
						<SelectControl
							label={__('Image position (wide screens)', 'chargenet')}
							value={imagePosition}
							options={[
								{ value: 'right', label: __('Right', 'chargenet') },
								{ value: 'left', label: __('Left', 'chargenet') },
							]}
							onChange={(value) => setAttributes({ imagePosition: value })}
						/>
						<TextControl
							label={__('Alt text override', 'chargenet')}
							help={__(
								'Leave empty to use the alt text of the media library item. Fill it in per language.',
								'chargenet',
							)}
							value={imageAlt}
							onChange={(value) => setAttributes({ imageAlt: value })}
						/>
					</PanelBody>
				</InspectorControls>
				{imageUrl && (
					<BlockControls group="other">
						<MediaReplaceFlow
							mediaId={imageId}
							mediaURL={imageUrl}
							allowedTypes={['image']}
							accept="image/*"
							onSelect={onSelect}
						/>
					</BlockControls>
				)}
				<section {...blockProps}>
					<div className="container">
						<div className={`rti rti--image-${imagePosition}`}>
							<div className="rti__body stack">
								<RichText
									tagName="p"
									className="t-eyebrow"
									value={eyebrow}
									onChange={(value) => setAttributes({ eyebrow: value })}
									placeholder={__('Eyebrow (optional)', 'chargenet')}
									allowedFormats={[]}
								/>
								<RichText
									tagName={`h${headingLevel}`}
									value={heading}
									onChange={(value) => setAttributes({ heading: value })}
									placeholder={__('Section heading', 'chargenet')}
									allowedFormats={['core/bold', 'core/italic']}
								/>
								<div className="rti__content stack">
									<InnerBlocks
										allowedBlocks={['core/paragraph', 'core/list', 'chargenet/button']}
										template={[
											['core/paragraph', { placeholder: __('Write the text…', 'chargenet') }],
										]}
									/>
								</div>
							</div>
							<figure className="rti__media">
								{imageUrl ? (
									<img src={imageUrl} alt={imageAlt} />
								) : (
									<MediaPlaceholder
										icon="format-image"
										labels={{ title: __('Image', 'chargenet') }}
										allowedTypes={['image']}
										onSelect={onSelect}
									/>
								)}
							</figure>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => <InnerBlocks.Content />,
});
