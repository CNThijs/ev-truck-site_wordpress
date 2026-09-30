import { chromium } from 'playwright';
const b = await chromium.launch(); const p = await (await b.newContext()).newPage();
for (const u of ['/projects/destination-charging','/projects/chargebase','/projects/ijmondaanzet','/projects/bouw-pow','/projecten/chargebase']) { await p.goto('https://chargenet.energy'+u,{waitUntil:'networkidle'}); console.log(u, 'h1=', await p.locator('h1').first().innerText()); }
await b.close();
