<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Http\Dto\NewArrivals;

final readonly class ArrivalMetaContentDto
{
    public function __construct(
        public string $title,
        public ?string $body = null
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
        ];
    }

    public function toJson(): false|string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}
