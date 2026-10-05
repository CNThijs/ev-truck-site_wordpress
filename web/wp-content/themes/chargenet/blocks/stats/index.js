import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import {
	HeadingLevelControl,
	SectionInspector,
	sectionProps,
	withSectionAttributes,
} from '../_shared/section.js';
import { ImagePanel } from '../_shared/media.js';
import './style.scss';
import './editor.scss';

registerBlockType(metadata, {
	attributes: withSectionAttributes(metadata),
	edit({ attributes, setAttributes }) {
		const { variant, eyebrow, heading, headingLevel, text, imageId, imageUrl } = attributes;
		const blockProps = useBlockProps(sectionProps(attributes, 'stats'));
		blockProps.className += ` stats stats--${variant}${imageUrl ? ' stats--has-bg' : ''}`;

		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<InspectorControls>
					<PanelBody title={__('Statistics', 'chargenet')}>
						<SelectControl
							label={__('Variant', 'chargenet')}
							value={variant}
							options={[
								{ value: 'row', label: __('Row (heading above)', 'chargenet') },
								{ value: 'with-text', label: __('Text beside the numbers', 'chargenet') },
							]}
							onChange={(value) => setAttributes({ variant: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<ImagePanel
					title={__('Background image (optional)', 'chargenet')}
					id={imageId}
					url={imageUrl}
					onSelect={({ id, url }) => setAttributes({ imageId: id, imageUrl: url })}
					onRemove={() => setAttributes({ imageId: 0, imageUrl: '' })}
				/>
				<section {...blockProps}>
					{imageUrl && (
						<div className="stats__bg">
							<img className="stats__bg-img" src={imageUrl} alt="" />
							<span className="stats__overlay" />
						</div>
					)}
					<div className="container">
						<div className="stats__layout">
							<header className="stats__head stack">
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
									placeholder={__('Section heading (optional)', 'chargenet')}
									allowedFormats={['core/bold', 'core/italic']}
								/>
								<RichText
									tagName="p"
									className="t-lead"
									value={text}
									onChange={(value) => setAttributes({ text: value })}
									placeholder={__('Supporting text (optional)', 'chargenet')}
									allowedFormats={['core/bold', 'core/italic', 'core/link']}
								/>
							</header>
							<div className="stats__list">
								<InnerBlocks
									allowedBlocks={['chargenet/stat-item']}
									template={[
										['chargenet/stat-item', { value: '80', suffix: '%' }],
										['chargenet/stat-item', { value: '18', suffix: '%' }],
										['chargenet/stat-item', { value: '3.8', suffix: 'Bn' }],
									]}
									orientation="horizontal"
								/>
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => <InnerBlocks.Content />,
});
