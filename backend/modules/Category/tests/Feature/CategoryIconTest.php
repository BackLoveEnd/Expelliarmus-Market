<?php

declare(strict_types=1);

namespace Feature;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Http\Management\Exceptions\CannotUploadIconForNotRootCategoryException;
use Modules\Category\Http\Management\Services\CategoryIconService;
use Modules\Category\Models\Category;

class CategoryIconTest extends TestCase
{
    use RefreshDatabase;

    private FilesystemManager $storageMock;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public_icons');

        $this->storageMock = $this->createMock(FilesystemManager::class);

        $this->storageMock
            ->method('disk')->with('public_icons')
            ->willReturn(Storage::disk('public_icons'));
    }

    public function test_can_upload_icon_for_category(): void
    {
        $category = Category::query()->create(['name' => 'Test Category 1']);

        $service = new CategoryIconService($this->storageMock);

        $svgIcon = UploadedFile::fake()->image('icon.svg');

        $name = $svgIcon->hashName();

        $service->upload($svgIcon, $category);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'icon_origin' => $name,
        ]);

        Storage::disk('public_icons')->assertExists('categories/'.$name);
    }

    public function test_can_edit_icon_for_category(): void
    {
        $category = Category::query()->create(['name' => 'Test Category 1']);

        $service = new CategoryIconService($this->storageMock);

        $firstIcon = UploadedFile::fake()->image('icon.svg');
        $firstIconName = $firstIcon->hashName();

        // First upload
        $service->upload($firstIcon, $category);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'icon_origin' => $firstIconName,
        ]);

        Storage::disk('public_icons')->assertExists('categories/'.$firstIconName);

        // Edit upload
        $secondIcon = UploadedFile::fake()->image('edit.svg');

        $secondIconName = $secondIcon->hashName();

        $service->edit($secondIcon, $category);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'icon_origin' => $secondIconName,
        ]);

        Storage::disk('public_icons')->assertExists('categories/'.$secondIconName);
        Storage::disk('public_icons')->assertMissing('categories/'.$firstIconName);
    }

    public function test_will_throw_error_if_try_to_upload_icon_to_child_category(): void
    {
        $categories = Category::create([
            'name' => 'Food & Drinks',
            'slug' => 'food-and-drinks',
            'children' => [
                [
                    'name' => 'Drinks',
                    'slug' => 'drinks',
                ],
            ],
        ]);

        $service = new CategoryIconService($this->storageMock);

        $svgIcon = UploadedFile::fake()->image('icon.svg');

        $this->assertThrows(
            test: fn () => $service->upload($svgIcon, $categories->children[0]),
            expectedClass: CannotUploadIconForNotRootCategoryException::class,
        );
    }
}
