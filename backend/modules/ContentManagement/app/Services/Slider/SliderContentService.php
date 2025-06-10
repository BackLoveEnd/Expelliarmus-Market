<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Services\Slider;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\ContentManagement\Http\Dto\Slider\SliderContentDto;
use Modules\ContentManagement\Http\Exceptions\FailedToDeleteSlideException;
use Modules\ContentManagement\Http\Exceptions\OrderOfSliderContentIsNotUniqueException;
use Modules\ContentManagement\Models\ContentSlider;
use Modules\ContentManagement\Storage\Slider\SliderContentStorage;
use Ramsey\Uuid\Uuid;
use Throwable;

class SliderContentService
{
    public function __construct(
        protected SliderContentStorage $storage
    ) {}

    public function getAll(): EloquentCollection
    {
        return ContentSlider::query()->orderBy('order')
            ->get([
                'slide_id',
                'image_url',
                'order',
                'content_url',
            ]);
    }

    public function saveSlider(Collection $sliderContentDto): void
    {
        $newSlides = $this->getNewSlides($sliderContentDto);

        $slidesToUpdate = $this->getSlidesThatAlreadyExistsAndWasChanged($sliderContentDto);

        DB::transaction(function () use ($newSlides, $slidesToUpdate, $sliderContentDto) {
            $this->ensureThatOrdersIsUnique($newSlides->merge($slidesToUpdate));

            if ($newSlides->isNotEmpty()) {
                $preparedNewSlides = $this->uploadNewSlides($newSlides);
            }

            if ($slidesToUpdate->isNotEmpty()) {
                $this->updateExistsChangedSlides($slidesToUpdate);
            }

            $this->updateSlidesOrder(
                $this->getUntouchedSlides($sliderContentDto)->merge($preparedNewSlides ?? collect())
            );
        });
    }

    public function delete(ContentSlider $contentSlider): void
    {
        try {
            $contentSlider->delete();

            $this->storage->delete($contentSlider->image_source);

            $this->updateOrderOnDelete($contentSlider);
        } catch (Throwable $e) {
            throw new FailedToDeleteSlideException($e->getMessage());
        }
    }

    protected function updateOrderOnDelete(ContentSlider $deletedSlide): void
    {
        ContentSlider::query()->where('order', '>', $deletedSlide->order)
            ->orderBy('order')
            ->decrement('order');
    }

    protected function ensureThatOrdersIsUnique(Collection $slides): void
    {
        if ($slides->unique('order')->count() !== $slides->count()) {
            throw new OrderOfSliderContentIsNotUniqueException;
        }
    }

    protected function updateSlidesOrder(Collection $slides): void
    {
        $dbSlides = ContentSlider::query()->whereIn('slide_id', $slides->pluck('slide_id'))
            ->get(['slide_id', 'order'])
            ->keyBy('slide_id');

        $orderChanged = $slides->filter(function ($slide) use ($dbSlides) {
            return ! isset($dbSlides[$slide['slide_id']]) || $dbSlides[$slide['slide_id']]['order'] !== $slide['order'];
        });

        $orderChanged->each(function (array $slide) {
            ContentSlider::query()->where('slide_id', $slide['slide_id'])
                ->update(['order' => $slide['order']]);
        });
    }

    protected function updateExistsChangedSlides(Collection $slidesToUpdate): void
    {
        $slidesFromDb = ContentSlider::query()
            ->whereIn('slide_id', $slidesToUpdate->pluck('slideId')->toArray())
            ->get(['slide_id', 'image_source']);

        $slidesWithNewImage = $slidesToUpdate->filter(fn ($dto) => $dto->image !== null);

        if ($slidesWithNewImage->isNotEmpty()) {
            $this->uploadAndUpdateWithImage($slidesWithNewImage, $slidesFromDb);

            return;
        }

        $slidesWithoutNewImage = $slidesToUpdate->filter(fn ($dto) => $dto->image === null);

        $this->updateWithoutImage($slidesWithoutNewImage);
    }

    protected function updateWithoutImage(Collection $slidesWithoutImage): void
    {
        $slidesMapped = $slidesWithoutImage->map(function (SliderContentDto $dto) {
            return [
                'slide_id' => $dto->slideId,
                'order' => $dto->order,
                'content_url' => $dto->contentUrl,
                'image_url' => '"content_sliders"."image_url"',
                'image_source' => '"content_sliders"."image_source"',
            ];
        });

        ContentSlider::query()->upsert(
            values: $slidesMapped->toArray(),
            uniqueBy: ['slide_id'],
            update: ['content_url', 'order']
        );
    }

    protected function uploadAndUpdateWithImage(Collection $slidesWithImages, EloquentCollection $slidesFromDb): void
    {
        $slidesFromDb = $slidesFromDb->only($slidesWithImages->pluck('slideId')->toArray());

        $this->storage->deleteManyById($slidesFromDb);

        $this->storage->uploadMany($slidesWithImages);

        ContentSlider::query()->upsert(
            values: $this->prepareSliderContentData($slidesWithImages)->toArray(),
            uniqueBy: ['slide_id'],
            update: ['content_url', 'order', 'image_url', 'image_source']
        );
    }

    protected function uploadNewSlides(Collection $newSlides): Collection
    {
        $this->storage->uploadMany($newSlides);

        $preparedNewSlides = $this->prepareSliderContentData($newSlides);

        ContentSlider::query()->insert($preparedNewSlides->toArray());

        return $preparedNewSlides;
    }

    protected function getUntouchedSlides(Collection $slides): Collection
    {
        $unTouchedSlides = $slides->whereNotNull('slideId')
            ->filter(fn (SliderContentDto $dto) => $dto->image === null && $dto->imageUrl !== null);

        return $unTouchedSlides->map(function (SliderContentDto $dto) {
            return [
                'slide_id' => $dto->slideId,
                'order' => $dto->order,
            ];
        });
    }

    protected function getSlidesThatAlreadyExistsAndWasChanged(Collection $slides): Collection
    {
        return $slides->filter(function (SliderContentDto $dto) {
            return $dto->slideId !== null && ($dto->imageUrl !== null || $dto->image !== null);
        });
    }

    protected function getNewSlides(Collection $sliderContentDto): Collection
    {
        return $sliderContentDto->filter(function (SliderContentDto $dto) {
            return $dto->image !== null && $dto->imageUrl === null && $dto->slideId === null;
        });
    }

    protected function prepareSliderContentData(Collection $sliderContent): Collection
    {
        return $sliderContent->map(function (SliderContentDto $dto) {
            return [
                'slide_id' => $dto->slideId ?: Uuid::uuid7()->toString(),
                'order' => $dto->order,
                'image_source' => $dto->image->hashName(),
                'image_url' => $this->storage->getImageUrl($dto->image->hashName()),
                'content_url' => $dto->contentUrl,
            ];
        });
    }
}
