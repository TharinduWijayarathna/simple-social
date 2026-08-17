<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\PortfolioMediaType;
use App\Models\PortfolioItem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePortfolioItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PortfolioItem::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'talent_id' => ['nullable', 'integer', 'exists:talents,id'],
            'media_type' => ['required', Rule::enum(PortfolioMediaType::class)],
            'file' => ['required', 'file', 'max:51200', 'mimes:jpg,jpeg,png,webp,gif,mp4,mov,mp3,wav,pdf,doc,docx'],
            'published' => ['sometimes', 'boolean'],
        ];
    }
}
