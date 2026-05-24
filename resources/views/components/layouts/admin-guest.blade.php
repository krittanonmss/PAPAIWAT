<!DOCTYPE html>
@php
    $guestTheme = request()->cookie('papaiwat_admin_theme', 'dark');
    $guestTheme = in_array($guestTheme, ['dark', 'light', 'system'], true) ? $guestTheme : 'dark';
@endphp
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Login' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --admin-scale: 80%;
        }

        html {
            font-size: var(--admin-scale);
        }

        body {
            font-weight: 300;
        }

        .font-medium {
            font-weight: 400 !important;
        }

        .font-semibold {
            font-weight: 500 !important;
        }

        .font-bold {
            font-weight: 600 !important;
        }

        .text-slate-400,
        .text-gray-400,
        .text-zinc-400 {
            color: rgb(203 213 225) !important;
        }

        .text-slate-500,
        .text-gray-500,
        .text-zinc-500 {
            color: rgb(148 163 184) !important;
        }

        body[data-admin-theme="light"],
        body[data-admin-theme="system"].prefers-light {
            background: #f8fafc !important;
            color: #020617;
        }

        body[data-admin-theme="light"] .admin-guest-bg,
        body[data-admin-theme="system"].prefers-light .admin-guest-bg {
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%) !important;
        }

        body[data-admin-theme="light"] .admin-guest-card,
        body[data-admin-theme="system"].prefers-light .admin-guest-card {
            border-color: rgba(15, 23, 42, 0.14) !important;
            background: rgba(255, 255, 255, 0.88) !important;
            color: #020617;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.12) !important;
        }

        body[data-admin-theme="light"] .text-white,
        body[data-admin-theme="system"].prefers-light .text-white {
            color: #020617 !important;
        }

        body[data-admin-theme="light"] .text-slate-300,
        body[data-admin-theme="light"] .text-slate-400,
        body[data-admin-theme="system"].prefers-light .text-slate-300,
        body[data-admin-theme="system"].prefers-light .text-slate-400 {
            color: #1e293b !important;
        }

        body[data-admin-theme="light"] .text-slate-500,
        body[data-admin-theme="system"].prefers-light .text-slate-500 {
            color: #334155 !important;
        }

        body[data-admin-theme="light"] input,
        body[data-admin-theme="system"].prefers-light input {
            border-color: rgba(15, 23, 42, 0.16) !important;
            background: rgba(255, 255, 255, 0.92) !important;
            color: #020617 !important;
        }

        body[data-admin-theme="light"] [class*="border-white/"],
        body[data-admin-theme="system"].prefers-light [class*="border-white/"] {
            border-color: rgba(15, 23, 42, 0.14) !important;
        }

        body[data-admin-theme="light"] [class*="bg-white/"],
        body[data-admin-theme="system"].prefers-light [class*="bg-white/"] {
            background-color: rgba(255, 255, 255, 0.74) !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.body.dataset.adminTheme = @js($guestTheme);

            if (@js($guestTheme) === 'system' && window.matchMedia('(prefers-color-scheme: light)').matches) {
                document.body.classList.add('prefers-light');
            }
        });
    </script>
</head>
<body class="m-0 bg-[#0f1b2e] font-sans text-white antialiased" data-admin-theme="{{ $guestTheme }}">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden p-6">
        <div class="admin-guest-bg pointer-events-none absolute inset-0 bg-gradient-to-br from-[#0f1b2e] via-[#142238] to-[#1c1736]"></div>
        <div class="pointer-events-none absolute left-[-12%] top-[22%] h-80 w-80 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="pointer-events-none absolute right-[-10%] top-[35%] h-96 w-96 rounded-full bg-purple-600/20 blur-3xl"></div>

        <section
            class="admin-guest-card relative z-10 w-full max-w-md rounded-2xl border border-white/10 bg-white/[0.04] p-8 shadow-[0_24px_80px_rgba(0,0,0,0.28)] backdrop-blur-xl"
            aria-label="{{ $title ?? 'Admin Login' }}"
        >
            {{ $slot }}
        </section>
    </main>
</body>
</html>
