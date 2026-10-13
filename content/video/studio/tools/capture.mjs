// Captures real Aqar UI screenshots for the videos (Arabic RTL by default).
//
//   BASE=http://127.0.0.1:8000 node tools/capture.mjs            # all shots
//   BASE=https://aqar-demo.novixa.dev node tools/capture.mjs m-home d-admin
//
// Output: public/shots/<name>.jpg + src/shots.json (natural pixel sizes).
// Demo accounts: docs/TEST_USERS.md (password "password").
import {chromium} from 'playwright';
import fs from 'node:fs';
import path from 'node:path';

const BASE = process.env.BASE || 'http://127.0.0.1:8000';
const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname), '..');
const OUT = path.join(ROOT, 'public', 'shots');
const MANIFEST = path.join(ROOT, 'src', 'shots.json');
fs.mkdirSync(OUT, {recursive: true});

const MOBILE = {viewport: {width: 390, height: 844}, deviceScaleFactor: 2, isMobile: true, hasTouch: true};
const DESKTOP = {viewport: {width: 1440, height: 900}, deviceScaleFactor: 1.5};

const login = async (page, panel, email) => {
  await page.goto(`${BASE}/${panel}/login`, {waitUntil: 'networkidle'});
  await page.fill('input[type=email]', email);
  await page.fill('input[type=password]', 'password');
  await Promise.all([page.waitForURL((u) => !u.pathname.endsWith('/login'), {timeout: 30000}), page.click('button[type=submit]')]);
  await page.waitForLoadState('networkidle');
};

// name, device, path, options
//   full: full-page screenshot; theme: 'dark'; locale: 'en'; as: [panel, email]; prep: async (page) => {}
const firstProperty = async (page) => {
  const href = await page.evaluate(() => {
    const a = [...document.querySelectorAll('a[href*="/properties/"]')].find((x) => /\/properties\/\d+$/.test(x.getAttribute('href') || ''));
    return a ? a.getAttribute('href') : null;
  });
  return href;
};

const SHOTS = [
  // ---- public site, mobile ----
  ['m-home', MOBILE, '/', {full: true}],
  ['m-list', MOBILE, '/properties', {full: true}],
  ['m-detail', MOBILE, '@property', {full: true}],
  ['m-book', MOBILE, '@book', {full: true}],
  ['m-calc', MOBILE, '/calculators', {full: true}],
  ['m-home-dark', MOBILE, '/', {full: true, theme: 'dark'}],
  ['m-home-en', MOBILE, '/', {full: true, locale: 'en'}],
  ['m-submit', MOBILE, '/submit-property', {full: true}],
  ['m-chat', MOBILE, '/', {chat: true}],
  // ---- public site, desktop ----
  ['d-home', DESKTOP, '/', {full: true}],
  ['d-list', DESKTOP, '/properties', {full: true}],
  ['d-detail', DESKTOP, '@property', {full: true}],
  ['d-calc', DESKTOP, '/calculators', {full: true}],
  ['d-home-en', DESKTOP, '/', {locale: 'en'}],
  ['d-home-dark', DESKTOP, '/', {theme: 'dark'}],
  ['d-news', DESKTOP, '/news', {full: true}],
  ['d-request', DESKTOP, '/property-request', {full: true}],
  ['d-list-en', DESKTOP, '/properties', {full: true, locale: 'en'}],
  ['m-detail-en', MOBILE, '@property', {full: true, locale: 'en'}],
  ['d-admin-dark-en', DESKTOP, '/admin', {as: ['admin', 'admin@novixa.dev'], theme: 'dark', locale: 'en'}],
  // ---- role panels, desktop ----
  ['d-admin', DESKTOP, '/admin', {as: ['admin', 'admin@novixa.dev'], full: true}],
  ['d-admin-props', DESKTOP, '/admin/properties', {as: ['admin', 'admin@novixa.dev']}],
  ['d-admin-prop-new', DESKTOP, '/admin/properties/create', {as: ['admin', 'admin@novixa.dev'], full: true}],
  ['d-admin-users', DESKTOP, '/admin/users', {as: ['admin', 'admin@novixa.dev']}],
  ['d-admin-roles', DESKTOP, '/admin/shield/roles', {as: ['admin', 'admin@novixa.dev']}],
  ['d-admin-dark', DESKTOP, '/admin', {as: ['admin', 'admin@novixa.dev'], theme: 'dark'}],
  ['d-staff', DESKTOP, '/staff', {as: ['staff', 'staff@novixa.dev'], full: true}],
  ['d-agent', DESKTOP, '/agent', {as: ['agent', 'agent@novixa.dev'], full: true}],
  ['d-landlord', DESKTOP, '/landlord', {as: ['landlord', 'landlord@novixa.dev'], full: true}],
  ['d-landlord-leases', DESKTOP, '/landlord/leases', {as: ['landlord', 'landlord@novixa.dev']}],
  ['d-tenant', DESKTOP, '/tenant', {as: ['tenant', 'tenant@novixa.dev'], full: true}],
  ['d-tenant-maint', DESKTOP, '/tenant/maintenance-requests', {as: ['tenant', 'tenant@novixa.dev']}],
  ['d-tenant-maint-new', DESKTOP, '/tenant/maintenance-requests/create', {as: ['tenant', 'tenant@novixa.dev'], full: true}],
  ['d-buyer', DESKTOP, '/buyer', {as: ['buyer', 'buyer@novixa.dev'], full: true}],
  ['d-seller', DESKTOP, '/seller', {as: ['seller', 'seller@novixa.dev'], full: true}],
  ['d-contractor', DESKTOP, '/contractor', {as: ['contractor', 'contractor@novixa.dev'], full: true}],
  ['d-contractor-orders', DESKTOP, '/contractor/maintenance-requests', {as: ['contractor', 'contractor@novixa.dev']}],
  // ---- panels, mobile ----
  ['m-admin', MOBILE, '/admin', {as: ['admin', 'admin@novixa.dev'], full: true}],
  ['m-tenant', MOBILE, '/tenant', {as: ['tenant', 'tenant@novixa.dev'], full: true}],
];

// width/height straight from the JPEG SOF marker
const jpegSize = (b) => {
  let i = 2;
  while (i < b.length) {
    const marker = b[i + 1];
    const len = b.readUInt16BE(i + 2);
    if (marker >= 0xc0 && marker <= 0xc2) return [b.readUInt16BE(i + 7), b.readUInt16BE(i + 5)];
    i += 2 + len;
  }
  throw new Error('no SOF');
};

const only = process.argv.slice(2);
const manifest = fs.existsSync(MANIFEST) ? JSON.parse(fs.readFileSync(MANIFEST, 'utf8')) : {};
const browser = await chromium.launch();
let propertyHref = null;

for (const [name, device, p, opt] of SHOTS) {
  if (only.length && !only.includes(name)) continue;
  const ctx = await browser.newContext({...device, locale: opt.locale === 'en' ? 'en-US' : 'ar'});
  const page = await ctx.newPage();
  // ui-avatars.com renders initials avatars on the live site; answer locally so
  // offline/sandboxed captures look the same instead of showing broken images.
  await page.route('https://ui-avatars.com/**', (route) => {
    const u = new URL(route.request().url());
    const name = decodeURIComponent((u.searchParams.get('name') || '').replace(/\+/g, ' '));
    const initials = name.split(' ').filter(Boolean).map((w) => w[0]).join('').slice(0, 2);
    const bg = '#' + (u.searchParams.get('background') || '1F6FF2').replace('#', '');
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"><rect width="64" height="64" fill="${bg}"/><text x="32" y="41" font-size="26" font-family="sans-serif" fill="#fff" text-anchor="middle">${initials}</text></svg>`;
    return route.fulfill({status: 200, contentType: 'image/svg+xml', body: svg});
  });
  try {
    await page.addInitScript((theme) => {
      try {
        localStorage.setItem('theme', theme);
      } catch {}
    }, opt.theme === 'dark' ? 'dark' : 'light');
    await page.goto(`${BASE}/lang/${opt.locale === 'en' ? 'en' : 'ar'}`, {waitUntil: 'networkidle'});
    if (opt.as) await login(page, opt.as[0], opt.as[1]);
    let target = p;
    if (p === '@property' || p === '@book') {
      if (!propertyHref) {
        await page.goto(`${BASE}/properties`, {waitUntil: 'networkidle'});
        propertyHref = await firstProperty(page);
      }
      target = p === '@book' ? `${propertyHref}/book` : propertyHref;
    }
    await page.goto(target.startsWith('http') ? target : `${BASE}${target}`, {waitUntil: 'networkidle', timeout: 60000});
    if (opt.theme === 'dark') {
      await page.evaluate(() => document.documentElement.classList.add('dark'));
    }
    if (opt.chat) {
      await page.click('[x-data="chatbotWidget()"] button').catch(() => {});
      await page.waitForTimeout(800);
    } else {
      await page.addStyleTag({content: '[x-data="chatbotWidget()"]{display:none!important}'});
    }
    // let lazy images/maps/charts settle
    await page.evaluate(async () => {
      for (let y = 0; y < document.body.scrollHeight; y += 600) {
        window.scrollTo(0, y);
        await new Promise((r) => setTimeout(r, 120));
      }
      window.scrollTo(0, 0);
    });
    await page.waitForTimeout(1200);
    const file = path.join(OUT, `${name}.jpg`);
    await page.screenshot({path: file, fullPage: !!opt.full, type: 'jpeg', quality: 86});
    manifest[name] = jpegSize(fs.readFileSync(file));
    console.log('ok  ', name, page.url(), manifest[name].join('x'));
  } catch (e) {
    console.log('FAIL', name, String(e).split('\n')[0]);
  }
  await ctx.close();
}
await browser.close();
fs.writeFileSync(MANIFEST, JSON.stringify(manifest, null, 2) + '\n');
