<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\DevicePlatform;
use App\Enums\DeviceType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PairDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::enum(DeviceType::class)],
            'platform' => ['required', Rule::enum(DevicePlatform::class)],
            'push_token' => ['nullable', 'string', 'max:255'],
        ];
    }
}
