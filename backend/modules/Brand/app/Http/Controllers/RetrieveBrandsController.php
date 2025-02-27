<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Config\Repository as Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Brand\Http\Actions\GetLimitOffsetPaginatedBrandsAction;
use Modules\Brand\Http\Actions\GetPagePaginatedBrandsAction;
use Modules\Brand\Http\Resources\BrandsPaginatedResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class RetrieveBrandsController extends Controller
{
    private array $columns;

    private int $defaultBrandsShowNumber;

    public function __construct(private Config $config, private Request $request)
    {
        $this->columns = ['id', 'name', 'slug', 'description'];

        $this->defaultBrandsShowNumber = $this->config->get('brand.max_brands_show_number');
    }

    /**
     * Retrieve brands (paginated).
     *
     * Usage place -Admin section.
     *
     * @return JsonApiResourceCollection|JsonResponse
     */
    public function getPaginated(): JsonApiResourceCollection|JsonResponse
    {
        if ($this->request->hasAny(['limit', 'offset'])) {
            return $this->wantsLimitOffsetPaginatedBrands();
        }

        return $this->wantsPagePaginatedBrands();
    }

    private function wantsPagePaginatedBrands(): JsonApiResourceCollection
    {
        $brands = (new GetPagePaginatedBrandsAction())->handle($this->columns, $this->defaultBrandsShowNumber);

        return BrandsPaginatedResource::collection($brands['items'])->additional($brands['additional']);
    }

    private function wantsLimitOffsetPaginatedBrands(): JsonApiResourceCollection
    {
        $brands = (new GetLimitOffsetPaginatedBrandsAction())
            ->handle(
                columns: $this->columns,
                limit: (int) $this->request->query('limit') ?? $this->defaultBrandsShowNumber,
                offset: (int) $this->request->query('offset') ?? 0,
            );

        return BrandsPaginatedResource::collection($brands['items'])->additional($brands['additional']);
    }
}