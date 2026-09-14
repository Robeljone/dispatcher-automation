<?php

namespace App\Actions;

use App\Models\DocumentFolder;
use Illuminate\Support\Facades\Storage;

class DeleteDocumentFolder
{
    public function handle(DocumentFolder $folder): void
    {
        $folder->load(['children', 'files']);

        foreach ($folder->children as $child) {
            $this->handle($child);
        }

        foreach ($folder->files as $file) {
            Storage::disk('local')->delete($file->path);
            $file->delete();
        }

        $folder->delete();
    }
}
