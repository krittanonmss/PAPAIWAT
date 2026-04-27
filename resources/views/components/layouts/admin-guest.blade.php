<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Login' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="m-0 bg-[#0f1b2e] font-sans text-white antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden p-6">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-[#0f1b2e] via-[#142238] to-[#1c1736]"></div>
        <div class="pointer-events-none absolute left-[-12%] top-[22%] h-80 w-80 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="pointer-events-none absolute right-[-10%] top-[35%] h-96 w-96 rounded-full bg-purple-600/20 blur-3xl"></div>

        <section
            class="relative z-10 w-full max-w-md rounded-2xl border border-white/10 bg-white/[0.04] p-8 shadow-[0_24px_80px_rgba(0,0,0,0.28)] backdrop-blur-xl"
            aria-label="{{ $title ?? 'Admin Login' }}"
        >
            {{ $slot }}
        </section>
    </main>
</body>
</html>