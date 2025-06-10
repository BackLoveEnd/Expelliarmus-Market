<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Services\NewArrivals;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\ContentManagement\Http\Dto\NewArrivals\ArrivalContentDto;
use Modules\ContentManagement\Http\Exceptions\FailedToDeleteArrivalException;
use Modules\ContentManagement\Http\Exceptions\PositionOfArrivalsIsNotUniqueException;
use Modules\ContentManagement\Models\NewArrival;
use Modules\ContentManagement\Storage\NewArrivals\NewArrivalsStorage;
use Ramsey\Uuid\Uuid;
use Throwable;

class NewArrivalsContentService
{
    public function __construct(
        protected NewArrivalsStorage $storage,
    ) {}

    public function getArrivals(): EloquentCollection
    {
        return NewArrival::query()->orderBy('position')
            ->get([
                'arrival_id',
                'image_url',
                'arrival_url',
                'content',
                'position',
            ]);
    }

    public function saveArrivals(Collection $arrivalsDto): void
    {
        $this->ensureThatPositionsIsUnique($arrivalsDto);

        $newArrivals = $this->getNewArrivals($arrivalsDto);

        $existsArrivals = $this->getArrivalsThatWannaBeChanged($arrivalsDto);

        DB::transaction(function () use ($newArrivals, $existsArrivals) {
            if ($newArrivals->isNotEmpty()) {
                $this->uploadNewArrivals($newArrivals);
            }

            if ($existsArrivals->isNotEmpty()) {
                $this->uploadExistsArrivals($existsArrivals);
            }
        });
    }

    public function deleteArrival(NewArrival $arrival): void
    {
        try {
            $arrival->delete();

            $this->storage->delete($arrival->image_source);
        } catch (Throwable $e) {
            throw new FailedToDeleteArrivalException;
        }
    }

    protected function uploadNewArrivals(Collection $newArrivals): Collection
    {
        $this->storage->uploadMany($newArrivals);

        $preparedNewArrivals = $this->prepareArrivalsContentImages($newArrivals);

        NewArrival::query()->insert($preparedNewArrivals->toArray());

        return $preparedNewArrivals;
    }

    protected function uploadExistsArrivals(Collection $existsArrivals): void
    {
        $existsArrivalsInDb = NewArrival::query()
            ->whereIn('arrival_id', $existsArrivals->pluck('arrivalId'))
            ->get(['arrival_id', 'image_source']);

        $arrivalsWithNewImage = $existsArrivals->filter(fn ($dto) => $dto->file !== null);

        if ($arrivalsWithNewImage->isNotEmpty()) {
            $this->arrivalsWantsToUpdateImage($arrivalsWithNewImage, $existsArrivalsInDb);

            return;
        }

        $arrivalsWithoutImage = $existsArrivals->filter(fn ($dto) => $dto->file === null);

        $this->wantsUpdateExceptImage($arrivalsWithoutImage);
    }

    protected function wantsUpdateExceptImage(Collection $arrivalsWithoutImage): void
    {
        $slidesMapped = $arrivalsWithoutImage->map(function (ArrivalContentDto $dto) {
            return [
                'arrival_id' => $dto->arrivalId,
                'position' => $dto->position,
                'arrival_url' => $dto->arrivalUrl,
                'content' => $dto->content->toJson(),
                'image_url' => '"content_sliders"."image_url"',
                'image_source' => '"content_sliders"."image_source"',
            ];
        });

        NewArrival::query()->upsert(
            values: $slidesMapped->toArray(),
            uniqueBy: ['arrival_id'],
            update: ['arrival_url', 'position', 'content'],
        );
    }

    protected function arrivalsWantsToUpdateImage(Collection $arrivals, EloquentCollection $arrivalsFromDb): void
    {
        $arrivalsFromDb = $arrivalsFromDb->only($arrivals->pluck('arrivalId')->toArray());

        $this->storage->deleteManyById($arrivalsFromDb);

        $this->storage->uploadMany($arrivals);

        NewArrival::query()->upsert(
            values: $this->prepareArrivalsContentImages($arrivals)->toArray(),
            uniqueBy: ['arrival_id'],
            update: ['image_url', 'image_source', 'arrival_url', 'position', 'content'],
        );
    }

    protected function prepareArrivalsContentImages(Collection $arrivals): Collection
    {
        return $arrivals->map(function (ArrivalContentDto $dto) {
            return [
                ...$dto->toArray(),
                'arrival_id' => $dto->arrivalId ?? Uuid::uuid7()->toString(),
                'image_url' => $this->storage->getImageUrl($dto->file->hashName()),
            ];
        });
    }

    protected function ensureThatPositionsIsUnique(Collection $slides): void
    {
        if ($slides->unique('position')->count() !== $slides->count()) {
            throw new PositionOfArrivalsIsNotUniqueException;
        }
    }

    protected function getNewArrivals(Collection $arrivals): Collection
    {
        return $arrivals->filter(function (ArrivalContentDto $dto) {
            return $dto->arrivalId === null && $dto->file !== null;
        });
    }

    protected function getArrivalsThatWannaBeChanged(Collection $arrivals): Collection
    {
        return $arrivals->filter(function (ArrivalContentDto $dto) {
            return $dto->arrivalId !== null;
        });
    }
}
