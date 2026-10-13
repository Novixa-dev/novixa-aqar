// Novixa Aqar brand tokens, sampled from the official Aqar / Novixa flyers.
export const C = {
  navy950: '#03122F',
  navy900: '#061E4A',
  navy800: '#0B2A63',
  navy700: '#123A80',
  ink: '#0B1F4D',
  blue: '#1F6FF2',
  blueSoft: '#3B8BFF',
  teal: '#19C3B1',
  tealDeep: '#0CB0A5',
  whatsapp: '#25D366',
  gold: '#F5B83D',
  light: '#F3F6FB',
  white: '#FFFFFF',
  muted: '#9FB3D9',
};

export const GRAD = `linear-gradient(135deg, ${C.blue} 0%, ${C.teal} 100%)`;

export const FONT_AR = 'Cairo, Tajawal, sans-serif';
export const FONT_EN = 'Poppins, Cairo, sans-serif';

// Facts used across videos. Only claims the product actually supports
// (see content/research/FEATURE_REALITY_CLASSIFICATION.md).
export const FACTS = {
  demoUrl: 'aqar-demo.novixa.dev',
  site: 'novixa.dev',
  whatsapp: '+967 772 025 992',
  priceFrom: '\u2066$500\u2069', // LTR isolate so it reads "$500" inside Arabic text
  handle: '@novixa49',
};

export const FPS = 30;
