// Compiles languages/chargenet-<locale>.po into:
//   chargenet-<locale>.mo                                  (PHP strings, via msgfmt from gettext)
//   chargenet-<locale>-<block-script-handle>.json          (editor strings for each block's editor script)
// Works for any locale: add languages/chargenet-fr_FR.po and run `npm run translations`.
import { execFileSync } from 'node:child_process';
import { readdirSync, readFileSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';

const theme = resolve(import.meta.dirname, '../web/wp-content/themes/chargenet');
const langDir = resolve(theme, 'languages');
const blocksDir = resolve(theme, 'blocks');

const unquote = (line) => JSON.parse(line.slice(line.indexOf('"')));

// Minimal .po reader: msgid/msgstr pairs (no plurals or contexts used in this theme).
function parsePo(text) {
	const entries = {};
	let id = null;
	let str = null;
	let target = null;
	const flush = () => {
		if (id !== null && str !== null && id !== '' && str !== '') {
			entries[id] = str;
		}
	};
	for (const raw of text.split('\n')) {
		const line = raw.trim();
		if (line.startsWith('msgid ')) {
			flush();
			id = unquote(line);
			str = null;
			target = 'id';
		} else if (line.startsWith('msgstr ')) {
			str = unquote(line);
			target = 'str';
		} else if (line.startsWith('"')) {
			if (target === 'id') {
				id += unquote(line);
			} else if (target === 'str') {
				str += unquote(line);
			}
		}
	}
	flush();
	return entries;
}

// Block editor script handle, as WordPress generates it (generate_block_asset_handle).
const handles = readdirSync(blocksDir, { withFileTypes: true })
	.filter((d) => d.isDirectory() && !d.name.startsWith('_'))
	.map((d) => `chargenet-${d.name}-editor-script`);

for (const file of readdirSync(langDir).filter((f) => /^chargenet-.+\.po$/.test(f))) {
	const locale = file.replace(/^chargenet-/, '').replace(/\.po$/, '');
	const po = resolve(langDir, file);
	execFileSync('msgfmt', ['-o', resolve(langDir, `chargenet-${locale}.mo`), po]);

	const strings = Object.fromEntries(
		Object.entries(parsePo(readFileSync(po, 'utf8'))).map(([k, v]) => [k, [v]]),
	);
	const json = JSON.stringify({
		'translation-revision-date': '2026-10-05 12:00+0000',
		generator: 'chargenet bin/build-translations.mjs',
		domain: 'messages',
		locale_data: {
			messages: {
				'': { domain: 'messages', lang: locale, 'plural-forms': 'nplurals=2; plural=(n != 1);' },
				...strings,
			},
		},
	});
	for (const handle of handles) {
		writeFileSync(resolve(langDir, `chargenet-${locale}-${handle}.json`), json);
	}
	console.log(
		`${locale}: ${Object.keys(strings).length} strings, ${handles.length} editor scripts`,
	);
}
