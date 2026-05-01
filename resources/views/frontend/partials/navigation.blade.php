@php
    use App\Support\MenuUrl;
@endphp

<nav class="flex items-center gap-6 text-sm" aria-label="เมนูหลัก">
    @forelse ($frontendMenuItems ?? [] as $item)
        @include('frontend.partials.navigation-item', ['item' => $item])
    @empty
        <a href="{{ route('home') }}" class="text-slate-700 hover:text-slate-900">
            หน้าแรก
        </a>

        <a href="temple-list" class="text-slate-700 hover:text-slate-900">
            วัด
        </a>

        <a href="articles" class="text-slate-700 hover:text-slate-900">
            บทความ
        </a>
    @endforelse
</nav>