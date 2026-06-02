@extends('frontend.layouts.app')

@section('title', 'สมัครสมาชิก')
@section('meta_description', 'สมัครสมาชิกเพื่อเก็บรายการโปรดข้ามอุปกรณ์')

@section('content')
    <section class="px-4 py-14 text-white">
        <div class="mx-auto max-w-md">
            <div class="mb-4 grid grid-cols-2 gap-2 rounded-2xl border border-white/10 bg-white/[0.035] p-1">
                <a href="{{ route('login', request()->only('redirect')) }}" class="rounded-xl px-4 py-2.5 text-center text-sm font-semibold text-slate-400 transition hover:bg-white/[0.05] hover:text-white">เข้าสู่ระบบ</a>
                <a href="{{ route('register', request()->only('redirect')) }}" class="rounded-xl bg-white/[0.08] px-4 py-2.5 text-center text-sm font-semibold text-white">สมัครสมาชิก</a>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/20">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-300">Account</p>
                    <h1 class="mt-3 text-2xl font-semibold leading-8">สมัครสมาชิก</h1>
                    <p class="mt-2 text-sm leading-7 text-slate-400">เริ่มเก็บรายการโปรดไว้กับบัญชีของคุณ</p>
                </div>

                <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="mb-1.5 block text-sm text-slate-300">ชื่อ</label>
                        <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus class="w-full rounded-2xl border border-white/10 bg-slate-950/35 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        @error('name')
                            <p class="mt-1.5 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-sm text-slate-300">อีเมล</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required class="w-full rounded-2xl border border-white/10 bg-slate-950/35 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="mb-1.5 block text-sm text-slate-300">รหัสผ่าน</label>
                            <input id="password" type="password" name="password" autocomplete="new-password" required class="w-full rounded-2xl border border-white/10 bg-slate-950/35 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-1.5 block text-sm text-slate-300">ยืนยัน</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required class="w-full rounded-2xl border border-white/10 bg-slate-950/35 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        </div>
                    </div>
                    @error('password')
                        <p class="text-sm text-red-300">{{ $message }}</p>
                    @enderror

                    <p class="text-xs leading-5 text-slate-500">หลังสมัคร ระบบจะส่งอีเมลยืนยันให้ และคุณยังใช้รายการโปรดในเครื่องนี้ต่อได้ทันที</p>

                    <button type="submit" class="w-full rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">สมัครสมาชิก</button>
                </form>
            </div>
        </div>
    </section>
@endsection
