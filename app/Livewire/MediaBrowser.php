<?php

namespace App\Livewire;

use Slimani\MediaManager\Livewire\MediaBrowser as BaseMediaBrowser;
use Slimani\MediaManager\Models\Folder;

/**
 * Fixes the missing clearCachedSchemas() call in setCurrentFolder().
 * Without it, Filament's schema cache is not invalidated on folder navigation,
 * so the items list never updates when browsing into a sub-folder.
 */
class MediaBrowser extends BaseMediaBrowser
{
    public function setCurrentFolder(?int $id): void
    {
        $this->currentFolderId = $id;
        $this->currentFolder = $id
            ? Folder::query()->with(['tags'])->withCount(['children', 'files'])->find($id)
            : null;
        $this->editingFolderId = null;
        $this->isEditingTags = false;
        $this->generateBreadcrumbs();
        $this->setPage(1, $this->getPageName());
        $this->clearCachedSchemas();

        $this->dispatch('media-folder-changed', folderId: $id, statePath: $this->statePath);
    }
}
