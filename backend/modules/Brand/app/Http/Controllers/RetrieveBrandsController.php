<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Config\Repository as Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Brand\Http\Actions\GetLimitOffsetPaginatedBrandsAction;
use Modules\Brand\Http\Actions\GetPagePaginatedBrandsAction;
use Modules\Brand\Http\Resources\BrandResource;
use Modules\Brand\Http\Resources\BrandsPaginatedResource;
use Modules\Brand\Models\Brand;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class RetrieveBrandsController extends Controller
{
    private array $columns;

    private int $defaultBrandsShowNumber;

    public function __construct(private Config $config, private Request $request)
    {
        $this->columns = ['id', 'name', 'slug', 'description', 'logo_url'];

        $this->defaultBrandsShowNumber = $this->config->get('brand.max_brands_show_number');
    }

    /**
     * Retrieve brands (paginated).
     *
     * Usage place -Admin section.
     */
    public function getPaginated(): JsonApiResourceCollection|JsonResponse
    {
        if ($this->request->hasAny(['limit', 'offset'])) {
            return $this->wantsLimitOffsetPaginatedBrands();
        }

        return $this->wantsPagePaginatedBrands();
    }

    /**
     * Retrieve brand by ID or slug.
     *
     * Usage place - Admin section|Shop.
     */
    public function getBrandInfo(string|int $brandId): BrandResource
    {
        $brand = Brand::query()
            ->when(
                value: is_numeric($brandId),
                callback: fn ($query) => $query->where('id', $brandId),
                default: fn ($query) => $query->where('slug', $brandId),
            )
            ->firstOrFail();

        return BrandResource::make($brand);
    }

    private function wantsPagePaginatedBrands(): JsonApiResourceCollection|JsonResponse
    {
        $brands = (new GetPagePaginatedBrandsAction)->handle($this->columns, $this->defaultBrandsShowNumber);

        if (! $brands['items']) {
            return response()->json(['message' => 'Brands not found.'], 404);
        }

        return BrandsPaginatedResource::collection($brands['items'])->additional($brands['additional']);
    }

    private function wantsLimitOffsetPaginatedBrands(): JsonApiResourceCollection|JsonResponse
    {
        $brands = (new GetLimitOffsetPaginatedBrandsAction)
            ->handle(
                columns: $this->columns,
                limit: (int) $this->request->query('limit', $this->defaultBrandsShowNumber),
                offset: (int) $this->request->query('offset', 0),
            );

        if ($brands->items->isEmpty()) {
            return response()->json(['message' => 'Brands not found.'], 404);
        }

        return BrandsPaginatedResource::collection($brands->items)->additional($brands->wrapMeta());
    }
}
