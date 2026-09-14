<?php

namespace App\Http\Requests;

use App\Models\DocumentFolder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentFileRequest extends FormRequest
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
        return [
            'folder_id' => ['nullable', 'integer', Rule::exists(DocumentFolder::class, 'id')],
            'file' => [
                'required',
                'file',
                'max:20480',
                'mimes:pdf,doc,docx,jpg,jpeg,png,gif,webp',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.mimes' => 'Only PDF, Word, and image files can be stored.',
        ];
    }
}
