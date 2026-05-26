    <script>
        document.body.dataset.adminTheme = @js($adminTheme);
        document.body.dataset.adminDensity = @js($adminDensity);
        document.body.dataset.adminDetailNewTab = @js($adminOpenDetailInNewTab ? '1' : '0');
        document.body.dataset.adminReducedMotion = @js($adminReducedMotion ? '1' : '0');
        document.body.dataset.adminHighContrast = @js($adminHighContrast ? '1' : '0');

        if (@js($adminTheme) === 'system' && window.matchMedia('(prefers-color-scheme: light)').matches) {
            document.body.classList.add('prefers-light');
        }
    </script>

    <style>
        :root {
            --admin-scale: {{ $adminScale }}%;
        }

        @if ($adminDensity === 'compact')
            body[data-admin-density="compact"] .admin-content {
                --admin-density-padding: 1rem;
                padding: 1rem !important;
            }

            body[data-admin-density="compact"] header > div {
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }

            body[data-admin-density="compact"] aside > div > div:first-child {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }

            body[data-admin-density="compact"] aside > div > div:nth-child(2),
            body[data-admin-density="compact"] aside > div > div:last-child {
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }

            body[data-admin-density="compact"] aside nav {
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }

            body[data-admin-density="compact"] aside nav > * + * {
                margin-top: 0.5rem !important;
            }

            body[data-admin-density="compact"] aside nav a,
            body[data-admin-density="compact"] aside nav summary {
                padding-top: 0.5rem !important;
                padding-bottom: 0.5rem !important;
            }

            body[data-admin-density="compact"] .admin-content .p-6 {
                padding: 1rem !important;
            }

            body[data-admin-density="compact"] .admin-content .p-5 {
                padding: 0.875rem !important;
            }

            body[data-admin-density="compact"] .admin-content .px-6 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            body[data-admin-density="compact"] .admin-content .py-6 {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }

            body[data-admin-density="compact"] .admin-content .space-y-5 > :not([hidden]) ~ :not([hidden]),
            body[data-admin-density="compact"] .admin-content .space-y-6 > :not([hidden]) ~ :not([hidden]) {
                margin-top: 0.875rem !important;
            }

            body[data-admin-density="compact"] .admin-content .gap-5,
            body[data-admin-density="compact"] .admin-content .gap-6 {
                gap: 0.875rem !important;
            }

            body[data-admin-density="compact"] .admin-content table th,
            body[data-admin-density="compact"] .admin-content table td {
                padding-top: 0.625rem !important;
                padding-bottom: 0.625rem !important;
            }

            body[data-admin-density="compact"] .admin-content input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
            body[data-admin-density="compact"] .admin-content select,
            body[data-admin-density="compact"] .admin-content a[class~="py-2.5"],
            body[data-admin-density="compact"] .admin-content button[class~="py-2.5"] {
                padding-top: 0.5rem !important;
                padding-bottom: 0.5rem !important;
            }
        @endif

        @if ($adminReducedMotion)
            *, *::before, *::after {
                animation-duration: 0.001ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: 0.001ms !important;
            }
        @endif

        @if ($adminHighContrast)
            .admin-content {
                --admin-border: rgba(255, 255, 255, 0.24);
            }

            body[data-admin-theme="light"],
            body[data-admin-theme="system"].prefers-light {
                background: #ffffff !important;
                color: #000000 !important;
            }

            body[data-admin-theme="light"] aside,
            body[data-admin-theme="system"].prefers-light aside,
            body[data-admin-theme="light"] header,
            body[data-admin-theme="system"].prefers-light header {
                border-color: #111827 !important;
                background: #ffffff !important;
                color: #000000 !important;
                box-shadow: none !important;
            }

            body[data-admin-theme="light"] .admin-content,
            body[data-admin-theme="system"].prefers-light .admin-content {
                --admin-border: #111827;
                background: #ffffff !important;
            }

            body[data-admin-theme="light"] .admin-content > div > :first-child,
            body[data-admin-theme="system"].prefers-light .admin-content > div > :first-child,
            body[data-admin-theme="light"] .admin-content section,
            body[data-admin-theme="system"].prefers-light .admin-content section,
            body[data-admin-theme="light"] .admin-content aside,
            body[data-admin-theme="system"].prefers-light .admin-content aside,
            body[data-admin-theme="light"] .admin-content form,
            body[data-admin-theme="system"].prefers-light .admin-content form,
            body[data-admin-theme="light"] .admin-content details,
            body[data-admin-theme="system"].prefers-light .admin-content details,
            body[data-admin-theme="light"] .admin-content table {
                border-color: #111827 !important;
                background-color: #ffffff !important;
                background-image: none !important;
                box-shadow: none !important;
            }

            body[data-admin-theme="system"].prefers-light .admin-content table,
            body[data-admin-theme="light"] .admin-content [class*="bg-gradient"],
            body[data-admin-theme="system"].prefers-light .admin-content [class*="bg-gradient"],
            body[data-admin-theme="light"] .admin-content [class*="bg-white/"],
            body[data-admin-theme="system"].prefers-light .admin-content [class*="bg-white/"],
            body[data-admin-theme="light"] .admin-content [class*="bg-slate-950"],
            body[data-admin-theme="light"] .admin-content [class*="bg-slate-900"],
            body[data-admin-theme="light"] .admin-content [class*="bg-slate-800"],
            body[data-admin-theme="light"] .admin-content [class*="bg-[#0f1727]"],
            body[data-admin-theme="system"].prefers-light .admin-content [class*="bg-slate-950"],
            body[data-admin-theme="system"].prefers-light .admin-content [class*="bg-slate-900"],
            body[data-admin-theme="system"].prefers-light .admin-content [class*="bg-slate-800"],
            body[data-admin-theme="system"].prefers-light .admin-content [class*="bg-[#0f1727]"] {
                background-color: #ffffff !important;
                background-image: none !important;
                box-shadow: none !important;
            }

            body[data-admin-theme="light"] .text-white,
            body[data-admin-theme="light"] .text-slate-100,
            body[data-admin-theme="light"] .text-slate-200,
            body[data-admin-theme="light"] .text-slate-300,
            body[data-admin-theme="light"] .text-slate-400,
            body[data-admin-theme="light"] .text-slate-500,
            body[data-admin-theme="light"] .text-gray-100,
            body[data-admin-theme="light"] .text-gray-300,
            body[data-admin-theme="light"] .text-gray-400,
            body[data-admin-theme="light"] .text-gray-500,
            body[data-admin-theme="system"].prefers-light .text-white,
            body[data-admin-theme="system"].prefers-light .text-slate-100,
            body[data-admin-theme="system"].prefers-light .text-slate-200,
            body[data-admin-theme="system"].prefers-light .text-slate-300,
            body[data-admin-theme="system"].prefers-light .text-slate-400,
            body[data-admin-theme="system"].prefers-light .text-slate-500,
            body[data-admin-theme="system"].prefers-light .text-gray-100,
            body[data-admin-theme="system"].prefers-light .text-gray-300,
            body[data-admin-theme="system"].prefers-light .text-gray-400,
            body[data-admin-theme="system"].prefers-light .text-gray-500 {
                color: #000000 !important;
            }

            body[data-admin-theme="light"] [class*="bg-blue-600"],
            body[data-admin-theme="light"] [class*="bg-blue-700"],
            body[data-admin-theme="light"] [class*="bg-blue-900"],
            body[data-admin-theme="light"] [class*="bg-indigo-600"],
            body[data-admin-theme="light"] [class*="bg-indigo-700"],
            body[data-admin-theme="light"] [class*="bg-purple-600"],
            body[data-admin-theme="light"] [class*="bg-emerald-600"],
            body[data-admin-theme="light"] [class*="bg-red-600"],
            body[data-admin-theme="light"] [class*="from-blue-600"],
            body[data-admin-theme="light"] [class*="from-indigo-500"],
            body[data-admin-theme="light"] [class*="from-sky-500"],
            body[data-admin-theme="light"] [class*="from-purple-600"],
            body[data-admin-theme="system"].prefers-light [class*="bg-blue-600"],
            body[data-admin-theme="system"].prefers-light [class*="bg-blue-700"],
            body[data-admin-theme="system"].prefers-light [class*="bg-blue-900"],
            body[data-admin-theme="system"].prefers-light [class*="bg-indigo-600"],
            body[data-admin-theme="system"].prefers-light [class*="bg-indigo-700"],
            body[data-admin-theme="system"].prefers-light [class*="bg-purple-600"],
            body[data-admin-theme="system"].prefers-light [class*="bg-emerald-600"],
            body[data-admin-theme="system"].prefers-light [class*="bg-red-600"],
            body[data-admin-theme="system"].prefers-light [class*="from-blue-600"],
            body[data-admin-theme="system"].prefers-light [class*="from-indigo-500"],
            body[data-admin-theme="system"].prefers-light [class*="from-sky-500"],
            body[data-admin-theme="system"].prefers-light [class*="from-purple-600"] {
                border-color: #000000 !important;
                background-color: #000000 !important;
                background-image: none !important;
                box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px #000000 !important;
                color: #ffffff !important;
            }

            body[data-admin-theme="light"] .admin-content a[class*="from-blue-600"],
            body[data-admin-theme="light"] .admin-content button[class*="from-blue-600"],
            body[data-admin-theme="system"].prefers-light .admin-content a[class*="from-blue-600"],
            body[data-admin-theme="system"].prefers-light .admin-content button[class*="from-blue-600"] {
                border: 2px solid #000000 !important;
                background: #000000 !important;
                color: #ffffff !important;
                text-decoration: none !important;
            }

            body[data-admin-theme="light"] .admin-content a[class*="from-blue-600"]:hover,
            body[data-admin-theme="light"] .admin-content button[class*="from-blue-600"]:hover,
            body[data-admin-theme="system"].prefers-light .admin-content a[class*="from-blue-600"]:hover,
            body[data-admin-theme="system"].prefers-light .admin-content button[class*="from-blue-600"]:hover {
                background: #1f2937 !important;
            }

            body[data-admin-theme="light"] [class*="bg-amber-500"],
            body[data-admin-theme="light"] [class*="bg-yellow-400"],
            body[data-admin-theme="light"] [class*="bg-yellow-500"],
            body[data-admin-theme="system"].prefers-light [class*="bg-amber-500"],
            body[data-admin-theme="system"].prefers-light [class*="bg-yellow-400"],
            body[data-admin-theme="system"].prefers-light [class*="bg-yellow-500"] {
                color: #000000 !important;
            }

            body[data-admin-theme="light"] .admin-content input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
            body[data-admin-theme="light"] .admin-content select,
            body[data-admin-theme="light"] .admin-content textarea,
            body[data-admin-theme="system"].prefers-light .admin-content input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
            body[data-admin-theme="system"].prefers-light .admin-content select,
            body[data-admin-theme="system"].prefers-light .admin-content textarea {
                border-color: #111827 !important;
                background: #ffffff !important;
                color: #000000 !important;
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
                color: #000000 !important;
            }

            body[data-admin-theme="light"] .admin-content select option:checked,
            body[data-admin-theme="system"].prefers-light .admin-content select option:checked {
                background-color: #bfdbfe !important;
                color: #000000 !important;
            }

            body[data-admin-theme="light"] .admin-content input:focus,
            body[data-admin-theme="light"] .admin-content select:focus,
            body[data-admin-theme="light"] .admin-content textarea:focus,
            body[data-admin-theme="system"].prefers-light .admin-content input:focus,
            body[data-admin-theme="system"].prefers-light .admin-content select:focus,
            body[data-admin-theme="system"].prefers-light .admin-content textarea:focus {
                outline: 3px solid #000000 !important;
                outline-offset: 2px;
                box-shadow: none !important;
            }
        @endif
    </style>
