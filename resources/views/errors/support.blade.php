<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} · AITROL</title>
    <style>
        :root {
            color-scheme: light;
            --page: #edf4f8;
            --surface: #ffffff;
            --ink: #102f47;
            --muted: #667f91;
            --line: #d8e5ec;
            --navy: #0b4c70;
            --blue: #087aa2;
            --teal: #11a6a5;
            --soft: #eef9fb;
            --warning: #f59e0b;
            --shadow: 0 24px 70px rgba(15, 57, 81, .16);
        }

        * { box-sizing: border-box; }

        html, body { min-height: 100%; }

        body {
            align-items: center;
            background:
                radial-gradient(circle at 10% 10%, rgba(17, 166, 165, .13), transparent 30%),
                radial-gradient(circle at 90% 90%, rgba(8, 122, 162, .14), transparent 32%),
                var(--page);
            color: var(--ink);
            display: flex;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            justify-content: center;
            margin: 0;
            padding: 32px 20px;
        }

        button, a { font: inherit; }

        .support-error {
            background: var(--surface);
            border: 1px solid rgba(139, 174, 193, .38);
            border-radius: 24px;
            box-shadow: var(--shadow);
            max-width: 920px;
            overflow: hidden;
            width: 100%;
        }

        .support-error__hero {
            background: linear-gradient(118deg, var(--navy) 0%, var(--blue) 60%, var(--teal) 100%);
            color: #fff;
            overflow: hidden;
            padding: 34px 38px 32px;
            position: relative;
        }

        .support-error__hero::before,
        .support-error__hero::after {
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 50%;
            content: "";
            position: absolute;
        }

        .support-error__hero::before { height: 240px; right: -75px; top: -130px; width: 240px; }
        .support-error__hero::after { bottom: -125px; height: 185px; right: 80px; width: 185px; }

        .support-error__brand {
            align-items: center;
            display: flex;
            gap: 10px;
            margin-bottom: 28px;
            position: relative;
            z-index: 1;
        }

        .support-error__brand-mark {
            align-items: center;
            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 10px;
            display: inline-flex;
            font-size: 15px;
            font-weight: 800;
            height: 38px;
            justify-content: center;
            letter-spacing: .05em;
            width: 38px;
        }

        .support-error__brand-name { font-size: 16px; font-weight: 800; letter-spacing: .16em; }
        .support-error__brand-subtitle { color: rgba(255, 255, 255, .72); display: block; font-size: 9px; letter-spacing: .2em; margin-top: 1px; }

        .support-error__headline {
            align-items: flex-start;
            display: flex;
            gap: 18px;
            max-width: 760px;
            position: relative;
            z-index: 1;
        }

        .support-error__icon {
            align-items: center;
            background: rgba(255, 255, 255, .13);
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 16px;
            display: inline-flex;
            flex: 0 0 62px;
            height: 62px;
            justify-content: center;
        }

        .support-error__icon svg { height: 29px; width: 29px; }
        .support-error__eyebrow { color: #a9f1ea; font-size: 11px; font-weight: 800; letter-spacing: .12em; margin: 1px 0 7px; text-transform: uppercase; }
        .support-error__hero h1 { font-size: clamp(25px, 4vw, 35px); line-height: 1.14; margin: 0 0 9px; }
        .support-error__hero p { color: rgba(255, 255, 255, .87); font-size: 15px; line-height: 1.65; margin: 0; }

        .support-error__body { display: grid; gap: 22px; grid-template-columns: minmax(0, 1.05fr) minmax(280px, .95fr); padding: 30px 38px 34px; }

        .support-error__section-title {
            color: var(--ink);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            margin: 0 0 11px;
            text-transform: uppercase;
        }

        .support-error__action {
            align-items: flex-start;
            background: #fff9eb;
            border: 1px solid #f8dc9a;
            border-radius: 14px;
            color: #684712;
            display: flex;
            gap: 12px;
            line-height: 1.55;
            padding: 15px;
        }

        .support-error__action-mark {
            align-items: center;
            background: #fef0c7;
            border-radius: 9px;
            color: #b87505;
            display: inline-flex;
            flex: 0 0 34px;
            font-weight: 900;
            height: 34px;
            justify-content: center;
        }

        .support-error__incident {
            background: var(--soft);
            border: 1px solid #cbe6eb;
            border-radius: 14px;
            margin-top: 18px;
            padding: 16px;
        }

        .support-error__incident-label { color: #547486; font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .support-error__incident-code { color: #075f7d; display: block; font-family: "SFMono-Regular", Consolas, "Liberation Mono", monospace; font-size: clamp(17px, 3vw, 22px); font-weight: 800; letter-spacing: .04em; margin: 7px 0; overflow-wrap: anywhere; }
        .support-error__incident-help { color: var(--muted); font-size: 12px; line-height: 1.5; margin: 0; }

        .support-error__details {
            border: 1px solid var(--line);
            border-radius: 14px;
            margin: 0;
            overflow: hidden;
        }

        .support-error__detail {
            align-items: flex-start;
            border-bottom: 1px solid var(--line);
            display: grid;
            gap: 10px;
            grid-template-columns: 118px minmax(0, 1fr);
            padding: 12px 14px;
        }

        .support-error__detail:last-child { border-bottom: 0; }
        .support-error__detail dt { color: var(--muted); font-size: 11px; font-weight: 700; }
        .support-error__detail dd { color: var(--ink); font-size: 12px; font-weight: 650; margin: 0; overflow-wrap: anywhere; text-align: right; }
        .support-error__security { color: var(--muted); font-size: 11px; line-height: 1.55; margin: 14px 0 0; }

        .support-error__footer {
            align-items: center;
            border-top: 1px solid var(--line);
            display: flex;
            gap: 10px;
            justify-content: space-between;
            padding: 20px 38px;
        }

        .support-error__buttons { display: flex; flex-wrap: wrap; gap: 9px; }
        .support-error__button { align-items: center; border: 1px solid #c9dce5; border-radius: 9px; color: #31566d; cursor: pointer; display: inline-flex; font-weight: 700; gap: 7px; justify-content: center; min-height: 40px; padding: 9px 13px; text-decoration: none; transition: background .15s ease, border-color .15s ease, transform .15s ease; }
        .support-error__button:hover { background: #f0f8fb; border-color: #9fc4d2; transform: translateY(-1px); }
        .support-error__button--primary { background: var(--blue); border-color: var(--blue); color: #fff; }
        .support-error__button--primary:hover { background: #066b90; border-color: #066b90; }
        .support-error__button--copy { background: #e6f7f5; border-color: #b8e5e0; color: #087871; }
        .support-error__copy-status { color: #087871; font-size: 12px; font-weight: 700; min-height: 18px; }

        @media (prefers-color-scheme: dark) {
            :root { color-scheme: dark; --page: #0e1d27; --surface: #142733; --ink: #e8f3f7; --muted: #a1b5c0; --line: #29414f; --soft: #173842; --shadow: 0 25px 75px rgba(0, 0, 0, .42); }
            .support-error__action { background: #372d19; border-color: #5d4921; color: #f9dda3; }
            .support-error__action-mark { background: #4b3918; color: #ffd77a; }
            .support-error__incident { border-color: #27606a; }
            .support-error__incident-code { color: #80e5dc; }
            .support-error__button { background: #172d39; border-color: #36505e; color: #d8e9ef; }
            .support-error__button--primary { background: #087aa2; border-color: #087aa2; color: #fff; }
            .support-error__button--copy { background: #163d3d; border-color: #28645f; color: #9aeee5; }
        }

        @media (max-width: 720px) {
            body { align-items: flex-start; padding: 14px; }
            .support-error { border-radius: 18px; }
            .support-error__hero { padding: 25px 22px; }
            .support-error__brand { margin-bottom: 22px; }
            .support-error__headline { gap: 13px; }
            .support-error__icon { flex-basis: 50px; height: 50px; }
            .support-error__icon svg { height: 24px; width: 24px; }
            .support-error__body { grid-template-columns: 1fr; padding: 24px 22px; }
            .support-error__footer { align-items: stretch; flex-direction: column; padding: 18px 22px; }
            .support-error__buttons { display: grid; grid-template-columns: 1fr 1fr; }
            .support-error__button--copy { grid-column: 1 / -1; }
            .support-error__copy-status { text-align: center; }
        }

        @media (max-width: 420px) {
            .support-error__headline { display: block; }
            .support-error__icon { margin-bottom: 14px; width: 50px; }
            .support-error__detail { display: block; }
            .support-error__detail dd { margin-top: 4px; text-align: left; }
            .support-error__buttons { grid-template-columns: 1fr; }
            .support-error__button--copy { grid-column: auto; }
        }
    </style>
</head>
<body>
    <main class="support-error" aria-labelledby="support-error-title">
        <header class="support-error__hero">
            <div class="support-error__brand" aria-label="AITROL Sistema Médico">
                <span class="support-error__brand-mark">A</span>
                <span><span class="support-error__brand-name">AITROL</span><span class="support-error__brand-subtitle">SISTEMA MÉDICO</span></span>
            </div>

            <div class="support-error__headline">
                <span class="support-error__icon" aria-hidden="true">
                    @if ($icon === 'configuration')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21h-4v-.09A1.7 1.7 0 0 0 8.6 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3v-4h.09A1.7 1.7 0 0 0 4.6 8.6a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V3h4v.09A1.7 1.7 0 0 0 15.4 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 9c.16.38.37.73.68 1 .3.25.68.4 1.1.4H21v4h-.09a1.7 1.7 0 0 0-1.51.6Z"/></svg>
                    @elseif ($icon === 'database')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v6c0 1.66 3.58 3 8 3s8-1.34 8-3V5M4 11v6c0 1.66 3.58 3 8 3s8-1.34 8-3v-6"/></svg>
                    @elseif ($icon === 'search')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m15.5 15.5 5 5"/></svg>
                    @elseif ($icon === 'clock')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    @elseif ($icon === 'screen')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10.3 3.4 2.2 18a2 2 0 0 0 1.75 3h16.1a2 2 0 0 0 1.75-3L13.7 3.4a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4M12 17h.01"/></svg>
                    @endif
                </span>
                <div>
                    <p class="support-error__eyebrow">{{ __('new_system.support_errors.eyebrow') }}</p>
                    <h1 id="support-error-title">{{ $title }}</h1>
                    <p>{{ $message }}</p>
                </div>
            </div>
        </header>

        <div class="support-error__body">
            <section>
                <h2 class="support-error__section-title">{{ __('new_system.support_errors.what_to_do') }}</h2>
                <div class="support-error__action"><span class="support-error__action-mark" aria-hidden="true">!</span><span>{{ $action }}</span></div>

                <div class="support-error__incident">
                    <span class="support-error__incident-label">{{ __('new_system.support_errors.incident_label') }}</span>
                    <code class="support-error__incident-code">{{ $incidentId }}</code>
                    <p class="support-error__incident-help">{{ __('new_system.support_errors.incident_help') }}</p>
                </div>
            </section>

            <section>
                <h2 class="support-error__section-title">{{ __('new_system.support_errors.incident_label') }}</h2>
                <dl class="support-error__details">
                    <div class="support-error__detail"><dt>{{ __('new_system.support_errors.occurred_at') }}</dt><dd>{{ $occurredAt }}</dd></div>
                    <div class="support-error__detail"><dt>{{ __('new_system.support_errors.request_path') }}</dt><dd>{{ $requestPath }}</dd></div>
                    <div class="support-error__detail"><dt>{{ __('new_system.support_errors.response_code') }}</dt><dd>HTTP {{ $status }}</dd></div>
                </dl>
                <p class="support-error__security">{{ __('new_system.support_errors.support_note') }}</p>
            </section>
        </div>

        <footer class="support-error__footer">
            <div class="support-error__buttons">
                <button class="support-error__button support-error__button--primary" type="button" onclick="window.location.reload()">↻ {{ __('new_system.support_errors.retry') }}</button>
                <button class="support-error__button" type="button" onclick="window.history.back()">← {{ __('new_system.support_errors.back') }}</button>
                <a class="support-error__button" href="{{ url('/') }}">⌂ {{ __('new_system.support_errors.home') }}</a>
                <button id="copy-support-incident" class="support-error__button support-error__button--copy" type="button">▣ {{ __('new_system.support_errors.copy') }}</button>
            </div>
            <span id="copy-support-status" class="support-error__copy-status" role="status" aria-live="polite"></span>
        </footer>
    </main>

    <pre id="support-incident-copy" hidden>{{ __('new_system.support_errors.incident_label') }}: {{ $incidentId }}
{{ __('new_system.support_errors.occurred_at') }}: {{ $occurredAt }}
{{ __('new_system.support_errors.request_path') }}: {{ $requestPath }}
{{ __('new_system.support_errors.response_code') }}: HTTP {{ $status }}
Problema: {{ $title }}</pre>

    <script>
        (function () {
            'use strict';

            var button = document.getElementById('copy-support-incident');
            var source = document.getElementById('support-incident-copy');
            var status = document.getElementById('copy-support-status');

            function copied() {
                status.textContent = @json(__('new_system.support_errors.copied'));
                window.setTimeout(function () { status.textContent = ''; }, 3000);
            }

            function fallbackCopy(text) {
                var field = document.createElement('textarea');
                field.value = text;
                field.setAttribute('readonly', '');
                field.style.position = 'fixed';
                field.style.opacity = '0';
                document.body.appendChild(field);
                field.select();
                document.execCommand('copy');
                document.body.removeChild(field);
                copied();
            }

            button.addEventListener('click', function () {
                var text = source.textContent.trim();

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(copied, function () { fallbackCopy(text); });
                    return;
                }

                fallbackCopy(text);
            });
        }());
    </script>
</body>
</html>
