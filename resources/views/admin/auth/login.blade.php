<x-layouts.admin-guest :title="'Admin Login'">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900">PAPAIWAT Admin</h1>
        <p class="mt-1 text-sm text-gray-600">เข้าสู่ระบบผู้ดูแล</p>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('email'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
            {{ $errors->first('email') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.store') }}" novalidate class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-gray-700">อีเมล</label>
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
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-200"
            >
            @error('email')
                <div id="email-error" class="mt-1 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium text-gray-700">รหัสผ่าน</label>
            <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-200"
            >
            @error('password')
                <div id="password-error" class="mt-1 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input
                id="remember"
                name="remember"
                type="checkbox"
                value="1"
                {{ old('remember') ? 'checked' : '' }}
                class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400"
            >
            <label for="remember" class="text-sm text-gray-700">จดจำการเข้าสู่ระบบ</label>
        </div>

        <button
            type="submit"
            class="w-full rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-300"
        >
            เข้าสู่ระบบ
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        เฉพาะผู้ดูแลระบบที่ได้รับอนุญาตเท่านั้น
    </p>
</x-layouts.admin-guest>