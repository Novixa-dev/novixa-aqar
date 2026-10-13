import React from 'react';
import {C, FONT_AR, FONT_EN} from '../brand';

// Vector re-drawings of the official marks (from the Novixa / Aqar flyers).
// To use the official files instead, drop them in public/brand/ and swap the
// <svg> for <Img src={staticFile('brand/aqar-mark.svg')} />.

export const AqarMark: React.FC<{size: number; id?: string; progress?: number}> = ({
  size,
  id = 'am',
  progress = 1,
}) => {
  // progress 0..1 draws the house outline (used for logo reveals)
  const len = 330;
  return (
    <svg width={size} height={size} viewBox="0 0 100 100">
      <defs>
        <linearGradient id={`${id}-g`} x1="0" y1="0" x2="1" y2="1">
          <stop offset="0" stopColor="#1C5FF0" />
          <stop offset="0.55" stopColor="#2A8DF0" />
          <stop offset="1" stopColor="#1FCDA8" />
        </linearGradient>
        <linearGradient id={`${id}-w`} x1="0" y1="0" x2="1" y2="1">
          <stop offset="0" stopColor="#2F7BF5" />
          <stop offset="1" stopColor="#25A6E8" />
        </linearGradient>
      </defs>
      <path
        d="M17 93 L17 39 L50 11 L83 39 L83 83 L65 83"
        fill="none"
        stroke={`url(#${id}-g)`}
        strokeWidth={13}
        strokeLinejoin="miter"
        strokeDasharray={len}
        strokeDashoffset={len * (1 - progress)}
      />
      <g opacity={Math.max(0, (progress - 0.6) / 0.4)}>
        <rect x="40.5" y="47" width="8.5" height="8.5" rx="0.8" fill={`url(#${id}-w)`} />
        <rect x="51" y="47" width="8.5" height="8.5" rx="0.8" fill={`url(#${id}-w)`} />
        <rect x="40.5" y="57.5" width="8.5" height="8.5" rx="0.8" fill={`url(#${id}-w)`} />
        <rect x="51" y="57.5" width="8.5" height="8.5" rx="0.8" fill={`url(#${id}-w)`} />
      </g>
    </svg>
  );
};

export const AqarLockup: React.FC<{
  height: number;
  dark?: boolean; // dark background → white wordmark
  progress?: number;
}> = ({height, dark = true, progress = 1}) => {
  const color = dark ? C.white : C.ink;
  return (
    <div style={{display: 'flex', alignItems: 'center', gap: height * 0.22, direction: 'ltr'}}>
      <AqarMark size={height} progress={progress} />
      <div
        style={{
          fontFamily: FONT_EN,
          fontWeight: 700,
          fontSize: height * 0.82,
          color,
          letterSpacing: -height * 0.02,
          lineHeight: 1,
        }}
      >
        Aqar
      </div>
      <div style={{width: Math.max(2, height * 0.03), height: height * 0.62, background: dark ? 'rgba(255,255,255,.5)' : C.ink}} />
      <div style={{fontFamily: FONT_AR, fontWeight: 800, fontSize: height * 0.66, color, lineHeight: 1}}>عقار</div>
    </div>
  );
};

export const NovixaMark: React.FC<{size: number; id?: string}> = ({size, id = 'nm'}) => (
  <svg width={size} height={size} viewBox="0 0 100 100">
    <defs>
      <linearGradient id={`${id}-a`} x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stopColor="#2B7CF6" />
        <stop offset="1" stopColor="#1B4FD8" />
      </linearGradient>
      <linearGradient id={`${id}-b`} x1="0" y1="0" x2="1" y2="1">
        <stop offset="0" stopColor="#1E63E8" />
        <stop offset="1" stopColor="#1FC4B4" />
      </linearGradient>
      <linearGradient id={`${id}-c`} x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stopColor="#22D0B0" />
        <stop offset="1" stopColor="#14A9C8" />
      </linearGradient>
    </defs>
    {/* diagonal ribbon */}
    <polygon points="12,26 34,15 88,62 66,74" fill={`url(#${id}-b)`} />
    {/* left post */}
    <polygon points="12,26 34,15 34,84 12,96" fill={`url(#${id}-a)`} />
    {/* right post */}
    <polygon points="66,16 88,5 88,62 66,74" fill={`url(#${id}-c)`} />
    <polygon points="66,16 76,11 76,22 66,27" fill="#ffffff" opacity="0.18" />
  </svg>
);

export const NovixaLockup: React.FC<{height: number; dark?: boolean}> = ({height, dark = true}) => {
  const color = dark ? C.white : C.ink;
  return (
    <div style={{display: 'flex', alignItems: 'center', gap: height * 0.22, direction: 'ltr'}}>
      <NovixaMark size={height} />
      <div style={{fontFamily: FONT_EN, fontWeight: 600, fontSize: height * 0.78, color, letterSpacing: -height * 0.01, lineHeight: 1}}>
        Novixa
      </div>
      <div style={{width: Math.max(2, height * 0.03), height: height * 0.62, background: dark ? 'rgba(255,255,255,.5)' : C.ink}} />
      <div style={{fontFamily: FONT_AR, fontWeight: 700, fontSize: height * 0.62, color, lineHeight: 1}}>نوفيكسا</div>
    </div>
  );
};
