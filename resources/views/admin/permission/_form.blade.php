@php
    $selectedGroupKey = old('group_key', $permission->group_key ?? '');
    $selectedActionKey = old('action_key', isset($permission) ? (explode('.', $permission->key, 2)[1] ?? '') : '');
    $currentName = old('name', $permission->name ?? '');
    $currentKey = old('key', $permission->key ?? '');
    $currentDescription = old('description', $permission->description ?? '');
@endphp

<div class="grid gap-6 text-white">

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="group_key" class="mb-1 block text-sm font-medium text-slate-400">
                กลุ่มสิทธิ์
            </label>
            <select
                id="group_key"
                name="group_key"
                class="w-full appearance-none rounded-xl border border-white/10 bg-slate-900 px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
            >
                <option class="bg-slate-900 text-white" value="">เลือกกลุ่มสิทธิ์</option>
                @foreach ($groupOptions as $groupKey => $groupLabel)
                    <option class="bg-slate-900 text-white" value="{{ $groupKey }}" @selected($selectedGroupKey === $groupKey)>
                        {{ $groupLabel }} ({{ $groupKey }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500">ตัวอย่าง: users, roles, permissions</p>
            @error('group_key')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="action_key" class="mb-1 block text-sm font-medium text-slate-400">
                ประเภทสิทธิ์
            </label>
            <select
                id="action_key"
                name="action_key"
                class="w-full appearance-none rounded-xl border border-white/10 bg-slate-900 px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
            >
                <option class="bg-slate-900 text-white" value="">เลือกประเภทสิทธิ์</option>
                @foreach ($actionOptions as $actionKey => $actionLabel)
                    <option class="bg-slate-900 text-white" value="{{ $actionKey }}" @selected($selectedActionKey === $actionKey)>
                        {{ $actionLabel }} ({{ $actionKey }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500">ตัวอย่าง: view, create, update, delete</p>
            @error('action_key')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="name" class="mb-1 block text-sm font-medium text-slate-400">
            ชื่อสิทธิ์
        </label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ $currentName }}"
            class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
            placeholder="ระบบจะช่วยกรอกให้ เช่น ดูผู้ใช้งาน"
        >
        <p class="mt-1 text-xs text-slate-500">แก้ไขชื่อเองได้ตามต้องการ</p>
        @error('name')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="key" class="mb-1 block text-sm font-medium text-slate-400">
            Permission Key
        </label>
        <input
            type="text"
            id="key"
            name="key"
            value="{{ $currentKey }}"
            readonly
            class="w-full rounded-xl border border-white/10 bg-slate-800 px-3 py-2 text-sm text-slate-300 focus:outline-none"
            placeholder="ระบบจะสร้างให้อัตโนมัติ"
        >
        <p class="mt-1 text-xs text-slate-500">ระบบจะสร้างจาก กลุ่มสิทธิ์ + ประเภทสิทธิ์ เช่น users.view</p>
        @error('key')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm font-medium text-slate-400">
            คำอธิบาย
        </label>
        <textarea
            id="description"
            name="description"
            rows="4"
            class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
            placeholder="อธิบายว่าสิทธิ์นี้ใช้ทำอะไร"
        >{{ $currentDescription }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const groupInput = document.getElementById('group_key');
    const actionInput = document.getElementById('action_key');
    const nameInput = document.getElementById('name');
    const keyInput = document.getElementById('key');

    const nameMap = {
        users: 'ผู้ใช้งาน',
        roles: 'บทบาท',
        permissions: 'สิทธิ์',
        temples: 'วัด',
        media: 'สื่อ',
        settings: 'ตั้งค่าระบบ',
        dashboard: 'แดชบอร์ด',
    };

    const actionMap = {
        view: 'ดู',
        create: 'สร้าง',
        update: 'แก้ไข',
        delete: 'ลบ',
        manage: 'จัดการ',
        publish: 'เผยแพร่',
        approve: 'อนุมัติ',
        permissions: 'จัดการสิทธิ์',
    };

    let isNameTouched = nameInput.value.trim() !== '';

    nameInput.addEventListener('input', function () {
        isNameTouched = nameInput.value.trim() !== '';
    });

    function buildKey() {
        const group = groupInput.value.trim();
        const action = actionInput.value.trim();

        if (!group || !action) {
            keyInput.value = '';
            return;
        }

        keyInput.value = `${group}.${action}`;
    }

    function buildName() {
        const group = groupInput.value.trim();
        const action = actionInput.value.trim();

        if (!group || !action) {
            return;
        }

        if (!isNameTouched) {
            const groupLabel = nameMap[group] ?? group;
            const actionLabel = actionMap[action] ?? action;
            nameInput.value = `${actionLabel}${groupLabel}`;
        }
    }

    function syncFields() {
        buildKey();
        buildName();
    }

    groupInput.addEventListener('change', syncFields);
    actionInput.addEventListener('change', syncFields);

    syncFields();
});
</script>