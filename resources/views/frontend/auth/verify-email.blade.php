@extends('frontend.layouts.app')

@section('title', 'ยืนยันอีเมล')
@section('meta_description', 'ยืนยันอีเมลเพื่อเปิดใช้งานการ sync รายการโปรด')

@section('content')
    <section class="px-4 py-16 text-white">
        <div class="mx-auto max-w-xl rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-2xl shadow-slate-950/30">
            <p class="text-sm font-semibold text-blue-300">ยืนยันตัวตน</p>
            <h1 class="mt-2 text-2xl font-bold">กรุณายืนยันอีเมล</h1>
            <p class="mt-3 text-sm leading-6 text-slate-400">
                เราส่งลิงก์ยืนยันไปที่ {{ auth()->user()?->email }} แล้ว ต้องยืนยันอีเมลก่อนจึงจะ sync รายการโปรดข้ามอุปกรณ์ได้
            </p>

            @if (session('status') === 'verification-link-sent')
                <div class="mt-5 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                    ส่งลิงก์ยืนยันอีเมลใหม่แล้ว
                </div>
            @endif

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500 sm:w-auto">ส่งอีเมลอีกครั้ง</button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-2xl border border-white/10 px-5 py-3 text-sm font-semibold text-slate-300 transition hover:bg-white/10 hover:text-white sm:w-auto">ออกจากระบบ</button>
                </form>
            </div>
        </div>
    </section>
@endsection
