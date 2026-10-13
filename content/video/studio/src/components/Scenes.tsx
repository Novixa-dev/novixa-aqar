import React from 'react';
import {AbsoluteFill, Audio, Easing, interpolate, Sequence, staticFile, useCurrentFrame, useVideoConfig} from 'remotion';
import {C, FONT_AR, FONT_EN} from '../brand';
import shots from '../shots.json';
import {Icon, IconName} from './Icons';
import {Appear, Browser, Chip, clamp, EndCard, Phone, phoneScreen, RealTag, Scene, Shot, Stop, Tap, useSpring, Words, ZoomStop} from './Kit';
import {AqarMark} from './Logos';
import {ROLES, RoleCard} from './Roles';

export const useLayout = () => {
  const {width, height} = useVideoConfig();
  const v = height / width > 1.5; // 9:16
  const tall = height / width > 1.1; // 4:5 / 9:16
  const land = width > height;
  const k = Math.min(width, height) / 1080;
  return {width, height, v, tall, land, k};
};

const size = (name: string) => (shots as unknown as Record<string, [number, number]>)[name];

/* A timeline is a list of [element, durationInFrames]. */
export type Beat = [React.ReactNode, number];
export const total = (beats: Beat[]) => beats.reduce((a, b) => a + b[1], 0);

export const Timeline: React.FC<{beats: Beat[]; whoosh?: boolean}> = ({beats, whoosh = true}) => {
  let t = 0;
  return (
    <>
      {beats.map(([el, d], i) => {
        const from = t;
        t += d;
        return (
          <Sequence key={i} from={from} durationInFrames={d}>
            {whoosh && i > 0 ? <Audio src={staticFile('audio/whoosh.mp3')} volume={0.25} /> : null}
            <Scene dur={d} fadeIn={i === 0 ? 0 : 8} fadeOut={i === beats.length - 1 ? 0 : 8}>
              {el}
            </Scene>
          </Sequence>
        );
      })}
    </>
  );
};

/* ---------- Big hook: a question in 1–2 lines ---------- */
export const Hook: React.FC<{lines: string[]; en?: boolean; sub?: string}> = ({lines, en, sub}) => {
  const {k, v} = useLayout();
  return (
    <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 20 * k, padding: 70 * k}}>
      {lines.map((l, i) => (
        <Words key={i} text={l} size={(v ? 104 : 92) * k} delay={i * 10} en={en} maxWidth={980 * k} weight={900} lineHeight={1.25} />
      ))}
      {sub ? <Words text={sub} size={40 * k} weight={600} delay={lines.length * 10 + 6} color={C.muted} en={en} maxWidth={900 * k} /> : null}
    </AbsoluteFill>
  );
};

/* ---------- Pain list: the scattered way of working (struck through) ---------- */
export const Pain: React.FC<{title: string; items: [IconName, string][]; en?: boolean}> = ({title, items, en}) => {
  const {k, land} = useLayout();
  return (
    <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 40 * k, padding: 70 * k}}>
      <Words text={title} size={64 * k} en={en} maxWidth={1000 * k} />
      <div style={{display: 'flex', flexDirection: land ? 'row' : 'column', flexWrap: 'wrap', justifyContent: 'center', gap: 22 * k}}>
        {items.map(([icon, label], i) => (
          <PainRow key={i} icon={icon} label={label} delay={12 + i * 9} k={k} en={en} />
        ))}
      </div>
    </AbsoluteFill>
  );
};

const PainRow: React.FC<{icon: IconName; label: string; delay: number; k: number; en?: boolean}> = ({icon, label, delay, k, en}) => {
  const s = useSpring(delay, 14);
  const f = useCurrentFrame();
  const strike = interpolate(f, [delay + 14, delay + 24], [0, 1], clamp);
  return (
    <div
      style={{
        display: 'flex',
        alignItems: 'center',
        gap: 22 * k,
        direction: en ? 'ltr' : 'rtl',
        padding: `${20 * k}px ${34 * k}px`,
        borderRadius: 24 * k,
        background: 'rgba(255,77,94,.08)',
        border: '1.5px solid rgba(255,77,94,.35)',
        minWidth: 640 * k,
        opacity: s,
        transform: `translateX(${(1 - s) * (en ? -60 : 60)}px)`,
      }}
    >
      <Icon name={icon} size={52 * k} color="#FF8A95" />
      <div style={{position: 'relative', fontFamily: en ? FONT_EN : FONT_AR, fontWeight: 700, fontSize: 44 * k, color: '#fff', flex: 1}}>
        {label}
        <div style={{position: 'absolute', top: '55%', right: 0, left: 0, height: 4 * k, background: '#FF4D5E', transform: `scaleX(${strike})`, transformOrigin: en ? 'left' : 'right'}} />
      </div>
      <div style={{opacity: strike}}>
        <Icon name="x" size={44 * k} color="#FF4D5E" stroke={3} />
      </div>
    </div>
  );
};

/* ---------- Reveal: the logo draws itself, then the promise ---------- */
export const Reveal: React.FC<{text: string; sub?: string; en?: boolean}> = ({text, sub, en}) => {
  const {k} = useLayout();
  const f = useCurrentFrame();
  const p = interpolate(f, [0, 30], [0, 1], {...clamp, easing: Easing.out(Easing.cubic)});
  const glow = interpolate(f, [20, 40], [0, 1], clamp);
  return (
    <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 50 * k, padding: 70 * k}}>
      <Audio src={staticFile('audio/chime.mp3')} volume={0.45} />
      <div style={{filter: `drop-shadow(0 0 ${60 * glow * k}px ${C.teal}88)`}}>
        <AqarMark size={300 * k} progress={p} id="rv" />
      </div>
      <Words text={text} size={84 * k} delay={22} en={en} maxWidth={1000 * k} weight={900} />
      {sub ? <Words text={sub} size={42 * k} weight={600} delay={34} color={C.muted} en={en} maxWidth={900 * k} /> : null}
    </AbsoluteFill>
  );
};

/* ---------- Phone demo: caption + phone with scrolling real screenshot ---------- */
export const PhoneDemo: React.FC<{
  shot: string;
  caption: string;
  sub?: string;
  stops?: Stop[];
  zoom?: ZoomStop[];
  taps?: [x: number, y: number, at: number][]; // x,y as fraction of the phone screen
  en?: boolean;
  step?: string; // e.g. "1"
}> = ({shot, caption, sub, stops, zoom, taps = [], en, step}) => {
  const {k, land, v, height} = useLayout();
  const pw = land ? 470 * (height / 1080) : v ? 630 * k : 440 * k;
  const {sw, sh} = phoneScreen(pw);
  const enter = useSpring(4, 15);
  const text = (
    <div style={{display: 'flex', flexDirection: 'column', alignItems: land ? (en ? 'flex-start' : 'flex-end') : 'center', gap: 18 * k, maxWidth: land ? 820 * k : 980 * k}}>
      {step ? <Chip label={step} size={34 * k} tone="brand" en={en} /> : null}
      <Words text={caption} size={(land ? 74 : 66) * k} en={en} align={land ? 'start' : 'center'} maxWidth={land ? 800 * k : 980 * k} />
      {sub ? <Words text={sub} size={36 * k} weight={600} color={C.muted} delay={10} en={en} align={land ? 'start' : 'center'} maxWidth={land ? 760 * k : 900 * k} /> : null}
    </div>
  );
  return (
    <AbsoluteFill
      style={{
        alignItems: 'center',
        justifyContent: 'center',
        flexDirection: land ? (en ? 'row' : 'row-reverse') : 'column',
        gap: (land ? 120 : 34) * k,
        paddingBottom: land ? 0 : v ? 90 * k : 30 * k,
      }}
    >
      {text}
      <div style={{position: 'relative', transform: `translateY(${(1 - enter) * 300}px)`, opacity: enter}}>
        <Phone w={pw}>
          <Shot src={`shots/${shot}.jpg`} w={sw} h={sh} img={size(shot)} stops={stops} zoom={zoom} />
          {taps.map(([x, y, at], i) => (
            <Tap key={i} x={x * sw} y={y * sh} at={at} size={80 * k} />
          ))}
        </Phone>
        <div style={{position: 'absolute', bottom: -28 * k, left: 0, right: 0, display: 'flex', justifyContent: 'center'}}>
          <RealTag en={en} size={24 * k} />
        </div>
      </div>
    </AbsoluteFill>
  );
};

/* ---------- Desktop demo: caption + browser window with real screenshot ---------- */
export const DesktopDemo: React.FC<{
  shot: string;
  caption: string;
  sub?: string;
  stops?: Stop[];
  zoom?: ZoomStop[];
  en?: boolean;
  url?: string;
  dark?: boolean;
  step?: string;
}> = ({shot, caption, sub, stops, zoom, en, url, dark, step}) => {
  const {k, land, v, width} = useLayout();
  const bw = land ? width * 0.62 : width * 0.92;
  // In 9:16 a whole desktop page is unreadably small: use a taller window and
  // zoom ~1.7x into the content (RTL pages keep their content on the right).
  const bh = v ? bw * 0.95 : bw * (1350 / 2160);
  const vz: ZoomStop[] | undefined = v
    ? (zoom ?? [[0, 1, 0.62, 0.1]]).map(([f, s, ox, oy]) => [f, Math.min(1.95, s * 1.7), Math.max(0.5, ox), Math.min(oy, 0.15)] as ZoomStop)
    : zoom;
  const enter = useSpring(4, 15);
  return (
    <AbsoluteFill
      style={{
        alignItems: 'center',
        justifyContent: 'center',
        flexDirection: land ? (en ? 'row' : 'row-reverse') : 'column',
        gap: (land ? 70 : 60) * k,
        paddingBottom: land ? 0 : v ? 120 * k : 40 * k,
        padding: land ? `0 ${70 * k}px` : undefined,
      }}
    >
      <div style={{display: 'flex', flexDirection: 'column', alignItems: land ? (en ? 'flex-start' : 'flex-end') : 'center', gap: 18 * k, maxWidth: land ? 560 * k : 980 * k}}>
        {step ? <Chip label={step} size={34 * k} tone="brand" en={en} /> : null}
        <Words text={caption} size={(land ? 64 : 66) * k} en={en} align={land ? 'start' : 'center'} maxWidth={land ? 560 * k : 980 * k} />
        {sub ? <Words text={sub} size={36 * k} weight={600} color={C.muted} delay={10} en={en} align={land ? 'start' : 'center'} maxWidth={land ? 560 * k : 940 * k} /> : null}
      </div>
      <div style={{position: 'relative', transform: `translateY(${(1 - enter) * 200}px) perspective(2400px) rotateX(${(1 - enter) * 12}deg)`, opacity: enter}}>
        <Browser w={bw} h={bh} url={url} dark={dark}>
          <Shot src={`shots/${shot}.jpg`} w={bw} h={bh} img={size(shot)} stops={stops} zoom={vz} bg={dark ? '#09090b' : '#fafafa'} />
        </Browser>
        <div style={{position: 'absolute', bottom: -60 * k, left: 0, right: 0, display: 'flex', justifyContent: 'center'}}>
          <RealTag en={en} size={24 * k} />
        </div>
      </div>
    </AbsoluteFill>
  );
};

/* ---------- Roles grid ---------- */
export const RolesGrid: React.FC<{title: string; sub?: string; en?: boolean}> = ({title, sub, en}) => {
  const {k, land} = useLayout();
  return (
    <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 46 * k, padding: 50 * k}}>
      <Words text={title} size={70 * k} en={en} maxWidth={1000 * k} weight={900} />
      <div style={{display: 'flex', flexWrap: 'wrap', justifyContent: 'center', gap: 22 * k, maxWidth: (land ? 1900 : 980) * k, direction: en ? 'ltr' : 'rtl'}}>
        {ROLES.map((r, i) => (
          <RoleCard key={r.key} role={r} delay={10 + i * 4} k={k} en={en} w={land ? 200 : 225} />
        ))}
      </div>
      {sub ? <Words text={sub} size={40 * k} weight={600} color={C.muted} delay={50} en={en} maxWidth={940 * k} /> : null}
    </AbsoluteFill>
  );
};

/* ---------- Feature chips cloud ---------- */
export const Features: React.FC<{title: string; items: [IconName, string][]; en?: boolean}> = ({title, items, en}) => {
  const {k, land} = useLayout();
  return (
    <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 50 * k, padding: 60 * k}}>
      <Words text={title} size={72 * k} en={en} maxWidth={1000 * k} weight={900} />
      <div style={{display: 'flex', flexWrap: 'wrap', justifyContent: 'center', gap: 22 * k, maxWidth: (land ? 1600 : 980) * k, direction: en ? 'ltr' : 'rtl'}}>
        {items.map(([icon, label], i) => (
          <Chip key={i} icon={icon} label={label} size={40 * k} delay={10 + i * 5} en={en} />
        ))}
      </div>
    </AbsoluteFill>
  );
};

/* ---------- Statement: one strong line, optional icon ---------- */
export const Statement: React.FC<{text: string; sub?: string; icon?: IconName; en?: boolean}> = ({text, sub, icon, en}) => {
  const {k} = useLayout();
  return (
    <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 40 * k, padding: 70 * k}}>
      {icon ? (
        <Appear>
          <div style={{width: 150 * k, height: 150 * k, borderRadius: 42 * k, background: 'linear-gradient(135deg,#1F6FF2,#19C3B1)', display: 'flex', alignItems: 'center', justifyContent: 'center'}}>
            <Icon name={icon} size={84 * k} color="#fff" />
          </div>
        </Appear>
      ) : null}
      <Words text={text} size={84 * k} delay={8} en={en} maxWidth={1000 * k} weight={900} lineHeight={1.3} />
      {sub ? <Words text={sub} size={42 * k} weight={600} delay={22} color={C.muted} en={en} maxWidth={940 * k} /> : null}
    </AbsoluteFill>
  );
};

/* ---------- Flow: numbered steps connected by a line ---------- */
export const Flow: React.FC<{title: string; steps: [IconName, string, string][]; en?: boolean}> = ({title, steps, en}) => {
  const {k, land} = useLayout();
  const f = useCurrentFrame();
  return (
    <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 50 * k, padding: 60 * k}}>
      <Words text={title} size={68 * k} en={en} maxWidth={1000 * k} weight={900} />
      <div style={{display: 'flex', flexDirection: land ? (en ? 'row' : 'row-reverse') : 'column', gap: 26 * k, alignItems: 'stretch'}}>
        {steps.map(([icon, who, what], i) => {
          const d = 12 + i * 16;
          const lit = interpolate(f, [d, d + 10], [0, 1], clamp);
          return (
            <Appear key={i} delay={d} lift={40}>
              <div
                style={{
                  display: 'flex',
                  alignItems: 'center',
                  gap: 26 * k,
                  direction: en ? 'ltr' : 'rtl',
                  width: land ? 420 * k : 860 * k,
                  padding: `${24 * k}px ${30 * k}px`,
                  borderRadius: 28 * k,
                  background: `rgba(255,255,255,${0.05 + 0.05 * lit})`,
                  border: `2px solid ${lit > 0.5 ? C.teal : 'rgba(255,255,255,.14)'}`,
                }}
              >
                <div style={{width: 96 * k, height: 96 * k, flex: 'none', borderRadius: 26 * k, background: 'linear-gradient(135deg,#1F6FF2,#19C3B1)', display: 'flex', alignItems: 'center', justifyContent: 'center', position: 'relative'}}>
                  <Icon name={icon} size={52 * k} color="#fff" />
                  <div style={{position: 'absolute', top: -12 * k, [en ? 'left' : 'right']: -12 * k, width: 40 * k, height: 40 * k, borderRadius: '50%', background: C.white, color: C.ink, fontFamily: FONT_EN, fontWeight: 800, fontSize: 24 * k, display: 'flex', alignItems: 'center', justifyContent: 'center'}}>
                    {i + 1}
                  </div>
                </div>
                <div style={{display: 'flex', flexDirection: 'column', gap: 4 * k}}>
                  <div style={{fontFamily: en ? FONT_EN : FONT_AR, fontWeight: 800, fontSize: 40 * k, color: C.teal}}>{who}</div>
                  <div style={{fontFamily: en ? FONT_EN : FONT_AR, fontWeight: 600, fontSize: 34 * k, color: '#fff'}}>{what}</div>
                </div>
              </div>
            </Appear>
          );
        })}
      </div>
    </AbsoluteFill>
  );
};

export const End: React.FC<{headline?: string; sub?: string; en?: boolean; price?: boolean}> = ({headline, sub, en, price = true}) => {
  const {k, land} = useLayout();
  return <EndCard headline={headline} sub={sub} en={en} showPrice={price} scale={land ? k * 0.95 : k} />;
};
