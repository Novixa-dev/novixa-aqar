# Aqar & Novixa — Marketing Videos

Finished videos, the code that makes them, and how to publish them.

| Folder | What's inside |
|---|---|
| `exports/` | **Final MP4s** (H.264/AAC, 30 fps, platform sizes) — upload these |
| `exports/covers/` | Thumbnail/cover image per video |
| `PLAYBOOK.md` | What each video is for, captions AR/EN, hashtags, schedule, ads plan, hook tests |
| `vo/` | Voice-over scripts timed to each video (+ free TTS recipe in the playbook) |
| `brand/` | Logo crops taken from the official flyers (reference only) |
| `studio/` | The video source: a [Remotion](https://www.remotion.dev) project (React → MP4) |

## Why it's built this way

- **Real product, not mock-ups.** Every UI shot is a real screenshot of Aqar,
  captured automatically from the seeded demo (`studio/tools/capture.mjs`,
  Playwright, Arabic RTL, phone 390×844@2x and desktop 1440×900@1.5x) and
  animated with scrolls, zooms and tap markers. Re-capture after any UI change
  and every video updates.
- **Videos as code.** Each video is a short list of scenes in
  `studio/src/videos/Aqar.tsx`; one scene library (`studio/src/components/`)
  gives a consistent identity: navy/blue/teal brand colours, Cairo + Poppins
  type, the Aqar house mark. Changing a hook, a price or the WhatsApp number
  (`studio/src/brand.ts`) and re-rendering takes minutes, in every size.
- **Own audio.** The music beds and SFX are synthesised from scratch
  (`studio/tools/music.py`) — no licence risk on ads. On TikTok/Reels you can
  also add a trending sound in-app at low volume.
- **Honest.** Footage carries "لقطات حقيقية من النظام · بيانات تجريبية"; claims
  are limited to `content/research/FEATURE_REALITY_CLASSIFICATION.md`.

## Re-render (any machine with Node 20+ and Chrome/Chromium)

```bash
cd content/video/studio
npm install
npm run studio                       # live preview/editor in the browser
node render-all.mjs                  # all videos → out/videos + out/covers
node render-all.mjs AQ05-Offer-4x5   # just one
node tools/preview.mjs AQ01-Hero-9x16   # storyboard contact sheet
```

If Remotion can't download its own headless browser, point it at an installed one:
`REMOTION_CHROME=/path/to/chrome node render-all.mjs`.

Re-capture the UI (app running locally or against the live demo):

```bash
BASE=https://aqar-demo.novixa.dev node tools/capture.mjs        # all shots
BASE=http://127.0.0.1:8000 node tools/capture.mjs m-home d-admin  # some
python3 tools/music.py                                           # regenerate music
```

### Using the official logo files
The marks in `studio/src/components/Logos.tsx` are faithful vector redraws of
the flyer logos. If you have the original SVG/PNG files, put them in
`studio/public/brand/` and swap the `<svg>` for `<Img src={staticFile('brand/…')} />`.

### Licence note
Remotion is free for individuals and companies with up to 3 employees; larger
teams need a company licence (remotion.pro). Fonts: Cairo, Poppins (SIL OFL).
Icons: Lucide-style paths (ISC).

## Compositions

| ID | Size | Length |
|---|---|---|
| AQ01-Hero-9x16 / -4x5 / -16x9 | 1080×1920 / 1080×1350 / 1920×1080 | 36 s |
| AQ01EN-Hero-16x9 / -1x1 | 1920×1080 / 1080×1080 | 36 s |
| AQ02-Roles-9x16 | 1080×1920 | 34 s |
| AQ03-ClientJourney-9x16 | 1080×1920 | 29 s |
| AQ04-Maintenance-9x16 | 1080×1920 | 25 s |
| AQ05-Offer-9x16 / -4x5 | 1080×1920 / 1080×1350 | 18 s |
| AQ06-Insight-9x16 | 1080×1920 | 23 s |
| AQ07-Marketers-9x16 | 1080×1920 | 13 s |
| AQ08-Explainer-16x9 | 1920×1080 | 62 s |
| AQ09-TryIt-9x16 | 1080×1920 | 21 s |
| NX01-Novixa-9x16 / -16x9 | 1080×1920 / 1920×1080 | 29 s |
