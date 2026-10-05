import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
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
		const { variant } = attributes;
		const blockProps = useBlockProps(sectionProps(attributes, 'steps'));

		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<InspectorControls>
					<PanelBody title={__('Steps', 'chargenet')}>
						<SelectControl
							label={__('Variant', 'chargenet')}
							value={variant}
							options={[
								{ value: 'horizontal', label: __('Horizontal', 'chargenet') },
								{ value: 'vertical', label: __('Vertical (timeline)', 'chargenet') },
							]}
							onChange={(value) => setAttributes({ variant: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<section {...blockProps}>
					<div className="container">
						<div className={`steps steps--${variant} stack`}>
							<SectionHeaderFields attributes={attributes} setAttributes={setAttributes} />
							<div className="steps__track">
								<svg
									className="steps__line"
									aria-hidden="true"
									focusable="false"
									viewBox={variant === 'vertical' ? '0 0 2 100' : '0 0 100 2'}
									preserveAspectRatio="none"
								>
									<path d={variant === 'vertical' ? 'M1 0V100' : 'M0 1H100'} pathLength="1" />
								</svg>
								<div className="steps__list">
									<InnerBlocks
										allowedBlocks={['chargenet/step-item']}
										template={[
											['chargenet/step-item'],
											['chargenet/step-item'],
											['chargenet/step-item'],
										]}
										orientation="horizontal"
									/>
								</div>
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => <InnerBlocks.Content />,
});
