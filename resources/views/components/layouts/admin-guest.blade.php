<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Login' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="m-0 bg-gray-100 font-sans text-gray-800">
    <main class="flex min-h-screen items-center justify-center p-6">
        <section
            class="w-full max-w-md rounded-xl border border-gray-200 bg-white p-8 shadow-[0_10px_30px_rgba(0,0,0,0.06)]"
            aria-label="{{ $title ?? 'Admin Login' }}"
        >
            {{ $slot }}
        </section>
    </main>
</body>
</html>