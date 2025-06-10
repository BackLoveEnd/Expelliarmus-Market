<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Http\Dto\Slider;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

final readonly class SliderContentDto
{
    public function __construct(
        public int $order,
        public string $contentUrl,
        public ?UploadedFile $image = null,
        public ?string $slideId = null,
        public ?string $imageUrl = null
    ) {}

    public static function collection(array $data): Collection
    {
        return collect($data)->map(function (array $image) {
            return new self(
                order: (int) $image['order'],
                contentUrl: $image['content_url'],
                image: $image['image'] ?? null,
                slideId: $image['slide_id'] ?? null,
                imageUrl: $image['image_url'] ?? null
            );
        });
    }
}
