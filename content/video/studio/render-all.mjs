// Renders every composition (or the ids given) to out/videos/<id>.mp4 and a cover to out/covers/<id>.jpg
//   REMOTION_CHROME=/path/to/headless_shell node render-all.mjs [ids...]
import {bundle} from '@remotion/bundler';
import {getCompositions, renderMedia, renderStill} from '@remotion/renderer';
import fs from 'node:fs';
import path from 'node:path';

const ROOT = path.dirname(new URL(import.meta.url).pathname);
const browserExecutable = process.env.REMOTION_CHROME || null;
const only = process.argv.slice(2);
fs.mkdirSync(path.join(ROOT, 'out', 'videos'), {recursive: true});
fs.mkdirSync(path.join(ROOT, 'out', 'covers'), {recursive: true});

const serveUrl = await bundle({entryPoint: path.join(ROOT, 'src', 'index.ts')});
const comps = await getCompositions(serveUrl, {browserExecutable});

for (const composition of comps) {
  if (only.length && !only.includes(composition.id)) continue;
  const t = Date.now();
  await renderMedia({
    composition,
    serveUrl,
    codec: 'h264',
    crf: 20,
    pixelFormat: 'yuv420p',
    audioBitrate: '192k',
    imageFormat: 'jpeg',
    jpegQuality: 92,
    outputLocation: path.join(ROOT, 'out', 'videos', `${composition.id}.mp4`),
    browserExecutable,
    concurrency: Number(process.env.CONCURRENCY || 4),
  });
  // Cover = the hook frame (fully revealed headline) — use as the post thumbnail.
  await renderStill({composition, serveUrl, frame: 50, output: path.join(ROOT, 'out', 'covers', `${composition.id}.jpg`), imageFormat: 'jpeg', jpegQuality: 90, browserExecutable});
  console.log(`done ${composition.id} ${(composition.durationInFrames / composition.fps).toFixed(1)}s in ${((Date.now() - t) / 1000).toFixed(0)}s`);
}
