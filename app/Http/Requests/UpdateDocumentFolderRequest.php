<?php

namespace App\Http\Requests;

use App\Models\DocumentFolder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentFolderRequest extends FormRequest
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
        $folder = $this->route('documentFolder');
        $parentId = $folder instanceof DocumentFolder ? $folder->parent_id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('document_folders', 'name')
                    ->ignore($folder instanceof DocumentFolder ? $folder->id : null)
                    ->where(
                        fn ($query) => $parentId === null
                            ? $query->whereNull('parent_id')
                            : $query->where('parent_id', $parentId),
                    ),
            ],
        ];
    }
}
