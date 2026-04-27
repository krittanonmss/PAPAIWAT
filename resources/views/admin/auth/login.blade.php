<x-layouts.admin-guest :title="'Admin Login'">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-white tracking-wide">PAPAWAT CMS</h1>
        <p class="mt-2 text-sm text-slate-400">ระบบจัดการเนื้อหา</p>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-xl px-6 py-6 shadow-xl">
        <h2 class="mb-6 text-lg font-semibold text-white">เข้าสู่ระบบ</h2>

        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-300" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->has('email'))
            <div class="mb-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300" role="alert">
                {{ $errors->first('email') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" novalidate class="space-y-5">
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-300">อีเมล</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    inputmode="email"
                    autocomplete="username"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                    aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-400 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30"
                >
                @error('email')
                    <div id="email-error" class="mt-1 text-sm text-red-400">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-slate-300">รหัสผ่าน</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                    aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-400 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30"
                >
                @error('password')
                    <div id="password-error" class="mt-1 text-sm text-red-400">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input
                    id="remember"
                    name="remember"
                    type="checkbox"
                    value="1"
                    {{ old('remember') ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-white/20 bg-white/5 text-indigo-500 focus:ring-indigo-400"
                >
                <label for="remember" class="text-sm text-slate-400">จดจำการเข้าสู่ระบบ</label>
            </div>

            <button
                type="submit"
                class="w-full rounded-xl bg-gradient-to-r from-indigo-500 via-purple-500 to-fuchsia-500 px-4 py-3 text-sm font-medium text-white shadow-lg transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-purple-400"
            >
                เข้าสู่ระบบ
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            เฉพาะผู้ดูแลระบบที่ได้รับอนุญาตเท่านั้น
        </p>
    </div>
</x-layouts.admin-guest>