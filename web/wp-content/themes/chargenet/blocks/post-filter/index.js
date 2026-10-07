import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import { SectionInspector, withSectionAttributes } from '../_shared/section.js';
import './style.scss';

// Dynamic section: the preview is the real server output.
registerBlockType(metadata, {
	attributes: withSectionAttributes(metadata),
	edit({ attributes, setAttributes }) {
		const { showCategories, showSearch } = attributes;
		const blockProps = useBlockProps();
		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<InspectorControls>
					<PanelBody title={__('Filter', 'chargenet')}>
						<ToggleControl
							label={__('Category links', 'chargenet')}
							checked={showCategories}
							onChange={(value) => setAttributes({ showCategories: value })}
						/>
						<ToggleControl
							label={__('Search box', 'chargenet')}
							checked={showSearch}
							onChange={(value) => setAttributes({ showSearch: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<ServerSideRender block={metadata.name} attributes={attributes} />
				</div>
			</>
		);
	},
	save: () => null,
});
