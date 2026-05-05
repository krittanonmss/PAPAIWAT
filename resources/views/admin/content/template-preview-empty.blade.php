<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Preview</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <main class="flex min-h-screen items-center justify-center px-6">
        <section class="max-w-lg rounded-2xl border border-white/10 bg-white/[0.04] p-6 text-center shadow-xl shadow-slate-950/30">
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-300">Preview unavailable</p>
            <h1 class="mt-3 text-2xl font-semibold text-white">ยังไม่มีข้อมูลจริงสำหรับ preview</h1>
            <p class="mt-3 text-sm leading-6 text-slate-400">
                Preview นี้แสดงเฉพาะข้อมูลจาก database เท่านั้น ตอนนี้ยังไม่พบ {{ $type === 'temple' ? 'วัด' : 'บทความ' }} ในระบบ
                ให้สร้างรายการจริงก่อน แล้วกลับมาเลือก template อีกครั้ง
            </p>
        </section>
    </main>
</body>
</html>
