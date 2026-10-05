import { SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import iconData from '../../inc/icons.json';

// Same icon set the PHP side prints (inc/icons.json), so the editor shows what visitors see.
export const iconNames = Object.keys(iconData);

const label = (name) => name.replace(/-/g, ' ').replace(/^./, (c) => c.toUpperCase());

export function Icon({ name }) {
	if (!iconData[name]) {
		return null;
	}
	return (
		<svg
			className="icon"
			xmlns="http://www.w3.org/2000/svg"
			width="24"
			height="24"
			viewBox="0 0 24 24"
			fill="none"
			stroke="currentColor"
			strokeWidth="1.75"
			strokeLinecap="round"
			strokeLinejoin="round"
			aria-hidden="true"
			focusable="false"
			dangerouslySetInnerHTML={{ __html: iconData[name] }}
		/>
	);
}

export function IconControl({ value, onChange }) {
	return (
		<SelectControl
			label={__('Icon', 'chargenet')}
			value={value}
			options={[
				{ value: '', label: __('No icon', 'chargenet') },
				...iconNames.map((name) => ({ value: name, label: label(name) })),
			]}
			onChange={onChange}
		/>
	);
}
