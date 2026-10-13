import './fonts';
import React from 'react';
import {Composition} from 'remotion';
import {FPS} from './brand';
import {total} from './components/Scenes';
import * as A from './videos/Aqar';
import {NovixaBrand, NOVIXA_DUR} from './videos/NovixaBrand';

const V = {width: 1080, height: 1920}; // Reels / TikTok / Shorts / Stories / WhatsApp Status
const F = {width: 1080, height: 1350}; // Instagram & Facebook feed 4:5
const S = {width: 1080, height: 1080}; // square (LinkedIn / X / feed)
const L = {width: 1920, height: 1080}; // YouTube / Facebook / website / WhatsApp sales

const list: [string, React.FC, number, {width: number; height: number}][] = [
  ['AQ01-Hero-9x16', A.Hero, total(A.heroBeats), V],
  ['AQ01-Hero-4x5', A.Hero, total(A.heroBeats), F],
  ['AQ01-Hero-16x9', A.Hero, total(A.heroBeats), L],
  ['AQ01EN-Hero-16x9', A.HeroEn, total(A.heroEnBeats), L],
  ['AQ01EN-Hero-1x1', A.HeroEn, total(A.heroEnBeats), S],
  ['AQ02-Roles-9x16', A.Roles, total(A.rolesBeats), V],
  ['AQ03-ClientJourney-9x16', A.ClientSearch, total(A.searchBeats), V],
  ['AQ04-Maintenance-9x16', A.Maintenance, total(A.maintBeats), V],
  ['AQ05-Offer-9x16', A.Offer, total(A.offerBeats), V],
  ['AQ05-Offer-4x5', A.Offer, total(A.offerBeats), F],
  ['AQ06-Insight-9x16', A.Insight, total(A.insightBeats), V],
  ['AQ07-Marketers-9x16', A.Marketers, total(A.marketersBeats), V],
  ['AQ08-Explainer-16x9', A.Explainer, total(A.explainerBeats), L],
  ['AQ09-TryIt-9x16', A.TryIt, total(A.tryBeats), V],
  ['NX01-Novixa-9x16', NovixaBrand, NOVIXA_DUR, V],
  ['NX01-Novixa-16x9', NovixaBrand, NOVIXA_DUR, L],
];

export const Root: React.FC = () => (
  <>
    {list.map(([id, C, d, size]) => (
      <Composition key={id} id={id} component={C} durationInFrames={d} fps={FPS} {...size} />
    ))}
  </>
);
