    <style>
        [x-cloak] { display: none !important; }

        :root {
            --admin-bg: #020617;
            --admin-sidebar: #0b1220;
            --admin-surface: rgba(255, 255, 255, 0.04);
            --admin-surface-strong: rgba(15, 23, 42, 0.42);
            --admin-border: rgba(255, 255, 255, 0.10);
            --admin-muted: #94a3b8;
            --admin-blue: #60a5fa;
            --admin-scale: 80%;
            --admin-density-padding: 1.5rem;
        }

        html {
            font-size: var(--admin-scale);
        }

        body {
            font-weight: 300;
            background:
                radial-gradient(circle at top right, rgba(30, 64, 175, 0.18), transparent 28rem),
                linear-gradient(180deg, #020617 0%, #0f172a 100%);
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

        .admin-content {
            background:
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.08), transparent 24rem),
                rgba(2, 6, 23, 0.64);
        }

        .admin-content > div > :first-child {
            overflow: hidden;
            border: 1px solid var(--admin-border) !important;
            border-radius: 1.5rem !important;
            background:
                linear-gradient(90deg, rgba(15, 23, 42, 0.96), rgba(15, 23, 42, 0.94), rgba(49, 46, 129, 0.88)) !important;
            box-shadow: 0 20px 42px rgba(2, 6, 23, 0.24) !important;
            backdrop-filter: blur(16px);
        }

        .admin-content > div > :first-child:not([class*=" p-"]):not([class^="p-"]):not([class*=" px-"]):not([class^="px-"]) {
            padding: var(--admin-density-padding);
        }

        .admin-content > div > :first-child h1 {
            color: #fff;
        }

        .admin-content > div > :first-child > div:first-child {
            border-color: var(--admin-border);
        }

        .admin-content section,
        .admin-content aside,
        .admin-content form,
        .admin-content บทความ,
        .admin-content div {
            border-color: var(--admin-border);
        }

        .admin-content input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        .admin-content select,
        .admin-content textarea {
            border-color: var(--admin-border) !important;
            background-color: rgba(2, 6, 23, 0.42) !important;
            color: #fff;
        }

        .admin-content select {
            color-scheme: dark;
        }

        .admin-content select option,
        .admin-content select optgroup {
            background-color: #0f172a !important;
            color: #ffffff !important;
        }

        .admin-content input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):focus,
        .admin-content select:focus,
        .admin-content textarea:focus {
            border-color: rgba(96, 165, 250, 0.9) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.18) !important;
            outline: none;
        }

        .admin-content table thead {
            background: rgba(2, 6, 23, 0.34);
            color: #94a3b8;
        }

        .admin-content table tbody tr:hover {
            background: rgba(255, 255, 255, 0.06);
        }

        .admin-content a,
        .admin-content button {
            transition-property: color, background-color, border-color, opacity, transform, box-shadow;
            transition-duration: 150ms;
        }

        .admin-content .shadow-xl {
            box-shadow: 0 20px 42px rgba(2, 6, 23, 0.24) !important;
        }

        body[data-admin-theme="light"],
        body[data-admin-theme="system"].prefers-light {
            background:
                radial-gradient(circle at top right, rgba(37, 99, 235, 0.10), transparent 28rem),
                linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
            color: #020617;
        }

        body[data-admin-theme="light"] aside,
        body[data-admin-theme="system"].prefers-light aside,
        body[data-admin-theme="light"] header,
        body[data-admin-theme="system"].prefers-light header {
            border-color: rgba(15, 23, 42, 0.12) !important;
            background: rgba(255, 255, 255, 0.92) !important;
            color: #020617;
        }

        body[data-admin-theme="light"] .admin-content,
        body[data-admin-theme="system"].prefers-light .admin-content {
            background:
                radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 24rem),
                rgba(248, 250, 252, 0.84);
        }

        body[data-admin-theme="light"] .admin-content > div > :first-child,
        body[data-admin-theme="system"].prefers-light .admin-content > div > :first-child {
            border-color: rgba(15, 23, 42, 0.12) !important;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.94), rgba(219, 234, 254, 0.9)) !important;
            box-shadow: 0 20px 42px rgba(15, 23, 42, 0.10) !important;
        }

        body[data-admin-theme="light"] .text-white,
        body[data-admin-theme="system"].prefers-light .text-white {
            color: #020617 !important;
        }

        body[data-admin-theme="light"] .text-slate-100,
        body[data-admin-theme="light"] .text-gray-100,
        body[data-admin-theme="system"].prefers-light .text-slate-100,
        body[data-admin-theme="system"].prefers-light .text-gray-100 {
            color: #020617 !important;
        }

        body[data-admin-theme="light"] .text-slate-200,
        body[data-admin-theme="light"] .text-slate-300,
        body[data-admin-theme="light"] .text-gray-300,
        body[data-admin-theme="system"].prefers-light .text-slate-200,
        body[data-admin-theme="system"].prefers-light .text-slate-300,
        body[data-admin-theme="system"].prefers-light .text-gray-300 {
            color: #0f172a !important;
        }

        body[data-admin-theme="light"] .text-slate-400,
        body[data-admin-theme="light"] .text-gray-400,
        body[data-admin-theme="light"] .text-zinc-400,
        body[data-admin-theme="system"].prefers-light .text-slate-400,
        body[data-admin-theme="system"].prefers-light .text-gray-400,
        body[data-admin-theme="system"].prefers-light .text-zinc-400 {
            color: #1e293b !important;
        }

        body[data-admin-theme="light"] .text-slate-500,
        body[data-admin-theme="light"] .text-gray-500,
        body[data-admin-theme="light"] .text-zinc-500,
        body[data-admin-theme="system"].prefers-light .text-slate-500,
        body[data-admin-theme="system"].prefers-light .text-gray-500,
        body[data-admin-theme="system"].prefers-light .text-zinc-500 {
            color: #334155 !important;
        }

        body[data-admin-theme="light"] .text-blue-100,
        body[data-admin-theme="light"] .text-blue-200,
        body[data-admin-theme="light"] .text-blue-300,
        body[data-admin-theme="system"].prefers-light .text-blue-100,
        body[data-admin-theme="system"].prefers-light .text-blue-200,
        body[data-admin-theme="system"].prefers-light .text-blue-300 {
            color: #1d4ed8 !important;
        }

        body[data-admin-theme="light"] .text-emerald-100,
        body[data-admin-theme="light"] .text-emerald-200,
        body[data-admin-theme="light"] .text-emerald-300,
        body[data-admin-theme="system"].prefers-light .text-emerald-100,
        body[data-admin-theme="system"].prefers-light .text-emerald-200,
        body[data-admin-theme="system"].prefers-light .text-emerald-300 {
            color: #047857 !important;
        }

        body[data-admin-theme="light"] .text-amber-100,
        body[data-admin-theme="light"] .text-amber-200,
        body[data-admin-theme="light"] .text-amber-300,
        body[data-admin-theme="light"] .text-yellow-100,
        body[data-admin-theme="light"] .text-yellow-200,
        body[data-admin-theme="light"] .text-yellow-300,
        body[data-admin-theme="system"].prefers-light .text-amber-100,
        body[data-admin-theme="system"].prefers-light .text-amber-200,
        body[data-admin-theme="system"].prefers-light .text-amber-300,
        body[data-admin-theme="system"].prefers-light .text-yellow-100,
        body[data-admin-theme="system"].prefers-light .text-yellow-200,
        body[data-admin-theme="system"].prefers-light .text-yellow-300 {
            color: #92400e !important;
        }

        body[data-admin-theme="light"] .text-red-100,
        body[data-admin-theme="light"] .text-red-200,
        body[data-admin-theme="light"] .text-red-300,
        body[data-admin-theme="light"] .text-rose-100,
        body[data-admin-theme="light"] .text-rose-200,
        body[data-admin-theme="light"] .text-rose-300,
        body[data-admin-theme="system"].prefers-light .text-red-100,
        body[data-admin-theme="system"].prefers-light .text-red-200,
        body[data-admin-theme="system"].prefers-light .text-red-300,
        body[data-admin-theme="system"].prefers-light .text-rose-100,
        body[data-admin-theme="system"].prefers-light .text-rose-200,
        body[data-admin-theme="system"].prefers-light .text-rose-300 {
            color: #991b1b !important;
        }

        body[data-admin-theme="light"] [class*="border-white/"],
        body[data-admin-theme="system"].prefers-light [class*="border-white/"] {
            border-color: rgba(15, 23, 42, 0.12) !important;
        }

        body[data-admin-theme="light"] [class*="bg-white/"],
        body[data-admin-theme="system"].prefers-light [class*="bg-white/"] {
            background-color: rgba(255, 255, 255, 0.74) !important;
        }

        body[data-admin-theme="light"] [class*="bg-slate-950"],
        body[data-admin-theme="light"] [class*="bg-slate-900"],
        body[data-admin-theme="light"] [class*="bg-slate-800"],
        body[data-admin-theme="light"] [class*="bg-[#0f1727]"],
        body[data-admin-theme="system"].prefers-light [class*="bg-slate-950"],
        body[data-admin-theme="system"].prefers-light [class*="bg-slate-900"],
        body[data-admin-theme="system"].prefers-light [class*="bg-slate-800"],
        body[data-admin-theme="system"].prefers-light [class*="bg-[#0f1727]"] {
            background-color: rgba(241, 245, 249, 0.78) !important;
        }

        body[data-admin-theme="light"] [class*="from-slate-900"][class*="to-indigo-950"],
        body[data-admin-theme="system"].prefers-light [class*="from-slate-900"][class*="to-indigo-950"] {
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.96), rgba(219, 234, 254, 0.92)) !important;
        }

        body[data-admin-theme="light"] [class*="from-blue-600"][class*="to-indigo-600"],
        body[data-admin-theme="light"] [class*="bg-blue-600"],
        body[data-admin-theme="light"] [class*="bg-blue-700"],
        body[data-admin-theme="light"] [class*="bg-blue-900"],
        body[data-admin-theme="light"] [class*="bg-emerald-600"],
        body[data-admin-theme="light"] [class*="bg-red-600"],
        body[data-admin-theme="light"] [class*="bg-amber-500"],
        body[data-admin-theme="system"].prefers-light [class*="from-blue-600"][class*="to-indigo-600"],
        body[data-admin-theme="system"].prefers-light [class*="bg-blue-600"],
        body[data-admin-theme="system"].prefers-light [class*="bg-blue-700"],
        body[data-admin-theme="system"].prefers-light [class*="bg-blue-900"],
        body[data-admin-theme="system"].prefers-light [class*="bg-emerald-600"],
        body[data-admin-theme="system"].prefers-light [class*="bg-red-600"],
        body[data-admin-theme="system"].prefers-light [class*="bg-amber-500"] {
            color: #fff !important;
        }

        body[data-admin-theme="light"] [class*="bg-blue-900"],
        body[data-admin-theme="system"].prefers-light [class*="bg-blue-900"] {
            background-color: #1d4ed8 !important;
            border-color: rgba(37, 99, 235, 0.34) !important;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.18) !important;
        }

        body[data-admin-theme="light"] [class*="from-blue-600"][class*="to-indigo-600"],
        body[data-admin-theme="system"].prefers-light [class*="from-blue-600"][class*="to-indigo-600"] {
            background: linear-gradient(90deg, #2563eb, #4f46e5) !important;
            box-shadow: 0 12px 26px rgba(37, 99, 235, 0.22) !important;
        }

        body[data-admin-theme="light"] .admin-content input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        body[data-admin-theme="light"] .admin-content select,
        body[data-admin-theme="light"] .admin-content textarea,
        body[data-admin-theme="system"].prefers-light .admin-content input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        body[data-admin-theme="system"].prefers-light .admin-content select,
        body[data-admin-theme="system"].prefers-light .admin-content textarea {
            border-color: rgba(15, 23, 42, 0.14) !important;
            background-color: rgba(255, 255, 255, 0.86) !important;
            color: #0f172a !important;
        }

        body[data-admin-theme="light"] .admin-content select,
        body[data-admin-theme="system"].prefers-light .admin-content select {
            color-scheme: light;
        }

        body[data-admin-theme="light"] .admin-content select option,
        body[data-admin-theme="light"] .admin-content select optgroup,
        body[data-admin-theme="system"].prefers-light .admin-content select option,
        body[data-admin-theme="system"].prefers-light .admin-content select optgroup {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }

        body[data-admin-theme="light"] .admin-content select option:checked,
        body[data-admin-theme="system"].prefers-light .admin-content select option:checked {
            background-color: #dbeafe !important;
            color: #0f172a !important;
        }

        body[data-admin-theme="light"] .admin-content table thead,
        body[data-admin-theme="system"].prefers-light .admin-content table thead {
            background: rgba(226, 232, 240, 0.72);
            color: #475569;
        }

        body[data-admin-theme="light"] .admin-content table tbody tr:hover,
        body[data-admin-theme="system"].prefers-light .admin-content table tbody tr:hover {
            background: rgba(15, 23, 42, 0.04);
        }
    </style>
