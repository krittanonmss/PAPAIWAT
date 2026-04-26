<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'PAPAIWAT' }}</title>

    {{-- SEO --}}
    <meta name="description" content="{{ $metaDescription ?? 'PAPAIWAT Platform' }}">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">

    {{-- Header --}}
    @include('frontend.partials.header')

    {{-- Main Content --}}
    <main class="min-h-screen">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('frontend.partials.footer')

</body>
</html>