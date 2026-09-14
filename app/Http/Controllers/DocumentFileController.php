<?php

namespace App\Http\Controllers;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Http\Requests\StoreDocumentFileRequest;
use App\Models\DocumentFile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentFileController extends Controller
{
    public function store(StoreDocumentFileRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $uploaded = $request->file('file');
        assert($uploaded instanceof UploadedFile);

        $folderId = $request->integer('folder_id') ?: null;
        $path = $uploaded->store('company-documents', 'local');

        $file = DocumentFile::query()->create([
            'folder_id' => $folderId,
            'user_id' => $user->id,
            'name' => $uploaded->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $uploaded->getMimeType() ?: $uploaded->getClientMimeType(),
            'size' => $uploaded->getSize(),
        ]);

        $recordActivity->handle(
            $user,
            ActivityAction::DocumentUploaded,
            "Uploaded \"{$file->name}\".",
        );

        return redirect()
            ->route('documents.index', $folderId ? ['folder' => $folderId] : [])
            ->with('success', 'File uploaded.');
    }

    public function download(DocumentFile $documentFile): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($documentFile->path), 404);

        return Storage::disk('local')->download($documentFile->path, $documentFile->name);
    }

    public function destroy(Request $request, DocumentFile $documentFile, RecordActivity $recordActivity): RedirectResponse
    {
        $folderId = $documentFile->folder_id;
        $name = $documentFile->name;

        Storage::disk('local')->delete($documentFile->path);
        $documentFile->delete();

        $recordActivity->handle(
            $request->user(),
            ActivityAction::DocumentDeleted,
            "Deleted \"{$name}\".",
        );

        return redirect()
            ->route('documents.index', $folderId ? ['folder' => $folderId] : [])
            ->with('success', 'File deleted.');
    }
}
