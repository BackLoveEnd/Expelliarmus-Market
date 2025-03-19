<?php

declare(strict_types=1);

namespace Modules\Category\Http\Shop\Actions;

use Illuminate\Database\Eloquent\Collection;
use Modules\Category\Models\Category;

class GetCategoriesBrowseListAction
{

    public function handle(): Collection
    {
        return Category::query()
            ->whereIsRoot()
            ->whereNotNull('icon_url')
            ->get([
                'id',
                'name',
                'slug',
                'icon_url',
            ]);
    }

}
