<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIPP · Sistema de Cálculo y Proyección de Pobreza')</title>
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-alt: #f1f5f9;
            --text: #0f172a;
            --text-muted: #475569;
            --border: #cbd5e1;
            --input-border: #64748b;
            --accent: #0f766e;
            --accent-hover: #115e59;
            --accent-soft: #ccfbf1;
            --danger: #b91c1c;
            --danger-bg: #fef2f2;
            --success: #15803d;
            --warning: #b45309;
            --info: #0369a1;
            --radius: 0.75rem;
            --shadow: 0 4px 16px -2px rgb(15 23 42 / 0.08);
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.55;
            color: var(--text);
            background-color: var(--bg);
        }

        a {
            color: var(--accent);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        :focus-visible {
            outline: 3px solid #0b57d0;
            outline-offset: 2px;
        }

        /* Visually hidden utility keeping elements in the accessibility tree */
        .visually-hidden:where(:not(:focus-within, :active)) {
            position: absolute !important;
            clip-path: inset(50%) !important;
            overflow: hidden !important;
            width: 1px !important;
            height: 1px !important;
            margin: -1px !important;
            padding: 0 !important;
            border: 0 !important;
            white-space: nowrap !important;
        }

        /* Responsive Site Navigation (modern-web-guidance: navbar) */
        .site-header {
            background: var(--surface);
            border-block-end: 1px solid var(--border);
            position: sticky;
            inset-block-start: 0;
            z-index: 30;
        }

        .header-inner {
            max-inline-size: 1280px;
            margin-inline: auto;
            padding-block: 0.75rem;
            padding-inline: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .header-inner > * {
            flex-shrink: 0;
            white-space: nowrap;
        }

        .site-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            font-weight: 800;
            font-size: 1.125rem;
            color: var(--text);
            text-decoration: none;
        }

        .site-logo-badge {
            background: var(--accent);
            color: #fff;
            padding: 0.25rem 0.55rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            letter-spacing: 0.04em;
        }

        .site-header .menu-button {
            anchor-name: --menu-button;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 0.9rem;
            min-block-size: 48px;
            border: 1px solid var(--input-border);
            border-radius: 0.5rem;
            background: var(--surface);
            color: var(--text);
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }

        .site-header .site-menu {
            position: fixed;
            inset: auto;
            inset-block-start: 4.5rem;
            inset-block-start: anchor(--menu-button bottom);
            inset-inline-end: 1rem;
            inline-size: min(85dvw, 22rem);
            max-inline-size: calc(100dvw - 2rem);
            block-size: fit-content;
            max-block-size: calc(100dvh - 6rem);
            margin-block-start: 0.5rem;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface);
            box-shadow: var(--shadow);
            list-style: none;
            overflow: auto;
        }

        [popover]:is(:popover-open, .\:popover-open) {
            display: block;
        }

        .site-menu li {
            margin: 0;
        }

        .site-menu a, .site-menu button.nav-link-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.55rem 0.8rem;
            min-block-size: 44px;
            border-radius: 0.5rem;
            color: var(--text);
            font-weight: 500;
            text-decoration: none;
            position: relative;
        }

        .site-menu a:hover {
            background: var(--surface-alt);
        }

        .site-menu a[aria-current="page"] {
            color: var(--accent);
            font-weight: 700;
        }

        .site-menu a[aria-current="page"]::after {
            content: "";
            position: absolute;
            inset-inline: 0.6rem;
            inset-block-end: 0.2rem;
            border-block-end: 3px solid var(--accent);
        }

        .site-header.is-wide .menu-button {
            display: none;
        }

        .site-header.is-wide .site-menu {
            position: static;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            inline-size: auto;
            max-inline-size: none;
            block-size: auto;
            max-block-size: none;
            overflow: visible;
            margin: 0;
            padding: 0;
            border: 0;
            background: transparent;
            box-shadow: none;
        }

        .site-header.is-wide .site-menu::backdrop {
            display: none;
        }

        /* Layout Containers */
        .container {
            max-inline-size: 1280px;
            margin-inline: auto;
            padding-block: 2rem;
            padding-inline: 1.25rem;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-block-end: 1.5rem;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr));
            gap: 1.5rem;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));
            gap: 1.25rem;
        }

        .grid-5 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 210px), 1fr));
            gap: 1rem;
        }

        /* Forms & Validation After Interaction (modern-web-guidance: forms, validate-input-after-interaction) */
        fieldset {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem;
            margin-block-end: 1.5rem;
            background: var(--surface);
        }

        legend {
            font-weight: 700;
            font-size: 1.05rem;
            padding-inline: 0.5rem;
            color: var(--text);
        }

        .field {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
            margin-block-end: 1.25rem;
        }

        .field label {
            font-weight: 600;
            color: var(--text);
            display: inline-block;
            padding: 6px 0;
            margin: -6px 0;
        }

        .hint {
            display: block;
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-block-end: 0.2rem;
        }

        input:not([type="checkbox"], [type="radio"]),
        select,
        textarea {
            inline-size: 100%;
            font: inherit;
            font-size: max(1rem, 1em);
            padding: 0.65rem 0.85rem;
            min-height: 48px;
            border: 1.5px solid #767676;
            border-radius: 0.5rem;
            background-color: #fff;
            color: var(--text);
        }

        @media (pointer: coarse) {
            input:not([type="checkbox"], [type="radio"]),
            select,
            textarea,
            .btn {
                min-height: 52px;
            }
        }

        input[type="checkbox"],
        input[type="radio"] {
            accent-color: var(--accent);
            inline-size: 1.25rem;
            block-size: 1.25rem;
        }

        .error-msg {
            display: none;
            color: var(--danger);
            font-size: 0.875rem;
            margin-block-start: 0.25rem;
            font-weight: 500;
        }

        :is(:user-invalid, .user-invalid-fallback) {
            border-color: #d93025 !important;
            background-color: #fce8e6 !important;
        }

        :is(:user-invalid, .user-invalid-fallback) + .error-msg {
            display: block;
        }

        input:user-valid {
            border-color: #188038;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 48px;
            padding: 0.65rem 1.25rem;
            border-radius: 0.5rem;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--accent);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            text-decoration: none;
        }

        .btn-outline {
            background: var(--surface);
            color: var(--text);
            border-color: #767676;
        }

        .btn-outline:hover {
            background: var(--surface-alt);
            text-decoration: none;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            font-size: 0.825rem;
            font-weight: 700;
            color: #fff;
        }

        /* Responsive Table (modern-web-guidance: responsive-table) */
        .table-wrapper {
            overflow: auto;
            max-inline-size: 100%;
            max-block-size: min(560px, 80vh);
            container-type: inline-size;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface);
        }

        .table-wrapper table {
            inline-size: 100%;
            border-collapse: separate;
            border-spacing: 0;
            text-align: start;
        }

        .table-wrapper caption {
            padding: 0.85rem 1rem;
            font-weight: 700;
            text-align: start;
            background: var(--surface-alt);
            border-block-end: 1px solid var(--border);
        }

        .table-wrapper thead {
            position: sticky;
            inset-block-start: 0;
            z-index: 1;
            background: var(--surface-alt);
        }

        .table-wrapper th,
        .table-wrapper td {
            padding: 0.75rem 1rem;
            border-block-end: 1px solid var(--border);
            white-space: nowrap;
        }

        .table-wrapper tbody th {
            position: sticky;
            inset-inline-start: 0;
            background: var(--surface);
            font-weight: 700;
        }

        @container (width < 600px) {
            .table-wrapper table,
            .table-wrapper thead,
            .table-wrapper tbody,
            .table-wrapper tr,
            .table-wrapper th,
            .table-wrapper td {
                display: block;
                white-space: normal;
            }

            .table-wrapper thead {
                display: none;
            }

            .table-wrapper tr {
                margin-block-end: 1rem;
                border: 1px solid var(--border);
            }

            .table-wrapper tbody th {
                position: sticky;
                inset-block-start: 0;
                z-index: 2;
                background: var(--surface-alt);
            }

            .table-wrapper td:nth-child(2)::before {
                content: var(--label-2, "Col 2") ": ";
                font-weight: 700;
            }
            .table-wrapper td:nth-child(3)::before {
                content: var(--label-3, "Col 3") ": ";
                font-weight: 700;
            }
            .table-wrapper td:nth-child(4)::before {
                content: var(--label-4, "Col 4") ": ";
                font-weight: 700;
            }
            .table-wrapper td:nth-child(5)::before {
                content: var(--label-5, "Col 5") ": ";
                font-weight: 700;
            }
            .table-wrapper td:nth-child(6)::before {
                content: var(--label-6, "Col 6") ": ";
                font-weight: 700;
            }
        }

        /* BMI-like Poverty Index Gauge */
        .bmi-scale-bar {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            border-radius: 0.5rem;
            overflow: hidden;
            margin-block: 0.75rem;
            border: 1px solid var(--border);
        }

        .bmi-segment {
            padding: 0.5rem;
            text-align: center;
            color: #fff;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .alert-status {
            padding: 1rem 1.25rem;
            border-radius: 0.5rem;
            background: var(--accent-soft);
            color: #134e4a;
            border: 1px solid #99f6e4;
            margin-block-end: 1.5rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header class="site-header is-wide" id="site-header">
        <div class="header-inner" id="header-inner">
            <a class="site-logo" href="{{ route('portal.home') }}">
                <span class="site-logo-badge" aria-hidden="true">SIPP</span>
                <span>Índice y Proyección de Pobreza</span>
            </a>

            <nav class="site-nav" aria-label="Navegación Principal">
                <button
                    class="menu-button"
                    type="button"
                    popovertarget="site-menu"
                    popovertargetaction="toggle"
                    aria-controls="site-menu"
                >
                    <span>Menú</span>
                </button>

                <ul id="site-menu" class="site-menu">
                    <li>
                        <a href="{{ route('portal.home') }}" @if(request()->routeIs('portal.home')) aria-current="page" @endif>
                            Inicio y Escala
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('calculator.index') }}" @if(request()->routeIs('calculator.*')) aria-current="page" @endif>
                            Calculadora (Tipo IMC)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('temporal.index') }}" @if(request()->routeIs('temporal.*')) aria-current="page" @endif>
                            Análisis Temporal y Pronósticos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('import.index') }}" @if(request()->routeIs('import.*')) aria-current="page" @endif>
                            Importación Masiva (ENIGH)
                        </a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('portal.my-history') }}" @if(request()->routeIs('portal.my-history')) aria-current="page" @endif>
                                Mi Panel ({{ auth()->user()->name }})
                            </a>
                        </li>
                        <li>
                            <a href="/admin" style="font-weight: 700; color: var(--accent);">
                                Panel FilamentPHP ↗
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('login') }}" @if(request()->routeIs('login')) aria-current="page" @endif>
                                Iniciar Sesión
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('portal.register') }}" @if(request()->routeIs('portal.register')) aria-current="page" @endif>
                                Registrarse
                            </a>
                        </li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        @if (session('status'))
            <div class="alert-status" role="status">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer style="border-block-start: 1px solid var(--border); background: var(--surface); padding: 1.5rem 1.25rem; margin-block-start: 3rem; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
        <div>
            <strong>Sistema Integral de Cálculo, Análisis Temporal y Proyección de Pobreza (SIPP)</strong> · Motor Multidimensional y Marco Técnico de Política Pública.
        </div>
        <div style="margin-block-start: 0.35rem;">
            Administración backend gestionada con <a href="/admin">FilamentPHP (/admin)</a> · Basado en la especificación técnica de evaluación socioeconómica.
        </div>
    </footer>

    <script type="module">
        // 1. Conditional Popover Polyfill (modern-web-guidance: navbar)
        if (!Object.hasOwn(HTMLElement.prototype, "popover")) {
            import("https://unpkg.com/@oddbird/popover-polyfill@latest/dist/popover.min.js").catch(() => {});
        }

        // 2. Responsive Navbar width detection (modern-web-guidance: navbar)
        const header = document.getElementById('site-header');
        const inner = document.getElementById('header-inner');
        const menu = document.getElementById('site-menu');
        let lastHeaderWidth = 0;

        const updateNavLayout = () => {
            if (!header || !inner || !menu) return;
            const currentWidth = header.clientWidth;
            const wasWide = header.classList.contains('is-wide');
            if (!wasWide && currentWidth <= lastHeaderWidth) {
                lastHeaderWidth = currentWidth;
                return;
            }
            lastHeaderWidth = currentWidth;

            header.classList.add('is-wide');
            menu.removeAttribute('popover');
            const fits = inner.scrollWidth <= inner.clientWidth;

            if (!fits) {
                header.classList.remove('is-wide');
                menu.setAttribute('popover', 'auto');
            }
        };

        if (window.ResizeObserver && header) {
            const ro = new ResizeObserver(() => updateNavLayout());
            ro.observe(header);
            updateNavLayout();
        }

        // 3. Validate Input After Interaction & ARIA sync (modern-web-guidance: validate-input-after-interaction)
        const linkError = (el, shown) => {
            const errorId = el.dataset?.error;
            if (!errorId) return;
            const ids = (el.getAttribute('aria-describedby') || '').split(/\s+/).filter((id) => id && id !== errorId);
            if (shown) ids.push(errorId);
            if (ids.length) el.setAttribute('aria-describedby', ids.join(' '));
            else el.removeAttribute('aria-describedby');
        };

        const announcer = document.createElement('div');
        announcer.setAttribute('aria-live', 'polite');
        announcer.classList.add('visually-hidden');
        document.body.append(announcer);
        let announceTimer;
        let announcedField;

        const spokenText = (node) => {
            const copy = node.cloneNode(true);
            copy.querySelectorAll('[aria-hidden="true"]').forEach((n) => n.remove());
            copy.querySelectorAll('[role="img"][aria-label]').forEach((n) => n.replaceWith(`${n.getAttribute('aria-label')}: `));
            return copy.textContent.replace(/\s+/g, ' ').trim();
        };

        const announceError = (el) => {
            const error = document.getElementById(el.dataset.error);
            if (!error) return;
            const label = el.labels?.[0] ? `${spokenText(el.labels[0])}: ` : '';
            clearTimeout(announceTimer);
            announcer.textContent = '';
            announcedField = el;
            announceTimer = setTimeout(() => { announcer.textContent = label + spokenText(error); }, 500);
        };

        const flagged = new WeakSet();
        const announced = new WeakSet();
        const syncAria = (el, announce = false) => {
            if (!el.matches?.('input, select, textarea')) return;
            let invalid = false;
            try {
                invalid = el.matches(':user-invalid') || el.classList.contains('user-invalid-fallback');
            } catch {
                invalid = el.classList.contains('user-invalid-fallback');
            }
            if (invalid) {
                if (announce && !announced.has(el)) {
                    announceError(el);
                    announced.add(el);
                }
                el.setAttribute('aria-invalid', 'true');
                linkError(el, true);
                flagged.add(el);
            } else if (flagged.has(el) || el.getAttribute('aria-invalid') !== 'true') {
                el.setAttribute('aria-invalid', 'false');
                linkError(el, false);
                flagged.delete(el);
                announced.delete(el);
                if (announcedField === el) {
                    clearTimeout(announceTimer);
                    announcer.textContent = '';
                }
            }
        };

        document.querySelectorAll('input, select, textarea').forEach((el) => syncAria(el));
        document.addEventListener('blur', (e) => syncAria(e.target, true), true);
        document.addEventListener('input', (e) => {
            if (e.target.hasAttribute?.('aria-invalid')) syncAria(e.target);
        });
        document.addEventListener('change', (e) => syncAria(e.target, true), true);
        document.addEventListener('invalid', (e) => {
            announced.add(e.target);
            syncAria(e.target);
        }, true);
    </script>
    @stack('scripts')
</body>
</html>
