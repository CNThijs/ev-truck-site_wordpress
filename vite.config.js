import { defineConfig } from 'vite';
import { writeFileSync, rmSync } from 'node:fs';
import { resolve } from 'node:path';

const theme = resolve(import.meta.dirname, 'web/wp-content/themes/chargenet');
const hotFile = resolve(theme, 'hot');

// Writes theme/hot while the dev server runs; the PHP enqueue helper reads it.
const hot = () => ({
	name: 'chargenet-hot',
	configureServer(server) {
		server.httpServer?.once('listening', () => {
			const { port } = server.httpServer.address();
			writeFileSync(hotFile, `http://localhost:${port}`);
		});
		const clean = () => rmSync(hotFile, { force: true });
		server.httpServer?.on('close', clean);
		process.on('exit', clean);
		process.on('SIGINT', () => process.exit());
		process.on('SIGTERM', () => process.exit());
	},
});

export default defineConfig({
	root: theme,
	base: './',
	plugins: [hot()],
	server: { host: 'localhost', port: 5273, strictPort: true, cors: true },
	build: {
		outDir: resolve(theme, 'assets/dist'),
		emptyOutDir: true,
		manifest: true,
		rollupOptions: {
			input: [
				resolve(theme, 'assets/src/js/main.js'),
				resolve(theme, 'assets/src/scss/editor.scss'),
			],
		},
	},
});
