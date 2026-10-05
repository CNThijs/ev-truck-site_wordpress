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
		const blockProps = useBlockProps(sectionProps(attributes, 'card-slider'));

		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<InspectorControls>
					<PanelBody title={__('Card slider', 'chargenet')}>
						<SelectControl
							label={__('Variant', 'chargenet')}
							value={variant}
							options={[
								{ value: 'image-bg', label: __('Image as background', 'chargenet') },
								{ value: 'image-top', label: __('Image on top', 'chargenet') },
							]}
							onChange={(value) => setAttributes({ variant: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<section {...blockProps}>
					<div className="container">
						<div className={`card-slider card-slider--${variant} stack`}>
							<SectionHeaderFields attributes={attributes} setAttributes={setAttributes} />
							<div className="card-slider__viewport">
								<div className="card-slider__track">
									<InnerBlocks
										allowedBlocks={['chargenet/slide-card']}
										template={[
											['chargenet/slide-card'],
											['chargenet/slide-card'],
											['chargenet/slide-card'],
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
