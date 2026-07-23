<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'folder_id',
        'disk',
        'path',
        'thumbnail_path',
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'width',
        'height',
        'alt_text',
        'title',
        'hash',
        'meta',
        'uploaded_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<MediaFolder, $this>
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * @return HasMany<Mediable, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Mediable::class);
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function thumbnailUrl(): string
    {
        if ($this->thumbnail_path) {
            return Storage::disk($this->disk)->url($this->thumbnail_path);
        }

        return $this->url();
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function usageCount(): int
    {
        return $this->attachments()->count();
    }

    public function formattedSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($this->size, 0);
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        $power = min((int) $power, count($units) - 1);

        return round($bytes / (1024 ** $power), 1).' '.$units[$power];
    }
}
