"""Original, royalty-free music beds for the Aqar videos.

Synthesised from scratch (no samples, no third-party audio), so the output is
owned by Novixa and safe for ads/boosted posts on every platform.

  python3 tools/music.py            -> public/audio/*.wav (then encoded to .mp3)

Bed "aqar": 96 BPM, D Hijaz flavour (D Eb F# G A Bb C) — plucked-string
arpeggios (Karplus-Strong, oud-like), warm pad, soft kick/tak/shaker.
Bed "calm": slower, pad + pluck only, for the explainer / brand film.
"""
import os
import subprocess
import wave

import numpy as np
from scipy.signal import lfilter, butter

SR = 44100
OUT = os.path.join(os.path.dirname(__file__), '..', 'public', 'audio')
rng = np.random.default_rng(7)


def midi(n):
    return 440.0 * 2 ** ((n - 69) / 12)


def pluck(freq, dur, bright=0.5, decay=0.996):
    n = int(SR * dur)
    N = max(2, int(SR / freq))
    exc = rng.uniform(-1, 1, N)
    b, a = butter(1, 0.15 + bright * 0.6)
    exc = lfilter(b, a, exc)
    x = np.zeros(n)
    x[:N] = exc
    a_coef = np.zeros(N + 2)
    a_coef[0] = 1
    a_coef[N] = -0.5 * decay
    a_coef[N + 1] = -0.5 * decay
    y = lfilter([1.0], a_coef, x)
    env = np.minimum(1, np.linspace(0, 40, n)) * np.exp(-np.linspace(0, 3.2, n))
    return y * env


def pad(freqs, dur, vol=0.12):
    n = int(SR * dur)
    t = np.arange(n) / SR
    s = np.zeros(n)
    for f in freqs:
        for det in (-0.12, 0.0, 0.11):
            ff = f * 2 ** (det / 12)
            # soft saw via a few harmonics
            for h, amp in ((1, 1.0), (2, 0.35), (3, 0.15), (4, 0.07)):
                s += amp * np.sin(2 * np.pi * ff * h * t + rng.uniform(0, 6.28))
    b, a = butter(2, 1800 / (SR / 2))
    s = lfilter(b, a, s)
    att = int(SR * min(1.2, dur / 3))
    env = np.ones(n)
    env[:att] = np.linspace(0, 1, att)
    env[-att:] = np.linspace(1, 0, att)
    return s / (len(freqs) * 3) * env * vol * 6


def kick(vol=0.9):
    n = int(SR * 0.35)
    t = np.arange(n) / SR
    f = 45 + 85 * np.exp(-t * 28)
    ph = 2 * np.pi * np.cumsum(f) / SR
    return np.sin(ph) * np.exp(-t * 9) * vol


def tak(vol=0.25):
    n = int(SR * 0.09)
    t = np.arange(n) / SR
    s = rng.uniform(-1, 1, n)
    b, a = butter(2, [1800 / (SR / 2), 6000 / (SR / 2)], btype='band')
    tone = np.sin(2 * np.pi * 420 * t) * 0.6
    return (lfilter(b, a, s) + tone) * np.exp(-t * 55) * vol


def shaker(vol=0.05):
    n = int(SR * 0.06)
    t = np.arange(n) / SR
    s = rng.uniform(-1, 1, n)
    b, a = butter(2, 7000 / (SR / 2), btype='high')
    return lfilter(b, a, s) * np.exp(-t * 70) * vol


def place(buf, clip, at):
    i = int(at * SR)
    j = min(len(buf), i + len(clip))
    if i < len(buf):
        buf[i:j] += clip[: j - i]


def reverb(x, wet=0.22):
    out = x.copy()
    for d, g in ((0.031, 0.5), (0.047, 0.42), (0.071, 0.33), (0.113, 0.25), (0.17, 0.18), (0.26, 0.11)):
        k = int(d * SR)
        out[k:] += x[:-k] * g * wet * 2
    return out


# D Hijaz: D Eb F# G A Bb C
D = 62
SCALE = [0, 1, 4, 5, 7, 8, 10]
# chord roots (semitones from D) and triads, 2 bars each
PROG = [
    (0, [0, 4, 7]),     # D (Hijaz I)
    (5, [5, 8, 12]),    # Gm
    (10, [10, 13, 17]), # Cm-ish (C Eb G)
    (0, [0, 4, 7]),     # D
]
ARP = [0, 2, 1, 2, 0, 2, 1, 3]  # index pattern into chord tones (+octave)


def build(name, bpm, bars, drums=True, lead=True, master=0.8):
    beat = 60 / bpm
    bar = beat * 4
    total = bars * bar + 2.5
    L = np.zeros(int(total * SR))
    R = np.zeros(int(total * SR))
    for b in range(bars):
        root, tri = PROG[(b // 2) % len(PROG)]
        t0 = b * bar
        if b % 2 == 0:
            p = pad([midi(D - 12 + n) for n in tri], bar * 2 + 0.6, vol=0.10)
            place(L, p, t0)
            place(R, p * 0.92, t0)
            bass = pluck(midi(D - 24 + root), bar * 2, bright=0.2, decay=0.998) * 0.35
            place(L, bass, t0)
            place(R, bass, t0)
        # arpeggio on 8ths (sparser in intro/outro)
        intro = b < 2 or b >= bars - 2
        for k in range(8):
            if intro and k % 2:
                continue
            idx = ARP[k]
            note = tri[idx % 3] + (12 if idx >= 3 else 0)
            f = midi(D + note)
            c = pluck(f, 1.2, bright=0.55) * (0.22 if k % 2 == 0 else 0.15)
            pan = 0.6 + 0.4 * ((k % 4) / 3)
            place(L, c * pan, t0 + k * beat / 2)
            place(R, c * (1.6 - pan), t0 + k * beat / 2 + 0.008)
        # melodic hook every 4 bars in the middle section
        if lead and 4 <= b < bars - 4 and b % 4 == 2:
            mel = [7, 8, 7, 4, 5, 4, 1, 0]
            for k, m in enumerate(mel):
                f = midi(D + 12 + m)
                c = pluck(f, 1.0, bright=0.8, decay=0.997) * 0.2
                place(L, c * 0.8, t0 + k * beat / 2)
                place(R, c, t0 + k * beat / 2 + 0.012)
        if drums and 2 <= b < bars - 1:
            for q in range(4):
                if q in (0, 2):
                    place(L, kick(0.55), t0 + q * beat)
                    place(R, kick(0.55), t0 + q * beat)
                if q in (1, 3) and b >= 4:
                    place(L, tak(0.18), t0 + q * beat)
                    place(R, tak(0.2), t0 + q * beat + 0.005)
                if b >= 4:
                    for s16 in range(4):
                        sh = shaker(0.045 if s16 % 2 else 0.03)
                        place(L if s16 % 2 else R, sh, t0 + q * beat + s16 * beat / 4)
    L, R = reverb(L), reverb(R)
    st = np.stack([L, R], axis=1)
    st /= np.max(np.abs(st)) + 1e-9
    st *= master
    fade = int(SR * 2.0)
    st[-fade:] *= np.linspace(1, 0, fade)[:, None]
    write(name, st)


def sfx():
    # whoosh: band-passed noise sweep
    n = int(SR * 0.7)
    t = np.arange(n) / SR
    s = rng.uniform(-1, 1, n)
    out = np.zeros(n)
    for i in range(0, n, 512):
        fc = 400 + 5000 * (i / n) ** 1.5
        b, a = butter(2, [max(50, fc * 0.7) / (SR / 2), min(SR / 2 - 100, fc * 1.3) / (SR / 2)], btype='band')
        out[i:i + 512] = lfilter(b, a, s[i:i + 512])
    env = np.sin(np.pi * np.linspace(0, 1, n)) ** 2
    w = out * env
    w /= np.max(np.abs(w))
    write('whoosh', np.stack([w * 0.5, w * 0.5], 1))
    # pop / tap
    n = int(SR * 0.12)
    t = np.arange(n) / SR
    p = np.sin(2 * np.pi * (900 - 2500 * t) * t) * np.exp(-t * 45)
    write('pop', np.stack([p * 0.5, p * 0.5], 1))
    # chime for logo reveals
    n = int(SR * 2.2)
    c = sum(pluck(midi(D + 24 + k), 2.2, bright=0.9, decay=0.9985) * (0.5 / (i + 1)) for i, k in enumerate([0, 7, 12, 16]))
    c /= np.max(np.abs(c))
    write('chime', np.stack([c * 0.6, c * 0.6], 1))


def write(name, st):
    os.makedirs(OUT, exist_ok=True)
    path = os.path.join(OUT, f'{name}.wav')
    data = (np.clip(st, -1, 1) * 32767).astype('<i2')
    with wave.open(path, 'wb') as w:
        w.setnchannels(2)
        w.setsampwidth(2)
        w.setframerate(SR)
        w.writeframes(data.tobytes())
    mp3 = path[:-4] + '.mp3'
    subprocess.run(['ffmpeg', '-y', '-loglevel', 'error', '-i', path, '-b:a', '192k', mp3], check=True)
    os.remove(path)
    print('wrote', mp3)


if __name__ == '__main__':
    build('aqar-bed', bpm=96, bars=44)        # ~110 s
    build('calm-bed', bpm=84, bars=34, drums=False, master=0.7)  # ~97 s
    sfx()
