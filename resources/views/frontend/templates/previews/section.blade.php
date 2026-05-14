<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section Preview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --frontend-zoom: 0.8; }
        html, body { margin: 0; min-height: 100%; background: #020617; }
        body { zoom: var(--frontend-zoom); font-weight: 300; }
        .font-medium { font-weight: 400 !important; }
        .font-semibold { font-weight: 500 !important; }
        .font-bold { font-weight: 600 !important; }
        .text-slate-400, .text-gray-400, .text-zinc-400 { color: rgb(203 213 225) !important; }
        .text-slate-500, .text-gray-500, .text-zinc-500 { color: rgb(148 163 184) !important; }
    </style>
</head>
<body class="bg-slate-950 text-white">
    @foreach($sections as $section)
        @include('frontend.templates.sections._renderer', ['section' => $section])
    @endforeach
</body>
</html>
