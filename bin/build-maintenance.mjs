// Builds the "work in progress" gate for installing the new site directly on the main domain (docs/launch-runbook.md).
// Everyone gets a 503 maintenance page (search engines treat 503 as temporary) except the listed IP addresses and
// browsers that carry the secret cookie (open https://<site>/?cn_preview=<secret> once; a phone on mobile data uses this).
//   node bin/build-maintenance.mjs --secret <random> [--ip 203.0.113.5 --ip 2001:db8::1] [--host chargenet.energy] [--out dist/maintenance]
// Writes <out>/maintenance.html and <out>/htaccess-gate.txt (paste ABOVE every other block in .htaccess; delete the block at
// go-live). Test on a real Apache: npm run test:maintenance-gate.
import { mkdirSync, writeFileSync } from 'node:fs';

const args = process.argv.slice(2);
const values = (flag) => args.flatMap((a, i) => (a === flag ? [args[i + 1]] : []));
const secret = values('--secret')[0];
const ips = values('--ip');
const out = values('--out')[0] ?? 'dist/maintenance';
const host = values('--host')[0] ?? 'chargenet.energy';
if (!secret || !/^[A-Za-z0-9]{16,}$/.test(secret)) {
	console.error(
		'Give --secret with at least 16 letters and digits (for example: openssl rand -hex 16).',
	);
	process.exit(1);
}
for (const ip of ips) {
	if (!/^[0-9a-fA-F:.]+$/.test(ip)) {
		console.error(`Not an IP address: ${ip}`);
		process.exit(1);
	}
}

const html = `<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>ChargeNet — back soon</title>
<style>
body{margin:0;min-height:100vh;display:grid;place-items:center;background:#083a0b;color:#fff;font:400 18px/1.5 system-ui,-apple-system,"Segoe UI",sans-serif;text-align:center}
main{max-width:34rem;padding:2rem}
h1{font-size:1.8rem;margin:0 0 .75rem;color:#fae104}
p{margin:.5rem 0}
a{color:#fae104}
</style>
</head>
<body>
<main>
<h1>Our new website is almost ready</h1>
<p>We are updating chargenet.energy and will be back shortly.</p>
<p>Questions? <a href="mailto:info@chargenet.energy">info@chargenet.energy</a></p>
<hr style="border:0;border-top:1px solid #ffffff55;margin:1.5rem 0">
<h1 lang="nl">Onze nieuwe website is bijna klaar</h1>
<p lang="nl">Wij werken aan chargenet.energy en zijn zo weer terug.</p>
<p lang="nl">Vragen? <a href="mailto:info@chargenet.energy">info@chargenet.energy</a></p>
</main>
</body>
</html>
`;

const ipCond = ips
	.map((ip) => `\tRewriteCond %{REMOTE_ADDR} !^${ip.replace(/\./g, '\\.')}$`)
	.join('\n');
const gate = `# BEGIN ChargeNet maintenance gate (remove at go-live)
<IfModule mod_rewrite.c>
	RewriteEngine On
	ErrorDocument 503 /maintenance.html
	# Open https://<site>/?cn_preview=${secret} once to get the cookie (30 days), then browse normally.
	RewriteCond %{QUERY_STRING} (^|&)cn_preview=${secret}(&|$)
	RewriteRule ^ - [CO=cn_preview:${secret}:${host}:43200:/,S=1]
${ipCond}${ipCond ? '\n' : ''}	RewriteCond %{HTTP_COOKIE} !(^|;\\s*)cn_preview=${secret}(;|$)
	RewriteCond %{REQUEST_URI} !^/maintenance\\.html$
	RewriteRule ^ - [R=503,L]
</IfModule>
<IfModule mod_headers.c>
	Header always set Retry-After "3600"
	Header always set X-Robots-Tag "noindex"
</IfModule>
# END ChargeNet maintenance gate
`;
mkdirSync(out, { recursive: true });
writeFileSync(`${out}/maintenance.html`, html);
writeFileSync(`${out}/htaccess-gate.txt`, gate);
console.log(`Wrote ${out}/maintenance.html and ${out}/htaccess-gate.txt`);
