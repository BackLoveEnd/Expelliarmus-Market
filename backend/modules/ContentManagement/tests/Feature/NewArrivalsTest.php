<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Modules\ContentManagement\Enum\NewArrivalsPositionEnum;
use Modules\ContentManagement\Http\Dto\NewArrivals\ArrivalContentDto;
use Modules\ContentManagement\Models\NewArrival;
use Modules\ContentManagement\Services\NewArrivals\NewArrivalsContentService;
use Modules\ContentManagement\Storage\NewArrivals\NewArrivalsStorage;

class NewArrivalsTest extends TestCase
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

    public function test_can_upload_new_arrivals(): void
    {
        $arrivals = $this->generateNewArrivals();

        (new NewArrivalsContentService(new NewArrivalsStorage($this->storageMock)))
            ->saveArrivals($arrivals);

        foreach ($arrivals as $arrival) {
            Storage::disk('public_content')->assertExists('arrivals/'.$arrival->file->hashName());

            $this->assertDatabaseHas('content_new_arrivals', [
                'image_source' => $arrival->file->hashName(),
            ]);
        }
    }

    public function test_can_delete_new_arrival(): void
    {
        $arrival = ArrivalContentDto::collection([
            [
                'file' => UploadedFile::fake()->image('image_1.png'),
                'position' => 1,
                'image_url' => null,
                'arrival_url' => config('app.frontend_url'),
                'content' => [
                    'title' => 'Expelliarmus',
                    'body' => 'Expelliarmus is a disarming charm.',
                ],
                'arrival_id' => null,
            ],
        ]);

        $service = new NewArrivalsContentService(new NewArrivalsStorage($this->storageMock));

        $service->saveArrivals($arrival);

        $arrivalFromDb = NewArrival::query()->where('image_source', $arrival[0]->file->hashName())
            ->first(['arrival_id', 'image_source']);

        $service->deleteArrival($arrivalFromDb);

        $this->assertDatabaseMissing('content_new_arrivals', [
            'arrival_id' => $arrivalFromDb->arrival_id,
        ]);

        $this->storageMock->assertMissing('arrivals/'.$arrivalFromDb->image_source);
    }

    public function test_can_update_exists_arrivals(): void
    {
        $initialArrivals = $this->generateNewArrivals();

        $service = new NewArrivalsContentService(new NewArrivalsStorage($this->storageMock));

        $service->saveArrivals($initialArrivals);

        $arrivalsFromDb = NewArrival::query()->get();

        $changedArrivals = $this->generateChangedArrivalsFromExisting($arrivalsFromDb, 2);

        $service->saveArrivals($changedArrivals);

        // Changed image arrivals + new
        foreach ($changedArrivals as $arrival) {
            $this->storageMock->assertExists('arrivals/'.$arrival->file->hashName());

            $this->assertDatabaseHas('content_new_arrivals', [
                'image_source' => $arrival->file->hashName(),
            ]);
        }

        // Previous images deleted
        foreach ($initialArrivals as $arrival) {
            $this->storageMock->assertExists('arrivals/'.$arrival->file->hashName());
        }
    }

    private function generateNewArrivals(): Collection
    {
        return ArrivalContentDto::collection([
            [
                'arrival_url' => config('app.frontend_url'),
                'position' => 1,
                'content' => [
                    'title' => 'Expelliarmus',
                    'body' => 'Expelliarmus is a disarming charm.',
                ],
                'file' => UploadedFile::fake()->image('expelliarmus.jpg'),
                'exists_image_url' => null,
            ],
            [
                'arrival_url' => config('app.frontend_url'),
                'position' => 2,
                'content' => [
                    'title' => 'Stupefy',
                    'body' => 'Stupefy is a stunning spell.',
                ],
                'file' => UploadedFile::fake()->image('stupefy.jpg'),
                'exists_image_url' => null,
            ],
            [
                'arrival_url' => config('app.frontend_url'),
                'position' => 3,
                'content' => [
                    'title' => 'Lumos',
                    'body' => 'Lumos is a light spell.',
                ],
                'file' => UploadedFile::fake()->image('lumos.jpg'),
                'exists_image_url' => null,
            ],
            [
                'arrival_url' => config('app.frontend_url'),
                'position' => 4,
                'content' => [
                    'title' => 'Alohomora',
                    'body' => 'Alohomora is a unlocking spell.',
                ],
                'file' => UploadedFile::fake()->image('alohomora.jpg'),
                'exists_image_url' => null,
            ],
        ]);
    }

    private function generateChangedArrivalsFromExisting(
        EloquentCollection $arrivalsFromDb,
        int $randomTake
    ): Collection {
        $arrivals = $arrivalsFromDb->take($randomTake)->map(function (NewArrival $arrival) {
            return [
                'arrival_id' => $arrival->arrival_id,
                'file' => UploadedFile::fake()->image('new_image.png'),
                'image_url' => $arrival->image_url,
                'arrival_url' => $arrival->arrival_url,
                'content' => $arrival->content,
                'position' => $arrival->position->value,
            ];
        });

        return ArrivalContentDto::collection([
            [
                'file' => UploadedFile::fake()->image('image_1.png'),
                'position' => NewArrivalsPositionEnum::QUATERNARY->value,
                'image_url' => null,
                'arrival_url' => config('app.frontend_url'),
                'arrival_id' => null,
                'content' => [
                    'title' => 'Expelliarmus',
                    'body' => 'Expelliarmus is a disarming charm.',
                ],
            ],
            ...$arrivals->toArray(),
        ]);
    }
}
