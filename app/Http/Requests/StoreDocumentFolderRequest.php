<?php

namespace App\Http\Requests;

use App\Models\DocumentFolder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentFolderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $parentId = $this->integer('parent_id') ?: null;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('document_folders', 'name')->where(
                    fn ($query) => $parentId === null
                        ? $query->whereNull('parent_id')
                        : $query->where('parent_id', $parentId),
                ),
            ],
            'parent_id' => ['nullable', 'integer', Rule::exists(DocumentFolder::class, 'id')],
        ];
    }
}
