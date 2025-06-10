<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Http\Dto\NewArrivals;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

final readonly class ArrivalContentDto
{
    public function __construct(
        public string $arrivalUrl,
        public int $position,
        public ?string $arrivalId,
        public ?UploadedFile $file,
        public ?string $existsImageUrl,
        public ArrivalMetaContentDto $content
    ) {}

    public static function collection(array $data): Collection
    {
        return collect($data)->map(function (array $data) {
            return new self(
                arrivalUrl: $data['arrival_url'],
                position: (int) $data['position'],
                arrivalId: $data['arrival_id'] ?? null,
                file: $data['file'] ?? null,
                existsImageUrl: $data['exists_image_url'] ?? null,
                content: new ArrivalMetaContentDto(
                    title: $data['content']['title'],
                    body: $data['content']['body'] ?? null,
                )
            );
        });
    }

    public function toArray(): array
    {
        return [
            'arrival_id' => $this->arrivalId,
            'arrival_url' => $this->arrivalUrl,
            'position' => $this->position,
            'image_source' => $this->file->hashName(),
            'image_url' => null,
            'content' => $this->content->toJson(),
        ];
    }
}
