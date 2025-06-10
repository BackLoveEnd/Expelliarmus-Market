<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\UploadedFile;
use Modules\Category\Http\Management\Exceptions\CannotUploadIconForNotRootCategoryException;
use Modules\Category\Http\Management\Exceptions\FailedToUploadIconForCategory;
use Modules\Category\Models\Category;

class CategoryIconService
{
    private Filesystem $filesystem;

    public function __construct(
        FilesystemManager $storage
    ) {
        $this->filesystem = $storage->disk('public_icons');
    }

    public function upload(UploadedFile $icon, Category $category): void
    {
        if (! $category->isRoot()) {
            throw new CannotUploadIconForNotRootCategoryException;
        }

        $this->saveIcon($icon);

        $fileName = $icon->hashName();

        $category->saveFileId(
            url: $this->filesystem->url($this->getIconsPath($fileName)),
            origin: $fileName
        );
    }

    public function edit(UploadedFile $icon, Category $category): void
    {
        if (! $category->isRoot()) {
            throw new CannotUploadIconForNotRootCategoryException;
        }

        if ($category->icon_origin !== null && $this->filesystem->exists($this->getIconsPath($category->icon_origin))) {
            $this->filesystem->delete($this->getIconsPath($category->icon_origin));
        }

        $this->saveIcon($icon);

        $fileName = $icon->hashName();

        $category->saveFileId(
            url: $this->filesystem->url($this->getIconsPath($icon->hashName())),
            origin: $fileName
        );
    }

    public function delete(Category $category): void
    {
        if ($category->icon_origin === null) {
            return;
        }

        $this->filesystem->delete($this->getIconsPath($category->icon_origin));
    }

    protected function saveIcon(UploadedFile $icon): void
    {
        if (! $this->filesystem->put('categories/', $icon)) {
            throw new FailedToUploadIconForCategory;
        }
    }

    protected function getIconsPath(string $iconId): string
    {
        return 'categories/'.$iconId;
    }
}
