import React from 'react';
import {AbsoluteFill, Audio, interpolate, Sequence, staticFile, useVideoConfig} from 'remotion';
import {C, FACTS, FONT_AR, FONT_EN} from '../brand';
import {Icon, IconName} from '../components/Icons';
import {Appear, Bg, Chip, Phone, phoneScreen, Progress, Scene, Shot, useSpring, WhatsPill, Words, clamp} from '../components/Kit';
import {AqarLockup, NovixaLockup, NovixaMark} from '../components/Logos';
import shots from '../shots.json';

// "Novixa — Engineered for Growth": company intro film (9:16 and 16:9).
// Every line is taken from Novixa's own published flyers.

const S = {intro: 105, what: 150, aqar: 165, community: 180, world: 105, end: 150};
export const NOVIXA_DUR = Object.values(S).reduce((a, b) => a + b, 0);

const Service: React.FC<{icon: IconName; label: string; delay: number; k: number}> = ({icon, label, delay, k}) => {
  const s = useSpring(delay, 14);
  return (
    <div
      style={{
        width: 300 * k,
        padding: `${30 * k}px ${16 * k}px`,
        borderRadius: 28 * k,
        background: 'rgba(255,255,255,.07)',
        border: '1.5px solid rgba(255,255,255,.14)',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        gap: 16 * k,
        opacity: s,
        transform: `translateY(${(1 - s) * 50}px)`,
      }}
    >
      <div
        style={{
          width: 96 * k,
          height: 96 * k,
          borderRadius: 26 * k,
          background: 'linear-gradient(135deg,#1F6FF2,#19C3B1)',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
        }}
      >
        <Icon name={icon} size={54 * k} color="#fff" />
      </div>
      <div style={{fontFamily: FONT_AR, fontWeight: 800, fontSize: 38 * k, color: '#fff'}}>{label}</div>
    </div>
  );
};

export const NovixaBrand: React.FC = () => {
  const {width, height} = useVideoConfig();
  const v = height > width;
  const k = Math.min(width, height) / 1080;
  let t = 0;
  const at = (n: number) => {
    const s = t;
    t += n;
    return s;
  };
  const markS = useSpring(4, 12);
  const phoneW = v ? 480 : 380 * (height / 1080);
  const {sw, sh} = phoneScreen(phoneW);
  const home = (shots as unknown as Record<string, [number, number]>)['m-home'];

  return (
    <AbsoluteFill style={{background: C.navy950}}>
      <Bg />
      <Audio src={staticFile('audio/calm-bed.mp3')} volume={(fr) => interpolate(fr, [0, 20, NOVIXA_DUR - 40, NOVIXA_DUR], [0, 0.55, 0.55, 0], clamp)} />

      <Sequence from={at(S.intro)} durationInFrames={S.intro}>
        <Scene dur={S.intro} fadeIn={0}>
          <Audio src={staticFile('audio/chime.mp3')} volume={0.5} />
          <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 50 * k}}>
            <div style={{transform: `scale(${0.6 + 0.4 * markS}) rotate(${(1 - markS) * -20}deg)`, opacity: markS}}>
              <NovixaMark size={240 * k} />
            </div>
            <Words text="من الفكرة إلى التقنية / التي *تصنع* *أثرًا*" size={(v ? 78 : 72) * k} delay={18} maxWidth={1000 * k} />
            <Words text="From idea to technology that creates an impact." size={32 * k} weight={500} en color={C.muted} delay={36} />
          </AbsoluteFill>
        </Scene>
      </Sequence>

      <Sequence from={at(S.what)} durationInFrames={S.what}>
        <Scene dur={S.what}>
          <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 60 * k, padding: 60 * k}}>
            <NovixaLockup height={86 * k} />
            <Words text="شركة برمجيات تصمّم وتطوّر" size={60 * k} delay={8} />
            <div style={{display: 'flex', flexWrap: 'wrap', gap: 28 * k, justifyContent: 'center', maxWidth: (v ? 680 : 1400) * k, direction: 'rtl'}}>
              <Service icon="globe" label="المواقع" delay={18} k={k} />
              <Service icon="layers" label="المنصّات" delay={24} k={k} />
              <Service icon="code" label="الأنظمة" delay={30} k={k} />
              <Service icon="rocket" label="المنتجات الرقمية" delay={36} k={k} />
            </div>
            <Words text="حلول تقنية حقيقية تساعد المؤسسات *على* *النمو*" size={40 * k} weight={700} color={C.muted} delay={50} maxWidth={900 * k} />
          </AbsoluteFill>
        </Scene>
      </Sequence>

      <Sequence from={at(S.aqar)} durationInFrames={S.aqar}>
        <Scene dur={S.aqar}>
          <AbsoluteFill
            style={{
              alignItems: 'center',
              justifyContent: 'center',
              flexDirection: v ? 'column' : 'row-reverse',
              gap: 60 * k,
              padding: 60 * k,
            }}
          >
            <div style={{display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 34 * k}}>
              <Chip icon="rocket" label="أول منتجات نوفيكسا" size={38 * k} tone="brand" />
              <AqarLockup height={110 * k} />
              <Words text="منصة متكاملة لإدارة / الأنشطة والعمليات العقارية" size={52 * k} delay={12} maxWidth={900 * k} />
            </div>
            <Phone w={phoneW}>
              <Shot src="shots/m-home.jpg" w={sw} h={sh} img={home} stops={[[10, 0], [150, 0.45]]} />
            </Phone>
          </AbsoluteFill>
        </Scene>
      </Sequence>

      <Sequence from={at(S.community)} durationInFrames={S.community}>
        <Scene dur={S.community}>
          <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 46 * k, padding: 70 * k}}>
            <div
              style={{
                width: 150 * k,
                height: 150 * k,
                borderRadius: '50%',
                background: 'rgba(25,195,177,.15)',
                border: `2px solid ${C.teal}`,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
              }}
            >
              <Icon name="heart" size={76 * k} color={C.teal} />
            </div>
            <Words text="نؤمن أن دور التقنية / لا يقتصر على المشاريع التجارية" size={58 * k} delay={6} maxWidth={1000 * k} />
            <Words
              text="لذلك أطلقنا *مبادرة* *مجتمعية* نصمّم فيها / مشاريع تقنية مجانية لجهات لها أثر في المجتمع"
              size={42 * k}
              weight={700}
              color="#D6E2F7"
              delay={30}
              maxWidth={1000 * k}
            />
            <Chip icon="building" label="أولها: تصوّر رقمي لمعهد بحوث حضرموت — جامعة الأحقاف" size={(v ? 30 : 32) * k} delay={70} />
          </AbsoluteFill>
        </Scene>
      </Sequence>

      <Sequence from={at(S.world)} durationInFrames={S.world}>
        <Scene dur={S.world}>
          <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 30 * k}}>
            <Words text="من *اليمن* .. / إلى العالم" size={110 * k} delay={4} maxWidth={1000 * k} />
            <Words text="Engineered for Growth." size={56 * k} weight={600} en color={C.teal} delay={22} />
          </AbsoluteFill>
        </Scene>
      </Sequence>

      <Sequence from={at(S.end)} durationInFrames={S.end}>
        <Scene dur={S.end} fadeOut={0}>
          <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center', gap: 44 * k}}>
            <NovixaLockup height={110 * k} />
            <Words text="نبني التقنية التي تجعل أعمالك *أقوى*" size={62 * k} delay={8} maxWidth={1000 * k} />
            <div style={{display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 22 * k}}>
              <Chip icon="globe" label={FACTS.site} size={40 * k} tone="white" en delay={18} />
              <WhatsPill delay={24} k={k} />
              <Appear delay={32}>
                <div style={{fontFamily: FONT_EN, color: C.muted, fontSize: 32 * k, fontWeight: 600}}>{FACTS.handle} · Novixa-dev</div>
              </Appear>
            </div>
          </AbsoluteFill>
        </Scene>
      </Sequence>
      <Progress />
    </AbsoluteFill>
  );
};
