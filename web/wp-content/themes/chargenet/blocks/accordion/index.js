import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import {
	HeadingLevelControl,
	SectionInspector,
	sectionProps,
	withSectionAttributes,
} from '../_shared/section.js';
import { SectionHeaderFields } from '../_shared/header.js';
import './style.scss';
import './editor.scss';

registerBlockType(metadata, {
	attributes: withSectionAttributes(metadata),
	edit({ attributes, setAttributes }) {
		const { variant, headingLevel, asideHeading, asideText, asideLabel, asideUrl, faqSchema } =
			attributes;
		const blockProps = useBlockProps(sectionProps(attributes, 'accordion'));
		const withAside = variant === 'with-aside';

		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<InspectorControls>
					<PanelBody title={__('Accordion', 'chargenet')}>
						<SelectControl
							label={__('Variant', 'chargenet')}
							value={variant}
							options={[
								{ value: 'single', label: __('Single column', 'chargenet') },
								{ value: 'with-aside', label: __('With help box beside it', 'chargenet') },
							]}
							onChange={(value) => setAttributes({ variant: value })}
						/>
						<ToggleControl
							label={__('Add FAQ structured data', 'chargenet')}
							help={__(
								'Prints schema.org FAQPage data for search engines. Leave off if an SEO plugin handles it.',
								'chargenet',
							)}
							checked={faqSchema}
							onChange={(value) => setAttributes({ faqSchema: value })}
						/>
					</PanelBody>
					{withAside && (
						<PanelBody title={__('Help box button', 'chargenet')} initialOpen={false}>
							<TextControl
								label={__('Button label', 'chargenet')}
								value={asideLabel}
								onChange={(value) => setAttributes({ asideLabel: value })}
							/>
							<TextControl
								label={__('Button URL', 'chargenet')}
								value={asideUrl}
								type="url"
								onChange={(value) => setAttributes({ asideUrl: value })}
							/>
						</PanelBody>
					)}
				</InspectorControls>
				<section {...blockProps}>
					<div className="container">
						<div className={`accordion accordion--${variant} stack`}>
							<SectionHeaderFields attributes={attributes} setAttributes={setAttributes} />
							<div className={`accordion__layout${withAside ? ' accordion__layout--aside' : ''}`}>
								<div className="accordion__list">
									<InnerBlocks
										allowedBlocks={['chargenet/accordion-item']}
										template={[['chargenet/accordion-item'], ['chargenet/accordion-item']]}
									/>
								</div>
								{withAside && (
									<aside className="accordion__aside card stack">
										<RichText
											tagName={`h${Math.min(4, headingLevel + 1)}`}
											className="accordion__aside-title"
											value={asideHeading}
											onChange={(value) => setAttributes({ asideHeading: value })}
											placeholder={__('Help box heading', 'chargenet')}
											allowedFormats={[]}
										/>
										<RichText
											tagName="p"
											value={asideText}
											onChange={(value) => setAttributes({ asideText: value })}
											placeholder={__('Help box text', 'chargenet')}
											allowedFormats={['core/bold', 'core/italic', 'core/link']}
										/>
										{asideLabel && <span className="btn btn--primary">{asideLabel}</span>}
									</aside>
								)}
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => <InnerBlocks.Content />,
});
