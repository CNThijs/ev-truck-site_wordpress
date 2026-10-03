// Usage: npm run make:section <name> [--view]
// Creates web/wp-content/themes/chargenet/blocks/<name>/ from bin/templates/section.
import { existsSync, mkdirSync, readdirSync, readFileSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';

const args = process.argv.slice(2);
const withView = args.includes('--view');
const name = args.find((a) => !a.startsWith('--'));

if (!name || !/^[a-z][a-z0-9]*(-[a-z0-9]+)*$/.test(name)) {
	console.error(
		'Usage: npm run make:section <name> [--view]\nName must be lowercase kebab-case, e.g. faq-list.',
	);
	process.exit(1);
}

const root = resolve(import.meta.dirname, '..');
const target = resolve(root, 'web/wp-content/themes/chargenet/blocks', name);
if (existsSync(target)) {
	console.error(`blocks/${name} already exists.`);
	process.exit(1);
}

const title = name.replace(/-/g, ' ').replace(/^./, (c) => c.toUpperCase());
const view = withView ? ',\n\t"viewScriptModule": "file:./view.js"' : '';
const fill = (text) =>
	text.replaceAll('{{slug}}', name).replaceAll('{{Title}}', title).replaceAll('{{view}}', view);

mkdirSync(target, { recursive: true });
const templates = resolve(import.meta.dirname, 'templates/section');
for (const file of readdirSync(templates)) {
	if (file === 'view.js' && !withView) {
		continue;
	}
	writeFileSync(resolve(target, file), fill(readFileSync(resolve(templates, file), 'utf8')));
}
console.log(
	`Created blocks/${name}. Run npm run build, then add it to a page. See docs/sections.md.`,
);
