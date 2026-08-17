<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\ExperienceLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->user()->profile) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'experience_level' => ['sometimes', Rule::enum(ExperienceLevel::class)],
            'talent_ids' => ['sometimes', 'array', 'max:12'],
            'talent_ids.*' => ['integer', 'exists:talents,id'],
            'favorite_talent_ids' => ['sometimes', 'array', 'max:'.config('vibecraft.wearable.favorite_talent_limit')],
            'favorite_talent_ids.*' => ['integer', 'exists:talents,id'],
        ];
    }
}
