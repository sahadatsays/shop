<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaFolderRequest;
use App\Models\MediaFolder;
use App\Services\Admin\MediaFolderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class MediaFolderController extends Controller
{
    public function __construct(private MediaFolderService $folders) {}

    public function store(StoreMediaFolderRequest $request): RedirectResponse
    {
        $this->folders->create($request->validated());

        return redirect()
            ->route('admin.media.index', array_filter(['folder_id' => $request->input('parent_id')]))
            ->with('success', 'Folder created successfully.');
    }

    public function update(StoreMediaFolderRequest $request, MediaFolder $folder): RedirectResponse
    {
        $this->folders->update($folder, $request->validated());

        return redirect()
            ->route('admin.media.index', ['folder_id' => $folder->id])
            ->with('success', 'Folder updated successfully.');
    }

    public function destroy(MediaFolder $folder): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('media.folders.manage'), 403);

        try {
            $this->folders->delete($folder);
        } catch (\InvalidArgumentException $exception) {
            return redirect()
                ->route('admin.media.index', ['folder_id' => $folder->id])
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.media.index', array_filter(['folder_id' => $folder->parent_id]))
            ->with('success', 'Folder deleted successfully.');
    }
}
