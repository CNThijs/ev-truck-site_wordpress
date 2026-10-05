import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl } from '@wordpress/components';
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
		const { variant, columns, eyebrow, heading, headingLevel, intro } = attributes;
		const blockProps = useBlockProps(sectionProps(attributes, 'feature-grid'));

		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<InspectorControls>
					<PanelBody title={__('Grid', 'chargenet')}>
						<SelectControl
							label={__('Variant', 'chargenet')}
							value={variant}
							options={[
								{ value: 'cards', label: __('Cards', 'chargenet') },
								{ value: 'plain', label: __('Plain (no boxes)', 'chargenet') },
								{ value: 'numbered', label: __('Numbered', 'chargenet') },
							]}
							onChange={(value) => setAttributes({ variant: value })}
						/>
						<RangeControl
							label={__('Columns (wide screens)', 'chargenet')}
							value={columns}
							min={2}
							max={4}
							onChange={(value) => setAttributes({ columns: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<section {...blockProps}>
					<div className="container">
						<div className={`feature-grid feature-grid--${variant} stack`}>
							<header className="section-intro stack">
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
								<RichText
									tagName="p"
									className="t-lead"
									value={intro}
									onChange={(value) => setAttributes({ intro: value })}
									placeholder={__('Introduction (optional)', 'chargenet')}
									allowedFormats={['core/bold', 'core/italic', 'core/link']}
								/>
							</header>
							<div className="feature-grid__list" data-columns={columns}>
								<InnerBlocks
									allowedBlocks={['chargenet/feature-grid-item']}
									template={[
										['chargenet/feature-grid-item', { icon: 'zap' }],
										['chargenet/feature-grid-item', { icon: 'lock' }],
										['chargenet/feature-grid-item', { icon: 'users' }],
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
