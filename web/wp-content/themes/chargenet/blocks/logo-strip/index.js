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
		const blockProps = useBlockProps(sectionProps(attributes, 'logo-strip'));

		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<InspectorControls>
					<PanelBody title={__('Logo strip', 'chargenet')}>
						<SelectControl
							label={__('Variant', 'chargenet')}
							value={variant}
							options={[
								{ value: 'grayscale', label: __('Grayscale (colour on hover)', 'chargenet') },
								{ value: 'colour', label: __('Colour', 'chargenet') },
							]}
							onChange={(value) => setAttributes({ variant: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<section {...blockProps}>
					<div className="container">
						<div className={`logo-strip logo-strip--${variant} stack`}>
							<SectionHeaderFields attributes={attributes} setAttributes={setAttributes} />
							<div className="logo-strip__list">
								<InnerBlocks
									allowedBlocks={['chargenet/logo-item']}
									template={[
										['chargenet/logo-item'],
										['chargenet/logo-item'],
										['chargenet/logo-item'],
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
