import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';
import {
	HeadingLevelControl,
	SectionInspector,
	withSectionAttributes,
} from '../_shared/section.js';
import { SectionHeaderFields } from '../_shared/header.js';
import './style.scss';

// Dynamic section: the heading is edited here, the form below is the real server output (not clickable in the editor).
registerBlockType(metadata, {
	attributes: withSectionAttributes(metadata),
	edit({ attributes, setAttributes }) {
		const blockProps = useBlockProps();
		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<div {...blockProps}>
					<div className="container container--narrow stack">
						<SectionHeaderFields attributes={attributes} setAttributes={setAttributes} />
						<div className="cn-form-wrap" style={{ pointerEvents: 'none' }}>
							<ServerSideRender
								block={metadata.name}
								attributes={{ ...attributes, heading: '', eyebrow: '', intro: '' }}
							/>
						</div>
					</div>
				</div>
			</>
		);
	},
	save: () => null,
});
