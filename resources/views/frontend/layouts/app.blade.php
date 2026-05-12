<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $title ?? 'PAPAIWAT')</title>
    <meta name="description" content="@yield('meta_description', $metaDescription ?? 'PAPAIWAT Platform')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-white">
    @include('frontend.partials.header')

    <main class="min-h-screen">
        @yield('content')
    </main>

    @include('frontend.partials.footer')
</body>
</html>
