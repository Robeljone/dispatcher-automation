<?php

namespace App\Http\Controllers;

use App\Actions\DeleteDocumentFolder;
use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Http\Requests\StoreDocumentFolderRequest;
use App\Http\Requests\UpdateDocumentFolderRequest;
use App\Models\DocumentFolder;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DocumentFolderController extends Controller
{
    public function store(StoreDocumentFolderRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $parentId = $request->integer('parent_id') ?: null;

        $folder = DocumentFolder::query()->create([
            'parent_id' => $parentId,
            'user_id' => $user->id,
            'name' => $request->string('name')->toString(),
        ]);

        $recordActivity->handle(
            $user,
            ActivityAction::FolderCreated,
            "Created folder \"{$folder->name}\".",
        );

        return redirect()
            ->route('documents.index', $parentId ? ['folder' => $parentId] : [])
            ->with('success', 'Folder created.');
    }

    public function update(UpdateDocumentFolderRequest $request, DocumentFolder $documentFolder): RedirectResponse
    {
        $documentFolder->update([
            'name' => $request->string('name')->toString(),
        ]);

        return redirect()
            ->route('documents.index', $documentFolder->parent_id ? ['folder' => $documentFolder->parent_id] : [])
            ->with('success', 'Folder renamed.');
    }

    public function destroy(Request $request, DocumentFolder $documentFolder, DeleteDocumentFolder $deleteDocumentFolder, RecordActivity $recordActivity): RedirectResponse
    {
        $parentId = $documentFolder->parent_id;
        $name = $documentFolder->name;

        $deleteDocumentFolder->handle($documentFolder);

        $recordActivity->handle(
            $request->user(),
            ActivityAction::FolderDeleted,
            "Deleted folder \"{$name}\".",
        );

        return redirect()
            ->route('documents.index', $parentId ? ['folder' => $parentId] : [])
            ->with('success', 'Folder deleted.');
    }
}
