# ChargeNet website

WordPress with a custom theme (`chargenet`). See `CLAUDE.md` for the full project context and commands.

## Local development

Needs Docker, [DDEV](https://ddev.com) and Node 22.

```sh
npm ci && ddev start
```

`ddev start` installs Composer packages (the plugin manifest), downloads WordPress, installs it and activates the theme and Polylang.
Open https://chargenet.ddev.site, log in at `/wp-admin` with `admin` / `admin` (local only).

Hot reload: `npm run dev` (Vite on http://localhost:5273; the theme picks it up while it runs).

Other commands: `npm run lint`, `npm run build`, `npm run package`, `ddev wp …`.

## Troubleshooting

- `docker-credential-desktop: executable file not found`: add `/Applications/Docker.app/Contents/Resources/bin` to your `PATH`.
- `Port 5273 is already in use`: another process holds the Vite port. Stop it, or change `server.port` in `vite.config.js` (the theme reads the port from the `hot` file).
- Translation template: `npm run i18n` (runs `wp i18n make-pot` in DDEV).
