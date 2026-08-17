<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Event;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Event::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'talent_id' => ['nullable', 'integer', 'exists:talents,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'application_deadline' => ['nullable', 'date', 'before:starts_at'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
