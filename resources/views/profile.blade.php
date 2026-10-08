@extends('layouts.app')

@section('title', 'Profil - THW Trainer')
@section('description', 'Verwalte deine persönlichen Daten, Fortschritt und Datenschutz.')

@push('styles')
<style>
    /* ═══════════════════════════════════════════════════════════════
       Profil-Redesign — 1:1 aus Claude Design (profil-redisgn)
       ═══════════════════════════════════════════════════════════════ */

    .dash-container { max-width: 1180px; padding: 0 1rem; }

    /* Top stat pills */
    .profile-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem; }
    @media (max-width: 720px) { .profile-stats { grid-template-columns: repeat(3, 1fr); } }

    /* ─── Hero / Identity ─── */
    .hero-card {
        position: relative;
        padding: 1.5rem;
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 1.25rem;
        align-items: center;
        overflow: hidden;
    }
    .hero-card > * { position: relative; }

    @media (max-width: 640px) {
        .hero-card { grid-template-columns: 1fr; text-align: center; justify-items: center; }
        .hero-meta, .hero-actions { justify-content: center; }
        .hero-card .streak-dots { justify-content: center; }
        .hero-card .hero-xp { width: 100%; }
    }

    .avatar-ring {
        --ring-size: 96px;
        --ring-pct: 50;
        width: var(--ring-size);
        height: var(--ring-size);
        border-radius: 50%;
        background: conic-gradient(var(--thw-blue) calc(var(--ring-pct) * 1%), rgba(0,51,127,0.12) 0);
        padding: 4px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        position: relative;
    }
    html:not(.light-mode) .avatar-ring {
        background: conic-gradient(var(--thw-blue-glow, #5b9aff) calc(var(--ring-pct) * 1%), rgba(91,154,255,0.15) 0);
    }
    .avatar-ring__inner {
        width: 100%; height: 100%;
        border-radius: 50%;
        background: var(--bg-elevated);
        display: grid;
        place-items: center;
        overflow: hidden;
        position: relative;
    }
    .avatar-ring__inner img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
    }
    html:not(.light-mode) .avatar-ring__inner { background: #1a1a1d; }
    .avatar-ring__lvl {
        position: absolute;
        bottom: -4px; left: 50%;
        transform: translateX(-50%);
        background: var(--thw-blue);
        color: #fff;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.625rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        white-space: nowrap;
    }

    .avatar-regen-btn {
        position: absolute;
        top: -4px; right: -4px;
        width: 28px; height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00337F, #0055cc);
        border: 2px solid var(--bg-base);
        color: #fff;
        font-size: 0.7rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 200ms, box-shadow 200ms;
        padding: 0;
        z-index: 2;
    }
    .avatar-regen-btn:hover { transform: scale(1.15); box-shadow: 0 4px 12px rgba(0, 51, 127, 0.35); }
    .avatar-regen-btn.is-spinning i { animation: pf-spin 0.6s ease-in-out; }
    @keyframes pf-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    html.light-mode .avatar-regen-btn { border-color: var(--bg-elevated); }

    .hero-name {
        font-family: 'Barlow Condensed', 'Figtree', sans-serif;
        font-weight: 800;
        font-size: 1.75rem;
        line-height: 1.1;
        letter-spacing: -0.015em;
        color: var(--text-primary);
        margin: 0 0 0.25rem;
    }
    .hero-email { font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.6rem; word-break: break-all; }
    .hero-eyebrow {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        margin-bottom: 0.4rem;
    }
    .hero-meta { display: flex; gap: 0.5rem; flex-wrap: wrap; }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        border-radius: 999px;
        background: var(--glass-bg, rgba(255,255,255,0.05));
        border: 1px solid var(--glass-border, rgba(255,255,255,0.1));
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-secondary);
    }
    html.light-mode .chip { background: #fff; border-color: rgba(0,51,127,0.1); }
    .chip--ok   { background: rgba(34,197,94,0.12);  border-color: rgba(34,197,94,0.3);  color: #22c55e; }
    .chip--warn { background: rgba(245,158,11,0.12); border-color: rgba(245,158,11,0.3); color: #f59e0b; }
    .chip--blue { background: rgba(0,51,127,0.08);   border-color: rgba(0,51,127,0.2);   color: #00337F; }
    html:not(.light-mode) .chip--blue { background: rgba(91,154,255,0.14); border-color: rgba(91,154,255,0.3); color: #5b9aff; }
    .chip--gold { background: rgba(251,191,36,0.15); border-color: rgba(251,191,36,0.35); color: #f59e0b; }

    .hero-actions { display: flex; gap: 0.5rem; flex-shrink: 0; }

    /* XP bar */
    .hero-xp { grid-column: 1 / -1; margin-top: 0.25rem; }
    .hero-xp__row { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.375rem; flex-wrap: wrap; gap: 0.5rem; }
    .hero-xp__label { font-family: 'IBM Plex Mono', monospace; font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); }
    .hero-xp__values { font-size: 0.8125rem; color: var(--text-secondary); }
    .hero-xp__values strong { color: var(--text-primary); font-weight: 700; }

    .pf-xp-bar {
        width: 100%;
        height: 8px;
        background: rgba(0,51,127,0.10);
        border-radius: 999px;
        overflow: hidden;
    }
    html:not(.light-mode) .pf-xp-bar { background: rgba(255,255,255,0.06); }
    .pf-xp-bar__fill {
        height: 100%;
        background: linear-gradient(90deg, #00337F, #5b9aff);
        border-radius: 999px;
    }

    /* ─── Bento Grid ─── */
    .bento {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 1rem;
        margin-top: 1rem;
    }
    .bento > .card-identity      { grid-column: span 12; }
    .bento > .card-learning      { grid-column: span 8; }
    .bento > .card-achievements  { grid-column: span 4; }
    .bento > .card-accessories   { grid-column: span 12; }

    @media (max-width: 1024px) {
        .bento > .card-learning,
        .bento > .card-achievements { grid-column: span 12; }
    }

    .card { padding: 1.25rem; border-radius: 0.75rem; }
    .card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; gap: 1rem; }
    .card-head .section-label { flex: 1; min-width: 0; }
    .card-head__hint { font-size: 0.75rem; color: var(--text-muted); }

    .section-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
    }
    html.light-mode .section-label { color: #6b7280; }

    /* ─── Page title ─── */
    .pf-page-title { margin-bottom: 1.5rem; }
    .pf-page-title__eyebrow {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }
    .pf-page-title__h1 {
        font-family: 'Barlow Condensed', 'Figtree', sans-serif;
        font-weight: 800;
        font-size: 2rem;
        line-height: 1.1;
        color: var(--text-primary);
        margin: 0 0 0.25rem;
        letter-spacing: -0.02em;
    }
    html.light-mode .pf-page-title__h1 { color: var(--thw-blue, #00337F); }
    .pf-page-title__sub { font-size: 0.875rem; color: var(--text-secondary); }

    .pf-settings-gear {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--glass-bg, rgba(255,255,255,0.05));
        border: 1px solid var(--glass-border, rgba(0,51,127,0.15));
        color: var(--text-secondary);
        font-size: 1.125rem;
        text-decoration: none;
        transition: transform 200ms ease, background 200ms ease, color 200ms ease, border-color 200ms ease;
        flex-shrink: 0;
    }
    .pf-settings-gear:hover {
        background: rgba(0,51,127,0.1);
        border-color: rgba(0,51,127,0.4);
        color: var(--thw-blue);
        transform: rotate(45deg);
    }
    html:not(.light-mode) .pf-settings-gear:hover {
        background: rgba(91,154,255,0.15);
        border-color: rgba(91,154,255,0.4);
        color: #5b9aff;
    }

    /* ─── Form primitives ─── */
    .field { display: block; margin-bottom: 1rem; }
    .field:last-child { margin-bottom: 0; }
    .field__label { display: block; font-size: 0.8125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.375rem; }
    .field__help { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.375rem; }
    .field__error { font-size: 0.75rem; color: #ef4444; margin-top: 0.3rem; }
    .field__row { display: flex; gap: 0.5rem; align-items: stretch; }
    .field__row > .input { flex: 1; }

    .input, .select {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid rgba(0,51,127,0.14);
        background: var(--bg-elevated, #fff);
        color: var(--text-primary);
        font-family: 'Figtree', system-ui, sans-serif;
        font-size: 0.9375rem;
        line-height: 1.35;
        transition: border-color 150ms ease, box-shadow 150ms ease;
    }
    html:not(.light-mode) .input, html:not(.light-mode) .select {
        background: #121214;
        border-color: rgba(255,255,255,0.1);
    }
    .input:hover, .select:hover { border-color: rgba(0,51,127,0.25); }
    html:not(.light-mode) .input:hover, html:not(.light-mode) .select:hover { border-color: rgba(91,154,255,0.35); }
    .input:focus, .select:focus {
        outline: none;
        border-color: #00337F;
        box-shadow: 0 0 0 3px rgba(0,51,127,0.12);
    }
    html:not(.light-mode) .input:focus, html:not(.light-mode) .select:focus {
        border-color: #5b9aff;
        box-shadow: 0 0 0 3px rgba(91,154,255,0.20);
    }
    .input--error { border-color: #ef4444 !important; box-shadow: 0 0 0 2px rgba(239,68,68,0.15) !important; }

    /* ─── Toggle switches ─── */
    .toggle-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0,51,127,0.06);
    }
    html:not(.light-mode) .toggle-row { border-bottom-color: rgba(255,255,255,0.06); }
    .toggle-row:last-child { border-bottom: 0; padding-bottom: 0; }
    .toggle-row:first-child { padding-top: 0; }
    .toggle-row__body { flex: 1; min-width: 0; }
    .toggle-row__title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .toggle-row__desc { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; line-height: 1.5; }

    .switch { position: relative; display: inline-block; width: 40px; height: 22px; flex-shrink: 0; }
    .switch input { opacity: 0; width: 0; height: 0; position: absolute; }
    .switch__slider {
        position: absolute; cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,51,127,0.15);
        border-radius: 999px;
        transition: background 150ms ease;
    }
    html:not(.light-mode) .switch__slider { background: rgba(255,255,255,0.12); }
    .switch__slider::before {
        content: '';
        position: absolute;
        height: 16px; width: 16px;
        left: 3px; top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: transform 150ms ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .switch input:checked + .switch__slider { background: #00337F; }
    html:not(.light-mode) .switch input:checked + .switch__slider { background: #5b9aff; }
    .switch input:checked + .switch__slider::before { transform: translateX(18px); }
    .switch input:disabled + .switch__slider { cursor: not-allowed; opacity: 0.45; }

    /* ─── Mini-Switch (für Per-Kategorie Email/Push) ─── */
    .switch--mini { width: 32px; height: 18px; }
    .switch--mini .switch__slider::before { height: 12px; width: 12px; left: 3px; top: 3px; }
    .switch--mini input:checked + .switch__slider::before { transform: translateX(14px); }

    /* ─── Notification card layout ─── */
    .notif-master { display: flex; flex-direction: column; }
    .notif-categories {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(0,51,127,0.08);
    }
    html:not(.light-mode) .notif-categories { border-top-color: rgba(255,255,255,0.08); }
    .notif-categories__header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 0.25rem;
    }
    .notif-categories__label {
        font-size: 0.7rem; font-weight: 700; letter-spacing: 0.06em;
        text-transform: uppercase; color: var(--text-muted);
    }
    .notif-categories__legend {
        display: flex; gap: 0.875rem; font-size: 0.85rem; color: var(--text-muted);
        padding-right: 0.25rem;
    }
    .notif-categories__legend span { display: inline-flex; align-items: center; justify-content: center; width: 32px; }
    .notif-cat-switches { display: flex; gap: 0.625rem; align-items: center; }
    .notif-cat-row.is-saving { opacity: 0.6; pointer-events: none; }

    /* ─── Achievements preview ─── */
    .ach-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.625rem; }
    .ach-grid--seven {
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
    }
    .ach-grid--seven .ach { aspect-ratio: 1 / 1; font-size: 1.5rem; }
    .ach--more {
        background: rgba(0,51,127,0.08) !important;
        color: var(--thw-blue) !important;
        text-decoration: none;
        border: 1px dashed rgba(0,51,127,0.25) !important;
    }
    .ach--more:hover { background: rgba(0,51,127,0.14) !important; }

    /* 14-Tage Streak-Dots */
    .streak-dots {
        display: flex; flex-wrap: wrap; gap: 0.375rem; margin-top: 0.5rem;
    }
    .streak-dot {
        width: 22px; height: 22px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.6875rem;
        background: rgba(0,51,127,0.06);
        border: 1px solid rgba(0,51,127,0.12);
        color: transparent;
        transition: transform 0.15s;
    }
    html:not(.light-mode) .streak-dot {
        background: rgba(255,255,255,0.04);
        border-color: rgba(255,255,255,0.1);
    }
    .streak-dot:hover { transform: scale(1.15); }
    .streak-dot--learned {
        background: linear-gradient(135deg, #f59e0b, #f97316);
        border-color: #f59e0b;
        color: #fff;
        box-shadow: 0 2px 6px rgba(245,158,11,0.35);
    }
    .streak-dot--freeze {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        border-color: #3b82f6;
        color: #fff;
        box-shadow: 0 2px 6px rgba(59,130,246,0.35);
    }
    .streak-dots__empty {
        font-size: 0.75rem; color: var(--text-muted); font-style: italic;
    }

    /* Lernstatistik-Kacheln (eckig) */
    .card-learning .stat-pills .stat-pill {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 0.25rem;
        padding: 1rem 0.75rem;
        background: rgba(255,255,255,0.6);
        border: 1px solid rgba(0,51,127,0.12);
        border-radius: 10px;
        transition: border-color 0.15s;
    }
    html:not(.light-mode) .card-learning .stat-pills .stat-pill {
        background: rgba(255,255,255,0.04);
        border-color: rgba(255,255,255,0.08);
    }
    .card-learning .stat-pills .stat-pill:hover { transform: none; border-color: rgba(0,51,127,0.25); }
    .card-learning .stat-pill__value {
        font-size: 1.75rem; font-weight: 700; line-height: 1; color: var(--text-primary);
    }
    .card-learning .stat-pill__value--ok { color: #10b981; }
    .card-learning .stat-pill__value--err { color: #ef4444; }
    .card-learning .stat-pill__label {
        font-size: 0.75rem; color: var(--text-muted); margin-top: 0.125rem;
    }

    /* Lernabschnitte-Liste */
    .section-list { display: flex; flex-direction: column; }
    .section-row {
        display: grid;
        grid-template-columns: 32px 1fr auto 14px;
        gap: 0.75rem;
        align-items: center;
        padding: 0.625rem 0;
        border-top: 1px solid rgba(0,51,127,0.08);
        text-decoration: none;
        color: inherit;
        transition: background 0.15s;
    }
    .section-row:hover { background: rgba(0,51,127,0.03); }
    .section-row__rank {
        display: flex; align-items: center; justify-content: center;
        width: 28px; height: 28px; border-radius: 7px;
        background: var(--thw-blue); color: #fff;
        font-size: 0.8125rem; font-weight: 700;
    }
    .section-row--empty .section-row__rank { background: rgba(0,51,127,0.12); color: var(--text-muted); }
    .section-row__body { min-width: 0; }
    .section-row__name {
        font-size: 0.875rem; color: var(--text-primary); font-weight: 500;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        margin-bottom: 0.375rem;
    }
    .section-row--empty .section-row__name { color: var(--text-muted); }
    .section-row__bar {
        height: 3px; border-radius: 2px; background: rgba(0,51,127,0.08); overflow: hidden;
    }
    .section-row__fill {
        height: 100%; background: var(--thw-blue); border-radius: 2px;
        transition: width 0.4s ease;
    }
    .section-row__pct {
        font-size: 0.8125rem; font-weight: 600; color: var(--thw-blue);
        min-width: 36px; text-align: right;
    }
    .section-row--empty .section-row__pct { color: var(--text-muted); font-weight: 500; }
    .section-row__chev { color: var(--text-muted); font-size: 0.75rem; }
    .ach {
        aspect-ratio: 1;
        border-radius: 0.625rem;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        display: grid;
        place-items: center;
        font-size: 1.5rem;
        position: relative;
        transition: transform 150ms ease;
    }
    html.light-mode .ach { background: #fff; border-color: rgba(0,51,127,0.08); }
    .ach.ach--unlocked,
    html.light-mode .ach.ach--unlocked {
        background: linear-gradient(135deg, rgba(251,191,36,0.22), rgba(245,158,11,0.12));
        border-color: rgba(251,191,36,0.4);
        color: #f59e0b;
    }
    html:not(.light-mode) .ach.ach--unlocked { color: #fbbf24; }
    .ach.ach--locked { opacity: 0.45; color: var(--text-muted); }
    .ach .bi { font-size: 1.25rem; }

    /* ─── Streak ─── */
    .streak-hero { text-align: center; padding: 0.5rem 0 1rem; }
    .streak-num {
        font-family: 'Barlow Condensed', 'Figtree', sans-serif;
        font-weight: 800;
        font-size: 3rem;
        line-height: 1;
        color: #fbbf24;
        letter-spacing: -0.03em;
    }
    html.light-mode .streak-num { color: #f59e0b; }
    .streak-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }

    .heat-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 3px; }
    .heat-cell {
        aspect-ratio: 1;
        border-radius: 3px;
        background: rgba(255,255,255,0.06);
    }
    .heat-cell[data-v="1"] { background: rgba(91,154,255,0.25); }
    .heat-cell[data-v="2"] { background: rgba(91,154,255,0.5); }
    .heat-cell[data-v="3"] { background: rgba(91,154,255,0.75); }
    .heat-cell[data-v="4"] { background: #00337F; }
    html.light-mode .heat-cell[data-v="0"] { background: rgba(0,51,127,0.06); }
    html.light-mode .heat-cell[data-v="1"] { background: rgba(0,51,127,0.22); }
    html.light-mode .heat-cell[data-v="2"] { background: rgba(0,51,127,0.45); }
    html.light-mode .heat-cell[data-v="3"] { background: rgba(0,51,127,0.7); }
    html.light-mode .heat-cell[data-v="4"] { background: #00337F; }

    .heat-legend {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 0.625rem;
        font-size: 0.6875rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }
    .heat-legend__scale { display: flex; gap: 3px; align-items: center; }
    .heat-legend__swatch { width: 10px; height: 10px; border-radius: 2px; }

    /* ─── Meta grid ─── */
    .meta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    @media (max-width: 720px) { .meta-grid { grid-template-columns: 1fr; } }
    .meta-item__label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }
    .meta-item__value { font-size: 0.9375rem; font-weight: 600; color: var(--text-primary); }
    .meta-item__sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.1rem; }

    /* ─── Danger zone ─── */
    .danger-card {
        padding: 1.25rem;
        border: 1px solid rgba(239,68,68,0.3);
        background: rgba(239,68,68,0.04);
        border-radius: 0.75rem;
    }
    html:not(.light-mode) .danger-card { background: rgba(239,68,68,0.08); }
    .danger-card .section-label { color: #ef4444; }
    .danger-head { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
    .danger-icon {
        width: 36px; height: 36px;
        border-radius: 0.5rem;
        background: rgba(239,68,68,0.12);
        color: #ef4444;
        display: grid;
        place-items: center;
        font-size: 1.125rem;
        flex-shrink: 0;
    }
    .danger-title { font-size: 1rem; font-weight: 700; color: var(--text-primary); }
    .danger-desc { font-size: 0.8125rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.5; }

    .btn-danger-inline {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        border-radius: 0.5rem;
        background: #ef4444;
        color: #fff;
        border: 0;
        font-family: 'Figtree', system-ui, sans-serif;
        font-weight: 700;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background 150ms ease, box-shadow 150ms ease;
    }
    .btn-danger-inline:hover { background: #dc2626; box-shadow: 0 4px 14px rgba(239,68,68,0.35); }
    .btn-danger-inline:disabled { opacity: 0.5; cursor: not-allowed; }

    /* ─── Card footer (save bar) ─── */
    .card-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(0,51,127,0.06);
        flex-wrap: wrap;
    }
    html:not(.light-mode) .card-foot { border-top-color: rgba(255,255,255,0.06); }
    .card-foot__note { font-size: 0.75rem; color: var(--text-muted); }

    .btn-pf-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.125rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #00337F, #0055cc);
        color: #fff;
        border: 0;
        font-family: 'Figtree', system-ui, sans-serif;
        font-weight: 700;
        font-size: 0.875rem;
        cursor: pointer;
        transition: box-shadow 150ms ease, transform 150ms ease;
    }
    .btn-pf-primary:hover { box-shadow: 0 8px 25px rgba(0,51,127,0.45); transform: translateY(-1px); }

    .btn-pf-ghost {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.875rem;
        border-radius: 0.5rem;
        background: transparent;
        color: var(--text-primary);
        border: 1px solid rgba(0,51,127,0.2);
        font-family: 'Figtree', system-ui, sans-serif;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background 150ms ease, border-color 150ms ease;
        text-decoration: none;
    }
    html:not(.light-mode) .btn-pf-ghost { border-color: rgba(255,255,255,0.1); }
    .btn-pf-ghost:hover { background: rgba(0,51,127,0.05); border-color: rgba(0,51,127,0.35); }
    html:not(.light-mode) .btn-pf-ghost:hover { background: rgba(91,154,255,0.1); border-color: rgba(91,154,255,0.35); }

    /* ─── Avatar accessories ─── */
    .acc-row { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
    .acc {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.75rem 0.4rem 0.4rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        font-size: 0.8125rem;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 150ms ease;
    }
    html.light-mode .acc { background: #fff; border-color: rgba(0,51,127,0.1); }
    .acc:disabled { opacity: 0.6; cursor: not-allowed; }
    .acc--on { border-color: #00337F; color: #00337F; background: rgba(0,51,127,0.05); }
    html:not(.light-mode) .acc--on { color: #5b9aff; background: rgba(91,154,255,0.1); border-color: rgba(91,154,255,0.4); }
    .acc__preview { width: 24px; height: 24px; border-radius: 50%; object-fit: cover; }

    .acc-colors { display: flex; gap: 0.4rem; margin-top: 0.5rem; align-items: center; }
    .acc-color-dot {
        width: 22px; height: 22px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
        transition: transform 150ms ease;
    }
    .acc-color-dot:hover, .acc-color-dot.is-selected { transform: scale(1.2); }
    .acc-color-dot.is-selected { border-color: var(--text-primary); }

    .acc-section-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        margin: 0.875rem 0 0.5rem;
    }
    .acc-section-label:first-child { margin-top: 0; }
    .acc-empty { padding: 1rem; text-align: center; color: var(--text-muted); font-size: 0.8125rem; }
    .acc-empty a { color: #a855f7; font-weight: 600; text-decoration: none; }
    .acc-empty a:hover { text-decoration: underline; }

    /* ─── Info row (DSGVO) ─── */
    .info-row {
        display: flex;
        gap: 0.75rem;
        padding: 0.875rem;
        margin: 0.25rem 0;
        border-radius: 0.5rem;
        background: rgba(0, 51, 127, 0.04);
        border: 1px solid rgba(0, 51, 127, 0.1);
    }
    html:not(.light-mode) .info-row { background: rgba(91,154,255,0.08); border-color: rgba(91,154,255,0.2); }
    .info-row__icon {
        width: 28px; height: 28px;
        border-radius: 50%;
        background: #00337F;
        color: #fff;
        display: grid;
        place-items: center;
        font-size: 0.8125rem;
        flex-shrink: 0;
    }
    html:not(.light-mode) .info-row__icon { background: #5b9aff; }
    .info-row__title { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.2rem; }
    .info-row__desc { font-size: 0.75rem; color: var(--text-secondary); line-height: 1.55; }

    /* ─── Link rows ─── */
    .link-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0,51,127,0.06);
        text-decoration: none;
        color: inherit;
        cursor: pointer;
    }
    html:not(.light-mode) .link-row { border-bottom-color: rgba(255,255,255,0.06); }
    .link-row:last-child { border-bottom: 0; }
    .link-row__title { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); }
    .link-row__sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem; }
    .link-row:hover .link-row__title { color: #00337F; }
    html:not(.light-mode) .link-row:hover .link-row__title { color: #5b9aff; }
    .link-row .bi { color: var(--text-muted); font-size: 0.875rem; }

    /* ─── Locked / Coming-soon sections ─── */
    .is-locked { position: relative; }
    .is-locked .card-body-locked {
        opacity: 0.42;
        pointer-events: none;
        user-select: none;
    }
    .locked-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        background: rgba(91,154,255,0.12);
        border: 1px solid rgba(91,154,255,0.25);
        color: #5b9aff;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.5625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    html.light-mode .locked-badge {
        background: rgba(0,51,127,0.06);
        border-color: rgba(0,51,127,0.15);
        color: #00337F;
    }

    /* ─── Password match indicator ─── */
    .pf-pw-msg {
        font-size: 0.75rem;
        margin-top: 0.3rem;
        display: none;
        align-items: center;
        gap: 0.3rem;
    }

    /* ─── Stagger Animation ─── */
    @keyframes pf-rise {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .bento > * { animation: pf-rise 0.45s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .bento > *:nth-child(1)  { animation-delay: 0.03s; }
    .bento > *:nth-child(2)  { animation-delay: 0.07s; }
    .bento > *:nth-child(3)  { animation-delay: 0.11s; }
    .bento > *:nth-child(4)  { animation-delay: 0.15s; }
    .bento > *:nth-child(5)  { animation-delay: 0.19s; }
    .bento > *:nth-child(6)  { animation-delay: 0.23s; }
    .bento > *:nth-child(7)  { animation-delay: 0.27s; }
    .bento > *:nth-child(8)  { animation-delay: 0.31s; }
    .bento > *:nth-child(9)  { animation-delay: 0.35s; }
    .bento > *:nth-child(10) { animation-delay: 0.39s; }
    .bento > *:nth-child(11) { animation-delay: 0.43s; }
    @media (prefers-reduced-motion: reduce) { .bento > * { animation: none; } }
</style>
@endpush

@section('content')
@php
    $level = $user->level ?? 1;
    $points = $user->points ?? 0;
    $ringPct = isset($levelProgress) ? max(0, min(100, (int) $levelProgress)) : 0;
    $nextPts = $nextLevelPoints ?? 0;
    $streakDays = $user->streak_days ?? 0;

    $solvedCount = $solvedTotal ?? 0;
    $wrongCount = $wrongTotal ?? 0;
    $accuracy = $hitRate ?? null;
    $totalQuestions = $totalQuestions ?? 0;
    $streakDaysArr = $streakDaysArr ?? [];

    $totalOwnedAccessories = count($ownedAccessories['accessories'] ?? [])
        + count($ownedAccessories['top'] ?? [])
        + count($ownedAccessories['facialHair'] ?? []);
@endphp

<div class="dash-container">

    <div class="pf-page-title" style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
        <div>
            <div class="pf-page-title__eyebrow">Dein Konto</div>
            <h1 class="pf-page-title__h1">Profil</h1>
            <div class="pf-page-title__sub">Avatar, Fortschritt und Achievements.</div>
        </div>
        <a href="{{ route('einstellungen') }}" class="pf-settings-gear" title="Einstellungen" aria-label="Einstellungen">
            <i class="bi bi-gear-fill"></i>
        </a>
    </div>

    @if (session('status') && session('status') === 'avatar-updated')
    <div class="alert-compact glass-success" style="margin-bottom: 1rem;">
        <i class="bi bi-check-circle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">Avatar aktualisiert.</div>
        </div>
        <button style="background:none;border:none;cursor:pointer;font-size:1.25rem;color:var(--text-secondary);" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif

    {{-- ── Bento Grid ── --}}
    <div class="bento">

        {{-- ══════════════════════════════════════════
             HERO / IDENTITY (span 12)
        ══════════════════════════════════════════ --}}
        <div class="glass card-identity card hero-card">
            <div class="avatar-ring" style="--ring-pct: {{ $ringPct }};">
                <div class="avatar-ring__inner">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" id="pf-avatar-img">
                </div>
                <div class="avatar-ring__lvl">Lvl {{ $level }}</div>
                <button type="button" class="avatar-regen-btn" id="pf-regen-btn" title="Avatar neu generieren">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
            </div>

            <div>
                <div class="hero-eyebrow">Helfer · THW-Trainer</div>
                <h2 class="hero-name">{{ $user->name }}</h2>
                <div class="hero-email">{{ $user->email }}</div>
                <div class="hero-meta">
                    @if($user->pending_email)
                        <span class="chip chip--warn"><i class="bi bi-clock-history"></i> E-Mail ausstehend</span>
                    @elseif($user->hasVerifiedEmail())
                        <span class="chip chip--ok"><i class="bi bi-patch-check-fill"></i> Verifiziert</span>
                    @else
                        <span class="chip chip--warn"><i class="bi bi-exclamation-circle"></i> Nicht verifiziert</span>
                    @endif
                    <span class="chip chip--blue"><i class="bi bi-calendar3"></i> Seit {{ $user->created_at->format('d.m.Y') }}</span>
                    <span class="chip chip--gold"><i class="bi bi-fire"></i> {{ $streakDays }} {{ $streakDays === 1 ? 'Tag' : 'Tage' }} Streak</span>
                </div>
            </div>

            <div class="hero-actions">
                <a href="{{ route('profile.avatar.edit') }}" class="btn-pf-ghost"><i class="bi bi-palette"></i> Avatar anpassen</a>
            </div>

            <div class="hero-xp">
                <div class="hero-xp__row">
                    <span class="hero-xp__label">Letzte 14 Tage</span>
                    <span class="hero-xp__values">
                        <strong>{{ $points }}</strong> XP
                        @if($nextPts > 0)
                            · noch {{ $nextPts }} XP bis Level {{ $level + 1 }}
                        @else
                            · Max-Level erreicht
                        @endif
                    </span>
                </div>
                <div class="streak-dots" role="list" aria-label="Letzte 14 Tage Lernaktivität">
                    @forelse($streakDaysArr as $day)
                        <span class="streak-dot streak-dot--{{ $day['state'] }}" role="listitem" title="{{ $day['label'] }} · @if($day['state']==='learned')Gelernt @elseif($day['state']==='freeze')Freeze @else Nicht gelernt @endif">
                            @if($day['state'] === 'learned')
                                <i class="bi bi-fire"></i>
                            @elseif($day['state'] === 'freeze')
                                <i class="bi bi-snow"></i>
                            @endif
                        </span>
                    @empty
                        <span class="streak-dots__empty">Starte dein erstes Training, um hier deine Aktivität zu sehen.</span>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             LEARNING STATS (span 8)
        ══════════════════════════════════════════ --}}
        <div class="glass card-learning card">
            <div class="card-head" style="display:flex;align-items:center;justify-content:space-between;">
                <span class="section-label">Lernstatistik · Gesamtübersicht</span>
                <a href="{{ route('practice.menu') }}" style="font-size:0.8125rem;color:var(--thw-blue);text-decoration:none;font-weight:500;">Detail-Statistiken →</a>
            </div>
            <div>
                <div class="stat-pills" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-bottom: 1.25rem;">
                    <div class="stat-pill">
                        <div class="stat-pill__value stat-pill__value--ok">{{ $solvedCount }}</div>
                        <div class="stat-pill__label">Gelöst</div>
                    </div>
                    <div class="stat-pill">
                        <div class="stat-pill__value">{{ $accuracy !== null ? $accuracy.'%' : '—' }}</div>
                        <div class="stat-pill__label">Genauigkeit</div>
                    </div>
                    <div class="stat-pill">
                        <div class="stat-pill__value stat-pill__value--err">{{ $wrongCount }}</div>
                        <div class="stat-pill__label">Fehler</div>
                    </div>
                    <div class="stat-pill">
                        <div class="stat-pill__value">{{ $totalQuestions }}</div>
                        <div class="stat-pill__label">Gesamt</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ══════════════════════════════════════════
             ACHIEVEMENTS (span 6)
        ══════════════════════════════════════════ --}}
        @php
            $achievementsList = $achievements ?? [];
            $unlockedCount = collect($achievementsList)->where('unlocked', true)->count();
            $totalAchievements = count($achievementsList);
            $achSorted = collect($achievementsList)->sortByDesc('unlocked')->values();
            $achVisible = $achSorted->take(7);
        @endphp
        <div class="glass card-achievements card">
            <div class="card-head">
                <span class="section-label">Achievements · {{ $unlockedCount }} / {{ $totalAchievements }} freigeschaltet</span>
            </div>
            <div>
                <div class="ach-grid ach-grid--seven">
                    @foreach($achVisible as $ach)
                        <div class="ach {{ $ach['unlocked'] ? 'ach--unlocked' : 'ach--locked' }}" title="{{ $ach['title'] }} – {{ $ach['description'] }}">
                            <i class="bi {{ $ach['icon'] }}"></i>
                        </div>
                    @endforeach
                    <a href="{{ route('gamification.achievements') }}" class="ach ach--more" title="Alle Achievements ansehen">
                        <i class="bi bi-three-dots"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Account-Verwaltung (entfernt - siehe /einstellungen) --}}

        {{-- Sicherheit (entfernt - siehe /einstellungen) --}}

        {{-- Benachrichtigungen, Privatsphäre (entfernt - siehe /einstellungen) --}}

        {{-- ══════════════════════════════════════════
             AVATAR-ACCESSOIRES (span 12) — REAL
        ══════════════════════════════════════════ --}}
        <div class="glass card-accessories card" id="accessories-card" x-data="accessoryManager()">
            <div class="card-head">
                <span class="section-label">Avatar-Accessoires</span>
                <span class="card-head__hint">{{ $totalOwnedAccessories }} besessen · <a href="{{ route('shop.index') }}" style="color: var(--thw-blue, #00337F); text-decoration: none; font-weight: 600;">Shop →</a></span>
            </div>

            @if($totalOwnedAccessories > 0)
                @if(count($ownedAccessories['accessories'] ?? []) > 0)
                <div class="acc-section-label">Brillen</div>
                <div class="acc-row">
                    @foreach($ownedAccessories['accessories'] as $slug => $acc)
                    <button type="button" class="acc" :class="activeItems.accessories === '{{ $acc['accessory_value'] }}' && 'acc--on'"
                            @click="toggle('{{ $slug }}', '{{ $acc['accessory_value'] }}', 'accessories')" :disabled="toggling">
                        <img src="https://dicebear.niclas-reutter.de/9.x/avataaars/svg?radius=50&seed=Preview&backgroundType=gradientLinear&backgroundColor=00337f,0055cc&backgroundRotation=135&accessories={{ $acc['accessory_value'] }}&accessoriesProbability=100&eyes=default&mouth=smile" alt="{{ $acc['name'] }}" class="acc__preview" loading="lazy">
                        {{ $acc['name'] }}
                    </button>
                    @endforeach
                </div>
                <template x-if="activeItems.accessories">
                    <div class="acc-colors">
                        @php $glassColors = ['000000', '4a312c', '5b9aff', 'fbbf24', 'ef4444']; @endphp
                        @foreach($glassColors as $hex)
                        <div class="acc-color-dot" :class="activeItems.accessoriesColor === '{{ $hex }}' && 'is-selected'"
                             style="background:#{{ $hex }};" @click="changeColor(activeSlug('accessories'), '{{ $hex }}')"></div>
                        @endforeach
                    </div>
                </template>
                @endif

                @if(count($ownedAccessories['top'] ?? []) > 0)
                <div class="acc-section-label">Kopfbedeckungen</div>
                <div class="acc-row">
                    @foreach($ownedAccessories['top'] as $slug => $acc)
                    <button type="button" class="acc" :class="activeItems.top === '{{ $acc['accessory_value'] }}' && 'acc--on'"
                            @click="toggle('{{ $slug }}', '{{ $acc['accessory_value'] }}', 'top')" :disabled="toggling">
                        <img src="https://dicebear.niclas-reutter.de/9.x/avataaars/svg?radius=50&seed=Preview&backgroundType=gradientLinear&backgroundColor=00337f,0055cc&backgroundRotation=135&top={{ $acc['accessory_value'] }}&eyes=default&mouth=smile" alt="{{ $acc['name'] }}" class="acc__preview" loading="lazy">
                        {{ $acc['name'] }}
                    </button>
                    @endforeach
                </div>
                @endif

                @if(count($ownedAccessories['facialHair'] ?? []) > 0)
                <div class="acc-section-label">Bärte</div>
                <div class="acc-row">
                    @foreach($ownedAccessories['facialHair'] as $slug => $acc)
                    <button type="button" class="acc" :class="activeItems.facialHair === '{{ $acc['accessory_value'] }}' && 'acc--on'"
                            @click="toggle('{{ $slug }}', '{{ $acc['accessory_value'] }}', 'facialHair')" :disabled="toggling">
                        <img src="https://dicebear.niclas-reutter.de/9.x/avataaars/svg?radius=50&seed=Preview&backgroundType=gradientLinear&backgroundColor=00337f,0055cc&backgroundRotation=135&facialHair={{ $acc['accessory_value'] }}&facialHairProbability=100&eyes=default&mouth=smile" alt="{{ $acc['name'] }}" class="acc__preview" loading="lazy">
                        {{ $acc['name'] }}
                    </button>
                    @endforeach
                </div>
                <template x-if="activeItems.facialHair">
                    <div class="acc-colors">
                        @php $beardColors = ['2c1b18', '4a312c', '724133', 'a55728', 'b58143', 'c93305', 'd6b370', '000000']; @endphp
                        @foreach($beardColors as $hex)
                        <div class="acc-color-dot" :class="activeItems.facialHairColor === '{{ $hex }}' && 'is-selected'"
                             style="background:#{{ $hex }};" @click="changeColor(activeSlug('facialHair'), '{{ $hex }}')"></div>
                        @endforeach
                    </div>
                </template>
                @endif
            @else
                <div class="acc-empty">
                    Noch keine Accessoires. <a href="{{ route('shop.index') }}">Besuche den Shop!</a>
                </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════
             VERWALTUNGSÄNDERUNGEN (DSGVO Audit-Log)
        ══════════════════════════════════════════ --}}
        <div class="glass card" id="audit-log-card" style="grid-column: 1 / -1;">
            <div class="card-head">
                <span class="section-label"><i class="bi bi-shield-lock" style="margin-right: 0.4rem;"></i>Verwaltungsänderungen</span>
            </div>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 1rem; line-height: 1.5;">
                Admins können deine Daten zur Systemverwaltung einsehen und ändern — gemäß Art. 6 Abs. 1 lit. f DSGVO.
                Hier siehst du alle Änderungen, die von Administratoren an deinem Konto vorgenommen wurden.
            </p>

            @if($auditLogs->isEmpty())
                <div style="text-align: center; padding: 2rem 1rem; color: var(--text-muted);">
                    <i class="bi bi-inbox" style="font-size: 1.75rem; display: block; margin-bottom: 0.5rem; opacity: 0.4;"></i>
                    <div style="font-weight: 600; color: var(--text-secondary);">Keine Verwaltungsänderungen vorhanden</div>
                    <div style="font-size: 0.8125rem; margin-top: 0.25rem;">Dein Konto wurde bisher nicht durch Admins bearbeitet.</div>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($auditLogs as $log)
                    @php
                        $adminName = $log->admin ? explode(' ', $log->admin->name)[0] : 'Admin';
                        $actionVerbMap = [
                            'mastered'  => 'als gemeistert markiert',
                            'sr'        => 'zur Wiederholung gesetzt',
                            'reset'     => 'zurückgesetzt',
                            'increment' => '+1 Streak',
                        ];
                        $actionLabel = match($log->action) {
                            'update_field'   => match($log->field) {
                                'name'   => 'Name geändert',
                                'email'  => 'E-Mail geändert',
                                'role'   => 'Rolle geändert',
                                'points' => 'Punkte (XP) geändert',
                                default  => ucfirst($log->field ?? '') . ' geändert',
                            },
                            'verify_email'             => 'E-Mail verifiziert',
                            'delete'                   => 'Konto gelöscht',
                            'reset_progress'           => 'Lernfortschritt zurückgesetzt',
                            'update_progress'          => 'Lernfortschritt bearbeitet',
                            'update_progress_question' => 'Einzelne Frage ' . ($actionVerbMap[$log->new_value] ?? 'bearbeitet'),
                            'update_progress_module'   => 'Lernabschnitt/Lehrgang ' . ($actionVerbMap[explode(' ', $log->new_value ?? '')[0]] ?? 'bearbeitet'),
                            'view_progress_detail'     => 'Lernfortschritt eingesehen',
                            'view_xp_history'          => 'XP-Verlauf eingesehen',
                            default                    => ucfirst(str_replace('_', ' ', $log->action)),
                        };
                        $hasDetail = $log->action === 'update_field' && ($log->old_value !== null || $log->new_value !== null);
                    @endphp
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--bg-elevated, rgba(255,255,255,0.03)); border-radius: 0.5rem; border: 1px solid rgba(0,51,127,0.06);">
                        <div style="flex-shrink: 0; margin-top: 0.1rem;">
                            @if($log->action === 'delete')
                                <i class="bi bi-trash-fill" style="color: var(--error); font-size: 0.875rem;"></i>
                            @elseif($log->action === 'verify_email')
                                <i class="bi bi-envelope-check-fill" style="color: var(--success); font-size: 0.875rem;"></i>
                            @elseif($log->action === 'reset_progress')
                                <i class="bi bi-arrow-counterclockwise" style="color: var(--warning, #f59e0b); font-size: 0.875rem;"></i>
                            @elseif(in_array($log->action, ['update_progress', 'update_progress_question', 'update_progress_module']))
                                <i class="bi bi-graph-up" style="color: var(--thw-blue); font-size: 0.875rem;"></i>
                            @elseif(in_array($log->action, ['view_progress_detail', 'view_xp_history']))
                                <i class="bi bi-eye" style="color: var(--text-muted); font-size: 0.875rem;"></i>
                            @else
                                <i class="bi bi-pencil-fill" style="color: var(--text-muted); font-size: 0.875rem;"></i>
                            @endif
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">
                                {{ $actionLabel }}
                                <span style="font-weight: 400; color: var(--text-muted);">durch {{ $adminName }}</span>
                            </div>
                            @if($hasDetail)
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; font-family: 'IBM Plex Mono', monospace;">
                                @if($log->old_value !== null)
                                    <span style="background: rgba(239,68,68,0.08); color: var(--error); padding: 0.1rem 0.4rem; border-radius: 0.25rem;">{{ $log->old_value }}</span>
                                    &nbsp;→&nbsp;
                                @endif
                                @if($log->new_value !== null)
                                    <span style="background: rgba(34,197,94,0.10); color: var(--success); padding: 0.1rem 0.4rem; border-radius: 0.25rem;">{{ $log->new_value }}</span>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div style="flex-shrink: 0; font-size: 0.75rem; color: var(--text-muted); font-family: 'IBM Plex Mono', monospace; white-space: nowrap;">
                            {{ $log->created_at->format('d.m.Y H:i') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>{{-- /.bento --}}
</div>{{-- /.dash-container --}}

<script>
    function accessoryManager() {
        return {
            activeItems: @json($user->active_accessories ?? (object)[]),
            toggling: false,
            slugMap: @json(collect($ownedAccessories)->flatMap(function($items) { return collect($items)->mapWithKeys(function($item, $slug) { return [$item['accessory_value'] => $slug]; }); })),

            activeSlug(paramName) {
                var val = this.activeItems[paramName];
                return val ? (this.slugMap[val] || '') : '';
            },

            async toggle(slug, value, paramName) {
                if (this.toggling) return;
                this.toggling = true;
                try {
                    var response = await fetch('{{ route("profile.accessory.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ accessory_slug: slug }),
                        cache: 'no-store',
                    });
                    var data = await response.json();
                    if (data.success) {
                        var active = {};
                        ['accessories', 'top', 'facialHair'].forEach(function(type) {
                            var items = data.owned_accessories[type] || {};
                            Object.values(items).forEach(function(item) {
                                if (item.is_active) active[item.accessory_type] = item.accessory_value;
                            });
                        });
                        if (data.avatar_url) {
                            var url = new URL(data.avatar_url);
                            var accColor = url.searchParams.get('accessoriesColor');
                            var fhColor = url.searchParams.get('facialHairColor');
                            if (accColor) active.accessoriesColor = accColor;
                            if (fhColor) active.facialHairColor = fhColor;
                        }
                        // Bestehendes reactive Object mutieren statt ersetzen (Alpine-Reaktivität)
                        var self = this;
                        Object.keys(self.activeItems).forEach(function(k) { delete self.activeItems[k]; });
                        Object.keys(active).forEach(function(k) { self.activeItems[k] = active[k]; });
                        document.getElementById('pf-avatar-img').src = data.avatar_url + '&_t=' + Date.now();
                    }
                } catch (e) { console.error('Accessory toggle error:', e); }
                finally { this.toggling = false; }
            },

            async changeColor(slug, color) {
                if (!slug || this.toggling) return;
                this.toggling = true;
                try {
                    var response = await fetch('{{ route("profile.accessory.color") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ accessory_slug: slug, color: color }),
                        cache: 'no-store',
                    });
                    var data = await response.json();
                    if (data.success) {
                        var url = new URL(data.avatar_url);
                        var accColor = url.searchParams.get('accessoriesColor');
                        var fhColor = url.searchParams.get('facialHairColor');
                        if (accColor) this.activeItems.accessoriesColor = accColor;
                        if (fhColor) this.activeItems.facialHairColor = fhColor;
                        document.getElementById('pf-avatar-img').src = data.avatar_url + '&_t=' + Date.now();
                    }
                } catch (e) { console.error('Color change error:', e); }
                finally { this.toggling = false; }
            }
        };
    }

    // Avatar regeneration
    var regenBtn = document.getElementById('pf-regen-btn');
    if (regenBtn) {
        regenBtn.addEventListener('click', function() {
            var btn = this;
            var img = document.getElementById('pf-avatar-img');
            if (btn.disabled) return;
            btn.classList.add('is-spinning');
            btn.disabled = true;
            fetch('{{ route("profile.avatar.regenerate") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) { if (data.avatar_url) img.src = data.avatar_url + '&_t=' + Date.now(); })
            .catch(function(e) { console.error('Avatar error:', e); })
            .finally(function() {
                setTimeout(function() {
                    btn.classList.remove('is-spinning');
                    btn.disabled = false;
                }, 600);
            });
        });
    }

</script>
@endsection
