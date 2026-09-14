<?php

namespace App\Models;

use Database\Factories\DocumentFolderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property int $user_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['parent_id', 'user_id', 'name'])]
class DocumentFolder extends Model
{
    /** @use HasFactory<DocumentFolderFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<DocumentFolder, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(DocumentFolder::class, 'parent_id');
    }

    /**
     * @return HasMany<DocumentFolder, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(DocumentFolder::class, 'parent_id');
    }

    /**
     * @return HasMany<DocumentFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(DocumentFile::class, 'folder_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function breadcrumb(): Collection
    {
        $crumbs = collect();
        $current = $this;

        while ($current instanceof self) {
            $crumbs->prepend([
                'id' => $current->id,
                'name' => $current->name,
            ]);
            $current = $current->parent;
        }

        return $crumbs;
    }
}
