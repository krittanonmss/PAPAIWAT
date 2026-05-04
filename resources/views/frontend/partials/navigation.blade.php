<nav aria-label="Main navigation">
    <ul class="flex items-center gap-5 text-sm text-slate-300">
        <li>
            <a href="{{ route('home') }}" class="hover:text-white">หน้าแรก</a>
        </li>
        <li>
            <a href="{{ url('/temple-list') }}" class="hover:text-white">วัด</a>
        </li>
        <li>
            <a href="{{ url('/article-list') }}" class="hover:text-white">บทความ</a>
        </li>
    </ul>
</nav>