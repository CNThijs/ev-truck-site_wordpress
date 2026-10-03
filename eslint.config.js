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
		files: ['web/wp-content/themes/chargenet/blocks/**/*.js'],
		languageOptions: {
			globals: globals.browser,
			parserOptions: { ecmaFeatures: { jsx: true } },
		},
	},
	{
		files: ['vite.config.js', 'eslint.config.js', 'bin/**/*.mjs'],
		languageOptions: { globals: globals.node },
	},
];
