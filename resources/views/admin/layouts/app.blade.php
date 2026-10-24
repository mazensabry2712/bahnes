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
        input, button { font: inherit; }
        a { color: inherit; }
        .admin-shell { width: min(100% - 2rem, 48rem); margin-inline: auto; padding-block: 3rem; }
        .admin-card { background: white; border: 1px solid #e2e6ed; border-radius: 1rem; padding: clamp(1.25rem, 4vw, 2rem); box-shadow: 0 12px 36px #1720330a; }
        .admin-field { display: grid; gap: .4rem; margin-block: 1rem; }
        .admin-input { width: 100%; padding: .75rem .85rem; border: 1px solid #cbd2de; border-radius: .55rem; background: #fff; }
        .admin-button { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; border: 0; border-radius: .55rem; background: #172033; color: white; padding: .8rem 1rem; font-weight: 600; cursor: pointer; text-decoration: none; }
        .admin-button:focus-visible, .admin-input:focus-visible { outline: 3px solid #55d6be; outline-offset: 2px; }
        .admin-muted { color: #667085; }
        .admin-error { color: #b42318; font-size: .9rem; }
        .admin-status { border: 1px solid #a6f4c5; background: #ecfdf3; color: #05603a; padding: .75rem; border-radius: .5rem; }
    </style>
</head>
<body>
    <main class="admin-shell">
        @yield('content')
    </main>
</body>
</html>
