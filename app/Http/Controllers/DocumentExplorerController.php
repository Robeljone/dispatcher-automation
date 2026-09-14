<?php

namespace App\Http\Controllers;

use App\Models\DocumentFile;
use App\Models\DocumentFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DocumentExplorerController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $folder = null;

        if ($search === '' && $request->filled('folder')) {
            $folder = DocumentFolder::query()->with('parent')->findOrFail($request->integer('folder'));
        }

        $folderIndex = DocumentFolder::query()->orderBy('name')->get(['id', 'parent_id', 'name']);

        return Inertia::render('Documents/Index', [
            'folder' => $folder === null ? null : [
                'id' => $folder->id,
                'name' => $folder->name,
                'parent_id' => $folder->parent_id,
            ],
            'breadcrumbs' => $folder?->breadcrumb() ?? [],
            'tree' => $this->tree($folderIndex),
            'search' => $search,
            'items' => $search === ''
                ? $this->browseItems($folder?->id)
                : $this->searchItems($search, $folderIndex),
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function browseItems(?int $folderId): Collection
    {
        $folders = DocumentFolder::query()
            ->where('parent_id', $folderId)
            ->orderBy('name')
            ->get()
            ->map(fn (DocumentFolder $item): array => [
                'id' => $item->id,
                'name' => $item->name,
                'kind' => 'folder',
                'location' => null,
                'updated_at' => $item->updated_at?->toDayDateTimeString(),
            ]);

        $files = DocumentFile::query()
            ->with('uploader:id,name')
            ->where('folder_id', $folderId)
            ->orderBy('name')
            ->get()
            ->map(fn (DocumentFile $file): array => [
                'id' => $file->id,
                'name' => $file->name,
                'kind' => $file->kind(),
                'size' => $file->humanSize(),
                'uploader' => $file->uploader?->name,
                'location' => null,
                'updated_at' => $file->updated_at?->toDayDateTimeString(),
            ]);

        return $folders->concat($files)->values();
    }

    /**
     * @param  Collection<int, DocumentFolder>  $folderIndex
     * @return Collection<int, array<string, mixed>>
     */
    private function searchItems(string $search, Collection $folderIndex): Collection
    {
        $like = '%'.$this->escapeLike($search).'%';
        $extension = ltrim($search, '.');
        $extensionLike = str_contains($extension, '.')
            ? null
            : '%.'.$this->escapeLike($extension);

        $folders = DocumentFolder::query()
            ->where('name', 'like', $like)
            ->orderBy('name')
            ->limit(100)
            ->get()
            ->map(fn (DocumentFolder $item): array => [
                'id' => $item->id,
                'name' => $item->name,
                'kind' => 'folder',
                'location' => $this->location($item->parent_id, $folderIndex),
                'updated_at' => $item->updated_at?->toDayDateTimeString(),
            ]);

        $files = DocumentFile::query()
            ->with('uploader:id,name')
            ->where(function ($query) use ($like, $extensionLike): void {
                $query->where('name', 'like', $like);

                if ($extensionLike !== null) {
                    $query->orWhere('name', 'like', $extensionLike)
                        ->orWhere('mime_type', 'like', $like);
                }
            })
            ->orderBy('name')
            ->limit(100)
            ->get()
            ->map(fn (DocumentFile $file): array => [
                'id' => $file->id,
                'name' => $file->name,
                'kind' => $file->kind(),
                'size' => $file->humanSize(),
                'uploader' => $file->uploader?->name,
                'location' => $this->location($file->folder_id, $folderIndex),
                'updated_at' => $file->updated_at?->toDayDateTimeString(),
            ]);

        return $folders->concat($files)->values();
    }

    /**
     * @param  Collection<int, DocumentFolder>  $folders
     * @return array<int, array{id: int, name: string, children: array<int, mixed>}>
     */
    private function tree(Collection $folders, ?int $parentId = null): array
    {
        return $folders
            ->where('parent_id', $parentId)
            ->values()
            ->map(fn (DocumentFolder $folder): array => [
                'id' => $folder->id,
                'name' => $folder->name,
                'children' => $this->tree($folders, $folder->id),
            ])
            ->all();
    }

    /**
     * @param  Collection<int, DocumentFolder>  $folders
     */
    private function location(?int $folderId, Collection $folders): string
    {
        $names = [];
        $currentId = $folderId;
        $guard = 0;

        $byId = $folders->keyBy('id');

        while ($currentId !== null && $guard < 50) {
            $folder = $byId->get($currentId);

            if (! $folder instanceof DocumentFolder) {
                break;
            }

            array_unshift($names, $folder->name);
            $currentId = $folder->parent_id;
            $guard++;
        }

        return $names === [] ? 'Company Documents' : 'Company Documents \\ '.implode(' \\ ', $names);
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
    }
}
