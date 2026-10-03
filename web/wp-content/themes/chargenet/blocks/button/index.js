import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

registerBlockType(metadata, {
	edit({ attributes, setAttributes }) {
		const blockProps = useBlockProps({ className: `btn btn--${attributes.variant}` });
		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Button', 'chargenet')}>
						<TextControl
							label={__('Link URL', 'chargenet')}
							type="url"
							value={attributes.url}
							onChange={(url) => setAttributes({ url })}
							help={__('Buttons without a URL are not shown on the site.', 'chargenet')}
						/>
						<SelectControl
							label={__('Style', 'chargenet')}
							value={attributes.variant}
							options={[
								{ value: 'primary', label: __('Primary', 'chargenet') },
								{ value: 'secondary', label: __('Secondary', 'chargenet') },
								{ value: 'ghost', label: __('Ghost', 'chargenet') },
							]}
							onChange={(variant) => setAttributes({ variant })}
						/>
						<ToggleControl
							label={__('Open in a new tab', 'chargenet')}
							checked={attributes.opensInNewTab}
							onChange={(opensInNewTab) => setAttributes({ opensInNewTab })}
						/>
					</PanelBody>
				</InspectorControls>
				<span {...blockProps}>
					<RichText
						tagName="span"
						value={attributes.label}
						onChange={(label) => setAttributes({ label })}
						placeholder={__('Button label', 'chargenet')}
						allowedFormats={[]}
					/>
				</span>
			</>
		);
	},
	save: () => null,
});
