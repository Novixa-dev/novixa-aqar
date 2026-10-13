// Storyboard preview: renders N evenly spaced frames per composition into one contact sheet.
//   node tools/preview.mjs AQ01-Hero-9x16 [AQ02-Roles-9x16 ...]   (N via PREVIEW_N, default 12)
import {bundle} from '@remotion/bundler';
import {renderStill, selectComposition} from '@remotion/renderer';
import {execFileSync} from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';

const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname), '..');
const OUT = path.join(ROOT, 'out', 'preview');
fs.mkdirSync(OUT, {recursive: true});
const browserExecutable = process.env.REMOTION_CHROME || null;
const N = Number(process.env.PREVIEW_N || 12);
const serveUrl = await bundle({entryPoint: path.join(ROOT, 'src', 'index.ts')});

for (const id of process.argv.slice(2)) {
  const composition = await selectComposition({serveUrl, id, browserExecutable});
  const files = [];
  for (let i = 0; i < N; i++) {
    const frame = Math.min(composition.durationInFrames - 1, Math.round(((i + 0.6) / N) * composition.durationInFrames));
    const output = path.join(OUT, `${id}-${String(i).padStart(2, '0')}.png`);
    await renderStill({composition, serveUrl, output, frame, browserExecutable, scale: 0.35});
    files.push(output);
  }
  const cols = composition.width > composition.height ? 3 : 6;
  const sheet = path.join(OUT, `${id}.jpg`);
  execFileSync('ffmpeg', ['-y', '-loglevel', 'error', ...files.flatMap((f) => ['-i', f]),
    '-filter_complex', `xstack=inputs=${files.length}:layout=${files.map((_, i) => `${i % cols === 0 ? '0' : Array.from({length: i % cols}, () => 'w0').join('+')}_${Math.floor(i / cols) === 0 ? '0' : Array.from({length: Math.floor(i / cols)}, () => 'h0').join('+')}`).join('|')}`,
    sheet]);
  files.forEach((f) => fs.unlinkSync(f));
  console.log('sheet', sheet);
}
