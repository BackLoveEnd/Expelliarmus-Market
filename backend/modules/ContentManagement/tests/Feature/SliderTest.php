<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Modules\ContentManagement\Http\Dto\Slider\SliderContentDto;
use Modules\ContentManagement\Models\ContentSlider;
use Modules\ContentManagement\Services\Slider\SliderContentService;
use Modules\ContentManagement\Storage\Slider\SliderContentStorage;

class SliderTest extends TestCase
{
    use RefreshDatabase;

    protected FilesystemManager $storageMock;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public_content');

        $this->storageMock = $this->createMock(FilesystemManager::class);

        $this->storageMock->method('disk')->with('public_content')
            ->willReturn(Storage::disk('public_content'));
    }

    public function test_can_upload_new_slides(): void
    {
        $slides = $this->generateNewSlides();

        (new SliderContentService(new SliderContentStorage($this->storageMock)))
            ->saveSlider($slides);

        foreach ($slides as $slide) {
            Storage::disk('public_content')->assertExists('slider/'.$slide->image->hashName());

            $this->assertDatabaseHas('content_sliders', [
                'image_source' => $slide->image->hashName(),
            ]);
        }
    }

    public function test_can_update_existing_slides(): void
    {
        $initialSlides = $this->generateNewSlides();

        $service = new SliderContentService(new SliderContentStorage($this->storageMock));

        $service->saveSlider($initialSlides);

        $slidesFromDb = ContentSlider::query()->get();

        $changedSlides = $this->generateChangedSlidesFromExisting($slidesFromDb, 2);

        $service->saveSlider($changedSlides);

        // Changed image slides + new
        foreach ($changedSlides as $slide) {
            $this->storageMock->assertExists('slider/'.$slide->image->hashName());

            $this->assertDatabaseHas('content_sliders', [
                'image_source' => $slide->image->hashName(),
            ]);
        }

        // Previous images deleted
        foreach ($initialSlides as $slide) {
            $this->storageMock->assertExists('slider/'.$slide->image->hashName());
        }
    }

    public function test_can_delete_slide(): void
    {
        $slide = SliderContentDto::collection([
            [
                'image' => UploadedFile::fake()->image('image_1.png'),
                'order' => 1,
                'image_url' => null,
                'content_url' => config('app.frontend_url'),
                'slide_id' => null,
            ],
        ]);

        $service = new SliderContentService(new SliderContentStorage($this->storageMock));

        $service->saveSlider($slide);

        $slideFromDb = ContentSlider::query()->where('image_source', $slide[0]->image->hashName())
            ->first(['slide_id', 'image_source', 'order']);

        $service->delete($slideFromDb);

        $this->assertDatabaseMissing('content_sliders', [
            'slide_id' => $slideFromDb->slide_id,
        ]);

        $this->storageMock->assertMissing('slider/'.$slideFromDb->image_source);
    }

    public function test_deleting_slide_updates_order(): void
    {
        $slides = $this->generateNewSlides();

        $service = new SliderContentService(new SliderContentStorage($this->storageMock));

        $service->saveSlider($slides);

        $firstSlide = ContentSlider::query()->first();

        $service->delete($firstSlide);

        $remainingSlides = ContentSlider::query()->orderBy('order')->get();

        $this->assertEquals([1, 2], $remainingSlides->pluck('order')->toArray());
    }

    private function generateChangedSlidesFromExisting(
        EloquentCollection $slidesFromDb,
        int $randomTake
    ): Collection {
        $slides = $slidesFromDb->take($randomTake)->map(function (ContentSlider $contentSlider) {
            return [
                'slide_id' => $contentSlider->slide_id,
                'image' => UploadedFile::fake()->image('new_image.png'),
                'image_url' => $contentSlider->image_url,
                'content_url' => $contentSlider->content_url,
                'order' => $contentSlider->order,
            ];
        });

        return SliderContentDto::collection([
            [
                'image' => UploadedFile::fake()->image('image_1.png'),
                'order' => $slides->max('order') + 1,
                'image_url' => null,
                'content_url' => config('app.frontend_url'),
                'slide_id' => null,
            ],
            ...$slides->toArray(),
        ]);
    }

    private function generateNewSlides(): Collection
    {
        return SliderContentDto::collection([
            [
                'image' => UploadedFile::fake()->image('image_1.png'),
                'order' => 1,
                'image_url' => null,
                'content_url' => config('app.frontend_url'),
                'slide_id' => null,
            ],
            [
                'image' => UploadedFile::fake()->image('image_2.png'),
                'order' => 2,
                'image_url' => null,
                'content_url' => config('app.frontend_url'),
                'slide_id' => null,
            ],
            [
                'image' => UploadedFile::fake()->image('image_2.png'),
                'order' => 3,
                'image_url' => null,
                'content_url' => config('app.frontend_url'),
                'slide_id' => null,
            ],
        ]);
    }
}
