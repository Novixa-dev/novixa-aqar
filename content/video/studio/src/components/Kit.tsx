import React from 'react';
import {
  AbsoluteFill,
  Easing,
  Img,
  interpolate,
  spring,
  staticFile,
  useCurrentFrame,
  useVideoConfig,
} from 'remotion';
import {C, FACTS, FONT_AR, FONT_EN, GRAD} from '../brand';
import {Icon, IconName, WhatsAppGlyph} from './Icons';
import {AqarLockup} from './Logos';

export const clamp = {extrapolateLeft: 'clamp', extrapolateRight: 'clamp'} as const;

export const useSpring = (delay = 0, damping = 16, mass = 0.8) => {
  const frame = useCurrentFrame();
  const {fps} = useVideoConfig();
  return spring({frame: frame - delay, fps, config: {damping, mass}});
};

/* ---------- Background: deep navy, soft brand glows, faint blueprint grid ---------- */
export const Bg: React.FC<{light?: boolean; grid?: boolean}> = ({light = false, grid = true}) => {
  const f = useCurrentFrame();
  const {width, height} = useVideoConfig();
  const drift = Math.sin(f / 90) * 60;
  const base = light
    ? `linear-gradient(180deg, ${C.light} 0%, #E6EEFA 100%)`
    : `radial-gradient(120% 80% at 50% 0%, ${C.navy800} 0%, ${C.navy900} 45%, ${C.navy950} 100%)`;
  return (
    <AbsoluteFill style={{background: base, overflow: 'hidden'}}>
      <div
        style={{
          position: 'absolute',
          width: width * 0.9,
          height: width * 0.9,
          left: -width * 0.25 + drift,
          top: height * 0.08,
          borderRadius: '50%',
          background: `radial-gradient(circle, ${C.blue}55 0%, transparent 65%)`,
          filter: 'blur(40px)',
        }}
      />
      <div
        style={{
          position: 'absolute',
          width: width * 0.8,
          height: width * 0.8,
          right: -width * 0.3 - drift,
          bottom: height * 0.05,
          borderRadius: '50%',
          background: `radial-gradient(circle, ${C.teal}40 0%, transparent 65%)`,
          filter: 'blur(40px)',
        }}
      />
      {grid ? (
        <AbsoluteFill
          style={{
            backgroundImage: `linear-gradient(${light ? '#0B1F4D10' : '#ffffff0b'} 1px, transparent 1px), linear-gradient(90deg, ${light ? '#0B1F4D10' : '#ffffff0b'} 1px, transparent 1px)`,
            backgroundSize: '72px 72px',
            backgroundPosition: `${(f * 0.4) % 72}px ${(f * 0.4) % 72}px`,
            maskImage: 'radial-gradient(ellipse at center, black 30%, transparent 80%)',
          }}
        />
      ) : null}
    </AbsoluteFill>
  );
};

/* ---------- Kinetic text: word-by-word (Arabic letters must stay joined) ---------- */
export const Words: React.FC<{
  text: string; // wrap words in *stars* to highlight
  size: number;
  delay?: number;
  stagger?: number;
  color?: string;
  hl?: string;
  weight?: number;
  en?: boolean;
  maxWidth?: number;
  lineHeight?: number;
  align?: 'center' | 'start';
  out?: number; // frame (relative) at which to fade out
}> = ({
  text,
  size,
  delay = 0,
  stagger = 3,
  color = C.white,
  hl = C.teal,
  weight = 800,
  en = false,
  maxWidth,
  lineHeight = 1.35,
  align = 'center',
  out,
}) => {
  const frame = useCurrentFrame();
  const {fps} = useVideoConfig();
  const tokens = text.split(/\s+/).filter(Boolean);
  const outV = out === undefined ? 1 : interpolate(frame, [out, out + 8], [1, 0], clamp);
  return (
    <div
      style={{
        direction: en ? 'ltr' : 'rtl',
        display: 'flex',
        flexWrap: 'wrap',
        justifyContent: align === 'center' ? 'center' : 'flex-start',
        columnGap: size * 0.28,
        maxWidth,
        fontFamily: en ? FONT_EN : FONT_AR,
        fontWeight: weight,
        fontSize: size,
        lineHeight,
        opacity: outV,
        textAlign: align === 'center' ? 'center' : en ? 'left' : 'right',
      }}
    >
      {tokens.map((t, i) => {
        if (t === '/') return <div key={i} style={{flexBasis: '100%', height: 0}} />;
        const isHl = t.startsWith('*') && t.endsWith('*');
        const word = isHl ? t.slice(1, -1) : t;
        const s = spring({frame: frame - delay - i * stagger, fps, config: {damping: 14, mass: 0.7}});
        return (
          <span
            key={i}
            style={{
              display: 'inline-block',
              color: isHl ? hl : color,
              opacity: s,
              transform: `translateY(${(1 - s) * size * 0.6}px) scale(${0.9 + 0.1 * s})`,
              filter: `blur(${(1 - s) * 10}px)`,
            }}
          >
            {word.replace(/_/g, ' ')}
          </span>
        );
      })}
    </div>
  );
};

/* ---------- Pill chip ---------- */
export const Chip: React.FC<{
  icon?: IconName;
  label: string;
  size?: number;
  delay?: number;
  tone?: 'glass' | 'brand' | 'white';
  en?: boolean;
}> = ({icon, label, size = 40, delay = 0, tone = 'glass', en = false}) => {
  const s = useSpring(delay, 13);
  const bg =
    tone === 'brand' ? GRAD : tone === 'white' ? C.white : 'rgba(255,255,255,0.09)';
  const fg = tone === 'white' ? C.ink : C.white;
  return (
    <div
      style={{
        display: 'inline-flex',
        alignItems: 'center',
        gap: size * 0.4,
        direction: en ? 'ltr' : 'rtl',
        padding: `${size * 0.36}px ${size * 0.6}px`,
        borderRadius: size,
        background: bg,
        border: tone === 'glass' ? '1.5px solid rgba(255,255,255,0.18)' : 'none',
        boxShadow: tone === 'glass' ? 'none' : '0 12px 30px rgba(0,0,0,.25)',
        backdropFilter: 'blur(8px)',
        color: fg,
        fontFamily: en ? FONT_EN : FONT_AR,
        fontWeight: 700,
        fontSize: size,
        lineHeight: 1.2,
        opacity: s,
        transform: `translateY(${(1 - s) * 30}px) scale(${0.85 + 0.15 * s})`,
        whiteSpace: 'nowrap',
      }}
    >
      {icon ? <Icon name={icon} size={size * 1.05} color={tone === 'white' ? C.blue : C.teal} stroke={2.2} /> : null}
      <span>{label}</span>
    </div>
  );
};

/* ---------- Honest footage tag ---------- */
export const RealTag: React.FC<{en?: boolean; size?: number}> = ({en = false, size = 26}) => (
  <div
    style={{
      display: 'inline-flex',
      alignItems: 'center',
      gap: 10,
      padding: `${size * 0.3}px ${size * 0.7}px`,
      borderRadius: 100,
      background: 'rgba(3,18,47,.72)',
      border: '1px solid rgba(255,255,255,.18)',
      color: C.white,
      fontFamily: en ? FONT_EN : FONT_AR,
      fontWeight: 600,
      fontSize: size,
      direction: en ? 'ltr' : 'rtl',
    }}
  >
    <span style={{width: size * 0.4, height: size * 0.4, borderRadius: '50%', background: '#FF4D5E'}} />
    {en ? 'Real product footage · demo data' : 'لقطات حقيقية من النظام · بيانات تجريبية'}
  </div>
);

/* ---------- Scrolling screenshot (the workhorse for UI footage) ---------- */
export type Stop = [frame: number, scroll: number]; // scroll: 0..1 of max scroll, or (>1) a top offset in image pixels
export type ZoomStop = [frame: number, scale: number, ox: number, oy: number]; // origin 0..1

export const Shot: React.FC<{
  src: string;
  w: number; // viewport width (px)
  h: number; // viewport height (px)
  img: [number, number]; // natural size
  stops?: Stop[];
  zoom?: ZoomStop[];
  delay?: number;
  bg?: string;
}> = ({src, w, h, img, stops = [[0, 0]], zoom, delay = 0, bg = '#fff'}) => {
  const frame = useCurrentFrame() - delay;
  const renderedH = (w * img[1]) / img[0];
  const maxScroll = Math.max(0, renderedH - h);
  const fr = stops.map((s) => s[0]);
  const k = w / img[0];
  const sc = stops.map((s) => (s[1] > 1 ? Math.min(1, (s[1] * k) / (maxScroll || 1)) : s[1]));
  const y =
    stops.length > 1
      ? interpolate(frame, fr, sc, {...clamp, easing: Easing.inOut(Easing.cubic)})
      : sc[0];
  let transform = '';
  let origin = '50% 50%';
  if (zoom && zoom.length) {
    const zf = zoom.map((z) => z[0]);
    const opts = {...clamp, easing: Easing.inOut(Easing.cubic)};
    const s = zoom.length > 1 ? interpolate(frame, zf, zoom.map((z) => z[1]), opts) : zoom[0][1];
    const ox = zoom.length > 1 ? interpolate(frame, zf, zoom.map((z) => z[2]), opts) : zoom[0][2];
    const oy = zoom.length > 1 ? interpolate(frame, zf, zoom.map((z) => z[3]), opts) : zoom[0][3];
    transform = `scale(${s})`;
    origin = `${ox * 100}% ${oy * 100}%`;
  }
  return (
    <div style={{width: w, height: h, overflow: 'hidden', position: 'relative', background: bg}}>
      <div style={{width: w, height: h, transform, transformOrigin: origin}}>
        <Img
          src={staticFile(src)}
          style={{width: w, height: renderedH, display: 'block', transform: `translateY(${-y * maxScroll}px)`}}
        />
      </div>
    </div>
  );
};

/* ---------- Device frames ---------- */
export const Phone: React.FC<{w: number; children: React.ReactNode; tilt?: number; style?: React.CSSProperties}> = ({
  w,
  children,
  tilt = 0,
  style,
}) => {
  const h = w * 2.08;
  const bez = w * 0.035;
  return (
    <div
      style={{
        width: w,
        height: h,
        borderRadius: w * 0.14,
        background: '#0A0F1C',
        padding: bez,
        boxShadow: '0 40px 90px rgba(0,0,0,.55), 0 0 0 2px #2A3550 inset',
        transform: `perspective(2000px) rotateY(${tilt}deg)`,
        position: 'relative',
        ...style,
      }}
    >
      <div style={{width: '100%', height: '100%', borderRadius: w * 0.11, overflow: 'hidden', position: 'relative', background: '#fff'}}>
        {children}
        <div
          style={{
            position: 'absolute',
            top: w * 0.025,
            left: '50%',
            transform: 'translateX(-50%)',
            width: w * 0.3,
            height: w * 0.075,
            borderRadius: 100,
            background: '#0A0F1C',
          }}
        />
      </div>
    </div>
  );
};

export const phoneScreen = (w: number) => {
  const bez = w * 0.035;
  return {sw: w - bez * 2, sh: w * 2.08 - bez * 2};
};

export const Browser: React.FC<{w: number; h: number; url?: string; children: React.ReactNode; style?: React.CSSProperties; dark?: boolean}> = ({
  w,
  h,
  url = FACTS.demoUrl,
  children,
  style,
  dark = false,
}) => {
  const bar = Math.max(34, w * 0.035);
  return (
    <div
      style={{
        width: w,
        height: h + bar,
        borderRadius: w * 0.014,
        overflow: 'hidden',
        background: dark ? '#1b2433' : '#E9EEF6',
        boxShadow: '0 40px 100px rgba(0,0,0,.5)',
        ...style,
      }}
    >
      <div style={{height: bar, display: 'flex', alignItems: 'center', gap: bar * 0.22, padding: `0 ${bar * 0.4}px`, direction: 'ltr'}}>
        {['#FF5F57', '#FEBC2E', '#28C840'].map((c) => (
          <div key={c} style={{width: bar * 0.3, height: bar * 0.3, borderRadius: '50%', background: c}} />
        ))}
        <div
          style={{
            marginLeft: bar * 0.4,
            flex: 1,
            height: bar * 0.62,
            borderRadius: bar,
            background: dark ? '#0f1622' : '#fff',
            display: 'flex',
            alignItems: 'center',
            padding: `0 ${bar * 0.4}px`,
            fontFamily: FONT_EN,
            fontSize: bar * 0.36,
            color: dark ? '#9fb3d9' : '#475569',
          }}
        >
          🔒&nbsp;{url}
        </div>
      </div>
      <div style={{width: w, height: h, overflow: 'hidden'}}>{children}</div>
    </div>
  );
};

/* ---------- Tap ripple (shows an interaction on footage) ---------- */
export const Tap: React.FC<{x: number; y: number; at: number; size?: number}> = ({x, y, at, size = 70}) => {
  const f = useCurrentFrame() - at;
  if (f < -6 || f > 22) return null;
  const pre = interpolate(f, [-6, 0], [0, 1], clamp);
  const r = interpolate(f, [0, 20], [0.4, 1.6], clamp);
  const o = interpolate(f, [0, 20], [0.9, 0], clamp);
  return (
    <>
      <div
        style={{
          position: 'absolute',
          left: x - size / 2,
          top: y - size / 2,
          width: size,
          height: size,
          borderRadius: '50%',
          background: 'rgba(31,111,242,.35)',
          border: '3px solid rgba(255,255,255,.9)',
          opacity: f < 0 ? pre : o,
          transform: `scale(${f < 0 ? 0.6 : r})`,
        }}
      />
    </>
  );
};

/* ---------- Scene transition helper: fade+lift in, fade out ---------- */
export const Scene: React.FC<{dur: number; children: React.ReactNode; fadeIn?: number; fadeOut?: number}> = ({
  dur,
  children,
  fadeIn = 8,
  fadeOut = 8,
}) => {
  const f = useCurrentFrame();
  const o = Math.min(
    fadeIn ? interpolate(f, [0, fadeIn], [0, 1], clamp) : 1,
    fadeOut ? interpolate(f, [dur - fadeOut, dur], [1, 0], clamp) : 1,
  );
  const s = interpolate(f, [0, fadeIn || 1], [1.03, 1], clamp);
  return <AbsoluteFill style={{opacity: o, transform: `scale(${s})`}}>{children}</AbsoluteFill>;
};

/* ---------- End card ---------- */
export const EndCard: React.FC<{
  headline?: string;
  sub?: string;
  en?: boolean;
  showPrice?: boolean;
  scale?: number;
}> = ({headline, sub, en = false, showPrice = true, scale = 1}) => {
  const k = scale;
  const logo = useSpring(0, 14);
  const lp = interpolate(useCurrentFrame(), [0, 26], [0, 1], {...clamp, easing: Easing.out(Easing.cubic)});
  return (
    <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 42 * k, padding: 60 * k}}>
      <div style={{transform: `scale(${0.8 + 0.2 * logo})`, opacity: logo}}>
        <AqarLockup height={120 * k} progress={lp} />
      </div>
      <Words
        text={headline ?? (en ? 'Try the live demo today' : 'جرّب الديمو الآن بنفسك')}
        size={64 * k}
        delay={10}
        en={en}
        maxWidth={900 * k}
      />
      {sub ? <Words text={sub} size={36 * k} weight={600} delay={16} color={C.muted} en={en} maxWidth={900 * k} /> : null}
      <div style={{display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 22 * k}}>
        <Chip icon="globe" label={FACTS.demoUrl} size={40 * k} delay={22} tone="white" en />
        <WhatsPill delay={28} k={k} />
        {showPrice ? (
          <Chip icon="tag" label={en ? `License from ${FACTS.priceFrom} — no monthly fee` : `ترخيص يبدأ من ${FACTS.priceFrom} — بدون اشتراك شهري`} size={32 * k} delay={34} en={en} />
        ) : null}
      </div>
    </AbsoluteFill>
  );
};

export const WhatsPill: React.FC<{delay?: number; k?: number}> = ({delay = 0, k = 1}) => {
  const s = useSpring(delay, 13);
  return (
    <div
      style={{
        display: 'inline-flex',
        alignItems: 'center',
        gap: 16 * k,
        padding: `${16 * k}px ${34 * k}px`,
        borderRadius: 100,
        background: C.whatsapp,
        color: '#fff',
        fontFamily: FONT_EN,
        fontWeight: 700,
        fontSize: 42 * k,
        direction: 'ltr',
        boxShadow: '0 16px 40px rgba(37,211,102,.35)',
        opacity: s,
        transform: `translateY(${(1 - s) * 30}px) scale(${0.85 + 0.15 * s})`,
      }}
    >
      <WhatsAppGlyph size={46 * k} />
      {FACTS.whatsapp}
    </div>
  );
};

/* ---------- Persistent small brand bug (top corner) ---------- */
export const Bug: React.FC<{top?: number}> = ({top = 90}) => (
  <div style={{position: 'absolute', top, left: 0, right: 0, display: 'flex', justifyContent: 'center', opacity: 0.95}}>
    <AqarLockup height={58} />
  </div>
);

/* ---------- Progress bar along the bottom (retention cue) ---------- */
export const Progress: React.FC<{color?: string}> = ({color = C.teal}) => {
  const f = useCurrentFrame();
  const {durationInFrames, width} = useVideoConfig();
  return <div style={{position: 'absolute', top: 0, right: 0, height: 8, width: (f / durationInFrames) * width, background: color}} />;
};

/* ---------- Simple appear wrapper (uses the Sequence-relative frame) ---------- */
export const Appear: React.FC<{delay?: number; children: React.ReactNode; style?: React.CSSProperties; lift?: number}> = ({
  delay = 0,
  children,
  style,
  lift = 30,
}) => {
  const s = useSpring(delay, 14);
  return <div style={{opacity: s, transform: `translateY(${(1 - s) * lift}px)`, ...style}}>{children}</div>;
};
