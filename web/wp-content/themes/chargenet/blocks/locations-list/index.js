import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl, TextControl, TextareaControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import {
	HeadingLevelControl,
	SectionInspector,
	withSectionAttributes,
} from '../_shared/section.js';
import './style.scss';

// Dynamic section: the preview is the real server output. Locations are edited under Locations in the admin menu.
registerBlockType(metadata, {
	attributes: withSectionAttributes(metadata),
	edit({ attributes, setAttributes }) {
		const { eyebrow, heading, intro, count } = attributes;
		const blockProps = useBlockProps();
		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<InspectorControls>
					<PanelBody title={__('Locations', 'chargenet')}>
						<RangeControl
							label={__('Maximum number of locations', 'chargenet')}
							value={count}
							min={1}
							max={200}
							onChange={(value) => setAttributes({ count: value })}
						/>
						<p>
							{__('Add and edit the locations under Locations in the admin menu.', 'chargenet')}
						</p>
					</PanelBody>
					<PanelBody title={__('Text above the list', 'chargenet')} initialOpen={false}>
						<TextControl
							label={__('Eyebrow (optional)', 'chargenet')}
							value={eyebrow}
							onChange={(value) => setAttributes({ eyebrow: value })}
						/>
						<TextControl
							label={__('Section heading', 'chargenet')}
							value={heading}
							onChange={(value) => setAttributes({ heading: value })}
						/>
						<TextareaControl
							label={__('Introduction (optional)', 'chargenet')}
							value={intro}
							onChange={(value) => setAttributes({ intro: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<ServerSideRender
						block={metadata.name}
						attributes={attributes}
						EmptyResponsePlaceholder={() => (
							<p className="locations__empty">
								{__(
									'No locations yet in this language. Add them under Locations in the admin menu.',
									'chargenet',
								)}
							</p>
						)}
					/>
				</div>
			</>
		);
	},
	save: () => null,
});
