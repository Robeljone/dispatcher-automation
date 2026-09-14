<?php

namespace App\Models;

use Database\Factories\DocumentFileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int|null $folder_id
 * @property int $user_id
 * @property string $name
 * @property string $path
 * @property string $mime_type
 * @property int $size
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['folder_id', 'user_id', 'name', 'path', 'mime_type', 'size'])]
class DocumentFile extends Model
{
    /** @use HasFactory<DocumentFileFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<DocumentFolder, $this>
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(DocumentFolder::class, 'folder_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kind(): string
    {
        if (Str::startsWith($this->mime_type, 'image/')) {
            return 'image';
        }

        if ($this->mime_type === 'application/pdf') {
            return 'pdf';
        }

        return 'document';
    }

    public function humanSize(): string
    {
        return Number::fileSize($this->size);
    }
}
