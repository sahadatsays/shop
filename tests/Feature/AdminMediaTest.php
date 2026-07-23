<?php

use App\Models\Media;
use App\Models\MediaFolder;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\MediaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    $this->seed(AdminAccessSeeder::class);
    $this->seed(MediaSeeder::class);
    actingAsAdmin();
});

test('media library index renders folders and upload zone', function (): void {
    $folder = MediaFolder::query()->where('slug', 'products')->firstOrFail();

    $this->get(route('admin.media.index', ['folder_id' => $folder->id]))
        ->assertSuccessful()
        ->assertSee('Media Library')
        ->assertSee('Products')
        ->assertSee('Drag and drop files here');
});

test('admin can upload image to media library', function (): void {
    $folder = MediaFolder::query()->where('slug', 'marketing')->firstOrFail();

    $this->post(route('admin.media.store'), [
        'folder_id' => $folder->id,
        'files' => [
            UploadedFile::fake()->image('hero-banner.jpg', 1200, 800),
        ],
    ])
        ->assertOk()
        ->assertJsonPath('message', '1 file(s) uploaded successfully.');

    $media = Media::query()->where('folder_id', $folder->id)->first();

    expect($media)->not->toBeNull()
        ->and($media->original_filename)->toBe('hero-banner.jpg')
        ->and(Storage::disk('public')->exists($media->path))->toBeTrue();
});

test('invalid media upload returns descriptive json validation errors', function (): void {
    $folder = MediaFolder::query()->where('slug', 'marketing')->firstOrFail();

    $this->post(route('admin.media.store'), [
        'folder_id' => $folder->id,
        'files' => [
            UploadedFile::fake()->create('notes.txt', 100, 'text/plain'),
        ],
    ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only JPG, PNG, WebP, GIF, SVG, and PDF files are allowed.');
});

test('media metadata can be updated', function (): void {
    $folder = MediaFolder::query()->firstOrFail();

    $this->post(route('admin.media.store'), [
        'folder_id' => $folder->id,
        'files' => [UploadedFile::fake()->image('asset.jpg')],
    ]);

    $media = Media::query()->firstOrFail();

    $this->patch(route('admin.media.update', $media), [
        'title' => 'Updated asset title',
        'alt_text' => 'Updated alt text',
        'folder_id' => $folder->id,
    ])->assertRedirect(route('admin.media.show', $media));

    expect($media->fresh()->title)->toBe('Updated asset title')
        ->and($media->fresh()->alt_text)->toBe('Updated alt text');
});

test('media search finds files by title', function (): void {
    $folder = MediaFolder::query()->firstOrFail();

    $this->post(route('admin.media.store'), [
        'folder_id' => $folder->id,
        'files' => [UploadedFile::fake()->image('searchable-asset.jpg')],
    ]);

    $this->get(route('admin.media.index', ['search' => 'searchable-asset']))
        ->assertSuccessful()
        ->assertSee('searchable-asset');
});

test('media can be optimized and deleted', function (): void {
    $folder = MediaFolder::query()->firstOrFail();

    $this->post(route('admin.media.store'), [
        'folder_id' => $folder->id,
        'files' => [UploadedFile::fake()->image('optimize-me.jpg', 800, 600)],
    ]);

    $media = Media::query()->firstOrFail();

    $this->post(route('admin.media.optimize', $media))
        ->assertRedirect(route('admin.media.show', $media));

    expect($media->fresh()->meta['optimized_at'] ?? null)->not->toBeNull();

    $this->delete(route('admin.media.destroy', $media))
        ->assertRedirect(route('admin.media.index', ['folder_id' => $folder->id]));

    expect(Media::query()->whereKey($media->id)->exists())->toBeFalse();
});

test('media folder can be created', function (): void {
    $this->post(route('admin.media.folders.store'), [
        'name' => 'Campaign 2026',
    ])->assertRedirect();

    expect(MediaFolder::query()->where('slug', 'campaign-2026')->exists())->toBeTrue();
});

test('media routes require authentication', function (): void {
    auth('admin')->logout();

    $this->get(route('admin.media.index'))->assertRedirect(route('admin.login'));
});
