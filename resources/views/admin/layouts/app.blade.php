<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') · BAHNES IT SOLUTIONS</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @endif

    <style>
        :root { color-scheme: light; font-family: Inter, ui-sans-serif, system-ui, sans-serif; color: #172033; background: #f5f6f8; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; }
        input, button, select, textarea { font: inherit; }
        a { color: inherit; }
        .admin-shell { width: min(100% - 2rem, 76rem); margin-inline: auto; padding-block: 3rem; }
        .admin-card { background: white; border: 1px solid #e2e6ed; border-radius: 1rem; padding: clamp(1.25rem, 4vw, 2rem); box-shadow: 0 12px 36px #1720330a; }
        .admin-field { display: grid; gap: .4rem; margin-block: 1rem; }
        .admin-input { width: 100%; padding: .75rem .85rem; border: 1px solid #cbd2de; border-radius: .55rem; background: #fff; }
        .admin-button { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; border: 0; border-radius: .55rem; background: #172033; color: white; padding: .8rem 1rem; font-weight: 600; cursor: pointer; text-decoration: none; }
        .admin-button:focus-visible, .admin-input:focus-visible { outline: 3px solid #55d6be; outline-offset: 2px; }
        .admin-muted { color: #667085; }
        .admin-error { color: #b42318; font-size: .9rem; }
        .admin-status { border: 1px solid #a6f4c5; background: #ecfdf3; color: #05603a; padding: .75rem; border-radius: .5rem; }
        .admin-select, .admin-textarea { width: 100%; padding: .75rem .85rem; border: 1px solid #cbd2de; border-radius: .55rem; background: #fff; }
        .admin-textarea { min-height: 7rem; resize: vertical; }
        .admin-table-wrap { overflow-x: auto; width: 100%; }
        .admin-table { border-collapse: collapse; width: 100%; font-size: .94rem; }
        .admin-table th, .admin-table td { border-bottom: 1px solid #e2e6ed; padding: .8rem .7rem; text-align: left; vertical-align: top; }
        .admin-table th { color: #667085; white-space: nowrap; font-size: .78rem; letter-spacing: .04em; text-transform: uppercase; }
        .admin-link { text-decoration: underline; text-underline-offset: .2em; text-decoration-color: #98a2b3; }
        .admin-badge { display: inline-flex; align-items: center; border-radius: 999px; padding: .2rem .55rem; background: #f2f4f7; color: #344054; font-size: .78rem; font-weight: 600; }
        .admin-badge-published { background: #ecfdf3; color: #05603a; }
        .admin-badge-archived { background: #fef3f2; color: #912018; }
        .admin-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .admin-section-heading { margin-block: 1.5rem .5rem; padding-bottom: .5rem; border-bottom: 1px solid #e2e6ed; font-weight: 700; }
        .admin-flash { margin-bottom: 1rem; }
        @media (max-width: 640px) { .admin-grid { grid-template-columns: 1fr; } .admin-table th, .admin-table td { padding: .7rem .5rem; } }

    </style>
</head>
<body>
    <main class="admin-shell">
        @yield('content')
    </main>
</body>
</html>
