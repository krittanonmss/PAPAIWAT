<section class="relative overflow-hidden py-20">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-950/40 via-slate-950 to-indigo-950/40"></div>

    <div class="relative mx-auto max-w-6xl px-4 text-center">
        <p class="text-sm font-medium text-blue-300">
            {{ $content['eyebrow'] ?? 'PAPAIWAT' }}
        </p>

        <h1 class="mt-4 text-4xl font-bold tracking-tight text-white md:text-6xl">
            {{ $content['title'] ?? $settings['title'] ?? 'ค้นหาวัดทั่วประเทศไทย' }}
        </h1>

        <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-300">
            {{ $content['subtitle'] ?? $settings['subtitle'] ?? 'สำรวจวัด สถานที่ศักดิ์สิทธิ์ และวัฒนธรรมไทย' }}
        </p>

        <div class="mt-8 flex justify-center">
            <a
                href="{{ url('/temple-list') }}"
                class="rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
            >
                เริ่มค้นหาวัด
            </a>
        </div>
    </div>
</section>