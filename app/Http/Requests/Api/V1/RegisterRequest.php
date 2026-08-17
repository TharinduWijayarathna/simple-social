<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\DevicePlatform;
use App\Enums\DeviceType;
use App\Enums\Role;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['sometimes', Rule::in([Role::Student->value])],
            'device_name' => ['required', 'string', 'max:100'],
            'device_type' => ['required', Rule::enum(DeviceType::class)],
            'device_platform' => ['required', Rule::enum(DevicePlatform::class)],
            'push_token' => ['nullable', 'string', 'max:255'],
        ];
    }
}
