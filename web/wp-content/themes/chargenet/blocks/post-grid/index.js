import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import {
	HeadingLevelControl,
	SectionInspector,
	withSectionAttributes,
} from '../_shared/section.js';
import './style.scss';

// Dynamic section: the preview is the real server output, so the heading and settings live in the sidebar.
registerBlockType(metadata, {
	attributes: withSectionAttributes(metadata),
	edit({ attributes, setAttributes }) {
		const { variant, eyebrow, heading, intro, count, categoryId, linkLabel, linkUrl } = attributes;
		const blockProps = useBlockProps();
		const categories = useSelect(
			(select) => select(coreStore).getEntityRecords('taxonomy', 'category', { per_page: -1 }),
			[],
		);

		return (
			<>
				<SectionInspector attributes={attributes} setAttributes={setAttributes} />
				<HeadingLevelControl attributes={attributes} setAttributes={setAttributes} />
				<InspectorControls>
					<PanelBody title={__('Posts', 'chargenet')}>
						<SelectControl
							label={__('Variant', 'chargenet')}
							value={variant}
							options={[
								{ value: 'latest', label: __('Latest (equal cards)', 'chargenet') },
								{ value: 'featured-grid', label: __('Featured plus grid', 'chargenet') },
							]}
							onChange={(value) => setAttributes({ variant: value })}
						/>
						<RangeControl
							label={__('Number of posts', 'chargenet')}
							value={count}
							min={1}
							max={12}
							onChange={(value) => setAttributes({ count: value })}
						/>
						<SelectControl
							label={__('Category', 'chargenet')}
							help={__(
								'Categories belong to one language: on the Dutch page pick the Dutch category.',
								'chargenet',
							)}
							value={String(categoryId)}
							options={[
								{ value: '0', label: __('All categories', 'chargenet') },
								...(categories ?? []).map((term) => ({ value: String(term.id), label: term.name })),
							]}
							onChange={(value) => setAttributes({ categoryId: Number(value) })}
						/>
					</PanelBody>
					<PanelBody title={__('Text above the posts', 'chargenet')} initialOpen={false}>
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
					<PanelBody title={__('"View all" link', 'chargenet')} initialOpen={false}>
						<TextControl
							label={__('Link label', 'chargenet')}
							help={__('Leave empty for no link.', 'chargenet')}
							value={linkLabel}
							onChange={(value) => setAttributes({ linkLabel: value })}
						/>
						<TextControl
							label={__('Link URL (optional)', 'chargenet')}
							help={__('Empty: the posts page from Settings → Reading.', 'chargenet')}
							value={linkUrl}
							type="url"
							onChange={(value) => setAttributes({ linkUrl: value })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<ServerSideRender
						block={metadata.name}
						attributes={attributes}
						EmptyResponsePlaceholder={() => (
							<p className="post-grid__empty">
								{__('No posts to show yet in this language and category.', 'chargenet')}
							</p>
						)}
					/>
				</div>
			</>
		);
	},
	save: () => null,
});
