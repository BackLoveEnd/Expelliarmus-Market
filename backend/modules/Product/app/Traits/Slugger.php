<?php

namespace Modules\Product\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

trait Slugger
{
    private Collection $relatedSlugs;

    public function createSlug(string $name): string
    {
        $slug = Str::slug($name);

        $this->relatedSlugs = $this->getRelatedSlugs($slug);

        if (! $this->relatedSlugs->contains($this->slugColumn(), $slug)) {
            return $slug;
        }

        return $this->overrideSlugOnExist($slug);
    }

    protected function getRelatedSlugs(string $name, int $id = 0): Collection
    {
        return $this->newModelQuery()
            ->whereLike($this->slugColumn(), $name.'%')
            ->where('id', '<>', $id)
            ->get([$this->slugColumn(), 'id']);
    }

    private function overrideSlugOnExist(string $slug): string
    {
        $uniqueId = 1;

        do {
            $newSlug = $slug.'-'.$uniqueId;

            if (! $this->relatedSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }

            $uniqueId++;
        } while (true);
    }

    abstract protected function slugColumn(): string;
}
