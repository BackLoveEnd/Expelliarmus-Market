<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreeCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon_url ?? null,
            'root' => $this->getRootCategoryName(),
            'last' => $this->children->isEmpty(),
            'children' => self::collection(
                $this->children
            ),
        ];
    }

    private function getRootCategoryName(): string
    {
        $root = $this;
        while ($root->parent !== null) {
            $root = $root->parent;
        }

        return $root->name;
    }
}
