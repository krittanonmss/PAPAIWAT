<?php

namespace App\Http\Requests\Admin\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    private const GROUP_OPTIONS = [
        'users',
        'roles',
        'permissions',
        'temples',
        'media',
        'categories',
        'settings',
        'dashboard',
    ];

    private const ACTION_OPTIONS = [
        'view',
        'create',
        'update',
        'delete',
        'manage',
        'publish',
        'approve',
        'permissions',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $groupKey = trim((string) $this->input('group_key'));
        $actionKey = trim((string) $this->input('action_key'));

        if ($groupKey !== '' && $actionKey !== '') {
            $this->merge([
                'key' => $groupKey . '.' . $actionKey,
            ]);
        }
    }

    public function rules(): array
    {
        $permission = $this->route('permission');

        return [
            'name' => ['required', 'string', 'max:255'],
            'group_key' => ['required', 'string', Rule::in(self::GROUP_OPTIONS)],
            'action_key' => ['required', 'string', Rule::in(self::ACTION_OPTIONS)],
            'key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'key')->ignore($permission?->id),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'ชื่อสิทธิ์',
            'group_key' => 'กลุ่มสิทธิ์',
            'action_key' => 'การกระทำ',
            'key' => 'permission key',
            'description' => 'คำอธิบาย',
        ];
    }
}