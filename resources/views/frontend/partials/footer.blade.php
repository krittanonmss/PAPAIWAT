<footer class="mt-16 border-t border-white/10 bg-white/[0.03] backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 py-10">
        <div class="grid gap-8 md:grid-cols-3">
            <div>
                <h3 class="text-lg font-semibold text-white">PAPAIWAT</h3>
                <p class="mt-3 text-sm leading-6 text-slate-400">
                    แพลตฟอร์มรวบรวมข้อมูลวัด สถานที่ศักดิ์สิทธิ์ และวัฒนธรรมไทย
                </p>
            </div>

            <div>
                <h4 class="text-sm font-medium text-slate-200">เมนู</h4>
                <ul class="mt-3 space-y-2 text-sm text-slate-400">
                    <li><a href="{{ route('home') }}" class="hover:text-white">หน้าแรก</a></li>
                    <li><a href="{{ url('/temple-list') }}" class="hover:text-white">วัด</a></li>
                    <li><a href="{{ url('/articles') }}" class="hover:text-white">บทความ</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-medium text-slate-200">ข้อมูล</h4>
                <ul class="mt-3 space-y-2 text-sm text-slate-400">
                    <li><a href="{{ url('/about') }}" class="hover:text-white">เกี่ยวกับเรา</a></li>
                    <li><a href="{{ url('/contact') }}" class="hover:text-white">ติดต่อ</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-10 border-t border-white/10 pt-6 text-center text-xs text-slate-500">
            © {{ date('Y') }} PAPAIWAT. All rights reserved.
        </div>
    </div>
</footer>