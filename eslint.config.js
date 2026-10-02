import js from '@eslint/js';
import globals from 'globals';

export default [
	{
		ignores: [
			'**/dist/**',
			'vendor/**',
			'node_modules/**',
			'tools/**',
			'docs/**',
			'web/wp-content/plugins/**',
		],
	},
	js.configs.recommended,
	{
		files: ['web/wp-content/themes/chargenet/assets/src/**/*.js'],
		languageOptions: { globals: globals.browser },
	},
	{
		files: ['vite.config.js', 'eslint.config.js'],
		languageOptions: { globals: globals.node },
	},
];
