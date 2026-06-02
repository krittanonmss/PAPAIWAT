@extends('frontend.layouts.app')

@section('title', 'เข้าสู่ระบบ')
@section('meta_description', 'เข้าสู่ระบบเพื่อ sync รายการโปรดข้ามอุปกรณ์')

@section('content')
    <section class="px-4 py-14 text-white">
        <div class="mx-auto max-w-md">
            <div class="mb-4 grid grid-cols-2 gap-2 rounded-2xl border border-white/10 bg-white/[0.035] p-1">
                <a href="{{ route('login', request()->only('redirect')) }}" class="rounded-xl bg-white/[0.08] px-4 py-2.5 text-center text-sm font-semibold text-white">เข้าสู่ระบบ</a>
                <a href="{{ route('register', request()->only('redirect')) }}" class="rounded-xl px-4 py-2.5 text-center text-sm font-semibold text-slate-400 transition hover:bg-white/[0.05] hover:text-white">สมัครสมาชิก</a>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/20">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-300">Account</p>
                    <h1 class="mt-3 text-2xl font-semibold leading-8">เข้าสู่ระบบ</h1>
                    <p class="mt-2 text-sm leading-7 text-slate-400">กลับไปดูและ sync รายการโปรดของคุณ</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm text-slate-300">อีเมล</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="w-full rounded-2xl border border-white/10 bg-slate-950/35 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-600 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm text-slate-300">รหัสผ่าน</label>
                        <input id="password" type="password" name="password" autocomplete="current-password" required class="w-full rounded-2xl border border-white/10 bg-slate-950/35 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 text-sm text-slate-400">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600 focus:ring-blue-500">
                        จดจำการเข้าสู่ระบบ
                    </label>

                    <button type="submit" class="w-full rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">เข้าสู่ระบบ</button>
                </form>
            </div>
        </div>
    </section>
@endsection
