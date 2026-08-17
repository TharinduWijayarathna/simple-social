<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Collaboration;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCollaborationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Collaboration::class) ?? false;
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
            'credit_notes' => ['nullable', 'string', 'max:2000'],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
