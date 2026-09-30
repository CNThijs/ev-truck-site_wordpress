import { request } from 'playwright';
import fs from 'fs';
const ctx = await request.newContext();
for (const f of ['sitemap.xml','robots.txt']) {
  const r = await ctx.get('https://chargenet.energy/' + f);
  const t = await r.text();
  fs.writeFileSync('../../docs/audit/' + f, t);
  console.log('==', f, r.status(), r.headers()['content-type']); console.log(t);
}
