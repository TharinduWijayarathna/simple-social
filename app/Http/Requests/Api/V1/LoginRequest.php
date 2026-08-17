<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\DevicePlatform;
use App\Enums\DeviceType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
            'device_type' => ['required', Rule::enum(DeviceType::class)],
            'device_platform' => ['required', Rule::enum(DevicePlatform::class)],
            'push_token' => ['nullable', 'string', 'max:255'],
        ];
    }
}
