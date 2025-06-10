<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Service\Attributes\Dto;

final readonly class FetchAttributesColumnsDto
{
    public function __construct(
        public array $singleAttrCols = [['*'], ['*']],
        public array $combinedAttrCols = [['*'], ['*']],
        public array $warehouseCols = ['*'],
    ) {}
}
